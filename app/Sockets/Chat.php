<?php
namespace App\Sockets;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class Chat implements MessageComponentInterface {
    protected $clients;

    protected $clientsConnexion = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }


    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $session = $this->getSession($conn);

        // Bind the session handler to the client connection
        $conn->session = $session;
        echo "New connection! ({$conn->resourceId})\n";

        
        $session->start();

        $idUser = $session->get(Auth::getName());

        if (!isset($idUser)) {
            $idUser = $session->get('id');
        }

        $this->clientsConnexion[$conn->resourceId] = ["ref" => $conn, "lat" => 0, "long" => 0, "id" => $idUser];

    }

    public function onMessage(ConnectionInterface $from, $event) {
        $event = json_decode($event);

        $con = $this->clientsConnexion[$from->resourceId];

        switch($event->type) {
            case 'conversation':
                $this->onConversation($event->data, $con);
            break;

            case 'message':
                $this->onMessageSent($event->data, $con);
                break;

            case 'newpos':
                //var_dump($this->clientsConnexion[$from->resourceId]->lat);
                $this->clientsConnexion[$from->resourceId]['lat'] = $event->data->lat;
                $this->clientsConnexion[$from->resourceId]['long'] = $event->data->long;
                $this->sendReachableConversations($event->data, $from);

            default:
            break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);
        unset($this->clientsConnexion[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        echo $e;

        $conn->close();
    }

    public function onConversation($event, $from) {
        if(!isset($event->conversation) || !isset($event->message))
            return;

        $message = $event->message;
        
        $message->image = isset($message->image) ? $message->image : NULL;
        if (!isset($message->message))
            return;

        $data = $event->conversation;
        if (!isset($data->lat) || !isset($data->long) || !isset($data->lifetime))
            return;

        
        $radius = 30000;
        $lifetime = "+30 minutes";
        if(is_string($data->lifetime) && preg_match('/^\d{2}:\d{2}$/', $data->lifetime)) {
            $time = explode(':', $data->lifetime);
            $lifetime = '+ ' . $time[0] . " hours " . $time[1] . " minutes";
        }
        
        $timeOfDeath = date('Y-m-d H:i:s', strtotime($lifetime));

        
        $lat = $data->lat;
        $long = $data->long;

        if ((is_numeric($lat) && $lat >= -90 && $lat <= 90
            && is_numeric($long)&& $long>= -180&& $long<= 180)) {
                $conv = Conversation::create([
                    'radius' => $radius,
                    'time_of_death' => $timeOfDeath,
                    'lat' => $lat,
                    'long' => $long,
                    'author' => $from["id"]]);

                $message->parent = $conv['id'];

            $clientInRange = array_filter($this->clientsConnexion, function($client) use ($conv, $lat, $long) {
                return $this->distance($lat, $long, $client['lat'], $client['long']) <= $conv['radius'];
            });

            

            $this->onMessageSent($message, $from, $conv['id']);
        }
    }

    public function onMessageSent($event, $from, $convID = NULL) {
        $isNewConv = ($convID != NULL);
        $convID = $event->parent ?? $convID;
        
        if($convID != NULL) {

            $now = date('Y-m-d H:i:s');
            $msg = ['content' => $event->message, 
            'image' => $event->image, 
            'posted' => $now, 
            'parent' => $convID, 
            'author' => $from["id"]];
            
            Message::insert($msg);

            $parentConv = Conversation::find($convID);
            $clientInRange = array_filter($this->clientsConnexion, function($client) use ($parentConv) {
                return $this->distance($parentConv['lat'], $parentConv['long'], $client['lat'], $client['long']) <= $parentConv['radius'];
            });

            if (!$isNewConv) {
                // Convert to string
                $msg = json_encode((object)['type' => 'message', 'data' => $msg]);
                foreach ($clientInRange as $clientId => $clientData) {
                    $dataJson = $msg;
                    $this->clientsConnexion[$clientId]['ref']->send($dataJson);
                    var_dump("send message");
                }
            } else {
                /*$dataJson = json_encode((object)['type' => 'conversation', 'data' => (object)$data]);
                foreach ($clientInRange as $clientId => $clientData) {
                    $this->clientsConnexion[$clientId]['ref']->send($dataJson);
                }*/

                $conv = Conversation::all()->last();
                $conv = (object)$conv;
                $conv->{'messages'} = Message::select('content', 'image', 'posted', 'username')->where(['parent' => $conv->id])->leftJoin('users', 'users.id', '=', 'author')->get()->toArray();
                $msg = json_encode((object)['type' => 'conversation', 'data' => $conv]);
                foreach ($clientInRange as $clientId => $clientData) {
                    $this->clientsConnexion[$clientId]['ref']->send($msg);
                }
            }
        }
    }

    public function sendReachableConversations($event, $sender) {
        $lat = $event->lat;
        $long = $event->long;

        //session(['lat' => $lat, 'long' => $long]);

        $conversations = Conversation::all()->toArray();

        $conversations = array_filter($conversations, function($conv) use ($lat, $long) {
            return $this->distance($lat, $long, $conv['lat'], $conv['long']) <= $conv['radius'];
        });

        foreach($conversations as &$conv) {
            $conv = (object)$conv;
            $conv->{'messages'} = Message::select('content', 'image', 'posted', 'username')->where(['parent' => $conv->id])->leftJoin('users', 'users.id', '=', 'author')->get()->toArray();
        }

        $msg = json_encode((object)['type' => 'conversations', 'data' => $conversations]);
        $sender->send($msg);
    }

    private function distance($lat1, $lon1, $lat2, $lon2) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            return $dist * 60 * 1.1515 * 1.609344 * 1000;
        }
    }

    private function getSession($conn) {
        // Create a new session handler for this client
        $session = (new SessionManager(App::getInstance()))->driver();
        // Get the cookies
        $cookiesHeader = $conn->httpRequest->getHeader('Cookie');
        $cookies = \GuzzleHttp\Psr7\Header::parse($cookiesHeader)[0];

        // Get the laravel's one
        $laravelCookie = urldecode($cookies[Config::get('session.cookie')]);
        // get the user session id from it
        $idSession = Crypt::decrypt($laravelCookie, false);
        //$idSession = $laravelCookie;
        
        $idSession = explode("|", $idSession)[1];

        // Set the session id to the session handler
        $session->setId($idSession);

        return $session;
    }
}
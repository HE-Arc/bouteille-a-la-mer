<?php
namespace App\Sockets;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Message;
use App\Models\Conversation;
use App\Jobs\DeleteConversation;
use DateTime;
use App\Models\Like;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class Chat implements MessageComponentInterface {
    protected $clients;

    protected $clientsConnexion = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;

        // Delete old conversations
        Conversation::whereDate('time_of_death', '<', new DateTime())->delete();

        // Create jobs for existing conversations
        $convs = Conversation::all();
        foreach($convs as $conv) {
            DeleteConversation::dispatch($conv)->delay(new DateTime($conv['time_of_death']));
        }
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
                break;
            case 'likeMessage':
                $this->onLike($event->data, $con);
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

        if (empty($event->message->message)) {
            dump("message empty");
            return;
        }

        
        $radius = 30000;
        $lifetime = "+30 minutes";
        if(is_string($data->lifetime) && preg_match('/^\d{2}:\d{2}$/', $data->lifetime)) {
            $time = explode(':', $data->lifetime);
            $lifetime = '+ ' . $time[0] . " hours " . $time[1] . " minutes";
        }
        
        $timeOfDeath = new DateTime(date('Y-m-d H:i:s', strtotime($lifetime)));
        
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

                // Job to delete conversation when it needs to
                DeleteConversation::dispatch($conv)->delay($timeOfDeath);

                $message->parent = $conv['id'];

            $this->onMessageSent($message, $from, $conv['id']);
        }
    }

    public function onMessageSent($event, $from, $convID = NULL) {
        $isNewConv = ($convID != NULL);
        $convID = $event->parent ?? $convID;
        
        if($convID != NULL) {
            $imageURL = NULL;

            if ($event->image !== NULL) {
                if (!getimagesize($event->image))
                    $event->image = NULL;
                else {
                    
                    $image = $event->image;  // your base64 encoded
                    //$image = str_replace('data:image/png;base64,', '', $image);
                    $image = preg_replace('/^data:image\/\w+;base64,/', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $ext = explode('/', explode(':', substr($event->image, 0, strpos($event->image, ';')))[1])[1];
                    
                    do {
                        $imageName = Str::random(10).'.'.$ext;
                    } while(File::exists(public_path('uploads/').$imageName));


                    File::put(public_path('uploads/').$imageName, base64_decode($image));
                    $imageURL = "/uploads/".$imageName;
                }
            }

            if ($event->message == NULL && $imageURL == NULL) {
                return;
            }

            $now = date('Y-m-d H:i:s');
            $msg = ['content' => $event->message, 
            'image' => $imageURL, 
            'posted' => $now, 
            'parent' => $convID,
            'author' => $from["id"]];
            
            $parentConv = Conversation::find($convID);
            if ($parentConv == NULL)
                return;
            
            Message::insert($msg);

            $clientInRange = array_filter($this->clientsConnexion, function($client) use ($parentConv) {
                return $this->distance($parentConv['lat'], $parentConv['long'], $client['lat'], $client['long']) <= $parentConv['radius'];
            });

            if (!$isNewConv) {
                // Convert to string
                //$msg = Message::select('content', 'image', 'posted', 'username', 'messages.id as id', 'author', 'parent')->where(['parent' => $convID])->leftJoin('users', 'users.id', '=', 'author')->get()->last();
                $msg = Message
                    ::select('content', 'image', 'posted', 'username', 'messages.id as id', 'author', 'parent',
                    DB::raw('count(likes_relation.id) as nbLike'))
                    ->where(['parent' => $convID])
                    ->leftJoin('users', 'users.id', '=', 'author')
                    ->leftJoin('likes_relation', 'likes_relation.message', '=', 'messages.id')
                    ->groupBy('messages.id')
                    ->get()->last();
                $msg = json_encode((object)['type' => 'message', 'data' => $msg]);
                foreach ($clientInRange as $clientId => $clientData) {
                    $dataJson = $msg;
                    $this->clientsConnexion[$clientId]['ref']->send($dataJson);
                }
            } else {
                $conv = Conversation::all()->last();
                $conv = (object)$conv;
                $conv->{'messages'} = Message
                    ::select('content', 'image', 'posted', 'username', 'messages.id as id', 'author', 'parent',
                    DB::raw('count(likes_relation.id) as nbLike'))
                    ->where(['parent' => $conv->id])
                    ->leftJoin('users', 'users.id', '=', 'author')
                    ->leftJoin('likes_relation', 'likes_relation.message', '=', 'messages.id')
                    ->groupBy('messages.id')
                    ->get()->toArray();
                //$conv->{'messages'} = Message::select('content', 'image', 'posted', 'username', 'messages.id as id', 'author')->where(['parent' => $conv->id])->leftJoin('users', 'users.id', '=', 'author')->get()->toArray();
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
            $conv->{'messages'} = Message
                ::select('content', 'image', 'posted', 'username', 'messages.id as id', 'author',
                DB::raw('count(likes_relation.id) as nbLike'))
                ->where(['parent' => $conv->id])
                ->leftJoin('users', 'users.id', '=', 'author')
                ->leftJoin('likes_relation', 'likes_relation.message', '=', 'messages.id')
                ->groupBy('messages.id')
                ->get()->toArray();
        }

        $msg = json_encode((object)['type' => 'conversations', 'data' => $conversations]);
        $sender->send($msg);
    }

    public function onLike($event, $from) {
        $messageID = $event->messageID;
        $userID = $from["id"];

        dump($messageID);
        dump($userID);

        $likeExist = Like::where(['user' => $userID, 'message' => $messageID])->first();
        if ($likeExist != NULL) {
            $likeExist->delete();
        } else {
            Like::create(["user" => $userID, "message" => $messageID]);
        }
        $conv = Message::select('conversations.*', 'parent', 'messages.id as messageID')->where(["messages.id" => $messageID])->join('conversations', 'conversations.id', '=', 'parent')->first();

        $nbLike = Like::where(['message' => $messageID])->count();
        //$clientInRange = $this->getClientInRange($conv['lat'], $conv['long']);
        $data = json_encode((object)['type' => 'like', 'data' => ['messageID' => $messageID, 'convID' => $conv['id'], 'nbLike' => $nbLike]]);
        $this->sendToClientInRange($data, $conv['lat'], $conv['long'], $conv['radius']);
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

    function getClientInRange($lat, $long, $radius) {
        $clientInRange = array_filter($this->clientsConnexion, function($client) use ($lat, $long, $radius) {
            return $this->distance($lat, $long, $client['lat'], $client['long']) <= $radius;
        });
        return $clientInRange;
    }

    function sendToClientInRange($data, $lat, $long, $radius) {
        $clientInRange = $this->getClientInRange($lat, $long, $radius);
        foreach ($clientInRange as $clientId => $clientData) {
            $this->clientsConnexion[$clientId]['ref']->send($data);
        }
    }
}
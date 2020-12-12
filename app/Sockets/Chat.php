<?php
namespace App\Sockets;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\Conversation;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $event) {
        $event = json_decode($event);

        switch($event->type) {
            case 'conversation':
                $this->onConversation($event->data);
            break;

            case 'message':
                $this->onMessageSent($event->data);
                break;

            case 'newpos':
                $this->sendReachableConversations($event->data, $from);

            default:
            break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        echo $e;

        $conn->close();
    }

    public function onConversation($event) {
        $data = $event->conversation;
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
                $id = Conversation::insertGetId([
                    'radius' => $radius,
                    'time_of_death' => $timeOfDeath,
                    'lat' => $lat,
                    'long' => $long,
                    'author' => 1]);

            // TODO send to reachable clients only (store lat and long as variable instead of session)
            $dataJson = json_encode((object)$data);
            foreach ($this->clients as $client) {
                $client->send($dataJson);
            }


            $this->onMessageSent($event->message, $id);
        }
    }

    public function onMessageSent($event, $convID = NULL) {
        $convID == $event->parent ?? $convID;
        
        if($convID != NULL) {
            $now = date('Y-m-d H:i:s');
            $msg = ['content' => $event->message, 
            'image' => $event->image, 
            'posted' => $now, 
            'parent' => $convID, 
            'author' => 1]; // TODO session('loginID')
            
            Message::insert($msg);


            // Convert to string
            $msg = json_encode((object)['type' => 'message', 'data' => $msg]);
    
            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        }
    }

    public function sendReachableConversations($event, $sender) {
        $lat = $event->lat;
        $long = $event->long;

        session(['lat' => $lat, 'long' => $long]);

        $conversations = Conversation::all()->toArray();

        $conversations = array_filter($conversations, function($conv) use ($lat, $long) {
            return $this->distance($lat, $long, $conv['lat'], $conv['long']) <= $conv['radius'];
        });

        foreach($conversations as &$conv) {
            $conv = (object)$conv;
            $conv->{'messages'} = Message::where(['parent' => $conv->id])->get()->toArray();
        }

        var_dump($conversations);

        $msg = json_encode((object)['type' => 'conversations', 'data' => $conversations]);
        $sender->send($msg);
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
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
}
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
                $this->onConversation($event->data->conversation);

            case 'message':
                $this->onMessageSent($event->data->message);
                
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
        $radius = 30000;
        $lifetime = "+30 minutes";
        if(is_string($event->lifetime) && preg_match('/^\d{2}:\d{2}$/', $event->lifetime)) {
            $time = explode(':', $event->lifetime);
            $lifetime = '+ ' . $time[0] . " hours " . $time[1] . " minutes";
        }
        
        $timeOfDeath = date('Y-m-d H:i:s', strtotime($lifetime));
        $lat = $event->lat;
        $long = $event->long;

        if ((is_numeric($lat) && $lat >= -90 && $lat <= 90
            && is_numeric($long)&& $long>= -180&& $long<= 180)) {
                Conversation::insert([
                    'radius' => $radius,
                    'timeOfDeath' => $timeOfDeath,
                    'lat' => $lat,
                    'long' => $long,
                    'author' => session('loginID')]);

            $event = json_encode((object)$event);
            foreach ($this->clients as $client) {
                $client->send($event);
            }
        }
    }

    public function onMessageSent($event) {
        $now = date('Y-m-d H:i:s');
        $event = ['content' => $event->text, 
        'image' => $event->image, 
        'posted' => $now, 
        'parent' => $event->parent, 
        'author' => session('loginID')];

        Message::insert($event);


        // Convert to string
        $event = json_encode((object)$event);

        foreach ($this->clients as $client) {
            $client->send($event);
        }
    }
}
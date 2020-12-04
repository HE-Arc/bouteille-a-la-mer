<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Sockets\Chat;


class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing Websocket server to receive and manage connections';
 
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
     /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        date_default_timezone_set('Europe/Zurich');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8080
        );
        echo "server running\n";
        $server->run();
        echo "server running\n";
    }
}
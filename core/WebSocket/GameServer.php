<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use WebSocket\GameHandler;;

    require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new GameHandler()
            )
        ),
        8080
    );

    $server->run();
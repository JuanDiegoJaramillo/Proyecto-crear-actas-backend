<?php 
// server.php
// server.php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require_once 'vendor/autoload.php'; // Cargar el autoload de Composer primero
require_once 'websocketServer.php'; // Incluir tu servidor WebSocket

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    49152 // Cambia a un puerto libre que desees usar
);

$server->run();


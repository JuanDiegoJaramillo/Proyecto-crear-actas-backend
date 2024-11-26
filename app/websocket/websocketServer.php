<?php
require 'vendor/autoload.php';  // Asegúrate de que esta línea esté al principio
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require_once './app/controllers/ProposicionesController.php';
require_once './app/middlewares/AuthMiddleware.php'; // Controlador de autenticación
class Chat implements MessageComponentInterface {
    protected $clients;
    private $proposicionesModel;
    private $authController;

    public function __construct() {
        // Verifica que esta línea no genere errores
        echo "Creando ProposicionesController...";
        $this->proposicionesModel = new ProposicionesController();
        $this->authController = new AuthMiddleware();
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (isset($data['token'])) {
            // Validar el token
            $isValidToken = $this->authController->authenticate();

            if (!$isValidToken) {
                $from->send(json_encode(['message' => 'Token inválido o expirado']));
                return;
            }
        } else {
            $from->send(json_encode(['message' => 'Token no proporcionado']));
            return;
        }

        // Validar datos obligatorios
        if (isset($data['descripcion']) && isset($data['idSesion']) && isset($data['idMiembro'])) {
            $insertResult = $this->proposicionesModel->crearProposicion($data['descripcion'], $data['idSesion'], $data['idMiembro']);

            if ($insertResult === 201) {
                $this->broadcastMessage($data);
            } else {
                $from->send(json_encode(['message' => 'Error al insertar proposición']));
            }
        } else {
            $from->send(json_encode(['message' => 'Datos inválidos']));
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: " . $e->getMessage();
        $conn->close();
    }

    private function broadcastMessage($data) {
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'descripcion' => $data['descripcion'],
                'idSesion' => $data['idSesion'],
                'idMiembro' => $data['idMiembro'],
                'autor' => $data['autor'] // Autor del mensaje
            ]));
        }
    }
}


<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once './app/controllers/ProposicionesController.php';
require_once './app/middlewares/AuthMiddleware.php';

class Chat implements MessageComponentInterface
{
    protected $clients;
    private $proposicionesModel;
    private $authController;

    public function __construct()
    {
        $this->proposicionesModel = new ProposicionesController();
        $this->authController = new AuthMiddleware();
        $this->clients = new \SplObjectStorage;
    }

    // Cuando se abre una conexión WebSocket
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

   // Cuando se recibe un mensaje de un cliente
    // public function onMessage(ConnectionInterface $from, $msg)
    // {
        // try {
            // $data = json_decode($msg, true);
// 
          //  Validar el formato del mensaje recibido
            // if (!$data || !is_array($data)) {
                // $from->send(json_encode(['message' => 'Formato de mensaje inválido']));
                // return;
            // }
// 
        //    Validar el token
            // if (!isset($data['token']) || !$this->authController->validateToken($data['token'])) {
                // $from->send(json_encode(['message' => 'Token inválido o expirado']));
                // return;
            // }
// 
         //   Verificar los datos requeridos
            // if (isset($data['descripcion'], $data['idSesion'], $data['idMiembro'])) {
            //    Insertar la proposición en la base de datos
                // $insertResult = $this->proposicionesModel->crearProposicion(
                    // $data['descripcion'],
                    // $data['idSesion'],
                    // $data['idMiembro']
                // );
// 
            //    Verificar si la inserción fue exitosa y obtener los datos necesarios
                // if (isset($insertResult['data'])) {
              //      Broadcast del mensaje a todos los clientes con los datos completos
                    // $this->broadcastMessage($insertResult['data']);
                // } else {
                    // $from->send(json_encode([
                        // 'message' => 'Error al insertar proposición',
                        // 'error' => $insertResult['error'] ?? 'Error desconocido'
                    // ]));
                // }
            // } else {
                // $from->send(json_encode(['message' => 'Datos incompletos']));
            // }
        // } catch (\Exception $e) {
          //  Manejo de errores no controlados
            // error_log("Error en WebSocket: " . $e->getMessage());
            // $from->send(json_encode(['message' => 'Error interno del servidor']));
        // }
    // }
// 
   // Cuando se cierra una conexión WebSocket


   public function onMessage(ConnectionInterface $from, $msg)
{
    try {
        $data = json_decode($msg, true);

        // Validar el formato del mensaje recibido
        if (!$data || !is_array($data)) {
            $from->send(json_encode(['status' => 400, 'message' => 'Formato de mensaje inválido']));
            return;
        }

        // Validar el token
        if (!isset($data['token']) || !$this->authController->validateToken($data['token'])) {
            $from->send(json_encode(['status' => 401, 'message' => 'Token inválido o expirado']));
            return;
        }

        // Verificar los datos requeridos para la proposición
        if (isset($data['descripcion'], $data['idSesion'], $data['idMiembro'])) {
            // Intentar insertar la proposición
            $insertResult = $this->proposicionesModel->crearProposicion(
                $data['descripcion'],
                $data['idSesion'],
                $data['idMiembro']
            );

            // Verificar el resultado de la operación
            if ($insertResult === 201) {
                // Enviar el mensaje a todos los clientes conectados
                $this->broadcastMessage([
                    'status' => 201,
                    'message' => 'Proposición creada exitosamente.',
                    'descripcion' => $data['descripcion'],
                    'idSesion' => $data['idSesion'],
                    'idMiembro' => $data['idMiembro']
                ]);
            } elseif ($insertResult === 400) {
                $from->send(json_encode([
                    'status' => 400,
                    'message' => 'Datos incompletos para crear la proposición.'
                ]));
            } else {
                $from->send(json_encode([
                    'status' => 500,
                    'message' => 'Error al crear la proposición.'
                ]));
            }
        } else {
            $from->send(json_encode(['status' => 400, 'message' => 'Datos incompletos']));
        }
    } catch (\Exception $e) {
        // Manejo de errores no controlados
        error_log("Error en WebSocket: " . $e->getMessage());
        $from->send(json_encode(['status' => 500, 'message' => 'Error interno del servidor']));
    }
}
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    // Cuando ocurre un error en la conexión WebSocket
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        error_log("Error en la conexión WebSocket: " . $e->getMessage());
        $conn->close();
    }

    // Método para enviar un mensaje a todos los clientes conectados
 //   private function broadcastMessage($data)
   // {
     //   foreach ($this->clients as $client) {
            //    $client->send(json_encode([
            //        'idProposicion' => $data['ID_PROPOSICIONES'],
            //        'descripcion' => $data['DESCRIPCION'],
            //        'fecha' => $data['FECHA'],
            //        'autor' => $data['autor'], // El nombre del autor
            //        'idMiembro' => $data['MIEMBRO_IDMIEMBRO']
            //    ]));
           // $client->send(json_encode(['status' => '201']));
            //}

       // }
  // }

  private function broadcastMessage($data)
{
    foreach ($this->clients as $client) {
        $client->send(json_encode($data));
    }
}
}

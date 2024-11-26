<?php
//app\controllers\SolicitudController.php

require_once  'app/models/Invitados.php';



class InvitadosController
{
    private $InvitadosudModel;

    public function __construct()
    {
        $this->InvitadosudModel = new Invitados();
    }

    public function MostrarInvitados()
    {


        $respuesta = $this->InvitadosudModel->SelectInvitados();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron invitados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }
    
    public function DetallesUsuarioInvitado()
    {
        $respuesta = $this->InvitadosudModel->DetallesUsuarioInvitado();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron invitados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

}

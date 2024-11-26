<?php
//app\controllers\SolicitudController.php

require_once  'app/models/Miembros.php';



class MiembrosController
{
    private $MiembrosdModel;

    public function __construct()
    {
        $this->MiembrosdModel = new Miembros();
    }

    public function MostrarMiembros()
    {
        $respuesta = $this->MiembrosdModel->SelectMiembros();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron Miembros']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function MiembrosAsignadosTarea($IDtarea)
    {
        $respuesta = $this->MiembrosdModel->MiembrosAsignadosTarea($IDtarea);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron Miembros']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function DetallesUsuarioMiembro()
    {
        $respuesta = $this->MiembrosdModel->DetallesUsuarioMiembro();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontro el invitado']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }


    public function getIMiembro()
    {
        $respuesta = $this->MiembrosdModel->getIMiembro();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontro el solicitante']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }
}
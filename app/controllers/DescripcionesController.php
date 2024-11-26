<?php
//app\controllers\SolicitudController.php

require_once  'app/models/Descripciones.php';



class DescripcionesController
{
    private $DescripcionesModel;

    public function __construct()
    {
        $this->DescripcionesModel = new Descripciones();
    }

    public function MostrarDescripciones()
    {
        $respuesta = $this->DescripcionesModel->mostrarDescripciones();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron Descripciones']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }
}
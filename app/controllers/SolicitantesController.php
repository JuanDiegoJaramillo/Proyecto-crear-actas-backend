<?php
//app\controllers\SolicitudController.php

require_once 'app/models/solicitantes.php';



class SolicitantesController
{
    private $solicitantesModel;

    public function __construct()
    {
        $this->solicitantesModel = new solicitantes();
    }

    public function SolicitanteIDsolicitud($id)
    {
        $respuesta = $this->solicitantesModel->mostrarSolicitanteIDSolicitud($id);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontro el solicitante']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function MostrarSolicitantes()
    {
        $respuesta = $this->solicitantesModel->mostrarSolicitantes();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se hay Solicitantes Registrados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function mostrarSolicitantesConSolicitudes()
    {
        $respuesta = $this->solicitantesModel->mostrarSolicitantesConSolicitudes();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se hay Solicitantes Registrados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function mostrarSolicitantesPorTipo($tipo)
    {
        $respuesta = $this->solicitantesModel->mostrarSolicitantesPorTipo($tipo);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se hay Solicitantes Registrados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function buscarSolicitante($dato)
    {
        // Llamar al modelo para buscar al solicitante
        $respuesta = $this->solicitantesModel->buscarSolicitante($dato);
    
        // Manejar la respuesta del modelo
        if (isset($respuesta['mensaje']) && empty($respuesta['data'])) {
            // Si no hay datos, devolver un mensaje de error 404
            http_response_code(404);
            echo json_encode(['message' => $respuesta['mensaje']]);
        } elseif (isset($respuesta['data']) && !empty($respuesta['data'])) {
            // Si hay datos, devolver la información del solicitante con un código 200
            http_response_code(200);
            echo json_encode([
                'message' => $respuesta['mensaje'],
                'data' => $respuesta['data']
            ]);
        } else {
            // Manejar errores del servidor, si ocurre una excepción en el modelo
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor durante la consulta.']);
        }
    }
    


    public function DetallesUsuarioSolicitante()
    {
        $respuesta = $this->solicitantesModel->DetallesUsuarioSolicitante();
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
    public function getIDolicitante()
    {
        $respuesta = $this->solicitantesModel->getIDolicitante();
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
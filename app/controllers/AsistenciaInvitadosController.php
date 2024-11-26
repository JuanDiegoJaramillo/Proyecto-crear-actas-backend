<?php
//app\controllers\sesionController.php

require_once 'app/models/AsistenciaInvitados.php';


class AsistenciaInvitadosController
{
    private $AsistenciaInvitadosModel;

    public function __construct()
    {
        $this->AsistenciaInvitadosModel = new AsistenciaInvitados();
    }

    public function asistenciaInvitadosSesion($idSesion)
    {
        $respuesta = $this->AsistenciaInvitadosModel->AsistenciaInvitadosSesion($idSesion);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 400) {
            http_response_code(404);
            echo json_encode(['message' => 'invitados no citados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }


    public function AgregarInvitado()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['idInvitado'], $data['idSesion'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['idInvitado'] == "" || $data['idSesion'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            
            if ($this->AsistenciaInvitadosModel->validarAsistencia($data['idInvitado'],$data['idSesion'])== 1) {
                http_response_code(409);
                echo json_encode(['message' => ' El Invidato ya ha sido Agregado']);
                return;
            }

            if ($this->AsistenciaInvitadosModel->AgregarInvitadoAsistencia($data['idInvitado'], $data['idSesion'])) {
                http_response_code(201);
                echo json_encode(['message' => 'Invitado Agregado']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Agregar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function actualizarAsistenciaInvitados()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['estado'], $data['idSesion'], $data['idInvitado'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Todos los campos son obligatorios']);
            return;
        }
        if ($data['estado'] == "" || $data['idSesion'] == "" || $data['idInvitado'] == "") {
            http_response_code(400);
            echo json_encode(['message' => 'No deje campos vacios']);
            return;
        }
        $respuesta = $this->AsistenciaInvitadosModel->actualizarAsistenciaInvitados($data['estado'],$data['idSesion'],$data['idInvitado'] );
        if ($respuesta == true) {
            http_response_code(201);
            echo json_encode(['message' => 'Asistencia Registrada']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al Actualizar']);
        }

    }

    public function obtenerInvitadosNoAsistentes($idSesion)
    {
        $respuesta = $this->AsistenciaInvitadosModel->validarInvitacionPendiente($idSesion);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 400) {
            http_response_code(404);
            echo json_encode(['message' => 'Todos estan Invitados']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function EliminarAsistenciaInvitado($sesionId,$invitadoId)
    {

        if ($sesionId !== null && $invitadoId !== null) {
            $respuesta = $this->AsistenciaInvitadosModel->eliminarAsistenciaInvitado($sesionId,$invitadoId);

            if ($respuesta == false) {
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if ($respuesta == true) {
                http_response_code(200);
                echo json_encode(['message' => 'Asistencia Eliminada']);
            }
        } else{
            http_response_code(400);
            echo json_encode(['message' => 'Datos vacios']);
        }
           
    }

}
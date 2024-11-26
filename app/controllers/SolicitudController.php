<?php
//app\controllers\SolicitudController.php

require_once 'app/models/solicitud.php';



class SolicitudController
{
    private $solicitudModel;

    public function __construct()
    {
        $this->solicitudModel = new solicitud();
    }

    public function SolicitudesSesion($idSesion)
    {
        $respuesta = $this->solicitudModel->solicitudesSesion($idSesion);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron solicitudes']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }
    public function SolicitudSelecionada($idSolicitud)
    {
        $respuesta = $this->solicitudModel->SolicitudSelecionada($idSolicitud);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No existe la solicitud']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    
    public function solicitudesSesionSolicitante($idSesion,$idSolicitante)
    {
        $respuesta = $this->solicitudModel->solicitudesSesionSolicitante($idSesion,$idSolicitante);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron solicitudes']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function InsertarSolicitud()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['DEPENDENCIA'], $data['ASUNTO'], $data['IDSOLICITANTE'], $data['IDSESION'], $data['IDDESCRIPCION'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['DEPENDENCIA'] == "" || $data['ASUNTO'] == "" ||  $data['IDSOLICITANTE'] == "" || $data['IDSESION'] == "" || $data['IDDESCRIPCION'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $dependencia = $data['DEPENDENCIA'];
            $dependencia_mayusculas = strtoupper($dependencia);
            $asunto = $data['ASUNTO'];
            $asunto_mayusculas = strtoupper($asunto);


            if ($this->solicitudModel->InsertarSolicitud($dependencia_mayusculas, $asunto_mayusculas, $data['IDSOLICITANTE'], $data['IDSESION'], $data['IDDESCRIPCION'])) {
                http_response_code(201);
                echo json_encode(['message' => 'Solicitud Registrada']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }


    public function ActualizarSolicitud()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['DEPENDENCIA'], $data['ASUNTO'], $data['IDSOLICITANTE'],$data['IDSESION'],$data['IDDESCRIPCION'],$data['IDSOLICITUD'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['DEPENDENCIA'] == "" || $data['ASUNTO'] == "" || $data['IDSOLICITANTE'] == ""|| $data['IDSESION'] == "" || $data['IDDESCRIPCION'] == ""|| $data['IDSOLICITUD'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
      

            if ($this->solicitudModel->ActualizarSolicitud($data['DEPENDENCIA'], $data['ASUNTO'], $data['IDSOLICITANTE'],$data['IDSESION'],$data['IDDESCRIPCION'],$data['IDSOLICITUD'])) {
                http_response_code(201);
                echo json_encode(['message' => 'Actualizacion Exitosa']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Actualizar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function ResponderSolicitud()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['DESICION'], $data['IDSOLICITUD'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['DESICION'] == "" || $data['IDSOLICITUD'] == "" ) {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
      

            if ($this->solicitudModel->ResponderSolicitud( $data['DESICION'],  $data['IDSOLICITUD'])) {
                http_response_code(201);
                echo json_encode(['message' => 'Actualizacion Exitosa']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Actualizar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function EliminarSolicitud($id)
    {

        if ($id !== null) {
            $respuesta = $this->solicitudModel->EliminarSolicitud($id);

            if ($respuesta == false) {
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if ($respuesta == true) {
                http_response_code(200);
                echo json_encode(['message' => 'Tema Eliminado']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Dato vacio']);
        }
    }
}

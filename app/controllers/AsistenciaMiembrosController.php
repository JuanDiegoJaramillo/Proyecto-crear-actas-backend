<?php
//app\controllers\sesionController.php

require_once  'app/models/AsistenciaMiembros.php';


class AsistenciaMiembrosController
{
    private $AsistenciaMiembrosModel;

    public function __construct()
    {
        $this->AsistenciaMiembrosModel = new AsistenciaMiembros();
    }

    public function asistenciaMiembroSesion($idSesion)
    {
        $respuesta = $this->AsistenciaMiembrosModel->AsistenciaMiembroSesion($idSesion);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 400) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay sesiones registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function actualizarAsistenciaMiembro()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['estado'], $data['idSesion'], $data['idMiembro'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Todos los campos son obligatorios']);
            return;
        }
        if ($data['estado'] == "" || $data['idSesion'] == "" || $data['idMiembro'] == "") {
            http_response_code(400);
            echo json_encode(['message' => 'No deje campos vacios']);
            return;
        }
        $respuesta = $this->AsistenciaMiembrosModel->actualizarAsistenciaMiembros($data['estado'],$data['idSesion'],$data['idMiembro'] );
        if ($respuesta == true) {
            http_response_code(201);
            echo json_encode(['message' => 'Asistencia asignada']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al Actualizar']);
        }
    }


    public function cargarMiembros()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['idSesion'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['idSesion'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
             

            if ($this->AsistenciaMiembrosModel->cargarAsistenciaMiembros($data['idSesion']) == true) {
                http_response_code(201);
                echo json_encode(['message' => 'Miembros Cargados Correctamente']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function Todoscitados($IDsesion){
       if($this->AsistenciaMiembrosModel->todosMiembrosCitados($IDsesion) == true){
        http_response_code(200);
        echo json_encode(['message' => true]);
       }else{
        http_response_code(400);
        echo json_encode(['message' => false]);
       }


    }

}
    
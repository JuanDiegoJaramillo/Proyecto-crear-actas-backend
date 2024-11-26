<?php
//app\controllers\sesionController.php

require_once  'app/models/Tareas.php';



class TareasController
{
    private $TareasModel;

    public function __construct()
    {
        $this->TareasModel = new Tareas();
    }

    public function InsertarTarea()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['DESCRIPCION'], $data['IDSESION'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['DESCRIPCION'] == "" || $data['IDSESION'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $descripcion = $data['DESCRIPCION'];
            $descripcion_mayusculas = strtoupper($descripcion);
            if ($this->TareasModel->TareaExists($descripcion_mayusculas, $data['IDSESION'])) {
                http_response_code(409);
                echo json_encode(['message' => 'Tarea Existente']);
                return;
            }

            if ($this->TareasModel->InsertarTarea($descripcion_mayusculas, $data['IDSESION'])==201) {
                http_response_code(201);
                echo json_encode(['message' => 'Tarea Registrada']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function ActualizarTarea()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['DESCRIPCION'], $data['IDTAREA'],$data['IDSESION'] )) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['DESCRIPCION'] == "" || $data['IDTAREA'] == ""|| $data['IDSESION'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $descripcion = $data['DESCRIPCION'];
            $descripcion_mayusculas = strtoupper($descripcion);
            if ($this->TareasModel->TareaExists($descripcion_mayusculas, $data['IDSESION']    )) {
                http_response_code(409);
                echo json_encode(['message' => 'Tarea Existente']);
                return;
            }
            $result = $this->TareasModel->ActualizaTarea($descripcion_mayusculas, $data['IDTAREA']);
            error_log("Resultado de ActualizaTarea: " . $result);
            if ($result === 201) {
                http_response_code(201);
                echo json_encode(['message' => 'Tarea actualizada correctamente']);
            } elseif ($result === 400) {
                http_response_code(400);
                echo json_encode(['message' => 'Error al actualizar la tarea']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error interno del servidor']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }
    public function MotrarTareasIDSesion($id)
    {
        $respuesta = $this->TareasModel->MotrarTareasIDSesion($id);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay tareas registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    
    public function MotrarTareas()
    {
        $respuesta = $this->TareasModel->MostrarTareas();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay tareas registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function EliminarTarea($IDTAREAS)
    {

        if ($IDTAREAS !== null) {
            $respuesta = $this->TareasModel->eliminarTareaConResponsables( $IDTAREAS);

            if ($respuesta == false) {
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if ($respuesta == true) {
                http_response_code(200);
                echo json_encode(['message' => 'Tarea Eliminada']);
            }
        } else{
            http_response_code(400);
            echo json_encode(['message' => 'Dato vacio']);
        }
           
    }
}

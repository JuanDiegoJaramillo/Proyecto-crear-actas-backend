<?php

require_once  'app/models/EncargadosTareas.php';


class EncargadosTareasController
{
    private $EncargadosTareasModel;

    public function __construct()
    {
        $this->EncargadosTareasModel = new EncargadosTareas();
    }


    public function AsignarResponsable()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['IDTAREAS'], $data['IDMIEMBROS'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if (empty($data['IDTAREAS']) || empty($data['IDMIEMBROS'])) {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }


   //linea 34
            if ($this->EncargadosTareasModel->AsignarResponsable($data['IDTAREAS'],  $data['IDMIEMBROS'])==true) {
                http_response_code(201);
                echo json_encode(['message' => 'Miembro Asignado']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }


    public function DesasignarResponsable($IDMIEMBRO, $ID_TAREAS)
    {

        if ($IDMIEMBRO !== null && $ID_TAREAS !== null) {
            $respuesta = $this->EncargadosTareasModel->DesasignarResponsable($IDMIEMBRO, $ID_TAREAS);

            if ($respuesta == false) {
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if ($respuesta == true) {
                http_response_code(200);
                echo json_encode(['message' => 'Miembro Desasignado']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Datos vacios']);
        }
    }

    
    public function MotrarTareasSesioAnterior($id)
    {
        $respuesta = $this->EncargadosTareasModel->MotrarTareasSesioAnterior($id);
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


    public function cambiarEstadoTarea()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['ESTADO'], $data['IDTAREA'] )) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if (($data['ESTADO'] !== "REALIZADA" && $data['ESTADO'] !== "PENDIENTE") || empty($data['IDTAREA'])){
                http_response_code(400);
                echo json_encode(['message' => 'Estado inválido o ID de tarea vacío']);
                return;
            }      

            if ($this->EncargadosTareasModel->cambiarEstadoTarea($data['IDTAREA'],$data['ESTADO'])==true) {
                http_response_code(201);
                echo json_encode(['message' => 'Verificacion realizada']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

}

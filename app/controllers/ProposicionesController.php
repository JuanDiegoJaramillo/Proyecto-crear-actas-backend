<?php

require_once 'app/models/Proposiciones.php';

class ProposicionesController
{
    private $proposicionesModel;

    public function __construct()
    {
        $this->proposicionesModel = new Proposiciones();
    }

    // Listar todas las proposiciones
    public function listarProposiciones()
    {
        $respuesta = $this->proposicionesModel->listarProposiciones();

        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor al listar proposiciones.']);
        } elseif ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron proposiciones.']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function listarProposicionesSesion($idSesion)
    {
        $respuesta = $this->proposicionesModel->listarProposicionesSesion($idSesion);

        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor al listar proposiciones.']);
        } elseif ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron proposiciones.']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    // Crear una nueva proposición

    
    
    public function crearProposicion()
    {
        // Validar que los datos requeridos están presentes
      
        $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['descripcion'], $data['idMiembro'], $data['idSesion'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

        if (!$data['descripcion'] || !$data['idMiembro'] || !$data['idSesion']) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos para crear la proposición.']);
            return;
        }
    
        // Llamar al modelo para insertar la proposición
       
        $respuesta = $this->proposicionesModel->insertarProposicion($data['descripcion'], $data['idSesion'], $data['idMiembro']);


        // Manejar la respuesta del modelo
        if ($respuesta['status'] === 201) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Proposición creada exitosamente.',
                'data' => $respuesta['data'] // Enviar los datos completos de la proposición
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'message' => 'Error al crear la proposición.',
                'error' => $respuesta['error'] ?? 'Error desconocido.'
            ]);
        }
    }

    // Actualizar una proposición
    public function actualizarProposicion($data)
    {
        $idProposicion = $data['idProposicion'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $decision = $data['decision'] ?? null;

        if (!$idProposicion || !$descripcion || !$decision) {
            http_response_code(400);
            echo json_encode(['message' => 'Datos incompletos para actualizar la proposición.']);
            return;
        }

        $respuesta = $this->proposicionesModel->actualizarProposicion($idProposicion, $descripcion, $decision);

        if ($respuesta == 200) {
            http_response_code(200);
            echo json_encode(['message' => 'Proposición actualizada exitosamente.']);
        } elseif ($respuesta == 404) {
            http_response_code(404);
            echo json_encode(['message' => 'Proposición no encontrada.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al actualizar la proposición.']);
        }
    }

    // Eliminar una proposición
    public function eliminarProposicion($idProposicion)
    {
        if (!$idProposicion) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de proposición no proporcionado.']);
            return;
        }

        $respuesta = $this->proposicionesModel->eliminarProposicion($idProposicion);

        if ($respuesta == 200) {
            http_response_code(200);
            echo json_encode(['message' => 'Proposición eliminada exitosamente.']);
        } elseif ($respuesta == 404) {
            http_response_code(404);
            echo json_encode(['message' => 'Proposición no encontrada.']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error al eliminar la proposición.']);
        }
    }
}

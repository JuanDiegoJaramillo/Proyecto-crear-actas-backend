<?php
//app\controllers\ActasController.php

require_once 'app/models/Actas.php';



class ActasController
{
    private $ActasdModel;

    public function __construct()
    {
        $this->ActasdModel = new Actas();
    }

    public function MostrarActasFirmadas()
    {
        $respuesta = $this->ActasdModel->SelecActasFirmadas();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'No se encontraron Actas Firmadas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function ActaAnterior($idSesion)
    {
        $respuesta = $this->ActasdModel->ActaAnterior($idSesion);
        if ($respuesta == null) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if (empty($respuesta)) {
            http_response_code(404);
            echo json_encode(['message' => 'Sesion no registrata']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }


    public function InsertarActa()
    {
        try {
            // Decodifica los datos de la solicitud POST
            $data = json_decode(file_get_contents("php://input"), true);

            // Valida si se ha recibido el IDSESION en la solicitud
            if (!isset($data['IDSESION'])) {
                http_response_code(400);
                echo json_encode(['message' => 'ID de sesión es obligatorio']);
                return;
            }

            $idSesion = $data['IDSESION'];

            if ($this->ActasdModel->crearActa($idSesion) == 201) {
                http_response_code(201);
                echo json_encode(['message' => 'Acta creada exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Error al insertar el acta']);
            }
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error de base de datos', 'error' => $e->getMessage()]);
        }
    }

    public function AprobarActa()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['ESTADO'], $data['IDSESION'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if (($data['ESTADO'] !== "FIRMADA" && $data['ESTADO'] !== "PENDIENTE") || empty($data['IDSESION'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Estado inválido o ID de tarea vacío']);
                return;
            }

            if ($this->ActasdModel->AprobarActa($data['IDSESION'], $data['ESTADO']) == 201) {
                http_response_code(201);
                echo json_encode(['message' => 'Estado del Acta Registrado']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }


    public function BuscarActas($filtro)
    {
        try {
            // Llamar a la función del modelo para buscar por ID o Tema
            $respuesta = $this->ActasdModel->buscarPorIdOTema($filtro);

            // Validar la respuesta del modelo
            if ($respuesta == 500) {
                // Error interno en la consulta
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if (empty($respuesta)) {
                // No se encontraron resultados
                http_response_code(404);
                echo json_encode(['message' => 'No se encontraron actas registradas con el filtro proporcionado.']);
            } else {
                // Respuesta exitosa con los resultados
                http_response_code(200);
                echo json_encode($respuesta);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y responder con error
            http_response_code(500);
            echo json_encode(['message' => 'Ocurrió un error inesperado: ' . $e->getMessage()]);
        }
    }
    public function BuscarActasFecha($Year, $Month)
    {
        if (!$Year || !$Month) {
            http_response_code(400);
            echo json_encode(['message' => 'Parámetros requeridos']);
            return;
        }
        try {
            // Llamar a la función del modelo para buscar por ID o Tema
            $respuesta = $this->ActasdModel->BuscarActaPorFecha($Year, $Month);

            // Validar la respuesta del modelo
            if ($respuesta == 500) {
                // Error interno en la consulta
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            }else if (empty($respuesta)) {
                // No se encontraron resultados
                http_response_code(404);
                echo json_encode(['message' => 'No se encontraron actas registradas con el filtro proporcionado.']);
            } else {
                // Respuesta exitosa con los resultados
                http_response_code(200);
                echo json_encode($respuesta);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y responder con error
            http_response_code(500);
            echo json_encode(['message' => 'Ocurrió un error inesperado: ' . $e->getMessage()]);
        }
    }


    public function BuscarActaPorYear($Year)
    {
        if (!$Year) {
            http_response_code(400);
            echo json_encode(['message' => 'Parámetro Year requerido']);
            return;
        }
        try {
            // Llamar a la función del modelo para buscar por ID o Tema
            $respuesta = $this->ActasdModel->BuscarActaPorYear($Year);

            // Validar la respuesta del modelo
            if ($respuesta == 500) {
                // Error interno en la consulta
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if (empty($respuesta)) {
                // No se encontraron resultados
                http_response_code(404);
                echo json_encode(['message' => 'No se encontraron actas registradas con el filtro proporcionado.']);
            } else {
                // Respuesta exitosa con los resultados
                http_response_code(200);
                echo json_encode($respuesta);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y responder con error
            http_response_code(500);
            echo json_encode(['message' => 'Ocurrió un error inesperado: ' . $e->getMessage()]);
        }
    }

    public function BuscarActasTemaFecha($Year, $Month, $Tema)
    {
        if (!$Year || !$Month || !$Tema) {
            http_response_code(400);
            echo json_encode(['message' => 'Parámetros requeridos']);
            return;
        }
      //  $Tema = str_replace('t2', ' ', $Tema);

        try {
            // Llamar a la función del modelo para buscar por ID o Tema
            $respuesta = $this->ActasdModel->buscarPorTemaFecha($Tema,$Year, $Month);

            // Validar la respuesta del modelo
            if ($respuesta == 500) {
                // Error interno en la consulta
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if (empty($respuesta)) {
                // No se encontraron resultados
                http_response_code(404);
                echo json_encode(['message' => 'No se encontraron actas registradas con el filtro proporcionado.']);
            } else {
                // Respuesta exitosa con los resultados
                http_response_code(200);
                echo json_encode($respuesta);
            }
        } catch (Exception $e) {
            // Capturar cualquier excepción y responder con error
            http_response_code(500);
            echo json_encode(['message' => 'Ocurrió un error inesperado: ' . $e->getMessage()]);
        }
    }
}

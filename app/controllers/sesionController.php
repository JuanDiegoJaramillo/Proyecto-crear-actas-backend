<?php
//app\controllers\sesionController.php

require_once  'app/models/sesion.php';


class SesionController
{
    private $SesionModel;
    private $ActasdModel;
    public function __construct()
    {
        $this->SesionModel = new Sesion();  
    }

    public function proponerFecha()
    {
        $sesionModel = new Sesion();
        //$authMiddleware = new AuthMiddleware();
        //  $decoded = $authMiddleware->handle();
        //print_r( $decoded);
        $respuesta = $sesionModel->obtenerUltimaFechaSesion();
        if ($respuesta == null) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function mostrarSesiones()
    {
        $respuesta = $this->SesionModel->mostrarSesiones();
        if ($respuesta == null) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if (empty($respuesta)) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay sesiones registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function mostrarSesionesSolicitante($idSolicitante)
    {
        $respuesta = $this->SesionModel->mostrarSesionesSolicitante($idSolicitante);
    
        if ($respuesta === null) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if (isset($respuesta['message'])) {
            http_response_code(404);
            echo json_encode(['message' => $respuesta['message']]); // Usar el mensaje personalizado
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }
    

    public function mostrarSesioneIDSesion($idSesion)
    {
        $respuesta = $this->SesionModel->mostrarSesioneIDSesion($idSesion);
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

    public function MostrarSesionSelect()
    {
        $respuesta = $this->SesionModel->MostrarSesionSelect();
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if (empty($respuesta)) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay sesiones registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    // Controlador SesionController.php
    public function InsertarSesion2()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['LUGAR'], $data['FECHA'], $data['HORAINICIO'], $data['HORAFINAL'], $data['PRESIDENTE'], $data['SECRETARIO'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }
            if ($data['LUGAR'] == "" || $data['FECHA'] == "" || $data['HORAINICIO'] == "" || $data['HORAFINAL'] == "" || $data['PRESIDENTE'] == "" || $data['SECRETARIO'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $LUGAR = strtoupper($data['LUGAR']);
            $FECHA = $data['FECHA'];
            $HORAINICIO = $data['HORAINICIO'];
            $HORAFINAL = $data['HORAFINAL'];
            $horaInico = strtoupper($data['PRESIDENTE']);
            $PRESIDENTE = $horaInico;
            $horaFinal = strtoupper($data['SECRETARIO']);
            $SECRETARIO = $horaFinal;

            $idSesion = $this->SesionModel->registrarSesion($LUGAR, $FECHA, $HORAINICIO, $HORAFINAL, $PRESIDENTE, $SECRETARIO);
            if ($idSesion) {

                http_response_code(201);
                echo json_encode(['message' => 'Sesión correctamente', 'IDSESION' => $idSesion]);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Error al crear el acta']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor: ' . $e->getMessage()]);
        }

    }

// Controlador SesionController.php
public function InsertarSesion()
{
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validación de campos
        if (!isset($data['LUGAR'], $data['FECHA'], $data['HORAINICIO'], $data['HORAFINAL'], $data['PRESIDENTE'], $data['SECRETARIO'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Todos los campos son obligatorios']);
            return;
        }

        if ($data['LUGAR'] == "" || $data['FECHA'] == "" || $data['HORAINICIO'] == "" || $data['HORAFINAL'] == "" || $data['PRESIDENTE'] == "" || $data['SECRETARIO'] == "") {
            http_response_code(400);
            echo json_encode(['message' => 'No deje campos vacios']);
            return;
        }
        // Convertir a mayúsculas y asignar variables
        $LUGAR = strtoupper($data['LUGAR']);
        $FECHA = $data['FECHA'];
        $HORAINICIO = $data['HORAINICIO'];
        $HORAFINAL = $data['HORAFINAL'];
        $PRESIDENTE = strtoupper($data['PRESIDENTE']);
        $SECRETARIO = strtoupper($data['SECRETARIO']);

        // Inicia la transacción en el modelo
        $this->SesionModel->beginTransaction();

        // Registrar la sesión
        $idSesion = $this->SesionModel->registrarSesion($LUGAR, $FECHA, $HORAINICIO, $HORAFINAL, $PRESIDENTE, $SECRETARIO);
        if ($idSesion) {
            // Crear acta para la sesión recién creada
            if ($this->SesionModel->crearActa($idSesion)==201) {
                $this->SesionModel->commit(); // Confirmar transacción
                http_response_code(201);
                echo json_encode(['message' => 'Sesión y acta creadas correctamente', 'IDSESION' => $idSesion]);
                return;
            }
        }

        // Si falla alguna operación, deshacer la transacción
        $this->SesionModel->rollBack();
        http_response_code(500);
        echo json_encode(['message' => 'Error al registrar la sesión o crear el acta']);

    } catch (Exception $e) {
        $this->SesionModel->rollBack(); // Deshacer transacción si ocurre una excepción
        http_response_code(500);
        echo json_encode(['message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
}



}

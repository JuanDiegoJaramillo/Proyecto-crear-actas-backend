<?php
//app\controllers\sesionController.php

require_once 'app/models/ordenSesion.php';



class OrdenSesionController
{
    private $OrdenModel;

    public function __construct()
    {
        $this->OrdenModel = new ordenSesion();
    }

    public function TemasSesion($id)
    {
        $respuesta = $this->OrdenModel->temasSesion($id);
        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta==0) {
            http_response_code(404);
            echo json_encode(['message' => 'No hay temas registradas']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }

    public function datosOrdenSesion($tema)
    {
        $respuesta = $this->OrdenModel->solicitud($tema);

        if ($respuesta == 500) {
            http_response_code(500);
            echo json_encode(['message' => 'Error en el servidor tipo consulta']);
        } else if ($respuesta == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'Tema no registrado']);
        } else {
            http_response_code(200);
            echo json_encode($respuesta);
        }
    }



    //insertar temas
    public function InsertarTema()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['tema'], $data['descripcion'], $data['idSesion'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['tema'] == "" || $data['descripcion'] == "" || $data['idSesion'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $tema = $data['tema'];
            $tema_mayusculas = strtoupper($tema);
            $descrip = $data['descripcion'];
            $Descripcion_mayusculas = strtoupper($descrip);
            if ($this->OrdenModel->TemaExists($tema_mayusculas,$data['idSesion'])) {
                http_response_code(409);
                echo json_encode(['message' => 'El tema ya Existe']);
                return;
            }

            if ($this->OrdenModel->registrarTema($tema_mayusculas, $Descripcion_mayusculas, $data['idSesion'])) {
                http_response_code(201);
                echo json_encode(['message' => 'Registro Creado']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Error al Registrar']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    public function ActualizarTema()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['tema'], $data['descripcion'], $data['idTema'],$data['idSesion'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            if ($data['tema'] == "" || $data['descripcion'] == "" || $data['idTema'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            $tema = $data['tema'];
            $tema_mayusculas = strtoupper($tema);
            $descrip = $data['descripcion'];
            $Descripcion_mayusculas = strtoupper($descrip);
            if ($this->OrdenModel->ValidarTema($data['idTema'], $tema_mayusculas,$data['idSesion']) == 1) {
                http_response_code(409);
                echo json_encode(['message' => 'El tema ya Existe']);
                return;
            }

            if ($this->OrdenModel->ActualizarTema($data['idTema'], $tema_mayusculas, $Descripcion_mayusculas)) {
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

    public function EliminarTema($id)
    {

        if ($id !== null) {
            $respuesta = $this->OrdenModel->eliminarTema($id);

            if ($respuesta == false) {
                http_response_code(500);
                echo json_encode(['message' => 'Error en el servidor tipo consulta']);
            } else if ($respuesta == true) {
                http_response_code(200);
                echo json_encode(['message' => 'Tema Eliminado']);
            }
        } else{
            http_response_code(400);
            echo json_encode(['message' => 'Dato vacio']);
        }
           
    }
}

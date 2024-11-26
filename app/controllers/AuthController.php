<?php

use Firebase\JWT\JWT;

require_once 'app/models/User.php';
require_once 'app/models/Invitados.php';
require_once 'app/models/Miembros.php';
require_once 'app/models/Solicitantes.php';
class AuthController
{
    private $InvitadosudModel;
    private $MiembrosudModel;
    private $SolicitantesModel;
    private $userModel;

    public function __construct()
    {
        $this->InvitadosudModel = new Invitados();
        $this->MiembrosudModel = new Miembros();
        $this->SolicitantesModel = new Solicitantes();
        $this->userModel = new User();
    }
    // Método para registrar un nuevo usuario
    public function register()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            // Validar que los datos existan
            if (!isset($data['nombre'], $data['email'], $data['password'], $data['rol'])) {
                http_response_code(400); // Bad Request
                echo json_encode(['message' => 'Todos los campos son obligatorios']);
                return;
            }

            // Validar el correo electrónico
            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                http_response_code(400); // Bad Request
                echo json_encode(['message' => 'Correo electrónico no válido']);
                return;
            }


            // Guardar usuario en la base de datos
            $userModel = new User();
            if ($userModel->usuarioExists($email, $data['rol'])) {
                http_response_code(409); // Conflict
                echo json_encode(['message' => 'El usuario ya exsistente']);
                return;
            }
            // Hashear la contraseña
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $rol = $data['rol'];
            $nombre =  $data['nombre'];
            // Roles válidos según tu ENUM en la base de datos
            $validRoles = ['coordinador'];

            // Validar si el rol proporcionado es válido
            if (!in_array($rol, $validRoles)) {
                http_response_code(400); // Bad Request
                echo json_encode(['message' => 'El rol proporcionado no es válido']);
                return;
            }

            if ($userModel->register($nombre, $email, $password, $rol)) {
                echo json_encode(['message' => 'Usuario registrado correctamente']);
            } else {
                throw new Exception('Error al registrar el usuario');
            }
        } catch (Exception $e) {
            http_response_code(500); // Error en el servidor
            echo json_encode(['message' => $e->getMessage()]);
        }
    }


    public function RegistrarInvitado()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $requiredFields = ['NOMBRE', 'DEPENDENCIA', 'CARGO', 'EMAIL', 'urlLogin'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "El campo $field es obligatorio."]);
                    return;
                }
            }

            if ($data['NOMBRE'] == "" || $data['DEPENDENCIA'] == "" || $data['CARGO'] == "" || $data['EMAIL'] == "" || $data['urlLogin'] == "") {
                http_response_code(400);
                echo json_encode(['message' => 'No deje campos vacios']);
                return;
            }
            // Validar formato de email
            if (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Formato de email no válido']);
                return;
            }

            // Verificar si el email ya existe
            if ($this->InvitadosudModel->verificarEmail($data['EMAIL'])) {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'Este Usuario ya está registrado.']);
                return;
            }

            $nombre = $data['NOMBRE'];
            $nombre_mayusculas = strtoupper($nombre);
            $dependencia = $data['DEPENDENCIA'];
            $dependencia_mayusculas = strtoupper($dependencia);
            $cargo = $data['CARGO'];
            $cargo_mayusculas = strtoupper($cargo);

            // Registrar invitado
            $respuesta = $this->InvitadosudModel->InsertarInvitados(
                $nombre_mayusculas,
                $dependencia_mayusculas,
                $cargo_mayusculas,
                $data['EMAIL'],
                $data['urlLogin']
            );

            if ($respuesta == 201) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Invitado registrado correctamente.']);
            } else {
                throw new Exception('Error al registrar al invitado.');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function RegistrarMiembro()
    {
        try {
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $requiredFields = ['NOMBRE', 'CARGO', 'EMAIL', 'urlLogin'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "El campo $field es obligatorio."]);
                    return;
                }
            }

            // Validar formato de email
            if (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Formato de email no válido']);
                return;
            }

            // Verificar si el email ya existe
            if ($this->MiembrosudModel->verificarEmail($data['EMAIL'])) {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'El usuario ya está registrado.']);
                return;
            }

            // Convertir a mayúsculas
            $nombre_mayusculas = strtoupper($data['NOMBRE']);
            $cargo_mayusculas = strtoupper($data['CARGO']);

            // Registrar miembro
            $respuesta = $this->MiembrosudModel->InsertarMiembro(
                $nombre_mayusculas,
                $cargo_mayusculas,
                $data['EMAIL'],
                $data['urlLogin']
            );

            // Verificar respuesta de la función InsertarMiembro
            if ($respuesta === 201) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Miembro registrado correctamente']);
            } else {
                throw new Exception('No se pudo registrar el miembro.');
            }
        } catch (Exception $e) {
            // Manejo de excepciones
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function RegistrarSolicitanteeee()
    {
        try {
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);

            // Validar campos requeridos
            $requiredFields = ['NOMBRE', 'IDENTIFICACION', 'TIPODESOLICITANTE', 'EMAIL', 'CELULAR', 'PASSWORD'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => "El campo $field es obligatorio."]);
                    return;
                }
            }

            // Validar formato de email
            if (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Formato de email no válido']);
                return;
            }

            // Verificar si el email ya existe
            if ($this->SolicitantesModel->verificarEmail($data['EMAIL'])) {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'El usuario ya está registrado.']);
                return;
            }

            // Convertir a mayúsculas
            $nombre_mayusculas = strtoupper($data['NOMBRE']);
            $tipo_mayusculas = strtoupper($data['TIPODESOLICITANTE']);

            // Registrar miembro
            $respuesta = $this->SolicitantesModel->InsertarSolicitante(
                $nombre_mayusculas,
                $$data['IDENTIFICACION'],
                $tipo_mayusculas,
                $data['EMAIL'],
                $data['CELULAR'],
                $data['PASSWORD']
            );

            // Verificar respuesta de la función InsertarMiembro
            if ($respuesta === 201) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Miembro registrado correctamente']);
            } else {
                throw new Exception('No se pudo registrar el miembro.');
            }
        } catch (Exception $e) {
            // Manejo de excepciones
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function RegistrarSolicitante()
    {
        try {
            // Obtener datos de la solicitud
            $data = json_decode(file_get_contents("php://input"), true);
    
            // Validar campos requeridos
            $requiredFields = ['NOMBRE', 'IDENTIFICACION', 'TIPODESOLICITANTE', 'EMAIL', 'CELULAR', 'PASSWORD'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->responder(400, 'error', "El campo $field es obligatorio.");
                    return;
                }
            }
    
            // Validar formato de email
            if (!filter_var($data['EMAIL'], FILTER_VALIDATE_EMAIL)) {
                $this->responder(400, 'error', 'Formato de email no válido');
                return;
            }
    
            // Verificar si el email ya existe
            if ($this->SolicitantesModel->verificarEmail($data['EMAIL'])) {
                $this->responder(409, 'error', 'El usuario ya está registrado.');
                return;
            }
    
            // Convertir a mayúsculas
            $nombre_mayusculas = strtoupper($data['NOMBRE']);
            $tipo_mayusculas = strtoupper($data['TIPODESOLICITANTE']);
    
            // Registrar solicitante
            $respuesta = $this->SolicitantesModel->InsertarSolicitante(
                $nombre_mayusculas,
                $data['IDENTIFICACION'],
                $tipo_mayusculas,
                $data['EMAIL'],
                $data['CELULAR'],
                $data['PASSWORD']
            );
    
            // Verificar respuesta del modelo
            switch ($respuesta) {
                case 201:
                    $this->responder(201, 'success', 'Solicitante registrado correctamente');
                    break;
                case 400:
                    $this->responder(400, 'error', 'Dominio de email no permitido.');
                    break;
                case 409:
                    $this->responder(409, 'error', 'La identificación ya está registrada.');
                    break;
                default:
                    throw new Exception('No se pudo registrar el solicitante.');
            }
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->responder(500, 'error', $e->getMessage());
        }
    }
    
    private function responder($statusCode, $status, $message)
    {
        http_response_code($statusCode);
        echo json_encode(['status' => $status, 'message' => $message]);
    }

    public function CambiarPasswordUsuario()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data || empty($data['PasswordActual']) || empty($data['NuevaPassword'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
                return;
            }

            $respuesta = $this->userModel->CambiarPasswordUsuario(
                $data['PasswordActual'],
                $data['NuevaPassword']
            );

            if ($respuesta['success']) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => $respuesta['message']]);
            } else {
                throw new Exception($respuesta['message']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $response = $this->userModel->login($data);

        if (isset($response['error'])) {
            http_response_code(400); // Error de validación
            echo json_encode(['message' => $response['error']]);
        } else {
            http_response_code(200); // Exitoso
            echo json_encode([
                'message' => $response['message'],
                'token' => $response['token'],
                'nombre' => $response['nombre'],
                'email' => $response['email']
            ]);
        }
    }

    public function logout()
    {
        try {
            $respuesta = $this->userModel->logout();

            if ($respuesta['success']) {
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => $respuesta['message']]);
            } else {
                throw new Exception($respuesta['message']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

     // Función para obtener el ID del miembro basado en el token
    public function IDMiembro(){
        $authMiddleware = new AuthMiddleware();
        $user = $authMiddleware->authenticate();

        if (isset($user['error'])) {
            http_response_code(401);
            echo json_encode(['message' => $user['error']]);
            exit;
        }
        $idToken = $user['id_token'];  
        $response = $this->userModel->getIdMiembroByToken($idToken);
        echo $response;
    }

}

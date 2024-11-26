<?php
//require_once BASE_PATH . '/app/models/Database.php';
require_once  'DataBaseManejoActas.php';
require_once 'app/middlewares/AuthMiddleware.php';

use Firebase\JWT\JWT;

class User
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }
    // Método para registrar un nuevo usuario
    public function register($Nombre, $email, $password, $role)
    {
        $query = "INSERT INTO usuarios (nombre,email, password, rol) VALUES (:nombre,:email, :password, :rol)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $Nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':rol', $role);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para verificar las credenciales del usuario
    public function verifyCredentials($email, $password, $rol)
    {
        $query = "SELECT * FROM usuarios WHERE email = :email AND rol = :rol LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar la contraseña ingresada con la almacenada
            if (password_verify($password, $user['password'])) {
                return $user; // Retorna el usuario si las credenciales son correctas
            }
        }

        return null; // Retorna null si no se encontró el usuario o las credenciales son incorrectas
    }

    public function mostrarAlgo()
    {
        return "hola index desde model";
    }

    public function usuarioExists($email, $rol)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND rol = :rol");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        $stmt->execute();

        // Retorna verdadero si el correo existe, falso en caso contrario
        return $stmt->fetchColumn() > 0;
    }

    public function saveToken($idUsuario, $token, $expiracionTimestamp)
    {
        $expiracion = date('Y-m-d H:i:s', $expiracionTimestamp);
        $query = "INSERT INTO tokens (id_usuario, token, expiracion) VALUES (:id_usuario, :token, :expiracion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $idUsuario);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiracion', $expiracion);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Devuelve el ID del token recién creado
        }

        return false; // Indica que no se pudo insertar
    }

    public function updateToken($idToken, $jwt)
    {
        $query = "UPDATE tokens SET token = :token WHERE id_token = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $jwt);
        $stmt->bindParam(':id', $idToken);
        return $stmt->execute();
    }

    public function login($data)
    {
        // Iniciar la transacción
        $this->conn->beginTransaction();

        try {
            if (!isset($data['email'], $data['password'], $data['rol'])) {
                throw new Exception('Correo, contraseña y rol son obligatorios');
            }

            // Validar el email
            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new Exception('Correo electrónico no válido');
            }

            // Extraer contraseña y rol
            $password = $data['password'];
            $rol = $data['rol'];

            // Verificar las credenciales y rol
            $user = $this->verifyCredentials($email, $password, $rol);

            if (!$user) {
                throw new Exception('Credenciales incorrectas o rol no válido');
            }

            // Generar el JWT
            $payload = [
                'rol' => $user['rol'],
                'iat' => time(),
                'exp' => time() + (60 * 60) // Expira en 1 hora
            ];

            // Usar la clave secreta para el JWT
            $secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Guardar el token en la base de datos
            $idToken = $this->saveToken($user['id_usuario'], $jwt, $payload['exp']);
            if (!$idToken) {
                throw new Exception('Error al guardar el token');
            }

            // Actualizar el token con el id_token
            $payload['id_token'] = $idToken;
            $jwt = JWT::encode($payload, $secretKey, 'HS256'); // Actualizamos el JWT con el id_token

            // Actualizar el token en la base de datos
            if (!$this->updateToken($idToken, $jwt)) {
                throw new Exception('Error al actualizar el token');
            }

            // Si todo salió bien, confirmar la transacción
            $this->conn->commit();

            return [
                'token' => $jwt,
                'message' => 'Inicio de sesión exitoso',
                'nombre' => $user['nombre'],
                'email' => $user['email']
            ];
        } catch (Exception $e) {
            // Si hubo un error, revertir la transacción
            $this->conn->rollBack();
            return ['error' => $e->getMessage()];
        }
    }


    public function CambiarPasswordUsuario($ActualPassword, $nuevaPassword)
    {
        $authMiddleware = new AuthMiddleware();
        $user = $authMiddleware->authenticate();

        if (isset($user['error'])) {
            http_response_code(401);
            echo json_encode(['message' => $user['error']]);
            exit;
        }

        $idToken = $user['id_token'];

        try {
            $this->conn->beginTransaction();

            $queryToken = "SELECT id_usuario FROM tokens WHERE id_token = :id_token";
            $stmtToken = $this->conn->prepare($queryToken);
            $stmtToken->bindParam(':id_token', $idToken);
            $stmtToken->execute();
            $resultado = $stmtToken->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                throw new Exception("Token inválido o no encontrado.");
            }

            $idUsuario = $resultado['id_usuario'];

            $queryPassword = "SELECT password FROM usuarios WHERE id_usuario = :id_usuario";
            $stmtPassword = $this->conn->prepare($queryPassword);
            $stmtPassword->bindParam(':id_usuario', $idUsuario);
            $stmtPassword->execute();
            $resultadoPassword = $stmtPassword->fetch(PDO::FETCH_ASSOC);

            if (!$resultadoPassword) {
                throw new Exception("Usuario no encontrado.");
            }

            $passwordRegistrada = $resultadoPassword['password'];

            if (!password_verify($ActualPassword, $passwordRegistrada)) {

                throw new Exception("La contraseña actual no es correcta.");
            }

            $nuevoPasswordHashed = password_hash($nuevaPassword, PASSWORD_DEFAULT);

            $queryUpdate = "UPDATE usuarios SET password = :nuevo_password WHERE id_usuario = :id_usuario";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':nuevo_password', $nuevoPasswordHashed);
            $stmtUpdate->bindParam(':id_usuario', $idUsuario);
            $stmtUpdate->execute();

            $this->conn->commit();

            return ["success" => true, "message" => "Contraseña actualizada exitosamente."];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["success" => false, "message" => $e->getMessage()];
        }
    }


    public function logout()
    {
        // Eliminar el token actual de la base de datos
        $authMiddleware = new AuthMiddleware();
        $user = $authMiddleware->authenticate();

        if (isset($user['error'])) {
            http_response_code(401);
            echo json_encode(['message' => $user['error']]);
            exit;
        }

        $idToken = $user['id_token'];
        $query = "DELETE FROM tokens WHERE id_token = :id_token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_token', $idToken);
        $stmt->execute();

        return ['success' => true, 'message' => 'Sesión cerrada correctamente.'];
    }


    // Verificar las credenciales del usuario
    public function verificarEmail($email,$rol)
    {
        try {

            $query = "SELECT COUNT(*) FROM usuarios WHERE email  = :email  AND rol = :rol";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['COUNT(*)'] > 0) {
                return  true;
            } else {
                return  false;
            }
        } catch (PDOException $e) {
            return  500;
        }
    }

    public function getIdMiembroByToken($token) {
        try {
            // Consulta para obtener el ID del miembro
            $query = "
                SELECT miembros.IDMIEMBRO 
                FROM tokens 
                INNER JOIN usuarios ON usuarios.id_usuario = tokens.id_usuario 
                LEFT JOIN miembros ON miembros.Email = usuarios.email 
                WHERE tokens.id_token = :token";
                
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si el miembro existe, retornar el ID del miembro
            if ($result) {
                return $result['IDMIEMBRO'];
            }

            // Si no se encuentra el miembro
            return null;

        } catch (PDOException $e) {
            // Manejo de error si la consulta falla
            return null;
        }
    }

}

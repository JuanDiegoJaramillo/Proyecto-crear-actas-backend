<?php
require_once 'app/models/DataBaseManejoActas.php';
require_once   'app/Email/EnviarEmail.php';
require_once 'app/middlewares/AuthMiddleware.php';
class Invitados
{
    private $conn;

    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }


    public function SelectInvitados()
    {
        try {
            $query = "SELECT  invitados.IDINVITADOS, invitados.NOMBRE, invitados.DEPENDENCIA  , invitados.CARGO FROM invitados";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 0; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return 500;
        }
    }

    public function InsertarInvitados($NOMBRE, $DEPENDENCIA, $CARGO, $EMAIL, $urlLogin)
    {
        try {
            $this->conn->beginTransaction();

            // Generar contraseña aleatoria y encriptarla
            $passwordAleatorio = $this->generarPasswordAleatorio();
            $passwordEncriptado = password_hash($passwordAleatorio, PASSWORD_DEFAULT);

            // Insertar en la tabla `usuarios`
            $queryUsuario = "INSERT INTO usuarios (nombre, email, password, rol) 
                         VALUES (:nombre, :email, :password, 'invitado')";
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            $stmtUsuario->execute([
                ':nombre' => $NOMBRE,
                ':email' => $EMAIL,
                ':password' => $passwordEncriptado
            ]);

            // Insertar en la tabla `invitados`
            $queryInvitado = "INSERT INTO invitados (NOMBRE, DEPENDENCIA, CARGO, EMAIL) 
                          VALUES (:nombre, :dependencia, :cargo, :email)";
            $stmtInvitado = $this->conn->prepare($queryInvitado);
            $stmtInvitado->execute([
                ':nombre' => $NOMBRE,
                ':dependencia' => $DEPENDENCIA,
                ':cargo' => $CARGO,
                ':email' => $EMAIL
            ]);
                 // Confirmar transacción
                 $this->conn->commit();
            $rol = "invitado";
            // Enviar correo al invitado
            $correo = new Enviar_correo();
            if (!$correo->DatosLoginUsuario($NOMBRE, $EMAIL, $EMAIL, $passwordAleatorio, $urlLogin, $rol)) {
                throw new Exception('Error al enviar el correo.');
            }
            return 201;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return 500;
        }
    }

    function generarPasswordAleatorio($longitud = 8)
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        return substr(str_shuffle($caracteres), 0, $longitud);
    }


    //Actualizar invitados.
    public function ActualizarInvitados() {}

    //Eliminar invitados
    public function EliminarInvitados() {}

    // Endpoint para verificar si el email está registrado
    public function verificarEmail($email)
    {
        try {

            $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email  AND rol = 'invitado'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
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

    public function DetallesUsuarioInvitado()
    {
        $authMiddleware = new AuthMiddleware();
        // Obtener el token del encabezado Authorization
        // Autenticar al usuario
        $user = $authMiddleware->authenticate();

        if (isset($user['error'])) {
            http_response_code(401);
            echo json_encode(['message' => $user['error']]);
            exit;
        }
      
        try {
            $idToken = $user['id_token'];
            $query = "SELECT invitados.IDINVITADOS, invitados.NOMBRE, invitados.DEPENDENCIA  , invitados.CARGO , invitados.EMAIL FROM tokens inner join usuarios on usuarios.id_usuario = tokens.id_usuario LEFT JOIN invitados on invitados.EMAIL = usuarios.email where tokens.id_token = :idToken;";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idToken', $idToken);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 0; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return 500;
        }
    }
}

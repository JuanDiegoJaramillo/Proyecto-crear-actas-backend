<?php
require_once 'app/models/DataBaseManejoActas.php';
class Miembros
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }


    public function SelectMiembros()
    {
        try {
            $query = "SELECT miembros.IDMIEMBRO, miembros.NOMBRE , miembros.CARGO FROM  miembros";
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

    public function MiembrosAsignadosTarea($IDtarea)
    {
        try {
            $query = "SELECT 
         miembros.IDMIEMBRO,
         miembros.NOMBRE,
         miembros.CARGO,
         CASE WHEN encargados_tareas.TAREAS_IDTAREAS IS NOT NULL THEN 1 ELSE 0 END AS ASIGNADO
         FROM miembros LEFT JOIN encargados_tareas 
         ON miembros.IDMIEMBRO = encargados_tareas.MIEMBROS_IDMIEMBROS 
         AND encargados_tareas.TAREAS_IDTAREAS = :IDTAREA  -- Identifica la tarea actual
         ORDER BY miembros.NOMBRE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDTAREA', $IDtarea);
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

    public function InsertarMiembro($NOMBRE, $CARGO, $EMAIL, $urlLogin)
    {
        try {
            $this->conn->beginTransaction();

            // Generar contraseña aleatoria y encriptarla
            $passwordAleatorio = $this->generarPasswordAleatorio();
            $passwordEncriptado = password_hash($passwordAleatorio, PASSWORD_DEFAULT);

            // Insertar en la tabla `usuarios`
            $queryUsuario = "INSERT INTO usuarios (nombre, email, password, rol) 
                         VALUES (:nombre, :email, :password, 'miembro')";
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            $stmtUsuario->execute([
                ':nombre' => $NOMBRE,
                ':email' => $EMAIL,
                ':password' => $passwordEncriptado
            ]);

            // Insertar en la tabla `miembros`
            $queryMiembro = "INSERT INTO miembros (NOMBRE, CARGO, Email) 
                          VALUES (:nombre, :cargo, :email)";
            $stmtMiembro = $this->conn->prepare($queryMiembro);
            $stmtMiembro->execute([
                ':nombre' => $NOMBRE,
                ':cargo' => $CARGO,
                ':email' => $EMAIL
            ]);
            // Confirmar transacción
            $this->conn->commit();
            // Enviar correo al invitado
            $rol = "miembro";
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

    public function verificarEmail($email)
    {
        try {
            $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email  AND rol = 'miembro'";
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

    function generarPasswordAleatorio($longitud = 8)
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        return substr(str_shuffle($caracteres), 0, $longitud);
    }


    public function DetallesUsuarioMiembro()
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
            $query = "SELECT miembros.IDMIEMBRO, miembros.NOMBRE, miembros.CARGO , miembros.Email FROM tokens inner join usuarios on usuarios.id_usuario = tokens.id_usuario LEFT JOIN miembros on miembros.Email = usuarios.email where tokens.id_token = :idToken;";
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

    public function getIMiembro (){
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
            $query = "SELECT miembros.IDMIEMBRO FROM tokens inner join usuarios on usuarios.id_usuario = tokens.id_usuario LEFT JOIN miembros on miembros.Email = usuarios.email where tokens.id_token = :idToken";
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

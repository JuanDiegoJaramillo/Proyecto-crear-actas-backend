<?php
require_once  'app/models/DataBaseManejoActas.php';
class Solicitantes
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }


    public function mostrarSolicitanteIDSolicitud($idSolicitud)
    {
        try {
            $query = "SELECT solicitantes.IDSOLICITANTE, solicitantes.NOMBRE,solicitantes.EMAIL,solicitantes.CELULAR, solicitantes.TIPODESOLICITANTE FROM solicitantes INNER JOIN solicitud ON solicitud.SOLICITANTE_IDSOLICITANTE=IDSOLICITANTE WHERE  solicitantes.IDSOLICITANTE = :idSolicitud";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idSolicitud', $idSolicitud);
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

    //insertar solicitante.
    public function InsertarSolicitantee($Nombre, $IDENTIFICACION, $TipoSolicitante, $Email, $Celular)
    {
        $sql = "INSERT INTO solicitantes (NOMBRE,IDENTIFICACION,TIPODESOLICITANTES,EMAIL,CELULAR) VALUE (:Nombre,:identificacion,:Tipo,:Email,:Celular)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':identificacion', $IDENTIFICACION);
        $stmt->bindParam(':Tipo', $TipoSolicitante);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Celular', $Celular);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function InsertarSolicitante($Nombre, $Idenificacion, $TipoSolicitante, $Email, $Celular, $password)
    {
        try {
            // Validar el dominio del correo electrónico
            $dominioPermitido = "correo.unicordoba.edu.co"; // Cambia esto por el dominio que necesitas
            if (!$this->validarDominioCorreo($Email, $dominioPermitido)) {
                return 400; // Código de error para dominio no permitido
            }
            // Verificar si la identificación ya existe
            if ($this->identificacionExiste($Idenificacion)) {
                return 409; // Código de error para conflicto (identificación duplicada)
            }
            $this->conn->beginTransaction();
            $passwordEncriptado = password_hash($password, PASSWORD_DEFAULT);
            // Insertar en la tabla `usuarios`
            $queryUsuario = "INSERT INTO usuarios (nombre, email, password, rol) 
                         VALUES (:nombre, :email, :password, 'solicitante')";
            $stmtUsuario = $this->conn->prepare($queryUsuario);
            $stmtUsuario->execute([
                ':nombre' => $Nombre,
                ':email' => $Email,
                ':password' => $passwordEncriptado
            ]);

            // Insertar en la tabla `solicitante`
            $queryInvitado = "INSERT INTO solicitantes (NOMBRE,IDENTIFICACION,TIPODESOLICITANTE,EMAIL,CELULAR) VALUE (:Nombre,:Identificacion,:TipoSolicitante,:Email,:Celular)";
            $stmtInvitado = $this->conn->prepare($queryInvitado);
            $stmtInvitado->execute([
                ':Nombre' => $Nombre,
                ':Identificacion' => $Idenificacion,
                ':TipoSolicitante' => $TipoSolicitante,
                ':Email' => $Email,
                ':Celular' => $Celular

            ]);
            // Confirmar transacción
            $this->conn->commit();

            return 201;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log($e->getMessage());
            return 500;
        }
    }
    private function validarDominioCorreo($email, $dominioPermitido)
    {
        $dominio = substr(strrchr($email, "@"), 1); // Extraer el dominio del correo
        return $dominio === $dominioPermitido;
    }
    public function verificarEmail($email)
    {
        try {

            $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email  AND rol = 'solicitante'";
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

    private function identificacionExiste($identificacion)
    {
        $query = "SELECT COUNT(*) FROM solicitantes WHERE IDENTIFICACION = :identificacion";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':identificacion' => $identificacion]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }
    //Actualizar solicitante.
    public function ActualizarSolicitante($Nombre, $TipoSolicitante, $Email, $Celular, $id)
    {
        $sql = "UPDATE solicitantes SET  NOMBRE=:Nombre, TIPODESOLICITANTE=:Tipo, EMAIL=:Email, CELULAR=:Celular,  WHERE IDSOLICITANTE=:IDsolicitante";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':Nombre', $Nombre);
        $stmt->bindParam(':Tipo', $TipoSolicitante);
        $stmt->bindParam(':Email', $Email);
        $stmt->bindParam(':Celular', $Celular);
        $stmt->bindParam(':IDsolicitante', $id);
    }

    //Eliminar silisitante.
    public function EliminarSolicitante($id)
    {
        $sql = "DELETE FROM solicitantes WHERE IDSOLICITANTE=:IDsolicitante";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':IDsolicitante', $id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function mostrarSolicitantes()
    {
        try {
            $query = "SELECT solicitantes.IDSOLICITANTE, solicitantes.NOMBRE, solicitantes.IDENTIFICACION, solicitantes.EMAIL,solicitantes.CELULAR, solicitantes.TIPODESOLICITANTE FROM solicitantes ";
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


    public function mostrarSolicitantesConSolicitudes()
    {
        try {
            $query = "SELECT distinct IDSOLICITANTE,NOMBRE,IDENTIFICACION,TIPODESOLICITANTE,EMAIL, CELULAR FROM solicitantes inner	join solicitud on solicitud.SOLICITANTE_IDSOLICITANTE = solicitantes.IDSOLICITANTE;";
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

    public function mostrarSolicitantesPorTipo($tipo)
    {
        try {
            $Tipo = strtoupper($tipo);
            $query = "SELECT distinct IDSOLICITANTE,NOMBRE,IDENTIFICACION,TIPODESOLICITANTE,EMAIL, CELULAR FROM solicitantes inner join solicitud on solicitud.SOLICITANTE_IDSOLICITANTE = solicitantes.IDSOLICITANTE where solicitantes.TIPODESOLICITANTE = :Tipo";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':Tipo',$Tipo);
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

    public function buscarSolicitante($criterio)
{
    try {
        // Consulta para obtener los datos del solicitante y su estado respecto a solicitudes
        $query = "
            SELECT DISTINCT
                s.IDSOLICITANTE, 
                s.NOMBRE, 
                s.IDENTIFICACION, 
                s.TIPODESOLICITANTE, 
                s.EMAIL, 
                s.CELULAR,
                CASE 
                    WHEN COUNT(sol.SOLICITANTE_IDSOLICITANTE) > 0 THEN 1
                    ELSE 0
                END AS estado_solicitudes
            FROM solicitantes s
            LEFT JOIN solicitud sol ON sol.SOLICITANTE_IDSOLICITANTE = s.IDSOLICITANTE
            WHERE s.IDENTIFICACION LIKE :criterioParcial
              OR s.IDENTIFICACION = :criterioExacto
              OR s.NOMBRE LIKE :criterioNombre
            GROUP BY s.IDSOLICITANTE, s.NOMBRE, s.IDENTIFICACION, s.TIPODESOLICITANTE, s.EMAIL, s.CELULAR
        ";

        $stmt = $this->conn->prepare($query);

        if (ctype_digit((string)$criterio)) {
            // Si el criterio es numérico, configuramos criterios de ID
            $criterioParcial = "$criterio%"; // Búsqueda parcial por ID
            $criterioExacto = $criterio;    // Búsqueda exacta por ID
            $criterioNombre = "";           // Ignorar nombres en este caso
        } else {
            // Si el criterio no es numérico, configuramos búsqueda por nombre
            $Nombre = str_replace('t2', ' ', $criterio);
            $Nombre_mayusculas = strtoupper($Nombre);
            $criterioNombre = "$Nombre_mayusculas%";
            $criterioParcial = "0";         // Valor que no coincidirá con ningún ID válido
            $criterioExacto = "0";          // Valor que no coincidirá con ningún ID válido
        }
        // Vincular parámetros
        $stmt->bindParam(':criterioParcial', $criterioParcial, PDO::PARAM_STR);
        $stmt->bindParam(':criterioExacto', $criterioExacto, PDO::PARAM_STR);
        $stmt->bindParam(':criterioNombre', $criterioNombre, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no encuentra registros
        if (empty($result)) {
            return [
                "mensaje" => "No se encontró ningún solicitante con el criterio proporcionado.",
                "data" => []
            ];
        }

        // Verificar si el solicitante tiene solicitudes
        foreach ($result as &$solicitante) {
            if ($solicitante['estado_solicitudes'] === 0) {
                $solicitante['mensaje_estado'] = 'El solicitante existe pero no tiene solicitudes.';
            } else {
                $solicitante['mensaje_estado'] = 'El solicitante tiene solicitudes.';
            }
        }

        // Devolver la información del solicitante con su estado respecto a solicitudes
        return [
            "mensaje" => "Solicitantes encontrados.",
            "data" => $result
        ];
    } catch (PDOException $e) {
        return [
            "mensaje" => "Error en la consulta: " . $e->getMessage(),
            "data" => []
        ];
    }
}



    //Usuario solicitante
    public function DetallesUsuarioSolicitante()
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
            $query = "SELECT solicitantes.IDSOLICITANTE, solicitantes.NOMBRE, solicitantes.IDENTIFICACION ,solicitantes.TIPODESOLICITANTE, solicitantes.EMAIL,solicitantes.CELULAR FROM tokens inner join usuarios on usuarios.id_usuario = tokens.id_usuario LEFT JOIN solicitantes on solicitantes.EMAIL = usuarios.email where tokens.id_token = :idToken;";
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

    public function getIDolicitante (){
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
            $query = "SELECT solicitantes.IDSOLICITANTE FROM tokens inner join usuarios on usuarios.id_usuario = tokens.id_usuario LEFT JOIN solicitantes on solicitantes.EMAIL = usuarios.email where tokens.id_token = :idToken;";
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

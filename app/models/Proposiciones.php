<?php

require_once 'app/models/DataBaseManejoActas.php';

class Proposiciones
{
    private $conn;

    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    // Obtener todas las proposiciones
    public function listarProposiciones()
    {
        try {
            $query = "SELECT p.ID_PROPOSICIONES, p.DESCRIPCION, p.DESICION, 
                             m.NOMBRE AS NOMBRE_MIEMBRO, 
                             s.LUGAR AS LUGAR_SESION, s.FECHA AS FECHA_SESION 
                      FROM proposiciones p
                      JOIN miembros m ON p.MIEMBRO_IDMIEMBRO = m.IDMIEMBRO
                      JOIN sesion s ON p.SESION_IDSESION = s.IDSESION";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: 0; // Retorna 0 si no hay registros
        } catch (PDOException $e) {
            error_log("Error al listar proposiciones: " . $e->getMessage());
            return 500; // Error interno del servidor
        }
    }

    public function listarProposicionesSesion($idSesion)
    {
        try {
            $query = "SELECT  proposiciones.ID_PROPOSICIONES,
    proposiciones.DESCRIPCION,
    proposiciones.FECHA,
    miembros.NOMBRE AS autor,
    proposiciones.MIEMBRO_IDMIEMBRO 
    from proposiciones inner join miembros on miembros.IDMIEMBRO = proposiciones.MIEMBRO_IDMIEMBRO 
    where proposiciones.SESION_IDSESION = :idSesion order by proposiciones.FECHA ASC;";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idSesion', $idSesion);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result ?: 0; // Retorna 0 si no hay registros
        } catch (PDOException $e) {
            error_log("Error al listar proposiciones: " . $e->getMessage());
            return 500; // Error interno del servidor
        }
    }


    // Insertar una nueva proposición
    public function getIdMiembroByToken($token)
    {
        try {
            // Consulta SQL para obtener el ID del miembro asociado al token
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

            // Si no se encuentra el miembro, retornar null
            return null;
        } catch (PDOException $e) {
            // Manejo de error si la consulta falla
            error_log("Error en getIdMiembroByToken: " . $e->getMessage());
            return null;
        }
    }

    // Insertar una nueva proposición en la base de datos

    // Actualizar una proposición existente
    public function actualizarProposicion($idProposicion, $descripcion, $decision)
    {
        try {
            $query = "UPDATE proposiciones 
                      SET DESCRIPCION = :descripcion, DESICION = :decision 
                      WHERE ID_PROPOSICIONES = :idProposicion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':descripcion' => $descripcion,
                ':decision' => $decision,
                ':idProposicion' => $idProposicion,
            ]);

            return $stmt->rowCount() > 0 ? 200 : 404; // 200 si se actualizó, 404 si no se encontró
        } catch (PDOException $e) {
            error_log("Error al actualizar proposición: " . $e->getMessage());
            return 500;
        }
    }

    // Eliminar una proposición
    public function eliminarProposicion($idProposicion)
    {
        try {
            $query = "DELETE FROM proposiciones WHERE ID_PROPOSICIONES = :idProposicion";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':idProposicion' => $idProposicion]);

            return $stmt->rowCount() > 0 ? 200 : 404;
        } catch (PDOException $e) {
            error_log("Error al eliminar proposición: " . $e->getMessage());
            return 500;
        }
    }
    public function insertarProposicion($descripcion, $idSesion, $idMiembro)
    {
    try {
        $decision = "";
        $fecha = new DateTime('now', new DateTimeZone('America/Bogota'));
        $fecha = $fecha->format('Y-m-d H:i:s'); // Formato: 2024-11-25 10:00:00
        $Descripcion_mayusculas = strtoupper($descripcion);
        // Preparar la consulta para insertar la proposición
        $query = "INSERT INTO proposiciones (DESCRIPCION, DESICION, FECHA, SESION_IDSESION, MIEMBRO_IDMIEMBRO) 
                  VALUES (:descripcion, :decision, :fecha, :idSesion, :idMiembro)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':descripcion', $Descripcion_mayusculas);
        $stmt->bindParam(':decision', $decision);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':idSesion', $idSesion);
        $stmt->bindParam(':idMiembro', $idMiembro);
        // Ejecutar la consulta
        if ($stmt->execute()) {
            // Obtener el último ID insertado
            $lastInsertId = $this->conn->lastInsertId();

            // Consultar los datos completos de la proposición
            $queryDatos = "SELECT 
                            proposiciones.ID_PROPOSICIONES,
                            proposiciones.DESCRIPCION,
                            proposiciones.FECHA,
                            miembros.NOMBRE AS autor,
                            proposiciones.MIEMBRO_IDMIEMBRO
                           FROM proposiciones
                           INNER JOIN miembros ON miembros.IDMIEMBRO = proposiciones.MIEMBRO_IDMIEMBRO
                           WHERE proposiciones.ID_PROPOSICIONES = :id
                           ORDER BY proposiciones.FECHA ASC";
            $stmtDatos = $this->conn->prepare($queryDatos);
            $stmtDatos->bindParam(':id', $lastInsertId);

            if ($stmtDatos->execute()) {
                $datosProposicion = $stmtDatos->fetch(PDO::FETCH_ASSOC);
                return [
                    'status' => 201, // Creado
                    'data' => $datosProposicion
                ];
            } else {
                return [
                    'status' => 500, 
                    'error' => 'Error al obtener los datos completos de la proposición.'
                ];
            }
        } else {
            return [
                'status' => 500, 
                'error' => 'Error al insertar la proposición.'
            ];
        }
    } catch (PDOException $e) {
        // Manejo de error al insertar la proposición
        error_log("Error al insertar proposición: " . $e->getMessage());
        return [
            'status' => 500, 
            'error' => 'Error interno del servidor.'
        ];
    }
}
}

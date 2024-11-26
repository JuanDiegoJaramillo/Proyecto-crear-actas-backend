<?php
require_once  'app/models/DataBaseManejoActas.php';
class AsistenciaMiembros
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function commit()
    {
        $this->conn->commit();
    }

    public function rollBack()
    {
        $this->conn->rollBack();
    }

    public function AsistenciaMiembroSesion($idSesion)
    {
        try {
            $query = "SELECT MIEMBROS_IDMIEMBRO, CARGO, NOMBRE, ESTADO_ASISTENCIA FROM asistencia_miembros INNER JOIN  miembros ON miembros.IDMIEMBRO = asistencia_miembros.MIEMBROS_IDMIEMBRO INNER JOIN sesion ON sesion.IDSESION = asistencia_miembros.SESION_IDSESION WHERE SESION_IDSESION = :idSesion";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idSesion', $idSesion);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 400; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            print_r('Error: ' . $e->getMessage());
            return 500;
        }
    }

    public function actualizarAsistenciaMiembros($estado, $idSesion, $idMiembro)
    {
        $queryActualizacion = "UPDATE asistencia_miembros SET ESTADO_ASISTENCIA = :ESTADO WHERE (SESION_IDSESION = :IDSESION) and (MIEMBROS_IDMIEMBRO = :IDMIEMBRO)";
        $stmtActualizacion = $this->conn->prepare($queryActualizacion);
        $stmtActualizacion->bindParam(':ESTADO', $estado);
        $stmtActualizacion->bindParam(':IDSESION', $idSesion);
        $stmtActualizacion->bindParam(':IDMIEMBRO', $idMiembro);
        if ($stmtActualizacion->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function cargarAsistenciaMiembros($sessionId)
    {
        try {

            if ($this->todosMiembrosCitados($sessionId)) {
                return true; // Si todos están citados, no hay necesidad de insertar
            }
            // Iniciar la transacción
            $this->beginTransaction();

            // Recuperar todos los miembros registrados
            $query = "SELECT IDMIEMBRO FROM miembros";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $miembros = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Preparar la inserción en asistencia_miembros
            $insertQuery = "INSERT INTO asistencia_miembros (SESION_IDSESION, MIEMBROS_IDMIEMBRO, ESTADO_ASISTENCIA) VALUES (:session_id, :miembro_id, '')";
            $insertStmt = $this->conn->prepare($insertQuery);

            // Insertar cada miembro en asistencia_miembros
            foreach ($miembros as $miembro) {
                $insertStmt->bindParam(':session_id', $sessionId);
                $insertStmt->bindParam(':miembro_id', $miembro['IDMIEMBRO']);
                $insertStmt->execute();
            }

            // Confirmar la transacción si todo salió bien
            $this->commit();
            return true;
        } catch (Exception $e) {
            // Cancelar la transacción en caso de error
            $this->rollBack();
            return false;
        }
    }



    public function todosMiembrosCitados2($sessionId)
    {
        $query = "SELECT COUNT(m.IDMIEMBRO) AS total_miembros, 
               COUNT(am.MIEMBROS_IDMIEMBRO) AS citados
        FROM miembros AS m
        LEFT JOIN asistencia_miembros AS am
        ON m.IDMIEMBRO = am.MIEMBROS_IDMIEMBRO AND am.SESION_IDSESION = :sessionId
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sessionId', $sessionId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar que hay miembros y que todos están citados
        return $result['total_miembros'] > 0 && $result['total_miembros'] === $result['citados'];
    }
    public function todosMiembrosCitados($sessionId)
{
    $query = "SELECT 
                 COUNT(m.IDMIEMBRO) AS total_miembros, 
                 COUNT(am.MIEMBROS_IDMIEMBRO) AS citados
              FROM miembros AS m
              LEFT JOIN asistencia_miembros AS am
              ON m.IDMIEMBRO = am.MIEMBROS_IDMIEMBRO 
              AND am.SESION_IDSESION = :sessionId";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':sessionId', $sessionId, PDO::PARAM_INT);
    $stmt->execute();

    // Manejar resultados en caso de error o datos vacíos
    $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_miembros' => 0, 'citados' => 0];

    // Log para depuración
    error_log("Resultados consulta: " . print_r($result, true));

    // Verificar que hay miembros y que todos están citados
    return $result['total_miembros'] > 0 && $result['total_miembros'] === (int)$result['citados'];
}

}

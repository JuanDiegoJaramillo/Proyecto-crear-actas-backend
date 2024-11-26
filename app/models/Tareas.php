<?php
require_once  'app/models/DataBaseManejoActas.php';
class Tareas
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    function obtenerFechaActualColombia()
    {
        // Establecer la zona horaria a Bogotá
        date_default_timezone_set('America/Bogota');
        // Obtener la fecha actual
        return date('Y-m-d');
    }

    public function TareaExists($DESCRIPCION, $idSesion)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tareas WHERE tareas.DESCRIPCION = :DESCRIPCION  AND tareas.SESION_IDSESION = :idsesion");
        $stmt->bindParam(':DESCRIPCION', $DESCRIPCION);
        $stmt->bindParam(':idsesion', $idSesion);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function DescripcionExiste($DESCRIPCION, $IDSESION)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tareas WHERE tareas.DESCRIPCION = :DESCRIPCION  AND tareas.SESION_IDSESION = :IDSESION");
        $stmt->bindParam(':DESCRIPCION', $DESCRIPCION);
        $stmt->bindParam(':IDSESION', $IDSESION);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    public function InsertarTarea($DESCRIPCION, $SESION_IDSESION)
    {
        try {
            $fechaActual = $this->obtenerFechaActualColombia();
            $queryTarea = "INSERT INTO tareas (DESCRIPCION ,FECHA_ENTREGA,SESION_IDSESION) VALUES (:DESCRIPCION,:FECHA_ENTREGA,:SESION_IDSESION)";
            $stmtTarea = $this->conn->prepare($queryTarea);
            $stmtTarea->bindParam(':DESCRIPCION', $DESCRIPCION);
            $stmtTarea->bindParam(':FECHA_ENTREGA', $fechaActual);
            $stmtTarea->bindParam(':SESION_IDSESION', $SESION_IDSESION);
            if ($stmtTarea->execute()) {
                return 201; // Acta creada correctamente
            } else {
                return 400; // Error al insertar el acta
            }
        } catch (PDOException $e) {
            // Devuelve un código de error y no el objeto de excepción
            return 500;
        }
    }


    public function MostrarTareas()
    {
        try {
            $query = "  SELECT tareas.ID_TAREAS,tareas.DESCRIPCION,tareas.FECHA_ENTREGA,tareas.SESION_IDSESION,GROUP_CONCAT(miembros.NOMBRE ORDER BY miembros.NOMBRE SEPARATOR ', ') AS RESPONSABLES
            FROM tareas
            LEFT JOIN encargados_tareas ON tareas.ID_TAREAS = encargados_tareas.TAREAS_IDTAREAS
            LEFT JOIN miembros ON encargados_tareas.MIEMBROS_IDMIEMBROS = miembros.IDMIEMBRO
            WHERE (encargados_tareas.ESTADO = 'PENDIENTE' OR encargados_tareas.TAREAS_IDTAREAS IS NULL) 
            GROUP BY tareas.ID_TAREAS, tareas.DESCRIPCION, tareas.FECHA_ENTREGA";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 0; //  no hay registros
            }
        } catch (PDOException $e) {
            return 500;
        }
    }

    public function MotrarTareasIDSesion($idSesion)
    {
        try {
            $query = "SELECT 
    tareas.ID_TAREAS,
    tareas.DESCRIPCION,
    tareas.FECHA_ENTREGA,
    tareas.SESION_IDSESION,
    GROUP_CONCAT(miembros.NOMBRE ORDER BY miembros.NOMBRE SEPARATOR ', ') AS RESPONSABLES
FROM 
    tareas
LEFT JOIN 
    encargados_tareas ON tareas.ID_TAREAS = encargados_tareas.TAREAS_IDTAREAS 
                       AND encargados_tareas.ESTADO = 'PENDIENTE'
LEFT JOIN 
    miembros ON encargados_tareas.MIEMBROS_IDMIEMBROS = miembros.IDMIEMBRO
WHERE 
    tareas.SESION_IDSESION = :IDSESION
GROUP BY 
    tareas.ID_TAREAS, tareas.DESCRIPCION, tareas.FECHA_ENTREGA, tareas.SESION_IDSESION;";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDSESION', $idSesion);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 0; //  no hay registros
            }
        } catch (PDOException $e) {
            return 500;
        }
    }

    public function ActualizaTarea($DESCRIPCION, $IDtarea)
    {
        try {

            $query = "UPDATE tareas SET DESCRIPCION = :DESCRIPCION  WHERE (ID_TAREAS = :IDTAREA)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':DESCRIPCION', $DESCRIPCION);
            $stmt->bindParam(':IDTAREA', $IDtarea);
            if ($stmt->execute()) {
                return 201; // Acta creada correctamente
            } else {
                return 400; // Error al insertar el acta
            }
        } catch (PDOException $e) {
            error_log("Error en ActualizaTarea: " . $e->getMessage());
            return 500;
        }
    }

    public function eliminarTareaConResponsables($ID_TAREAS)
{
    try {
        // Iniciar transacción
        $this->conn->beginTransaction();

        // Eliminar responsables asociados a la tarea
        $queryEliminarResponsables = "DELETE FROM encargados_tareas WHERE TAREAS_IDTAREAS = :ID_TAREAS";
        $stmtResponsables = $this->conn->prepare($queryEliminarResponsables);
        $stmtResponsables->bindParam(':ID_TAREAS', $ID_TAREAS);
        $stmtResponsables->execute();

        // Eliminar la tarea en sí
        $queryEliminarTarea = "DELETE FROM tareas WHERE ID_TAREAS = :ID_TAREAS";
        $stmtTarea = $this->conn->prepare($queryEliminarTarea);
        $stmtTarea->bindParam(':ID_TAREAS', $ID_TAREAS);
        $stmtTarea->execute();

        // Confirmar transacción
        $this->conn->commit();
        return true;
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $this->conn->rollBack();
        error_log("Error eliminando tarea y responsables: " . $e->getMessage());
        return false;
    }
}

}

<?php
require_once  'app/models/DataBaseManejoActas.php';
class EncargadosTareas
{

    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    public function AsignarResponsable($IDTAREAS, $IDMIEMBROS)
    {

        try {
            $ESTADO = "PENDIENTE";
            $query = "INSERT INTO encargados_tareas (ESTADO,TAREAS_IDTAREAS,MIEMBROS_IDMIEMBROS) VALUES (:ESTADO,:IDTAREAS,:IDMIEMBROS)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ESTADO', $ESTADO);
            $stmt->bindParam(':IDTAREAS', $IDTAREAS);
            $stmt->bindParam(':IDMIEMBROS', $IDMIEMBROS);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Registra el error en el log o devuelve una respuesta sin mostrar detalles sensibles
            error_log("Error asignando responsable: " . $e->getMessage());
            return false;
        }
    }



    public function DesasignarResponsable($IDMIEMBROS, $ID_TAREAS)
    {
        try {
            // Cambia 'ID_TAREAS' por el nombre correcto del campo en la base de datos si es diferente
            $queryEliminar = "DELETE FROM encargados_tareas WHERE MIEMBROS_IDMIEMBROS = :IDMIEMBROS AND TAREAS_IDTAREAS = :ID_TAREAS";
            $stmt = $this->conn->prepare($queryEliminar);

            // Asigna los parámetros
            $stmt->bindParam(':IDMIEMBROS', $IDMIEMBROS, PDO::PARAM_INT);
            $stmt->bindParam(':ID_TAREAS', $ID_TAREAS, PDO::PARAM_INT);

            // Ejecuta la consulta y verifica el resultado
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Registra el error en el log para revisión sin exponer detalles
            error_log("Error desasignando responsable: " . $e->getMessage());
            return false;
        }
    }

    //tareas pendientes sesion anterior. 
    public function MotrarTareasSesioAnterior($idSesion)
    {
        try {
            $query = "SELECT 
    tareas.ID_TAREAS,
    tareas.DESCRIPCION,
    tareas.FECHA_ENTREGA,
    tareas.FECHA_VERIFICACION,
    tareas.SESION_IDSESION,
    GROUP_CONCAT(miembros.NOMBRE ORDER BY miembros.NOMBRE SEPARATOR ', ') AS RESPONSABLES,
    encargados_tareas.ESTADO 
FROM 
    tareas
LEFT JOIN 
    encargados_tareas ON tareas.ID_TAREAS = encargados_tareas.TAREAS_IDTAREAS                       
LEFT JOIN 
    miembros ON encargados_tareas.MIEMBROS_IDMIEMBROS = miembros.IDMIEMBRO
WHERE 
    tareas.SESION_IDSESION = :IDSESION
GROUP BY 
    tareas.ID_TAREAS, tareas.DESCRIPCION, tareas.FECHA_ENTREGA, tareas.FECHA_VERIFICACION, tareas.SESION_IDSESION, encargados_tareas.ESTADO;
";

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


    function cambiarEstadoTarea($idTarea, $nuevoEstado)
    {
        try {
            // Iniciar la transacción
            $this->conn->beginTransaction();

            // Establecer la zona horaria a Bogotá y obtener la fecha actual
            date_default_timezone_set('America/Bogota');
            $fechaVerificacion = date('Y-m-d');

            // Actualizar el estado en la tabla encargados_tarea
            $queryEstado = "UPDATE encargados_tareas 
                        SET ESTADO = :nuevoEstado 
                        WHERE TAREAS_IDTAREAS = :idTarea ";
            $stmtEstado = $this->conn->prepare($queryEstado);
            $stmtEstado->bindParam(':nuevoEstado', $nuevoEstado);
            $stmtEstado->bindParam(':idTarea', $idTarea);
            $stmtEstado->execute();

            // Actualizar la fecha de verificación en la tabla tareas
            $queryFecha = "UPDATE tareas 
                       SET FECHA_VERIFICACION = :fechaVerificacion 
                       WHERE ID_TAREAS = :idTarea";
            $stmtFecha = $this->conn->prepare($queryFecha);
            $stmtFecha->bindParam(':fechaVerificacion', $fechaVerificacion);
            $stmtFecha->bindParam(':idTarea', $idTarea);
            $stmtFecha->execute();

            // Confirmar los cambios
            $this->conn->commit();

            return true;
        } catch (PDOException $e) {
            // Si ocurre un error, revertir los cambios
            $this->conn->rollBack();
            error_log("Error al cambiar el estado de la tarea: " . $e->getMessage());
            return false;
        }
    }
}

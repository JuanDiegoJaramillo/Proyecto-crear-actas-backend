<?php
require_once  'app/models/DataBaseManejoActas.php';
class AsistenciaInvitados
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }



    public function AsistenciaInvitadosSesion($idSesion)
    {
        try {
            $query = "SELECT INVITADO_IDINVITADO, CARGO, NOMBRE, ESTADO_ASISTENCIA FROM asistencia_invitado INNER JOIN  invitados ON invitados.IDINVITADOS = asistencia_invitado.INVITADO_IDINVITADO INNER JOIN sesion ON sesion.IDSESION = asistencia_invitado.SESION_IDSESION WHERE SESION_IDSESION = :idSesion";
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

    public function actualizarAsistenciaInvitados($estado, $idSesion, $idMiembro)
    {
        $queryActualizacion = "UPDATE asistencia_invitado SET ESTADO_ASISTENCIA = :ESTADO WHERE (SESION_IDSESION = :IDSESION) and (INVITADO_IDINVITADO = :IDMIEMBRO);";
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


    public function AgregarInvitadoAsistencia($idInvitado, $idSesion)
    {
        $estado = "";
        $query = "INSERT INTO asistencia_invitado (ESTADO_ASISTENCIA, INVITADO_IDINVITADO, SESION_IDSESION) VALUES (:estado, :idInvitado, :idSesion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':idInvitado', $idInvitado);
        $stmt->bindParam(':idSesion', $idSesion);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function validarAsistencia($invitadoId, $sesionId)
    {
        $queryVerificar = "SELECT COUNT(*) FROM asistencia_invitado WHERE INVITADO_IDINVITADO = :invitadoId AND SESION_IDSESION = :sesionId";
        $stmtVerificar = $this->conn->prepare($queryVerificar);
        $stmtVerificar->bindParam(':invitadoId', $invitadoId);
        $stmtVerificar->bindParam(':sesionId', $sesionId);
        $stmtVerificar->execute();

        // Si existe un registro, evita la inserción y devuelve un mensaje
        if ($stmtVerificar->fetchColumn() > 0) {
            return 1;
        } else {
            return 0;
        }
    }


    public function validarInvitacionPendiente($sesionId)
    {
        try {
            // Consulta para seleccionar invitados que no están en asistencia_invitado para la sesión especificada
            $query = "SELECT i.IDINVITADOS, i.NOMBRE, i.CARGO
            FROM invitados AS i
            LEFT JOIN asistencia_invitado AS ai
            ON i.IDINVITADOS = ai.INVITADO_IDINVITADO AND ai.SESION_IDSESION = :sesionId
            WHERE ai.INVITADO_IDINVITADO IS NULL";


            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sesionId', $sesionId);
            $stmt->execute();

            // Obtenemos los invitados que no tienen registro en asistencia_invitado
            $invitadosPendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Si no hay invitados pendientes, devolvemos un mensaje indicando que todos están invitados
            if (empty($invitadosPendientes)) {
                return 400;
            }

            // Si hay invitados pendientes, devolvemos la lista de ellos
            return $invitadosPendientes;
        } catch (Exception $e) {
            // Si ocurre un error, devolvemos un mensaje con el error
            return 500;
        }
    }


    public function eliminarAsistenciaInvitado($sesionId,$invitadoId)
    {
        $queryEliminar = "DELETE FROM asistencia_invitado WHERE (INVITADO_IDINVITADO = :invitadoId) and (SESION_IDSESION = :sesionId)";
        $queryEliminar = $this->conn->prepare($queryEliminar);
        $queryEliminar->bindParam(':invitadoId', $invitadoId);
        $queryEliminar->bindParam(':sesionId', $sesionId);
        if ($queryEliminar->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

<?php
require_once 'app/models/DataBaseManejoActas.php';
class Solicitud
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    // mostrar solicitudes

    public function solicitudesSesionDisponible()
    {
        try {
            $query = "SELECT IDSOLICITUD, DEPENDENCIA, ASUNTO, DESICION , FECHADESOLICITUD, SOLICITANTE_IDSOLICITANTE, NOMBRE, TIPODESOLICITANTE, EMAIL, CELULAR, ESTU_IMPLICADOS, NUM_ESTU_IMPLICADOS, DOCEN_IMPLICADOS, NUM_DOCEN_IMPLICADOS, CIUDAD_IMPLICADA, PAIS_IMPLICADO, EVENTO  FROM solicitud  inner join solicitantes on solicitantes.IDSOLICITANTE = solicitud.SOLICITANTE_IDSOLICITANTE inner join descripcion on descripcion.ID_DESCRIPCION = solicitud.DESCRIPCION_IDDESCRIPCION where solicitud.SESION_IDSESION = :idSesion";
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
            return null;
        }
    }

    public function SolicitudSelecionada($idSolicitud)
    {
        try {
            $query = "SELECT IDSOLICITUD,DEPENDENCIA,ASUNTO, ID_DESCRIPCION, EVENTO,PAIS_IMPLICADO,CIUDAD_IMPLICADA,IDSESION,LUGAR,FECHA,IDSOLICITANTE,NOMBRE,TIPODESOLICITANTE FROM solicitud inner join sesion on sesion.IDSESION = solicitud.SESION_IDSESION inner join solicitantes on solicitantes.IDSOLICITANTE = solicitud.SOLICITANTE_IDSOLICITANTE inner join descripcion on descripcion.ID_DESCRIPCION = solicitud.DESCRIPCION_IDDESCRIPCION where solicitud.IDSOLICITUD = :idSolicitud";
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
            return null;
        }
    }

    // 
    public function solicitudesSesion($idSesion)
    {
        try {
            $query = "SELECT IDSOLICITUD, DEPENDENCIA, ASUNTO, DESICION , FECHADESOLICITUD, SOLICITANTE_IDSOLICITANTE, NOMBRE, TIPODESOLICITANTE, EMAIL, CELULAR, ESTU_IMPLICADOS, NUM_ESTU_IMPLICADOS, DOCEN_IMPLICADOS, NUM_DOCEN_IMPLICADOS, CIUDAD_IMPLICADA, PAIS_IMPLICADO, EVENTO  FROM solicitud  inner join solicitantes on solicitantes.IDSOLICITANTE = solicitud.SOLICITANTE_IDSOLICITANTE inner join descripcion on descripcion.ID_DESCRIPCION = solicitud.DESCRIPCION_IDDESCRIPCION where solicitud.SESION_IDSESION = :idSesion";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idSesion', $idSesion);
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
            return null;
        }
    }

    public function solicitudesSesionSolicitante($idSesion, $idSolicitante)
{
    try {
        $query = "
        SELECT DISTINCT
            sol.IDSOLICITANTE, 
            sol.NOMBRE, 
            sol.IDENTIFICACION, 
            sol.TIPODESOLICITANTE, 
            ses.IDSESION, 
            ses.LUGAR,
            ses.FECHA,
            a.NUM_ACTAS,
            a.ESTADO,
            solreq.ASUNTO,
            solreq.DEPENDENCIA,
            solreq.DESICION,
            solreq.FECHADESOLICITUD,
            solreq.DESCRIPCION_IDDESCRIPCION,
            descripcion.ID_DESCRIPCION,
            descripcion.EVENTO,
            descripcion.ESTU_IMPLICADOS,
            descripcion.NUM_ESTU_IMPLICADOS,
            descripcion.DOCEN_IMPLICADOS,
            descripcion.NUM_DOCEN_IMPLICADOS,
            descripcion.PAIS_IMPLICADO,
            descripcion.CIUDAD_IMPLICADA
        FROM 
            solicitantes sol
        INNER JOIN 
            solicitud solreq ON solreq.SOLICITANTE_IDSOLICITANTE = sol.IDSOLICITANTE
        INNER JOIN 
            sesion ses ON ses.IDSESION = solreq.SESION_IDSESION
        INNER JOIN 
            actas a ON ses.IDSESION = a.SESION_IDSESION
        INNER JOIN 
            descripcion ON descripcion.ID_DESCRIPCION = solreq.DESCRIPCION_IDDESCRIPCION
        WHERE 
            ses.IDSESION = :idSesion 
            AND solreq.SOLICITANTE_IDSOLICITANTE = :idSolicitante
        ORDER BY 
            ses.FECHA DESC;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idSesion', $idSesion, PDO::PARAM_INT);
        $stmt->bindParam(':idSolicitante', $idSolicitante, PDO::PARAM_INT);
        $stmt->execute();

        $resultadoSQL = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultadoSQL) > 0) {
            // Crear la estructura final
            $solicitante = [
                "IDSOLICITANTE" => $resultadoSQL[0]["IDSOLICITANTE"] ?? null,
                "NOMBRE" => $resultadoSQL[0]["NOMBRE"] ?? null,
                "IDENTIFICACION" => $resultadoSQL[0]["IDENTIFICACION"] ?? null,
                "TIPODESOLICITANTE" => $resultadoSQL[0]["TIPODESOLICITANTE"] ?? null,
                "IDSESION" => $resultadoSQL[0]["IDSESION"] ?? null,
                "LUGAR" => $resultadoSQL[0]["LUGAR"] ?? null,
                "FECHA" => $resultadoSQL[0]["FECHA"] ?? null,
                "NUM_ACTAS" => $resultadoSQL[0]["NUM_ACTAS"] ?? null,
                "ESTADO" => $resultadoSQL[0]["ESTADO"] ?? null,
                "SOLICITUD" => array_map(function ($row) {
                    return [
                        "DESCRIPCION_IDDESCRIPCION" => $row["DESCRIPCION_IDDESCRIPCION"] ?? null,
                        "ASUNTO" => $row["ASUNTO"] ?? null,
                        "DEPENDENCIA" => $row["DEPENDENCIA"] ?? null,
                        "FECHADESOLICITUD" => $row["FECHADESOLICITUD"] ?? null,
                        "DECISION" => $row["DECISION"] ?? null, // Corrección de typo en la clave
                    ];
                }, $resultadoSQL),
                "DESCRIPCION" => array_map(function ($row) {
                    return [
                        "ID_DESCRIPCION" => $row["ID_DESCRIPCION"] ?? null,
                        "EVENTO" => $row["EVENTO"] ?? null,
                        "ESTU_IMPLICADOS" => $row["ESTU_IMPLICADOS"] ?? null,
                        "NUM_ESTU_IMPLICADOS" => $row["NUM_ESTU_IMPLICADOS"] ?? null,
                        "DOCEN_IMPLICADOS" => $row["DOCEN_IMPLICADOS"] ?? null,
                        "NUM_DOCEN_IMPLICADOS" => $row["NUM_DOCEN_IMPLICADOS"] ?? null,
                        "PAIS_IMPLICADO" => $row["PAIS_IMPLICADO"] ?? null,
                        "CIUDAD_IMPLICADA" => $row["CIUDAD_IMPLICADA"] ?? null,
                    ];
                }, $resultadoSQL)
            ];

            return $solicitante;
        } else {
            return []; // Retornar un array vacío si no hay registros
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        return null;
    }
}

    //insertar solicitud.

    function obtenerFechaActualColombia()
    {
        // Establecer la zona horaria a Bogotá
        date_default_timezone_set('America/Bogota');
        // Obtener la fecha actual
        return date('Y-m-d');
    }
    public function InsertarSolicitud($DEPENDENCIA, $ASUNTO, $IDSOLICITANTE, $IDSESION, $IDDESCRIPCION)
    {

        $FECHADESOLICITUD = $this->obtenerFechaActualColombia();

        $sql = "INSERT INTO solicitud (DEPENDENCIA,ASUNTO,DESICION,FECHADESOLICITUD,SOLICITANTE_IDSOLICITANTE,SESION_IDSESION,DESCRIPCION_IDDESCRIPCION) VALUE (:DEPENDENCIA, :ASUNTO,'', :FECHADESOLICITUD, :IDSOLICITANTE, :IDSESION, :IDDESCRIPCION)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':DEPENDENCIA', $DEPENDENCIA);
        $stmt->bindParam(':ASUNTO', $ASUNTO);
        $stmt->bindParam(':FECHADESOLICITUD', $FECHADESOLICITUD);
        $stmt->bindParam(':IDSOLICITANTE', $IDSOLICITANTE);
        $stmt->bindParam(':IDSESION', $IDSESION);
        $stmt->bindParam(':IDDESCRIPCION', $IDDESCRIPCION);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Actualizar solicitud.
    public function ActualizarSolicitud($DEPENDENCIA, $ASUNTO, $IDSOLICITANTE, $IDSESION, $IDDESCRIPCION, $IDSOLICITUD)
    {
        $sql = "UPDATE solicitud SET DEPENDENCIA =:DEPENDENCIA ,ASUNTO =:ASUNTO, SOLICITANTE_IDSOLICITANTE=:IDSOLICITANTE ,SESION_IDSESION=:IDSESION ,DESCRIPCION_IDDESCRIPCION=:IDDESCRIPCION  WHERE IDSOLICITUD=:IDSOLICITUD";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':DEPENDENCIA', $DEPENDENCIA);
        $stmt->bindParam(':ASUNTO', $ASUNTO);
        $stmt->bindParam(':IDSOLICITANTE', $IDSOLICITANTE);
        $stmt->bindParam(':IDSESION', $IDSESION);
        $stmt->bindParam(':IDDESCRIPCION', $IDDESCRIPCION);
        $stmt->bindParam(':IDSOLICITUD', $IDSOLICITUD);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function ResponderSolicitud($DESICION, $IDSOLICITUD)
    {
        $sql = "UPDATE solicitud SET DESICION =:DESICION  WHERE IDSOLICITUD=:IDSOLICITUD";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':DESICION', $DESICION);
        $stmt->bindParam(':IDSOLICITUD', $IDSOLICITUD);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Eliminar solicitud.
    public function EliminarSolicitud($IDSOLICITUD)
    {
        $sql = "DELETE FROM solicitud WHERE IDSOLICITUD=:IDSOLICITUD";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':IDSOLICITUD', $IDSOLICITUD);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

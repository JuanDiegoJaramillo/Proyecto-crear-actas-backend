<?php
require_once  'app/models/DataBaseManejoActas.php';

class Sesion
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    public function obtenerUltimaFechaSesion()
    {
        try {
            $query = "SELECT fecha FROM actas INNER JOIN sesion ON actas.SESION_IDSESION = sesion.IDSESION ORDER BY fecha DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                $ultimaFecha = $result['fecha'];
                $diasMinimos = 9; // Puedes ajustar este valor según tu necesidad

                return [
                    'ultimaFecha' => $ultimaFecha,
                    'diasMinimos' => $diasMinimos
                ];
            } else {
                // Si no hay sesiones registradas, devolver null para indicar fecha libre
                return [
                    'ultimaFecha' => null,
                    'diasMinimos' => 0
                ];
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function mostrarSesiones()
    {
        try {
            $query = "SELECT sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, sesion.PRESIDENTE, sesion.SECRETARIO FROM sesion inner join actas on actas.SESION_IDSESION = sesion.IDSESION WHERE actas.ESTADO !='FIRMADA'  order by FECHA desc;";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return []; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    
    public function mostrarSesionesSolicitante($idSolicitante)
    {
        try {
            $query = "SELECT DISTINCT 
    sol.IDSOLICITANTE, 
    sol.NOMBRE, 
    sol.IDENTIFICACION, 
    sol.TIPODESOLICITANTE, 
    ses.IDSESION, 
    ses.LUGAR,
    ses.FECHA,
    ses.HORAINICIO,
    ses.HORAFINAL,
    a.NUM_ACTAS,
    a.ESTADO
FROM 
    solicitantes sol
LEFT JOIN 
    solicitud solreq ON solreq.SOLICITANTE_IDSOLICITANTE = sol.IDSOLICITANTE
LEFT JOIN 
    sesion ses ON ses.IDSESION = solreq.SESION_IDSESION
LEFT JOIN 
    actas a ON ses.IDSESION = a.SESION_IDSESION
WHERE 
    sol.IDSOLICITANTE = :idSolicitante
ORDER BY 
    ses.FECHA DESC ";

            $stmt = $this->conn->prepare($query);
            $stmt -> bindParam(':idSolicitante',$idSolicitante);
            $stmt->execute();
            $resultadoSQL = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($resultadoSQL) > 0) {
                $solicitante = [
                    "IDSOLICITANTE" => $resultadoSQL[0]["IDSOLICITANTE"],
                    "NOMBRE" => $resultadoSQL[0]["NOMBRE"],
                    "IDENTIFICACION" => $resultadoSQL[0]["IDENTIFICACION"],
                    "TIPODESOLICITANTE" => $resultadoSQL[0]["TIPODESOLICITANTE"],
                    "SESIONES" => array_filter(array_map(function($row) {
                        if ($row["IDSESION"] !== null) { // Filtrar sesiones no nulas
                            return [
                                "IDSESION" => $row["IDSESION"],
                                "LUGAR" => $row["LUGAR"],
                                "FECHA" => $row["FECHA"],
                                "HORAINICIO" => $row["HORAINICIO"],
                                "HORAFINAL" => $row["HORAFINAL"],
                                "NUM_ACTAS" => $row["NUM_ACTAS"],
                                "ESTADO" => $row["ESTADO"]
                            ];
                        }
                        return null;
                    }, $resultadoSQL))
                ];
                return $solicitante;
            } else {
                // Aquí puedes incluir un mensaje indicando que el solicitante no tiene sesiones
                return ["message" => "El solicitante no tiene solicitudes registradas"];
            }
            
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function mostrarSolicitantesSolicitantes($idSolicitante)
    {
        try {
            $query = "SELECT DISTINCT 
    sol.IDSOLICITANTE, 
    sol.NOMBRE, 
    sol.IDENTIFICACION, 
    sol.TIPODESOLICITANTE, 
    ses.IDSESION, 
    ses.LUGAR,
    ses.FECHA,
    ses.HORAINICIO,
    ses.HORAFINAL,
    a.NUM_ACTAS,
    a.ESTADO
FROM 
    solicitantes sol
INNER JOIN 
    solicitud solreq ON solreq.SOLICITANTE_IDSOLICITANTE = sol.IDSOLICITANTE
INNER JOIN 
    sesion ses ON ses.IDSESION = solreq.SESION_IDSESION
    inner join actas a on ses.IDSESION = a.SESION_IDSESION 
WHERE 
    sol.IDSOLICITANTE = :idSolicitante
ORDER BY 
     ses.FECHA DESC";
            $stmt = $this->conn->prepare($query);
            $stmt -> bindParam(':idSolicitante',$idSolicitante);
            $stmt->execute();
            $resultadoSQL = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($resultadoSQL) > 0) {
             
                    $solicitante = [
                        "IDSOLICITANTE" => $resultadoSQL[0]["IDSOLICITANTE"],
                        "NOMBRE" => $resultadoSQL[0]["NOMBRE"],
                        "IDENTIFICACION" => $resultadoSQL[0]["IDENTIFICACION"],
                        "TIPODESOLICITANTE" => $resultadoSQL[0]["TIPODESOLICITANTE"],
                        
                        "SESIONES" => array_map(function($row) {
                            return [
                                "IDSESION" => $row["IDSESION"],
                                "LUGAR" => $row["LUGAR"],
                                "FECHA" => $row["FECHA"],
                                "HORAINICIO" => $row["HORAINICIO"],
                                "HORAFINAL" => $row["HORAFINAL"],
                                "NUM_ACTAS" => $row["NUM_ACTAS"],
                                "ESTADO" => $row["ESTADO"]
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

    public function mostrarSesioneIDSesion($IDSesion)
    {
        try {
            $query = "SELECT sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, sesion.PRESIDENTE, sesion.SECRETARIO FROM sesion inner join actas on actas.SESION_IDSESION = sesion.IDSESION WHERE actas.ESTADO !='FIRMADA' AND sesion.IDSESION = :IDSesion order by FECHA desc";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDSesion', $IDSesion);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return []; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    public function SesionAnterior($IDSesion)
    {
        try {
            $query = "SELECT sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, 
       sesion.PRESIDENTE, sesion.SECRETARIO,actas.NUM_ACTAS,actas.ESTADO FROM sesion INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION
       WHERE  sesion.FECHA < (SELECT FECHA FROM sesion WHERE IDSESION = :IDSesion)
       ORDER BY sesion.FECHA DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':IDSesion', $IDSesion);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return []; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    function UltimaSesion()
    {
        try {
            $query = "SELECT s.IDSESION FROM sesion s WHERE s.FECHA = (SELECT MAX(FECHA) FROM sesion) ORDER BY s.IDSESION DESC LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            } else {
                return false;
            }
        } catch (PDOException $e) {

            return false;
        }
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

    public function registrarSesion($lugar, $fecha, $horaInicio, $horaFinal, $presidente, $secretario)
    {
        try {
            $query = "INSERT INTO sesion (LUGAR, FECHA, HORAINICIO, HORAFINAL, PRESIDENTE, SECRETARIO) VALUES (:lugar, :fecha, :horaInicio, :horaFinal, :presidente, :secretario)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':horaInicio', $horaInicio);
            $stmt->bindParam(':horaFinal', $horaFinal);
            $stmt->bindParam(':presidente', $presidente);
            $stmt->bindParam(':secretario', $secretario);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId(); // Devuelve el ID de la sesión creada
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crearActa($idSesion)
    {
        try {
            $estado = 'PENDIENTE';
            // Inserta el acta
            $queryActa = "INSERT INTO actas (ESTADO, SESION_IDSESION) VALUES (:estado, :idSesion)";
            $stmtActa = $this->conn->prepare($queryActa);
            $stmtActa->bindParam(':estado', $estado);
            $stmtActa->bindParam(':idSesion', $idSesion);

            if ($stmtActa->execute()) {
                return 201; // Acta creada correctamente
            } else {
                return 400; // Error al insertar el acta
            }
        } catch (PDOException $e) {
            // Devuelve un código de error y no el objeto de excepción
            return 500;
        }
    }
    function obtenerFechaActualColombia()
    {
        // Establecer la zona horaria a Bogotá
        date_default_timezone_set('America/Bogota');
        // Obtener la fecha actual
        return date('Y-m-d');
    }

    public function MostrarSesionSelect()
    {
        try {
            $fechaActual = $this->obtenerFechaActualColombia();

            $query = "SELECT IDSESION, LUGAR, FECHA, HORAINICIO, HORAFINAL FROM sesion INNER JOIN actas ON 
    sesion.IDSESION = actas.SESION_IDSESION WHERE actas.ESTADO != 'FIRMADA' 
    AND sesion.FECHA > :fechaActual";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':fechaActual', $fechaActual, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays

            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return []; // Retornar un array vacío si no hay registros
            }
        } catch (PDOException $e) {
            // Devuelve un código de error y no el objeto de excepción
            return 500;
        }
    }
}

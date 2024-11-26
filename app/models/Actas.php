<?php
require_once  'app/models/DataBaseManejoActas.php';
class Actas
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }


    public function SelecActasFirmadas()
    {
        try {
            $query = "SELECT * FROM actas inner join sesion on sesion.IDSESION = actas.SESION_IDSESION where ESTADO = 'FIRMADA' order by FECHA desc";
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

    public function ActaAnterior($IDSesion)
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

    public function AprobarActa($IDSESION, $ESTADO)
    {
        try {

            // Inserta el acta
            $queryActa = "UPDATE actas SET ESTADO = :ESTADO WHERE (SESION_IDSESION = :IDSESION)";
            $stmtActa = $this->conn->prepare($queryActa);
            $stmtActa->bindParam(':IDSESION', $IDSESION);
            $stmtActa->bindParam(':ESTADO', $ESTADO);
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

    //detalles actas.

    //temas.

    //solicitudes. 

    //tareas

    //proposiciones


    //asistencia:
    //invitados

    //miembros

    // filtros de actas. 
    function buscarPorIdOTema($filtro)
    {
        try {
            // Base de la consulta


            // Verificar si el filtro es numérico
            if (ctype_digit((string)$filtro)) {
                // Si es un ID, agregar condición para ID
                $query = "SELECT NUM_ACTAS, ESTADO, sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, 
       sesion.PRESIDENTE, sesion.SECRETARIO ,NUM_ACTAS,ESTADO
FROM sesion 
INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION
WHERE actas.NUM_ACTAS = :filtro";
            } else {
                // Si es un tema, agregar condición para tema
                $tema = str_replace('t2', ' ', $filtro);
                $tema_mayusculas = strtoupper($tema);
                $query = "SELECT NUM_ACTAS, ESTADO, sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, 
       sesion.PRESIDENTE, sesion.SECRETARIO ,NUM_ACTAS,ESTADO
FROM sesion 
INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION inner join orden_sesion on orden_sesion.SESION_IDSESION = sesion.IDSESION
WHERE orden_sesion.TEMA = :filtro";
            }

            // Preparar la consulta
            $stmt = $this->conn->prepare($query);

            // Asociar el parámetro
            if (ctype_digit((string)$filtro)) {
                $stmt->bindParam(':filtro', $filtro, PDO::PARAM_INT); // ID como entero
            } else {

                $stmt->bindParam(':filtro', $tema_mayusculas, PDO::PARAM_STR);
            }

            // Ejecutar la consulta
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($datos) {
                return $datos;
            } else {
                return [];
            }
            // Obtener resultados

        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en buscarPorIdOTema: " . $e->getMessage());
            return 500;
        }
    }


    public function BuscarActaPorFecha($Year, $Month)
    {
        try {
            // Base de la consulta

            $query = "SELECT NUM_ACTAS, ESTADO, sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, 
       sesion.PRESIDENTE, sesion.SECRETARIO 
FROM sesion 
INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION
WHERE  YEAR(sesion.FECHA) = :ActaYear AND MONTH(sesion.FECHA) = :ActaMonth";
            // Preparar la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ActaYear', $Year, PDO::PARAM_INT); // ID como entero
            $stmt->bindParam(':ActaMonth', $Month, PDO::PARAM_INT);
            // Ejecutar la consulta
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($datos) {
                return $datos;
            } else {
                return [];
            }
            // Obtener resultados

        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en buscarPorIdOTema: " . $e->getMessage());
            return 500;
        }
    }

    public function BuscarActaPorYear($Year)
    {

        try {
            // Base de la consulta

            $query = "SELECT NUM_ACTAS, ESTADO, sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL, 
       sesion.PRESIDENTE, sesion.SECRETARIO 
FROM sesion 
INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION
WHERE  YEAR(sesion.FECHA) = :ActaYear";

            // Preparar la consulta
            $stmt = $this->conn->prepare($query);

            // Asociar el parámetro      
            $stmt->bindParam(':ActaYear', $Year, PDO::PARAM_STR);

            // Ejecutar la consulta
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($datos) {
                return $datos;
            } else {
                return [];
            }
        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en buscarPorIdOTema: " . $e->getMessage());
            return 500;
        }
    }

    function buscarPorTemaFecha($Tema, $Year, $Month)
    {
        try {
            // Base de la consulta

            $query = "SELECT NUM_ACTAS, ESTADO, sesion.IDSESION, sesion.LUGAR, sesion.FECHA, sesion.HORAINICIO, sesion.HORAFINAL,
       sesion.PRESIDENTE, sesion.SECRETARIO ,NUM_ACTAS,ESTADO
FROM sesion 
INNER JOIN actas ON actas.SESION_IDSESION = sesion.IDSESION inner join orden_sesion on orden_sesion.SESION_IDSESION = sesion.IDSESION
WHERE orden_sesion.TEMA = :Tema AND YEAR(sesion.FECHA) = :ActaYear AND MONTH(sesion.FECHA) = :ActaMonth";

$tema = str_replace('t2', ' ', $Tema);
$tema_mayusculas = strtoupper($tema);
            // Preparar la consulta
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':Tema', $tema_mayusculas, PDO::PARAM_STR);
            $stmt->bindParam(':ActaYear', $Year, PDO::PARAM_INT); // ID como entero
            $stmt->bindParam(':ActaMonth', $Month, PDO::PARAM_INT); // ID como entero
            // Ejecutar la consulta
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($datos) {
                return $datos;
            } else {
                return [];
            }
            // Obtener resultados

        } catch (Exception $e) {
            // Manejar errores
            error_log("Error en buscarPorIdOTema: " . $e->getMessage());
            return 500;
        }
    }
}

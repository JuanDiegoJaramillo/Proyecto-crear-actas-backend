<?php
require_once  'app/models/DataBaseManejoActas.php';
class OrdenSesion
{
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

//retorna los temas asociados a una sesion
    public function temasSesion($id)
    {
        try {
            $query = "SELECT orden_sesion.SESION_IDSESION,orden_sesion.IDtema, orden_sesion.TEMA,orden_sesion.DESCRIPCION FROM orden_sesion inner join sesion on sesion.IDSESION = orden_sesion.SESION_IDSESION where IDSESION = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
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

// retorna el tema buscado y la descripcion
    public function solicitud($id)
    {
        try {
            $query = "SELECT orden_sesion.TEMA, orden_sesion.DESCRIPCION, orden_sesion.IDtema FROM orden_sesion where orden_sesion.IDtema = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener todos los registros como un array de arrays
            
            // Verificar si hay datos en la tabla
            if (count($result) > 0) {
                return $result;
            } else {
                return 0; //  no hay registros
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
            return 500;
        }
    }


// incertar temas para una sesion
//validar que el tema no se repita.
public function TemaExists($tema,$idSesion)
{
    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orden_sesion WHERE orden_sesion.TEMA= :tema AND orden_sesion.SESION_IDSESION = :idsesion");
    $stmt->bindParam(':tema', $tema);
    $stmt->bindParam(':idsesion', $idSesion);
    $stmt->execute();

    // Retorna verdadero si el correo existe, falso en caso contrario
    return $stmt->fetchColumn() > 0;
}

//regitrar el tema.
public function registrarTema($tema, $descripcion,$idSesion)
{
    $query = "INSERT INTO orden_sesion (TEMA, DESCRIPCION, SESION_IDSESION) VALUES (:tema, :descripcion, :idSesion)";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':tema', $tema);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':idSesion', $idSesion);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}


//Actualisar tema
public function ValidarTema($id, $nuevoTema, $idSesion) {
    // Verificamos si existe un tema igual en otro registro con diferente ID
    $queryVerificacion = "SELECT COUNT(*) as conteo FROM orden_sesion WHERE orden_sesion.TEMA = :nuevoTema AND orden_sesion.IDtema != :id AND orden_sesion.SESION_IDSESION = :idsesion";
    $stmtVerificacion = $this->conn->prepare($queryVerificacion);
    $stmtVerificacion->bindParam(':nuevoTema', $nuevoTema);
    $stmtVerificacion->bindParam(':id', $id);
    $stmtVerificacion->bindParam(':idsesion', $idSesion);
    $stmtVerificacion->execute();
    $resultado = $stmtVerificacion->fetch(PDO::FETCH_ASSOC);

    if ($resultado['conteo'] > 0) {
        // Tema ya existe en otro registro, devolver error
        return 1;
    }else{
        return 0;
    }
}

public function ActualizarTema($id, $nuevoTema, $nuevaDescripcion){
$queryActualizacion = "UPDATE orden_sesion SET orden_sesion.TEMA = :nuevoTema, orden_sesion.DESCRIPCION = :nuevaDescripcion WHERE orden_sesion.IDtema = :id";
$stmtActualizacion = $this->conn->prepare($queryActualizacion);
$stmtActualizacion->bindParam(':nuevoTema', $nuevoTema);
$stmtActualizacion->bindParam(':nuevaDescripcion', $nuevaDescripcion);
$stmtActualizacion->bindParam(':id', $id);
if ($stmtActualizacion->execute()) {
   return true;
} else {
    return false;
}
} 

//borrar tema 
public function eliminarTema($id){
    $queryEliminar = "DELETE FROM orden_sesion WHERE orden_sesion.IDtema = :id";
    $queryEliminar = $this->conn->prepare($queryEliminar);
    $queryEliminar->bindParam(':id', $id);
    if ($queryEliminar->execute()) {
       return true;
    } else {
        return false;
    }    
}
}
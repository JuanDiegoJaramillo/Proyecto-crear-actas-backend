<?php
require_once 'app/models/DataBaseManejoActas.php';
class Descripciones
{
    private $conn;
    public function __construct() 
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect(); 
    }


    public function mostrarDescripciones()
    {
        try {
            $query = "SELECT ID_DESCRIPCION, EVENTO,PAIS_IMPLICADO,CIUDAD_IMPLICADA FROM descripcion";
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
           // echo 'Error: ' . $e->getMessage();
         //   print_r($e);
            return 500;
        }
    }

}
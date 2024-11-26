<?php
require_once 'DataBaseManejoActas.php';

class TokenModel {
   
    private $conn;
    public function __construct()
    {
        $database = new DataBaseManejoActas();
        $this->conn = $database->connect();
    }

    // Método para eliminar un token
    public function deleteToken($token) {
        $query = "DELETE FROM tokens WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    }
}

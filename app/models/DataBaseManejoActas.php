<?php 
// app/core/Database.php

use Dotenv\Dotenv;

class DataBaseManejoActas {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $conn;

    public function __construct() {
        // Cargar las variables de entorno
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Ajusta la ruta
        $dotenv->load();


        $this->host = $_ENV['DB_HOST_MANEJO_ACTAS'];
        $this->db_name = $_ENV['DB_NAME_MANEJO_ACTAS'];
        $this->username = $_ENV['DB_USER_MANEJO_ACTAS'];
        $this->password = isset($_ENV['DB_PASST_MANEJO_ACTAS']) ? $_ENV['DB_PASST_MANEJO_ACTAS'] : '';
        $this->port = $_ENV['DB_PORT_MANEJO_ACTAS'];

    }

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host .';port=' . $this->port . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }

        return $this->conn;
    }
}

?>
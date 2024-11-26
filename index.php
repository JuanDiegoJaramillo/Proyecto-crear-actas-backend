<?php
require_once 'helpers/CorsMiddleware.php';
require_once 'vendor/autoload.php'; // Carga Composer
require_once 'routes/api.php'; // Incluye las rutas definidas

use Dotenv\Dotenv;
CorsMiddleware::handle();
// Cargar archivo .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Habilitar CORS


// Activar el reporte de errores (opcional para desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir ruta base
define('BASE_PATH', __DIR__);

// Obtener el método y URI
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar prefijo de URI si es necesario
$requestUri = str_replace('/crear-actas-backend', '', $requestUri);
$requestUri = trim($requestUri, '/');

// Llamar al enrutador
$router = require 'routes/api.php';

if (empty($requestUri)) {
    echo "Bienvenido al sistema de backend de Actas";
} else {
    try {
        $router->dispatch($requestMethod, $requestUri);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Error interno del servidor', 'error' => $e->getMessage()]);
    }
}
?>





<?php
// En /middlewares/CorsMiddleware.php
class CorsMiddleware
{
    public static function handle()
    {
        // Cambia '*' por la URL específica de tu frontend (por ejemplo, http://localhost:5173 en desarrollo)
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Incluye Authorization
        header("Access-Control-Allow-Credentials: true"); // Si necesitas enviar cookies o credenciales
        
        // Responder a preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}


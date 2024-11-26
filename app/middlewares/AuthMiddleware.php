<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// app/middlewares/AuthMiddleware.php
require_once 'app/models/TokenModel.php'; // Modelo para manejar tokens

class AuthMiddleware {
    public function authenticate() {
        $this->setCorsHeaders();

        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            return $this->unauthorizedResponse('No autorizado');
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            // Obtener la clave secreta
            $secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';

            // Decodificar el token
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Validar la expiración
            if ($decoded->exp < time()) {
                $this->invalidateToken($token);
                return $this->unauthorizedResponse('Sesión expirada');
            }

            // Retornar datos del usuario autenticado
            return [
                'id_token' => $decoded->id_token,
                'rol' => $decoded->rol,
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->invalidateToken($token);
            return $this->unauthorizedResponse('Sesión expirada');
        } catch (\Exception $e) {
            $this->invalidateToken($token);
            return $this->unauthorizedResponse('Token inválido o no autorizado');
        }
    }

    public function validateToken($token) {
        try {
            // Obtener la clave secreta
            $secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret_key';

            // Decodificar el token
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Validar la expiración
            if ($decoded->exp < time()) {
                $this->invalidateToken($token);
                return $this->unauthorizedResponse('Sesión expirada');
            }

            // Retornar datos del usuario autenticado
            return [
                'id_token' => $decoded->id_token,
                'rol' => $decoded->rol,
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->invalidateToken($token);
            return $this->unauthorizedResponse('Sesión expirada');
        } catch (\Exception $e) {
            $this->invalidateToken($token);
            return $this->unauthorizedResponse('Token inválido o no autorizado');
        }
    }

    private function unauthorizedResponse($message) {
        http_response_code(401);
        echo json_encode([
            'message' => $message,
            'action' => 'logout', // Indica al frontend cerrar sesión
        ]);
        exit;
    }

    private function invalidateToken($token) {
        // Elimina el token de la base de datos si es necesario
        $tokenModel = new TokenModel();
        $tokenModel->deleteToken($token);
    }

    private function setCorsHeaders() {
        // Configuración de CORS
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        header("Access-Control-Allow-Credentials: true");
    }
}


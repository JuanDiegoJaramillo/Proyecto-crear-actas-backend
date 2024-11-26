<?php
require_once 'app/middlewares/AuthMiddleware.php';
require_once 'app/middlewares/RoleMiddleware.php';

class Router
{
    private $routes = [];

    public function add($method, $uri, $action, $isPublic = false)
    {
        // Convierte parámetros entre llaves a expresiones regulares
       // $uri = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $uri);
       // $uri = "#^" . trim($uri, '/') . "$#"; // Crea un patrón completo de regex
       // $this->routes[] = ['method' => strtoupper($method), 'uri' => $uri, 'action' => $action, 'public' => $isPublic];
        $uri = trim($uri, '/');
        $uri = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_%+-]+)', $uri);
        $this->routes[] = [
            'method' => $method,
            'uri' => "#^{$uri}$#",
            'action' => $action,
            'public' => $isPublic
        ];
    
    }


    public function dispatch($method, $uri) {
        $uri = trim($uri, '/'); // Normaliza la URI
        foreach ($this->routes as $route) {
            // Cambia esta línea para utilizar expresiones regulares correctamente
            if ($route['method'] === $method && preg_match($route['uri'], $uri, $matches)) {
                // Filtramos los elementos numéricos (del preg_match)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);                          
                if (!$route['public']) {
                    $this->authenticateAndAuthorize($method, $uri); // Aplicar middlewares solo para rutas protegidas
                }

                return $this->executeAction($route['action'], $params);
            }
        }
    
        // Si no se encuentra la ruta, se devuelve 404
        http_response_code(404);
        echo json_encode(['message' => 'Ruta no encontrada']);
    }
    


    
    
    

   // public function dispatch($method, $uri)
   // {
   //     $uri = trim($uri, '/'); // Normalizamos la URI
//
   //     foreach ($this->routes as $route) {
   //         if ($route['method'] === strtoupper($method) && preg_match($route['uri'], $uri, $matches)) {
   //             $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
//
   //             if (!$route['public']) {
   //                 $this->authenticateAndAuthorize($method, $uri); // Aplicar middlewares solo para rutas protegidas
   //             }
//
   //             return $this->executeAction($route['action'], $params);
   //         }
   //     }
//
   //     http_response_code(404);
   //     echo json_encode(['message' => 'Ruta no encontrada']);
   // }

    private function authenticateAndAuthorize($method, $uri)
    {
        $authMiddleware = new AuthMiddleware();
        // Obtener el token del encabezado Authorization
        // Autenticar al usuario
        $user = $authMiddleware->authenticate();

        if (isset($user['error'])) {
            http_response_code(401);
            echo json_encode(['message' => $user['error']]);
            exit;
        }

        // Autorización basada en rol
        $roleMiddleware = new RoleMiddleware();
        try {
            $roleMiddleware->handle($user['rol'], $method, $uri);
        } catch (Exception $e) {
            http_response_code(403);
            echo json_encode(['message' => 'Acceso denegado: ' . $e->getMessage()]);
            exit;
        }
    }

    private function executeAction($action, $params = []) {
        list($controller, $method) = explode('@', $action);
    
        // Verifica si la clase del controlador y el método existen
        if (!class_exists($controller) || !method_exists($controller, $method)) {
            http_response_code(500);
            echo json_encode(['message' => 'Error interno del servidor']);
            return;
        }
    
        $controllerInstance = new $controller();
        
        // Llama al método pasando los parámetros
        return $controllerInstance->{$method}(...array_values($params));
    }
    
}

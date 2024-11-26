<?php 
$requestMethod = $_SERVER['REQUEST_METHOD'];
echo $requestMethod;
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo $requestUri;
?>
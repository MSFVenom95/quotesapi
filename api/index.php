<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
$segments   = explode('/', $requestUri);

$apiIndex = array_search('api', $segments);
$resource = ($apiIndex !== false && isset($segments[$apiIndex + 1]))
            ? strtolower($segments[$apiIndex + 1])
            : '';

switch ($resource) {
    case 'quotes':
        require_once __DIR__ . '/quotes/index.php';
        break;
    case 'authors':
        require_once __DIR__ . '/authors/index.php';
        break;
    case 'categories':
        require_once __DIR__ . '/categories/index.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Endpoint Not Found']);
}
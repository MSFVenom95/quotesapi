<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../classes/Category.php';

$database = new Database();
$db       = $database->getConnection();
$category = new Category($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $category->id = (int)$_GET['id'];
            $stmt = $category->getById();
        } else {
            $stmt = $category->getAll();
        }

        $results = $stmt->fetchAll();

        if (empty($results)) {
            http_response_code(404);
            echo json_encode(['message' => 'category_id Not Found']);
        } else {
            http_response_code(200);
            echo json_encode($results);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data)) $data = $_POST;

        if (empty($data['category'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $category->category = htmlspecialchars(strip_tags($data['category']));

        if ($category->create()) {
            http_response_code(201);
            echo json_encode([
                'id'       => $category->id,
                'category' => $category->category,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Category could not be created']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id']) || empty($data['category'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $category->id       = (int)$data['id'];
        $category->category = htmlspecialchars(strip_tags($data['category']));

        $result = $category->update();

        if ($result === 'not_found') {
            http_response_code(404);
            echo json_encode(['message' => 'category_id Not Found']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode([
                'id'       => $category->id,
                'category' => $category->category,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Category could not be updated']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $category->id = (int)$data['id'];
        $result       = $category->delete();

        if ($result === 'not_found') {
            http_response_code(404);
            echo json_encode(['message' => 'category_id Not Found']);
        } elseif ($result === 'fk_error') {
            http_response_code(409);
            echo json_encode(['message' => 'Cannot delete: category is referenced by existing quotes']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode(['id' => $category->id]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Category could not be deleted']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
}
```

---

## 10. `.htaccess`
```
Options -Indexes

RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/index.php [QSA,L]
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
require_once __DIR__ . '/../../classes/Author.php';

$database = new Database();
$db       = $database->getConnection();
$author   = new Author($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['id'])) {
            $author->id = (int)$_GET['id'];
            $stmt = $author->getById();
        } else {
            $stmt = $author->getAll();
        }

        $results = $stmt->fetchAll();

if (empty($results)) {
    http_response_code(200);
    echo json_encode(['message' => 'author_id Not Found']);
} else {
    http_response_code(200);
    if (isset($_GET['id'])) {
        echo json_encode($results[0]);
    } else {
        echo json_encode($results);
    }
}
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data)) $data = $_POST;

        if (empty($data['author'])) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $author->author = htmlspecialchars(strip_tags($data['author']));

        if ($author->create()) {
            http_response_code(201);
            echo json_encode([
                'id'     => $author->id,
                'author' => $author->author,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Author could not be created']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id']) || empty($data['author'])) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $author->id     = (int)$data['id'];
        $author->author = htmlspecialchars(strip_tags($data['author']));

        $result = $author->update();

        if ($result === 'not_found') {
            http_response_code(200);
            echo json_encode(['message' => 'author_id Not Found']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode([
                'id'     => $author->id,
                'author' => $author->author,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Author could not be updated']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id'])) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $author->id = (int)$data['id'];
        $result     = $author->delete();

        if ($result === 'not_found') {
            http_response_code(200);
            echo json_encode(['message' => 'author_id Not Found']);
        } elseif ($result === 'fk_error') {
            http_response_code(409);
            echo json_encode(['message' => 'Cannot delete: author is referenced by existing quotes']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode(['id' => $author->id]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Author could not be deleted']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
}
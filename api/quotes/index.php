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
require_once __DIR__ . '/../../classes/Quote.php';

$database = new Database();
$db       = $database->getConnection();
$quote    = new Quote($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $random      = isset($_GET['random']) && $_GET['random'] === 'true';
        $id          = isset($_GET['id'])          ? (int)$_GET['id']          : null;
        $author_id   = isset($_GET['author_id'])   ? (int)$_GET['author_id']   : null;
        $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        if ($id !== null) {
            $quote->id = $id;
            $stmt = $quote->getById();
        } elseif ($author_id !== null && $category_id !== null) {
            $quote->author_id   = $author_id;
            $quote->category_id = $category_id;
            $stmt = $quote->getByAuthorAndCategory($random);
        } elseif ($author_id !== null) {
            $quote->author_id = $author_id;
            $stmt = $quote->getByAuthor($random);
        } elseif ($category_id !== null) {
            $quote->category_id = $category_id;
            $stmt = $quote->getByCategory($random);
        } else {
            $stmt = $quote->getAll($random);
        }

       $results = $stmt->fetchAll();

if (empty($results)) {
    http_response_code(200);
    echo json_encode(['message' => 'No Quotes Found']);
} else {
    http_response_code(200);
    // Return single object when querying by id
    if ($id !== null) {
        echo json_encode($results[0]);
    } else {
        echo json_encode($results);
    }
}
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data)) $data = $_POST;

        if (
            empty($data['quote']) ||
            !isset($data['author_id']) ||
            !isset($data['category_id'])
        ) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $authorId   = (int)$data['author_id'];
        $categoryId = (int)$data['category_id'];

        if (!$quote->authorExists($authorId)) {
            http_response_code(200);
            echo json_encode(['message' => 'author_id Not Found']);
            break;
        }

        if (!$quote->categoryExists($categoryId)) {
            http_response_code(200);
            echo json_encode(['message' => 'category_id Not Found']);
            break;
        }

        $quote->quote       = htmlspecialchars(strip_tags($data['quote']));
        $quote->author_id   = $authorId;
        $quote->category_id = $categoryId;

        if ($quote->create()) {
            http_response_code(201);
            echo json_encode([
                'id'          => $quote->id,
                'quote'       => $quote->quote,
                'author_id'   => $quote->author_id,
                'category_id' => $quote->category_id,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Quote could not be created']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (
            empty($data['id']) ||
            empty($data['quote']) ||
            !isset($data['author_id']) ||
            !isset($data['category_id'])
        ) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $authorId   = (int)$data['author_id'];
        $categoryId = (int)$data['category_id'];

        if (!$quote->authorExists($authorId)) {
            http_response_code(200);
            echo json_encode(['message' => 'author_id Not Found']);
            break;
        }

        if (!$quote->categoryExists($categoryId)) {
            http_response_code(200);
            echo json_encode(['message' => 'category_id Not Found']);
            break;
        }

        $quote->id          = (int)$data['id'];
        $quote->quote       = htmlspecialchars(strip_tags($data['quote']));
        $quote->author_id   = $authorId;
        $quote->category_id = $categoryId;

        $result = $quote->update();

        if ($result === 'not_found') {
            http_response_code(200);
            echo json_encode(['message' => 'No Quotes Found']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode([
                'id'          => $quote->id,
                'quote'       => $quote->quote,
                'author_id'   => $quote->author_id,
                'category_id' => $quote->category_id,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Quote could not be updated']);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['id'])) {
            http_response_code(200);
            echo json_encode(['message' => 'Missing Required Parameters']);
            break;
        }

        $quote->id = (int)$data['id'];
        $result    = $quote->delete();

        if ($result === 'not_found') {
            http_response_code(200);
            echo json_encode(['message' => 'No Quotes Found']);
        } elseif ($result === true) {
            http_response_code(200);
            echo json_encode(['id' => $quote->id]);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Quote could not be deleted']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
}
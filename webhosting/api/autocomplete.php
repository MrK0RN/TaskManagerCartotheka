<?php
// API endpoint для автодополнения
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'results' => []];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = getDB();
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    
    if (empty($type)) {
        $response['message'] = 'Не указан тип поиска';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $results = [];
    
    switch ($type) {
        case 'language':
            $stmt = $db->prepare("
                SELECT id, name, code
                FROM languages
                WHERE name LIKE :query
                ORDER BY name
                LIMIT 20
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $results = $stmt->fetchAll();
            break;
            
        case 'city':
            $stmt = $db->prepare("
                SELECT id, name, country
                FROM cities
                WHERE name LIKE :query OR country LIKE :query
                ORDER BY name
                LIMIT 20
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $results = $stmt->fetchAll();
            break;
            
        case 'profession':
            $stmt = $db->prepare("
                SELECT id, name, category
                FROM professions
                WHERE name LIKE :query
                ORDER BY name
                LIMIT 20
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $results = $stmt->fetchAll();
            break;
            
        case 'skill':
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            if ($category) {
                $stmt = $db->prepare("
                    SELECT id, name, category
                    FROM skills
                    WHERE category = :category AND name LIKE :query
                    ORDER BY name
                    LIMIT 20
                ");
                $stmt->execute([
                    ':category' => $category,
                    ':query' => '%' . $query . '%'
                ]);
            } else {
                $stmt = $db->prepare("
                    SELECT id, name, category
                    FROM skills
                    WHERE name LIKE :query
                    ORDER BY category, name
                    LIMIT 20
                ");
                $stmt->execute([':query' => '%' . $query . '%']);
            }
            $results = $stmt->fetchAll();
            break;
            
        case 'institution':
            $stmt = $db->prepare("
                SELECT id, name, type, city
                FROM educational_institutions
                WHERE name LIKE :query
                ORDER BY name
                LIMIT 20
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $results = $stmt->fetchAll();
            break;

        case 'portrait':
            $stmt = $db->prepare("
                SELECT pd.portrait_id AS id,
                       COALESCE(pd.structured_data->>'fio', 'Без имени') AS name
                FROM portrait_data pd
                WHERE pd.param_number = 1
                  AND (pd.structured_data->>'fio') ILIKE :query
                ORDER BY name
                LIMIT 20
            ");
            $stmt->execute([':query' => '%' . $query . '%']);
            $results = $stmt->fetchAll();
            break;
            
        default:
            $response['message'] = 'Неизвестный тип поиска';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
    }
    
    $response['success'] = true;
    $response['results'] = $results;
    
} catch (Exception $e) {
    $response['message'] = 'Ошибка при поиске: ' . $e->getMessage();
    error_log('Autocomplete error: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

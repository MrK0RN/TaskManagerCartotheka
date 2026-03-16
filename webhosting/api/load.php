<?php
// API endpoint для загрузки данных портрета
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'data' => []];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $response['message'] = 'Метод не разрешен';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = getDB();
    $portraitId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$portraitId) {
        $response['message'] = 'Не указан ID портрета';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Проверяем существование портрета
    $stmt = $db->prepare("SELECT id, status, created_at, updated_at FROM portraits WHERE id = :id");
    $stmt->execute([':id' => $portraitId]);
    $portrait = $stmt->fetch();
    
    if (!$portrait) {
        $response['message'] = 'Портрет не найден';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Загружаем данные по всем параметрам
    $stmt = $db->prepare("
        SELECT param_number, structured_data, free_text
        FROM portrait_data
        WHERE portrait_id = :portrait_id
        ORDER BY param_number
    ");
    $stmt->execute([':portrait_id' => $portraitId]);
    $dataRows = $stmt->fetchAll();
    
    $data = [];
    foreach ($dataRows as $row) {
        $paramNumber = $row['param_number'];
        $data['param_' . $paramNumber] = [
            'structured_data' => $row['structured_data'] ? json_decode($row['structured_data'], true) : [],
            'free_text' => $row['free_text'] ?: ''
        ];
    }
    
    $response['success'] = true;
    $response['data'] = $data;
    $response['portrait'] = $portrait;
    $response['message'] = 'Данные успешно загружены';
    
} catch (Exception $e) {
    $response['message'] = 'Ошибка при загрузке: ' . $e->getMessage();
    error_log('Load error: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

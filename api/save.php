<?php
// API endpoint для сохранения данных портрета
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'portrait_id' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Метод не разрешен';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = getDB();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        $data = $_POST;
    }
    
    // Получаем или создаем portrait_id
    $portraitId = isset($data['portrait_id']) ? (int)$data['portrait_id'] : null;
    
    if (!$portraitId) {
        // Создаем новый портрет
        $stmt = $db->prepare("INSERT INTO portraits (status) VALUES ('draft')");
        $stmt->execute();
        $portraitId = $db->lastInsertId();
    }
    
    $response['portrait_id'] = $portraitId;
    
    // Сохраняем данные по каждому параметру
    foreach ($data as $key => $value) {
        if (preg_match('/^param_(\d+)$/', $key, $matches)) {
            $paramNumber = (int)$matches[1];
            
            // Разделяем структурированные данные и свободный текст
            $structuredData = [];
            $freeText = '';
            
            if (is_array($value)) {
                // Если это массив, извлекаем structured_data и free_text
                if (isset($value['structured_data'])) {
                    $structuredData = $value['structured_data'];
                } else {
                    // Если нет явного разделения, сохраняем все как structured_data
                    $structuredData = $value;
                }
                
                if (isset($value['free_text'])) {
                    $freeText = $value['free_text'];
                }
            } else {
                // Если это строка, сохраняем как free_text
                $freeText = $value;
            }
            
            // Сохраняем или обновляем данные параметра (PostgreSQL)
            $stmt = $db->prepare("
                INSERT INTO portrait_data (portrait_id, param_number, structured_data, free_text)
                VALUES (:portrait_id, :param_number, :structured_data, :free_text)
                ON CONFLICT (portrait_id, param_number) DO UPDATE SET
                    structured_data = EXCLUDED.structured_data,
                    free_text = EXCLUDED.free_text,
                    updated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                ':portrait_id' => $portraitId,
                ':param_number' => $paramNumber,
                ':structured_data' => !empty($structuredData) ? json_encode($structuredData, JSON_UNESCAPED_UNICODE) : null,
                ':free_text' => $freeText ?: null
            ]);
        }
    }
    
    // Обновляем статус портрета
    $stmt = $db->prepare("UPDATE portraits SET updated_at = CURRENT_TIMESTAMP WHERE id = :id");
    $stmt->execute([':id' => $portraitId]);
    
    $response['success'] = true;
    $response['message'] = 'Данные успешно сохранены';
    
} catch (Exception $e) {
    $response['message'] = 'Ошибка при сохранении: ' . $e->getMessage();
    error_log('Save error: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

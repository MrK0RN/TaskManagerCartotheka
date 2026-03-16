<?php
// API: список встреч по портрету (GET), создание встречи (POST)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'meetings' => [], 'meeting' => null];

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();

    if ($method === 'GET') {
        $portraitId = isset($_GET['portrait_id']) ? (int)$_GET['portrait_id'] : null;
        if (!$portraitId) {
            $response['message'] = 'Не указан ID портрета';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("SELECT id FROM portraits WHERE id = :id");
        $stmt->execute([':id' => $portraitId]);
        if (!$stmt->fetch()) {
            $response['message'] = 'Портрет не найден';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("
            SELECT id, portrait_id, meeting_date, with_whom, description
            FROM portrait_meetings
            WHERE portrait_id = :portrait_id
            ORDER BY meeting_date DESC, id DESC
        ");
        $stmt->execute([':portrait_id' => $portraitId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $response['meetings'][] = [
                'id' => (int) $row['id'],
                'portrait_id' => (int) $row['portrait_id'],
                'meeting_date' => $row['meeting_date'],
                'with_whom' => $row['with_whom'] ?? '',
                'description' => $row['description'] ?? ''
            ];
        }
        $response['success'] = true;
        $response['message'] = 'OK';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        $portraitId = isset($input['portrait_id']) ? (int)$input['portrait_id'] : null;
        $meetingDate = isset($input['meeting_date']) ? trim($input['meeting_date']) : '';
        $withWhom = isset($input['with_whom']) ? trim($input['with_whom']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        if (!$portraitId || !$meetingDate) {
            $response['message'] = 'Укажите portrait_id и meeting_date';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("SELECT id FROM portraits WHERE id = :id");
        $stmt->execute([':id' => $portraitId]);
        if (!$stmt->fetch()) {
            $response['message'] = 'Портрет не найден';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("
            INSERT INTO portrait_meetings (portrait_id, meeting_date, with_whom, description)
            VALUES (:portrait_id, :meeting_date, :with_whom, :description)
            RETURNING id, portrait_id, meeting_date, with_whom, description
        ");
        $stmt->execute([
            ':portrait_id' => $portraitId,
            ':meeting_date' => $meetingDate,
            ':with_whom' => $withWhom,
            ':description' => $description
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['success'] = true;
        $response['message'] = 'Встреча добавлена';
        $response['meeting'] = [
            'id' => (int) $row['id'],
            'portrait_id' => (int) $row['portrait_id'],
            'meeting_date' => $row['meeting_date'],
            'with_whom' => $row['with_whom'] ?? '',
            'description' => $row['description'] ?? ''
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['id'])) {
            $response['message'] = 'Укажите id встречи';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $meetingId = (int) $input['id'];
        $portraitId = isset($input['portrait_id']) ? (int)$input['portrait_id'] : null;
        $meetingDate = isset($input['meeting_date']) ? trim($input['meeting_date']) : '';
        $withWhom = isset($input['with_whom']) ? trim($input['with_whom']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        if (!$portraitId || !$meetingDate) {
            $response['message'] = 'Укажите portrait_id и meeting_date';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("SELECT id FROM portrait_meetings WHERE id = :id AND portrait_id = :portrait_id");
        $stmt->execute([':id' => $meetingId, ':portrait_id' => $portraitId]);
        if (!$stmt->fetch()) {
            $response['message'] = 'Встреча не найдена';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("
            UPDATE portrait_meetings
            SET meeting_date = :meeting_date, with_whom = :with_whom, description = :description
            WHERE id = :id
            RETURNING id, portrait_id, meeting_date, with_whom, description
        ");
        $stmt->execute([
            ':id' => $meetingId,
            ':meeting_date' => $meetingDate,
            ':with_whom' => $withWhom,
            ':description' => $description
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $response['success'] = true;
        $response['message'] = 'Встреча сохранена';
        $response['meeting'] = [
            'id' => (int) $row['id'],
            'portrait_id' => (int) $row['portrait_id'],
            'meeting_date' => $row['meeting_date'],
            'with_whom' => $row['with_whom'] ?? '',
            'description' => $row['description'] ?? ''
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $response['message'] = 'Метод не разрешен';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $response['message'] = 'Ошибка: ' . $e->getMessage();
    error_log('Meetings API error: ' . $e->getMessage());
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

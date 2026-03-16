<?php
// API endpoint для списка портретов и поиска
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'items' => []];

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $response['message'] = 'Метод не разрешен';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $db = getDB();
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
    $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

    // Валидация status
    if ($status !== '' && $status !== 'draft' && $status !== 'completed') {
        $status = '';
    }

    $params = [];
    $where = ['1=1'];

    if ($status !== '') {
        $where[] = 'p.status = :status';
        $params[':status'] = $status;
    }

    if ($dateFrom !== '') {
        $where[] = 'p.updated_at >= :date_from';
        $params[':date_from'] = $dateFrom;
    }

    if ($dateTo !== '') {
        $where[] = 'p.updated_at <= :date_to';
        $params[':date_to'] = $dateTo . ' 23:59:59';
    }

    if ($search !== '') {
        $searchPattern = '%' . $search . '%';
        $where[] = '(
            (pd.structured_data IS NOT NULL AND pd.structured_data->>\'fio\' ILIKE :search_fio)
            OR p.id IN (
                SELECT portrait_id FROM portrait_data
                WHERE free_text IS NOT NULL AND free_text ILIKE :search_text
            )
        )';
        $params[':search_fio'] = $searchPattern;
        $params[':search_text'] = $searchPattern;
    }

    $sql = "
        SELECT p.id, p.status, p.created_at, p.updated_at,
               COALESCE(pd.structured_data->>'fio', '') AS fio
        FROM portraits p
        LEFT JOIN portrait_data pd ON pd.portrait_id = p.id AND pd.param_number = 1
        WHERE " . implode(' AND ', $where) . "
        ORDER BY p.updated_at DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $response['items'][] = [
            'id' => (int) $row['id'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'fio' => $row['fio'] !== '' ? $row['fio'] : 'Без названия'
        ];
    }

    $response['success'] = true;
    $response['message'] = 'OK';

} catch (Exception $e) {
    $response['message'] = 'Ошибка: ' . $e->getMessage();
    error_log('List API error: ' . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

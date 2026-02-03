<?php
// API задач: дерево (GET), создание (POST), обновление (PUT), удаление (DELETE)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$response = ['success' => false, 'message' => '', 'tasks' => [], 'task' => null];

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();

    if ($method === 'GET') {
        $dueDate = isset($_GET['due_date']) ? trim($_GET['due_date']) : '';
        $portraitId = isset($_GET['portrait_id']) ? (int)$_GET['portrait_id'] : null;
        $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
        $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

        $where = ['1=1'];
        $params = [];

        if ($dueDate !== '') {
            $where[] = 't.due_date = :due_date';
            $params[':due_date'] = $dueDate;
        }
        if ($portraitId > 0) {
            $where[] = 't.portrait_id = :portrait_id';
            $params[':portrait_id'] = $portraitId;
        }
        if ($dateFrom !== '') {
            $where[] = 't.due_date >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== '') {
            $where[] = 't.due_date <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        $sql = "
            SELECT t.id, t.parent_id, t.title, t.due_date, t.portrait_id, t.sort_order, t.created_at, t.updated_at,
                   COALESCE(pd.structured_data->>'fio', '') AS assignee_fio
            FROM tasks t
            LEFT JOIN portrait_data pd ON pd.portrait_id = t.portrait_id AND pd.param_number = 1
            WHERE " . implode(' AND ', $where) . "
            ORDER BY t.parent_id NULLS FIRST, t.sort_order, t.id
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $flat = [];
        foreach ($rows as $row) {
            $flat[] = [
                'id' => (int) $row['id'],
                'parent_id' => $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
                'title' => $row['title'] ?? '',
                'due_date' => $row['due_date'] ?? null,
                'portrait_id' => $row['portrait_id'] !== null ? (int) $row['portrait_id'] : null,
                'assignee_fio' => $row['assignee_fio'] !== '' ? $row['assignee_fio'] : null,
                'sort_order' => (int) $row['sort_order'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'children' => [],
            ];
        }

        $response['tasks'] = buildTree($flat);
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
        $parentId = isset($input['parent_id']) ? (int)$input['parent_id'] : null;
        if ($parentId === 0) {
            $parentId = null;
        }
        $title = isset($input['title']) ? trim($input['title']) : '';
        $dueDate = isset($input['due_date']) ? trim($input['due_date']) : null;
        $portraitId = isset($input['portrait_id']) ? (int)$input['portrait_id'] : null;
        if ($portraitId === 0) {
            $portraitId = null;
        }
        $sortOrder = isset($input['sort_order']) ? (int)$input['sort_order'] : 0;

        if ($title === '') {
            $response['message'] = 'Укажите название задачи';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($parentId !== null) {
            $stmt = $db->prepare("SELECT id FROM tasks WHERE id = :id");
            $stmt->execute([':id' => $parentId]);
            if (!$stmt->fetch()) {
                $response['message'] = 'Родительская задача не найдена';
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        if ($portraitId !== null) {
            $stmt = $db->prepare("SELECT id FROM portraits WHERE id = :id");
            $stmt->execute([':id' => $portraitId]);
            if (!$stmt->fetch()) {
                $response['message'] = 'Портрет не найден';
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit;
            }
        }

        $stmt = $db->prepare("
            INSERT INTO tasks (parent_id, title, due_date, portrait_id, sort_order, updated_at)
            VALUES (:parent_id, :title, :due_date, :portrait_id, :sort_order, CURRENT_TIMESTAMP)
            RETURNING id, parent_id, title, due_date, portrait_id, sort_order, created_at, updated_at
        ");
        $stmt->execute([
            ':parent_id' => $parentId,
            ':title' => $title,
            ':due_date' => $dueDate ?: null,
            ':portrait_id' => $portraitId,
            ':sort_order' => $sortOrder,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $taskId = (int) $row['id'];
        $assigneeFio = null;
        if ($row['portrait_id']) {
            $st = $db->prepare("SELECT structured_data->>'fio' AS fio FROM portrait_data WHERE portrait_id = :pid AND param_number = 1");
            $st->execute([':pid' => $row['portrait_id']]);
            $fr = $st->fetch(PDO::FETCH_ASSOC);
            if ($fr && !empty($fr['fio'])) {
                $assigneeFio = $fr['fio'];
            }
        }
        $response['success'] = true;
        $response['message'] = 'Задача создана';
        $response['task'] = [
            'id' => $taskId,
            'parent_id' => $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
            'title' => $row['title'],
            'due_date' => $row['due_date'],
            'portrait_id' => $row['portrait_id'] !== null ? (int) $row['portrait_id'] : null,
            'assignee_fio' => $assigneeFio,
            'sort_order' => (int) $row['sort_order'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'children' => [],
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'PUT') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['id'])) {
            $response['message'] = 'Укажите id задачи';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $taskId = (int) $input['id'];
        $parentId = array_key_exists('parent_id', $input) ? (int)$input['parent_id'] : null;
        if ($parentId === 0) {
            $parentId = null;
        }
        $title = isset($input['title']) ? trim($input['title']) : null;
        $dueDate = isset($input['due_date']) ? trim($input['due_date']) : null;
        $portraitId = array_key_exists('portrait_id', $input) ? (int)$input['portrait_id'] : null;
        if ($portraitId === 0) {
            $portraitId = null;
        }
        $sortOrder = array_key_exists('sort_order', $input) ? (int)$input['sort_order'] : null;

        $stmt = $db->prepare("SELECT id, parent_id, title, due_date, portrait_id, sort_order FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $taskId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            $response['message'] = 'Задача не найдена';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($parentId !== null && $parentId === $taskId) {
            $response['message'] = 'Задача не может быть родителем самой себя';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $updates = [];
        $params = [':id' => $taskId];
        if ($parentId !== null) {
            $updates[] = 'parent_id = :parent_id';
            $params[':parent_id'] = $parentId;
        }
        if ($title !== null) {
            $updates[] = 'title = :title';
            $params[':title'] = $title;
        }
        if ($dueDate !== null) {
            $updates[] = 'due_date = :due_date';
            $params[':due_date'] = $dueDate ?: null;
        }
        if ($portraitId !== null) {
            $updates[] = 'portrait_id = :portrait_id';
            $params[':portrait_id'] = $portraitId;
        }
        if ($sortOrder !== null) {
            $updates[] = 'sort_order = :sort_order';
            $params[':sort_order'] = $sortOrder;
        }
        if (empty($updates)) {
            $response['success'] = true;
            $response['message'] = 'OK';
            $response['task'] = $existing;
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $updates[] = 'updated_at = CURRENT_TIMESTAMP';
        $stmt = $db->prepare("
            UPDATE tasks SET " . implode(', ', $updates) . "
            WHERE id = :id
            RETURNING id, parent_id, title, due_date, portrait_id, sort_order, created_at, updated_at
        ");
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $assigneeFio = null;
        if ($row['portrait_id']) {
            $st = $db->prepare("SELECT structured_data->>'fio' AS fio FROM portrait_data WHERE portrait_id = :pid AND param_number = 1");
            $st->execute([':pid' => $row['portrait_id']]);
            $fr = $st->fetch(PDO::FETCH_ASSOC);
            if ($fr && !empty($fr['fio'])) {
                $assigneeFio = $fr['fio'];
            }
        }
        $response['success'] = true;
        $response['message'] = 'Задача обновлена';
        $response['task'] = [
            'id' => (int) $row['id'],
            'parent_id' => $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
            'title' => $row['title'],
            'due_date' => $row['due_date'],
            'portrait_id' => $row['portrait_id'] !== null ? (int) $row['portrait_id'] : null,
            'assignee_fio' => $assigneeFio,
            'sort_order' => (int) $row['sort_order'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'children' => [],
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'DELETE') {
        $taskId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : null);
        if (!$taskId) {
            $response['message'] = 'Укажите id задачи';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $stmt = $db->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $taskId]);
        $response['success'] = true;
        $response['message'] = 'Задача удалена';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $response['message'] = 'Метод не разрешен';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    $response['message'] = 'Ошибка: ' . $e->getMessage();
    error_log('Tasks API error: ' . $e->getMessage());
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

function buildTree(array $flat) {
    $byId = [];
    foreach ($flat as $item) {
        $byId[$item['id']] = $item;
    }
    $tree = [];
    foreach ($flat as $item) {
        if ($item['parent_id'] === null) {
            $tree[] = $item;
        } else {
            if (isset($byId[$item['parent_id']])) {
                $byId[$item['parent_id']]['children'][] = $item;
            } else {
                $tree[] = $item;
            }
        }
    }
    return $tree;
}

<?php
/**
 * Подключение к PostgreSQL и запросы для бота.
 */

require_once __DIR__ . '/config.php';

function getConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s;options=--client_encoding=UTF8',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function subscribeChat(int $chatId): bool {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO telegram_subscribers (chat_id) VALUES (:chat_id) ON CONFLICT (chat_id) DO NOTHING'
    );
    $stmt->execute(['chat_id' => $chatId]);
    return $stmt->rowCount() > 0;
}

function getSubscribers(): array {
    $pdo = getConnection();
    $stmt = $pdo->query('SELECT chat_id FROM telegram_subscribers');
    return array_column($stmt->fetchAll(), 'chat_id');
}

/**
 * Люди с ДР в указанные месяц и день. portrait_id, fio, birth_date.
 */
function getBirthdaysOnDate(int $month, int $day): array {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT pd.portrait_id,
               COALESCE(pd.structured_data->>'fio', 'Без имени') AS fio,
               (pd.structured_data->>'birth_date')::text AS birth_date
        FROM portrait_data pd
        WHERE pd.param_number = 1
          AND pd.structured_data->>'birth_date' IS NOT NULL
          AND pd.structured_data->>'birth_date' <> ''
          AND EXTRACT(MONTH FROM (pd.structured_data->>'birth_date')::date) = :month
          AND EXTRACT(DAY FROM (pd.structured_data->>'birth_date')::date) = :day
        ORDER BY fio
    ");
    $stmt->execute(['month' => $month, 'day' => $day]);
    return $stmt->fetchAll();
}

/**
 * Задачи с due_date на указанную дату. id, title, due_date, portrait_id, fio.
 */
function getTasksDueOnDate(string $dueDate): array {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, t.due_date, t.portrait_id,
               COALESCE(pd.structured_data->>'fio', '') AS fio
        FROM tasks t
        LEFT JOIN portrait_data pd ON pd.portrait_id = t.portrait_id AND pd.param_number = 1
        WHERE t.due_date = :due_date
        ORDER BY t.parent_id NULLS FIRST, t.sort_order, t.id
    ");
    $stmt->execute(['due_date' => $dueDate]);
    return $stmt->fetchAll();
}

/**
 * Задачи на дату с привязанным человеком (для напоминания).
 */
function getTasksWithAssigneeDueOnDate(string $dueDate): array {
    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, t.due_date, t.portrait_id,
               COALESCE(pd.structured_data->>'fio', 'Без имени') AS fio
        FROM tasks t
        LEFT JOIN portrait_data pd ON pd.portrait_id = t.portrait_id AND pd.param_number = 1
        WHERE t.due_date = :due_date AND t.portrait_id IS NOT NULL
        ORDER BY t.parent_id NULLS FIRST, t.sort_order, t.id
    ");
    $stmt->execute(['due_date' => $dueDate]);
    return $stmt->fetchAll();
}

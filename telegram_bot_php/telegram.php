<?php
/**
 * Вызовы Telegram Bot API (getUpdates, sendMessage).
 */

require_once __DIR__ . '/config.php';

function telegramRequest(string $method, array $params = []): array {
    $url = 'https://api.telegram.org/bot' . BOT_TOKEN . '/' . $method;
    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/json',
            'content' => json_encode($params),
            'timeout' => 60,
        ],
    ];
    $ctx = stream_context_create($opts);
    $response = @file_get_contents($url, false, $ctx);
    if ($response === false) {
        return ['ok' => false, 'error' => 'Request failed'];
    }
    $data = json_decode($response, true);
    return is_array($data) ? $data : ['ok' => false, 'error' => 'Invalid JSON'];
}

function getUpdates(int $offset = 0, int $timeout = 30): array {
    $result = telegramRequest('getUpdates', [
        'offset'  => $offset,
        'timeout' => $timeout,
    ]);
    if (empty($result['ok']) || !isset($result['result'])) {
        return [];
    }
    return $result['result'];
}

function sendMessage(int $chatId, string $text): bool {
    $result = telegramRequest('sendMessage', [
        'chat_id' => $chatId,
        'text'    => $text,
        'disable_web_page_preview' => true,
    ]);
    return !empty($result['ok']);
}

<?php
// Транскрибация через Yandex SpeechKit: принимает аудио (Ogg Opus) от фронтенда, возвращает JSON с текстом.
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен'], JSON_UNESCAPED_UNICODE);
    exit;
}

$audioKey = 'audio';
if (!isset($_FILES[$audioKey]) || $_FILES[$audioKey]['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Файл аудио не загружен или ошибка загрузки'], JSON_UNESCAPED_UNICODE);
    exit;
}

$apiKey = getenv('YANDEX_SPEECHKIT_API_KEY');
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'Не настроен YANDEX_SPEECHKIT_API_KEY'], JSON_UNESCAPED_UNICODE);
    exit;
}

$file = $_FILES[$audioKey];
$tmpPath = $file['tmp_name'];
$size = filesize($tmpPath);

// SpeechKit short recognition: до 1 MB, до 30 сек
if ($size > 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => 'Файл слишком большой (макс. 1 МБ)'], JSON_UNESCAPED_UNICODE);
    exit;
}

$mime = $file['type'] ?: '';
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// SpeechKit принимает только Ogg Opus (или LPCM)
$allowedTypes = ['audio/ogg', 'audio/opus'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$detectedMime = finfo_file($finfo, $tmpPath);
finfo_close($finfo);
if (!in_array($detectedMime, $allowedTypes, true) && !in_array($mime, $allowedTypes, true)) {
    if (!in_array($ext, ['ogg', 'opus'], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Нужен формат Ogg Opus. Включите запись в этом формате или используйте другой браузер.'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$lang = getenv('YANDEX_STT_LANG') ?: 'ru-RU';
$url = 'https://stt.api.cloud.yandex.net/speech/v1/stt:recognize?' . http_build_query([
    'lang' => $lang,
    'format' => 'oggopus',
    'topic' => 'general',
]);

$audioBytes = file_get_contents($tmpPath);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $audioBytes,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Api-Key ' . $apiKey,
        'Content-Type: audio/ogg',
    ],
    CURLOPT_TIMEOUT => 30,
]);

$responseBody = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    http_response_code(502);
    echo json_encode(['error' => 'Сервис распознавания недоступен: ' . $curlErr], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($httpCode !== 200) {
    $decoded = json_decode($responseBody, true);
    $msg = isset($decoded['error']['message']) ? $decoded['error']['message'] : $responseBody;
    http_response_code($httpCode >= 400 ? $httpCode : 502);
    echo json_encode(['error' => 'Ошибка распознавания: ' . $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

// SpeechKit v1 sync recognize возвращает JSON: {"result": "распознанный текст"}
$decoded = json_decode($responseBody, true);
$text = isset($decoded['result']) ? $decoded['result'] : '';

echo json_encode(['text' => $text], JSON_UNESCAPED_UNICODE);

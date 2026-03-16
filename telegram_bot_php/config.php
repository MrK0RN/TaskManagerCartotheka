<?php
/**
 * Конфигурация бота: config.local.php или переменные окружения.
 */

$localFile = __DIR__ . '/config.local.php';
if (file_exists($localFile)) {
    $local = require $localFile;
} else {
    $local = [];
}

function envOrLocal(string $key, $default = ''): string {
    global $local;
    $v = getenv($key);
    if ($v !== false && $v !== '') {
        return is_string($v) ? trim($v) : (string) $v;
    }
    return isset($local[$key]) ? trim((string) $local[$key]) : (string) $default;
}

define('BOT_TOKEN', envOrLocal('TELEGRAM_BOT_TOKEN', ''));
define('SITE_URL', rtrim(envOrLocal('SITE_URL', ''), '/'));
define('DB_HOST', envOrLocal('DB_HOST', '127.0.0.1'));
define('DB_PORT', envOrLocal('DB_PORT', '5432'));
define('DB_NAME', envOrLocal('DB_NAME', 'portrait_db'));
define('DB_USER', envOrLocal('DB_USER', 'portrait_user'));
define('DB_PASS', envOrLocal('DB_PASS', 'portrait_pass'));

<?php
// Общие настройки приложения

// Настройки ошибок (для разработки)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Часовой пояс
date_default_timezone_set('Europe/Moscow');

// Кодировка
mb_internal_encoding('UTF-8');

// Базовый URL (настройте под ваш проект)
define('BASE_URL', '/');

// Пути к директориям
define('ROOT_PATH', dirname(__DIR__));
define('MODULES_PATH', ROOT_PATH . '/modules');
define('COMPONENTS_PATH', ROOT_PATH . '/components');
define('API_PATH', ROOT_PATH . '/api');
define('STYLES_PATH', ROOT_PATH . '/styles');
define('SCRIPTS_PATH', ROOT_PATH . '/scripts');

// Версия по времени изменения файла (для сброса кэша CSS/JS)
function asset_version($path) {
    $full = ROOT_PATH . '/' . ltrim($path, '/');
    return file_exists($full) ? '?v=' . filemtime($full) : '';
}

// Настройки сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

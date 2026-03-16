<?php
/**
 * Локальный конфиг для webhosting (без Docker).
 * Скопируйте этот файл в local.php и заполните значения.
 * Файл local.php не попадает в git (секреты).
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'portrait_db');
define('DB_USER', 'portrait_user');
define('DB_PASS', 'your_password');

// Опционально: голосовой ввод (Yandex SpeechKit)
// define('YANDEX_SPEECHKIT_API_KEY', '');
// define('YANDEX_STT_LANG', 'ru-RU');

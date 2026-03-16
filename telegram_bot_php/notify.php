#!/usr/bin/env php
<?php
/**
 * Рассылка уведомлений по расписанию. Запускать по cron:
 *   8:00 МСК:  php notify.php --morning
 *   20:00 МСК: php notify.php --evening
 *   22:00 МСК: php notify.php --tasks-remind
 *
 * Или одной строкой для каждого типа:
 *   php notify.php --tasks-morning   (8:00 — задачи на сегодня)
 *   php notify.php --tasks-evening   (20:00 — задачи на завтра)
 * Дни рождения и задачи на день можно слать вместе (--morning / --evening уже включают ДР + задачи).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/messages.php';
require_once __DIR__ . '/telegram.php';

if (BOT_TOKEN === '') {
    fwrite(STDERR, "Укажите TELEGRAM_BOT_TOKEN в config.local.php или в переменных окружения.\n");
    exit(1);
}

$options = getopt('', ['morning', 'evening', 'tasks-morning', 'tasks-evening', 'tasks-remind']);
$morning = isset($options['morning']);
$evening = isset($options['evening']);
$tasksMorning = isset($options['tasks-morning']);
$tasksEvening = isset($options['tasks-evening']);
$tasksRemind = isset($options['tasks-remind']);

if (!$morning && !$evening && !$tasksMorning && !$tasksEvening && !$tasksRemind) {
    fwrite(STDERR, "Укажите один из флагов: --morning, --evening, --tasks-morning, --tasks-evening, --tasks-remind\n");
    exit(1);
}

$subscribers = getSubscribers();
if (empty($subscribers)) {
    echo "Нет подписчиков, рассылка не выполняется.\n";
    exit(0);
}

// Дни рождения + задачи на день (утро или вечер)
if ($morning || $evening) {
    $isMorning = $morning;
    $target = getTargetDate($isMorning);
    $month = (int) date('n', strtotime($target));
    $day = (int) date('j', strtotime($target));
    $people = getBirthdaysOnDate($month, $day);
    $textDr = buildMessage($people, $target, $isMorning, SITE_URL);
    foreach ($subscribers as $chatId) {
        sendMessage((int) $chatId, $textDr);
    }
    $tasks = getTasksDueOnDate($target);
    $textTasks = buildTasksMessage($tasks, $target, $isMorning, SITE_URL);
    foreach ($subscribers as $chatId) {
        sendMessage((int) $chatId, $textTasks);
    }
    exit(0);
}

// Только задачи на сегодня (8:00)
if ($tasksMorning) {
    $target = getTargetDate(true);
    $tasks = getTasksDueOnDate($target);
    $text = buildTasksMessage($tasks, $target, true, SITE_URL);
    foreach ($subscribers as $chatId) {
        sendMessage((int) $chatId, $text);
    }
    exit(0);
}

// Только задачи на завтра (20:00)
if ($tasksEvening) {
    $target = getTargetDate(false);
    $tasks = getTasksDueOnDate($target);
    $text = buildTasksMessage($tasks, $target, false, SITE_URL);
    foreach ($subscribers as $chatId) {
        sendMessage((int) $chatId, $text);
    }
    exit(0);
}

// Напоминание в 22:00
if ($tasksRemind) {
    $today = date('Y-m-d');
    $tasks = getTasksWithAssigneeDueOnDate($today);
    $text = buildTasksRemindMessage($tasks, SITE_URL);
    if ($text === '') {
        echo "Нет задач с исполнителем на сегодня, напоминание не отправляется.\n";
        exit(0);
    }
    foreach ($subscribers as $chatId) {
        sendMessage((int) $chatId, $text);
    }
    exit(0);
}

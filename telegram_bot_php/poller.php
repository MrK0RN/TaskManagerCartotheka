#!/usr/bin/env php
<?php
/**
 * Long-polling: приём команд /start, /morning, /evening.
 * Запуск: php poller.php
 * Рассылка по расписанию — через cron и notify.php (8:00, 20:00, 22:00 МСК).
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/messages.php';
require_once __DIR__ . '/telegram.php';

if (BOT_TOKEN === '') {
    fwrite(STDERR, "Укажите TELEGRAM_BOT_TOKEN в config.local.php или в переменных окружения.\n");
    exit(1);
}

$offset = 0;
$log = function (string $msg): void {
    echo date('Y-m-d H:i:s') . ' ' . $msg . "\n";
};

$log('Запуск бота. Команды: /start, /morning, /evening. Рассылка по cron: notify.php');

while (true) {
    try {
        $updates = getUpdates($offset, 30);
    } catch (Throwable $e) {
        $log('getUpdates error: ' . $e->getMessage());
        sleep(5);
        continue;
    }

    foreach ($updates as $u) {
        $offset = $u['update_id'] + 1;
        $message = $u['message'] ?? null;
        if (!$message || !isset($message['chat']['id'])) {
            continue;
        }
        $chatId = (int) $message['chat']['id'];
        $text = trim((string) ($message['text'] ?? ''));

        if ($text === '/start') {
            try {
                $isNew = subscribeChat($chatId);
                if ($isNew) {
                    sendMessage($chatId,
                        "Вы подписаны на уведомления.\n\n"
                        . "Дни рождения: в 8:00 — кто сегодня, в 20:00 — кто завтра.\n"
                        . "Задачи: в 8:00 — на сегодня, в 20:00 — на завтра; в 22:00 — напоминание внести обновление по задачам с прикреплённым человеком.\n\n"
                        . "Команды: /morning — ДР и задачи на сегодня, /evening — ДР и задачи на завтра."
                    );
                } else {
                    sendMessage($chatId, 'Вы уже подписаны на уведомления (ДР и задачи).');
                }
            } catch (Throwable $e) {
                $log('Subscribe error: ' . $e->getMessage());
                sendMessage($chatId, 'Ошибка подписки. Попробуйте позже.');
            }
            continue;
        }

        if ($text === '/morning') {
            try {
                $target = getTargetDate(true);
                $people = getBirthdaysOnDate((int) date('n', strtotime($target)), (int) date('j', strtotime($target)));
                $textMsg = buildMessage($people, $target, true, SITE_URL);
                sendMessage($chatId, $textMsg);
                $tasks = getTasksDueOnDate($target);
                $textTasks = buildTasksMessage($tasks, $target, true, SITE_URL);
                sendMessage($chatId, $textTasks);
            } catch (Throwable $e) {
                $log('Morning error: ' . $e->getMessage());
                sendMessage($chatId, 'Ошибка. Попробуйте позже.');
            }
            continue;
        }

        if ($text === '/evening') {
            try {
                $target = getTargetDate(false);
                $people = getBirthdaysOnDate((int) date('n', strtotime($target)), (int) date('j', strtotime($target)));
                $textMsg = buildMessage($people, $target, false, SITE_URL);
                sendMessage($chatId, $textMsg);
                $tasks = getTasksDueOnDate($target);
                $textTasks = buildTasksMessage($tasks, $target, false, SITE_URL);
                sendMessage($chatId, $textTasks);
            } catch (Throwable $e) {
                $log('Evening error: ' . $e->getMessage());
                sendMessage($chatId, 'Ошибка. Попробуйте позже.');
            }
            continue;
        }
    }
}

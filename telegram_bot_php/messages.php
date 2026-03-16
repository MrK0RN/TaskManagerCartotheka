<?php
/**
 * Формирование текстов сообщений: ДР и задачи.
 */

require_once __DIR__ . '/config.php';

function getTargetDate(bool $morning): string {
    $today = date('Y-m-d');
    if ($morning) {
        return $today;
    }
    return date('Y-m-d', strtotime($today . ' +1 day'));
}

function ageYears(string $birthDate, string $onDate): int {
    $b = new DateTime($birthDate);
    $o = new DateTime($onDate);
    return (int) $o->diff($b)->y;
}

function ageWord(int $years): string {
    if ($years % 100 >= 11 && $years % 100 <= 14) {
        return 'лет';
    }
    $r = $years % 10;
    if ($r === 1) {
        return 'год';
    }
    if ($r >= 2 && $r <= 4) {
        return 'года';
    }
    return 'лет';
}

function formatPerson(int $portraitId, string $fio, ?string $birthDate, string $onDate, string $siteUrl): string {
    $ageStr = '';
    if ($birthDate !== null && $birthDate !== '') {
        $years = ageYears($birthDate, $onDate);
        $ageStr = ', исполняется ' . $years . ' ' . ageWord($years);
    }
    $link = '';
    if ($siteUrl !== '' && $portraitId > 0) {
        $link = "\n   📎 Анкета: " . $siteUrl . '/view.php?id=' . $portraitId;
    }
    return '• ' . $fio . $ageStr . $link;
}

/**
 * Сообщение о днях рождения.
 * @param array $people [['portrait_id'=>, 'fio'=>, 'birth_date'=>], ...]
 */
function buildMessage(array $people, string $targetDate, bool $morning, string $siteUrl = ''): string {
    $siteUrl = $siteUrl !== '' ? $siteUrl : SITE_URL;
    if (empty($people)) {
        if ($morning) {
            return "Доброе утро!\n\nСегодня ни у кого из портретов в базе нет дня рождения.\n\nКоманды: /morning — кто сегодня, /evening — кто завтра.";
        }
        return "Добрый вечер!\n\nЗавтра ни у кого из портретов в базе нет дня рождения.\n\nКоманды: /morning — кто сегодня, /evening — кто завтра.";
    }
    $header = $morning
        ? "Доброе утро!\n\nСегодня день рождения:"
        : "Добрый вечер!\n\nЗавтра день рождения:";
    $lines = [$header];
    foreach ($people as $p) {
        $lines[] = formatPerson(
            (int) ($p['portrait_id'] ?? 0),
            $p['fio'] ?? 'Без имени',
            $p['birth_date'] ?? null,
            $targetDate,
            $siteUrl
        );
    }
    $lines[] = '';
    $lines[] = 'Команды: /morning — кто сегодня, /evening — кто завтра.';
    return implode("\n", $lines);
}

/**
 * Сообщение о задачах на сегодня/завтра.
 */
function buildTasksMessage(array $tasks, string $targetDate, bool $morning, string $siteUrl = ''): string {
    $header = $morning ? 'Задачи на сегодня:' : 'Задачи на завтра:';
    if (empty($tasks)) {
        return $header . "\n\nНет задач на эту дату.";
    }
    $lines = [$header];
    foreach ($tasks as $t) {
        $title = trim($t['title'] ?? 'Без названия');
        $fio = trim($t['fio'] ?? '');
        $lines[] = $fio !== '' ? "• {$title} — {$fio}" : "• {$title}";
    }
    if ($siteUrl !== '') {
        $lines[] = "\n📎 Задачи: " . rtrim($siteUrl, '/') . '/tasks.php';
    }
    return implode("\n", $lines);
}

/**
 * Напоминание внести обновление по задачам с прикреплённым человеком.
 */
function buildTasksRemindMessage(array $tasks, string $siteUrl = ''): string {
    if (empty($tasks)) {
        return '';
    }
    $lines = [
        'Напоминание: не забудьте внести обновление по задачам за сегодня, к которым прикреплены люди:',
        '',
    ];
    foreach ($tasks as $t) {
        $title = trim($t['title'] ?? 'Без названия');
        $fio = trim($t['fio'] ?? 'Без имени');
        $lines[] = "• {$title} — {$fio}";
    }
    if ($siteUrl !== '') {
        $lines[] = "\n📎 Задачи: " . rtrim($siteUrl, '/') . '/tasks.php';
    }
    return implode("\n", $lines);
}

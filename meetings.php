<?php
require_once __DIR__ . '/config/config.php';

$portraitId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$portrait = null;
$portraitData = [];

if ($portraitId) {
    try {
        require_once __DIR__ . '/config/database.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT id, status, created_at, updated_at FROM portraits WHERE id = :id");
        $stmt->execute([':id' => $portraitId]);
        $portrait = $stmt->fetch();
        if ($portrait) {
            $stmt = $db->prepare("
                SELECT param_number, structured_data, free_text
                FROM portrait_data
                WHERE portrait_id = :portrait_id
                ORDER BY param_number
            ");
            $stmt->execute([':portrait_id' => $portraitId]);
            $dataRows = $stmt->fetchAll();
            foreach ($dataRows as $row) {
                $paramNumber = $row['param_number'];
                $portraitData[$paramNumber] = [
                    'structured_data' => $row['structured_data'] ? json_decode($row['structured_data'], true) : [],
                    'free_text' => $row['free_text'] ?: ''
                ];
            }
        }
    } catch (Exception $e) {
        error_log('Meetings page load error: ' . $e->getMessage());
    }
}

if (!$portrait) {
    header('Location: list.php');
    exit;
}

$s = $portraitData[1]['structured_data'] ?? [];
$fio = isset($s['fio']) && $s['fio'] !== '' ? $s['fio'] : 'Без имени';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Встречи: <?php echo htmlspecialchars($fio); ?> — Портрет личности</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/components.css">
</head>
<body>
    <div class="container">
        <?php include COMPONENTS_PATH . '/header.php'; ?>

        <div class="form-container meetings-page">
            <p class="form-back-link">
                <a href="list.php">← Вернуться к списку</a>
                <a href="view.php?id=<?php echo (int)$portraitId; ?>" class="meetings-view-link">Просмотр портрета</a>
            </p>

            <h1 class="meetings-title">Встречи: <?php echo htmlspecialchars($fio); ?></h1>

            <div class="meetings-form-wrap">
                <form id="meetingForm" class="meetings-form">
                    <input type="hidden" name="portrait_id" id="meeting_portrait_id" value="<?php echo (int)$portraitId; ?>">
                    <div class="form-group">
                        <label for="meeting_date">Дата</label>
                        <input type="date" id="meeting_date" name="meeting_date" required>
                    </div>
                    <div class="form-group">
                        <label for="meeting_with_whom">С кем</label>
                        <input type="text" id="meeting_with_whom" name="with_whom" placeholder="С кем встреча">
                    </div>
                    <div class="form-group">
                        <label for="meeting_description">Описание</label>
                        <textarea id="meeting_description" name="description" rows="3" placeholder="Описание встречи"></textarea>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">Добавить встречу</button>
                    </div>
                </form>
            </div>

            <div class="meetings-list-wrap">
                <h2 class="meetings-list-title">Список встреч</h2>
                <div id="meetings-loading" class="meetings-loading">Загрузка…</div>
                <div id="meetings-empty" class="meetings-empty" style="display: none;">Встреч пока нет.</div>
                <ul id="meetings-list" class="meetings-list" style="display: none;"></ul>
            </div>
        </div>

        <?php include COMPONENTS_PATH . '/footer.php'; ?>
    </div>

    <script src="scripts/meetings.js"></script>
</body>
</html>

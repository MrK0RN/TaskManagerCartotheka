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
        error_log('View load error: ' . $e->getMessage());
    }
}

if (!$portrait) {
    header('Location: list.php');
    exit;
}

$readOnly = true;

$sections = [
    ['title' => '📋 Справочные (демографические и биографические) данные', 'params' => [1, 2, 3, 4]],
    ['title' => '💼 Ресурсы личности', 'params' => [5, 6, 7, 8, 9, 10, 11]],
    ['title' => '🧠 Психологический профиль', 'params' => [12, 13, 14, 15]],
    ['title' => '🌍 Ценностно-смысловая сфера', 'params' => [16, 17, 18]],
    ['title' => '👥 Социальное измерение', 'params' => [19, 20, 21, 22]],
    ['title' => '🔄 Поведенческие и контекстуальные паттерны', 'params' => [23, 24, 25]]
];

$s = $portraitData[1]['structured_data'] ?? [];
$fio = isset($s['fio']) && $s['fio'] !== '' ? $s['fio'] : 'Без имени';
$statusText = $portrait['status'] === 'completed' ? 'Завершён' : 'Черновик';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($fio); ?> — Портрет личности</title>
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/components.css">
</head>
<body>
    <div class="container">
        <?php include COMPONENTS_PATH . '/header.php'; ?>

        <div class="form-container view-mode">
            <p class="form-back-link">
                <a href="list.php">← Вернуться к списку</a>
                <a href="meetings.php?id=<?php echo (int)$portraitId; ?>" class="btn btn-outline">Встречи с этим человеком</a>
            </p>

            <div id="personalityView">
                <?php foreach ($sections as $sectionIndex => $section): ?>
                <div class="section" data-section="<?php echo $sectionIndex; ?>">
                    <div class="section-header" onclick="toggleSection(this)">
                        <h2><?php echo htmlspecialchars($section['title']); ?></h2>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="section-content">
                        <?php foreach ($section['params'] as $paramNumber): ?>
                            <?php
                            $paramData = isset($portraitData[$paramNumber]) ? $portraitData[$paramNumber] : ['structured_data' => [], 'free_text' => ''];
                            $moduleFile = MODULES_PATH . '/param-' . $paramNumber . '.php';
                            if (file_exists($moduleFile)) {
                                include $moduleFile;
                            } else {
                                echo '<div class="form-group"><p>Модуль param-' . $paramNumber . '.php не найден</p></div>';
                            }
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="btn-container">
                    <a href="meetings.php?id=<?php echo (int)$portraitId; ?>" class="btn btn-outline">Встречи с этим человеком</a>
                    <a href="index.php?id=<?php echo (int)$portraitId; ?>" class="btn btn-primary">Редактировать портрет</a>
                </div>
            </div>
        </div>

        <?php include COMPONENTS_PATH . '/footer.php'; ?>
    </div>

    <script src="scripts/main.js"></script>
</body>
</html>

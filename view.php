<?php
require_once __DIR__ . '/config/config.php';

$portraitId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$portrait = null;
$param1 = ['structured_data' => [], 'free_text' => ''];

if ($portraitId) {
    try {
        require_once __DIR__ . '/config/database.php';
        $db = getDB();
        $stmt = $db->prepare("SELECT id, status, created_at, updated_at FROM portraits WHERE id = :id");
        $stmt->execute([':id' => $portraitId]);
        $portrait = $stmt->fetch();
        if ($portrait) {
            $stmt = $db->prepare("SELECT structured_data, free_text FROM portrait_data WHERE portrait_id = :portrait_id AND param_number = 1");
            $stmt->execute([':portrait_id' => $portraitId]);
            $row = $stmt->fetch();
            if ($row) {
                $param1['structured_data'] = $row['structured_data'] ? json_decode($row['structured_data'], true) : [];
                $param1['free_text'] = $row['free_text'] ?: '';
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

$s = $param1['structured_data'];
$fio = isset($s['fio']) && $s['fio'] !== '' ? $s['fio'] : 'Без имени';
$birthDate = isset($s['birth_date']) && $s['birth_date'] !== '' ? $s['birth_date'] : null;
$age = isset($s['age']) && $s['age'] !== '' ? $s['age'] : null;
$gender = isset($s['gender']) ? $s['gender'] : '';
$genderLabels = ['male' => 'Мужской', 'female' => 'Женский', 'other' => 'Другое'];
$citizenship = isset($s['citizenship']) && $s['citizenship'] !== '' ? $s['citizenship'] : null;
$birthplace = isset($s['birthplace']) && $s['birthplace'] !== '' ? $s['birthplace'] : null;
$residence = isset($s['residence']) && $s['residence'] !== '' ? $s['residence'] : null;
$statusText = $portrait['status'] === 'completed' ? 'Завершён' : 'Черновик';
$createdAt = $portrait['created_at'] ? date('d.m.Y', strtotime($portrait['created_at'])) : '—';
$updatedAt = $portrait['updated_at'] ? date('d.m.Y', strtotime($portrait['updated_at'])) : '—';

function fmt($v) {
    return $v !== null && $v !== '' ? htmlspecialchars($v) : '—';
}
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

        <div class="person-card-page">
            <p class="form-back-link"><a href="list.php">← Вернуться к списку</a></p>

            <article class="person-card">
                <header class="person-card-header">
                    <h1 class="person-card-title"><?php echo htmlspecialchars($fio); ?></h1>
                    <span class="person-card-status person-card-status--<?php echo $portrait['status']; ?>"><?php echo htmlspecialchars($statusText); ?></span>
                </header>

                <div class="person-card-body">
                    <dl class="person-card-fields">
                        <div class="person-card-row">
                            <dt>Дата рождения</dt>
                            <dd><?php echo $birthDate ? date('d.m.Y', strtotime($birthDate)) : '—'; ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Возраст</dt>
                            <dd><?php echo fmt($age); ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Пол</dt>
                            <dd><?php echo isset($genderLabels[$gender]) ? $genderLabels[$gender] : '—'; ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Гражданство</dt>
                            <dd><?php echo fmt($citizenship); ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Место рождения</dt>
                            <dd><?php echo fmt($birthplace); ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Место проживания</dt>
                            <dd><?php echo fmt($residence); ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Создан</dt>
                            <dd><?php echo htmlspecialchars($createdAt); ?></dd>
                        </div>
                        <div class="person-card-row">
                            <dt>Обновлён</dt>
                            <dd><?php echo htmlspecialchars($updatedAt); ?></dd>
                        </div>
                    </dl>
                    <?php if (trim($param1['free_text']) !== ''): ?>
                    <div class="person-card-extra">
                        <h3 class="person-card-extra-title">Дополнительные детали</h3>
                        <div class="person-card-extra-text"><?php echo nl2br(htmlspecialchars($param1['free_text'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <footer class="person-card-footer">
                    <a href="index.php?id=<?php echo (int)$portraitId; ?>" class="btn btn-primary">Редактировать портрет</a>
                </footer>
            </article>
        </div>

        <?php include COMPONENTS_PATH . '/footer.php'; ?>
    </div>
</body>
</html>

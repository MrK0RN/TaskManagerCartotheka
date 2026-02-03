<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Список портретов — Портрет личности</title>
    <link rel="stylesheet" href="styles/common.css<?php echo asset_version('styles/common.css'); ?>">
    <link rel="stylesheet" href="styles/components.css<?php echo asset_version('styles/components.css'); ?>">
</head>
<body>
    <div class="container">
        <?php include COMPONENTS_PATH . '/header.php'; ?>

        <div class="list-page">
            <div class="list-toolbar">
                <h2 class="list-title">Список портретов</h2>
                <a href="index.php" class="btn btn-primary">Новый портрет</a>
            </div>

            <div class="list-filters">
                <div class="filter-group">
                    <label for="list-search">Поиск</label>
                    <input type="text" id="list-search" placeholder="ФИО или текст в анкете..." autocomplete="off">
                </div>
                <div class="filter-group">
                    <label for="list-status">Статус</label>
                    <select id="list-status">
                        <option value="">Все</option>
                        <option value="draft">Черновик</option>
                        <option value="completed">Завершён</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="list-date-from">Дата от</label>
                    <input type="date" id="list-date-from">
                </div>
                <div class="filter-group">
                    <label for="list-date-to">Дата до</label>
                    <input type="date" id="list-date-to">
                </div>
            </div>

            <div id="list-loading" class="list-loading">Загрузка…</div>
            <div id="list-error" class="list-error" style="display: none;"></div>
            <div id="list-table-wrap" class="list-table-wrap" style="display: none;">
                <table class="list-table">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Создан</th>
                            <th>Обновлён</th>
                            <th>Статус</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="list-tbody"></tbody>
                </table>
            </div>
            <div id="list-empty" class="list-empty" style="display: none;">Портретов пока нет. <a href="index.php">Создать первый</a>.</div>
        </div>

        <?php include COMPONENTS_PATH . '/footer.php'; ?>
    </div>

    <script src="scripts/list.js"></script>
</body>
</html>

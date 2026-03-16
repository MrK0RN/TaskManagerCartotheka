<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Задачи — Портрет личности</title>
    <link rel="stylesheet" href="styles/common.css<?php echo asset_version('styles/common.css'); ?>">
    <link rel="stylesheet" href="styles/components.css<?php echo asset_version('styles/components.css'); ?>">
</head>
<body>
    <div class="container">
        <?php include COMPONENTS_PATH . '/header.php'; ?>

        <div class="tasks-page">
            <h1 class="tasks-title">Менеджер задач</h1>

            <div class="tasks-toolbar">
                <button type="button" class="btn btn-primary" id="taskAddRootBtn">Добавить задачу</button>
            </div>

            <div id="tasks-loading" class="tasks-loading">Загрузка…</div>
            <div id="tasks-empty" class="tasks-empty" style="display: none;">Задач пока нет. Нажмите «Добавить задачу».</div>
            <div id="tasks-tree" class="tasks-tree" style="display: none;"></div>
        </div>

        <div id="taskModal" class="task-modal" style="display: none;">
            <div class="task-modal-backdrop"></div>
            <div class="task-modal-content">
                <h2 class="task-modal-title" id="taskModalTitle">Добавить задачу</h2>
                <form id="taskForm">
                    <input type="hidden" id="task_id" name="id" value="">
                    <input type="hidden" id="task_parent_id" name="parent_id" value="">
                    <div class="form-group">
                        <label for="task_title">Название *</label>
                        <input type="text" id="task_title" name="title" required placeholder="Название задачи">
                    </div>
                    <div class="form-group">
                        <label for="task_due_date">Дата</label>
                        <input type="date" id="task_due_date" name="due_date">
                    </div>
                    <div class="form-group">
                        <label for="task_assignee">Привязать человека</label>
                        <div class="task-assignee-wrap">
                            <input type="text" id="task_assignee" name="assignee_display" placeholder="Начните вводить ФИО..." autocomplete="off">
                            <input type="hidden" id="task_portrait_id" name="portrait_id" value="">
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary" id="taskSubmitBtn">Сохранить</button>
                        <button type="button" class="btn btn-outline" id="taskModalCancel">Отмена</button>
                    </div>
                </form>
            </div>
        </div>

        <?php include COMPONENTS_PATH . '/footer.php'; ?>
    </div>

    <script src="scripts/autocomplete.js"></script>
    <script src="scripts/tasks.js"></script>
</body>
</html>

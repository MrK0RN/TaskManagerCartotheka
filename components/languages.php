<?php
// Компонент для выбора языков с уровнем владения
// Принимает параметры: $languages (массив сохраненных языков), $paramName (имя поля)
$paramName = $paramName ?? 'languages';
$savedLanguages = $languages ?? [];
$languagesList = [];

// Получаем список языков из БД
try {
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();
    $stmt = $db->query("SELECT id, name, code FROM languages ORDER BY name");
    $languagesList = $stmt->fetchAll();
} catch (Exception $e) {
    // Если БД недоступна, используем базовый список
    $languagesList = [
        ['id' => 1, 'name' => 'Русский', 'code' => 'ru'],
        ['id' => 2, 'name' => 'Английский', 'code' => 'en'],
        ['id' => 3, 'name' => 'Немецкий', 'code' => 'de'],
        ['id' => 4, 'name' => 'Французский', 'code' => 'fr'],
        ['id' => 5, 'name' => 'Испанский', 'code' => 'es'],
    ];
}

$levels = [
    'A1' => 'A1 - Начальный',
    'A2' => 'A2 - Элементарный',
    'B1' => 'B1 - Средний',
    'B2' => 'B2 - Выше среднего',
    'C1' => 'C1 - Продвинутый',
    'C2' => 'C2 - Владение',
    'Native' => 'Родной'
];
?>

<div class="languages-component" data-param-name="<?php echo htmlspecialchars($paramName); ?>">
    <div class="languages-list" id="languages-list-<?php echo htmlspecialchars($paramName); ?>">
        <?php if (empty($savedLanguages)): ?>
            <div class="language-item" data-index="0">
                <div class="language-row">
                    <div class="language-select-wrapper">
                        <label>Язык:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][language_id]" class="language-select" data-autocomplete>
                            <option value="">-- Выберите язык --</option>
                            <?php foreach ($languagesList as $lang): ?>
                                <option value="<?php echo $lang['id']; ?>"><?php echo htmlspecialchars($lang['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="language-search" placeholder="Поиск языка..." autocomplete="off">
                        <div class="autocomplete-dropdown"></div>
                    </div>
                    <div class="level-select-wrapper">
                        <label>Уровень:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][level]" class="level-select">
                            <option value="">-- Выберите уровень --</option>
                            <?php foreach ($levels as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="button" class="btn-remove-language" onclick="removeLanguage(this)" style="display:none;">✕</button>
                </div>
                <div class="comment-wrapper">
                    <label>Комментарий:</label>
                    <textarea name="<?php echo htmlspecialchars($paramName); ?>[0][comment]" class="language-comment" placeholder="Дополнительная информация о владении языком..." rows="2"></textarea>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($savedLanguages as $index => $lang): ?>
                <div class="language-item" data-index="<?php echo $index; ?>">
                    <div class="language-row">
                        <div class="language-select-wrapper">
                            <label>Язык:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][language_id]" class="language-select" data-autocomplete>
                                <option value="">-- Выберите язык --</option>
                                <?php foreach ($languagesList as $langOption): ?>
                                    <option value="<?php echo $langOption['id']; ?>" <?php echo (isset($lang['language_id']) && $lang['language_id'] == $langOption['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($langOption['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="language-search" placeholder="Поиск языка..." autocomplete="off">
                            <div class="autocomplete-dropdown"></div>
                        </div>
                        <div class="level-select-wrapper">
                            <label>Уровень:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][level]" class="level-select">
                                <option value="">-- Выберите уровень --</option>
                                <?php foreach ($levels as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($lang['level']) && $lang['level'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="btn-remove-language" onclick="removeLanguage(this)">✕</button>
                    </div>
                    <div class="comment-wrapper">
                        <label>Комментарий:</label>
                        <textarea name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][comment]" class="language-comment" placeholder="Дополнительная информация о владении языком..." rows="2"><?php echo isset($lang['comment']) ? htmlspecialchars($lang['comment']) : ''; ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="btn-add-language" onclick="addLanguage('<?php echo htmlspecialchars($paramName); ?>')">+ Добавить язык</button>
</div>

<script>
// Функции для работы с языками будут в основном JS файле
</script>

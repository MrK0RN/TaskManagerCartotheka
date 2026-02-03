<?php
// Компонент для образовательного маршрута
// Принимает параметры: $educationItems (массив сохраненных записей), $paramName, $readOnly
$paramName = $paramName ?? 'education';
$savedEducation = $educationItems ?? [];
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';

$educationTypes = [
    'school' => 'Школа',
    'college' => 'Колледж',
    'university' => 'ВУЗ',
    'course' => 'Курсы',
    'self' => 'Самообразование'
];

function education_item_has_content($edu) {
    $fields = ['type', 'institution', 'specialization', 'year_from', 'year_to', 'details'];
    foreach ($fields as $f) {
        $v = isset($edu[$f]) ? $edu[$f] : '';
        if (trim((string) $v) !== '') return true;
    }
    return false;
}

// Получаем список учреждений из БД
$institutionsList = [];
try {
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();
    $stmt = $db->query("SELECT id, name, type FROM educational_institutions ORDER BY name");
    $institutionsList = $stmt->fetchAll();
} catch (Exception $e) {
    // Базовый список
    $institutionsList = [];
}
?>

<div class="education-component" data-param-name="<?php echo htmlspecialchars($paramName); ?>">
    <div class="education-list" id="education-list-<?php echo htmlspecialchars($paramName); ?>">
        <?php if (empty($savedEducation)): ?>
            <?php if (!$readOnly): ?>
            <div class="education-item" data-index="0">
                <div class="education-row">
                    <div class="education-type-wrapper">
                        <label>Тип:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][type]" class="education-type"<?php echo $roDis; ?>>
                            <option value="">-- Выберите тип --</option>
                            <?php foreach ($educationTypes as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="institution-wrapper">
                        <label>Учреждение:</label>
                        <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[0][institution]" class="institution-input" placeholder="Название учреждения" list="institutions-list-<?php echo htmlspecialchars($paramName); ?>-0"<?php echo $ro; ?>>
                        <datalist id="institutions-list-<?php echo htmlspecialchars($paramName); ?>-0">
                            <?php foreach ($institutionsList as $inst): ?>
                                <option value="<?php echo htmlspecialchars($inst['name']); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="specialization-wrapper">
                        <label>Специализация:</label>
                        <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[0][specialization]" class="specialization-input" placeholder="Специализация/направление"<?php echo $ro; ?>>
                    </div>
                    <button type="button" class="btn-remove-education" onclick="removeEducation(this)" style="display:none;">✕</button>
                </div>
                <div class="education-dates">
                    <div class="date-from">
                        <label>Год начала:</label>
                        <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[0][year_from]" class="year-input" placeholder="ГГГГ" min="1950" max="<?php echo date('Y'); ?>"<?php echo $ro; ?>>
                    </div>
                    <div class="date-to">
                        <label>Год окончания:</label>
                        <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[0][year_to]" class="year-input" placeholder="ГГГГ" min="1950" max="<?php echo date('Y') + 10; ?>"<?php echo $ro; ?>>
                    </div>
                </div>
                <div class="education-details">
                    <label>Дополнительные детали:</label>
                    <textarea name="<?php echo htmlspecialchars($paramName); ?>[0][details]" class="education-details-textarea" placeholder="Дополнительная информация об образовании..." rows="2"<?php echo $ro; ?>></textarea>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <?php foreach ($savedEducation as $index => $edu): ?>
                <?php if ($readOnly && !education_item_has_content($edu)) continue; ?>
                <div class="education-item" data-index="<?php echo $index; ?>">
                    <div class="education-row">
                        <div class="education-type-wrapper">
                            <label>Тип:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][type]" class="education-type"<?php echo $roDis; ?>>
                                <option value="">-- Выберите тип --</option>
                                <?php foreach ($educationTypes as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($edu['type']) && $edu['type'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="institution-wrapper">
                            <label>Учреждение:</label>
                            <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][institution]" class="institution-input" value="<?php echo isset($edu['institution']) ? htmlspecialchars($edu['institution']) : ''; ?>" placeholder="Название учреждения" list="institutions-list-<?php echo htmlspecialchars($paramName); ?>-<?php echo $index; ?>"<?php echo $ro; ?>>
                            <datalist id="institutions-list-<?php echo htmlspecialchars($paramName); ?>-<?php echo $index; ?>">
                                <?php foreach ($institutionsList as $inst): ?>
                                    <option value="<?php echo htmlspecialchars($inst['name']); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="specialization-wrapper">
                            <label>Специализация:</label>
                            <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][specialization]" class="specialization-input" value="<?php echo isset($edu['specialization']) ? htmlspecialchars($edu['specialization']) : ''; ?>" placeholder="Специализация/направление"<?php echo $ro; ?>>
                        </div>
                        <?php if (!$readOnly): ?><button type="button" class="btn-remove-education" onclick="removeEducation(this)">✕</button><?php endif; ?>
                    </div>
                    <div class="education-dates">
                        <div class="date-from">
                            <label>Год начала:</label>
                            <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][year_from]" class="year-input" value="<?php echo isset($edu['year_from']) ? htmlspecialchars($edu['year_from']) : ''; ?>" placeholder="ГГГГ" min="1950" max="<?php echo date('Y'); ?>"<?php echo $ro; ?>>
                        </div>
                        <div class="date-to">
                            <label>Год окончания:</label>
                            <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][year_to]" class="year-input" value="<?php echo isset($edu['year_to']) ? htmlspecialchars($edu['year_to']) : ''; ?>" placeholder="ГГГГ" min="1950" max="<?php echo date('Y') + 10; ?>"<?php echo $ro; ?>>
                        </div>
                    </div>
                    <?php if (!$readOnly || trim(isset($edu['details']) ? $edu['details'] : '') !== ''): ?>
                    <div class="education-details">
                        <label>Дополнительные детали:</label>
                        <textarea name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][details]" class="education-details-textarea" placeholder="Дополнительная информация об образовании..." rows="2"<?php echo $ro; ?>><?php echo isset($edu['details']) ? htmlspecialchars($edu['details']) : ''; ?></textarea>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (!$readOnly): ?><button type="button" class="btn-add-education" onclick="addEducation('<?php echo htmlspecialchars($paramName); ?>')">+ Добавить образование</button><?php endif; ?>
</div>

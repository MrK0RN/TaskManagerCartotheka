<?php
// Компонент для образовательного маршрута
// Принимает параметры: $educationItems (массив сохраненных записей), $paramName
$paramName = $paramName ?? 'education';
$savedEducation = $educationItems ?? [];

$educationTypes = [
    'school' => 'Школа',
    'college' => 'Колледж',
    'university' => 'ВУЗ',
    'course' => 'Курсы',
    'self' => 'Самообразование'
];

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
            <div class="education-item" data-index="0">
                <div class="education-row">
                    <div class="education-type-wrapper">
                        <label>Тип:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][type]" class="education-type">
                            <option value="">-- Выберите тип --</option>
                            <?php foreach ($educationTypes as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="institution-wrapper">
                        <label>Учреждение:</label>
                        <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[0][institution]" class="institution-input" placeholder="Название учреждения" list="institutions-list-<?php echo htmlspecialchars($paramName); ?>-0">
                        <datalist id="institutions-list-<?php echo htmlspecialchars($paramName); ?>-0">
                            <?php foreach ($institutionsList as $inst): ?>
                                <option value="<?php echo htmlspecialchars($inst['name']); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="specialization-wrapper">
                        <label>Специализация:</label>
                        <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[0][specialization]" class="specialization-input" placeholder="Специализация/направление">
                    </div>
                    <button type="button" class="btn-remove-education" onclick="removeEducation(this)" style="display:none;">✕</button>
                </div>
                <div class="education-dates">
                    <div class="date-from">
                        <label>Год начала:</label>
                        <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[0][year_from]" class="year-input" placeholder="ГГГГ" min="1950" max="<?php echo date('Y'); ?>">
                    </div>
                    <div class="date-to">
                        <label>Год окончания:</label>
                        <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[0][year_to]" class="year-input" placeholder="ГГГГ" min="1950" max="<?php echo date('Y') + 10; ?>">
                    </div>
                </div>
                <div class="education-details">
                    <label>Дополнительные детали:</label>
                    <textarea name="<?php echo htmlspecialchars($paramName); ?>[0][details]" class="education-details-textarea" placeholder="Дополнительная информация об образовании..." rows="2"></textarea>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($savedEducation as $index => $edu): ?>
                <div class="education-item" data-index="<?php echo $index; ?>">
                    <div class="education-row">
                        <div class="education-type-wrapper">
                            <label>Тип:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][type]" class="education-type">
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
                            <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][institution]" class="institution-input" value="<?php echo isset($edu['institution']) ? htmlspecialchars($edu['institution']) : ''; ?>" placeholder="Название учреждения" list="institutions-list-<?php echo htmlspecialchars($paramName); ?>-<?php echo $index; ?>">
                            <datalist id="institutions-list-<?php echo htmlspecialchars($paramName); ?>-<?php echo $index; ?>">
                                <?php foreach ($institutionsList as $inst): ?>
                                    <option value="<?php echo htmlspecialchars($inst['name']); ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="specialization-wrapper">
                            <label>Специализация:</label>
                            <input type="text" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][specialization]" class="specialization-input" value="<?php echo isset($edu['specialization']) ? htmlspecialchars($edu['specialization']) : ''; ?>" placeholder="Специализация/направление">
                        </div>
                        <button type="button" class="btn-remove-education" onclick="removeEducation(this)">✕</button>
                    </div>
                    <div class="education-dates">
                        <div class="date-from">
                            <label>Год начала:</label>
                            <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][year_from]" class="year-input" value="<?php echo isset($edu['year_from']) ? htmlspecialchars($edu['year_from']) : ''; ?>" placeholder="ГГГГ" min="1950" max="<?php echo date('Y'); ?>">
                        </div>
                        <div class="date-to">
                            <label>Год окончания:</label>
                            <input type="number" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][year_to]" class="year-input" value="<?php echo isset($edu['year_to']) ? htmlspecialchars($edu['year_to']) : ''; ?>" placeholder="ГГГГ" min="1950" max="<?php echo date('Y') + 10; ?>">
                        </div>
                    </div>
                    <div class="education-details">
                        <label>Дополнительные детали:</label>
                        <textarea name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][details]" class="education-details-textarea" placeholder="Дополнительная информация об образовании..." rows="2"><?php echo isset($edu['details']) ? htmlspecialchars($edu['details']) : ''; ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="btn-add-education" onclick="addEducation('<?php echo htmlspecialchars($paramName); ?>')">+ Добавить образование</button>
</div>

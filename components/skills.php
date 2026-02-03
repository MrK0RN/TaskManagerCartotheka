<?php
// Компонент для навыков (Hard/Soft skills)
// Принимает параметры: $skills (массив сохраненных навыков), $paramName, $readOnly
$paramName = $paramName ?? 'skills';
$savedSkills = $skills ?? [];
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';

$skillCategories = [
    'hard' => 'Hard skills',
    'soft' => 'Soft skills'
];

$skillLevels = [
    'expert' => 'Эксперт',
    'advanced' => 'Продвинутый',
    'intermediate' => 'Средний',
    'basic' => 'Базовый'
];

// Получаем список навыков из БД
$skillsList = [];
try {
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();
    $stmt = $db->query("SELECT id, name, category FROM skills ORDER BY category, name");
    $skillsList = $stmt->fetchAll();
} catch (Exception $e) {
    // Базовый список
    $skillsList = [];
}

// Группируем по категориям
$skillsByCategory = ['hard' => [], 'soft' => []];
foreach ($skillsList as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}
?>

<div class="skills-component" data-param-name="<?php echo htmlspecialchars($paramName); ?>">
    <div class="skills-list" id="skills-list-<?php echo htmlspecialchars($paramName); ?>">
        <?php if (empty($savedSkills)): ?>
            <div class="skill-item" data-index="0">
                <div class="skill-row">
                    <div class="skill-category-wrapper">
                        <label>Категория:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][category]" class="skill-category" onchange="updateSkillOptions(this)"<?php echo $roDis; ?>>
                            <option value="">-- Выберите категорию --</option>
                            <?php foreach ($skillCategories as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="skill-name-wrapper">
                        <label>Навык:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][skill_id]" class="skill-select" data-category=""<?php echo $roDis; ?>>
                            <option value="">-- Сначала выберите категорию --</option>
                        </select>
                        <input type="text" class="skill-custom-input" name="<?php echo htmlspecialchars($paramName); ?>[0][skill_custom]" placeholder="Или введите свой навык" style="display:none;"<?php echo $ro; ?>>
                    </div>
                    <div class="skill-level-wrapper">
                        <label>Уровень:</label>
                        <select name="<?php echo htmlspecialchars($paramName); ?>[0][level]" class="skill-level"<?php echo $roDis; ?>>
                            <option value="">-- Выберите уровень --</option>
                            <?php foreach ($skillLevels as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (!$readOnly): ?><button type="button" class="btn-remove-skill" onclick="removeSkill(this)" style="display:none;">✕</button><?php endif; ?>
                </div>
                <div class="skill-comment-wrapper">
                    <label>Комментарий:</label>
                    <textarea name="<?php echo htmlspecialchars($paramName); ?>[0][comment]" class="skill-comment" placeholder="Дополнительная информация о навыке..." rows="2"<?php echo $ro; ?>></textarea>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($savedSkills as $index => $skill): ?>
                <div class="skill-item" data-index="<?php echo $index; ?>">
                    <div class="skill-row">
                        <div class="skill-category-wrapper">
                            <label>Категория:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][category]" class="skill-category" onchange="updateSkillOptions(this)"<?php echo $roDis; ?>>
                                <option value="">-- Выберите категорию --</option>
                                <?php foreach ($skillCategories as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($skill['category']) && $skill['category'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="skill-name-wrapper">
                            <label>Навык:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][skill_id]" class="skill-select" data-category="<?php echo isset($skill['category']) ? htmlspecialchars($skill['category']) : ''; ?>"<?php echo $roDis; ?>>
                                <option value="">-- Выберите навык --</option>
                                <?php 
                                $currentCategory = isset($skill['category']) ? $skill['category'] : '';
                                if ($currentCategory && isset($skillsByCategory[$currentCategory])): 
                                    foreach ($skillsByCategory[$currentCategory] as $skillOption): 
                                ?>
                                    <option value="<?php echo $skillOption['id']; ?>" <?php echo (isset($skill['skill_id']) && $skill['skill_id'] == $skillOption['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($skillOption['name']); ?>
                                    </option>
                                <?php 
                                    endforeach;
                                endif; 
                                ?>
                            </select>
                            <input type="text" class="skill-custom-input" name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][skill_custom]" value="<?php echo isset($skill['skill_custom']) ? htmlspecialchars($skill['skill_custom']) : ''; ?>" placeholder="Или введите свой навык" style="<?php echo (isset($skill['skill_custom']) && !empty($skill['skill_custom'])) ? '' : 'display:none;'; ?>"<?php echo $ro; ?>>
                        </div>
                        <div class="skill-level-wrapper">
                            <label>Уровень:</label>
                            <select name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][level]" class="skill-level"<?php echo $roDis; ?>>
                                <option value="">-- Выберите уровень --</option>
                                <?php foreach ($skillLevels as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo (isset($skill['level']) && $skill['level'] == $key) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if (!$readOnly): ?><button type="button" class="btn-remove-skill" onclick="removeSkill(this)">✕</button><?php endif; ?>
                    </div>
                    <div class="skill-comment-wrapper">
                        <label>Комментарий:</label>
                        <textarea name="<?php echo htmlspecialchars($paramName); ?>[<?php echo $index; ?>][comment]" class="skill-comment" placeholder="Дополнительная информация о навыке..." rows="2"<?php echo $ro; ?>><?php echo isset($skill['comment']) ? htmlspecialchars($skill['comment']) : ''; ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if (!$readOnly): ?><button type="button" class="btn-add-skill" onclick="addSkill('<?php echo htmlspecialchars($paramName); ?>')">+ Добавить навык</button><?php endif; ?>
</div>

<script>
// Данные навыков для JS
window.skillsData = <?php echo json_encode($skillsByCategory, JSON_UNESCAPED_UNICODE); ?>;
</script>

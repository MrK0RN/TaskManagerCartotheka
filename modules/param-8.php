<?php
// Параметр 8: Интеллектуальный и творческий капитал
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$languages = isset($structured['languages']) ? $structured['languages'] : [];
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_8"><span class="param-name">8. Интеллектуальный и творческий капитал</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Включите:</strong>
        <ul>
            <li>Уникальные знания и экспертиза</li>
            <li>Патенты, авторские права, интеллектуальная собственность</li>
            <li>Творческие работы (книги, музыка, искусство)</li>
            <li>Знание языков (уровень владения)</li>
            <li>Когнитивные способности (аналитическое мышление, память, скорость обучения)</li>
        </ul>
        <strong>Важно:</strong> не только то, что есть, но и то, как это используется. Пишет ли человек статьи? Преподаёт? Создаёт что-то новое?
    </div>
    <?php endif; ?>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_8_has_patents" value="1" <?php echo (isset($structured['has_patents']) && $structured['has_patents']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Есть патенты/авторские права
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_8_has_creative_works" value="1" <?php echo (isset($structured['has_creative_works']) && $structured['has_creative_works']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Есть творческие работы
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_8_teaches" value="1" <?php echo (isset($structured['teaches']) && $structured['teaches']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Преподаёт/обучает
                </label>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 15px;">
        <label><strong>Знание языков:</strong></label>
        <?php
        $paramName = 'param_8_languages';
        include COMPONENTS_PATH . '/languages.php';
        ?>
    </div>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_8_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_8_free_text" id="param_8_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>

<?php
// Параметр 4: Профессиональная траектория
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_4"><span class="param-name">4. Профессиональная траектория</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Опишите:</strong>
        <ul>
            <li>Текущая профессия и должность</li>
            <li>Все значимые места работы (с датами)</li>
            <li>Карьерные переходы и их причины</li>
            <li>Общий стаж и стаж по специальности</li>
            <li>Отношение к работе (призвание / средство к существованию)</li>
        </ul>
        <strong>Важные вопросы:</strong> почему выбрал эту профессию? Были ли неудачи на пути? Что ценит в работе больше всего (деньги, статус, творчество, стабильность)?
    </div>
    <?php endif; ?>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Текущая профессия:</label>
                <input type="text" name="param_4_profession" id="param_4_profession" value="<?php echo htmlspecialchars($structured['profession'] ?? ''); ?>" placeholder="Профессия" class="profession-input" data-autocomplete data-autocomplete-type="profession"<?php echo $ro; ?>>
            </div>
            <div class="field-group">
                <label>Должность:</label>
                <input type="text" name="param_4_position" id="param_4_position" value="<?php echo htmlspecialchars($structured['position'] ?? ''); ?>" placeholder="Должность"<?php echo $ro; ?>>
            </div>
            <div class="field-group">
                <label>Отрасль:</label>
                <select name="param_4_industry" id="param_4_industry"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="IT" <?php echo (isset($structured['industry']) && $structured['industry'] == 'IT') ? 'selected' : ''; ?>>IT</option>
                    <option value="Finance" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Finance') ? 'selected' : ''; ?>>Финансы</option>
                    <option value="Healthcare" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Healthcare') ? 'selected' : ''; ?>>Здравоохранение</option>
                    <option value="Education" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Education') ? 'selected' : ''; ?>>Образование</option>
                    <option value="Creative" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Creative') ? 'selected' : ''; ?>>Творчество</option>
                    <option value="Management" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Management') ? 'selected' : ''; ?>>Управление</option>
                    <option value="Other" <?php echo (isset($structured['industry']) && $structured['industry'] == 'Other') ? 'selected' : ''; ?>>Другое</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Общий стаж (лет):</label>
                <input type="number" name="param_4_total_experience" id="param_4_total_experience" value="<?php echo htmlspecialchars($structured['total_experience'] ?? ''); ?>" placeholder="0" min="0" max="100"<?php echo $ro; ?>>
            </div>
            <div class="field-group">
                <label>Стаж по специальности (лет):</label>
                <input type="number" name="param_4_specialty_experience" id="param_4_specialty_experience" value="<?php echo htmlspecialchars($structured['specialty_experience'] ?? ''); ?>" placeholder="0" min="0" max="100"<?php echo $ro; ?>>
            </div>
        </div>
    </div>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_4_free_text" style="margin-top: 15px;">Описание карьерной траектории:</label>
    <textarea name="param_4_free_text" id="param_4_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>

<?php
// Параметр 18: Жизненные цели и смысложизненные ориентиры
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_18"><span class="param-name">18. Жизненные цели и смысложизненные ориентиры</span></label>
    <div class="help-text">
        <strong>Структурируйте по временным горизонтам:</strong>
        <ul>
            <li><strong>Краткосрочные цели (1-2 года):</strong> что хочет достичь в ближайшее время?</li>
            <li><strong>Среднесрочные цели (3-7 лет):</strong> планы на среднюю перспективу</li>
            <li><strong>Долгосрочные цели (10+ лет):</strong> мечты и большие жизненные проекты</li>
            <li><strong>Источник смысла:</strong> что делает жизнь значимой? (любовь, творчество, служение, познание, саморазвитие)</li>
        </ul>
        <br>
        <strong>Глубокие вопросы:</strong>
        <ul>
            <li>Если бы все было возможно — чего бы человек хотел достичь в жизни?</li>
            <li>Что бы хотел оставить после себя?</li>
            <li>Ради чего готов трудиться, несмотря на трудности?</li>
            <li>Есть ли ощущение «призвания» или «миссии»?</li>
        </ul>
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Источник смысла:</label>
                <select name="param_18_meaning_source" id="param_18_meaning_source"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="love" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'love') ? 'selected' : ''; ?>>Любовь</option>
                    <option value="creativity" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'creativity') ? 'selected' : ''; ?>>Творчество</option>
                    <option value="service" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'service') ? 'selected' : ''; ?>>Служение</option>
                    <option value="knowledge" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'knowledge') ? 'selected' : ''; ?>>Познание</option>
                    <option value="self_development" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'self_development') ? 'selected' : ''; ?>>Саморазвитие</option>
                    <option value="other" <?php echo (isset($structured['meaning_source']) && $structured['meaning_source'] == 'other') ? 'selected' : ''; ?>>Другое</option>
                </select>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_18_has_vocation" value="1" <?php echo (isset($structured['has_vocation']) && $structured['has_vocation']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Есть ощущение призвания/миссии
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_18_free_text" style="margin-top: 15px;">Цели и ориентиры:</label>
    <textarea name="param_18_free_text" id="param_18_free_text" class="xlarge"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

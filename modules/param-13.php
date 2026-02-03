<?php
// Параметр 13: Когнитивный стиль
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_13"><span class="param-name">13. Когнитивный стиль</span></label>
    <div class="help-text">
        <strong>Проанализируйте:</strong>
        <ul>
            <li><strong>Тип мышления:</strong> аналитический (разбирает по частям) или синтетический (видит целое); интуитивный (ощущения) или рациональный (логика)</li>
            <li><strong>Визуализация:</strong> визуальный (образы), аудиальный (звуки), кинестетик (ощущения) тип восприятия</li>
            <li><strong>Скорость принятия решений:</strong> быстро (импульсивно) или медленно (взвешенно)</li>
            <li><strong>Глубина обработки:</strong> поверхностно или глубоко анализирует информацию</li>
            <li><strong>Склонность к рефлексии:</strong> часто ли размышляет о себе и своих поступках</li>
        </ul>
        <strong>Пример:</strong> «Визуал, предпочитает схемы и картинки. Решения принимает медленно, но взвешенно. Любит глубокие размышления о смысле жизни».
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Тип мышления:</label>
                <select name="param_13_thinking_type" id="param_13_thinking_type"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="analytical" <?php echo (isset($structured['thinking_type']) && $structured['thinking_type'] == 'analytical') ? 'selected' : ''; ?>>Аналитический</option>
                    <option value="synthetic" <?php echo (isset($structured['thinking_type']) && $structured['thinking_type'] == 'synthetic') ? 'selected' : ''; ?>>Синтетический</option>
                    <option value="intuitive" <?php echo (isset($structured['thinking_type']) && $structured['thinking_type'] == 'intuitive') ? 'selected' : ''; ?>>Интуитивный</option>
                    <option value="rational" <?php echo (isset($structured['thinking_type']) && $structured['thinking_type'] == 'rational') ? 'selected' : ''; ?>>Рациональный</option>
                    <option value="mixed" <?php echo (isset($structured['thinking_type']) && $structured['thinking_type'] == 'mixed') ? 'selected' : ''; ?>>Смешанный</option>
                </select>
            </div>
            <div class="field-group">
                <label>Тип восприятия:</label>
                <select name="param_13_perception" id="param_13_perception"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="visual" <?php echo (isset($structured['perception']) && $structured['perception'] == 'visual') ? 'selected' : ''; ?>>Визуальный</option>
                    <option value="auditory" <?php echo (isset($structured['perception']) && $structured['perception'] == 'auditory') ? 'selected' : ''; ?>>Аудиальный</option>
                    <option value="kinesthetic" <?php echo (isset($structured['perception']) && $structured['perception'] == 'kinesthetic') ? 'selected' : ''; ?>>Кинестетик</option>
                    <option value="mixed" <?php echo (isset($structured['perception']) && $structured['perception'] == 'mixed') ? 'selected' : ''; ?>>Смешанный</option>
                </select>
            </div>
            <div class="field-group">
                <label>Скорость принятия решений:</label>
                <select name="param_13_decision_speed" id="param_13_decision_speed"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="very_fast" <?php echo (isset($structured['decision_speed']) && $structured['decision_speed'] == 'very_fast') ? 'selected' : ''; ?>>Очень быстро</option>
                    <option value="fast" <?php echo (isset($structured['decision_speed']) && $structured['decision_speed'] == 'fast') ? 'selected' : ''; ?>>Быстро</option>
                    <option value="medium" <?php echo (isset($structured['decision_speed']) && $structured['decision_speed'] == 'medium') ? 'selected' : ''; ?>>Средне</option>
                    <option value="slow" <?php echo (isset($structured['decision_speed']) && $structured['decision_speed'] == 'slow') ? 'selected' : ''; ?>>Медленно</option>
                </select>
            </div>
        </div>
    </div>
    
    <label for="param_13_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_13_free_text" id="param_13_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

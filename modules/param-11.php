<?php
// Параметр 11: Психологические ресурсы
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_11"><span class="param-name">11. Психологические ресурсы</span></label>
    <div class="help-text">
        <strong>Ключевые компоненты:</strong>
        <ul>
            <li><strong>Стрессоустойчивость:</strong> как справляется с давлением</li>
            <li><strong>Эмоциональный интеллект:</strong> понимание своих и чужих эмоций</li>
            <li><strong>Оптимизм/пессимизм:</strong> базовое ожидание от будущего</li>
            <li><strong>Внутренняя опора:</strong> на что опирается в трудностях (вера, ценности, люди)</li>
            <li><strong>Гибкость:</strong> способность адаптироваться к изменениям</li>
            <li><strong>Самосострадание:</strong> как относится к своим ошибкам</li>
        </ul>
        <strong>Вопрос:</strong> что помогает человеку восстанавливаться после неудач?
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Стрессоустойчивость:</label>
                <select name="param_11_stress_resistance" id="param_11_stress_resistance">
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['stress_resistance']) && $structured['stress_resistance'] == 'very_high') ? 'selected' : ''; ?>>Очень высокая</option>
                    <option value="high" <?php echo (isset($structured['stress_resistance']) && $structured['stress_resistance'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['stress_resistance']) && $structured['stress_resistance'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['stress_resistance']) && $structured['stress_resistance'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
            <div class="field-group">
                <label>Эмоциональный интеллект:</label>
                <select name="param_11_eq" id="param_11_eq">
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['eq']) && $structured['eq'] == 'very_high') ? 'selected' : ''; ?>>Очень высокий</option>
                    <option value="high" <?php echo (isset($structured['eq']) && $structured['eq'] == 'high') ? 'selected' : ''; ?>>Высокий</option>
                    <option value="medium" <?php echo (isset($structured['eq']) && $structured['eq'] == 'medium') ? 'selected' : ''; ?>>Средний</option>
                    <option value="low" <?php echo (isset($structured['eq']) && $structured['eq'] == 'low') ? 'selected' : ''; ?>>Низкий</option>
                </select>
            </div>
            <div class="field-group">
                <label>Оптимизм/Пессимизм:</label>
                <select name="param_11_outlook" id="param_11_outlook">
                    <option value="">-- Выберите --</option>
                    <option value="very_optimistic" <?php echo (isset($structured['outlook']) && $structured['outlook'] == 'very_optimistic') ? 'selected' : ''; ?>>Очень оптимистичный</option>
                    <option value="optimistic" <?php echo (isset($structured['outlook']) && $structured['outlook'] == 'optimistic') ? 'selected' : ''; ?>>Оптимистичный</option>
                    <option value="balanced" <?php echo (isset($structured['outlook']) && $structured['outlook'] == 'balanced') ? 'selected' : ''; ?>>Сбалансированный</option>
                    <option value="pessimistic" <?php echo (isset($structured['outlook']) && $structured['outlook'] == 'pessimistic') ? 'selected' : ''; ?>>Пессимистичный</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Гибкость:</label>
                <select name="param_11_flexibility" id="param_11_flexibility">
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['flexibility']) && $structured['flexibility'] == 'very_high') ? 'selected' : ''; ?>>Очень высокая</option>
                    <option value="high" <?php echo (isset($structured['flexibility']) && $structured['flexibility'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['flexibility']) && $structured['flexibility'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['flexibility']) && $structured['flexibility'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
        </div>
    </div>
    
    <label for="param_11_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_11_free_text" id="param_11_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

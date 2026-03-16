<?php
// Параметр 10: Физиологические ресурсы
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_10"><span class="param-name">10. Физиологические ресурсы</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Оцените объективно:</strong>
        <ul>
            <li>Общее состояние здоровья (отличное/хорошее/удовлетворительное/плохое)</li>
            <li>Хронические заболевания (если есть)</li>
            <li>Энергетический уровень в течение дня</li>
            <li>Выносливость (физическая и умственная)</li>
            <li>Сон, питание, физическая активность</li>
            <li>Телесные ограничения или, наоборот, возможности</li>
        </ul>
        <strong>Важно:</strong> как здоровье влияет на качество жизни и возможности? Есть ли ресурсы для улучшения (спортзал, врач, время)?
    </div>
    <?php endif; ?>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Общее состояние здоровья:</label>
                <select name="param_10_health_status" id="param_10_health_status"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="excellent" <?php echo (isset($structured['health_status']) && $structured['health_status'] == 'excellent') ? 'selected' : ''; ?>>Отличное</option>
                    <option value="good" <?php echo (isset($structured['health_status']) && $structured['health_status'] == 'good') ? 'selected' : ''; ?>>Хорошее</option>
                    <option value="satisfactory" <?php echo (isset($structured['health_status']) && $structured['health_status'] == 'satisfactory') ? 'selected' : ''; ?>>Удовлетворительное</option>
                    <option value="poor" <?php echo (isset($structured['health_status']) && $structured['health_status'] == 'poor') ? 'selected' : ''; ?>>Плохое</option>
                </select>
            </div>
            <div class="field-group">
                <label>Энергетический уровень:</label>
                <select name="param_10_energy_level" id="param_10_energy_level"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['energy_level']) && $structured['energy_level'] == 'very_high') ? 'selected' : ''; ?>>Очень высокий</option>
                    <option value="high" <?php echo (isset($structured['energy_level']) && $structured['energy_level'] == 'high') ? 'selected' : ''; ?>>Высокий</option>
                    <option value="medium" <?php echo (isset($structured['energy_level']) && $structured['energy_level'] == 'medium') ? 'selected' : ''; ?>>Средний</option>
                    <option value="low" <?php echo (isset($structured['energy_level']) && $structured['energy_level'] == 'low') ? 'selected' : ''; ?>>Низкий</option>
                </select>
            </div>
            <div class="field-group">
                <label>Выносливость:</label>
                <select name="param_10_endurance" id="param_10_endurance"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['endurance']) && $structured['endurance'] == 'very_high') ? 'selected' : ''; ?>>Очень высокая</option>
                    <option value="high" <?php echo (isset($structured['endurance']) && $structured['endurance'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['endurance']) && $structured['endurance'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['endurance']) && $structured['endurance'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_10_has_chronic" value="1" <?php echo (isset($structured['has_chronic']) && $structured['has_chronic']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Есть хронические заболевания
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_10_regular_sport" value="1" <?php echo (isset($structured['regular_sport']) && $structured['regular_sport']) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Регулярные занятия спортом
                </label>
            </div>
        </div>
    </div>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_10_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_10_free_text" id="param_10_free_text"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>

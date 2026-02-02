<?php
// Параметр 5: Текущие материальные ресурсы
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_5"><span class="param-name">5. Текущие материальные ресурсы</span></label>
    <div class="help-text">
        <strong>Что оценить:</strong>
        <ul>
            <li>Ежемесячный доход (основной и дополнительный)</li>
            <li>Сбережения и накопления</li>
            <li>Недвижимость, транспорт, другое имущество</li>
            <li>Финансовые обязательства (кредиты, ипотека)</li>
            <li>Уровень финансовой стабильности (высокий/средний/низкий)</li>
        </ul>
        <strong>Формулировка:</strong> не указывайте точные суммы, если это чувствительно. Используйте категории: «выше среднего», «обеспечен комфортный уровень жизни», «живёт от зарплаты к зарплате».
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Уровень дохода:</label>
                <select name="param_5_income_level" id="param_5_income_level">
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'very_high') ? 'selected' : ''; ?>>Очень высокий</option>
                    <option value="high" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'high') ? 'selected' : ''; ?>>Высокий</option>
                    <option value="above_average" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'above_average') ? 'selected' : ''; ?>>Выше среднего</option>
                    <option value="average" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'average') ? 'selected' : ''; ?>>Средний</option>
                    <option value="below_average" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'below_average') ? 'selected' : ''; ?>>Ниже среднего</option>
                    <option value="low" <?php echo (isset($structured['income_level']) && $structured['income_level'] == 'low') ? 'selected' : ''; ?>>Низкий</option>
                </select>
            </div>
            <div class="field-group">
                <label>Уровень финансовой стабильности:</label>
                <select name="param_5_stability" id="param_5_stability">
                    <option value="">-- Выберите --</option>
                    <option value="high" <?php echo (isset($structured['stability']) && $structured['stability'] == 'high') ? 'selected' : ''; ?>>Высокий</option>
                    <option value="medium" <?php echo (isset($structured['stability']) && $structured['stability'] == 'medium') ? 'selected' : ''; ?>>Средний</option>
                    <option value="low" <?php echo (isset($structured['stability']) && $structured['stability'] == 'low') ? 'selected' : ''; ?>>Низкий</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_5_has_property" value="1" <?php echo (isset($structured['has_property']) && $structured['has_property']) ? 'checked' : ''; ?>>
                    Есть недвижимость
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_5_has_transport" value="1" <?php echo (isset($structured['has_transport']) && $structured['has_transport']) ? 'checked' : ''; ?>>
                    Есть транспорт
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_5_has_loans" value="1" <?php echo (isset($structured['has_loans']) && $structured['has_loans']) ? 'checked' : ''; ?>>
                    Есть кредиты/ипотека
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_5_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_5_free_text" id="param_5_free_text"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

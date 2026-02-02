<?php
// Параметр 15: Мотивационная структура
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$motivations = isset($structured['motivations']) ? $structured['motivations'] : [];
?>
<div class="form-group">
    <label for="param_15"><span class="param-name">15. Мотивационная структура</span></label>
    <div class="help-text">
        <strong>Определите ведущие драйверы (выберите 3-5 главных):</strong>
        <br><br>
        <strong>Дополнительно опишите:</strong>
        <ul>
            <li>Что мотивирует больше всего — внутренние или внешние факторы?</li>
            <li>Есть ли «демотиваторы» — то, что отбивает желание действовать?</li>
            <li>Как изменились мотивы с возрастом?</li>
        </ul>
    </div>
    
    <div class="structured-fields">
        <div class="options-grid">
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="autonomy" <?php echo in_array('autonomy', $motivations) ? 'checked' : ''; ?>>
                    Автономия / свобода
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="recognition" <?php echo in_array('recognition', $motivations) ? 'checked' : ''; ?>>
                    Признание / статус
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="security" <?php echo in_array('security', $motivations) ? 'checked' : ''; ?>>
                    Безопасность / стабильность
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="mastery" <?php echo in_array('mastery', $motivations) ? 'checked' : ''; ?>>
                    Мастерство / рост
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="connection" <?php echo in_array('connection', $motivations) ? 'checked' : ''; ?>>
                    Связь / отношения
                </label>
            </div>
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="power" <?php echo in_array('power', $motivations) ? 'checked' : ''; ?>>
                    Власть / влияние
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="creativity" <?php echo in_array('creativity', $motivations) ? 'checked' : ''; ?>>
                    Творчество / самовыражение
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="altruism" <?php echo in_array('altruism', $motivations) ? 'checked' : ''; ?>>
                    Помощь другим / альтруизм
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="money" <?php echo in_array('money', $motivations) ? 'checked' : ''; ?>>
                    Деньги / материальные блага
                </label>
                <label>
                    <input type="checkbox" name="param_15_motivations[]" value="health" <?php echo in_array('health', $motivations) ? 'checked' : ''; ?>>
                    Здоровье / благополучие
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_15_free_text" style="margin-top: 15px;">Дополнительный анализ:</label>
    <textarea name="param_15_free_text" id="param_15_free_text" placeholder="Ведущие мотивы: автономия, мастерство, творчество&#10;&#10;Дополнительный анализ..."><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

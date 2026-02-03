<?php
// Параметр 16: Базовые ценности и приоритеты
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$values = isset($structured['values']) ? $structured['values'] : [];
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_16"><span class="param-name">16. Базовые ценности и приоритеты</span></label>
    <div class="help-text">
        <strong>Составьте иерархию (от самого важного):</strong>
        <br><br>
        <strong>Важные вопросы:</strong>
        <ul>
            <li>Ради чего человек готов пожертвовать чем-то другим?</li>
            <li>Что вызывает сильное возмущение или боль (нарушение ценностей)?</li>
            <li>Как ценности проявляются в повседневных решениях?</li>
            <li>Есть ли конфликт между декларируемыми и реальными ценностями?</li>
        </ul>
        <strong>Пример:</strong> «Семья на первом месте — ради детей готов отказаться от повышения, если это потребует переезда. Свобода важнее денег — не будет работать в месте, где нет автономии».
    </div>
    
    <div class="structured-fields">
        <div class="options-grid">
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_16_values[]" value="family" <?php echo in_array('family', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Семья
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="freedom" <?php echo in_array('freedom', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Свобода
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="career" <?php echo in_array('career', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Карьера / успех
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="spirituality" <?php echo in_array('spirituality', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Духовность / вера
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="health" <?php echo in_array('health', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Здоровье
                </label>
            </div>
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_16_values[]" value="friends" <?php echo in_array('friends', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Друзья / общение
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="education" <?php echo in_array('education', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Образование / знания
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="creativity" <?php echo in_array('creativity', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Творчество
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="justice" <?php echo in_array('justice', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Справедливость
                </label>
                <label>
                    <input type="checkbox" name="param_16_values[]" value="security" <?php echo in_array('security', $values) ? 'checked' : ''; ?><?php echo $roDis; ?>>
                    Безопасность
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_16_free_text" style="margin-top: 15px;">Иерархия и анализ:</label>
    <textarea name="param_16_free_text" id="param_16_free_text" class="large"<?php echo $ro; ?> placeholder="Иерархия ценностей:&#10;1. Семья&#10;2. Здоровье&#10;3. Свобода&#10;4. Друзья&#10;5. Творчество&#10;&#10;Анализ..."><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

<?php
// Параметр 20: Коммуникативный стиль
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_20"><span class="param-name">20. Коммуникативный стиль</span></label>
    <div class="help-text">
        <strong>Проанализируйте по измерениям:</strong>
        <ul>
            <li><strong>Доминирование:</strong> доминирующий / равноправный / уступчивый</li>
            <li><strong>Открытость:</strong> открытый / избирательный / замкнутый</li>
            <li><strong>Эмоциональность:</strong> экспрессивный / сдержанный</li>
            <li><strong>Направление внимания:</strong> говорит больше, чем слушает / хороший слушатель</li>
        </ul>
        <br>
        <strong>Вербальные паттерны:</strong> словарный запас, темп речи, использование вопросов, утверждений, историй, склонность к спорам.<br>
        <strong>Невербальные паттерны:</strong> контакт глазами, жесты, поза, дистанция в общении.
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Доминирование:</label>
                <select name="param_20_dominance" id="param_20_dominance">
                    <option value="">-- Выберите --</option>
                    <option value="dominant" <?php echo (isset($structured['dominance']) && $structured['dominance'] == 'dominant') ? 'selected' : ''; ?>>Доминирующий</option>
                    <option value="equal" <?php echo (isset($structured['dominance']) && $structured['dominance'] == 'equal') ? 'selected' : ''; ?>>Равноправный</option>
                    <option value="submissive" <?php echo (isset($structured['dominance']) && $structured['dominance'] == 'submissive') ? 'selected' : ''; ?>>Уступчивый</option>
                </select>
            </div>
            <div class="field-group">
                <label>Открытость:</label>
                <select name="param_20_openness" id="param_20_openness">
                    <option value="">-- Выберите --</option>
                    <option value="open" <?php echo (isset($structured['openness']) && $structured['openness'] == 'open') ? 'selected' : ''; ?>>Открытый</option>
                    <option value="selective" <?php echo (isset($structured['openness']) && $structured['openness'] == 'selective') ? 'selected' : ''; ?>>Избирательный</option>
                    <option value="closed" <?php echo (isset($structured['openness']) && $structured['openness'] == 'closed') ? 'selected' : ''; ?>>Замкнутый</option>
                </select>
            </div>
            <div class="field-group">
                <label>Эмоциональность:</label>
                <select name="param_20_emotionality" id="param_20_emotionality">
                    <option value="">-- Выберите --</option>
                    <option value="expressive" <?php echo (isset($structured['emotionality']) && $structured['emotionality'] == 'expressive') ? 'selected' : ''; ?>>Экспрессивный</option>
                    <option value="restrained" <?php echo (isset($structured['emotionality']) && $structured['emotionality'] == 'restrained') ? 'selected' : ''; ?>>Сдержанный</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Направление внимания:</label>
                <select name="param_20_attention" id="param_20_attention">
                    <option value="">-- Выберите --</option>
                    <option value="talks_more" <?php echo (isset($structured['attention']) && $structured['attention'] == 'talks_more') ? 'selected' : ''; ?>>Говорит больше, чем слушает</option>
                    <option value="good_listener" <?php echo (isset($structured['attention']) && $structured['attention'] == 'good_listener') ? 'selected' : ''; ?>>Хороший слушатель</option>
                    <option value="balanced" <?php echo (isset($structured['attention']) && $structured['attention'] == 'balanced') ? 'selected' : ''; ?>>Сбалансированный</option>
                </select>
            </div>
            <div class="field-group">
                <label>Темп речи:</label>
                <select name="param_20_speech_pace" id="param_20_speech_pace">
                    <option value="">-- Выберите --</option>
                    <option value="fast" <?php echo (isset($structured['speech_pace']) && $structured['speech_pace'] == 'fast') ? 'selected' : ''; ?>>Быстрый</option>
                    <option value="medium" <?php echo (isset($structured['speech_pace']) && $structured['speech_pace'] == 'medium') ? 'selected' : ''; ?>>Средний</option>
                    <option value="slow" <?php echo (isset($structured['speech_pace']) && $structured['speech_pace'] == 'slow') ? 'selected' : ''; ?>>Медленный</option>
                </select>
            </div>
        </div>
    </div>
    
    <label for="param_20_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_20_free_text" id="param_20_free_text" class="xlarge"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

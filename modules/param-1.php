<?php
// Параметр 1: Базовые демографические данные
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_1"><span class="param-name">1. Базовые демографические данные</span></label>
    <div class="help-text">
        <strong>Что указать:</strong> ФИО, дата рождения, возраст, пол, гражданство, место рождения и текущее место проживания.<br>
        <strong>Зачем это нужно:</strong> эти данные задают базовый контекст — культурную среду, поколенческие особенности, социальные условия формирования личности.<br>
        <strong>Совет:</strong> укажите не только факты, но и важные детали — например, «родился в маленьком городе, сейчас живёт в мегаполисе» или «переехал в другую страну в 25 лет».
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>ФИО:</label>
                <input type="text" name="param_1_fio" id="param_1_fio" value="<?php echo htmlspecialchars($structured['fio'] ?? ''); ?>" placeholder="Фамилия Имя Отчество">
            </div>
            <div class="field-group">
                <label>Дата рождения:</label>
                <input type="date" name="param_1_birth_date" id="param_1_birth_date" value="<?php echo htmlspecialchars($structured['birth_date'] ?? ''); ?>">
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Возраст:</label>
                <input type="number" name="param_1_age" id="param_1_age" value="<?php echo htmlspecialchars($structured['age'] ?? ''); ?>" placeholder="Возраст" min="0" max="150">
            </div>
            <div class="field-group">
                <label>Пол:</label>
                <select name="param_1_gender" id="param_1_gender">
                    <option value="">-- Выберите --</option>
                    <option value="male" <?php echo (isset($structured['gender']) && $structured['gender'] == 'male') ? 'selected' : ''; ?>>Мужской</option>
                    <option value="female" <?php echo (isset($structured['gender']) && $structured['gender'] == 'female') ? 'selected' : ''; ?>>Женский</option>
                    <option value="other" <?php echo (isset($structured['gender']) && $structured['gender'] == 'other') ? 'selected' : ''; ?>>Другое</option>
                </select>
            </div>
            <div class="field-group">
                <label>Гражданство:</label>
                <input type="text" name="param_1_citizenship" id="param_1_citizenship" value="<?php echo htmlspecialchars($structured['citizenship'] ?? ''); ?>" placeholder="Гражданство" data-autocomplete data-autocomplete-type="city">
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Место рождения:</label>
                <input type="text" name="param_1_birthplace" id="param_1_birthplace" value="<?php echo htmlspecialchars($structured['birthplace'] ?? ''); ?>" placeholder="Город, страна" class="city-input" data-autocomplete data-autocomplete-type="city">
            </div>
            <div class="field-group">
                <label>Место проживания:</label>
                <input type="text" name="param_1_residence" id="param_1_residence" value="<?php echo htmlspecialchars($structured['residence'] ?? ''); ?>" placeholder="Город, район" class="city-input" data-autocomplete data-autocomplete-type="city">
            </div>
        </div>
    </div>
    
    <label for="param_1_free_text" style="margin-top: 15px;">Дополнительные детали:</label>
    <textarea name="param_1_free_text" id="param_1_free_text" class="large" placeholder="ФИО: Иванов Иван Иванович&#10;Дата рождения: 15.03.1992&#10;Возраст: 32 года&#10;Пол: мужской&#10;Гражданство: РФ&#10;Место рождения: Екатеринбург&#10;Место проживания: Москва, район Хамовники"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

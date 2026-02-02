<?php
// Параметр 22: Паттерны в конфликтах
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$strategies = isset($structured['strategies']) ? $structured['strategies'] : [];
?>
<div class="form-group">
    <label for="param_22"><span class="param-name">22. Паттерны в конфликтах</span></label>
    <div class="help-text">
        <strong>Типичные стратегии разрешения конфликтов:</strong>
        <br><br>
        <strong>Дополнительный анализ:</strong>
        <ul>
            <li><strong>Триггеры конфликтов:</strong> что чаще всего вызывает раздражение?</li>
            <li><strong>Физические реакции:</strong> краснеет, кричит, замолкает, уходит?</li>
            <li><strong>Время восстановления:</strong> как быстро возвращается к норме после ссоры?</li>
            <li><strong>Склонность к прощению:</strong> держит обиду или быстро отпускает?</li>
            <li><strong>Паттерны в разных контекстах:</strong> ведёт ли себя одинаково с начальником, партнёром, друзьями?</li>
        </ul>
    </div>
    
    <div class="structured-fields">
        <div class="options-grid">
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="cooperation" <?php echo in_array('cooperation', $strategies) ? 'checked' : ''; ?>>
                    Сотрудничество (ищу взаимовыгодное решение)
                </label>
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="compromise" <?php echo in_array('compromise', $strategies) ? 'checked' : ''; ?>>
                    Компромисс (готов пойти на уступки)
                </label>
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="avoidance" <?php echo in_array('avoidance', $strategies) ? 'checked' : ''; ?>>
                    Избегание (уходит от конфликта)
                </label>
            </div>
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="competition" <?php echo in_array('competition', $strategies) ? 'checked' : ''; ?>>
                    Конкуренция (стоит на своём)
                </label>
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="accommodation" <?php echo in_array('accommodation', $strategies) ? 'checked' : ''; ?>>
                    Приспособление (уступает ради мира)
                </label>
                <label>
                    <input type="checkbox" name="param_22_strategies[]" value="passive_aggression" <?php echo in_array('passive_aggression', $strategies) ? 'checked' : ''; ?>>
                    Пассивная агрессия
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_22_free_text" style="margin-top: 15px;">Дополнительный анализ:</label>
    <textarea name="param_22_free_text" id="param_22_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

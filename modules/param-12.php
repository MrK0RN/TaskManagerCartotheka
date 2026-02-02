<?php
// Параметр 12: Темперамент и эмоциональная регуляция
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_12"><span class="param-name">12. Темперамент и эмоциональная регуляция</span></label>
    <div class="help-text">
        <strong>Что описать:</strong>
        <ul>
            <li><strong>Темперамент:</strong> холерик (быстрый, страстный), сангвиник (живой, уравновешенный), флегматик (спокойный, медлительный), меланхолик (чувствительный, ранимый) — или смешанный тип</li>
            <li><strong>Эмоциональная реактивность:</strong> быстро ли реагирует на события?</li>
            <li><strong>Интенсивность эмоций:</strong> глубоко ли переживает?</li>
            <li><strong>Скорость восстановления:</strong> как быстро приходит в себя после стресса?</li>
            <li><strong>Эмоциональный диапазон:</strong> какие эмоции выражает чаще всего?</li>
        </ul>
        <strong>Наблюдайте:</strong> вспыльчивый или сдержанный? Долго ли держит обиду? Как проявляется радость, грусть, гнев?
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Темперамент:</label>
                <select name="param_12_temperament" id="param_12_temperament">
                    <option value="">-- Выберите --</option>
                    <option value="choleric" <?php echo (isset($structured['temperament']) && $structured['temperament'] == 'choleric') ? 'selected' : ''; ?>>Холерик</option>
                    <option value="sanguine" <?php echo (isset($structured['temperament']) && $structured['temperament'] == 'sanguine') ? 'selected' : ''; ?>>Сангвиник</option>
                    <option value="phlegmatic" <?php echo (isset($structured['temperament']) && $structured['temperament'] == 'phlegmatic') ? 'selected' : ''; ?>>Флегматик</option>
                    <option value="melancholic" <?php echo (isset($structured['temperament']) && $structured['temperament'] == 'melancholic') ? 'selected' : ''; ?>>Меланхолик</option>
                    <option value="mixed" <?php echo (isset($structured['temperament']) && $structured['temperament'] == 'mixed') ? 'selected' : ''; ?>>Смешанный</option>
                </select>
            </div>
            <div class="field-group">
                <label>Эмоциональная реактивность:</label>
                <select name="param_12_reactivity" id="param_12_reactivity">
                    <option value="">-- Выберите --</option>
                    <option value="very_fast" <?php echo (isset($structured['reactivity']) && $structured['reactivity'] == 'very_fast') ? 'selected' : ''; ?>>Очень быстрая</option>
                    <option value="fast" <?php echo (isset($structured['reactivity']) && $structured['reactivity'] == 'fast') ? 'selected' : ''; ?>>Быстрая</option>
                    <option value="medium" <?php echo (isset($structured['reactivity']) && $structured['reactivity'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="slow" <?php echo (isset($structured['reactivity']) && $structured['reactivity'] == 'slow') ? 'selected' : ''; ?>>Медленная</option>
                </select>
            </div>
            <div class="field-group">
                <label>Скорость восстановления:</label>
                <select name="param_12_recovery" id="param_12_recovery">
                    <option value="">-- Выберите --</option>
                    <option value="very_fast" <?php echo (isset($structured['recovery']) && $structured['recovery'] == 'very_fast') ? 'selected' : ''; ?>>Очень быстрая</option>
                    <option value="fast" <?php echo (isset($structured['recovery']) && $structured['recovery'] == 'fast') ? 'selected' : ''; ?>>Быстрая</option>
                    <option value="medium" <?php echo (isset($structured['recovery']) && $structured['recovery'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="slow" <?php echo (isset($structured['recovery']) && $structured['recovery'] == 'slow') ? 'selected' : ''; ?>>Медленная</option>
                </select>
            </div>
        </div>
    </div>
    
    <label for="param_12_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_12_free_text" id="param_12_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

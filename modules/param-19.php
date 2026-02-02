<?php
// Параметр 19: Социальная идентичность
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$identities = isset($structured['identities']) ? $structured['identities'] : [];
?>
<div class="form-group">
    <label for="param_19"><span class="param-name">19. Социальная идентичность</span></label>
    <div class="help-text">
        <strong>Перечислите группы, к которым человек себя относит:</strong>
        <br><br>
        <strong>Анализируйте:</strong>
        <ul>
            <li>Какие идентичности самые важные для самоощущения?</li>
            <li>Есть ли конфликт между разными идентичностями?</li>
            <li>Как человек воспринимается другими в этих группах?</li>
            <li>Есть ли «стигматизированные» идентичности (те, о которых стесняется)?</li>
            <li>Как идентичность влияет на поведение и решения?</li>
        </ul>
        <strong>Пример:</strong> «Сильная профессиональная идентичность (инженер) — гордится профессией. Слабая национальная идентичность — не чувствует связи с культурой предков».
    </div>
    
    <div class="structured-fields">
        <div class="options-grid">
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="professional" <?php echo in_array('professional', $identities) ? 'checked' : ''; ?>>
                    Профессиональная
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="ethnic" <?php echo in_array('ethnic', $identities) ? 'checked' : ''; ?>>
                    Этническая / национальная
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="age" <?php echo in_array('age', $identities) ? 'checked' : ''; ?>>
                    Возрастная (поколение)
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="regional" <?php echo in_array('regional', $identities) ? 'checked' : ''; ?>>
                    Региональная
                </label>
            </div>
            <div class="option-item">
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="religious" <?php echo in_array('religious', $identities) ? 'checked' : ''; ?>>
                    Религиозная
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="gender" <?php echo in_array('gender', $identities) ? 'checked' : ''; ?>>
                    Гендерная / сексуальная
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="subcultural" <?php echo in_array('subcultural', $identities) ? 'checked' : ''; ?>>
                    Субкультурная / хобби
                </label>
                <label>
                    <input type="checkbox" name="param_19_identities[]" value="political" <?php echo in_array('political', $identities) ? 'checked' : ''; ?>>
                    Политическая
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_19_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_19_free_text" id="param_19_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

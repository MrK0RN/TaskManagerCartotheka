<?php
// Параметр 9: Социальный капитал
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_9"><span class="param-name">9. Социальный капитал</span></label>
    <div class="help-text">
        <strong>Проанализируйте:</strong>
        <ul>
            <li><strong>Качество связей:</strong> глубина доверия, готовность помочь</li>
            <li><strong>Масштаб сети:</strong> количество значимых контактов</li>
            <li><strong>Влиятельные связи:</strong> есть ли доступ к людям с властью/ресурсами?</li>
            <li><strong>Сообщества:</strong> участие в профессиональных, хобби- или социальных группах</li>
            <li><strong>Репутация:</strong> как человек воспринимается в кругах общения</li>
        </ul>
        <strong>Пример:</strong> «Сильная сеть в IT-среде (50+ контактов), доверительные отношения с 3-4 наставниками, активный участник профессионального сообщества».
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Количество значимых контактов:</label>
                <select name="param_9_contacts_scale" id="param_9_contacts_scale">
                    <option value="">-- Выберите --</option>
                    <option value="very_large" <?php echo (isset($structured['contacts_scale']) && $structured['contacts_scale'] == 'very_large') ? 'selected' : ''; ?>>Очень большая сеть (100+)</option>
                    <option value="large" <?php echo (isset($structured['contacts_scale']) && $structured['contacts_scale'] == 'large') ? 'selected' : ''; ?>>Большая сеть (50-100)</option>
                    <option value="medium" <?php echo (isset($structured['contacts_scale']) && $structured['contacts_scale'] == 'medium') ? 'selected' : ''; ?>>Средняя сеть (20-50)</option>
                    <option value="small" <?php echo (isset($structured['contacts_scale']) && $structured['contacts_scale'] == 'small') ? 'selected' : ''; ?>>Небольшая сеть (10-20)</option>
                    <option value="very_small" <?php echo (isset($structured['contacts_scale']) && $structured['contacts_scale'] == 'very_small') ? 'selected' : ''; ?>>Очень маленькая сеть (<10)</option>
                </select>
            </div>
            <div class="field-group">
                <label>Качество связей:</label>
                <select name="param_9_quality" id="param_9_quality">
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['quality']) && $structured['quality'] == 'very_high') ? 'selected' : ''; ?>>Очень высокое</option>
                    <option value="high" <?php echo (isset($structured['quality']) && $structured['quality'] == 'high') ? 'selected' : ''; ?>>Высокое</option>
                    <option value="medium" <?php echo (isset($structured['quality']) && $structured['quality'] == 'medium') ? 'selected' : ''; ?>>Среднее</option>
                    <option value="low" <?php echo (isset($structured['quality']) && $structured['quality'] == 'low') ? 'selected' : ''; ?>>Низкое</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_9_has_influential" value="1" <?php echo (isset($structured['has_influential']) && $structured['has_influential']) ? 'checked' : ''; ?>>
                    Есть влиятельные связи
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_9_in_communities" value="1" <?php echo (isset($structured['in_communities']) && $structured['in_communities']) ? 'checked' : ''; ?>>
                    Участвует в сообществах
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_9_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_9_free_text" id="param_9_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

<?php
// Параметр 14: Уровень самооценки и самоэффективности
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_14"><span class="param-name">14. Уровень самооценки и самоэффективности</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Разберите по сферам:</strong>
        <ul>
            <li><strong>Общая самооценка:</strong> завышенная, адекватная, заниженная</li>
            <li><strong>Самооценка в отношениях:</strong> чувствует ли себя достойным любви?</li>
            <li><strong>Самооценка в работе:</strong> верит ли в свои профессиональные способности?</li>
            <li><strong>Самоэффективность:</strong> насколько уверен, что справится с трудностями</li>
            <li><strong>Склонность к самокритике:</strong> строгий ли к себе судья?</li>
            <li><strong>Источники самооценки:</strong> от чего зависит (мнение других, достижения, внешность)?</li>
        </ul>
        <strong>Важно:</strong> как человек реагирует на комплименты и критику?
    </div>
    <?php endif; ?>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Общая самооценка:</label>
                <select name="param_14_self_esteem" id="param_14_self_esteem"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="inflated" <?php echo (isset($structured['self_esteem']) && $structured['self_esteem'] == 'inflated') ? 'selected' : ''; ?>>Завышенная</option>
                    <option value="adequate" <?php echo (isset($structured['self_esteem']) && $structured['self_esteem'] == 'adequate') ? 'selected' : ''; ?>>Адекватная</option>
                    <option value="low" <?php echo (isset($structured['self_esteem']) && $structured['self_esteem'] == 'low') ? 'selected' : ''; ?>>Заниженная</option>
                </select>
            </div>
            <div class="field-group">
                <label>Самооценка в отношениях:</label>
                <select name="param_14_relationship_esteem" id="param_14_relationship_esteem"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="high" <?php echo (isset($structured['relationship_esteem']) && $structured['relationship_esteem'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['relationship_esteem']) && $structured['relationship_esteem'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['relationship_esteem']) && $structured['relationship_esteem'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
            <div class="field-group">
                <label>Самооценка в работе:</label>
                <select name="param_14_work_esteem" id="param_14_work_esteem"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="high" <?php echo (isset($structured['work_esteem']) && $structured['work_esteem'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['work_esteem']) && $structured['work_esteem'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['work_esteem']) && $structured['work_esteem'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>Самоэффективность:</label>
                <select name="param_14_self_efficacy" id="param_14_self_efficacy"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['self_efficacy']) && $structured['self_efficacy'] == 'very_high') ? 'selected' : ''; ?>>Очень высокая</option>
                    <option value="high" <?php echo (isset($structured['self_efficacy']) && $structured['self_efficacy'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['self_efficacy']) && $structured['self_efficacy'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['self_efficacy']) && $structured['self_efficacy'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
            <div class="field-group">
                <label>Склонность к самокритике:</label>
                <select name="param_14_self_criticism" id="param_14_self_criticism"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="very_high" <?php echo (isset($structured['self_criticism']) && $structured['self_criticism'] == 'very_high') ? 'selected' : ''; ?>>Очень высокая</option>
                    <option value="high" <?php echo (isset($structured['self_criticism']) && $structured['self_criticism'] == 'high') ? 'selected' : ''; ?>>Высокая</option>
                    <option value="medium" <?php echo (isset($structured['self_criticism']) && $structured['self_criticism'] == 'medium') ? 'selected' : ''; ?>>Средняя</option>
                    <option value="low" <?php echo (isset($structured['self_criticism']) && $structured['self_criticism'] == 'low') ? 'selected' : ''; ?>>Низкая</option>
                </select>
            </div>
        </div>
    </div>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_14_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_14_free_text" id="param_14_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>

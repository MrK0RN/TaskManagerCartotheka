<?php
// Параметр 17: Мировоззрение и убеждения
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_17"><span class="param-name">17. Мировоззрение и убеждения</span></label>
    <div class="help-text">
        <strong>Рассмотрите следующие аспекты:</strong>
        <ul>
            <li><strong>Религиозные/духовные взгляды:</strong> верующий, агностик, атеист; принадлежность к традиции</li>
            <li><strong>Философские взгляды:</strong> оптимистичный/пессимистичный взгляд на мир, вера в свободу воли или предопределение</li>
            <li><strong>Политическая ориентация:</strong> либеральные, консервативные, социалистические и др. взгляды (если важны для человека)</li>
            <li><strong>Отношение к справедливости:</strong> что считает справедливым/несправедливым?</li>
            <li><strong>Базовые убеждения о людях:</strong> люди по природе добрые или эгоистичные? Мир дружелюбный или враждебный?</li>
            <li><strong>Убеждения о себе:</strong> «я достоин», «я способный», «я контролирую свою жизнь» и т.д.</li>
        </ul>
        <strong>Важно:</strong> насколько эти убеждения осознаны? Менялись ли они со временем?
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Религиозные/духовные взгляды:</label>
                <select name="param_17_religious_views" id="param_17_religious_views">
                    <option value="">-- Выберите --</option>
                    <option value="believer" <?php echo (isset($structured['religious_views']) && $structured['religious_views'] == 'believer') ? 'selected' : ''; ?>>Верующий</option>
                    <option value="agnostic" <?php echo (isset($structured['religious_views']) && $structured['religious_views'] == 'agnostic') ? 'selected' : ''; ?>>Агностик</option>
                    <option value="atheist" <?php echo (isset($structured['religious_views']) && $structured['religious_views'] == 'atheist') ? 'selected' : ''; ?>>Атеист</option>
                </select>
            </div>
            <div class="field-group">
                <label>Философский взгляд на мир:</label>
                <select name="param_17_philosophical" id="param_17_philosophical">
                    <option value="">-- Выберите --</option>
                    <option value="optimistic" <?php echo (isset($structured['philosophical']) && $structured['philosophical'] == 'optimistic') ? 'selected' : ''; ?>>Оптимистичный</option>
                    <option value="pessimistic" <?php echo (isset($structured['philosophical']) && $structured['philosophical'] == 'pessimistic') ? 'selected' : ''; ?>>Пессимистичный</option>
                    <option value="realistic" <?php echo (isset($structured['philosophical']) && $structured['philosophical'] == 'realistic') ? 'selected' : ''; ?>>Реалистичный</option>
                </select>
            </div>
        </div>
    </div>
    
    <label for="param_17_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_17_free_text" id="param_17_free_text" class="xlarge"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

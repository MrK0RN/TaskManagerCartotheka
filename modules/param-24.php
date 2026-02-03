<?php
// Параметр 24: Ключевые жизненные события
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
?>
<div class="form-group">
    <label for="param_24"><span class="param-name">24. Ключевые жизненные события</span></label>
    <div class="help-text">
        <strong>Составьте хронологию переломных моментов:</strong>
        <ul>
            <li><strong>Позитивные события (достижения):</strong> образовательные успехи, карьерные прорывы, вступление в брак, рождение детей, переезды, путешествия, личные победы</li>
            <li><strong>Негативные события (потери, травмы):</strong> смерть близких, разводы, расставания, потеря работы, финансовые кризисы, болезни, травмы, предательства, обманы, неудачи, провалы</li>
        </ul>
        <br>
        <strong>Анализ влияния:</strong>
        <ul>
            <li>Как каждое событие изменило взгляд на жизнь?</li>
            <li>Какие уроки были извлечены?</li>
            <li>Что событие «сломало», а что «построило»?</li>
            <li>Есть ли непрожитые травмы?</li>
            <li>Как события сформировали текущие ценности и страхи?</li>
        </ul>
        <strong>Совет:</strong> не просто перечислите события, а опишите их психологическое воздействие.
    </div>
    
    <label for="param_24_free_text">Хронология и анализ событий:</label>
    <textarea name="param_24_free_text" id="param_24_free_text" class="xlarge"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

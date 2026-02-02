<?php
// Параметр 21: Ролевой репертуар
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_21"><span class="param-name">21. Ролевой репертуар</span></label>
    <div class="help-text">
        <strong>Опишите поведение человека в ключевых ролях:</strong>
        <ul>
            <li><strong>Родитель:</strong> строгий / мягкий, вовлечённый / дистантный</li>
            <li><strong>Партнёр / супруг:</strong> инициативный / пассивный, заботливый / требовательный</li>
            <li><strong>Руководитель / подчинённый:</strong> авторитарный / демократичный, ответственный / избегающий ответственности</li>
            <li><strong>Друг:</strong> надёжный / непостоянный, поддерживающий / критикующий</li>
            <li><strong>Сын / дочь:</strong> заботливый / отстранённый, благодарный / обиженный</li>
            <li><strong>Коллега:</strong> командный игрок / одиночка, конкурирующий / сотрудничающий</li>
        </ul>
        <br>
        <strong>Важные аспекты:</strong>
        <ul>
            <li>Какие роли даются легко, а какие — с трудом?</li>
            <li>Есть ли ролевые конфликты (например, работа против семьи)?</li>
            <li>Как человек переключается между ролями?</li>
            <li>Какие роли самые значимые для самооценки?</li>
        </ul>
    </div>
    
    <label for="param_21_free_text">Описание ролевого репертуара:</label>
    <textarea name="param_21_free_text" id="param_21_free_text" class="xlarge"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

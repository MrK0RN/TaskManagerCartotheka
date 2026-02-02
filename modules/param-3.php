<?php
// Параметр 3: Образовательный маршрут
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$educationItems = isset($structured['education']) ? $structured['education'] : [];
?>
<div class="form-group">
    <label for="param_3"><span class="param-name">3. Образовательный маршрут</span></label>
    <div class="help-text">
        <strong>Что включить:</strong>
        <ul>
            <li>Все уровни образования (школа, колледж, вуз)</li>
            <li>Специализации и квалификации</li>
            <li>Ключевые учебные заведения</li>
            <li>Самообразование, курсы, сертификаты</li>
            <li>Отношение к обучению (любит учиться / избегает)</li>
        </ul>
        <strong>Анализируйте:</strong> как образование повлияло на мировоззрение? Были ли переломы (смена специальности)? Какие предметы давались легко/трудно и почему?
    </div>
    
    <?php
    $educationItems = $educationItems;
    $paramName = 'param_3_education';
    include COMPONENTS_PATH . '/education.php';
    ?>
    
    <label for="param_3_free_text" style="margin-top: 15px;">Дополнительные детали:</label>
    <textarea name="param_3_free_text" id="param_3_free_text" class="large"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

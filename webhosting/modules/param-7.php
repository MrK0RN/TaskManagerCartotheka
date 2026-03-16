<?php
// Параметр 7: Профессиональные навыки и компетенции
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$skills = isset($structured['skills']) ? $structured['skills'] : [];
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
?>
<div class="form-group">
    <label for="param_7"><span class="param-name">7. Профессиональные навыки и компетенции</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Разделите на две группы:</strong>
        <ul>
            <li><strong>Hard skills</strong> — технические навыки: программирование, бухгалтерия, дизайн, управление проектами, владение инструментами и т.д.</li>
            <li><strong>Soft skills</strong> — личностные навыки: коммуникация, лидерство, эмпатия, стрессоустойчивость, креативность</li>
        </ul>
        <strong>Оцените уровень:</strong> эксперт, продвинутый, средний, базовый.<br>
        <strong>Пример:</strong> «Python — эксперт (7 лет), управление командой — продвинутый (руководил 5 проектами), публичные выступления — базовый (избегает)».
    </div>
    <?php endif; ?>
    
    <?php
    $paramName = 'param_7_skills';
    include COMPONENTS_PATH . '/skills.php';
    ?>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_7_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_7_free_text" id="param_7_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>

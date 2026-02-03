<?php
// Параметр 23: Привычки, ритуалы и распорядок
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
?>
<div class="form-group">
    <label for="param_23"><span class="param-name">23. Привычки, ритуалы и распорядок</span></label>
    <div class="help-text">
        <strong>Разберите по сферам жизни:</strong>
        <ul>
            <li><strong>Утро:</strong> время пробуждения, ритуалы (кофе, медитация, спорт, чтение), планирование дня</li>
            <li><strong>Работа:</strong> организация рабочего пространства, методы тайм-менеджмента, перерывы и отдых</li>
            <li><strong>Вечер:</strong> ритуалы отключения (прогулка, сериал, общение), время отхода ко сну, предсонные привычки</li>
            <li><strong>Уход за собой:</strong> гигиена, спорт, питание, внешний вид</li>
            <li><strong>Отдых и досуг:</strong> что делает в свободное время, предпочитает одиночество или компанию, активный или пассивный отдых</li>
        </ul>
        <strong>Важно:</strong> какие привычки поддерживают, а какие вредят? Есть ли зависимость от определённых ритуалов?
    </div>
    
    <label for="param_23_free_text">Описание привычек и распорядка:</label>
    <textarea name="param_23_free_text" id="param_23_free_text" class="xlarge"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

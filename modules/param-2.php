<?php
// Параметр 2: Семейное положение и структура
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
$readOnly = isset($readOnly) ? $readOnly : false;
$ro = $readOnly ? ' readonly' : '';
$roDis = $readOnly ? ' disabled' : '';
?>
<div class="form-group">
    <label for="param_2"><span class="param-name">2. Семейное положение и структура</span></label>
    <?php if (!$readOnly): ?>
    <div class="help-text">
        <strong>Что указать:</strong> наличие партнёра (статус отношений), детей (возраст, количество), близких родственников, тип семьи.<br>
        <strong>Важно описать:</strong>
        <ul>
            <li>Качество отношений с членами семьи</li>
            <li>Степень вовлечённости в семейную жизнь</li>
            <li>Ролевые обязанности в семье</li>
            <li>Историю семьи (разводы, потери, переезды)</li>
        </ul>
        <strong>Пример:</strong> «В браке 8 лет, двое детей (6 и 10 лет). Близкие отношения с родителями, живущими в другом городе. Старшая сестра — опора в трудных ситуациях».
    </div>
    <?php endif; ?>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>Семейное положение:</label>
                <select name="param_2_status" id="param_2_status"<?php echo $roDis; ?>>
                    <option value="">-- Выберите --</option>
                    <option value="single" <?php echo (isset($structured['status']) && $structured['status'] == 'single') ? 'selected' : ''; ?>>Холост/Не замужем</option>
                    <option value="married" <?php echo (isset($structured['status']) && $structured['status'] == 'married') ? 'selected' : ''; ?>>В браке</option>
                    <option value="divorced" <?php echo (isset($structured['status']) && $structured['status'] == 'divorced') ? 'selected' : ''; ?>>Разведен(а)</option>
                    <option value="widowed" <?php echo (isset($structured['status']) && $structured['status'] == 'widowed') ? 'selected' : ''; ?>>Вдовец/Вдова</option>
                    <option value="relationship" <?php echo (isset($structured['status']) && $structured['status'] == 'relationship') ? 'selected' : ''; ?>>В отношениях</option>
                </select>
            </div>
            <div class="field-group">
                <label>Количество детей:</label>
                <input type="number" name="param_2_children_count" id="param_2_children_count" value="<?php echo htmlspecialchars($structured['children_count'] ?? ''); ?>" placeholder="0" min="0" max="20"<?php echo $ro; ?>>
            </div>
        </div>
        <div id="children-ages-container">
            <?php if (isset($structured['children_ages']) && is_array($structured['children_ages'])): ?>
                <?php foreach ($structured['children_ages'] as $index => $age): ?>
                    <div class="field-row child-age-row">
                        <div class="field-group">
                            <label>Возраст ребенка <?php echo $index + 1; ?>:</label>
                            <input type="number" name="param_2_children_ages[]" value="<?php echo htmlspecialchars($age); ?>" placeholder="Возраст" min="0" max="100"<?php echo $ro; ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!$readOnly): ?><button type="button" class="btn" onclick="addChildAge()" style="margin-top: 10px; padding: 8px 20px; font-size: 0.9rem;">+ Добавить возраст ребенка</button><?php endif; ?>
    </div>
    
    <?php if (!$readOnly || trim($freeText) !== ''): ?>
    <label for="param_2_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_2_free_text" id="param_2_free_text" class="large"<?php echo $ro; ?>><?php echo htmlspecialchars($freeText); ?></textarea>
    <?php endif; ?>
</div>
<?php if (!$readOnly): ?>
<script>
function addChildAge() {
    const container = document.getElementById('children-ages-container');
    const index = container.querySelectorAll('.child-age-row').length;
    const row = document.createElement('div');
    row.className = 'field-row child-age-row';
    row.innerHTML = `
        <div class="field-group">
            <label>Возраст ребенка ${index + 1}:</label>
            <input type="number" name="param_2_children_ages[]" placeholder="Возраст" min="0" max="100">
        </div>
    `;
    container.appendChild(row);
}
</script>
<?php endif; ?>

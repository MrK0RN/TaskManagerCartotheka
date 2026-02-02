<?php
// Параметр 6: Потенциальные материальные ресурсы
$paramData = isset($paramData) ? $paramData : ['structured_data' => [], 'free_text' => ''];
$structured = $paramData['structured_data'] ?? [];
$freeText = $paramData['free_text'] ?? '';
?>
<div class="form-group">
    <label for="param_6"><span class="param-name">6. Потенциальные материальные ресурсы</span></label>
    <div class="help-text">
        <strong>Рассмотрите:</strong>
        <ul>
            <li>Возможное наследство</li>
            <li>Инвестиционные возможности</li>
            <li>Потенциал роста дохода (повышение, смена работы)</li>
            <li>Нереализованные активы (квартира, которую можно сдать)</li>
            <li>Поддержка от семьи/друзей в финансовых вопросах</li>
        </ul>
        <strong>Вопросы для размышления:</strong> на что человек может рассчитывать в будущем? Есть ли «подушка безопасности»? Какие возможности пока не используются?
    </div>
    
    <div class="structured-fields">
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_6_potential_inheritance" value="1" <?php echo (isset($structured['potential_inheritance']) && $structured['potential_inheritance']) ? 'checked' : ''; ?>>
                    Возможное наследство
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_6_investment_opportunities" value="1" <?php echo (isset($structured['investment_opportunities']) && $structured['investment_opportunities']) ? 'checked' : ''; ?>>
                    Инвестиционные возможности
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_6_income_growth_potential" value="1" <?php echo (isset($structured['income_growth_potential']) && $structured['income_growth_potential']) ? 'checked' : ''; ?>>
                    Потенциал роста дохода
                </label>
            </div>
        </div>
        <div class="field-row">
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_6_unrealized_assets" value="1" <?php echo (isset($structured['unrealized_assets']) && $structured['unrealized_assets']) ? 'checked' : ''; ?>>
                    Нереализованные активы
                </label>
            </div>
            <div class="field-group">
                <label>
                    <input type="checkbox" name="param_6_family_support" value="1" <?php echo (isset($structured['family_support']) && $structured['family_support']) ? 'checked' : ''; ?>>
                    Поддержка семьи/друзей
                </label>
            </div>
        </div>
    </div>
    
    <label for="param_6_free_text" style="margin-top: 15px;">Дополнительная информация:</label>
    <textarea name="param_6_free_text" id="param_6_free_text"><?php echo htmlspecialchars($freeText); ?></textarea>
</div>

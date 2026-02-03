// Обработка формы и сохранение данных

function saveForm() {
    const form = document.getElementById('personalityForm');
    const formData = new FormData(form);
    
    // Собираем данные по параметрам
    const data = {
        portrait_id: formData.get('portrait_id') || null
    };
    
    // Обрабатываем все поля формы
    for (let i = 1; i <= 25; i++) {
        const paramKey = `param_${i}`;
        const paramData = {
            structured_data: {},
            free_text: ''
        };
        let hasData = false;
        
        // Собираем все поля для этого параметра
        for (let [key, value] of formData.entries()) {
            if (key === 'portrait_id') continue;
            
            // Проверяем, относится ли поле к этому параметру
            if (key.startsWith(`param_${i}_`)) {
                hasData = true;
                const fieldName = key.replace(`param_${i}_`, '');
                
                // Если это свободный текст
                if (fieldName === 'free_text') {
                    paramData.free_text = value;
                } else {
                    // Структурированное поле
                    paramData.structured_data[fieldName] = value;
                }
            }
            
            // Обрабатываем вложенные структуры (языки, образование, навыки и т.д.)
            // Например: param_8_languages[0][language_id]
            const nestedMatch = key.match(new RegExp(`param_${i}_(\\w+)\\[(\\d+)\\]\\[(\\w+)\\]`));
            if (nestedMatch) {
                hasData = true;
                const componentName = nestedMatch[1]; // например, 'languages'
                const index = nestedMatch[2];
                const fieldName = nestedMatch[3]; // например, 'language_id'
                
                if (!paramData.structured_data[componentName]) {
                    paramData.structured_data[componentName] = [];
                }
                if (!paramData.structured_data[componentName][index]) {
                    paramData.structured_data[componentName][index] = {};
                }
                paramData.structured_data[componentName][index][fieldName] = value;
            }
            
            // Обрабатываем массивы (например, param_15_motivations[])
            const arrayMatch = key.match(new RegExp(`param_${i}_(\\w+)\\[\\]`));
            if (arrayMatch) {
                hasData = true;
                const fieldName = arrayMatch[1];
                if (!paramData.structured_data[fieldName]) {
                    paramData.structured_data[fieldName] = [];
                }
                paramData.structured_data[fieldName].push(value);
            }
        }
        
        if (hasData) {
            data[paramKey] = paramData;
        }
    }
    
    // Отправляем данные
    fetch('api/save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновляем portrait_id если он был создан
            if (data.portrait_id) {
                document.getElementById('portrait_id').value = data.portrait_id;
                // Обновляем URL без перезагрузки, чтобы при обновлении страницы остаться на том же портрете
                if (!window.location.search.includes('id=')) {
                    history.replaceState(null, '', 'index.php?id=' + data.portrait_id);
                }
            }
            
            showMessage('Данные успешно сохранены!', 'success');
        } else {
            showMessage('Ошибка при сохранении: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ошибка при сохранении данных', 'error');
    });
}

function showMessage(text, type) {
    // Удаляем существующие сообщения
    const existing = document.querySelector('.message');
    if (existing) {
        existing.remove();
    }
    
    const message = document.createElement('div');
    message.className = `message ${type}`;
    message.textContent = text;
    
    const formContainer = document.querySelector('.form-container');
    formContainer.insertBefore(message, formContainer.firstChild);
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        message.remove();
    }, 5000);
}

// Автосохранение каждые 30 секунд (опционально)
let autoSaveInterval = null;

function startAutoSave() {
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
    }
    
    autoSaveInterval = setInterval(() => {
        const portraitId = document.getElementById('portrait_id').value;
        if (portraitId) {
            saveForm();
        }
    }, 30000);
}

function stopAutoSave() {
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
        autoSaveInterval = null;
    }
}

// Запускаем автосохранение при загрузке
document.addEventListener('DOMContentLoaded', function() {
    // startAutoSave(); // Раскомментируйте если нужно автосохранение
});

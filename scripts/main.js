// Основная логика приложения

// Достаточно большое значение, чтобы контент секции никогда не обрезался и не пересекался с соседними
var SECTION_MAX_HEIGHT_OPEN = 8000;

// Сворачивание/разворачивание секций
function toggleSection(header) {
    const content = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    
    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
        const currentHeight = content.scrollHeight;
        content.style.maxHeight = currentHeight + 'px';
        content.offsetHeight; // reflow для корректной анимации
        content.style.maxHeight = '0px';
        content.style.opacity = '0';
        setTimeout(() => {
            content.style.display = 'none';
            icon.textContent = '▼';
        }, 500);
    } else {
        content.style.display = 'block';
        content.style.maxHeight = '0px';
        content.style.opacity = '0';
        requestAnimationFrame(() => {
            content.style.maxHeight = SECTION_MAX_HEIGHT_OPEN + 'px';
            content.style.opacity = '1';
            icon.textContent = '▲';
        });
    }
}

// Инициализация: все секции открыты
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section-content');
    sections.forEach(section => {
        section.style.display = 'block';
        // Используем запас по высоте, чтобы контент (в т.ч. параметры 11 и 12) не обрезался и не пересекался
        section.style.maxHeight = SECTION_MAX_HEIGHT_OPEN + 'px';
        section.style.opacity = '1';
    });
    
    const icons = document.querySelectorAll('.toggle-icon');
    icons.forEach(icon => {
        icon.textContent = '▲';
    });
    
    // Автоизменение размера textarea по содержимому
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});

// Функции для работы с языками
function addLanguage(paramName) {
    const container = document.querySelector(`.languages-component[data-param-name="${paramName}"]`);
    const list = container.querySelector('.languages-list');
    const items = list.querySelectorAll('.language-item');
    const nextIndex = items.length;
    
    // Список языков из data-атрибута (из PHP)
    let languagesList = [];
    try {
        const dataLanguages = container.getAttribute('data-languages');
        if (dataLanguages) {
            languagesList = JSON.parse(dataLanguages);
        }
    } catch (e) {
        console.warn('Не удалось загрузить список языков', e);
    }
    
    const optionsHtml = languagesList.map(lang =>
        `<option value="${lang.id}">${escapeHtml(lang.name)}</option>`
    ).join('');
    
    const newItem = document.createElement('div');
    newItem.className = 'language-item';
    newItem.setAttribute('data-index', nextIndex);
    
    newItem.innerHTML = `
        <div class="language-row">
            <div class="language-select-wrapper">
                <label>Язык:</label>
                <select name="${paramName}[${nextIndex}][language_id]" class="language-select" data-autocomplete>
                    <option value="">-- Выберите язык --</option>
                    ${optionsHtml}
                </select>
                <input type="text" class="language-search" placeholder="Поиск языка..." autocomplete="off">
                <div class="autocomplete-dropdown"></div>
            </div>
            <div class="level-select-wrapper">
                <label>Уровень:</label>
                <select name="${paramName}[${nextIndex}][level]" class="level-select">
                    <option value="">-- Выберите уровень --</option>
                    <option value="A1">A1 - Начальный</option>
                    <option value="A2">A2 - Элементарный</option>
                    <option value="B1">B1 - Средний</option>
                    <option value="B2">B2 - Выше среднего</option>
                    <option value="C1">C1 - Продвинутый</option>
                    <option value="C2">C2 - Владение</option>
                    <option value="Native">Родной</option>
                </select>
            </div>
            <button type="button" class="btn-remove-language" onclick="removeLanguage(this)">✕</button>
        </div>
        <div class="comment-wrapper">
            <label>Комментарий:</label>
            <textarea name="${paramName}[${nextIndex}][comment]" class="language-comment" placeholder="Дополнительная информация о владении языком..." rows="2"></textarea>
        </div>
    `;
    
    list.appendChild(newItem);
    
    // Показываем кнопку удаления для всех элементов
    list.querySelectorAll('.btn-remove-language').forEach(btn => {
        btn.style.display = 'block';
    });
    
    // Инициализируем автодополнение для нового элемента
    initLanguageAutocomplete(newItem);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function removeLanguage(button) {
    const item = button.closest('.language-item');
    const list = item.parentElement;
    item.remove();
    
    // Если остался один элемент, скрываем кнопку удаления
    const items = list.querySelectorAll('.language-item');
    if (items.length === 1) {
        items[0].querySelector('.btn-remove-language').style.display = 'none';
    }
}

// Функции для работы с образованием
function addEducation(paramName) {
    const container = document.querySelector(`.education-component[data-param-name="${paramName}"]`);
    const list = container.querySelector('.education-list');
    const items = list.querySelectorAll('.education-item');
    const nextIndex = items.length;
    
    const newItem = document.createElement('div');
    newItem.className = 'education-item';
    newItem.setAttribute('data-index', nextIndex);
    
    newItem.innerHTML = `
        <div class="education-row">
            <div class="education-type-wrapper">
                <label>Тип:</label>
                <select name="${paramName}[${nextIndex}][type]" class="education-type">
                    <option value="">-- Выберите тип --</option>
                    <option value="school">Школа</option>
                    <option value="college">Колледж</option>
                    <option value="university">ВУЗ</option>
                    <option value="course">Курсы</option>
                    <option value="self">Самообразование</option>
                </select>
            </div>
            <div class="institution-wrapper">
                <label>Учреждение:</label>
                <input type="text" name="${paramName}[${nextIndex}][institution]" class="institution-input" placeholder="Название учреждения">
            </div>
            <div class="specialization-wrapper">
                <label>Специализация:</label>
                <input type="text" name="${paramName}[${nextIndex}][specialization]" class="specialization-input" placeholder="Специализация/направление">
            </div>
            <button type="button" class="btn-remove-education" onclick="removeEducation(this)">✕</button>
        </div>
        <div class="education-dates">
            <div class="date-from">
                <label>Год начала:</label>
                <input type="number" name="${paramName}[${nextIndex}][year_from]" class="year-input" placeholder="ГГГГ" min="1950" max="${new Date().getFullYear()}">
            </div>
            <div class="date-to">
                <label>Год окончания:</label>
                <input type="number" name="${paramName}[${nextIndex}][year_to]" class="year-input" placeholder="ГГГГ" min="1950" max="${new Date().getFullYear() + 10}">
            </div>
        </div>
        <div class="education-details">
            <label>Дополнительные детали:</label>
            <textarea name="${paramName}[${nextIndex}][details]" class="education-details-textarea" placeholder="Дополнительная информация об образовании..." rows="2"></textarea>
        </div>
    `;
    
    list.appendChild(newItem);
    
    list.querySelectorAll('.btn-remove-education').forEach(btn => {
        btn.style.display = 'block';
    });
}

function removeEducation(button) {
    const item = button.closest('.education-item');
    const list = item.parentElement;
    item.remove();
    
    const items = list.querySelectorAll('.education-item');
    if (items.length === 1) {
        items[0].querySelector('.btn-remove-education').style.display = 'none';
    }
}

// Функции для работы с навыками
function addSkill(paramName) {
    const container = document.querySelector(`.skills-component[data-param-name="${paramName}"]`);
    const list = container.querySelector('.skills-list');
    const items = list.querySelectorAll('.skill-item');
    const nextIndex = items.length;
    
    const newItem = document.createElement('div');
    newItem.className = 'skill-item';
    newItem.setAttribute('data-index', nextIndex);
    
    newItem.innerHTML = `
        <div class="skill-row">
            <div class="skill-category-wrapper">
                <label>Категория:</label>
                <select name="${paramName}[${nextIndex}][category]" class="skill-category" onchange="updateSkillOptions(this)">
                    <option value="">-- Выберите категорию --</option>
                    <option value="hard">Hard skills</option>
                    <option value="soft">Soft skills</option>
                </select>
            </div>
            <div class="skill-name-wrapper">
                <label>Навык:</label>
                <select name="${paramName}[${nextIndex}][skill_id]" class="skill-select" data-category="">
                    <option value="">-- Сначала выберите категорию --</option>
                </select>
                <input type="text" class="skill-custom-input" name="${paramName}[${nextIndex}][skill_custom]" placeholder="Или введите свой навык" style="display:none;">
            </div>
            <div class="skill-level-wrapper">
                <label>Уровень:</label>
                <select name="${paramName}[${nextIndex}][level]" class="skill-level">
                    <option value="">-- Выберите уровень --</option>
                    <option value="expert">Эксперт</option>
                    <option value="advanced">Продвинутый</option>
                    <option value="intermediate">Средний</option>
                    <option value="basic">Базовый</option>
                </select>
            </div>
            <button type="button" class="btn-remove-skill" onclick="removeSkill(this)">✕</button>
        </div>
        <div class="skill-comment-wrapper">
            <label>Комментарий:</label>
            <textarea name="${paramName}[${nextIndex}][comment]" class="skill-comment" placeholder="Дополнительная информация о навыке..." rows="2"></textarea>
        </div>
    `;
    
    list.appendChild(newItem);
    
    list.querySelectorAll('.btn-remove-skill').forEach(btn => {
        btn.style.display = 'block';
    });
}

function removeSkill(button) {
    const item = button.closest('.skill-item');
    const list = item.parentElement;
    item.remove();
    
    const items = list.querySelectorAll('.skill-item');
    if (items.length === 1) {
        items[0].querySelector('.btn-remove-skill').style.display = 'none';
    }
}

function updateSkillOptions(select) {
    const category = select.value;
    const item = select.closest('.skill-item');
    const skillSelect = item.querySelector('.skill-select');
    const customInput = item.querySelector('.skill-custom-input');
    
    skillSelect.innerHTML = '<option value="">-- Выберите навык --</option>';
    skillSelect.setAttribute('data-category', category);
    
    if (category && window.skillsData && window.skillsData[category]) {
        window.skillsData[category].forEach(skill => {
            const option = document.createElement('option');
            option.value = skill.id;
            option.textContent = skill.name;
            skillSelect.appendChild(option);
        });
    }
    
    // Показываем/скрываем кастомный ввод
    if (category) {
        customInput.style.display = 'block';
    } else {
        customInput.style.display = 'none';
    }
}

// Инициализация автодополнения для языков
function initLanguageAutocomplete(item) {
    const searchInput = item.querySelector('.language-search');
    const dropdown = item.querySelector('.autocomplete-dropdown');
    const select = item.querySelector('.language-select');
    
    if (!searchInput || !dropdown || !select) return;
    
    searchInput.addEventListener('focus', function() {
        this.classList.add('active');
        loadLanguages(this.value);
    });
    
    searchInput.addEventListener('input', function() {
        loadLanguages(this.value);
    });
    
    function loadLanguages(query) {
        if (query.length < 1) {
            dropdown.classList.remove('active');
            return;
        }
        
        fetch(`api/autocomplete.php?type=language&query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.results.length > 0) {
                    dropdown.innerHTML = '';
                    data.results.forEach(lang => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.textContent = lang.name;
                        div.onclick = function() {
                            select.value = lang.id;
                            searchInput.value = lang.name;
                            dropdown.classList.remove('active');
                            searchInput.classList.remove('active');
                        };
                        dropdown.appendChild(div);
                    });
                    dropdown.classList.add('active');
                } else {
                    dropdown.classList.remove('active');
                }
            })
            .catch(error => {
                console.error('Error loading languages:', error);
            });
    }
    
    // Закрытие при клике вне
    document.addEventListener('click', function(e) {
        if (!item.contains(e.target)) {
            dropdown.classList.remove('active');
            searchInput.classList.remove('active');
        }
    });
}

// Инициализация всех автодополнений при загрузке
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.language-item').forEach(item => {
        initLanguageAutocomplete(item);
    });
});

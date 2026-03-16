// Автодополнение для различных полей

class Autocomplete {
    constructor(input, options = {}) {
        this.input = input;
        this.options = {
            type: options.type || 'city',
            minLength: options.minLength || 2,
            delay: options.delay || 300,
            onSelect: options.onSelect || null,
            ...options
        };
        
        this.dropdown = null;
        this.timeout = null;
        this.init();
    }
    
    init() {
        // Создаем dropdown если его нет
        if (!this.input.nextElementSibling || !this.input.nextElementSibling.classList.contains('autocomplete-dropdown')) {
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'autocomplete-dropdown';
            this.input.parentElement.appendChild(this.dropdown);
        } else {
            this.dropdown = this.input.nextElementSibling;
        }
        
        // Обработчики событий
        this.input.addEventListener('input', () => this.handleInput());
        this.input.addEventListener('focus', () => this.handleInput());
        this.input.addEventListener('blur', () => {
            // Задержка для обработки клика по элементу
            setTimeout(() => {
                this.hideDropdown();
            }, 200);
        });
        
        // Закрытие при клике вне
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }
    
    handleInput() {
        const query = this.input.value.trim();
        
        if (query.length < this.options.minLength) {
            this.hideDropdown();
            return;
        }
        
        // Задержка для уменьшения количества запросов
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => {
            this.search(query);
        }, this.options.delay);
    }
    
    async search(query) {
        try {
            const url = `api/autocomplete.php?type=${this.options.type}&query=${encodeURIComponent(query)}`;
            if (this.options.category) {
                url += `&category=${this.options.category}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.success && data.results.length > 0) {
                this.showResults(data.results);
            } else {
                this.hideDropdown();
            }
        } catch (error) {
            console.error('Autocomplete error:', error);
            this.hideDropdown();
        }
    }
    
    showResults(results) {
        this.dropdown.innerHTML = '';
        
        results.forEach(result => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            
            // Формируем текст в зависимости от типа
            let text = result.name || result.title || '';
            if (result.country) {
                text += ` (${result.country})`;
            }
            if (result.category) {
                text += ` [${result.category}]`;
            }
            
            item.textContent = text;
            item.onclick = () => {
                this.selectItem(result);
            };
            
            this.dropdown.appendChild(item);
        });
        
        this.dropdown.classList.add('active');
    }
    
    selectItem(item) {
        this.input.value = item.name || item.title || '';
        this.hideDropdown();
        
        if (this.options.onSelect) {
            this.options.onSelect(item);
        }
        
        // Если есть связанное поле (например, ID), заполняем его
        const hiddenInput = this.input.parentElement.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.value = item.id;
        }
    }
    
    hideDropdown() {
        if (this.dropdown) {
            this.dropdown.classList.remove('active');
        }
    }
}

// Инициализация автодополнения для всех полей с data-autocomplete
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-autocomplete]').forEach(input => {
        const type = input.getAttribute('data-autocomplete-type') || 'city';
        new Autocomplete(input, {
            type: type,
            onSelect: (item) => {
                // Дополнительная логика при выборе
                console.log('Selected:', item);
            }
        });
    });
    
    // Специальная инициализация для полей городов
    document.querySelectorAll('.city-input, input[placeholder*="город"], input[placeholder*="Город"]').forEach(input => {
        new Autocomplete(input, {
            type: 'city',
            minLength: 2
        });
    });
    
    // Специальная инициализация для полей профессий
    document.querySelectorAll('.profession-input, input[placeholder*="професси"], input[placeholder*="Професси"]').forEach(input => {
        new Autocomplete(input, {
            type: 'profession',
            minLength: 2
        });
    });
});

-- Схема базы данных для портрета личности

CREATE DATABASE IF NOT EXISTS portrait_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portrait_db;

-- Основная таблица портретов
CREATE TABLE IF NOT EXISTS portraits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('draft', 'completed') DEFAULT 'draft',
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица для хранения данных по параметрам
CREATE TABLE IF NOT EXISTS portrait_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portrait_id INT NOT NULL,
    param_number INT NOT NULL,
    structured_data JSON,
    free_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (portrait_id) REFERENCES portraits(id) ON DELETE CASCADE,
    UNIQUE KEY unique_param (portrait_id, param_number),
    INDEX idx_portrait (portrait_id),
    INDEX idx_param (param_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Встречи по портрету (человеку)
CREATE TABLE IF NOT EXISTS portrait_meetings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    portrait_id INT NOT NULL,
    meeting_date DATE NOT NULL,
    with_whom VARCHAR(255) DEFAULT '',
    description TEXT,
    FOREIGN KEY (portrait_id) REFERENCES portraits(id) ON DELETE CASCADE,
    INDEX idx_portrait_meetings_portrait_date (portrait_id, meeting_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Справочник языков
CREATE TABLE IF NOT EXISTS languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL UNIQUE,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Справочник городов
CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    INDEX idx_name (name),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Справочник профессий
CREATE TABLE IF NOT EXISTS professions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100),
    INDEX idx_name (name),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Справочник навыков
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category ENUM('hard', 'soft') NOT NULL,
    INDEX idx_name (name),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Справочник образовательных учреждений
CREATE TABLE IF NOT EXISTS educational_institutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    type ENUM('school', 'college', 'university', 'course', 'self') NOT NULL,
    city VARCHAR(100),
    INDEX idx_name (name),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Заполнение справочника языков
INSERT INTO languages (name, code) VALUES
('Русский', 'ru'),
('Английский', 'en'),
('Немецкий', 'de'),
('Французский', 'fr'),
('Испанский', 'es'),
('Итальянский', 'it'),
('Китайский', 'zh'),
('Японский', 'ja'),
('Корейский', 'ko'),
('Арабский', 'ar'),
('Португальский', 'pt'),
('Турецкий', 'tr'),
('Польский', 'pl'),
('Чешский', 'cs'),
('Греческий', 'el')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Заполнение справочника городов (основные города России и мира)
INSERT INTO cities (name, country) VALUES
('Москва', 'Россия'),
('Санкт-Петербург', 'Россия'),
('Екатеринбург', 'Россия'),
('Новосибирск', 'Россия'),
('Казань', 'Россия'),
('Нижний Новгород', 'Россия'),
('Челябинск', 'Россия'),
('Самара', 'Россия'),
('Омск', 'Россия'),
('Ростов-на-Дону', 'Россия'),
('Лондон', 'Великобритания'),
('Париж', 'Франция'),
('Берлин', 'Германия'),
('Нью-Йорк', 'США'),
('Токио', 'Япония'),
('Пекин', 'Китай')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Заполнение справочника профессий
INSERT INTO professions (name, category) VALUES
('Программист', 'IT'),
('Дизайнер', 'Creative'),
('Менеджер', 'Management'),
('Врач', 'Healthcare'),
('Учитель', 'Education'),
('Инженер', 'Engineering'),
('Бухгалтер', 'Finance'),
('Юрист', 'Legal'),
('Маркетолог', 'Marketing'),
('Психолог', 'Healthcare'),
('Переводчик', 'Language'),
('Журналист', 'Media'),
('Архитектор', 'Creative'),
('Повар', 'Service'),
('Экономист', 'Finance')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Заполнение справочника навыков
INSERT INTO skills (name, category) VALUES
('Python', 'hard'),
('JavaScript', 'hard'),
('PHP', 'hard'),
('Java', 'hard'),
('C++', 'hard'),
('SQL', 'hard'),
('HTML/CSS', 'hard'),
('React', 'hard'),
('Vue.js', 'hard'),
('Node.js', 'hard'),
('Коммуникация', 'soft'),
('Лидерство', 'soft'),
('Работа в команде', 'soft'),
('Тайм-менеджмент', 'soft'),
('Креативность', 'soft'),
('Стрессоустойчивость', 'soft'),
('Эмпатия', 'soft'),
('Публичные выступления', 'soft'),
('Решение проблем', 'soft'),
('Адаптивность', 'soft')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Схема базы данных для портрета личности (PostgreSQL)

-- Типы для ENUM
CREATE TYPE portrait_status AS ENUM ('draft', 'completed');
CREATE TYPE skill_category AS ENUM ('hard', 'soft');
CREATE TYPE institution_type AS ENUM ('school', 'college', 'university', 'course', 'self');

-- Основная таблица портретов
CREATE TABLE portraits (
    id SERIAL PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status portrait_status DEFAULT 'draft'
);
CREATE INDEX idx_portraits_status ON portraits (status);
CREATE INDEX idx_portraits_created ON portraits (created_at);

-- Таблица для хранения данных по параметрам
CREATE TABLE portrait_data (
    id SERIAL PRIMARY KEY,
    portrait_id INT NOT NULL REFERENCES portraits(id) ON DELETE CASCADE,
    param_number INT NOT NULL,
    structured_data JSONB,
    free_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (portrait_id, param_number)
);
CREATE INDEX idx_portrait_data_portrait ON portrait_data (portrait_id);
CREATE INDEX idx_portrait_data_param ON portrait_data (param_number);

-- Триггер для updated_at на portraits
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER portraits_updated_at
    BEFORE UPDATE ON portraits FOR EACH ROW EXECUTE PROCEDURE set_updated_at();
CREATE TRIGGER portrait_data_updated_at
    BEFORE UPDATE ON portrait_data FOR EACH ROW EXECUTE PROCEDURE set_updated_at();

-- Встречи по портрету (человеку)
CREATE TABLE portrait_meetings (
    id SERIAL PRIMARY KEY,
    portrait_id INT NOT NULL REFERENCES portraits(id) ON DELETE CASCADE,
    meeting_date DATE NOT NULL,
    with_whom VARCHAR(255) DEFAULT '',
    description TEXT DEFAULT ''
);
CREATE INDEX idx_portrait_meetings_portrait_date ON portrait_meetings (portrait_id, meeting_date);

-- Справочник языков
CREATE TABLE languages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL UNIQUE
);
CREATE INDEX idx_languages_name ON languages (name);

-- Справочник городов
CREATE TABLE cities (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL
);
CREATE INDEX idx_cities_name ON cities (name);
CREATE INDEX idx_cities_country ON cities (country);

-- Справочник профессий
CREATE TABLE professions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100)
);
CREATE INDEX idx_professions_name ON professions (name);
CREATE INDEX idx_professions_category ON professions (category);

-- Справочник навыков
CREATE TABLE skills (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category skill_category NOT NULL
);
CREATE INDEX idx_skills_name ON skills (name);
CREATE INDEX idx_skills_category ON skills (category);

-- Справочник образовательных учреждений
CREATE TABLE educational_institutions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    type institution_type NOT NULL,
    city VARCHAR(100)
);
CREATE INDEX idx_educational_institutions_name ON educational_institutions (name);
CREATE INDEX idx_educational_institutions_type ON educational_institutions (type);

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
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name;

-- Заполнение справочника городов
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
('Пекин', 'Китай');

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
('Экономист', 'Finance');

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
('Адаптивность', 'soft');

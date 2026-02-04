# База данных

Используется **PostgreSQL 16**. Схема инициализируется при первом запуске контейнера из `sql/schema.pgsql`. Таблицы `portrait_meetings` и `tasks` дополнительно создаются при первом подключении приложения (см. `config/database.php`), если их ещё нет.

## Основные таблицы

### portraits

Портрет личности (анкета).

| Колонка     | Тип        | Описание                    |
|------------|------------|-----------------------------|
| id         | SERIAL     | PK                          |
| created_at | TIMESTAMP  | По умолчанию CURRENT_TIMESTAMP |
| updated_at | TIMESTAMP  | Обновляется триггером       |
| status     | portrait_status | ENUM: `draft`, `completed` |

Индексы: `status`, `created_at`.

### portrait_data

Данные по параметрам портрета (1–25). Одна строка на (portrait_id, param_number).

| Колонка        | Тип    | Описание                    |
|----------------|--------|-----------------------------|
| id             | SERIAL | PK                          |
| portrait_id    | INT    | FK → portraits(id) ON DELETE CASCADE |
| param_number   | INT    | Номер параметра (1–25)      |
| structured_data| JSONB  | Структурированные поля      |
| free_text      | TEXT   | Произвольный текст          |
| created_at, updated_at | TIMESTAMP | |

UNIQUE (portrait_id, param_number). Индексы: portrait_id, param_number.

### portrait_meetings

Встречи по портрету (человеку).

| Колонка      | Тип         | Описание                    |
|-------------|-------------|-----------------------------|
| id          | SERIAL      | PK                          |
| portrait_id | INT         | FK → portraits(id) ON DELETE CASCADE |
| meeting_date| DATE        | Дата встречи                |
| with_whom   | VARCHAR(255)| С кем                       |
| description | TEXT        | Описание                    |

Индекс: (portrait_id, meeting_date).

### tasks

Задачи (дерево: parent_id), с опциональной привязкой к портрету.

| Колонка     | Тип     | Описание                    |
|------------|---------|-----------------------------|
| id         | SERIAL  | PK                          |
| parent_id  | INT     | FK → tasks(id) ON DELETE CASCADE, nullable |
| title      | VARCHAR(500) | Название                |
| due_date   | DATE    | nullable                    |
| portrait_id| INT     | FK → portraits(id) ON DELETE SET NULL, nullable |
| sort_order | INT     | Порядок (по умолчанию 0)    |
| created_at, updated_at | TIMESTAMP | |

Индексы: parent_id, due_date, portrait_id.

### telegram_subscribers

Чаты, подписанные на уведомления бота.

| Колонка   | Тип        |
|----------|------------|
| chat_id  | BIGINT PK  |
| created_at | TIMESTAMP |

## Справочники

- **languages** — id, name, code (языки для анкеты).
- **cities** — id, name, country (места рождения/проживания, автодополнение).
- **professions** — id, name, category.
- **skills** — id, name, category (ENUM: hard, soft).
- **educational_institutions** — id, name, type (ENUM: school, college, university, course, self), city.

Типы ENUM заданы в `schema.pgsql`: portrait_status, skill_category, institution_type.

## Триггеры

- `portraits.updated_at` и `portrait_data.updated_at` обновляются функцией `set_updated_at()` при UPDATE.

## Миграции

- **Существующая БД без таблицы подписчиков:**  
  `psql -U portrait_user -d portrait_db -p 5433 -f sql/telegram_subscribers_migration.pgsql`

- **Таблицы tasks / portrait_meetings** создаются кодом в `config/database.php` при первом подключении; при использовании полного `schema.pgsql` в docker-entrypoint они уже есть. Если поднимаете БД вручную без schema.pgsql — миграции для tasks см. в `sql/tasks_migration.pgsql` (если файл есть).

## Подключение

Переменные окружения (Docker или локально):

- `DB_HOST` (по умолчанию 127.0.0.1)
- `DB_PORT` (по умолчанию 5433)
- `DB_NAME` (по умолчанию portrait_db)
- `DB_USER` (по умолчанию portrait_user)
- `DB_PASS` (по умолчанию portrait_pass)

В Docker Compose сервис `db` отдаёт порт 5433, сервисы `app` и `telegram-bot` подключаются к хосту `db`.

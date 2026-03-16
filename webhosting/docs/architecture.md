# Архитектура проекта

## Обзор

Приложение состоит из трёх частей:

1. **Веб-приложение (PHP)** — страницы и API для портретов, списка, просмотра, встреч и задач.
2. **База данных (PostgreSQL)** — портреты, данные по параметрам, встречи, задачи, справочники, подписчики Telegram.
3. **Telegram-бот (Python)** — подписка на уведомления, рассылка по расписанию (дни рождения, задачи).

Все сервисы могут работать в Docker Compose; приложение и бот подключаются к одной БД.

## Структура каталогов

```
TaskManagerCartotheka/
├── api/                    # API endpoints (JSON)
│   ├── autocomplete.php    # Автодополнение (языки, города, профессии, навыки, портреты)
│   ├── list.php            # Список портретов с фильтрами
│   ├── load.php            # Загрузка данных портрета по ID
│   ├── meetings.php        # Встречи по портрету (GET/POST/PUT)
│   ├── save.php            # Сохранение данных портрета (POST)
│   └── tasks.php           # Задачи (GET/POST/PUT/DELETE)
├── components/             # Переиспользуемые PHP-компоненты
│   ├── education.php       # Блок «Образование»
│   ├── footer.php
│   ├── header.php
│   ├── languages.php       # Блок «Языки»
│   ├── skills.php          # Блок «Навыки»
│   └── ...
├── config/
│   ├── config.php          # Константы, пути, сессия, хелперы
│   └── database.php        # PDO PostgreSQL, синглтон, создание таблиц при первом подключении
├── modules/                # Модули параметров портрета (1–25)
│   ├── param-1.php         # Базовые демографические данные (ФИО, дата рождения, пол, гражданство и т.д.)
│   ├── param-2.php … param-25.php
│   └── ...
├── scripts/                # Клиентский JavaScript
│   ├── autocomplete.js     # Общая логика автодополнения (data-autocomplete)
│   ├── form-handler.js     # Сбор и отправка формы портрета (saveForm)
│   ├── list.js             # Список портретов, фильтры, загрузка с api/list.php
│   ├── main.js             # Секции (toggle), языки/образование/навыки (add/remove), автодополнение языков
│   ├── meetings.js         # Встречи: форма, список, редактирование
│   └── tasks.js            # Дерево задач, модальное окно, привязка к портрету
├── sql/
│   ├── schema.pgsql        # Полная схема БД (при инициализации контейнера)
│   ├── schema.sql          # Вариант для другой СУБД (если есть)
│   ├── tasks_migration.pgsql
│   └── telegram_subscribers_migration.pgsql
├── styles/
│   ├── common.css
│   └── components.css
├── telegram_bot/           # Python-бот (отдельный образ)
│   ├── bot.py              # Long polling, команды /start, /morning, /evening
│   ├── config.py           # Переменные окружения
│   ├── db.py               # Подключение к PostgreSQL
│   ├── notify.py           # Рассылка: --morning, --evening, --tasks-morning и т.д.
│   ├── Dockerfile
│   ├── env.example
│   ├── requirements.txt
│   └── README.md
├── index.php               # Форма портрета (создание/редактирование)
├── list.php                # Страница списка портретов
├── view.php                # Просмотр портрета (только чтение)
├── meetings.php            # Встречи по портрету
├── tasks.php               # Менеджер задач
├── docker-compose.yml      # app (PHP), db (PostgreSQL), telegram-bot
├── Dockerfile              # PHP 8.2 + Apache + pdo_pgsql
└── setup.sh                # Проверка портов и запуск docker compose
```

## Поток данных

### Портрет

- **Создание:** `index.php` (без `id`) → форма → `saveForm()` → `api/save.php` (POST) → создаётся запись в `portraits` и строки в `portrait_data`.
- **Редактирование:** `index.php?id=N` загружает данные из БД, подключает модули `param-*.php` с `$paramData`; сохранение — снова `api/save.php` с `portrait_id`.
- **Просмотр:** `view.php?id=N` — те же секции и модули, но `$readOnly = true`; выводятся только блоки с данными (`param_has_content`).
- **Список:** `list.php` → `scripts/list.js` запрашивает `api/list.php` с параметрами `search`, `status`, `date_from`, `date_to`; таблица строится на клиенте.

### Встречи

- Страница `meetings.php?id=N` (N — portrait_id). Форма и список работают через `api/meetings.php`: GET — список встреч, POST — добавление, PUT — обновление.

### Задачи

- Страница `tasks.php`. Дерево и CRUD через `api/tasks.php`: GET (опционально `due_date`, `portrait_id`, `date_from`, `date_to`), POST (создание), PUT (обновление), DELETE. Привязка к портрету — через автодополнение по ФИО (`api/autocomplete.php?type=portrait`).

### Telegram-бот

- Подписчики хранятся в `telegram_subscribers`. Рассылка (дни рождения из param 1, задачи из `tasks`) выполняется по расписанию внутри бота (8:00, 20:00, 22:00 МСК) или вручную через `notify.py` (см. [telegram_bot/README.md](../telegram_bot/README.md)).

## Конфигурация

- **config/config.php:** `BASE_URL`, пути (`ROOT_PATH`, `MODULES_PATH`, `API_PATH` и т.д.), `param_has_content()`, `asset_version()`, сессия, таймзона (Europe/Moscow).
- **config/database.php:** параметры БД из переменных окружения (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`), синглтон `Database`, при первом подключении создаются таблицы `portrait_meetings` и `tasks`, если их ещё нет (остальная схема — из `sql/schema.pgsql` при старте контейнера БД).

## Безопасность

- API не реализует отдельную аутентификацию; доступ к приложению должен ограничиваться на уровне веб-сервера/прокси при необходимости.
- Все запросы к БД используют подготовленные выражения (PDO).
- Ввод выводится через `htmlspecialchars` в шаблонах.

## Масштабирование

- Один экземпляр бота на один токен (иначе 409 Conflict). Для горизонтального масштабирования веб-части достаточно нескольких инстансов приложения за балансировщиком; БД — общая.

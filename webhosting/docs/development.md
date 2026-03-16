# Установка и запуск

## Требования

- Docker и Docker Compose (рекомендуемый способ)
- либо: PHP 8.2 с расширениями pdo, pdo_pgsql; Apache с mod_rewrite; PostgreSQL 16; для бота — Python 3 и зависимости из `telegram_bot/requirements.txt`

## Запуск через Docker Compose

1. Клонируйте репозиторий и перейдите в корень проекта.

2. Создайте файл `.env` в корне (опционально, но нужно для бота):

   ```env
   TELEGRAM_BOT_TOKEN=123456:ABC-DEF...   # получить у @BotFather
   SITE_URL=http://localhost:8081         # или ваш публичный URL (для ссылок в сообщениях бота)
   ```

   Если не задать `TELEGRAM_BOT_TOKEN`, сервис `telegram-bot` может падать при старте; приложение и БД работают без него.

3. Запуск:

   ```bash
   docker compose up -d --build
   ```

   Или через скрипт (проверка портов 8081 и 5433, при необходимости освобождение):

   ```bash
   ./setup.sh
   ```

4. Откройте в браузере: **http://localhost:8081**

- **Приложение (PHP):** порт **8081**
- **PostgreSQL:** порт **5433** (пользователь `portrait_user`, БД `portrait_db`, пароль `portrait_pass`)
- **Telegram-бот:** работает внутри контейнера, наружу порты не пробрасываются

### Остановка

```bash
docker compose down
```

Сброс данных БД (включая тома):

```bash
docker compose down -v
docker compose up -d
```

## Переменные окружения

### Веб-приложение (сервис app)

Задаются в `docker-compose.yml` или через окружение:

| Переменная | Описание | По умолчанию |
|------------|----------|--------------|
| DB_HOST    | Хост PostgreSQL | db (в Docker) |
| DB_PORT    | Порт PostgreSQL | 5433 |
| DB_NAME    | Имя БД   | portrait_db |
| DB_USER    | Пользователь | portrait_user |
| DB_PASS    | Пароль   | portrait_pass |

При смене пароля БД пересоздайте том: `docker compose down -v && docker compose up -d`.

### Telegram-бот

| Переменная | Описание |
|------------|----------|
| TELEGRAM_BOT_TOKEN | Токен от @BotFather (обязательно для бота) |
| SITE_URL   | Публичный URL сайта (для ссылок в сообщениях) |
| DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS | Подключение к той же PostgreSQL |

Пример `.env` в корне (используется docker-compose):

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF...
SITE_URL=https://your-domain.com
```

## Локальный запуск без Docker

### БД

1. Установите PostgreSQL 16, создайте БД и пользователя (или используйте порт 5433 и имя/пароль как выше).
2. Выполните схему:  
   `psql -U portrait_user -d portrait_db -p 5433 -f sql/schema.pgsql`  
   При необходимости:  
   `psql ... -f sql/telegram_subscribers_migration.pgsql`

### PHP

1. Установите PHP 8.2 и расширения `pdo`, `pdo_pgsql`.
2. Настройте Apache (или встроенный сервер PHP) так, чтобы корнем документа был корень проекта. Включите mod_rewrite.
3. Задайте переменные окружения для БД или отредактируйте значения по умолчанию в `config/database.php` (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS).
4. Откройте в браузере `http://localhost/index.php` (или ваш виртуальный хост).

### Telegram-бот

См. [telegram_bot/README.md](../telegram_bot/README.md):  
`cd telegram_bot`, `cp env.example .env`, настройте `.env`, `pip install -r requirements.txt`, `python bot.py`. Рассылка по расписанию — встроенная (job_queue) или через cron и `notify.py`.

## Режим разработки

В `config/config.php` включены:

- `error_reporting(E_ALL)` и `display_errors = 1` — для отладки. В продакшене рекомендуется отключить вывод ошибок и логировать их.

Версионирование статики (сброс кэша при изменении файлов): функция `asset_version($path)` в config — подставляет `?v=<mtime>` к путям CSS/JS.

## Порты и конфликты

- **8081** — веб-приложение. Если порт занят, измените в `docker-compose.yml` маппинг `"8081:80"` на другой, например `"8082:80"`.
- **5433** — PostgreSQL. Аналогично измените маппинг и переменные `DB_PORT` у сервисов `app` и `telegram-bot`.
- Скрипт `setup.sh` проверяет занятость 8081 и 5433 и может предложить завершить процессы на этих портах (переменные `APP_PORT`, `POSTGRES_PORT`).

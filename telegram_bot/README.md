# Telegram-бот: уведомления о днях рождения

Бот присылает в подписанный чат:
- **8:00** — у кого день рождения **сегодня**;
- **20:00** — у кого день рождения **завтра**.

Данные берутся из портретов (param 1: ФИО и дата рождения).

## Запуск через Docker Compose (рекомендуется)

В корне проекта создайте или отредактируйте `.env`:

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF...
# Публичный URL приложения — в сообщениях бота будут ссылки на анкеты (view.php?id=...)
SITE_URL=https://your-domain.com
```

Запуск всех сервисов (приложение, БД и бот):

```bash
# из корня проекта
docker compose up -d
```

Бот поднимается вместе с `app` и `db`. Рассылка в 8:00 и 20:00 (Москва) выполняется внутри контейнера бота.

Проверка логов бота:

```bash
docker compose logs -f telegram-bot
```

## Установка (без Docker)

```bash
cd telegram_bot
cp env.example .env
# Отредактируйте .env: TELEGRAM_BOT_TOKEN (получить у @BotFather), при необходимости DB_*
pip install -r requirements.txt
```

Для существующей БД создайте таблицу подписчиков (если её ещё нет):

```bash
psql -U portrait_user -d portrait_db -p 5433 -f ../sql/telegram_subscribers_migration.pgsql
```

## Запуск бота (подписка на уведомления)

```bash
python bot.py
```

Пользователи пишут боту **/start** — чат добавляется в рассылку.

Команды по запросу:
- **/morning** — присылает утреннее сообщение (кто сегодня отмечает ДР);
- **/evening** — присылает вечернее сообщение (у кого завтра ДР).

## Рассылка по расписанию (cron)

Запускайте скрипт уведомлений в **8:00** и **20:00** (по московскому времени):

```cron
0 8 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --morning
0 20 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --evening
```

Или через одну задачу с проверкой времени внутри (альтернатива):

```cron
0 8,20 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && ( [ $(date +\%H) -eq 8 ] && python notify.py --morning || python notify.py --evening )
```

Рекомендуется два отдельных задания, как в первом варианте.

## Переменные окружения (.env)

| Переменная | Описание |
|------------|----------|
| `TELEGRAM_BOT_TOKEN` | Токен от @BotFather (обязательно) |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Подключение к PostgreSQL (как у основного приложения) |
| `TZ` | Часовой пояс для cron, например `Europe/Moscow` |

# Telegram-бот: уведомления о днях рождения и задачах

Бот присылает в подписанный чат:

**Дни рождения** (из портретов, param 1: ФИО и дата рождения):
- **8:00** — у кого день рождения **сегодня**;
- **20:00** — у кого день рождения **завтра**.

**Задачи** (из менеджера задач на сайте):
- **8:00** — задачи на **сегодня**;
- **20:00** — задачи на **завтра**;
- **22:00** — напоминание внести обновление по задачам за сегодня, к которым прикреплён человек.

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

Бот поднимается вместе с `app` и `db`. Рассылка в 8:00, 20:00 и 22:00 (Москва) выполняется внутри контейнера бота.

Проверка логов бота:

```bash
docker compose logs -f telegram-bot
```

**Ошибка 409 Conflict** («make sure that only one bot instance is running»): с этим токеном уже идёт другой long polling (второй контейнер, локальный `python bot.py`, другой сервер). Бот теперь при 409 ждёт 60 с и пробует снова. Убедитесь, что нигде больше не запущен этот же бот; при необходимости выдайте новый токен в @BotFather и укажите его в `.env`.

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

Если не используете встроенное расписание бота (job_queue), можно запускать скрипт вручную:

**Дни рождения:**
```cron
0 8 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --morning
0 20 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --evening
```

**Задачи (сегодня / завтра / напоминание в 22:00):**
```cron
0 8 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --tasks-morning
0 20 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --tasks-evening
0 22 * * * cd /path/to/TaskManagerCartotheka/telegram_bot && python notify.py --tasks-remind
```

Все времена — по московскому времени (настройте `TZ=Europe/Moscow` для cron при необходимости).

## Переменные окружения (.env)

| Переменная | Описание |
|------------|----------|
| `TELEGRAM_BOT_TOKEN` | Токен от @BotFather (обязательно) |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Подключение к PostgreSQL (как у основного приложения) |
| `TZ` | Часовой пояс для cron, например `Europe/Moscow` |

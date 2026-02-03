-- Миграция: таблица подписчиков Telegram (для уже существующих БД)
-- Выполнить: psql -U portrait_user -d portrait_db -p 5433 -f sql/telegram_subscribers_migration.pgsql

CREATE TABLE IF NOT EXISTS telegram_subscribers (
    chat_id BIGINT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

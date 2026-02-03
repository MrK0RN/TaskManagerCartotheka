-- Миграция: таблица задач (менеджер задач с иерархией подзадач)
-- Выполнить: psql -U portrait_user -d portrait_db -p 5433 -f sql/tasks_migration.pgsql

CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    parent_id INT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    title VARCHAR(500) NOT NULL DEFAULT '',
    due_date DATE NULL,
    portrait_id INT NULL REFERENCES portraits(id) ON DELETE SET NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tasks_parent ON tasks (parent_id);
CREATE INDEX IF NOT EXISTS idx_tasks_due_date ON tasks (due_date);
CREATE INDEX IF NOT EXISTS idx_tasks_portrait ON tasks (portrait_id);

CREATE TRIGGER tasks_updated_at
    BEFORE UPDATE ON tasks FOR EACH ROW EXECUTE PROCEDURE set_updated_at();

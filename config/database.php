<?php
// Подключение к базе данных (PostgreSQL)

require_once __DIR__ . '/config.php';

// Настройки из переменных окружения (Docker) или значения по умолчанию
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'portrait_db');
define('DB_USER', getenv('DB_USER') ?: 'portrait_user');
define('DB_PASS', getenv('DB_PASS') ?: 'portrait_pass');
define('DB_PORT', getenv('DB_PORT') ?: '5433');

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";options=--client_encoding=UTF8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            $this->ensureMeetingsTable();
            $this->ensureTasksTable();
        } catch (PDOException $e) {
            throw new Exception("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    /** Создаёт таблицу portrait_meetings при первом подключении, если её ещё нет */
    private function ensureMeetingsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS portrait_meetings (
                id SERIAL PRIMARY KEY,
                portrait_id INT NOT NULL REFERENCES portraits(id) ON DELETE CASCADE,
                meeting_date DATE NOT NULL,
                with_whom VARCHAR(255) DEFAULT '',
                description TEXT DEFAULT ''
            )
        ";
        $this->connection->exec($sql);
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_portrait_meetings_portrait_date
            ON portrait_meetings (portrait_id, meeting_date)
        ");
    }

    /** Создаёт таблицу tasks при первом подключении, если её ещё нет */
    private function ensureTasksTable() {
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS tasks (
                id SERIAL PRIMARY KEY,
                parent_id INT NULL REFERENCES tasks(id) ON DELETE CASCADE,
                title VARCHAR(500) NOT NULL DEFAULT '',
                due_date DATE NULL,
                portrait_id INT NULL REFERENCES portraits(id) ON DELETE SET NULL,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_tasks_parent ON tasks (parent_id)
        ");
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_tasks_due_date ON tasks (due_date)
        ");
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_tasks_portrait ON tasks (portrait_id)
        ");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

function getDB() {
    return Database::getInstance()->getConnection();
}

# API

Все ответы в кодировке UTF-8, формат JSON. Базовый путь относительно корня сайта: например, `api/save.php`.

---

## Портреты

### Сохранение данных портрета

**POST** `api/save.php`

Тело запроса — JSON:

- `portrait_id` (число или null) — при null создаётся новый портрет.
- `param_1` … `param_25` — объекты вида:
  - `structured_data` — объект с полями параметра (например, `fio`, `birth_date` для param 1).
  - `free_text` — строка (дополнительный текст).

Ответ:

```json
{
  "success": true,
  "message": "Данные успешно сохранены",
  "portrait_id": 1
}
```

При ошибке: `success: false`, `message` с текстом.

---

### Загрузка данных портрета

**GET** `api/load.php?id={portrait_id}`

Ответ:

```json
{
  "success": true,
  "message": "Данные успешно загружены",
  "data": {
    "param_1": { "structured_data": {...}, "free_text": "" },
    "param_2": { ... }
  },
  "portrait": {
    "id": 1,
    "status": "draft",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

При отсутствии `id` или портрета: `success: false`, `message`.

---

### Список портретов

**GET** `api/list.php`

Параметры (все опциональны):

| Параметр    | Описание                          |
|------------|-----------------------------------|
| `search`   | Поиск по ФИО (param 1) и по free_text |
| `status`   | `draft` или `completed`           |
| `date_from`| Фильтр по дате обновления (от)    |
| `date_to`  | Фильтр по дате обновления (до)    |

Ответ:

```json
{
  "success": true,
  "message": "OK",
  "items": [
    {
      "id": 1,
      "status": "draft",
      "created_at": "...",
      "updated_at": "...",
      "fio": "Иванов Иван"
    }
  ]
}
```

---

## Задачи

**Базовый URL:** `api/tasks.php`

### Получить дерево задач

**GET** `api/tasks.php`

Параметры (опционально):

| Параметр     | Описание                    |
|-------------|-----------------------------|
| `due_date`  | Фильтр по дате (YYYY-MM-DD) |
| `portrait_id` | Задачи, привязанные к портрету |
| `date_from` | Диапазон дат (от)           |
| `date_to`   | Диапазон дат (до)           |

Ответ:

```json
{
  "success": true,
  "message": "OK",
  "tasks": [
    {
      "id": 1,
      "parent_id": null,
      "title": "Задача",
      "due_date": "2025-02-10",
      "portrait_id": 2,
      "assignee_fio": "Петров П.",
      "sort_order": 0,
      "created_at": "...",
      "updated_at": "...",
      "children": [ ... ]
    }
  ]
}
```

### Создать задачу

**POST** `api/tasks.php`

Тело (JSON):

- `parent_id` (число или null)
- `title` (обязательно)
- `due_date` (строка или null)
- `portrait_id` (число или null)
- `sort_order` (число, по умолчанию 0)

Ответ: `success`, `message`, `task` — объект созданной задачи (в т.ч. `assignee_fio` при наличии `portrait_id`).

### Обновить задачу

**PUT** `api/tasks.php`

Тело (JSON): `id` (обязательно), остальные поля опционально: `parent_id`, `title`, `due_date`, `portrait_id`, `sort_order`.

Ответ: `success`, `message`, `task`.

### Удалить задачу

**DELETE** `api/tasks.php?id={task_id}`

Ответ: `success`, `message`. Дочерние задачи удаляются каскадно (БД).

---

## Встречи

**Базовый URL:** `api/meetings.php`

### Список встреч по портрету

**GET** `api/meetings.php?portrait_id={id}`

Ответ:

```json
{
  "success": true,
  "message": "OK",
  "meetings": [
    {
      "id": 1,
      "portrait_id": 1,
      "meeting_date": "2025-02-01",
      "with_whom": "Иванов",
      "description": "Обсуждение"
    }
  ]
}
```

### Добавить встречу

**POST** `api/meetings.php`

Тело (JSON): `portrait_id`, `meeting_date` (обязательно), `with_whom`, `description`.

Ответ: `success`, `message`, `meeting` — объект созданной встречи.

### Обновить встречу

**PUT** `api/meetings.php`

Тело (JSON): `id`, `portrait_id`, `meeting_date`, `with_whom`, `description`.

Ответ: `success`, `message`, `meeting`.

---

## Автодополнение

**GET** `api/autocomplete.php`

Параметры:

- `type` (обязательно): `language` | `city` | `profession` | `skill` | `institution` | `portrait`
- `query` — строка поиска
- Для `skill` опционально: `category` — `hard` или `soft`

Ответ:

```json
{
  "success": true,
  "results": [
    { "id": 1, "name": "...", "code": "..." },
    ...
  ]
}
```

Для `portrait` в результатах: `id` (portrait_id), `name` (ФИО из param 1). Для `city`: `id`, `name`, `country`. Остальные типы возвращают поля соответствующих таблиц (languages, professions, skills, educational_institutions). Лимит: 20 записей.

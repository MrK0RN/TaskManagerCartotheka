# -*- coding: utf-8 -*-
import psycopg2
from psycopg2.extras import RealDictCursor
from config import get_db_connection_params


def get_connection():
    return psycopg2.connect(**get_db_connection_params(), cursor_factory=RealDictCursor)


def subscribe_chat(chat_id: int) -> bool:
    """Добавить chat_id в подписчики. Возвращает True если добавлен, False если уже был."""
    with get_connection() as conn:
        with conn.cursor() as cur:
            cur.execute(
                "INSERT INTO telegram_subscribers (chat_id) VALUES (%s) ON CONFLICT (chat_id) DO NOTHING",
                (chat_id,),
            )
            conn.commit()
            return cur.rowcount > 0


def get_subscribers():
    """Список chat_id всех подписчиков."""
    with get_connection() as conn:
        with conn.cursor() as cur:
            cur.execute("SELECT chat_id FROM telegram_subscribers")
            return [row["chat_id"] for row in cur.fetchall()]


def get_birthdays_on_date(month: int, day: int):
    """
    Список людей с портретами, у которых день рождения в указанные месяц и день.
    Возвращает список dict: portrait_id, fio, birth_date (строка YYYY-MM-DD).
    """
    with get_connection() as conn:
        with conn.cursor() as cur:
            cur.execute(
                """
                SELECT pd.portrait_id,
                       COALESCE(pd.structured_data->>'fio', 'Без имени') AS fio,
                       (pd.structured_data->>'birth_date')::text AS birth_date
                FROM portrait_data pd
                WHERE pd.param_number = 1
                  AND pd.structured_data->>'birth_date' IS NOT NULL
                  AND pd.structured_data->>'birth_date' <> ''
                  AND EXTRACT(MONTH FROM (pd.structured_data->>'birth_date')::date) = %s
                  AND EXTRACT(DAY FROM (pd.structured_data->>'birth_date')::date) = %s
                ORDER BY fio
                """,
                (month, day),
            )
            return [dict(row) for row in cur.fetchall()]

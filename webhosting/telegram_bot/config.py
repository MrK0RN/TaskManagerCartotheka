# -*- coding: utf-8 -*-
import os

try:
    from dotenv import load_dotenv
    load_dotenv()
except ImportError:
    pass

TELEGRAM_BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN", "").strip()
SITE_URL = os.getenv("SITE_URL", "").strip().rstrip("/")  # Базовый URL приложения для ссылок на анкеты
DB_HOST = os.getenv("DB_HOST", "127.0.0.1")
DB_PORT = os.getenv("DB_PORT", "5433")
DB_NAME = os.getenv("DB_NAME", "portrait_db")
DB_USER = os.getenv("DB_USER", "portrait_user")
DB_PASS = os.getenv("DB_PASS", "portrait_pass")


def get_db_connection_params():
    return {
        "host": DB_HOST,
        "port": int(DB_PORT),
        "dbname": DB_NAME,
        "user": DB_USER,
        "password": DB_PASS,
        "options": "-c client_encoding=UTF8",
    }

# -*- coding: utf-8 -*-
"""
Скрипт рассылки уведомлений о днях рождения.
Запускать по cron:
  - В 8:00:  python notify.py --morning   (сегодня ДР)
  - В 20:00: python notify.py --evening  (завтра ДР)
"""
import argparse
import asyncio
import logging
from datetime import date, timedelta

from telegram import Bot
from config import TELEGRAM_BOT_TOKEN, SITE_URL
import db

logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)
logger = logging.getLogger(__name__)


def get_target_date(morning: bool) -> date:
    """Утро = сегодня, вечер = завтра."""
    today = date.today()
    return today if morning else today + timedelta(days=1)


def _age_years(birth_date: date, on_date: date) -> int:
    """Возраст в полных годах на дату on_date."""
    return on_date.year - birth_date.year


def _age_word(years: int) -> str:
    """Склонение: 21 год, 22 года, 25 лет."""
    if 11 <= years % 100 <= 14:
        return "лет"
    if years % 10 == 1:
        return "год"
    if years % 10 in (2, 3, 4):
        return "года"
    return "лет"


def _format_person(portrait_id: int, fio: str, birth_date_str: str, on_date: date, site_url: str) -> str:
    """Одна строка/блок для человека: ФИО, возраст, ссылка на анкету."""
    try:
        birth = date.fromisoformat(birth_date_str) if birth_date_str else None
    except (TypeError, ValueError):
        birth = None
    if birth:
        years = _age_years(birth, on_date)
        age_str = f", исполняется {years} {_age_word(years)}"
    else:
        age_str = ""
    link = ""
    if site_url and portrait_id:
        link = f"\n   📎 Анкета: {site_url}/view.php?id={portrait_id}"
    return f"• {fio}{age_str}{link}"


def build_message(people: list, target_date: date, morning: bool, site_url: str = "") -> str:
    """
    people: список dict с ключами portrait_id, fio, birth_date.
    target_date: дата (сегодня или завтра).
    site_url: базовый URL приложения для ссылок на view.php?id=...
    """
    if not people:
        if morning:
            return (
                "Доброе утро!\n\n"
                "Сегодня ни у кого из портретов в базе нет дня рождения.\n\n"
                "Команды: /morning — кто сегодня, /evening — кто завтра."
            )
        return (
            "Добрый вечер!\n\n"
            "Завтра ни у кого из портретов в базе нет дня рождения.\n\n"
            "Команды: /morning — кто сегодня, /evening — кто завтра."
        )
    if morning:
        header = "Доброе утро!\n\nСегодня день рождения:"
    else:
        header = "Добрый вечер!\n\nЗавтра день рождения:"
    lines = [header]
    for p in people:
        lines.append(_format_person(
            p.get("portrait_id"),
            p.get("fio") or "Без имени",
            p.get("birth_date") or "",
            target_date,
            site_url or SITE_URL,
        ))
    lines.append("")
    lines.append("Команды: /morning — кто сегодня, /evening — кто завтра.")
    return "\n".join(lines)


async def send_notifications(morning: bool) -> None:
    if not TELEGRAM_BOT_TOKEN:
        raise SystemExit("Укажите TELEGRAM_BOT_TOKEN в .env")
    target = get_target_date(morning)
    people = db.get_birthdays_on_date(target.month, target.day)
    text = build_message(people, target, morning)
    subscribers = db.get_subscribers()
    if not subscribers:
        logger.info("Нет подписчиков, рассылка не выполняется.")
        return
    bot = Bot(token=TELEGRAM_BOT_TOKEN)
    for chat_id in subscribers:
        try:
            await bot.send_message(chat_id=chat_id, text=text)
        except Exception as e:
            logger.exception("Send to %s: %s", chat_id, e)


def main() -> None:
    parser = argparse.ArgumentParser(description="Рассылка уведомлений о ДР")
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--morning", action="store_true", help="8:00 — кто сегодня")
    group.add_argument("--evening", action="store_true", help="20:00 — кто завтра")
    args = parser.parse_args()
    asyncio.run(send_notifications(morning=args.morning))


if __name__ == "__main__":
    main()

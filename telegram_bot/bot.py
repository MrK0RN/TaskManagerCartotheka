# -*- coding: utf-8 -*-
"""
Telegram-бот для подписки на уведомления о днях рождения.
Команды: /start — подписка, /morning — кто сегодня, /evening — кто завтра.
По расписанию: 8:00 и 20:00 (Москва) — рассылка подписчикам.
"""
import logging
import time as time_module
from datetime import time

import pytz
from telegram import Update
from telegram.error import Conflict
from telegram.ext import Application, CommandHandler, ContextTypes

from config import TELEGRAM_BOT_TOKEN, SITE_URL
import db
from notify import build_message, get_target_date

logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    level=logging.INFO,
)
logger = logging.getLogger(__name__)

MOSCOW = pytz.timezone("Europe/Moscow")


async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    chat_id = update.effective_chat.id
    try:
        is_new = db.subscribe_chat(chat_id)
        if is_new:
            await update.message.reply_text(
                "Вы подписаны на уведомления о днях рождения.\n\n"
                "Каждый день в 8:00 — кто отмечает ДР сегодня.\n"
                "Каждый день в 20:00 — у кого ДР завтра.\n\n"
                "Команды: /morning — кто сегодня, /evening — кто завтра."
            )
        else:
            await update.message.reply_text(
                "Вы уже подписаны на уведомления о днях рождения."
            )
    except Exception as e:
        logger.exception("Subscribe error")
        await update.message.reply_text("Ошибка подписки. Попробуйте позже.")


async def morning(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Утреннее сообщение: кто сегодня отмечает ДР."""
    try:
        target = get_target_date(morning=True)
        people = db.get_birthdays_on_date(target.month, target.day)
        text = build_message(people, target, morning=True, site_url=SITE_URL)
        await update.message.reply_text(text)
    except Exception as e:
        logger.exception("Morning command error")
        await update.message.reply_text("Ошибка. Попробуйте позже.")


async def evening(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Вечернее сообщение: у кого завтра ДР."""
    try:
        target = get_target_date(morning=False)
        people = db.get_birthdays_on_date(target.month, target.day)
        text = build_message(people, target, morning=False, site_url=SITE_URL)
        await update.message.reply_text(text)
    except Exception as e:
        logger.exception("Evening command error")
        await update.message.reply_text("Ошибка. Попробуйте позже.")


async def _send_scheduled(context: ContextTypes.DEFAULT_TYPE, morning: bool) -> None:
    """Рассылка по расписанию: утро 8:00 (сегодня) или вечер 20:00 (завтра)."""
    try:
        target = get_target_date(morning=morning)
        people = db.get_birthdays_on_date(target.month, target.day)
        text = build_message(people, target, morning, site_url=SITE_URL)
        subscribers = db.get_subscribers()
        if not subscribers:
            logger.info("Нет подписчиков, рассылка не выполняется.")
            return
        for chat_id in subscribers:
            try:
                await context.bot.send_message(chat_id=chat_id, text=text)
            except Exception as e:
                logger.exception("Send to %s: %s", chat_id, e)
    except Exception as e:
        logger.exception("Scheduled notification error: %s", e)


async def job_morning(context: ContextTypes.DEFAULT_TYPE) -> None:
    await _send_scheduled(context, morning=True)


async def job_evening(context: ContextTypes.DEFAULT_TYPE) -> None:
    await _send_scheduled(context, morning=False)


def main() -> None:
    if not TELEGRAM_BOT_TOKEN:
        raise SystemExit("Укажите TELEGRAM_BOT_TOKEN в .env")
    logger.info("Запуск бота... Команды: /start, /morning, /evening. Рассылка: 8:00 и 20:00 МСК")
    app = (
        Application.builder()
        .token(TELEGRAM_BOT_TOKEN)
        .post_init(_setup_jobs)
        .build()
    )
    app.add_handler(CommandHandler("start", start))
    app.add_handler(CommandHandler("morning", morning))
    app.add_handler(CommandHandler("evening", evening))

    # Пауза, чтобы предыдущее соединение getUpdates успело закрыться у Telegram
    time_module.sleep(10)

    while True:
        try:
            app.run_polling(allowed_updates=Update.ALL_TYPES)
            break
        except Conflict:
            logger.warning(
                "409 Conflict: с этим токеном уже работает другой экземпляр. "
                "Ждём 60 с и пробуем снова..."
            )
            time_module.sleep(60)


async def _setup_jobs(application: Application) -> None:
    """Регистрация ежедневных рассылок в 8:00 и 20:00 по Москве."""
    job_queue = application.job_queue
    if job_queue is None:
        logger.warning("Job queue недоступен, рассылка по расписанию отключена")
        return
    time_08 = time(8, 0, tzinfo=MOSCOW)
    time_20 = time(20, 0, tzinfo=MOSCOW)
    job_queue.run_daily(job_morning, time=time_08)
    job_queue.run_daily(job_evening, time=time_20)
    logger.info("Рассылка запланирована: 8:00 и 20:00 (Europe/Moscow)")


if __name__ == "__main__":
    main()

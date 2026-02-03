FROM python:3.13-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY config.py db.py notify.py bot.py ./

ENV TZ=Europe/Moscow
ENV PYTHONUNBUFFERED=1

CMD ["python", "bot.py"]

#!/usr/bin/env bash
set -e

# Порты из docker-compose.yml: приложение 8081, PostgreSQL 5433
APP_PORT="${APP_PORT:-8081}"
POSTGRES_PORT="${POSTGRES_PORT:-5433}"
PORTS_TO_CHECK="$APP_PORT $POSTGRES_PORT"

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

info()  { echo -e "${GREEN}[INFO]${NC} $*"; }
warn()  { echo -e "${YELLOW}[WARN]${NC} $*"; }
err()   { echo -e "${RED}[ERR]${NC} $*"; }

# Проверка, занят ли порт: ss -tulnp | grep <port>
is_port_in_use() {
  local port="$1"
  ss -tulnp 2>/dev/null | grep -q ":${port} "
}

# Получить PID процесса на порту (из вывода ss)
get_pid_on_port() {
  local port="$1"
  ss -tulnp 2>/dev/null | grep ":${port} " | sed -n 's/.*pid=\([0-9]*\).*/\1/p'
}

# Освободить порт: kill <PID>
free_port() {
  local port="$1"
  local pids
  pids=$(get_pid_on_port "$port")
  if [[ -n "$pids" ]]; then
    for pid in $pids; do
      if kill -0 "$pid" 2>/dev/null; then
        info "Завершаю процесс на порту $port (PID $pid)..."
        kill "$pid"
      fi
    done
    sleep 1
    if is_port_in_use "$port"; then
      err "Не удалось освободить порт $port."
      return 1
    fi
    info "Порт $port освобождён."
  fi
  return 0
}

# Проверка и при необходимости освобождение портов
check_and_free_ports() {
  local need_free=0
  for port in $PORTS_TO_CHECK; do
    if is_port_in_use "$port"; then
      warn "Порт $port занят."
      need_free=1
    fi
  done

  if [[ $need_free -eq 1 ]]; then
    echo
    read -r -p "Освободить занятые порты? (y/n, по умолчанию y): " answer
    answer="${answer:-y}"
    if [[ "$answer" =~ ^[yYдД] ]]; then
      for port in $PORTS_TO_CHECK; do
        if is_port_in_use "$port"; then
          free_port "$port" || exit 1
        fi
      done
    else
      err "Запуск отменён: порты заняты."
      exit 1
    fi
  else
    info "Порты свободны (проверены: $PORTS_TO_CHECK)."
  fi
}

# Запуск приложения
run_app() {
  info "Запуск Docker Compose..."
  if command -v docker &>/dev/null && docker compose version &>/dev/null; then
    docker compose up -d --build
  elif command -v docker-compose &>/dev/null; then
    docker-compose up -d --build
  else
    err "Не найден docker compose или docker-compose."
    exit 1
  fi
  echo
  info "Приложение: http://localhost:$APP_PORT"
  info "PostgreSQL:  localhost:$POSTGRES_PORT (portrait_db, portrait_user)"
}

# Остановка (опционально)
stop_app() {
  info "Остановка контейнеров..."
  if command -v docker &>/dev/null && docker compose version &>/dev/null; then
    docker compose down
  else
    docker-compose down
  fi
}

case "${1:-}" in
  stop)
    stop_app
    ;;
  restart)
    stop_app
    check_and_free_ports
    run_app
    ;;
  *)
    check_and_free_ports
    run_app
    ;;
esac

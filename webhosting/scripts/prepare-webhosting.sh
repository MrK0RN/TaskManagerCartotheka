#!/usr/bin/env bash
# Формирует папку webhosting/ — копию сайта для развёртывания на хостинге с PostgreSQL (без Docker).
# Запускайте из корня репозитория.

set -e

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_ROOT"

WEBHOSTING_DIR="$REPO_ROOT/webhosting"

# Каталоги и файлы, которые копируем из корня
DIRS=(api components config modules scripts styles sql docs telegram_bot)
ROOT_FILES=(index.php view.php list.php meetings.php tasks.php portrait.html)

echo "[INFO] Создаю/очищаю webhosting/..."
rm -rf "$WEBHOSTING_DIR"
mkdir -p "$WEBHOSTING_DIR"

for d in "${DIRS[@]}"; do
  if [[ ! -d "$REPO_ROOT/$d" ]]; then
    echo "[WARN] Каталог $d не найден, пропуск."
    continue
  fi
  echo "[INFO] Копирую $d/ ..."
  if [[ "$d" == "config" ]]; then
    # Не копируем config/local.php (секреты)
    if command -v rsync &>/dev/null; then
      rsync -a --exclude='local.php' "$REPO_ROOT/$d/" "$WEBHOSTING_DIR/$d/"
    else
      mkdir -p "$WEBHOSTING_DIR/$d"
      for f in "$REPO_ROOT/$d"/*; do
        [[ -e "$f" && "$(basename "$f")" != "local.php" ]] && cp -r "$f" "$WEBHOSTING_DIR/$d/"
      done
    fi
  else
    if command -v rsync &>/dev/null; then
      rsync -a "$REPO_ROOT/$d/" "$WEBHOSTING_DIR/$d/"
    else
      cp -r "$REPO_ROOT/$d" "$WEBHOSTING_DIR/"
    fi
  fi
done

for f in "${ROOT_FILES[@]}"; do
  if [[ -f "$REPO_ROOT/$f" ]]; then
    echo "[INFO] Копирую $f"
    cp "$REPO_ROOT/$f" "$WEBHOSTING_DIR/"
  else
    echo "[WARN] Файл $f не найден, пропуск."
  fi
done

echo "[INFO] Добавляю README и .htaccess для webhosting..."
cp "$REPO_ROOT/scripts/webhosting-README.md" "$WEBHOSTING_DIR/README.md"
cp "$REPO_ROOT/scripts/webhosting.htaccess" "$WEBHOSTING_DIR/.htaccess"

echo "[INFO] Готово: $WEBHOSTING_DIR"

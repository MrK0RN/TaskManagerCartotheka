#!/usr/bin/env bash
# Проверяет, что файлы в webhosting/ идентичны соответствующим файлам в корне репозитория.
# Исключения: в webhosting допускаются только README.md и .htaccess (они специфичны для webhosting).
# Выход: 0 — идентичны, 1 — есть расхождения.

set -e

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_ROOT"

WEBHOSTING_DIR="$REPO_ROOT/webhosting"

if [[ ! -d "$WEBHOSTING_DIR" ]]; then
  echo "[ERR] Папка webhosting/ не найдена. Сначала выполните: ./scripts/prepare-webhosting.sh" >&2
  exit 1
fi

FAILED=0

while IFS= read -r -d '' f; do
  rel="${f#$WEBHOSTING_DIR/}"
  # Файлы, которые есть только в webhosting и не сравниваются с корнем
  if [[ "$rel" == "README.md" || "$rel" == ".htaccess" ]]; then
    continue
  fi
  root_file="$REPO_ROOT/$rel"
  if [[ ! -f "$root_file" ]]; then
    echo "[DIFF] В корне нет файла: $rel" >&2
    FAILED=1
    continue
  fi
  if ! diff -q "$root_file" "$f" &>/dev/null; then
    echo "[DIFF] Расхождение: $rel" >&2
    FAILED=1
  fi
done < <(find "$WEBHOSTING_DIR" -type f -print0)

if [[ $FAILED -eq 1 ]]; then
  echo "[ERR] Обнаружены расхождения. Версии должны быть идентичны." >&2
  exit 1
fi

echo "[OK] Версии корня и webhosting идентичны (с учётом README.md и .htaccess)."

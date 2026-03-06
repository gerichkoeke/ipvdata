#!/usr/bin/env bash
set -euo pipefail

REMOTE="${1:-origin}"
BRANCH="${2:-main}"
APPLY_STASH="${APPLY_STASH:-1}" # 1=pop stash after update, 0=keep stashed

if ! git rev-parse --git-dir >/dev/null 2>&1; then
  echo "[ERRO] Diretório atual não é um repositório git." >&2
  exit 1
fi

TIMESTAMP="$(date +%F-%H%M%S)"
STASH_NAME="pre-deploy-${TIMESTAMP}"

echo "[INFO] branch atual: $(git rev-parse --abbrev-ref HEAD)"
echo "[INFO] remoto/alvo: ${REMOTE}/${BRANCH}"

echo "[INFO] Verificando alterações locais..."
if [[ -n "$(git status --porcelain)" ]]; then
  echo "[INFO] Alterações locais detectadas. Criando stash: ${STASH_NAME}"
  git stash push -u -m "${STASH_NAME}" >/dev/null
  STASH_CREATED=1
else
  echo "[INFO] Working tree limpo."
  STASH_CREATED=0
fi

echo "[INFO] Atualizando refs remotas..."
git fetch "${REMOTE}" "${BRANCH}"

echo "[INFO] Sincronizando com ${REMOTE}/${BRANCH} (fast-forward only)..."
git pull --ff-only "${REMOTE}" "${BRANCH}"

if [[ "${STASH_CREATED}" -eq 1 && "${APPLY_STASH}" -eq 1 ]]; then
  echo "[INFO] Reaplicando alterações locais (git stash pop)..."
  if ! git stash pop; then
    echo "[ALERTA] Conflito ao reaplicar stash. Resolva os conflitos e faça commit." >&2
    exit 2
  fi
elif [[ "${STASH_CREATED}" -eq 1 ]]; then
  echo "[INFO] Stash preservado. Para reaplicar depois, use: git stash list && git stash pop"
fi

echo "[OK] Atualização concluída."

# Playbook de Deploy (Git) — Produção

Este playbook evita bloqueios no `git pull` quando há alterações locais no servidor.

## 1) Pré-check obrigatório

```bash
pwd
whoami
git rev-parse --abbrev-ref HEAD
git status --short
git remote -v
```

Se `git status --short` retornar algo, **não rode pull/rebase direto**.

---

## 2) Fluxo recomendado (seguro)

### Opção A — preservar alterações locais temporariamente (stash)

```bash
# 1) guarda alterações locais (inclui untracked)
git stash push -u -m "pre-deploy-$(date +%F-%H%M)"

# 2) atualiza referência remota
git fetch origin main

# 3) atualiza branch local
# escolha UM:
git pull --ff-only origin main
# ou
git rebase origin/main

# 4) reaplica alterações locais (se necessário)
git stash pop
```

> Se houver conflito no `stash pop`, resolver e commitar.

### Opção B — descartar alterações locais e sincronizar com remoto

```bash
git restore .
git clean -fd
git pull --ff-only origin main
```

> Use apenas quando tiver certeza que não precisa de nada local.

### Opção C — manter alterações locais como commit de segurança

```bash
git add -A
git commit -m "WIP: snapshot local before deploy"
git fetch origin main
git rebase origin/main
```

---

## 3) Padrão de deploy (aplicação Laravel)

Após atualizar código:

```bash
php artisan down || true
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

Se houver fila/workers:

```bash
php artisan queue:restart
```

---

## 4) Rollback rápido

```bash
git log --oneline -n 5
# escolher commit anterior estável
git reset --hard <commit_estavel>
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5) Diagnóstico do erro comum

Erro:

```text
error: cannot rebase: You have unstaged changes.
error: Please commit or stash them.
```

Causa: há modificações locais não commitadas.

Correção imediata:

```bash
git stash push -u -m "hotfix-pre-rebase"
git fetch origin main
git rebase origin/main
git stash pop
```

---

## 6) Boas práticas operacionais

- Preferir `git pull --ff-only` em produção para evitar merge commit inesperado.
- Nunca editar código manualmente no servidor sem registrar (`commit` ou `stash`).
- Criar uma branch para hotfix local e abrir PR, evitando divergência do `main`.
- Rodar `git status --short` antes de qualquer pull/rebase.

---

## 7) Cenário real (erro em `EditProfile.php`) e correção

Quando aparecer erro como:

```text
error: Your local changes to the following files would be overwritten by merge:
        app/Filament/Partner/Pages/Auth/EditProfile.php
Please commit your changes or stash them before you merge.
```

Execute:

```bash
git status --short
git stash push -u -m "hotfix-pre-pull"
git pull --ff-only origin main
git stash pop
```

Se também houver mudanças em migration (ex.: deletadas/localmente recriadas), validar antes do `stash pop` e depois resolver conflito com cuidado.

---

## 8) Script utilitário (recomendado)

Use o script do repositório para padronizar atualização segura:

```bash
./scripts/deploy/safe-git-update.sh origin main
```

Variáveis úteis:

- `APPLY_STASH=0 ./scripts/deploy/safe-git-update.sh` → atualiza e mantém stash guardado para reaplicar depois.

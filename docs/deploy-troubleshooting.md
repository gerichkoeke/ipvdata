# Troubleshooting de Deploy Git em Produção

## Erro: mudanças locais impedindo pull/rebase

Quando aparecer:

```text
error: cannot rebase: You have unstaged changes.
error: Please commit or stash them.
```

ou:

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

Se houver conflito no `stash pop`, resolva os conflitos, rode `git add -A` e finalize com commit.

## Script utilitário

Use o script para automatizar atualização segura:

```bash
./scripts/deploy/safe-git-update.sh origin main
```

Para manter stash sem reaplicar automaticamente:

```bash
APPLY_STASH=0 ./scripts/deploy/safe-git-update.sh origin main
```

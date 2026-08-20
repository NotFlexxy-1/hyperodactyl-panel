# Hyperodactyl

Hyperodactyl is a game server panel.
Everything that normally requires third-party themes or addons is **built in**.

## Built-in feature list

Client area: resource graphs, Monaco file editor, server notes, player list,
mod installer, plugin installer, scheduled restarts, console search.

Admin area: dashboard stats, node health monitor, bulk actions, maintenance banner.

Core: server splitter, nest marketplace, auto backup scheduler, per-server
subdomains, server transfer v2, audit log UI, per-key API rate limiting.

## Theme switching

```env
HYPERODACTYL_THEME=arix
HYPERODACTYL_THEME_ACCENT=#5b8cff
HYPERODACTYL_LOGO=/assets/hypernet-logo.png
HYPERODACTYL_BRAND=Hyperodactyl
```

## Install (same as upstream)

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan hyperodactyl:env
php artisan migrate --seed --force
yarn install && yarn build:production
```

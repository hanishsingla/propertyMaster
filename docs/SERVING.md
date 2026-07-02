# Serving PropertyMaster

The app is a Symfony backend that serves both the JSON API (`/api/*`), the
EasyAdmin panel (`/admin`), and the React SPA shell (everything else). The
frontend is built by Vite into `public/build/` and served as static files by
the same origin.

Pick the option that matches what you're doing.

---

## Option A — PHP built-in server (quickest, no config)

Good for a fast local run. A router script is required so SPA routes and the
API resolve (a bare `php -S -t public` only serves files, it won't route to the
front controller).

```bash
# from the project root
composer install
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
npm install && npm run build        # produces public/build/

php -S 127.0.0.1:8000 -t public public/router.php
```

Open http://127.0.0.1:8000

- `public/router.php` serves built assets/images/uploads directly and forwards
  everything else to `public/index.php`.
- On a memory-constrained machine, add `-d memory_limit=512M`.

---

## Option B — WAMP / Apache virtual host (recommended on this machine)

Matches a real deployment and avoids the built-in server entirely. `public/`
already ships a `.htaccess` (via symfony/apache-pack), so Apache routes for you.

1. Enable `mod_rewrite` and `mod_headers` in WAMP (left-click WAMP tray →
   Apache → Apache modules → tick `rewrite_module` and `headers_module`).

2. Add a vhost in `C:\wamp64\bin\apache\apache<ver>\conf\extra\httpd-vhosts.conf`:

   ```apache
   <VirtualHost *:80>
       ServerName propertymaster.local
       DocumentRoot "d:/wamp64/www/projects/propertyMaster/public"
       <Directory "d:/wamp64/www/projects/propertyMaster/public">
           AllowOverride All
           Require all granted
           FallbackResource /index.php
       </Directory>
   </VirtualHost>
   ```

3. Add to `C:\Windows\System32\drivers\etc\hosts`:

   ```
   127.0.0.1 propertymaster.local
   ```

4. Restart Apache (WAMP tray → Restart All Services).

Open http://propertymaster.local

Make sure the frontend is built first: `npm install && npm run build`.
`APP_ENV=prod` is recommended for this mode — set it in `.env.local`:

```
APP_ENV=prod
APP_DEBUG=0
```

and warm the cache: `php bin/console cache:clear` (then `assets:install` is not
needed — Vite already wrote to `public/build`).

---

## Option C — Full dev with Vite HMR (frontend work)

Run the backend AND the Vite dev server. When no production build manifest is
present (or you want live reload), the shell loads modules from Vite at
`http://localhost:5173` with hot-module reload.

```bash
# terminal 1 — backend (holds the session; API is same-origin)
php -S 127.0.0.1:8000 -t public public/router.php

# terminal 2 — Vite dev server (HMR)
npm run dev
```

Open the app via the **backend origin** (http://127.0.0.1:8000), not :5173, so
the session cookie and `/api` calls stay same-origin. `SpaController`
auto-detects: if `public/build/.vite/manifest.json` exists it serves the built
assets; otherwise it points at the Vite dev server.

> Tip: to force dev mode while a build exists, temporarily rename
> `public/build/.vite/manifest.json`.

---

## Seeded demo accounts (password: `password`)

- `admin@propertymaster.test` — admin, can open `/admin`
- `agent1@propertymaster.test` … `agent5@…` — agents (manage listings)
- `user1@propertymaster.test` … `user10@…` — regular users

## Health checks

```bash
curl -s http://127.0.0.1:8000/api/enums            # 200 JSON
curl -s http://127.0.0.1:8000/api/properties        # 200 list envelope
curl -s http://127.0.0.1:8000/                       # 200 SPA shell HTML
```

## Troubleshooting

- **404 on `/properties/...` (a client route)** — you're serving without the
  router/FallbackResource. Use `public/router.php` (Option A) or the Apache
  `FallbackResource /index.php` (Option B).
- **SPA loads but assets 404** — run `npm run build`; confirm
  `public/build/.vite/manifest.json` exists.
- **500 on every page after an env change** — clear the cache:
  `php bin/console cache:clear`.
- **`/admin` is slow / out-of-memory locally** — EasyAdmin renders many
  templates; raise PHP `memory_limit` (e.g. `-d memory_limit=1G`) and, on
  Windows, increase the system paging-file size.

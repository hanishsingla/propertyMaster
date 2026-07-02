# PropertyMaster

A real-estate listings platform, rebuilt as a **Symfony 7 JSON API** with a
**React 18 + Vite + TypeScript** single-page frontend. Auth is a same-origin
HttpOnly session cookie with double-submit CSRF. EasyAdmin remains for internal
administration (server-rendered).

## Stack

| Layer | Tech |
|---|---|
| Backend | PHP 8.4, Symfony 8.0, Doctrine ORM 3, MySQL 8 |
| API | JSON under `/api`, session-cookie auth, `X-CSRF-Token` on mutations, RFC7807-style errors |
| Frontend | React 18, Vite, TypeScript, TanStack Router + Query, Tailwind CSS, react-hook-form + zod, Leaflet |
| Admin | EasyAdmin 5 (`/admin`) |

Design docs live in [`docs/rebuild/`](docs/rebuild/): the data-model & API
contract and the phased implementation plan.

## Prerequisites

- PHP 8.1+ with `intl`, `pdo_mysql`
- Composer
- Node 18+ and npm
- MySQL 8 (a MySQL 5.7 works with a deprecation notice)

## Setup

```bash
# 1. PHP dependencies
composer install

# 2. Configure the database (edit DATABASE_URL in .env or .env.local)
#    default: mysql://root:@127.0.0.1:3306/app_property

# 3. Create schema + seed demo data
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# 4. Frontend dependencies + production build
npm install
npm run build        # outputs to public/build/ (+ .vite/manifest.json)
```

### Seeded demo accounts (password: `password`)

- `admin@propertymaster.test` — admin (EasyAdmin at `/admin`)
- `agent1@propertymaster.test` … `agent5@…` — agents (can manage listings)
- `user1@propertymaster.test` … `user10@…` — regular users

## Running

**Production-like** (Symfony serves the built SPA + API):

```bash
symfony serve            # or: php -S 127.0.0.1:8000 -t public
```

The `SpaController` reads `public/build/.vite/manifest.json` and serves the
hashed assets. Visit `http://localhost:8000`.

**Development** (Vite HMR): run the Symfony server *and* the Vite dev server.
When no build manifest is present, the shell auto-loads modules from the Vite
dev server at `http://localhost:5173`.

```bash
symfony serve            # backend + SPA shell (same origin, holds the session)
npm run dev              # Vite dev server on :5173 (HMR)
```

Open the app via the Symfony origin (`:8000`) so API calls and the session
cookie stay same-origin.

## Architecture notes

- **Auth**: single Symfony firewall with `json_login` at `/api/auth/login`.
  `GET /api/me` returns `{user: null}` when anonymous. `GET /api/csrf` issues
  the CSRF token (also embedded in the shell as `<meta name="csrf-token">`);
  send it as `X-CSRF-Token` on every `POST/PATCH/DELETE`. Admins log in through
  the same SPA login and the session is shared with `/admin`.
- **Money**: prices are stored as integer **minor units** (paise); the API
  exposes both `price` (int) and `priceFormatted` (₹, Indian grouping).
- **Enums**: `GET /api/enums` returns every enum as `{value,label}[]` so the SPA
  never hardcodes choice lists.
- **Soft delete**: rows carry a nullable `deletedAt`; account deletion
  anonymizes PII and frees the unique email.

## Quality

```bash
php vendor/bin/phpunit                       # functional API + shell/admin tests
vendor/bin/phpstan analyse src               # level 6, clean
PHP_CS_FIXER_IGNORE_ENV=1 php php-cs-fixer.phar fix src
npm run build                                # type-checks (tsc) + builds
```

Tests use a separate database (`.env.test`); create & seed it once with
`APP_ENV=test php bin/console doctrine:database:create` + `migrations:migrate`
+ `fixtures:load`.

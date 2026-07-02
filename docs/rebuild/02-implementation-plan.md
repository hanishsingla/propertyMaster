# PropertyMaster Rebuild — Implementation Plan

Companion to `01-data-model-and-api.md` (contract) with §9 decisions locked:
PropertyImage child entity · INR-only · reseed fixtures · geo in v1 · EasyAdmin stays server-rendered.

Stack: **Symfony 7 JSON API** (`/api/*`, same-origin session cookie) + **React 18 + Vite + TS** (TanStack Query/Router, Tailwind + shadcn/ui).

Legend: 🟥 backend · 🟦 frontend · ⬛ infra/shared

---

## Phase 1 — Backend: entities, schema, seed  🟥

**1.1 New enums** — `Gender, ListingType, PropertyType, PropertyCategory, PropertyStatus, AreaUnit, Direction` as backed enums (`src/Enum/`).

**1.2 AbstractEntity fix** — rename `isCreatedAt/isUpdatedAt/isDeletedAt` → `createdAt/updatedAt/deletedAt`; add `#[ORM\PrePersist]`/`#[ORM\PreUpdate]` to auto-set them (fixes the current unset-on-create bug); drop `isDeleted` boolean in favour of nullable `deletedAt`; add a Doctrine filter or repository convention for soft-delete. **Update `remember_me.signature_properties`** (currently `['password','updatedAt']` against the old field name) in `security.yaml` at the same time.

**1.3 Rework entities** per §2–§4:
- `Property` — typed fields, drop `property*` prefix, `owner` FK → User, `slug`, `status`, `listingType`, price bigint + currency, `lat/lng`, `isFeatured`.
- `PropertyImage` — new child (path/sortOrder/isCover/cover-uniqueness).
- `User` — `image`→`avatar`, unify upload path.
- `FavouriteProperty` — user FK + property FK, unique `(user,property)`, drop stringly fields.
- `Contact` — `username`→`name`, nullable `user` FK, drop email-unique.

**1.4 Migration** — squash the 3 existing migrations into one baseline for the new schema.

**1.5 Fixtures** — rewrite `PropertyFixtures` (+ new `UserFixtures`) with Faker: agents, users, ~30 properties with images, favourites. Reseed command documented.

**Exit:** `doctrine:schema:validate` clean; `doctrine:fixtures:load` populates a browsable DB.

---

## Phase 2 — Backend: API layer  🟥

**2.1 Firewall** — new `api` firewall on `^/api`, stateful (session), `json_login` at `/api/auth/login`, JSON entry-point (401 not redirect), `access_control` for `/api/my`, `/api/account`, agent-only writes. Keep `main` firewall for EasyAdmin/legacy.

**2.2 CSRF + rate limiting** — `GET /api/csrf`; enforce `X-CSRF-Token` on mutating `/api` requests (event subscriber). Add `symfony/rate-limiter` policies per §1 of the contract (login/register/reset/contact) + contact honeypot.

**2.3 Serialization** — Symfony Serializer with groups (`property:list/read`, `user:read/self`, `agent:read`); custom normalizer for `priceFormatted` (Indian grouping), `coverImage`/`images[]` URLs, and per-viewer `isFavourited` (batched, no N+1); standard list envelope `{data, meta}`.

**2.4 Error handling** — exception subscriber → RFC7807 body (§6); validation violations mapped to `violations[]`.

**2.5 Controllers/services** (thin controllers, logic in services):
- `AuthApiController` (+ reuse verify-email/reset-password bundles). Email links per contract §7/§10: verify-email **302-redirects to the SPA**, reset email links to SPA `/reset-password/{token}`; add resend-verification; `/api/me` returns `{user: null}` when anonymous.
- `PropertyApiController` + `PropertyService` (filters, pagination, slug gen, ownership checks via Voter).
- `PropertyImageApiController` + `Uploader` refactor (cover/order).
- `FavouriteApiController` (toggle, idempotent).
- `AgentApiController`, `AccountApiController` (delete = **anonymize email + PII** per contract §10, cascade soft-delete of listings), `ContactApiController`, `EnumApiController`.

**2.6 Security Voter** — `PropertyVoter` (`EDIT`/`DELETE` = owner or ROLE_ADMIN; `CREATE` additionally requires verified email per contract §10).

**2.7 Tests** — PHPUnit functional tests per endpoint (browser-kit present): auth flow, property CRUD + ownership 403s, favourites toggle, filters/pagination, CSRF rejection.

**Exit:** every endpoint in §7 returns correct JSON + status; test suite green; PHPStan clean.

---

## Phase 3 — Frontend scaffold  🟦⬛

**3.1 Tooling** — Vite + TS + Tailwind + shadcn/ui in `assets/` (or `frontend/`). Vite builds to `public/build`; dev server proxies `/api` → Symfony. **Remove Encore, jQuery, Bootstrap, Slick, SCSS, unused React-in-Encore**; update `package.json`, delete `webpack.config.js`, adjust `.gitignore`.

**3.2 Symfony shell** — catch-all controller serving `index.html`, excluding `/api`, `/admin`, `/_profiler`, `/_wdt`, `/build`, `/uploads`, `/image` (contract §10); remove the legacy `/{listType}` wildcard route and the overlapping `^/*` access_control rule; inject CSRF token + `me` bootstrap into the shell.

**3.3 App infra** — TanStack Router (route tree), TanStack Query (client + query keys), typed `apiClient` (fetch wrapper: credentials include, CSRF header, 401→login, error→§6 parse), auth provider (bootstraps `/api/me`), react-hook-form + zod schemas mirroring backend validation, enum loader from `/api/enums`.

**3.4 Design system** — tokens, theming, layout shell (header/nav, mobile drawer replacing the offcanvas, footer, verify-email banner), shared UI primitives (Button, Card, Input, Select, Dialog, Toast).

**Exit:** SPA boots, authenticated state resolves from `/api/me`, one smoke route renders live API data.

---

## Phase 4 — Frontend feature parity  🟦

Rebuild each current page as a React route (see mapping table below). Grouped by dependency:

1. **Auth**: login, register, reset-password request/confirm, email-verify banner.
2. **Public browse**: home (featured + list by buy/rent/sale), property listing with filter bar (listingType, type, category, city, price range, beds, search) + pagination + **map view (geo)**, property detail (image gallery replacing Slick, map, agent card, favourite toggle).
3. **Agents**: directory + agent detail (their listings).
4. **User area**: favourites page, account profile edit, avatar upload, change password, delete account.
5. **Agent area**: my-properties list, create/edit property (multi-step form + image upload/reorder/cover), delete.
6. **Static**: about, contact form.

**Exit:** all legacy routes have a React equivalent hitting the API; manual E2E of each flow.

---

## Phase 5 — Cutover & QA  ⬛

- Delete dead Twig templates (keep only EasyAdmin + email templates + SPA shell); remove old asset entries.
- Full E2E smoke across roles (guest / user / agent / admin).
- php-cs-fixer, PHPStan, frontend lint + typecheck, both test suites.
- Update EasyAdmin `PropertyCrudController` for new fields/enums.
- README/run docs updated (Vite dev + Symfony serve).

---

## Route → React page mapping

| Legacy Twig / route | New React route | API |
|---|---|---|
| `home` `/{buy\|rent\|sale}` | `/`, `/buy` `/rent` `/sale` | `GET /api/properties?...` |
| `propertyList` | `/properties` | `GET /api/properties` |
| `propertyDetails` | `/properties/:slug` | `GET /api/properties/:idOrSlug` |
| `propertyAgents` | `/agents` | `GET /api/agents` |
| `favouriteProperty` / `likedProperty` | `/favourites` | favourites endpoints |
| `userProperty` + create/edit/delete | `/my/properties` (+ new/edit) | `/api/my/properties`, property CRUD |
| `login` `logout` `register` | `/login` `/register` | auth endpoints |
| reset-password (request/check/reset) | `/reset-password` | reset endpoints |
| `account` / change-pw / delete | `/account` | account endpoints |
| `contact` | `/contact` | `POST /api/contact` |
| `about` | `/about` | static |
| EasyAdmin `/admin` | **unchanged (Twig)** | — |

---

## Sequencing & parallelism

- Phase 1 → 2 are sequential (API needs entities).
- Phase 3 can start **in parallel** with Phase 2 once §7 contract is frozen (frontend mocks the API).
- Phase 4 pages unblock as their endpoints land in Phase 2.

## Risks

- **Session cookie same-origin** requires Vite dev proxy configured correctly (cookie + CSRF) — validate early in 3.3.
- **EasyAdmin coexistence** — two firewalls (`main` for `/admin`, `api` for `/api`) must not conflict; verify login sessions interoperate or are intentionally separate.
- **Image upload** ownership/cover logic is the trickiest service — cover early with tests (2.5/2.7).
- **Reseed only** — confirm once more no live data exists before dropping columns.

Next step after sign-off: begin **Phase 1.1–1.3** (enums + entities).

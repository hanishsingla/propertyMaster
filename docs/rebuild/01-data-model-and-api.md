# PropertyMaster Rebuild — Data Model & API Contract

Status: **v1.1 (refined)** · Target: Symfony 7 JSON API + React SPA · Auth: same-site session cookie

This document defines the *contract* both sides build against. Nothing here is code yet.

---

## 1. Conventions

- **IDs**: UUID v4 strings (keep existing `doctrine.uuid_generator`).
- **Timestamps**: ISO-8601 UTC (`createdAt`, `updatedAt`). Rename the current `isCreatedAt/isUpdatedAt/isDeletedAt` → `createdAt/updatedAt/deletedAt` and make them auto-managed via lifecycle callbacks (they are currently non-nullable-but-unset on create — a latent bug).
- **Soft delete**: keep `deletedAt` (nullable). A row is deleted when `deletedAt != null`. Drop the redundant `isDeleted` boolean.
- **Money**: store price as integer **minor units** (e.g. paise/cents) + a `currency` (default `INR`). Never float. API exposes both `price` (int minor) and `priceFormatted`.
- **Enums**: PHP 8.1 backed enums, serialized as their string value. Listed in §5.
- **Envelope**: lists return `{ "data": [...], "meta": { page, perPage, total, totalPages } }`. Single resources return the bare object. Errors use §6 format.
- **Auth**: HttpOnly `SESSION` cookie (same-origin). All mutating requests (`POST/PUT/PATCH/DELETE`) require header `X-CSRF-Token` obtained from `GET /api/csrf` or bootstrapped in the SPA shell.
- **Serializer groups**: `property:list`, `property:read`, `user:read`, `user:self`, `agent:read`.
- **Computed serialized fields on Property**: `priceFormatted` (₹, Indian digit grouping), `coverImage` (URL, in `property:list`), `images[]` (ordered, in `property:read`), and `isFavourited` (bool — present only when a user is authenticated; saves the SPA an N+1 of favourite lookups).
- **Uploads**: property images → `public/uploads/properties/{propertyId}/`, avatars → `public/uploads/avatars/`. Constraints: jpeg/png/webp, ≤ 5 MB each, ≤ 15 images per property. API returns absolute URLs.
- **Rate limiting** (symfony/rate-limiter): login 5/min per IP+email, register & reset-request 3/hour per IP, contact 5/hour per IP (+ honeypot field).

---

## 2. Entity: `User`  (table `security_users`)

| Field | Type | Notes | Group |
|---|---|---|---|
| id | uuid | PK | user:read |
| email | string(180) unique | login identity | user:self |
| roles | json array | `ROLE_USER` implicit; hierarchy unchanged | user:self |
| password | string | hashed, **never serialized** | — |
| isAgent | bool | drives `ROLE_AGENT` | user:read |
| isVerified | bool | email verification | user:self |
| name | string? | display name | user:read |
| gender | enum? `Gender` | male/female/other | user:read |
| avatar | string? | filename (was `image`); served from `/uploads/avatars/` — **fix the current `userImage`/`userImages` path split** | user:read |
| phone | string? | | user:self |
| mobile | string? | | user:self |
| country | string? | | user:self |
| address, address2 | string? | | user:self |
| city, state, zip | string? | | user:self |
| createdAt, updatedAt | datetime | auto | user:self |

**Removed from runtime state:** the session-stored `gender/userImage/userName/email` (base template reads `app.session.get(...)`). SPA derives all of this from `GET /api/me`.

Role hierarchy (unchanged): `SUPER_LICENSE → LICENSE → {AGENT, ADMIN} → AGENT → USER`.

---

## 3. Entity: `Property`  (table `properties`) — **major normalization**

Today every column is `string`. New typed schema:

| Field | Old type | New type | Notes |
|---|---|---|---|
| id | uuid | uuid | PK |
| owner | string `ownerId` | **ManyToOne → User** | real FK, was a loose string |
| title | string | string(180) | |
| slug | — | string unique | NEW — SEO/deep links, generated from title+id |
| description | text | text | |
| listingType | string | enum `ListingType` | sale / rent (was `propertyStatus`… see §5 note) |
| category | string | enum `PropertyCategory` | villa/apartment/floor/office/shop/hotel/warehouse/agricultural_farm_land |
| type | string | enum `PropertyType` | residential / commercial |
| status | string | enum `PropertyStatus` | draft / published / sold / rented (NEW lifecycle; replaces the ambiguous old `propertyStatus`) |
| price | string | **bigint (minor units)** | + `currency` string(3) default INR |
| area | string | **int** | numeric |
| squareType | string | enum `AreaUnit` | sq_ft / sq_m (normalize old `feet`/`sq.ft.`/`sq.m.`) |
| bedRooms | string? | int? | |
| bathRooms | string | int? | nullable — land/warehouse categories have none |
| rooms | string? | int? | |
| direction | string | enum `Direction` | north/south/east/west |
| city | string | string | |
| state | string | string (default Punjab) | |
| country | string | string (default India) | |
| latitude, longitude | — | float? | v1 (locked §9.4); nullable — not every listing is geocoded |
| images | json array | **OneToMany → PropertyImage** | child entity (locked §9.1): path/sortOrder/isCover |
| isFeatured | — | bool default false | NEW — homepage highlight |
| createdAt, updatedAt, deletedAt | datetime | datetime | auto |

> **Naming note:** drop the `property*` prefix on every column (`propertyTitle`→`title`, etc.). It's redundant inside the `Property` entity and clutters the API.

> **Legacy ambiguity resolved:** old `propertyStatus` held `sale/rent` (listing intent) while the *form* radio also called it status. New model splits into `listingType` (sale/rent — intent) and `status` (draft/published/sold/rented — lifecycle).

---

## 4. Entities: `FavouriteProperty` & `Contact`

**FavouriteProperty** → simplify to a join: `id`, `user` (ManyToOne), `property` (ManyToOne), `createdAt`. Drop the stringly-typed `ownerId`/`favourite`. Unique constraint on `(user, property)`.

**Contact** → `id`, `name` (was `username`), `email`, `message` (text), `user?` (nullable ManyToOne if logged in — was `ownerId`), `createdAt`. Drop `unique` on email (a person can contact twice).

---

## 5. Enums

```
Gender:           male | female | other
ListingType:      sale | rent
PropertyType:     residential | commercial
PropertyCategory: villa | apartment | floor | office | shop | hotel | warehouse | agricultural_farm_land
PropertyStatus:   draft | published | sold | rented
AreaUnit:         sq_ft | sq_m
Direction:        north | south | east | west
```
API exposes `GET /api/enums` returning all of the above as `{value,label}[]` so the SPA never hardcodes them.

---

## 6. Error format (RFC 7807-ish)

```json
{ "error": { "code": "validation_failed", "message": "…",
  "violations": [ { "field": "email", "message": "Already in use" } ] } }
```
Status codes: 401 unauthenticated, 403 forbidden (incl. CSRF failure, code `csrf_invalid`), 404 not found, 409 conflict, 422 validation (`validation_failed` + `violations[]`), 429 rate-limited.

---

## 7. API Endpoints

### Auth  (`/api/auth`)
| Method | Path | Body | Returns | Notes |
|---|---|---|---|---|
| GET | `/api/csrf` | — | `{ token }` | for SPA bootstrap |
| POST | `/api/auth/login` | `{email,password}` | `user:self` | json_login; sets session cookie |
| POST | `/api/auth/logout` | — | 204 | |
| GET | `/api/me` | — | `user:self` \| 401 | SPA auth bootstrap |
| POST | `/api/auth/register` | `{email,password,name,isAgent?}` | `user:self` | sends verification email; logs the user in |
| GET | `/api/auth/verify-email?...` | — | **302 → SPA** `/email-verified?status=ok\|invalid` | link clicked from email (signed URL, verify-email bundle) — must redirect, not return JSON |
| POST | `/api/auth/resend-verification` | — | 204 | for the "please verify" banner |
| POST | `/api/auth/reset-password/request` | `{email}` | 204 | always 204 (no user enumeration); email links to **SPA** `/reset-password/{token}` |
| POST | `/api/auth/reset-password/reset` | `{token,password}` | 204 | SPA posts the token from its route param |

### Properties  (`/api/properties`)
| Method | Path | Auth | Body/Query | Returns |
|---|---|---|---|---|
| GET | `/api/properties` | public | `?listingType&type&category&city&minPrice&maxPrice&bedRooms&isFeatured&q&sort&page&perPage` | list `property:list` — public always sees `status=published` only; `sort ∈ {newest (default), price_asc, price_desc}`; `perPage` ≤ 50 (default 12); `isFeatured=1` drives the homepage strip |
| GET | `/api/properties/{idOrSlug}` | public | — | `property:read` (+ owner as `agent:read`) |
| POST | `/api/properties` | ROLE_AGENT | property fields | `property:read` |
| PATCH | `/api/properties/{id}` | owner/ADMIN | partial fields | `property:read` |
| DELETE | `/api/properties/{id}` | owner/ADMIN | — | 204 (soft delete) |
| POST | `/api/properties/{id}/images` | owner/ADMIN | multipart files | `property:read` |
| PATCH | `/api/properties/{id}/images` | owner/ADMIN | `[{imageId, sortOrder, isCover}]` | reorder / set cover in one call |
| DELETE | `/api/properties/{id}/images/{imageId}` | owner/ADMIN | — | 204 (deleting the cover promotes the next image) |
| GET | `/api/my/properties` | ROLE_AGENT | paging | own listings (any status) |

### Favourites  (`/api/favourites`)
| GET | `/api/favourites` | ROLE_USER | — | list of `property:list` |
| POST | `/api/favourites/{propertyId}` | ROLE_USER | — | `{favourited:true}` (idempotent toggle-on) |
| DELETE | `/api/favourites/{propertyId}` | ROLE_USER | — | 204 |

### Agents  (`/api/agents`)
| GET | `/api/agents` | public | paging | list `agent:read` (users where isAgent) |
| GET | `/api/agents/{id}` | public | — | `agent:read` + their published properties |

### Account  (`/api/account`)
| GET | `/api/account` | ROLE_USER | — | `user:self` |
| PATCH | `/api/account` | ROLE_USER | profile fields | `user:self` |
| POST | `/api/account/avatar` | ROLE_USER | multipart | `{avatar}` |
| POST | `/api/account/change-password` | ROLE_USER | `{currentPassword,newPassword}` | 204 |
| DELETE | `/api/account` | ROLE_USER | `{password}` | 204 (soft delete + logout) |

### Misc
| GET | `/api/enums` | public | — | all enums as `{value,label}[]` |
| POST | `/api/contact` | public | `{name,email,message}` | 204 |

---

## 8. Migration / seed strategy

- Full rebuild → **new migration set** replacing the 3 existing migrations (or one squashed baseline). Old `properties`/`security_users` columns renamed & retyped.
- Existing rows: since types change (string→int/enum), recommend **reseed via fixtures** (`PropertyFixtures` already exists — rewrite it) rather than data-migrating. Confirm no production data must be preserved.
- Keep EasyAdmin pointing at the new entities (update `PropertyCrudController`).

---

## 9. Sign-off decisions (LOCKED 2026-07-02)

1. **PropertyImage child entity** — adopted. Fields: `id`, `property` (ManyToOne), `path`, `sortOrder` (int), `isCover` (bool), `createdAt`. Exactly one cover per property enforced in service layer.
2. **Currency** — **INR only**. Store price as bigint minor units (paise); `currency` fixed to `INR`, no multi-currency UI. API still exposes `priceFormatted` (₹).
3. **Reseed from fixtures** — no production data preserved. New baseline migration + rewritten `PropertyFixtures`.
4. **Map/geo (lat/lng)** — **in v1**. `latitude`/`longitude` on `Property`; SPA property detail shows a map + list has optional map view.
5. **EasyAdmin** — **kept as-is, server-rendered**, outside the SPA. `PropertyCrudController` updated to the new typed fields/enums.

Next artifact: **implementation task breakdown** (`02-implementation-plan.md`).

---

## 10. Edge cases & decisions from review (v1.1)

- **Account deletion vs. unique email**: soft-deleting a user leaves their unique `email` row in place, blocking re-registration with the same address. Decision: on `DELETE /api/account`, **anonymize** — set `email` to `deleted+{id}@local.invalid`, null out PII, keep the row (their properties are soft-deleted too). Simple and reversible enough.
- **Agent-owned properties on deletion**: soft-delete cascades to the agent's properties and favourites rows referencing them stay (joins filter out soft-deleted properties).
- **remember_me config**: currently signs with `['password', 'updatedAt']` — must be updated when `isUpdatedAt` is renamed, or remember-me cookies break silently.
- **SPA catch-all route** must exclude `/api`, `/admin`, `/_profiler`, `/_wdt`, `/build`, `/uploads`, `/image`. The legacy wildcard `/{listType}` route and the overlapping `^/*` access_control rule are removed.
- **`GET /api/me` when unauthenticated returns 200 `{user: null}`**, not 401 — avoids console noise and lets the SPA bootstrap with one unconditional call. (Supersedes §7's `401`.)
- **Slug**: generated server-side as `kebab(title)-{first 8 of id}` — unique without a counter table; immutable after publish (stable deep links).
- **Verified-email gate**: unverified users can browse and favourite but **cannot create properties or submit contact as logged-in** — mirrors today's banner nudge with an actual enforcement point (403 `email_unverified`).

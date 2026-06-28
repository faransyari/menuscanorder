# Image & QR Production-Readiness — Design

**Date:** 2026-06-28
**Status:** Approved

## Problem

MenuScanOrder is a fully dynamic CodeIgniter 4 app, but two features are not
deployment-ready:

1. **Hardcoded host URL.** QR codes and login redirects encode a fixed legacy host
   (`https://infs3202-14206650.uqcloud.net/...`):
   - `app/Controllers/TableController.php` — QR data URL.
   - `app/Views/landing_page.php` — three JS redirects.
   A scanned QR therefore points at the old server, not the live deployment.

2. **Ephemeral local file storage.** Uploaded menu images and generated QR PNGs are
   written to the container's local disk (`public/uploads/menu_items`,
   `public/uploads/qr_codes`). On Render's free tier this disk is wiped on every
   redeploy/restart, so images and QR codes disappear.

## Goals

- QR codes always point at the live domain and survive redeploys.
- Uploaded menu images persist across redeploys.
- Local development keeps working without external accounts.
- No secrets in the repository.

## Non-goals

- Migrating any existing production image data (deploys start from a fresh DB).
- Changing the menu/order/auth domain logic.

## Design

### 1. QR codes — generate on demand

QR codes are deterministic from a URL, so there is no reason to persist PNGs.

- **Route:** `GET /admin/qr/(:num)` → `TableController::qr($tableId)`.
- The controller loads the table, builds the QR with Endroid from
  `site_url('menu') . '?restaurant_id=X&table=Y'`, and streams it as
  `image/png`. Access is gated to the logged-in owner of the table's restaurant.
- `generateQr()` no longer writes a file; it stores the **menu target URL** in the
  existing `tables.qr_code` column (informational / copy-link) and inserts the row.
- `delete()` drops the `unlink()` of the PNG (the DB row delete remains).
- `app/Views/generate_qr.php` references `site_url('admin/qr/' . $table['table_id'])`.

**Result:** removes QR file storage entirely and fixes the hardcoded host in one move.

### 2. Menu images — Cloudflare R2 (S3-compatible)

- **Dependency:** `aws/aws-sdk-php` (S3 client pointed at the R2 endpoint).
- **`app/Libraries/MenuImageStorage.php`** — thin interface:
  - `save(\CodeIgniter\Files\File $file): string` → returns the stored object key.
  - `delete(string $key): void`
  - `url(string $key): string` → public URL for display.
  - **Driver auto-selected by environment:** if `R2_*` vars are present, use R2;
    otherwise fall back to the local filesystem (`public/uploads/menu_items`) so
    `php spark serve` works locally without an R2 account.
- **`MenuController`** (`addMenuItem`, `updateMenuItem`, `handleImageUpload`,
  `delete`) call `MenuImageStorage` instead of `$file->move(...)` / `unlink(...)`.
  The `menu_items.image` column continues to store only the object key — no
  migration required.
- **View helper** `menu_image_url($key)` (in `app/Helpers/image_helper.php`)
  resolves a key to its display URL via `MenuImageStorage::url()`. The three
  `base_url('uploads/menu_items/...')` references in `menu_management.php` and
  `menu.php` switch to this helper.

### 3. Fix landing-page redirects

`app/Views/landing_page.php` — replace the three hardcoded `uqcloud.net` JS
redirects with `<?= site_url('admin') ?>`, `<?= site_url('loggedin') ?>`,
`<?= site_url('login') ?>`.

### 4. Repo hygiene & docs

- Remove committed sample uploads (`public/uploads/menu_items/*.webp`,
  `public/uploads/qr_codes/*.png`). Keep the directories (with `index.html`) and
  gitignore their contents.
- Add `R2_ENDPOINT`, `R2_BUCKET`, `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`,
  `R2_PUBLIC_URL` to `.env.example`, `render.yaml` (`sync: false`), and the README
  (env table + a short "Cloudflare R2 setup" section).

## Environment variables (new)

| Variable               | Required for R2 | Example |
|------------------------|-----------------|---------|
| `R2_ENDPOINT`          | yes             | `https://<account>.r2.cloudflarestorage.com` |
| `R2_BUCKET`            | yes             | `menuscanorder` |
| `R2_ACCESS_KEY_ID`     | yes             | `••••` |
| `R2_SECRET_ACCESS_KEY` | yes             | `••••` |
| `R2_PUBLIC_URL`        | yes             | `https://cdn.example.com` or the bucket public URL |

When these are unset, image storage falls back to local files (development only).

## Testing / verification

- `composer require aws/aws-sdk-php` resolves and `composer validate` passes.
- `php -l` on every changed PHP file.
- Manual: locally (no R2) upload a menu image and confirm it displays; load
  `/admin/qr/<id>` and confirm a PNG renders pointing at the local `menu` URL.
- Existing PHPUnit suite still passes in CI.

## Trade-offs

- `aws/aws-sdk-php` is a sizeable dependency. Accepted for reliability; a minimal
  S3 signer could replace it later if image size becomes a concern.

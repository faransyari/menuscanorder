# MenuScanOrder

MenuScanOrder lets restaurants publish QR-code menus that customers scan to browse
and order from their table. It is built on **CodeIgniter 4** (PHP 8.1) with
[Shield](https://shield.codeigniter.com/) authentication and a MySQL database.

[![CI](https://github.com/faransyari/menuscanorder/actions/workflows/ci.yml/badge.svg)](https://github.com/faransyari/menuscanorder/actions/workflows/ci.yml)

## Features

- **QR-code menus** — generate a QR per table that opens the live menu.
- **Order management** — place and track orders, items, and tables.
- **Authentication** — user accounts and access control via CodeIgniter Shield.
- **Responsive UI** — works on phones and desktops.

## Tech stack

| Layer     | Technology                          |
|-----------|-------------------------------------|
| Framework | CodeIgniter 4                       |
| Language  | PHP 8.1                             |
| Auth      | CodeIgniter Shield                  |
| Database  | MySQL 8 (`mysqli` / `pdo_mysql`)    |
| Web server| Apache (via Docker) / `php spark serve` locally |
| Hosting   | Render (Docker) + external MySQL    |

---

## Local development

### Prerequisites

- PHP 8.1+ with `intl`, `mysqli`, `pdo_mysql` extensions
- [Composer](https://getcomposer.org/)
- A local MySQL 8 server

### Setup

```bash
# 1. Install dependencies
composer install

# 2. Create your environment file
cp .env.example .env

# 3. Generate an encryption key (writes encryption.key into .env)
php spark key:generate

# 4. Edit .env and set your database credentials, then run migrations
php spark migrate

# 5. Start the development server
php spark serve
```

The app is now available at <http://localhost:8080>.

### Running tests

Tests use a separate database (`menuscanorder_test`). Set the `database.tests.*`
values in `.env`, then:

```bash
composer test          # or: vendor/bin/phpunit
```

---

## Environment variables

Configuration is driven entirely by environment variables — **no secrets live in
the repository**. Locally these come from `.env`; in production they are injected
by the host (see deployment below). The container entrypoint renders these into a
CodeIgniter `.env` file at startup.

| Variable          | Required | Example                                 | Notes |
|-------------------|----------|-----------------------------------------|-------|
| `CI_ENVIRONMENT`  | yes      | `production`                            | `development` locally |
| `APP_BASE_URL`    | yes      | `https://menuscanorder.onrender.com`    | Public URL of the app |
| `APP_FORCE_HTTPS` | no       | `true`                                  | Redirect HTTP → HTTPS in production |
| `ENCRYPTION_KEY`  | yes      | `hex2bin:...`                           | Generate with `php spark key:generate --show` |
| `DB_HOSTNAME`     | yes      | `mysql-xxxx.aivencloud.com`             | MySQL host |
| `DB_DATABASE`     | yes      | `menuscanorder`                         | Database name |
| `DB_USERNAME`     | yes      | `avnadmin`                              | DB user |
| `DB_PASSWORD`     | yes      | `••••••`                                | DB password |
| `DB_PORT`         | no       | `3306`                                  | Defaults to `3306` (Aiven uses a custom port) |
| `DB_SSL`          | prod     | `true`                                  | Enable TLS — required by Aiven/managed MySQL |
| `DB_SSL_CA_PATH`  | no       | `/etc/secrets/ca.pem`                   | Optional CA for strict cert verification |
| `DB_DEBUG`        | no       | `false`                                 | Keep `false` in production |
| `RUN_MIGRATIONS`  | no       | `true`                                  | Run migrations on container start |
| `SESSION_DRIVER`  | no       | `CodeIgniter\Session\Handlers\DatabaseHandler` | DB sessions for multi-instance safety |
| `SESSION_SAVE_PATH` | no     | `ci_sessions`                           | Session table name (DB driver) |
| `R2_ENDPOINT`     | prod     | `https://<acct>.r2.cloudflarestorage.com` | Cloudflare R2 endpoint |
| `R2_BUCKET`       | prod     | `menuscanorder`                         | R2 bucket name |
| `R2_ACCESS_KEY_ID`| prod     | `••••`                                  | R2 API token key |
| `R2_SECRET_ACCESS_KEY` | prod| `••••`                                  | R2 API token secret |
| `R2_PUBLIC_URL`   | prod     | `https://cdn.example.com`               | Public URL of the bucket / CDN domain |

> **Image storage:** when the `R2_*` variables are set, uploaded menu images go to
> Cloudflare R2 and persist across redeploys. When they are unset (local dev),
> images fall back to `public/uploads/menu_items`. **QR codes are never stored** —
> they are generated on demand at `/admin/qr/{tableId}` and always point at the
> live domain.

---

## Deployment (Render + free MySQL)

> **Why not Vercel?** Vercel does not run PHP/Apache apps natively and its free
> databases are Postgres/Redis, not MySQL. Render runs this Docker image natively
> on its free tier, so it is the right fit for a CodeIgniter + MySQL app.

### 1. Provision a free MySQL database

Render's free tier has no managed MySQL, so use a free external provider, e.g.
**[Aiven](https://aiven.io/)** or **[Railway](https://railway.app/)**. Create a
MySQL instance and note the host, port, database name, username, and password.

### 2. Set up Cloudflare R2 (menu image storage)

1. In the Cloudflare dashboard, create an **R2 bucket** (e.g. `menuscanorder`).
2. Enable public access (R2.dev URL or a custom domain) — this is your `R2_PUBLIC_URL`.
3. Create an **R2 API token** (Object Read & Write) → gives you `R2_ACCESS_KEY_ID`
   and `R2_SECRET_ACCESS_KEY`.
4. Your `R2_ENDPOINT` is `https://<account-id>.r2.cloudflarestorage.com`.

R2's free tier includes 10 GB of storage — ample for menu images.

### 3. Deploy the app on Render

This repo ships a [`render.yaml`](./render.yaml) Blueprint and a production
[`Dockerfile`](./Dockerfile).

1. Push this repository to GitHub.
2. In the Render dashboard: **New → Blueprint**, and select this repo.
3. Render reads `render.yaml` and creates the `menuscanorder` Docker web service.
4. Set the secret environment variables (marked `sync: false`) in the dashboard:
   `APP_BASE_URL`, `ENCRYPTION_KEY`, `DB_HOSTNAME`, `DB_DATABASE`,
   `DB_USERNAME`, `DB_PASSWORD`.
5. Deploy. On boot the container renders `.env`, runs migrations, and starts
   Apache on the port Render provides.

### How the image works

- Apache serves **`public/` only** — application code, `writable/`, and `vendor/`
  are never web-accessible.
- `composer install --no-dev` runs at build time; the optimized autoloader is
  generated.
- [`docker/entrypoint.sh`](./docker/entrypoint.sh) builds `.env` from environment
  variables, runs `php spark migrate`, and listens on `$PORT`.

> **Note on persistence:** uploads go to Cloudflare R2 and sessions to the database
> (`ci_sessions` table), so neither depends on the container's ephemeral local disk.
> The app is therefore safe to run across multiple instances. Anything still written
> under `writable/` (cache, logs) is disposable and resets on redeploy.

---

## Project structure

```
app/            Application code (Controllers, Models, Config, Database/Migrations)
public/         Web root (index.php, assets) — the only directory served publicly
writable/       Runtime cache, logs, sessions, uploads (ephemeral)
tests/          PHPUnit tests
docker/         Apache vhost + container entrypoint
Dockerfile      Production image
render.yaml     Render Blueprint
```

## License

Released under the [MIT License](./LICENSE).

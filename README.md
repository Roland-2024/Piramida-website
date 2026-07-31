# Piramida CMS

Laravel administration dashboard and bilingual content-management architecture for the Piramida website. The included public Blade frontend is intentionally simple and exists to verify managed content until the frontend team supplies the final HTML, Tailwind CSS, and JavaScript template.

## Stack

- Laravel 13 and PHP 8.4
- MySQL 8.4
- Nginx
- Blade, Tailwind CSS 4, and Vite 8
- Docker Compose
- PHPUnit

Redis and a JavaScript framework are intentionally omitted because the current CMS does not require them.

## Architecture

The CMS manages:

- Editable presentation pages and ordered page sections, including About Us
- News
- Events, exhibitions, and guided tours
- Attractions
- On-site businesses with logos, galleries, and public information dialogs
- Event and leasing spaces
- Careers
- Reusable media
- Contact, registration, event-space request, leasing-enquiry, and career submissions
- Global contact, opening-hours, social, and footer settings
- Admin and Editor dashboard users

Publishable modules store shared/queryable fields in their parent tables and bilingual content in separate translation tables. Translation tables enforce one translation per locale and database-level slug uniqueness per content type and locale.

Optional section-specific structured data is the only content stored as JSON. Public queries use model scopes and remain independent from Blade presentation.

Pages and Page Sections manage static presentation templates without coupling their content to the temporary frontend. For example, About Us can be assembled from ordered image/text, feature, gallery, and other sections; staff can change the content while the final frontend template controls its appearance.

Events, spaces, and careers support internal request forms, external links, both actions, or no action. Event spaces collect event requirements and preferred timing. Leasing spaces collect a business enquiry without creating a calendar booking. All internal forms create a staff-reviewed request; they do not confirm availability, take payment, or create an automatic reservation. Uploaded CV files are validated and kept on private storage.

The earlier Program tables remain in the database for reversibility but are not exposed in the dashboard or public routes. The Education, Innovation, Business, and Art & Culture cards seen in the design are presentation content managed through Pages and Page Sections, not a separate program catalogue.

## Requirements

- Docker Desktop or Docker Engine with Compose v2
- Git

Host PHP, Composer, Node.js, MySQL, and Nginx are not required.

## Initial installation

From the project directory:

```powershell
Copy-Item .env.example .env
docker compose build
docker compose run --rm --no-deps -e WAIT_FOR_DB=false app composer install
docker compose run --rm node npm install
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose run --rm node npm run build
```

Before seeding, set `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, and `INITIAL_ADMIN_PASSWORD` in `.env`. Keep `SEED_DEMO_CONTENT=false` unless local bilingual examples are wanted.

## Prototype demo content

The idempotent `DemoContentSeeder` creates a complete Albanian/English demonstration based on the approved Figma prototype: Home, Education, About, legal pages, ordered page sections, news, events, attractions, business experience cards, four event spaces, two leasing units, careers, site settings, and reusable prototype imagery. It updates only records identified by its stable demo slugs or internal section names and does not remove other CMS content.

Run it explicitly in an existing local database:

```bash
docker compose exec app php artisan db:seed --class=DemoContentSeeder
```

Alternatively set `SEED_DEMO_CONTENT=true` before `migrate --seed` for a fresh local installation. Keep it disabled in production unless this demonstration content is intentionally required.

## Local URLs and ports

| Service | Default URL or host port | Environment variable |
|---|---:|---|
| Website | `http://localhost:8088` | `APP_PORT` |
| Admin login | `http://localhost:8088/admin/login` | — |
| Albanian frontend | `http://localhost:8088/al` | — |
| English frontend | `http://localhost:8088/en` | — |
| MySQL from host | `127.0.0.1:3308` | `DB_FORWARD_PORT` |
| Vite HMR | `http://localhost:5178` | `VITE_PORT` |

Container ports remain conventional: Nginx `80`, MySQL `3306`, PHP-FPM `9000`, and Vite `5173`.

If a default host port is occupied, change the corresponding `.env` variable. Do not stop the process already using the port.

## Common commands

```bash
# Start in the foreground
docker compose up

# Start in the background
docker compose up -d

# Stop and remove containers
docker compose down

# Rebuild and restart
docker compose up -d --build

# Install PHP dependencies
docker compose exec app composer install

# Install frontend dependencies
docker compose run --rm node npm install

# Run migrations
docker compose exec app php artisan migrate

# Run seeders
docker compose exec app php artisan db:seed

# Reset and reseed the database
docker compose exec app php artisan migrate:fresh --seed

# Create the public storage link
docker compose exec app php artisan storage:link

# Start Vite with hot reload
docker compose --profile frontend up node

# Build production frontend assets
docker compose run --rm node npm run build

# Run tests
docker compose exec app php artisan test

# Format PHP
docker compose exec app ./vendor/bin/pint

# Inspect routes
docker compose exec app php artisan route:list

# Follow all container logs
docker compose logs -f

# Follow one service
docker compose logs -f app

# Remove containers and local volumes (deletes local database data)
docker compose down -v
```

## Environment configuration

Important `.env` values:

```dotenv
APP_NAME="Piramida CMS"
APP_URL=http://localhost:8088
APP_TIMEZONE=Europe/Tirane
APP_LOCALE=al
APP_FALLBACK_LOCALE=al

APP_PORT=8088
DB_FORWARD_PORT=3308
VITE_PORT=5178

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=piramida
DB_USERNAME=piramida
DB_PASSWORD=change-this-local-value
DB_ROOT_PASSWORD=change-this-local-value

FILESYSTEM_DISK=public

INITIAL_ADMIN_NAME=
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
SEED_DEMO_CONTENT=false
```

The Docker defaults are for local development only. Use deployment-specific secrets outside source control.

## Creating the first Admin

Set the three `INITIAL_ADMIN_*` values, then run:

```bash
docker compose exec app php artisan db:seed --class=InitialAdminSeeder
```

The seeder creates the account only when it does not already exist and never overwrites an existing password. Public registration is not available.

## Roles

### Admin

Admins may access all content and media modules, manage dashboard users, assign roles, deactivate accounts, and delete or restore content. The final active Admin cannot be demoted or deactivated. Users cannot change their own role or active status.

### Editor

Editors may access the dashboard and create or update pages, sections, news, events, and media. Editors cannot access user management or delete/restore content and media.

Editors may also manage attractions, businesses, spaces, and careers. Submissions and global site settings remain Admin-only.

## Request workflow

1. Configure the public action on an event, space, or career as `Internal request form`, `External link`, `Internal form and external link`, or `No booking`.
2. Set each space type to `Event space` or `Leasing`. The type determines which request form and staff submission category is used.
3. Set the submission notification address under **Dashboard → Site settings**.
4. New internal requests appear under **Dashboard → Submissions** with status `New`.
5. Staff can move a request through `In review`, `Replied`, and `Closed`, add private notes, download permitted attachments, and export the filtered inbox as CSV.

Email delivery uses Laravel's configured mailer. A mail failure is reported to the application log but does not discard a successfully stored request.

## Production deployment without Docker

Docker Compose is for local development only; the live server does not need Docker. The production host needs PHP 8.4.1 or newer with Laravel's required extensions, MySQL 8, Composer, a web server whose document root is `public/`, and either Node.js for the asset build or a prebuilt `public/build` directory.

Typical production commands:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Create the production `.env` directly on the server, set `APP_ENV=production`, `APP_DEBUG=false`, a real `APP_KEY`, database credentials, and the production mail transport. Keep `.env`, private uploads, and server credentials out of Git. Ensure the web-server user can write to `storage/` and `bootstrap/cache/`.

Dashboard users are deactivated rather than deleted so content attribution remains intact.

## Localization

- URL locales: `al` and `en`
- Default and fallback locale: Albanian (`al`)
- Interface strings: `resources/lang/{locale}`
- Managed content: normalized translation tables
- Public content records resolve only by the requested locale’s slug
- A missing requested record translation returns `404`
- Non-routing child content such as a section may fall back to Albanian

All Admin content forms expose Albanian and English fields clearly. Slugs are generated from titles when left blank and remain manually editable.

## Publication behavior

- Draft content is never public.
- Published content requires a publication date at or before the current time.
- Future news and other scheduled content are hidden.
- Only active page sections render, ordered by `display_order` and then ID.
- Upcoming events have an end time at or after the current time.
- Past events have an end time before the current time.
- Application and PHP container timezone: `Europe/Tirane`.

## Media and rich text

Media uses Laravel Storage and defaults to the `public` disk. Paths are portable to an S3-compatible disk.

Accepted uploads:

- JPEG
- PNG
- WebP
- GIF
- PDF
- Maximum 10 MB

The server validates MIME type, extension, and size, generates unique filenames, and records useful metadata. Referenced files cannot be deleted. Soft-deleted unreferenced media can be restored or permanently deleted by an Admin.

Long-form fields use a lightweight browser editor. Every stored value passes through Symfony HTML Sanitizer. Scripts, event attributes, styles, unsafe URLs, and unapproved elements are removed.

## Password reset

Password reset routes are enabled. The default local mailer writes reset messages to `storage/logs/laravel.log`. Configure a real mail transport before deployment.

## Tests

Tests use in-memory SQLite regardless of Docker’s development database values. Coverage includes authentication, roles, user safeguards, CRUD validation, translation slugs, ordering, publication visibility, event scopes, uploads, HTML sanitization, localized public resolution, and direct-route authorization.

```bash
docker compose exec app php artisan test
```

Do not point PHPUnit at a shared or production database.

## Integrating the final frontend template

The frontend team can replace:

- `resources/views/layouts/public.blade.php`
- `resources/views/public/**`
- Public styles and JavaScript under `resources/css` and `resources/js`

Keep public route names and controller inputs stable, or update links consistently. The models, translation tables, Admin modules, publication scopes, media records, and localized slug behavior do not depend on the temporary markup.

## Current limitations

- The public frontend is a functional test interface, not the final Figma implementation.
- Section ordering uses a numeric field rather than drag-and-drop.
- The native rich-text toolbar intentionally supports only basic formatting.
- There is no automated queue worker because the current workflows are synchronous.

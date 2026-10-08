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
- Separate Event Spaces and Leasing Spaces dashboard modules, with interactive leasing floor plans
- Careers
- Reusable media
- Contact, registration, event-space request, full leasing-application, and career submissions
- Global contact, opening-hours, social, and footer settings
- Admin and Editor dashboard users

Publishable modules store shared/queryable fields in their parent tables and bilingual content in separate translation tables. Translation tables enforce one translation per locale and database-level slug uniqueness per content type and locale.

Optional section-specific structured data is the only content stored as JSON. Public queries use model scopes and remain independent from Blade presentation.

Pages and Page Sections manage static presentation templates without coupling their content to the temporary frontend. For example, About Us can be assembled from ordered image/text, feature, gallery, video, and other sections; staff can change the content while the final frontend template controls its appearance. News and Page Sections support ordered reusable-media galleries.

Education, Innovation and Art & Culture carousel entries are managed in **Programs / Carousel posts**: select the carousel page, translations, featured image, publication status and display order. There is no fixed post count. Business slides remain managed in Businesses; page headings/SEO remain in Pages. For an existing installation, run `php artisan db:seed --class=CarouselProgramSeeder --force` once (locally prefix with `docker compose exec -T app`). It imports existing section slides only for categories without Program records, never overwrites posts, and retains the old sections.

Gallery fields display only attached image thumbnails. Use **Add images** to search/select/upload public images, **Use selected images** to apply, arrows to reorder, then save the record. Remove detaches an image without deleting it from Media.

Events, spaces, and careers support internal request forms, external links, both actions, or no action. Event spaces collect event requirements and preferred timing. Leasing spaces use the designed company, contact, offer, and named-document application; monthly rent is calculated from the unit area and offer per square metre. All internal forms create a staff-reviewed request; they do not confirm availability, take payment, or create an automatic reservation. Uploaded CV and leasing documents are validated and kept on private storage.

Events can also be synchronized from Piramida Ime's bilingual WordPress API. Imported translations keep their WordPress IDs, while manually created dashboard events remain independent and are never overwritten by the importer. The initial import pairs Albanian and English records only when their featured image provides an unambiguous match; subsequent runs use the stored WordPress IDs. Events removed from the complete upstream response are moved to draft rather than deleted.

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

To add only missing logos to the four demo businesses without reseeding other content, run `php artisan db:seed --class=BusinessLogoSeeder`. It reuses the supplied template logos (Piramida's mark is a demo placeholder for Piramida Store) and preserves existing logo assignments. Editors can replace them in Businesses.

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
| Albanian frontend | `http://localhost:8088` | — |
| English frontend | `http://localhost:8088/en` | — |
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

WORDPRESS_EVENTS_URL=https://piramidaime.al/wp-json
WORDPRESS_EVENTS_API_KEY=
```

The Docker defaults are for local development only. Use deployment-specific secrets outside source control.

Set `WORDPRESS_EVENTS_API_KEY` to the header value supplied by the WordPress owner. Store only the value, without an `api_key=` prefix. The key shown in development screenshots should be rotated before production use.

## WordPress event synchronization

Run a synchronization manually from **Dashboard → Events → Sync WordPress** or from the command line:

```bash
docker compose exec app php artisan events:sync-wordpress
```

Laravel schedules the same command daily at 06:00 and 18:00 in the application timezone. On the live server, run Laravel's scheduler every minute:

```cron
* * * * * cd /path/to/piramida && php artisan schedule:run >> /dev/null 2>&1
```

The API key remains server-side and is never exposed to the browser. A failed or incomplete API request does not draft existing imported events; events are drafted only after both language feeds have been retrieved successfully.

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

Editors may access the dashboard and create or update pages, sections, news, events, and media. Editors may move News articles to trash; only Admins may restore them. Editors cannot trash other content/media, permanently delete records, or access user management.

Editors may also manage attractions, businesses, spaces, and careers. Submissions has a separate sidebar link with read-only list/detail access for Editors; updates, private notes, attachment downloads and CSV exports remain Admin-only. Global site settings remain Admin-only.

## Request workflow

1. Configure the public action on an event, space, or career as `Internal request form`, `External link`, `Internal form and external link`, or `No booking`.
2. Create event spaces in **Event spaces** and rental posts in **Leasing spaces**. For leasing, select **Floor / map unit** and enable **Available for applications** when appropriate; publish the post with both translations. The floor follows the selected unit automatically.
3. Set the submission notification address under **Dashboard → Site settings**.
4. New internal requests appear under **Dashboard → Submissions** with status `New`.
5. Staff can move a request through `In review`, `Replied`, and `Closed`, add private notes, download permitted attachments, and export the filtered inbox as CSV.

Email delivery uses Laravel's configured mailer. A mail failure is reported to the application log but does not discard a successfully stored request.

### Pre-live security and upload configuration

Use PHP `post_max_size=50M` and Nginx `client_max_body_size 50M` on the production server, then reload the services. Leasing accepts up to eleven 4 MB documents; the total request needs more than the previous 20 MB limit. Individual file validation remains unchanged. Docker configuration is for local development only.

Password changes revoke database sessions and remember-me tokens; authenticated dashboard sessions also verify the password fingerprint. Forgot/reset-password endpoints are rate-limited and reset requests return a generic response.

WordPress featured images must use HTTPS on port 443 and an exact trusted hostname: the configured API host or `WORDPRESS_EVENTS_IMAGE_HOSTS` (comma-separated, defaults to `piramidaime.al,www.piramidaime.al`). Add a CDN only after verifying ownership. Redirects are rejected; downloads are capped at 10 MB and validated as images. Failed images produce warnings without preventing event content synchronization.

### Postmark SMTP

The dashboard Postmark settings also deliver administrator/editor password-reset emails. When disabled, Laravel uses the environment mail configuration. Before launch, verify an actual reset email with the verified production sender; automated tests use an in-memory transport and do not prove real delivery.

Run `php artisan migrate --force` after deploying the Postmark settings migration. Admins can configure **Site settings → Postmark SMTP** for all submission notifications. Use a transactional stream's SMTP Access Key and Secret Key (or the Server API Token in both fields), and a verified sender address/name. Enable SMTP in Postmark first. The application uses `smtp.postmarkapp.com:587` with mandatory STARTTLS and a 10-second timeout, following [Postmark's SMTP setup](https://postmarkapp.com/developer/user-guide/send-email-with-smtp).

Both credentials are encrypted with `APP_KEY`, excluded from serialization and validation flash data, and never prefilled. Blank credential fields retain saved values; enter new values to rotate them. Back up `APP_KEY` securely with the database. Disable the checkbox to return to the environment mailer. Saving sends no email; verify delivery with an intentional submission after configuration. SMTP failures do not discard requests and log only the submission ID, not authentication diagnostics. No additional package, queue, or environment variable is required.

## Leasing floor plans

Run `php artisan migrate --force` and rebuild assets when deploying the interactive plans. The additive migration installs 40 real SVG units across Ground, Third, Roof L+4 and Exterior. It preserves existing space IDs, translations, media and submissions. Existing leasing posts start unassigned and unavailable: assign each to its actual map unit in **Leasing spaces** rather than guessing from demo names.

The public map is at `/leasing` (Albanian) or `/en/leasing` (English). Event-space listings use `/event-space` and `/en/event-space`. Old `/spaces` listing links redirect permanently to the appropriate catalogue based on their former type parameter, preserving pagination. Individual space pages and application endpoints are unchanged. Available, published and translated units are green links to the existing application template. All other units are red and not clickable; unavailable detail URLs and application endpoints return 404. A unit can belong to only one post, including trashed posts; restore and unassign that post before reusing its unit.

The supplied PNG backgrounds and SVG paths are local assets. Unit geometry and floor inventory are fixed to the supplied plans, not uploaded by editors. No new environment variables or services are needed.

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

- Albanian URLs have no language prefix; English URLs use `/en`.
- Old `/al` GET/HEAD URLs redirect permanently to their unprefixed equivalent, preserving query filters. Old POST endpoints still process forms without redirecting uploads.
- Default and fallback locale: Albanian (`al`)
- Interface strings: `resources/lang/{locale}`
- Managed content: normalized translation tables
- Public content records resolve only by the requested locale’s slug
- A missing requested record translation returns `404`
- Non-routing child content such as a section may fall back to Albanian

All Admin content forms expose Albanian and English fields clearly. Slugs are generated from titles when left blank and remain manually editable. Route generation still accepts `al` / `en`; the URL formatter omits `al`. The same public route definitions serve both prefixes and the unprefixed Albanian routes. Admin URLs remain unchanged. After deploying routing changes, rebuild the route cache with `php artisan route:cache` if production uses cached routes. No database or environment changes are needed.

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

Homepage video: edit **Pages → Homepage → Homepage YouTube video**. The shared link applies to Albanian and English; Play opens a dialog and loads YouTube only on click. Clear it to restore the section-video / About fallback. Deploy the field with `php artisan migrate --force` and rebuild assets. The demo seeder includes the client-supplied video.

The draft `Piramida.zip` template is integrated through the public Blade views, `resources/css/public.css`, and `resources/js/public.js`. Its selected local images and fonts live under `public/template` (font licenses included). Build these with the existing `npm run build` command; Admin continues using its separate `app.css` / `app.js` bundle.

`/{locale}/rent-space` links to event-space and leasing catalogues. The animated homepage has a skip control and respects reduced motion. About uses editable Page Sections. Education, Innovation and Art & Culture use Programs / Carousel posts; the Business carousel uses Businesses. Businesses retain native information dialogs, and forms retain the existing staff-reviewed submission workflow. Museum is disabled pending the PM's destination. Template sample video and broken placeholder assets were not imported; use a Page Section video URL for real media.

After deploying the revised templates, add missing presentation pages once with:

```bash
php artisan db:seed --class=PresentationPageSeeder --force
# Local Docker equivalent:
docker compose exec -T app php artisan db:seed --class=PresentationPageSeeder --force
```

This additive seeder does not change existing pages. New Education, Innovation and Art & Culture pages start with demo Program posts; customize their slides in **Programs / Carousel posts**. The Business carousel uses **Businesses**. The one-time `CarouselProgramSeeder` remains available to import legacy section slides into empty categories, without overwriting existing or trashed posts. Legacy carousel sections are retained in storage, hidden from Page Sections and never used as a public fallback. No schema migration or environment changes are required.

Carousel posts expose only their page/category, publication, order, featured image, bilingual title, slug and caption. Unused historical booking/gallery/SEO fields are preserved in storage. The list filters by carousel page; dashboard counts and recent-content links include these posts and the other catalogues. Template-dependent English page slugs and About section names/parent pages are protected server-side; public titles and text remain editable.

Featured-image selectors reuse the gallery media library with a single-image preview, search, upload, replace and remove controls. Edit image details opens Media metadata in a new tab; save the content record to apply its selected image. Removing a selection never deletes the shared file. Only non-deleted public images are accepted (also enforced on save). Editor forms warn before leaving with unsaved changes; the mobile sidebar traps keyboard focus and closes with Escape. Run the lightweight JavaScript guard checks with `node tests/js/admin-ui.test.cjs` in addition to the Laravel tests.

The frontend team can replace:

- `resources/views/layouts/public.blade.php`
- `resources/views/public/**`
- Public styles and JavaScript under `resources/css` and `resources/js`

Keep public route names and controller inputs stable, or update links consistently. The models, translation tables, Admin modules, publication scopes, media records, and localized slug behavior do not depend on the temporary markup.

## Search metadata and launch checklist

- Public pages render localized titles/descriptions, canonical URLs, `sq`/`en` hreflang, Open Graph, Twitter cards and JSON-LD. Existing CMS SEO fields override curated metadata; visible copy is unchanged. Records without a translation are not advertised as alternate-language pages.
- `/sitemap.xml` streams published records with their actual translations, excluding closed careers and inaccessible leasing spaces. `/robots.txt` is generated by Laravel; do not deploy an old static `public/robots.txt` over it.
- Set `APP_URL=https://piramida.edu.al`, `SEO_URL=https://piramida.edu.al`, `APP_ENV=production`, `APP_DEBUG=false` and `SEO_INDEXABLE=true` on the final server, then refresh Laravel's configuration cache. Keep `SEO_INDEXABLE=false` for staging. Indexing is additionally restricted to the configured SEO hostname; admin/auth pages receive `X-Robots-Tag: noindex, nofollow`.
- Run `npm run build` and deploy the generated assets. The homepage uses responsive progressive JPEGs (960/1920 px) instead of the original 9 MB PNG. The original remains available. The local Nginx example enables gzip and static-asset caching; apply equivalent rules on the non-Docker production server, with long immutable caching only for hashed `/build/assets/` files.
- Before switching the live site: map existing indexed URLs to their replacements with 301 redirects, verify HTTPS and the preferred host, remove demo content, check that public media URLs load, and submit the sitemap in Google Search Console/Bing Webmaster Tools. Recheck social previews and mobile Core Web Vitals on the deployed host; local timings do not establish production scores.
- AI search uses the same crawlable HTML and factual structured data. No special AI-only pages, speculative `llms.txt`, invented ratings or guaranteed rankings are added. See [Google's AI search guidance](https://developers.google.com/search/docs/appearance/ai-features) and the [Open Graph protocol](https://ogp.me/). Metadata wording reflects the [current Piramida site](https://piramida.edu.al/) without importing outdated public copy.

## Editing public website content

Admins and Editors can use **Website content** (`/admin/website-content`) for bilingual shared labels, navigation, form labels, feedback and validation messages, template copy, default SEO/social metadata, and template images/logos. Existing Pages, Page Sections and individual content modules still manage their own records and media. Public contact details/footer copy are editable here; SMTP credentials, notification recipients, users and submission management remain Admin-only (Editors can read submission details).

Run `php artisan migrate --force` when deploying this update. The additive `website_texts` and `website_images` tables store overrides; existing content and file-based defaults are preserved without reseeding. Text is escaped, not HTML. Preserve placeholders such as `:year`, `:attribute` and `:max`. Images are selected from public Media; private uploads cannot be selected, and referenced media cannot be deleted. Shared template assets update wherever used. Restoring “Original template image” removes its override. Replacing a leasing plan must preserve its proportions and unit positions; map geometry is not CMS content. Browser-native date/file-picker text remains controlled by the visitor's browser.

WordPress event edit screens warn that the next synchronization can replace local edits. Permanent imported-event changes belong in WordPress; manually created events remain independent. Event-space mobile navigation now has one working button per panel plus its intro, with the active state following scroll position.

## Current limitations

- The public frontend uses the supplied draft template, not yet the final signed-off design. Managed content comes from the CMS; About and the four presentation pages have provisional fixed layout mappings documented in `docs/PROJECT_DECISIONS.md`. Website content manages shared template copy and image overrides without changing layout or map geometry.
- Section ordering uses a numeric field rather than drag-and-drop.
- The native rich-text toolbar intentionally supports only basic formatting.
- There is no automated queue worker because the current workflows are synchronous.

# Piramida CMS project decisions

This file records durable decisions that future tasks should preserve. Implementation and setup details belong in `README.md`.

## Product scope

- The repository contains the Laravel CMS and a temporary public Blade frontend.
- The final HTML, Tailwind CSS, and JavaScript templates may replace the public views without changing the CMS architecture.
- CMS content remains semantic and reusable; temporary CSS classes and Figma-specific layout details do not define the database schema.

## Languages and content storage

- Albanian (`al`) is the default and fallback locale; English (`en`) is secondary.
- Albanian public URLs are unprefixed; English uses `/en`. Legacy `/al` GET/HEAD URLs redirect with 301, preserving queries; legacy POST URLs still accept submissions. Existing locale-aware `public.*` route calls are retained through Laravel's path formatter. Unprefixed routes reuse the same definitions as `default.public.*`; SetLocale supplies the leading controller locale argument. Form Requests recognize both route-name prefixes.
- Leasing navigation uses `/leasing` or `/en/leasing`; event-space listings use `/event-space` or `/en/event-space`. Old `/spaces` listing URLs redirect with 301 to the appropriate catalogue, dropping the old type parameter and retaining other query parameters. Individual space detail and submission URLs remain unchanged.
- Interface translations stay in language files; managed content uses normalized translation tables.
- Shared/queryable values stay on parent records. JSON is limited to optional page-section structures.
- Public content resolves using the requested locale's slug. A missing record translation returns `404`; non-routing child content may fall back to Albanian.

## Presentation pages

- About Us is an editable page assembled from ordered page sections, not a museum module.
- Education, Innovation, and Art & Culture retain Pages for headings/SEO and use the existing Program model for unlimited ordered carousel posts, managed under Programs / Carousel posts. Only published, non-future posts with the requested translation appear. Business continues to use Business records.
- `CarouselProgramSeeder` imports existing section slides once per empty Program category, preserving translations, media and order. It skips categories containing any Program (including deleted records); old sections are retained, not synchronized. They are hidden from Page Sections, cannot be edited there, and never supply public fallback slides. Old editor links redirect to the relevant dynamic catalogue. New presentation demo pages seed Program posts directly. No new schema is required.
- The carousel editor exposes only fields used by the carousel. Historical booking, gallery, location, schedule, price and individual SEO values remain stored and are not cleared on save. Pages still manage the carousel heading and page SEO; Business slides remain exclusively in Businesses.
- Businesses are managed records displayed as experience cards; selecting one may open the designed information modal with its images, description, and location.
- News and general Page Sections may use media galleries. Events do not. Gallery controls show attached thumbnails; a shared media dialog handles selection and image uploads. Removing a thumbnail detaches it on record save, never deletes shared media; arrow controls set gallery order.

## Events and WordPress synchronization

- Piramida Ime's WordPress API is an external source for bilingual events.
- WordPress-imported events and manually created dashboard events coexist. Manual records are never overwritten by the importer.
- Imported translations retain their WordPress IDs. The initial import pairs translations only when the available data gives an unambiguous match; later runs update by stored ID.
- Synchronization retrieves both language feeds before changing removal state. Failed or partial retrieval leaves existing imports unchanged.
- Records absent from a complete upstream response move to draft rather than being deleted.
- Upcoming and past events returned by WordPress are retained for public filtering and content history.
- Synchronization runs daily at 06:00 and 18:00 in `Europe/Tirane` and can also be run manually.
- The WordPress API key stays in the server environment and is never exposed to the browser or committed.
- The event design and current API require one featured image. Event galleries were removed and should return only if a final template or upstream contract requires them.

## Spaces and requests

- Event Spaces and Leasing Spaces are separate dashboard modules with server-fixed types, not an editor-selectable type field. They retain the existing shared space/translation storage and IDs so galleries, localized URLs and historical submissions stay intact.
- Leasing posts select one predefined physical unit from the supplied SVG inventory. The unit defines its floor; a database unique constraint prevents duplicate assignments (including trashed posts).
- The four supplied floor templates contain 40 units: Ground A1–A15 and A17–A20; Third D1/D3; Roof E1/E2; Exterior BE1, BE1/1 and BE2–BE16. Do not invent A16, D2, an unprovided floor template, or unit dimensions from pixels. Areas remain editable on the post.
- PNG renders are the plan backgrounds; supplied SVG paths are the interactive overlays. Available, published and locale-translated leasing posts are green/clickable; every other unit is red/non-clickable. Unavailable direct detail/application URLs are blocked server-side.
- Existing leasing posts remain unassigned/unavailable until staff select their real units. No demo name-to-map association is guessed.
- Event-space requests collect event requirements and preferred timing.
- Leasing applications collect company, contact, offer, and required-document information.
- Event registration, event-space booking, leasing, and career forms are request-based. Staff review and confirm them manually.
- The current phase has no real-time availability, automatic confirmation, online payment, or booking engine.

## Users and security

- Public registration is disabled.
- Admins have full CMS and user-management access.
- Editors manage permitted content and media and have read-only access to the Submissions list/details. Submission updates, private notes, document downloads and CSV exports remain Admin-only, as do users, roles and system settings.
- The final active Admin cannot be demoted or deactivated.
- Sensitive CV and leasing documents remain on private storage.
- Password changes rotate remember-me tokens and revoke database sessions; dashboard requests also use Laravel's authenticated-session password check. Reset emails reuse dashboard Postmark settings with environment mail as the disabled-state fallback.
- Submission CSV exports neutralize formula-leading visitor input. WordPress image imports require exact trusted HTTPS hosts, reject redirects, and enforce a 10 MB transfer limit.

## Infrastructure and deployment

- Docker Compose is the supported local-development environment.
- The live server runs Laravel directly with PHP, a web server, MySQL, Composer, built frontend assets, and Laravel's scheduler; Docker is not required there.
- Redis, a queue worker, and a JavaScript framework are intentionally omitted until a measured requirement appears.
- Secrets and production configuration stay outside Git.
- Search metadata is server-rendered from actual locale translations and existing CMS SEO fields, with bilingual defaults for catalogue/presentation pages. Canonical and social URLs use `SEO_URL` (the final `https://piramida.edu.al` origin), independent of the preview host. Published records alone enter the streamed sitemap; the existing career/space accessibility scopes also apply.
- Indexing requires `SEO_INDEXABLE` and the configured production hostname. Local/staging responses and admin/auth pages are marked noindex; Laravel serves robots.txt dynamically. No AI-only content or new SEO dependency is introduced. Website/organization, news and event structured data describe existing content, without inventing ratings, addresses or ticket offers.

## Current boundary

- The dashboard and content architecture are close to completion.
- The public frontend now adapts the draft `Piramida.zip` template, using `animation.html` as the homepage direction. It remains provisional until the final templates arrive.
- The Museum menu entry is hidden until its template and destination are confirmed; it is not mapped to About Us or an attraction by assumption.
- Template interface copy uses bilingual `website_texts` overrides of known translation keys, applied only on public locale routes. `website_images` references public Media for shared template assets; existing files remain defaults. Admins and Editors manage these through Website content. This is separate from Admin-only technical settings; record content and section data stay in their established modules. No arbitrary HTML, template code, private media, or new dependencies are accepted.
- Public styles and scripts have their own Vite entry points, isolated from Admin. No Tailwind CDN or additional frontend dependencies are needed.
- About uses the English slug `about-us` and the seeded Overview, Mission, History and Timeline section names. Education, Innovation, Business and Art share the revised presentation layout, mapped by English slugs `education`, `innovation`, `business`, `art`. These existing English slugs and About internal section names/parent-page assignments are locked in dashboard forms and validated server-side; translated titles remain editable. Dynamic posts alone supply carousel slides. Other pages keep the generic section renderer. Preserve these identifiers until a final template mapping is agreed.
- The Business presentation page uses the same published, locale-translated Business records as the Businesses directory, rendered in the shared presentation carousel. Its heading remains managed through Pages; its slides come from Businesses, not placeholder Page Sections. Education, Innovation and Art & Culture use Program posts. `PresentationPageSeeder` skips existing pages, including drafts and soft-deleted records, then runs the non-destructive carousel import.
- The revised homepage skips its intro at widths up to 1024px. Careers apply links open native dialogs backed by the existing application endpoint, with standalone detail-page fallbacks. Event details retain one featured image; the updated carousel is for related events, not an event gallery.
- Homepage Play uses the optional shared `pages.homepage_video_url`, editable by Admins and Editors in Pages. Only valid HTTPS YouTube video links are accepted. The privacy-enhanced embed loads on click in a native dialog and stops on close. If empty, the existing section video / About fallback remains. Demo content uses the client-supplied YouTube video.
- New fields or modules should be added only when required by those templates, an approved business workflow, or a stable API contract.
- About History uses its existing section Video URL for a YouTube link or uploaded MP4/WebM. Media accepts videos up to the existing 10 MB limit; larger videos use YouTube. The section editor offers uploaded video URLs. Referenced video media cannot be deleted. Both History and Homepage load playback only when the dialog opens and stop it on close.
- Event-space request buttons open record-specific native dialogs on the listing, retaining standalone forms as no-JavaScript fallbacks. Validation reopens only the submitted form; successful requests remain staff-reviewed.

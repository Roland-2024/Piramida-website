# Piramida CMS project decisions

This file records durable decisions that future tasks should preserve. Implementation and setup details belong in `README.md`.

## Product scope

- The repository contains the Laravel CMS and a temporary public Blade frontend.
- The final HTML, Tailwind CSS, and JavaScript templates may replace the public views without changing the CMS architecture.
- CMS content remains semantic and reusable; temporary CSS classes and Figma-specific layout details do not define the database schema.

## Languages and content storage

- Albanian (`al`) is the default and fallback locale; English (`en`) is secondary.
- Interface translations stay in language files; managed content uses normalized translation tables.
- Shared/queryable values stay on parent records. JSON is limited to optional page-section structures.
- Public content resolves using the requested locale's slug. A missing record translation returns `404`; non-routing child content may fall back to Albanian.

## Presentation pages

- About Us is an editable page assembled from ordered page sections, not a museum module.
- Education, Innovation, Business, and Art & Culture are presentation content managed through Pages and Page Sections, not a separate Program catalogue.
- Businesses are managed records displayed as experience cards; selecting one may open the designed information modal with its images, description, and location.
- News and general Page Sections may use media galleries. Events do not.

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

- Spaces have two business types: event spaces and leasing units.
- Event-space requests collect event requirements and preferred timing.
- Leasing applications collect company, contact, offer, and required-document information.
- Event registration, event-space booking, leasing, and career forms are request-based. Staff review and confirm them manually.
- The current phase has no real-time availability, automatic confirmation, online payment, or booking engine.

## Users and security

- Public registration is disabled.
- Admins have full CMS and user-management access.
- Editors manage permitted content and media but cannot manage users, roles, submissions, or system settings.
- The final active Admin cannot be demoted or deactivated.
- Sensitive CV and leasing documents remain on private storage.

## Infrastructure and deployment

- Docker Compose is the supported local-development environment.
- The live server runs Laravel directly with PHP, a web server, MySQL, Composer, built frontend assets, and Laravel's scheduler; Docker is not required there.
- Redis, a queue worker, and a JavaScript framework are intentionally omitted until a measured requirement appears.
- Secrets and production configuration stay outside Git.

## Current boundary

- The dashboard and content architecture are close to completion.
- The public frontend now adapts the draft `Piramida.zip` template, using `animation.html` as the homepage direction. It remains provisional until the final templates arrive.
- The Museum menu entry remains visible but disabled until the PM confirms its destination; it is not mapped to About Us or an attraction by assumption.
- Template presentation assets and interface copy may remain static. Managed records, page sections, publication rules, languages and request forms continue to use the existing CMS.
- Public styles and scripts have their own Vite entry points, isolated from Admin. No Tailwind CDN or additional frontend dependencies are needed.
- The provisional About and Education layouts are selected by the existing English page slugs (`about-us`, `education`). About uses the seeded Overview, Mission, History and Timeline section names; other pages keep the generic section renderer. Preserve these identifiers until the final template mapping is agreed.
- The unrelated sample video from the draft is not published. Configure a real CMS section video URL when supplied; otherwise the homepage uses the template image and links to About.
- New fields or modules should be added only when required by those templates, an approved business workflow, or a stable API contract.

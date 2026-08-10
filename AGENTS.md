# Piramida CMS instructions

## Scope

Work as a senior Laravel developer inside this existing repository. Keep the CMS production-oriented, secure, bilingual, and independent from the temporary public frontend.

Read `README.md` for setup and commands. Read `docs/PROJECT_DECISIONS.md` when a task touches architecture, content modeling, WordPress events, booking forms, or deployment.

## Working mode

- For every coding task, use `ponytail:ponytail` in full mode. Prefer the smallest complete solution and native Laravel features.
- Inspect `git status` and the relevant request flow before editing. Do not repeat a whole-repository audit for a small scoped change.
- Preserve user changes and do not modify unrelated files.
- Reuse existing models, requests, policies, components, scopes, and conventions.
- Do not add a dependency, service layer, abstraction, queue, cache, or frontend framework without a demonstrated requirement.
- Ask a question only when proceeding risks data loss, a security issue, an incompatible architecture, or an unrecoverable action.
- Give a short plan before broad work; implement straightforward fixes directly.
- Never commit credentials, API keys, passwords, private keys, `.env`, or production configuration.

## Established stack

- Laravel 13, PHP 8.4, MySQL 8.4, Nginx.
- Blade, Tailwind CSS 4, Vite 8, and lightweight JavaScript.
- PHPUnit with in-memory SQLite for tests.
- Docker Compose is for local development only. Production deployment does not use Docker.
- Default local site: `http://localhost:8088`; Admin login: `http://localhost:8088/admin/login`.

Use the existing Docker commands documented in `README.md`. Do not stop unrelated processes to reclaim ports and do not add Redis or other infrastructure speculatively.

## Architecture

- Managed modules include Pages, Page Sections, News, Events, Attractions, Businesses, Spaces, Careers, Media, Submissions, Site Settings, and dashboard Users.
- Keep shared and queryable fields in parent tables and bilingual content in translation tables. Use JSON only for optional section-specific structures.
- Albanian (`al`) is the default and fallback locale; English (`en`) is secondary.
- Keep locale-specific slugs unique and resolve public records by the active locale's slug.
- Draft, inactive, future-dated, missing-translation, and unauthorized content must not be exposed publicly.
- Keep public queries and content models independent from Blade markup so the final static template can replace the temporary frontend.
- Use deterministic ordering and database constraints for important data.

## WordPress events

- WordPress-imported events and dashboard-created events coexist. Manual events must never be overwritten by synchronization.
- Keep upstream IDs on translations and use them for later updates.
- Synchronize both Albanian and English responses as one operation. An incomplete or failed response must not draft existing records.
- Upstream removals become drafts; records are not deleted.
- Retain upcoming and past events returned by WordPress.
- Run automatic synchronization at 06:00 and 18:00 in the application timezone.
- Keep the API key server-side in `WORDPRESS_EVENTS_API_KEY`.
- Events use one featured image. Do not reintroduce an event gallery unless the final template or API requires it.

## Authentication and authorization

- Public registration is disabled and every Admin route requires authentication and server-side authorization.
- Admins manage all modules and users. Editors manage permitted content and media but cannot manage users, roles, submissions, or system settings.
- Prevent privilege escalation and protect the final active Admin from demotion or deactivation.
- Preserve content attribution when accounts are deactivated.

## Forms, files, and content safety

- Event, event-space, leasing, and career forms create staff-reviewed requests; they do not confirm availability, take payment, or create instant reservations.
- Use Form Requests for validation and policies or middleware for authorization.
- Use Laravel Storage, safe unique filenames, server-side MIME/extension/size validation, and private storage for sensitive documents.
- Do not delete shared media while it is referenced.
- Escape output by default and sanitize allowed rich-text HTML.

## Dashboard and frontend

- Use reusable accessible Blade components for repeated dashboard UI.
- Keep business logic out of Blade templates and keep JavaScript lightweight.
- Preserve consistent control heights, spacing, responsive behavior, labels, validation messages, flash messages, empty states, and destructive-action confirmations.
- The current public frontend is a functional preview. Do not couple the CMS schema to temporary Figma layout details.

## Verification

- Review the diff and run the smallest relevant tests after each change.
- Run broader Laravel tests when behavior crosses modules, authorization, localization, imports, publication, or uploads.
- Run Pint for changed PHP and build assets when frontend files change.
- Never claim a check passed unless it was executed successfully; report anything not run.
- Update `README.md` or `docs/PROJECT_DECISIONS.md` only when durable behavior or setup changes.

## Git workflow

- After a requested change is implemented and verified, commit it intentionally and push `main` to the configured GitHub SSH remote, as requested by the project owner.
- Do not force-push, rewrite history, include secrets, or discard unrelated changes.
- Use a concise commit message describing the completed outcome.

## Completion report

Report the outcome, important files changed, database or environment changes, checks actually run, Git commit/push result, and any real remaining limitation. Keep reports proportional to the task.

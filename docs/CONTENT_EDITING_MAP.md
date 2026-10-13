# Dashboard to frontend content map

Reviewed against the current Blade templates on 13 October 2026. This is a source/usage map, not a promise that every retained CMS field is visible in every template. Public output is subject to publication, date, active-state and locale rules. Nothing in this update deletes or reseeds existing content.

## Where editors should make changes

| Public element | Editing location | Precedence / limitation |
| --- | --- | --- |
| Homepage large heading | Page sections > Homepage - Discover > Subtitle | Named active Discover section, then first active Hero, then first active Text and image. Shared hero labels are fallbacks only. |
| Homepage supporting title/body/actions | Same section > Title, Description, button label/URL pairs | Rich text is sanitized. Empty button label or URL hides the action. |
| Homepage hero background | Website content > Images and logos | Discover section images are retained, not hero-background controls. |
| Homepage pillars | First active Features section (normally Homepage - Pillars) | Title overrides `cms.pillars_title`; Subtitle/Description are displayed; first four locale-specific JSON items provide card titles/text. Images are shared template assets; destinations come from published presentation Pages. |
| Homepage video | Pages > Homepage YouTube video | Takes priority over the first active section video; otherwise About link. Poster is the shared template asset. |
| Homepage attraction card | First published, featured Attraction in display order | Title, Short description and Featured image. Shared label/template image are fallbacks; the old Homepage - Attraction section is retained but unused. |
| Homepage Social Spaces card | Website content labels/images | `cms.social_spaces`, `cms.businesses_intro`, `cms.explore_corners`; the old Homepage - Experiences section is unused. |
| Homepage events/news | Events / News records | Published localized records; events must be upcoming. News newest publication first. Shared section headings/actions stay in Website content. |
| Footer partners | Website content > Images and logos | Three template logos. Homepage - Partners is retained but not rendered. |
| About heading/SEO | Pages > About | Other page content is retained; the fixed layout uses named sections below. |
| About Overview | Page sections > About - Overview | Title, Subtitle, Description, Primary image. Page Featured image then template image are fallbacks. |
| About Mission | Page sections > About - Mission | Locale JSON items (text or title) beneath Overview. Other fields are retained, not displayed. |
| About History | Page sections > About - History | Title, Subtitle, Description, Primary image/poster and Video URL. |
| About Timeline | Page sections > About - Timeline | Title and locale JSON items. Timeline images come from Website content. |
| Education / Innovation / Art carousel | Programs / Carousel posts | Title, caption, Featured image, category, publication and order. Page controls heading and SEO. Legacy sections are not used. |
| Business presentation carousel | Businesses | Name, Description and Featured image. Page controls heading/SEO. Business categories do not feed Program carousels. |
| Generic pages | Pages and their active Page sections | Page Title, Short description, Main content; sections render Title, Subtitle, Description, images, button pairs, JSON items, video and galleries. Secondary image requires Primary image. Arbitrary JSON has no guaranteed visual meaning. |
| Navigation, buttons and form copy | Website content | Shared labels, not a second copy of page/record body fields. Keep placeholders. Some retained legacy labels have no current visual use. |
| Contact/footer details | Website content > Public contact and footer | Same SiteSetting storage as Admin Site settings. Public email/phone, Address, Footer text, Facebook/Instagram and Contact map are used. X/LinkedIn/Opening hours are retained but not currently displayed. |
| SEO/social descriptions | Record/Page SEO first; Website content defaults second | Not necessarily visible body text. Defaults are route-specific. Changing a default does not override a filled record SEO field. |
| Static 404 | File-based bilingual error template | Deliberately independent of CMS content/database. |

## Record modules and retained fields

| Module | Current public connections | Retained / non-visual fields |
| --- | --- | --- |
| News | Title, publication date, Featured image, Article content, Gallery, card Excerpt, localized slug, SEO | Author name has no visible byline. Created/updated attribution is administrative. |
| Events | Title, Featured image, Full description, starts/ends/duration, Location, verified address, booking actions, homepage Short description, slug, SEO/schema | Category, Featured flag, Capacity and Price label are not displayed by this template. Archive order is starts_at descending, not Display order; homepage upcoming selection differs. WordPress source IDs/sync timestamps are administrative. |
| Attractions | Carousel Featured image/Title/Location/Short description; detail Description/Gallery/Visitor information; Featured selects homepage card; slug/SEO | No clickable carousel popup. Preserve galleries; the existing localized detail route already displays them. |
| Businesses | Categories, directory/presentation cards, Name, Featured image, popup Logo/Gallery/Description or Short description, Address, Opening hours, Website URL, Email, Phone, slug/SEO | Featured is retained, not used by the current public queries. Directory is Social Spaces only; Business presentation includes all published localized businesses. |
| Event spaces | Listing Title/Featured image/Location/Area/Capacity; request dialog Title and booking mode; localized detail metadata | Gallery, Features and price fields are retained, not shown by the request-first layout. Description is shown on no-internal-form detail pages, not the request dialog. Short description is a metadata fallback. Featured is unused. |
| Leasing spaces | Real map unit/floor, Available, publication/locale gates; detail Title, Featured image/Gallery, Description, Area; booking options, SEO | Location is a fallback to unit location. Capacity/Features/price fields and Featured are retained but not displayed. Visitor offer and Area determine application rent, not Price from. |
| Careers | Title, Description, Requirements, Location, Deadline gates, booking actions, slug/SEO | Department/Employment type are retained but not displayed. Short description is metadata fallback. No gallery/media fields. |
| Media | Referenced public files; original files and optional display variants; gallery alt text and social alt fallback | Not every template uses Media alt text: some use the record title/context. Editing alt text does not change record captions. Removing from a gallery detaches; it does not delete the file. |
| Submissions | Created by public request forms; staff-only dashboard review | Status, notes, attachments, exports and staff attribution must never be wired into public templates. Editor remains read-only with existing restrictions. |
| Users | Authentication, authorization and staff attribution | Accounts, roles, active state and credentials are not public content. |
| Site settings | Shared public details via Website content; Admin-only mail recipient/Postmark | Mail settings configure delivery, not public labels. SMTP/Postmark remains an uncompleted deployment task until configured/tested. |

## Editor guidance and preservation

- Forms now explain fixed-template field usage. Website content marks the known overlapping hero/pillars defaults as fallbacks and links to Page sections.
- Confirmed unused shared labels are marked "Retained legacy label": language, more_links, museum/museum_pending, skip_intro, world_of_piramida, home, upcoming/past, price, programs, space_types, subject, organization, participant_age, requested_start/end, no_image, opening_hours and full_page. Constructed leasing field/entity/document labels are used and are not marked unused merely because their full keys do not appear literally in Blade.
- All input fields and existing stored values remain intact. No migration, bulk copy, auto-merge, deletion or reseed is performed. Conflicting existing text is not silently overwritten.
- A field unused by a fixed template is not necessarily unused by generic pages, SEO, a detail URL, historical data or another template. It is retained and explained rather than removed.
- Sections on carousel Pages remain redirected to the existing dynamic modules. This update does not reintroduce old section-based carousel slides.
- AL and EN saves, fallback precedence, inactive section preservation, Attraction galleries/visitor information, featured homepage images and Business public email have feature tests. Existing publication, authorization, media and template tests remain applicable.
- Button feedback is dashboard-only CSS: pointer, hover, pressed, keyboard focus and disabled states, with reduced-motion support. No public carousel behavior is changed.

## Boundaries

This audit traces current forms/controllers/models and template/SEO consumers. It is not a live-server inspection or a manual save of every production record. Retained fields listed above are not newly made visible merely to fill the design. A future Attractions popup needs its own approved layout; no popup is introduced here.

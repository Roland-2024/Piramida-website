<?php

// Editor guidance for the fixed templates; it does not alter or delete stored content.
return [
    'sections' => [
        'home' => [
            'Homepage - Discover' => 'Homepage hero: Subtitle is the large headline; Title and Description appear below it. Primary/secondary button labels and URLs appear when both are filled. The background image is edited in Website content > Images and logos. Section images, gallery and JSON are retained but not shown here.',
            'Homepage - Pillars' => 'Homepage pillars: Title, Subtitle, Description and the first four AL/EN JSON items (title/text) are displayed. Card images are edited in Website content > Images and logos; card links come from the published presentation Pages. Other fields are retained but not displayed.',
            'Homepage - Attraction' => 'Retained legacy section. The current homepage attraction card uses the first published, featured Attraction (title, short description and featured image), not this section. Edit Attractions instead.',
            'Homepage - Experiences' => 'Retained legacy section. The current Social Spaces card uses shared Website content labels and template images. Business records are edited in Businesses; this section is not displayed.',
            'Homepage - Partners' => 'Retained legacy section. Footer partner logos are edited in Website content > Images and logos. This section is not displayed by the current homepage.',
            '*' => 'The fixed homepage uses Homepage - Discover (or the first active Hero/Text and image section), the first active Features section for pillars, and the first section with a video as the Play fallback. Arbitrary additional sections are not automatically displayed. Other saved fields are retained.',
        ],
        'about-us' => [
            'About - Overview' => 'About overview: Title, Subtitle, Description and Primary image are displayed. Other fields are retained but not displayed by this block.',
            'About - Mission' => 'About mission: AL/EN JSON items (text, or title when text is empty) are displayed beneath Overview. The section Title, Subtitle, Description, images and buttons are retained but not displayed.',
            'About - History' => 'About history: Title, Subtitle, Description, Primary image (poster) and Video URL are displayed. Other fields are retained but not displayed.',
            'About - Timeline' => 'About timeline: Title and AL/EN JSON items (title/text) are displayed. Timeline images are edited in Website content > Images and logos. Other fields are retained but not displayed.',
            '*' => 'The fixed About template displays only About - Overview, About - Mission, About - History and About - Timeline. Additional sections are retained but not displayed.',
        ],
    ],
    'texts' => [
        'cms.pillars_title' => 'Fallback only: the homepage uses Page sections > Homepage - Pillars > Title when filled.',
        'cms.discover' => 'Hero fallback only: edit the homepage headline in Page sections > Homepage - Discover > Subtitle.',
        'cms.world_of' => 'Hero fallback only: edit the homepage headline in Page sections > Homepage - Discover > Subtitle.',
        'website.home_piramida' => 'Shared brand/alt label and hero fallback; not the headline when Homepage - Discover > Subtitle is filled.',
        'cms.step_into_piramida' => 'Homepage attraction button label and fallback title. The card content comes from the first published, featured Attraction.',
    ],
    'retained_texts' => [
        'cms.language', 'cms.more_links', 'cms.museum', 'cms.museum_pending',
        'cms.skip_intro', 'cms.world_of_piramida', 'cms.home', 'cms.upcoming',
        'cms.past', 'cms.price', 'cms.programs', 'cms.space_types', 'cms.subject',
        'cms.organization', 'cms.participant_age', 'cms.requested_start',
        'cms.requested_end', 'cms.no_image', 'cms.opening_hours', 'cms.full_page',
    ],
    'catalogs' => [
        'admin.attractions' => 'The carousel displays Featured image, Title, Location and Short description. Gallery images, Description and Visitor information appear on the existing attraction detail URL, not in a carousel popup. Featured selects the homepage card; SEO fields describe the detail page. Galleries are retained; no new popup is enabled.',
        'admin.businesses' => 'Cards use Name and Featured image; the popup/detail page uses Logo, Categories, Description (or Short description), Gallery, Address, Opening hours, Website URL, Public email and Public phone. Featured is retained but not used by the current templates. Business presentation slides use Name, Featured image and Description.',
        'admin.spaces' => 'The listing uses Title, Featured image, Location, Area and Capacity. Request dialogs use Title and booking options. Gallery, Short description, Description, Features and price fields are retained; not all are displayed by the current request-first template. SEO fields describe the detail page; Featured is not used by this template.',
        'admin.leasing' => 'The map uses Floor / map unit, Available for applications and publication/translation state. Detail/application pages use Title, Featured image, Gallery, Description and Area. Booking options control actions. Capacity, Features and price fields are retained but not displayed by this template; the visitor offer determines the application calculation.',
        'admin.careers' => 'The listing uses Title, Description, Requirements and Location. Deadline controls whether applications remain open; booking options control actions. Department and Employment type are retained but not displayed. Short description is a SEO fallback, not the listing body.',
    ],
];

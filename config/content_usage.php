<?php

// Editor guidance for the fixed templates; it does not alter or delete stored content.
return [
    'sections' => [
        'home' => [
            'Homepage - Discover' => 'Homepage hero: Subtitle is the large headline; Title and Description appear below it. Primary/secondary button labels and URLs appear when both are filled. The background image is edited in Website content > Images and logos. Section images, gallery and JSON are retained but not shown here.',
            'Homepage - Pillars' => 'Homepage pillars: Title, Subtitle, Description and the first four AL/EN JSON items (title/text) are displayed. Card images are edited in Website content > Images and logos; card links come from the published presentation Pages. Other fields are retained but not displayed.',
            'Homepage - Attraction' => 'Homepage left promotional card: Subtitle supplies the left part of the Attractions & Experiences heading. Title, Description and Primary image control the card. Fill each button label/URL pair to customize actions; leave both primary fields empty for the default Attractions link. A blank image uses the template fallback. Inactive hides the card. Other fields are retained but not shown. This does not change individual Attraction records.',
            'Homepage - Experiences' => 'Homepage right promotional card: Subtitle supplies the right part of the Attractions & Experiences heading. Title, Description and Primary image control the card. Fill each button label/URL pair to customize actions; leave both primary fields empty for the default Businesses link. A blank image uses the template fallback. Inactive hides the card. Other fields are retained but not shown. This does not change individual Business records.',
            'Homepage - Partners' => 'Footer partners across the website: Title is the heading; Subtitle and Description are optional supporting text. Gallery / partner logos controls the ordered logos. Empty gallery uses the three template logos from Website content > Images and logos. Inactive hides the entire partner block. Primary/secondary images, video, buttons and JSON are retained but not displayed. The parent homepage must be published.',
            '*' => 'The fixed homepage uses Discover for its hero, the first active Features section for pillars, Attraction and Experiences for promotional cards, and Partners for footer logos across the site. The first active section video is the Play fallback. Arbitrary additional sections are not automatically displayed.',
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
        'cms.step_into_piramida' => 'Default homepage Attraction button label when the section primary label/URL are both empty. Card copy is edited in Homepage - Attraction. The featured Attraction is used only when that section is absent.',
        'cms.social_spaces' => 'Homepage right-card title fallback only when Homepage - Experiences is absent; edit its configured Title in Page sections.',
        'cms.businesses_intro' => 'Homepage right-card body fallback only when Homepage - Experiences is absent; edit its configured Description in Page sections.',
        'cms.explore_corners' => 'Default homepage Experiences button label when its section primary label/URL are both empty.',
        'website.partials_footer_partner_t_tan' => 'Footer partner heading fallback only when Homepage - Partners is absent. Edit the configured heading in Page sections > Homepage - Partners > Title.',
    ],
    'images' => [
        'template/images/education.png' => 'Homepage pillar background: Education.',
        'template/images/innovation.png' => 'Homepage pillar background: Innovation.',
        'template/images/business.png' => 'Homepage pillar background: Business (independent from Innovation).',
        'template/images/art.png' => 'Homepage pillar background: Art & Culture (independent from Innovation).',
        'template/images/piramida_block.jpg' => 'Homepage Attraction image fallback when the configured section has no Primary image. Set the card image in Page sections > Homepage - Attraction.',
        'template/images/piramida_block_1.jpg' => 'Shared template/SEO fallback image. Homepage Experiences uses it only when its section has no Primary image.',
        'template/images/aadffinalk-logo 1.svg' => 'Footer logo fallback only when Homepage - Partners has an empty Gallery. Manage configured partner logos in that section.',
        'template/images/bashkia2 1.svg' => 'Footer logo fallback only when Homepage - Partners has an empty Gallery. Manage configured partner logos in that section.',
        'template/images/tumo-finale2 1.svg' => 'Footer logo fallback only when Homepage - Partners has an empty Gallery. Manage configured partner logos in that section.',
    ],
    'retained_texts' => [
        'cms.language', 'cms.more_links', 'cms.museum', 'cms.museum_pending',
        'cms.skip_intro', 'cms.world_of_piramida', 'cms.home', 'cms.upcoming',
        'cms.past', 'cms.price', 'cms.programs', 'cms.space_types', 'cms.subject',
        'cms.organization', 'cms.participant_age', 'cms.requested_start',
        'cms.requested_end', 'cms.no_image', 'cms.opening_hours', 'cms.full_page',
    ],
    'catalogs' => [
        'admin.attractions' => 'The carousel displays Featured image, Title, Location and Short description. Gallery images, Description and Visitor information appear on the existing attraction detail URL, not in a carousel popup. The homepage promotional card is edited in Page sections > Homepage - Attraction; Featured is a legacy fallback only when that section is absent. SEO fields describe the detail page. Galleries are retained; no new popup is enabled.',
        'admin.businesses' => 'Cards use Name and Featured image; the popup/detail page uses Logo, Categories, Description (or Short description), Gallery, Address, Opening hours, Website URL, Public email and Public phone. Featured is retained but not used by the current templates. Business presentation slides use Name, Featured image and Description.',
        'admin.spaces' => 'The listing uses Title, Featured image, Location, Area and Capacity. Request dialogs use Title and booking options. Gallery, Short description, Description, Features and price fields are retained; not all are displayed by the current request-first template. SEO fields describe the detail page; Featured is not used by this template.',
        'admin.leasing' => 'The map uses Floor / map unit, Available for applications and publication/translation state. Detail/application pages use Title, Featured image, Gallery, Description and Area. Booking options control actions. Capacity, Features and price fields are retained but not displayed by this template; the visitor offer determines the application calculation.',
        'admin.careers' => 'The listing uses Title, Description, Requirements and Location. Deadline controls whether applications remain open; booking options control actions. Department and Employment type are retained but not displayed. Short description is a SEO fallback, not the listing body.',
    ],
];

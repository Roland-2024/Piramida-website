<?php

namespace Database\Seeders;

use App\Enums\BookingMode;
use App\Enums\BusinessCategory;
use App\Enums\ContentStatus;
use App\Enums\EmploymentType;
use App\Enums\EventCategory;
use App\Enums\SectionType;
use App\Enums\SpaceType;
use App\Models\Attraction;
use App\Models\Business;
use App\Models\Career;
use App\Models\Event;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DemoContentSeeder extends Seeder
{
    /**
     * Demo assets exported from the approved Figma prototype.
     *
     * @var array<string, array{file: string, mime: string, width: int, height: int, al: string, en: string}>
     */
    private const MEDIA = [
        'front' => ['file' => 'piramida-front.jpg', 'mime' => 'image/jpeg', 'width' => 2560, 'height' => 1920, 'al' => 'Pamje ballore e Piramidës së Tiranës', 'en' => 'Front view of the Pyramid of Tirana'],
        'aerial' => ['file' => 'piramida-aerial.jpg', 'mime' => 'image/jpeg', 'width' => 1920, 'height' => 1438, 'al' => 'Pamje ajrore e Piramidës dhe qendrës së Tiranës', 'en' => 'Aerial view of Piramida and central Tirana'],
        'education_vr' => ['file' => 'education-vr.jpg', 'mime' => 'image/jpeg', 'width' => 1600, 'height' => 1066, 'al' => 'Të rinj duke provuar realitetin virtual', 'en' => 'Young people exploring virtual reality'],
        'education_workshop' => ['file' => 'education-workshop.png', 'mime' => 'image/png', 'width' => 313, 'height' => 209, 'al' => 'Punëtori edukative në Piramidë', 'en' => 'Education workshop at Piramida'],
        'asterix' => ['file' => 'event-asterix.jpg', 'mime' => 'image/jpeg', 'width' => 1080, 'height' => 1350, 'al' => 'Posteri i shfaqjes së filmit Asterix dhe Obelix', 'en' => 'Asterix and Obelix film screening poster'],
        'tung_ideve' => ['file' => 'event-tung-ideve.jpg', 'mime' => 'image/jpeg', 'width' => 320, 'height' => 427, 'al' => 'Posteri i TUNG IDEVE Spring of Innovation', 'en' => 'TUNG IDEVE Spring of Innovation poster'],
        'glass' => ['file' => 'event-glass.jpg', 'mime' => 'image/jpeg', 'width' => 1080, 'height' => 1440, 'al' => 'Posteri i punëtorisë Glass Experience', 'en' => 'Glass Experience workshop poster'],
        'stairs' => ['file' => 'attraction-stairs.jpg', 'mime' => 'image/jpeg', 'width' => 480, 'height' => 360, 'al' => 'Shkallët e jashtme të Piramidës', 'en' => 'Piramida exterior steps'],
        'rooftop' => ['file' => 'piramida-rooftop.jpg', 'mime' => 'image/jpeg', 'width' => 480, 'height' => 360, 'al' => 'Vizitorë në tarracën e Piramidës', 'en' => 'Visitors on the Piramida rooftop'],
        'tumo' => ['file' => 'tumo-interior.jpg', 'mime' => 'image/jpeg', 'width' => 300, 'height' => 200, 'al' => 'Hapësirat shumëngjyrëshe të TUMO-s', 'en' => 'Colourful TUMO learning spaces'],
        'meeting' => ['file' => 'meeting-room.jpg', 'mime' => 'image/jpeg', 'width' => 344, 'height' => 194, 'al' => 'Hapësirë e ndriçuar për takime', 'en' => 'Bright meeting space'],
        'creative_hub' => ['file' => 'creative-hub.jpg', 'mime' => 'image/jpeg', 'width' => 480, 'height' => 360, 'al' => 'Hapësira kreative brenda Piramidës', 'en' => 'Creative spaces inside Piramida'],
        'office' => ['file' => 'office-space.png', 'mime' => 'image/png', 'width' => 480, 'height' => 296, 'al' => 'Zyrë moderne për ekipe kreative', 'en' => 'Modern office for creative teams'],
        'yellow_house' => ['file' => 'yellow-house.jpg', 'mime' => 'image/jpeg', 'width' => 480, 'height' => 360, 'al' => 'Hapësirë e verdhë pranë Piramidës', 'en' => 'Yellow creative space beside Piramida'],
    ];

    public function run(): void
    {
        $ownerId = User::query()->where('role', 'admin')->value('id');
        $media = $this->seedMedia($ownerId);

        $this->seedPages($ownerId, $media);
        $this->seedNews($ownerId, $media);
        $this->seedEvents($ownerId, $media);
        $this->seedAttractions($ownerId, $media);
        $this->seedBusinesses($ownerId, $media);
        $this->seedSpaces($ownerId, $media);
        $this->seedCareers($ownerId);
        $this->seedSiteSettings();
    }

    /** @return array<string, Media> */
    private function seedMedia(?int $ownerId): array
    {
        $records = [];

        foreach (self::MEDIA as $key => $definition) {
            $source = database_path('seeders/assets/demo/'.$definition['file']);
            $path = 'media/demo/'.$definition['file'];

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, File::get($source));
            }

            $media = Media::withTrashed()->firstOrNew(['path' => $path]);
            if ($media->trashed()) {
                $media->restore();
            }
            $media->fill([
                'disk' => 'public',
                'original_name' => $definition['file'],
                'mime_type' => $definition['mime'],
                'extension' => File::extension($source),
                'size' => File::size($source),
                'width' => $definition['width'],
                'height' => $definition['height'],
                'alt_text_al' => $definition['al'],
                'alt_text_en' => $definition['en'],
                'created_by' => $media->created_by ?: $ownerId,
                'updated_by' => $ownerId,
            ])->save();

            $records[$key] = $media;
        }

        return $records;
    }

    /** @param array<string, Media> $media */
    private function seedPages(?int $ownerId, array $media): void
    {
        $home = $this->upsertTranslated(
            Page::class,
            ['kryefaqja', 'home'],
            [
                'featured_media_id' => $media['front']->id,
                'is_homepage' => true,
                'status' => ContentStatus::Published,
                'published_at' => now()->subMonth(),
                'display_order' => 0,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ],
            [
                'al' => [
                    'title' => 'Hapësira për t’u lidhur, krijuar dhe mësuar',
                    'slug' => 'kryefaqja',
                    'short_description' => 'Teknologjia, kultura dhe komuniteti bashkohen në një destinacion ikonik në zemër të Tiranës.',
                    'content' => null,
                    'seo_title' => 'Piramida e Tiranës',
                    'seo_description' => 'Një qendër e hapur për teknologji, kulturë, edukim, evente dhe komunitet.',
                ],
                'en' => [
                    'title' => 'The space to connect, build and learn',
                    'slug' => 'home',
                    'short_description' => 'Technology, culture and community come together in one iconic destination at the heart of Tirana.',
                    'content' => null,
                    'seo_title' => 'Piramida of Tirana',
                    'seo_description' => 'An open hub for technology, culture, education, events and community.',
                ],
            ],
        );

        $this->upsertSection($home, 'Homepage - Discover', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['aerial']->id,
            'display_order' => 10,
        ], [
            'al' => ['subtitle' => 'ZBULO BOTËN E PIRAMIDËS', 'title' => 'Një pikë takimi për qytetin', 'description' => '<p>Piramida është një vend ku njerëzit, idetë dhe energjia e Tiranës takohen çdo ditë. Vizitoni hapësirat, njihni komunitetin dhe shikoni qytetin nga një kënd i ri.</p>'],
            'en' => ['subtitle' => 'DISCOVER THE WORLD OF PIRAMIDA', 'title' => 'A meeting point for the city', 'description' => '<p>Piramida is where people, ideas and the energy of Tirana meet every day. Explore its spaces, discover its community and see the city from a new perspective.</p>'],
        ], ['Demo introduction']);

        $this->upsertSection($home, 'Homepage - Pillars', [
            'type' => SectionType::Features,
            'primary_media_id' => null,
            'display_order' => 20,
            'structured_data' => [
                'al' => ['items' => [
                    ['title' => 'EDUKIM', 'text' => 'Hapësira për të mësuar, eksperimentuar dhe zhvilluar aftësi të reja.'],
                    ['title' => 'INOVACION', 'text' => 'Teknologji, ide dhe mjete që e kthejnë kuriozitetin në projekte.'],
                    ['title' => 'BIZNES', 'text' => 'Një ekosistem për sipërmarrje, bashkëpunim dhe rritje.'],
                    ['title' => 'ART & KULTURË', 'text' => 'Ekspozita, evente dhe programe që sjellin artin dhe njerëzit bashkë.'],
                ]],
                'en' => ['items' => [
                    ['title' => 'EDUCATION', 'text' => 'Spaces to learn, experiment and build new skills.'],
                    ['title' => 'INNOVATION', 'text' => 'Technology, ideas and tools that turn curiosity into projects.'],
                    ['title' => 'BUSINESS', 'text' => 'An ecosystem for entrepreneurship, collaboration and growth.'],
                    ['title' => 'ART & CULTURE', 'text' => 'Exhibitions, events and cultural programmes that bring art and people together.'],
                ]],
            ],
        ], [
            'al' => ['title' => 'Hapësira për t’u mbledhur, lidhur dhe për të ndier lëvizjen e qytetit', 'description' => '<p>Katër fusha plotësojnë njëra-tjetrën dhe krijojnë përvoja për çdo vizitor.</p>'],
            'en' => ['title' => 'Spaces to gather, connect and feel the city move', 'description' => '<p>Four complementary themes create experiences for every visitor.</p>'],
        ]);

        $this->upsertSection($home, 'Homepage - Attraction', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['stairs']->id,
            'display_order' => 30,
        ], [
            'al' => ['subtitle' => 'ATRAKSIONE', 'title' => '100 shkallë drejt majës', 'description' => '<p>Ngjituni në çatinë e Piramidës dhe shikoni Tiranën të shpaloset para jush. Rruga është po aq e veçantë sa pamja.</p>'],
            'en' => ['subtitle' => 'ATTRACTIONS', 'title' => '100 Steps to the Top', 'description' => '<p>Climb to Piramida’s rooftop and watch Tirana unfold around you. The journey is as memorable as the view.</p>'],
        ]);

        $this->upsertSection($home, 'Homepage - Experiences', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['rooftop']->id,
            'display_order' => 40,
        ], [
            'al' => ['subtitle' => 'EKSPERIENCA', 'title' => 'Zbuloni çdo cep', 'description' => '<p>Një rrjet vendesh për t’u takuar, pushuar, punuar dhe shijuar qytetin—nga kafenetë te tarraca.</p>'],
            'en' => ['subtitle' => 'EXPERIENCES', 'title' => 'Explore every corner', 'description' => '<p>A network of places to meet, unwind, work and enjoy the city—from cafés to the rooftop.</p>'],
        ]);

        $this->upsertSection($home, 'Homepage - Partners', [
            'type' => SectionType::Partners,
            'primary_media_id' => null,
            'display_order' => 50,
            'structured_data' => [
                'al' => ['items' => [['title' => 'Bashkia Tiranë'], ['title' => 'TUMO'], ['title' => 'Fondacioni Shqiptaro-Amerikan për Zhvillim']]],
                'en' => ['items' => [['title' => 'Municipality of Tirana'], ['title' => 'TUMO'], ['title' => 'Albanian-American Development Foundation']]],
            ],
        ], [
            'al' => ['subtitle' => 'PARTNERËT TANË', 'title' => 'Ndërtuar përmes bashkëpunimit'],
            'en' => ['subtitle' => 'OUR PARTNERS', 'title' => 'Built through collaboration'],
        ]);

        $education = $this->upsertTranslated(Page::class, ['edukim', 'education'], [
            'featured_media_id' => $media['education_vr']->id,
            'is_homepage' => false,
            'status' => ContentStatus::Published,
            'published_at' => now()->subMonth(),
            'display_order' => 10,
            'created_by' => $ownerId,
            'updated_by' => $ownerId,
        ], [
            'al' => ['title' => 'Edukimi', 'slug' => 'edukim', 'short_description' => 'Një faqe prezantuese për mësimin, kërkimin dhe krijimtarinë në Piramidë.', 'content' => '<p>Piramida sjell së bashku të rinjtë, mentorët dhe teknologjinë në një mjedis ku dija kthehet në përvojë.</p>', 'seo_title' => 'Edukimi në Piramidë', 'seo_description' => 'Mësim, kërkim dhe eksperienca krijuese për të rinjtë.'],
            'en' => ['title' => 'Education', 'slug' => 'education', 'short_description' => 'A presentation of learning, research and creativity at Piramida.', 'content' => '<p>Piramida brings young people, mentors and technology together in an environment where knowledge becomes experience.</p>', 'seo_title' => 'Education at Piramida', 'seo_description' => 'Learning, research and creative experiences for young people.'],
        ]);

        $this->upsertSection($education, 'Education - Metro Research', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['education_vr']->id,
            'secondary_media_id' => $media['education_workshop']->id,
            'gallery_media_ids' => [$media['education_vr']->id, $media['education_workshop']->id, $media['tumo']->id],
            'display_order' => 10,
        ], [
            'al' => ['subtitle' => 'METRO RESEARCH', 'title' => 'Mësoni duke krijuar', 'description' => '<p>Programet dhe aktivitetet edukative përdorin teknologjinë, dizajnin dhe punën në grup për të ndërtuar aftësi reale. Kjo përmbajtje prezantuese mund të ndryshohet nga paneli si çdo faqe tjetër.</p>'],
            'en' => ['subtitle' => 'METRO RESEARCH', 'title' => 'Learn by making', 'description' => '<p>Educational activities use technology, design and teamwork to build practical skills. This presentation content can be changed from the dashboard like every other page.</p>'],
        ]);

        $this->upsertSection($education, 'Education - Themes', [
            'type' => SectionType::Features,
            'display_order' => 20,
            'structured_data' => [
                'al' => ['items' => [['title' => 'Teknologji kreative', 'text' => 'Mjete digjitale për të realizuar ide.'], ['title' => 'Punë në grup', 'text' => 'Projekte të përbashkëta dhe mentorim.'], ['title' => 'Eksperimentim', 'text' => 'Një vend i sigurt për të provuar dhe mësuar.']]],
                'en' => ['items' => [['title' => 'Creative technology', 'text' => 'Digital tools that help ideas become real.'], ['title' => 'Teamwork', 'text' => 'Shared projects and mentorship.'], ['title' => 'Experimentation', 'text' => 'A safe place to test, learn and improve.']]],
            ],
        ], [
            'al' => ['title' => 'Një hapësirë për kuriozitet'],
            'en' => ['title' => 'A space for curiosity'],
        ]);

        $about = $this->upsertTranslated(Page::class, ['rreth-nesh', 'about-us'], [
            'featured_media_id' => $media['aerial']->id,
            'is_homepage' => false,
            'status' => ContentStatus::Published,
            'published_at' => now()->subMonth(),
            'display_order' => 20,
            'created_by' => $ownerId,
            'updated_by' => $ownerId,
        ], [
            'al' => ['title' => 'Rreth nesh', 'slug' => 'rreth-nesh', 'short_description' => 'Rimendojmë një monument për një brez të ri.', 'content' => null, 'seo_title' => 'Rreth Piramidës', 'seo_description' => 'Historia, misioni dhe transformimi i Piramidës së Tiranës.'],
            'en' => ['title' => 'About us', 'slug' => 'about-us', 'short_description' => 'Reimagining a landmark for a new generation.', 'content' => null, 'seo_title' => 'About Piramida', 'seo_description' => 'The history, mission and transformation of the Pyramid of Tirana.'],
        ]);

        $this->upsertSection($about, 'About - Overview', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['front']->id,
            'display_order' => 10,
        ], [
            'al' => ['subtitle' => 'VËSHTRIM I PËRGJITHSHËM / MISIONI', 'title' => 'Rimendojmë një monument për një brez të ri', 'description' => '<p>Dikur simbol i një epoke tjetër, Piramida e Tiranës është transformuar në një qendër të gjallë për kulturën, teknologjinë dhe komunitetin.</p><p>Sot ajo qëndron si simbol i transformimit, krijimtarisë dhe hapjes.</p>'],
            'en' => ['subtitle' => 'OVERVIEW / MISSION', 'title' => 'Reimagining a Landmark for a New Generation', 'description' => '<p>Once a symbol of a different era, the Pyramid of Tirana has been transformed into a vibrant hub for culture, technology and community.</p><p>Today it stands as a symbol of transformation, creativity and openness.</p>'],
        ]);

        $this->upsertSection($about, 'About - Mission', [
            'type' => SectionType::Features,
            'display_order' => 20,
            'structured_data' => [
                'al' => ['items' => [['title' => 'Fuqizojmë të rinjtë', 'text' => 'Hapësira të aksesueshme për teknologji, edukim dhe mësim krijues.'], ['title' => 'Ruajmë historinë', 'text' => 'Rivlerësojmë domethënien kulturore të Piramidës së Tiranës.'], ['title' => 'Jemi të hapur', 'text' => 'Një mjedis gjithëpërfshirës për çdo vizitor dhe ide.'], ['title' => 'Lidhim rajonin', 'text' => 'Tërheqim talent, partnerë dhe evente që vendosin Tiranën në hartën e inovacionit.']]],
                'en' => ['items' => [['title' => 'Empower youth', 'text' => 'Accessible spaces for technology, education and creative learning.'], ['title' => 'Preserve history', 'text' => 'Protect and reinterpret the cultural significance of the Pyramid of Tirana.'], ['title' => 'Stay open', 'text' => 'An inclusive environment for every visitor and idea.'], ['title' => 'Connect the region', 'text' => 'Attract talent, partners and events that place Tirana on the innovation map.']]],
            ],
        ], [
            'al' => ['title' => 'Misioni ynë'],
            'en' => ['title' => 'Our mission'],
        ]);

        $this->upsertSection($about, 'About - History', [
            'type' => SectionType::TextImage,
            'primary_media_id' => $media['aerial']->id,
            'gallery_media_ids' => [$media['front']->id, $media['aerial']->id, $media['rooftop']->id],
            'display_order' => 30,
        ], [
            'al' => ['subtitle' => 'HISTORIA', 'title' => 'Nga monument në qendër të hapur', 'description' => '<p>E ndërtuar në vitin 1988 si muze, ndërtesa mori funksione të ndryshme pas rënies së komunizmit. Pas viteve të pasigurisë dhe degradimit, ajo u rimendua si një qendër e aksesueshme për qytetin.</p>'],
            'en' => ['subtitle' => 'HISTORY', 'title' => 'From monument to open hub', 'description' => '<p>Built in 1988 as a museum, the building took on many functions after the fall of communism. Following years of uncertainty and decline, it was reimagined as an accessible hub for the city.</p>'],
        ]);

        $this->upsertSection($about, 'About - Timeline', [
            'type' => SectionType::Features,
            'display_order' => 40,
            'structured_data' => [
                'al' => ['items' => [['title' => 'Vitet 1980 — Simbol pushteti', 'text' => 'Piramida u projektua dhe u hap si muze në vitin 1988.'], ['title' => 'Vitet 1990 — Tranzicion dhe transformim', 'text' => 'Ndërtesa mori role të reja në një qytet që ndryshonte shpejt.'], ['title' => '2000–Sot — Rënie dhe rinovim', 'text' => 'Një transformim i plotë e riktheu Piramidën te publiku.']]],
                'en' => ['items' => [['title' => '1980s — A Symbol of Power', 'text' => 'Piramida was designed and opened as a museum in 1988.'], ['title' => '1990s — Transition and Transformation', 'text' => 'The building took on new roles in a rapidly changing city.'], ['title' => '2000s–Present — Decline & Renewal', 'text' => 'A complete transformation returned Piramida to the public.']]],
            ],
        ], [
            'al' => ['title' => 'Pikat kryesore të historisë'],
            'en' => ['title' => 'History highlights'],
        ]);

        $this->seedSimplePage($ownerId, 900, 'Kushtet e përdorimit', 'terms', 'Terms of use', 'terms-of-use', '<p>Këto kushte përshkruajnë përdorimin e faqes së Piramidës. Teksti përfundimtar ligjor do të miratohet para publikimit të faqes finale.</p>', '<p>These terms describe use of the Piramida website. Final legal copy will be approved before the finished website is published.</p>');
        $this->seedSimplePage($ownerId, 910, 'Politika e privatësisë', 'privatesia', 'Privacy policy', 'privacy-policy', '<p>Piramida përdor të dhënat e formularëve vetëm për t’iu përgjigjur kërkesave. Teksti përfundimtar ligjor do të miratohet para publikimit.</p>', '<p>Piramida uses form data only to respond to requests. Final legal copy will be approved before publication.</p>');
    }

    private function seedSimplePage(?int $ownerId, int $order, string $titleAl, string $slugAl, string $titleEn, string $slugEn, string $contentAl, string $contentEn): void
    {
        $this->upsertTranslated(Page::class, [$slugAl, $slugEn], [
            'is_homepage' => false,
            'status' => ContentStatus::Published,
            'published_at' => now()->subMonth(),
            'display_order' => $order,
            'created_by' => $ownerId,
            'updated_by' => $ownerId,
        ], [
            'al' => ['title' => $titleAl, 'slug' => $slugAl, 'short_description' => null, 'content' => $contentAl, 'seo_title' => $titleAl, 'seo_description' => null],
            'en' => ['title' => $titleEn, 'slug' => $slugEn, 'short_description' => null, 'content' => $contentEn, 'seo_title' => $titleEn, 'seo_description' => null],
        ]);
    }

    /** @param array<string, Media> $media */
    private function seedNews(?int $ownerId, array $media): void
    {
        $items = [
            [
                'slugs' => ['lajmi-demo', 'demo-news', 'sezoni-i-ri-ne-piramide', 'a-new-season-at-piramida'],
                'image' => 'aerial',
                'published_at' => now()->subDay(),
                'al' => ['title' => 'Një sezon i ri kulture dhe teknologjie në Piramidë', 'slug' => 'sezoni-i-ri-ne-piramide', 'excerpt' => 'Kalendari i ri sjell ekspozita, punëtori, filma dhe takime për komunitetin.', 'content' => '<p>Piramida hap një sezon të ri aktivitetesh që lidhin kulturën, teknologjinë dhe qytetin.</p><p>Vizitorët do të gjejnë punëtori për familje, shfaqje filmash, diskutime dhe eksperienca kreative në hapësirat e rinovuara.</p><p>Programi i plotë do të përditësohet vazhdimisht nga ekipi përmes panelit të administrimit.</p>'],
                'en' => ['title' => 'A new season of culture and technology at Piramida', 'slug' => 'a-new-season-at-piramida', 'excerpt' => 'The new calendar brings exhibitions, workshops, films and community gatherings.', 'content' => '<p>Piramida is opening a new season of activities connecting culture, technology and the city.</p><p>Visitors will find family workshops, film screenings, conversations and creative experiences throughout its renewed spaces.</p><p>The team will keep the complete programme up to date through the administration dashboard.</p>'],
            ],
            [
                'slugs' => ['te-rinjte-takohen-ne-tung-ideve', 'young-innovators-meet-at-tung-ideve'],
                'image' => 'tung_ideve',
                'published_at' => now()->subDays(2),
                'al' => ['title' => 'Idetë e të rinjve takohen në TUNG IDEVE', 'slug' => 'te-rinjte-takohen-ne-tung-ideve', 'excerpt' => 'Nxënës dhe mentorë bashkohen për tri ditë krijimtarie, teknologjie dhe bashkëpunimi.', 'content' => '<p>TUNG IDEVE rikthehet në Piramidë me një shkollë pranverore dhe konkurs për ekipe të reja.</p><p>Pjesëmarrësit zhvillojnë ide, ndërtojnë prototipe dhe i prezantojnë ato përpara mentorëve dhe komunitetit.</p>'],
                'en' => ['title' => 'Young innovators meet at TUNG IDEVE', 'slug' => 'young-innovators-meet-at-tung-ideve', 'excerpt' => 'Students and mentors come together for three days of creativity, technology and collaboration.', 'content' => '<p>TUNG IDEVE returns to Piramida with a spring school and competition for young teams.</p><p>Participants develop ideas, build prototypes and present them to mentors and the community.</p>'],
            ],
            [
                'slugs' => ['tirana-nga-nje-kend-i-ri', 'tirana-from-a-new-perspective'],
                'image' => 'rooftop',
                'published_at' => now()->subDays(3),
                'al' => ['title' => 'Tirana nga një kënd i ri', 'slug' => 'tirana-nga-nje-kend-i-ri', 'excerpt' => 'Rruga e 100 shkallëve dhe tarraca e Piramidës krijojnë një përvojë unike urbane.', 'content' => '<p>Ngjitja në Piramidë është bërë një nga mënyrat më të veçanta për të përjetuar qendrën e Tiranës.</p><p>Shkallët, platformat dhe tarraca krijojnë vende për të pushuar, për t’u takuar dhe për të parë qytetin.</p>'],
                'en' => ['title' => 'Tirana from a new perspective', 'slug' => 'tirana-from-a-new-perspective', 'excerpt' => 'The 100-step route and Piramida rooftop create a unique urban experience.', 'content' => '<p>Climbing Piramida has become one of the most distinctive ways to experience central Tirana.</p><p>The steps, platforms and rooftop create places to pause, meet and see the city.</p>'],
            ],
        ];

        foreach ($items as $index => $item) {
            /** @var News $article */
            $article = $this->upsertTranslated(News::class, $item['slugs'], [
                'featured_media_id' => $media[$item['image']]->id,
                'status' => ContentStatus::Published,
                'published_at' => $item['published_at'],
                'author_name' => 'Piramida',
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => $item['al'] + ['seo_title' => $item['al']['title'], 'seo_description' => $item['al']['excerpt']],
                'en' => $item['en'] + ['seo_title' => $item['en']['title'], 'seo_description' => $item['en']['excerpt']],
            ]);
            $article->syncGallery([
                $media[$item['image']]->id,
                $media[$index === 1 ? 'education_workshop' : 'front']->id,
            ]);
        }
    }

    /** @param array<string, Media> $media */
    private function seedEvents(?int $ownerId, array $media): void
    {
        $items = [
            [
                'slugs' => ['eventi-demo', 'demo-event', 'asterix-dhe-obelix-misioni-kleopatra', 'asterix-and-obelix-mission-cleopatra'],
                'image' => 'asterix', 'category' => EventCategory::Event, 'starts' => '2026-11-08 11:30:00', 'ends' => '2026-11-08 13:30:00', 'capacity' => 100,
                'al' => ['title' => 'Asterix dhe Obelix: Misioni Kleopatra', 'slug' => 'asterix-dhe-obelix-misioni-kleopatra', 'short_description' => 'Një shfaqje filmi për familje në Piramidë.', 'description' => '<p>Bashkohuni me Asterix dhe Obelix në një aventurë të mbushur me humor. Shfaqja është e përshtatshme për familje dhe vendet konfirmohen nga stafi pas dërgimit të kërkesës.</p>', 'location' => 'Salla e Eventeve, Kati 2', 'price_label' => 'Hyrja e lirë me rezervim'],
                'en' => ['title' => 'Asterix and Obelix: Mission Cleopatra', 'slug' => 'asterix-and-obelix-mission-cleopatra', 'short_description' => 'A family film screening at Piramida.', 'description' => '<p>Join Asterix and Obelix for an adventure filled with humour. The screening is family friendly and places are confirmed by staff after a request is submitted.</p>', 'location' => 'Event Hall, Floor 2', 'price_label' => 'Free with reservation'],
            ],
            [
                'slugs' => ['glass-experience-prind-dhe-femije', 'glass-experience-parent-and-child'],
                'image' => 'glass', 'category' => EventCategory::Exhibition, 'starts' => '2026-09-12 10:00:00', 'ends' => '2026-09-13 17:00:00', 'capacity' => 24,
                'al' => ['title' => 'Glass Experience: Punëtori Prind & Fëmijë', 'slug' => 'glass-experience-prind-dhe-femije', 'short_description' => 'Një orë krijimtarie me xham, ngjyra dhe punë në grup.', 'description' => '<p>Prindërit dhe fëmijët punojnë së bashku për të krijuar një objekt të vogël prej xhami nën udhëzimin e artistëve. Çdo seancë zgjat një orë.</p>', 'location' => 'Innovation Hub, Kati 1', 'price_label' => 'Me regjistrim'],
                'en' => ['title' => 'Glass Experience: Parent & Child Workshop', 'slug' => 'glass-experience-parent-and-child', 'short_description' => 'One creative hour with glass, colour and teamwork.', 'description' => '<p>Parents and children work together to create a small glass object with guidance from artists. Each session lasts one hour.</p>', 'location' => 'Innovation Hub, Floor 1', 'price_label' => 'Registration required'],
            ],
            [
                'slugs' => ['tung-ideve-2026', 'tung-ideve-2026-en'],
                'image' => 'tung_ideve', 'category' => EventCategory::Event, 'starts' => '2026-10-01 09:00:00', 'ends' => '2026-10-03 18:00:00', 'capacity' => 120,
                'al' => ['title' => 'TUNG IDEVE 2026 — Shkolla & Konkursi i Inovacionit', 'slug' => 'tung-ideve-2026', 'short_description' => 'Tri ditë për ide, prototipe dhe zgjidhje të reja.', 'description' => '<p>Një shkollë intensive dhe konkurs ku të rinjtë zhvillojnë ide me mentorë nga teknologjia, biznesi dhe industritë kreative.</p>', 'location' => 'TUMO, Piramida', 'price_label' => 'Aplikim falas'],
                'en' => ['title' => 'TUNG IDEVE 2026 — Innovation School & Competition', 'slug' => 'tung-ideve-2026-en', 'short_description' => 'Three days for ideas, prototypes and new solutions.', 'description' => '<p>An intensive school and competition where young people develop ideas with mentors from technology, business and the creative industries.</p>', 'location' => 'TUMO, Piramida', 'price_label' => 'Free application'],
            ],
        ];

        foreach ($items as $index => $item) {
            $this->upsertTranslated(Event::class, $item['slugs'], [
                'featured_media_id' => $media[$item['image']]->id,
                'category' => $item['category'],
                'status' => ContentStatus::Published,
                'published_at' => now()->subMonth(),
                'starts_at' => $item['starts'],
                'ends_at' => $item['ends'],
                'external_url' => null,
                'booking_mode' => BookingMode::Internal,
                'is_featured' => $index < 2,
                'capacity' => $item['capacity'],
                'display_order' => ($index + 1) * 10,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => $item['al'] + ['seo_title' => $item['al']['title'], 'seo_description' => $item['al']['short_description']],
                'en' => $item['en'] + ['seo_title' => $item['en']['title'], 'seo_description' => $item['en']['short_description']],
            ]);
        }
    }

    /** @param array<string, Media> $media */
    private function seedAttractions(?int $ownerId, array $media): void
    {
        $items = [
            [
                'slugs' => ['100-shkallet', '100-steps'], 'image' => 'stairs',
                'al' => ['title' => '100 Shkallët', 'slug' => '100-shkallet', 'short_description' => 'Ngjitje përmes shtresave të arkitekturës drejt një pamjeje të hapur të Tiranës.', 'description' => '<p>Rruga e jashtme e Piramidës formohet nga shkallë, platforma dhe vende pushimi që e kthejnë ngjitjen në një përvojë urbane.</p><p>Në majë, qyteti shfaqet në 360 gradë.</p>', 'location' => 'Çatia e Piramidës', 'visitor_information' => 'Hyrje e lirë. Kujdes gjatë motit me shi.'],
                'en' => ['title' => '100 Steps', 'slug' => '100-steps', 'short_description' => 'Ascend through layers of architecture to an open view across Tirana.', 'description' => '<p>Piramida’s exterior route is formed by steps, platforms and resting points that turn the climb into an urban experience.</p><p>At the top, the city opens in every direction.</p>', 'location' => 'Piramida rooftop', 'visitor_information' => 'Free access. Take care during wet weather.'],
            ],
            [
                'slugs' => ['tarraca-e-qytetit', 'city-rooftop'], 'image' => 'rooftop',
                'al' => ['title' => 'Tarraca e Qytetit', 'slug' => 'tarraca-e-qytetit', 'short_description' => 'Një vend për të ndalur, për t’u takuar dhe për të parë horizontin.', 'description' => '<p>Tarraca është një pikë publike takimi mbi zhurmën e bulevardit, ideale për perëndimin e diellit dhe fotografi.</p>', 'location' => 'Maja e Piramidës', 'visitor_information' => 'E hapur gjatë orarit të Piramidës.'],
                'en' => ['title' => 'The City Rooftop', 'slug' => 'city-rooftop', 'short_description' => 'A place to pause, meet and look across the skyline.', 'description' => '<p>The rooftop is a public meeting point above the boulevard, ideal for sunset views and photography.</p>', 'location' => 'Top of Piramida', 'visitor_information' => 'Open during Piramida opening hours.'],
            ],
        ];

        foreach ($items as $index => $item) {
            /** @var Attraction $attraction */
            $attraction = $this->upsertTranslated(Attraction::class, $item['slugs'], [
                'featured_media_id' => $media[$item['image']]->id,
                'status' => ContentStatus::Published,
                'published_at' => now()->subMonth(),
                'is_featured' => true,
                'display_order' => ($index + 1) * 10,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => $item['al'] + ['seo_title' => $item['al']['title'], 'seo_description' => $item['al']['short_description']],
                'en' => $item['en'] + ['seo_title' => $item['en']['title'], 'seo_description' => $item['en']['short_description']],
            ]);
            $attraction->syncGallery([$media['stairs']->id, $media['rooftop']->id]);
        }
    }

    /** @param array<string, Media> $media */
    private function seedBusinesses(?int $ownerId, array $media): void
    {
        $items = [
            ['name' => 'DV8', 'slug' => 'dv8', 'category' => BusinessCategory::Cafe, 'image' => 'meeting', 'address_al' => 'Kati 0', 'address_en' => 'Floor 0', 'hours' => '08:00–22:00', 'al' => 'Kafe e qetë për pushime të shkurtra, takime dhe biseda.', 'en' => 'A relaxed café for quick breaks, meetings and conversation.'],
            ['name' => 'Mulliri', 'slug' => 'mulliri', 'category' => BusinessCategory::Cafe, 'image' => 'creative_hub', 'address_al' => 'Kati 4', 'address_en' => 'Floor 4', 'hours' => '07:30–22:30', 'al' => 'Kafe, ushqime të lehta dhe një vend i rehatshëm brenda Piramidës.', 'en' => 'Coffee, light food and a comfortable place inside Piramida.'],
            ['name' => 'Banas', 'slug' => 'banas', 'category' => BusinessCategory::Restaurant, 'image' => 'tumo', 'address_al' => 'Kati 1', 'address_en' => 'Floor 1', 'hours' => '11:00–23:00', 'al' => 'Shije të freskëta dhe një atmosferë e hapur për drekë ose darkë.', 'en' => 'Fresh flavours and an open atmosphere for lunch or dinner.'],
            ['name' => 'Piramida Store', 'slug' => 'piramida-store', 'category' => BusinessCategory::Shop, 'image' => 'yellow_house', 'address_al' => 'Hyrja kryesore', 'address_en' => 'Main entrance', 'hours' => '09:00–20:00', 'al' => 'Objekte, botime dhe kujtime të frymëzuara nga Piramida dhe Tirana.', 'en' => 'Objects, publications and souvenirs inspired by Piramida and Tirana.'],
        ];

        foreach ($items as $index => $item) {
            /** @var Business $business */
            $business = $this->upsertTranslated(Business::class, [$item['slug']], [
                'featured_media_id' => $media[$item['image']]->id,
                'logo_media_id' => null,
                'category' => $item['category'],
                'status' => ContentStatus::Published,
                'published_at' => now()->subMonth(),
                'website_url' => null,
                'email' => null,
                'phone' => null,
                'is_featured' => $index < 3,
                'display_order' => ($index + 1) * 10,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => ['name' => $item['name'], 'slug' => $item['slug'], 'short_description' => $item['al'], 'description' => '<p>'.$item['al'].'</p><p>Hapni kartën për të parë imazhet, vendndodhjen dhe orarin.</p>', 'address' => $item['address_al'], 'opening_hours' => $item['hours'], 'seo_title' => $item['name'].' në Piramidë', 'seo_description' => $item['al']],
                'en' => ['name' => $item['name'], 'slug' => $item['slug'], 'short_description' => $item['en'], 'description' => '<p>'.$item['en'].'</p><p>Open the card to view images, location and opening hours.</p>', 'address' => $item['address_en'], 'opening_hours' => $item['hours'], 'seo_title' => $item['name'].' at Piramida', 'seo_description' => $item['en']],
            ]);
            $business->syncGallery([$media[$item['image']]->id, $media[$index % 2 === 0 ? 'creative_hub' : 'meeting']->id]);
        }
    }

    /** @param array<string, Media> $media */
    private function seedSpaces(?int $ownerId, array $media): void
    {
        $items = [
            ['slugs' => ['salla-agora', 'agora-hall'], 'type' => SpaceType::EventSpace, 'image' => 'creative_hub', 'capacity' => 40, 'area' => 280, 'floor_al' => 'Kati 2', 'floor_en' => 'Floor 2', 'title_al' => 'Salla Agora', 'title_en' => 'Agora Hall', 'short_al' => 'Hapësirë fleksibël për ekspozita, prezantime dhe evente kulturore.', 'short_en' => 'A flexible space for exhibitions, presentations and cultural events.'],
            ['slugs' => ['studio-inovacioni', 'innovation-studio'], 'type' => SpaceType::EventSpace, 'image' => 'meeting', 'capacity' => 40, 'area' => 180, 'floor_al' => 'Kati 1', 'floor_en' => 'Floor 1', 'title_al' => 'Studio e Inovacionit', 'title_en' => 'Innovation Studio', 'short_al' => 'Studio modulare për punëtori, trajnime dhe sesione praktike.', 'short_en' => 'A modular studio for workshops, training and hands-on sessions.'],
            ['slugs' => ['skena-e-tarraces', 'terrace-stage'], 'type' => SpaceType::EventSpace, 'image' => 'rooftop', 'capacity' => 120, 'area' => 340, 'floor_al' => 'Tarraca', 'floor_en' => 'Rooftop', 'title_al' => 'Skena e Tarracës', 'title_en' => 'Terrace Stage', 'short_al' => 'Një skenë e hapur me qytetin si sfond.', 'short_en' => 'An open-air stage with the city as its backdrop.'],
            ['slugs' => ['salla-e-punetorive', 'workshop-room'], 'type' => SpaceType::EventSpace, 'image' => 'education_workshop', 'capacity' => 40, 'area' => 110, 'floor_al' => 'Kati 1', 'floor_en' => 'Floor 1', 'title_al' => 'Salla e Punëtorive', 'title_en' => 'Workshop Room', 'short_al' => 'Hapësirë e pajisur për punë kreative dhe grupe të vogla.', 'short_en' => 'An equipped room for creative work and smaller groups.'],
            ['slugs' => ['mix-digital', 'mix-digital-en'], 'type' => SpaceType::Leasing, 'image' => 'office', 'capacity' => null, 'area' => 110.2, 'floor_al' => 'Kati përdhe L0', 'floor_en' => 'Ground Floor L0', 'title_al' => 'Mix Digital', 'title_en' => 'Mix Digital', 'short_al' => 'Njësi për teknologji, inovacion dhe ekipe digjitale.', 'short_en' => 'A unit for technology, innovation and digital teams.'],
            ['slugs' => ['studio-kreative-l1', 'creative-studio-l1'], 'type' => SpaceType::Leasing, 'image' => 'yellow_house', 'capacity' => null, 'area' => 128, 'floor_al' => 'Kati 1', 'floor_en' => 'Floor 1', 'title_al' => 'Studio Kreative L1', 'title_en' => 'Creative Studio L1', 'short_al' => 'Hapësirë për studio, shërbime kreative ose koncept mikpritjeje.', 'short_en' => 'A space for a studio, creative service or hospitality concept.'],
        ];

        foreach ($items as $index => $item) {
            /** @var Space $space */
            $space = $this->upsertTranslated(Space::class, $item['slugs'], [
                'featured_media_id' => $media[$item['image']]->id,
                'type' => $item['type'],
                'status' => ContentStatus::Published,
                'published_at' => now()->subMonth(),
                'capacity' => $item['capacity'],
                'area_sqm' => $item['area'],
                'price_from' => null,
                'currency' => 'EUR',
                'booking_mode' => BookingMode::Internal,
                'external_url' => null,
                'is_featured' => $index < 3,
                'display_order' => ($index + 1) * 10,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => ['title' => $item['title_al'], 'slug' => $item['slugs'][0], 'short_description' => $item['short_al'], 'description' => '<p>'.$item['short_al'].'</p><p>Dërgoni një kërkesë dhe ekipi ynë do të konfirmojë disponueshmërinë dhe hapat e ardhshëm.</p>', 'features' => '<ul><li>Plan fleksibël</li><li>Akses në zonat e përbashkëta</li><li>Mbështetje nga ekipi i Piramidës</li></ul>', 'location' => $item['floor_al'], 'price_label' => 'Çmimi sipas kërkesës', 'seo_title' => $item['title_al'], 'seo_description' => $item['short_al']],
                'en' => ['title' => $item['title_en'], 'slug' => $item['slugs'][1], 'short_description' => $item['short_en'], 'description' => '<p>'.$item['short_en'].'</p><p>Send a request and our team will confirm availability and next steps.</p>', 'features' => '<ul><li>Flexible layout</li><li>Access to shared areas</li><li>Support from the Piramida team</li></ul>', 'location' => $item['floor_en'], 'price_label' => 'Price on request', 'seo_title' => $item['title_en'], 'seo_description' => $item['short_en']],
            ]);
            $space->syncGallery([$media[$item['image']]->id, $media[$item['type'] === SpaceType::Leasing ? 'office' : 'creative_hub']->id]);
        }
    }

    private function seedCareers(?int $ownerId): void
    {
        $items = [
            ['al' => 'Menaxher/e Projekti', 'slug_al' => 'menaxher-projekti', 'en' => 'Project Manager', 'slug_en' => 'project-manager', 'department' => 'Programmes'],
            ['al' => 'Analist/e Biznesi', 'slug_al' => 'analist-biznesi', 'en' => 'Business Analyst', 'slug_en' => 'business-analyst', 'department' => 'Operations'],
            ['al' => 'Menaxher/e Marketingu', 'slug_al' => 'menaxher-marketingu', 'en' => 'Marketing Manager', 'slug_en' => 'marketing-manager', 'department' => 'Communications'],
            ['al' => 'Specialist/e për Zhvillimin e Talenteve', 'slug_al' => 'zhvillimi-i-talenteve', 'en' => 'Talent Retention and Growth Specialist', 'slug_en' => 'talent-retention-and-growth', 'department' => 'People'],
            ['al' => 'Specialist/e Finance', 'slug_al' => 'specialist-finance', 'en' => 'Financial Officer', 'slug_en' => 'financial-officer', 'department' => 'Finance'],
        ];

        foreach ($items as $index => $item) {
            $this->upsertTranslated(Career::class, [$item['slug_al'], $item['slug_en']], [
                'employment_type' => EmploymentType::FullTime,
                'status' => ContentStatus::Published,
                'published_at' => now()->subWeek(),
                'deadline' => '2026-12-31 23:59:59',
                'booking_mode' => BookingMode::Internal,
                'external_url' => null,
                'display_order' => ($index + 1) * 10,
                'created_by' => $ownerId,
                'updated_by' => $ownerId,
            ], [
                'al' => ['title' => $item['al'], 'slug' => $item['slug_al'], 'department' => $item['department'], 'location' => 'Tiranë', 'short_description' => 'Bashkohuni me ekipin që po ndërton një hapësirë të hapur për ide dhe komunitet.', 'description' => '<p>Po kërkojmë një profesionist/e bashkëpunues/e që merr përgjegjësi, komunikon qartë dhe dëshiron të ketë ndikim në jetën kulturore e teknologjike të qytetit.</p>', 'requirements' => '<ul><li>Përvojë relevante në rol</li><li>Aftësi të mira komunikimi dhe organizimi</li><li>Shqip dhe anglisht</li></ul>', 'seo_title' => $item['al'].' — Karriera në Piramidë', 'seo_description' => 'Aplikoni për rolin '.$item['al'].' në Piramidë.'],
                'en' => ['title' => $item['en'], 'slug' => $item['slug_en'], 'department' => $item['department'], 'location' => 'Tirana', 'short_description' => 'Join the team building an open space for ideas and community.', 'description' => '<p>We are looking for a collaborative professional who takes ownership, communicates clearly and wants to contribute to the city’s cultural and technological life.</p>', 'requirements' => '<ul><li>Relevant experience for the role</li><li>Strong communication and organisational skills</li><li>Albanian and English</li></ul>', 'seo_title' => $item['en'].' — Careers at Piramida', 'seo_description' => 'Apply for the '.$item['en'].' role at Piramida.'],
            ]);
        }
    }

    private function seedSiteSettings(): void
    {
        $settings = SiteSetting::current();
        $settings->update([
            'notification_email' => 'info@piramida.edu.al',
            'email' => 'info@piramida.edu.al',
            'phone' => '+355 69 000 0000',
            'facebook_url' => null,
            'x_url' => null,
            'instagram_url' => null,
            'linkedin_url' => null,
            'map_url' => null,
        ]);
        $settings->syncTranslations([
            'al' => ['address' => 'Bulevardi Dëshmorët e Kombit 5, Tiranë, Shqipëri', 'opening_hours' => "E hënë – E diel\n08:00–22:00", 'footer_text' => 'Rimendojmë hapësirën. Frymëzojmë krijimtarinë.'],
            'en' => ['address' => 'Bulevardi Dëshmorët e Kombit 5, Tirana, Albania', 'opening_hours' => "Monday – Sunday\n08:00–22:00", 'footer_text' => 'Reimagining space. Inspiring creativity.'],
        ]);
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<int, string>  $slugs
     * @param  array<string, mixed>  $attributes
     * @param  array<string, array<string, mixed>>  $translations
     * @return TModel
     */
    private function upsertTranslated(string $modelClass, array $slugs, array $attributes, array $translations): Model
    {
        $model = $modelClass::query()
            ->whereHas('translations', fn ($query) => $query->whereIn('slug', $slugs))
            ->first();

        if (! $model) {
            $model = new $modelClass;
        }

        $model->fill($attributes)->save();
        $model->syncTranslations($translations);

        return $model;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, array<string, mixed>>  $translations
     * @param  array<int, string>  $legacyNames
     */
    private function upsertSection(Page $page, string $internalName, array $attributes, array $translations, array $legacyNames = []): PageSection
    {
        $section = PageSection::query()
            ->where('page_id', $page->id)
            ->whereIn('internal_name', [$internalName, ...$legacyNames])
            ->first() ?? new PageSection;

        $section->fill([
            'page_id' => $page->id,
            'internal_name' => $internalName,
            'type' => $attributes['type'],
            'primary_media_id' => $attributes['primary_media_id'] ?? null,
            'secondary_media_id' => $attributes['secondary_media_id'] ?? null,
            'video_url' => $attributes['video_url'] ?? null,
            'primary_button_url' => $attributes['primary_button_url'] ?? null,
            'secondary_button_url' => $attributes['secondary_button_url'] ?? null,
            'display_order' => $attributes['display_order'],
            'is_active' => true,
            'structured_data' => $attributes['structured_data'] ?? null,
            'created_by' => $section->created_by ?: $attributes['created_by'] ?? $page->created_by,
            'updated_by' => $attributes['updated_by'] ?? $page->updated_by,
        ])->save();
        $section->syncTranslations($translations);
        $section->syncGallery($attributes['gallery_media_ids'] ?? []);

        return $section;
    }
}

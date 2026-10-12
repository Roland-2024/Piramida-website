# Manual përdorimi - Piramida CMS

Roli: Editor | Versioni 1.0 | 12 tetor 2026

Ky manual shpjegon dashboard-in ekzistues të Piramida CMS. Udhëzimet janë në shqip; emrat e butonave dhe fushave jepen edhe siç shfaqen në ndërfaqen anglisht. Manuali përshkruan funksionet dhe lejet e kodit aktual, jo një CMS tjetër apo dashboard-in e WordPress-it. Ndërfaqja mund të ndryshojë në versione të ardhshme.

## 01. Fillimi i shpejtë

### Çfarë merr nga administratori

- Adresën e dashboard-it të mjedisit ku do të punosh. Pas publikimit adresa e synuar është https://piramida.edu.al/admin/login; gjatë përgatitjes përdor vetëm adresën që të jep administratori.
- Një llogari personale me rolin Editor dhe emailin tënd. Regjistrimi publik nuk ofrohet.
- Udhëzimet për lidhjen e mbrojtur gjatë preview-t, nëse HTTPS ende nuk është aktiv. Mos e konsidero skedarin hosts si mbrojtje të trafikut.
- Përmbajtjen e miratuar, përkthimet AL/EN dhe imazhet që ke të drejtë të përdorësh.

### Rrjedha e punës në shtatë hapa

1. Hyr me emailin dhe fjalëkalimin personal.
2. Zgjidh modulin e saktë në menunë majtas.
3. Gjej një rekord ekzistues me Search/Filter ose përdor Create për një të ri.
4. Plotëso shqip dhe anglisht. Fillimisht puno me Draft.
5. Vendos foton kryesore dhe galerinë, kur moduli i ka këto fusha.
6. Ruaj formularin. Për publikim cakto Published dhe Publication date.
7. Kontrollo rezultatin në web, në AL dhe EN. Në fund përdor Sign out.

### Rregulli i ruajtjes

Shkrimi në një fushë nuk është ruajtje. Use selected image/images vetëm përditëson përzgjedhjen brenda formularit; duhet edhe Save/Create për rekordin. Ndryshimet nuk ruhen automatikisht. Mos mbyll faqen pa ruajtur ose pa vendosur me vetëdije të humbasësh ndryshimet.

## 02. Lejet e rolit Editor

Editor-i menaxhon përmbajtjen, jo konfigurimin teknik të serverit. Mund të krijojë dhe ndryshojë rekordet e lejuara, të publikojë ose të kthejë në Draft dhe të menaxhojë Media. Kufizimet zbatohen nga serveri, jo vetëm nga fshehja e butonave.

| Veprimi | Leja e Editor-it |
| --- | --- |
| Pages, Page sections, Carousel posts | Krijim, lexim dhe ndryshim |
| News, Events, Careers | Krijim, lexim dhe ndryshim |
| Attractions, Businesses, Event spaces, Leasing spaces | Krijim, lexim dhe ndryshim |
| Media | Ngarkim dhe ndryshim i tekstit alternativ |
| Website content | Ndryshim i teksteve, imazheve dhe kontaktit publik |
| Trash për News | Lejohet; rekordi largohet nga publikimi |
| Trash për Pages/Page sections dhe module të tjera | Nuk lejohet |
| Restore ose Delete permanently | Vetëm Admin |
| Submissions | Vetëm lexim i listës dhe detajeve të lejuara |
| Statuset e kërkesave, private notes, dokumentet, Export CSV | Vetëm Admin |
| Users, role, Site settings, Postmark/SMTP | Vetëm Admin |

### Kujdes me strukturën e webit

Edhe pse nuk mund të hedhësh një Page ose Page section në Trash, mund të ndikosh dukshmërinë duke zgjedhur Draft ose duke hequr Active section. Mos çaktivizo faqet dhe blloqet kryesore pa miratim. Fushat e kyçura janë qëllimisht të mbrojtura dhe nuk duhet anashkaluar ky kufizim.

### Kur të kërkosh ndihmë

Kontakto Admin-in për rikthim nga Trash, heqje të rekordeve jashtë News, çaktivizim llogarie, ndryshim roli, shënime private, dokumente aplikimi, email-e që nuk dërgohen ose ndërhyrje në strukturën e template-it. Mos kërko apo përdor kredencialet deploy/root për punë editoriale.

## 03. Hyrja dhe orientimi në dashboard

### Login dhe rikuperimi i fjalëkalimit

1. Hape adresën që të ka dhënë administratori.
2. Shkruaj emailin dhe fjalëkalimin. Nëse hyrja refuzohet, kontrollo tastierën, Caps Lock dhe emailin e llogarisë.
3. Për fjalëkalim të harruar përdor lidhjen e resetimit në login. Vendos emailin dhe kontrollo Inbox/Spam.
4. Resetimi me email funksionon vetëm kur administratori ka konfiguruar dërgimin e email-eve. Nëse nuk vjen emaili, njofto Admin-in.
5. Llogaritë e çaktivizuara nuk hyjnë; këtë e zgjidh administratori.

### Elementet e ekranit

| Elementi | Si përdoret |
| --- | --- |
| Dashboard | Përmbledhje dhe lidhje te përmbajtja e ndryshuar së fundi |
| Create article | Shkurtore për krijimin e një lajmi |
| Open media library | Shkurtore te Media |
| View website | Hap webin publik në një tab tjetër |
| Recently updated content | Kliko titullin për të hapur editimin e rekordit |
| Numëruesit | Janë statistika; nuk japin të drejta shtesë mbi Users ose module të tjera |
| Sign out | Mbyll sesionin; ruaj ndryshimet përpara daljes |

### Menutë majtas

- Website: Pages, Page sections, Programs / Carousel posts, Website content.
- Activities & careers: News, Events, Careers.
- Places & spaces: Attractions, Businesses, Event spaces, Leasing spaces.
- Media: bibliotekë e përbashkët e skedarëve publikë.
- Submissions: kërkesa nga formularët, vetëm për lexim për Editor-in.
- Administration: vetëm Admin; mos e kërko te llogaria Editor.

Kliko emrin e grupit për të hapur ose mbyllur nënmenutë. Grupi i faqes aktive mund të jetë i hapur tashmë. Në mobile, butoni i navigimit hap menunë; Close navigation ose Escape e mbyllin.

## 04. Listat, filtrat dhe butonat

### Gjetja e një rekordi

1. Shkruaj disa fjalë të titullit ose emrit në Search. Te Media kërkohet emri i skedarit; te Submissions emri, emaili ose subjekti.
2. Zgjidh Status ose një filtër tjetër, kur shfaqet.
3. Kliko Filter. Shkrimi në kërkim pa dërguar formularin nuk është gjithmonë i mjaftueshëm.
4. Nëse lista është bosh, pastro kërkimin dhe zgjidh All statuses/Current. Listë bosh nuk do të thotë domosdoshmërisht që përmbajtja është fshirë.
5. Përdor lidhjet e faqeve poshtë listës për rezultate të tjera.

### Kuptimi i komandave

| Butoni/fusha | Kuptimi |
| --- | --- |
| Create page/article/event/... | Hap formularin e një rekordi të ri |
| Edit | Ndryshon rekordin ekzistues |
| View | Hap detajet në dashboard; nuk është domosdoshmërisht preview publik |
| Save... / Create... | Ruajtja përfundimtare e formularit |
| Cancel | Kthehet te lista; ndryshimet e paruajtura nuk aplikohen |
| Filter | Aplikon kërkimin dhe filtrat |
| Current | Rekordet që nuk janë në Trash |
| With trash / Trash only | Përfshin ose shfaq rekordet në Trash; Editor-i nuk fiton të drejtë rikthimi |
| Trash | Vetëm për News te Editor-i, pas konfirmimit |

Page sections filtrohen sipas faqes prind, tipit dhe Active/Inactive. Carousel posts kanë edhe filtrin Carousel page. Events në dashboard kanë All dates/Upcoming/Past; ky filtër është i dashboard-it, jo i arkivit publik.

### Mesazhet pas ruajtjes

Mesazhi i suksesit konfirmon ruajtjen. Një paralajmërim kërkon lexim, veçanërisht te WordPress. Nëse shfaqet Please correct the highlighted fields, kontrollo fushat me gabime, përfshirë panelin EN më poshtë në faqe. Mos e trajto një formular me gabime si të ruajtur.

## 05. Fushat e përbashkëta dhe publikimi

| Fusha | Çfarë vendos |
| --- | --- |
| Status: Draft | Punë e papublikuar, për përgatitje ose tërheqje nga webi |
| Status: Published | Përmbajtje e destinuar për publikim, me datë publikimi të vlefshme |
| Publication date | Datë/orë publikimi; kërkohet kur zgjedh Published |
| Display order | Numër jo negativ. Numrat më të vegjël renditen më herët në katalogët që përdorin rendin editorial |
| Featured | Shënim për përmbajtje të veçuar; nuk zëvendëson Status dhe nuk garanton shfaqje në çdo seksion |
| Title / Name | Titulli ose emri i lexueshëm për vizitorin, në secilën gjuhë |
| Slug | Pjesa e URL-së, p.sh. punetori-dizajni; shkruaje qartë me shkronja latine dhe viza |
| Short description / Excerpt | Përmbledhje e shkurtër; nuk zëvendëson përmbajtjen e plotë |
| Description / Main content | Përmbajtja e zgjeruar me formatimin e lejuar |
| SEO title / SEO description | Përshkrim i faqes për kërkim dhe metadata, në AL dhe EN |

### Kur një rekord bëhet publik

Published me datë në të ardhmen mbetet i fshehur deri në atë kohë. Draft nuk shfaqet publikisht. Duhet edhe përkthimi i gjuhës së kërkuar dhe çdo kusht i modulit: Active për seksionet, disponueshmëri/njësi për leasing ose afat i vlefshëm për karrierat. Një rekord mund të jetë në dashboard pa qenë i arritshëm në web.

Ora e projektit është Europe/Tirane. Në formularë kontrollo datën dhe orën; pamja mm/dd/yyyy ose dd/mm/yyyy mund të ndryshojë sipas browser-it. Publication date është koha e publikimit të rekordit, jo ora kur fillon një event.

### Rendi i shfaqjes

Për carousel posts dhe katalogë përdor 10, 20, 30 për të lënë hapësirë për shtesa të mëvonshme. Arkivi publik i Events renditet sipas datës së fillimit, nga më e fundit, jo sipas Display order. Nuk ka nevojë të krijosh kopje të një posti për ta parë në carousel.

## 06. Përmbajtja shqip/anglisht dhe teksti i formatuar

### Plotësimi i AL dhe EN

Hap panelet Shqip AL dhe English EN. Formularët e rekordeve kërkojnë titull/emër dhe slug të vlefshëm në të dyja gjuhët. Te Page sections, titujt mund të jenë bosh, por duhet të jenë të pranishme të dyja strukturat gjuhësore të formularit.

1. Plotëso tekstin shqip të miratuar.
2. Plotëso përkthimin anglisht të plotë; mos lër tekst demo ose përkthim të papërfunduar.
3. Shkruaj dhe kontrollo slug-un e secilës gjuhë. Mos u mbështet vetëm te sugjerimi Generated when blank; nëse formulari kërkon slug, plotësoje vetë.
4. Kontrollo AL dhe EN në web pas ruajtjes. Faqet shqip nuk kanë prefiks gjuhësor; anglishtja përdor /en.

Slug-u duhet të jetë unik për gjuhën dhe modulin përkatës. Ndryshimi i tij pas publikimit ndryshon adresën; për një URL ekzistuese kërko miratim dhe koordinim për ridrejtimin. Disa slugs të template-it janë të kyçura, sidomos EN te faqet prezantuese dhe About.

### Shiriti i formatimit

| Kontrolli | Përdorimi |
| --- | --- |
| Paragraph | Tekst normal për paragrafët |
| Heading | Nëntitull brenda përmbajtjes; titulli kryesor plotësohet në fushën Title |
| Bold | Theksim i një pjese të përzgjedhur |
| Italic | Theksim i butë i tekstit |
| Bullets | Listë me pika |
| Numbered list | Listë me hapa të numëruar |
| Link | Përzgjidh tekstin, kliko Link dhe vendos URL-në e plotë |

Mos krijo hapësira duke shtypur shumë Enter. Përdor paragrafë të shkurtër dhe një ndarje të qëndrueshme. Për tekst nga Word ose burime të tjera, ngjit tekst të thjeshtë me Ctrl+Shift+V kur browser-i e mbështet dhe formatoje në editor. Kodi HTML, script-et dhe stilet arbitrare pastrohen nga sistemi; nuk janë mënyrë për të ndryshuar dizajnin.

## 07. Media: ngarkimi dhe mirëmbajtja

Menuja Media është biblioteka e përbashkët. Një skedar mund të përdoret nga shumë rekorde. Biblioteka është për materiale publike; CV-të dhe dokumentet konfidenciale të aplikimeve nuk duhen ngarkuar këtu.

### Ngarkimi i një skedari

1. Hap Media dhe kliko Upload media.
2. Te File zgjidh skedarin nga kompjuteri.
3. Plotëso Alternative text · Shqip dhe Alternative text · English për imazhet.
4. Kliko Upload file dhe kontrollo mesazhin e suksesit.
5. Kthehu te rekordi ku do ta përdorësh dhe përzgjidhe nga biblioteka.

| Formati | Kufiri dhe përdorimi |
| --- | --- |
| JPG/JPEG, PNG, WebP, GIF | Deri në 10 MB për skedar; imazhe publike |
| PDF | Deri në 10 MB; vetëm dokument që mund të jetë publik |
| MP4, WebM | Deri në 10 MB; për video më të mëdha përdor YouTube |
| SVG i ri | Nuk ofrohet si format upload-i në formularin aktual; kërko Admin-in për asete vektoriale |

Imazhet e përshtatshme mund të marrin versione më të vogla automatike. Origjinali ruhet. Nuk ke nevojë të krijosh vetë variante 480/1600 në Media.

### Metadata dhe zëvendësimi

Edit te Media hap preview-n dhe të dhënat: Type, Size dhe Dimensions për imazhet. Fushat që ndryshon janë tekstet alternative AL/EN; ruaji me Save metadata. Nuk ka zëvendësim të skedarit origjinal në këtë formular. Për një foto tjetër, ngarkoje si skedar të ri dhe zëvendëso përzgjedhjen në rekord.

Teksti alternativ duhet të përshkruajë përmbajtjen reale, p.sh. Vizitorë në tarracën e Piramidës. Emri i skedarit ose fjalë të përgjithshme si foto1 nuk mjaftojnë. Ndryshimi i alt text te një media e përbashkët mund të ndikojë vende të tjera ku ajo përdoret.

Editor-i nuk mund të hedhë Media në Trash, të rikthejë ose të fshijë përgjithmonë skedarë. Heqja e një fotoje nga një rekord nuk e fshin nga biblioteka.

## 08. Featured image, logo dhe galeritë

### Vendosja e fotos kryesore

1. Në formular gjej Featured image, Primary image, Secondary image ose Logo.
2. Kliko Set / change image.
3. Në dialogun Media library kërko te Search images ose përdor Load more images.
4. Për një skedar të ri përdor Upload image. Në këtë dialog lejohen vetëm JPG, PNG, WebP, GIF, deri në 10 MB secila.
5. Zgjidh një foto dhe kliko Use selected image.
6. Kontrollo preview-n në formular dhe ruaj rekordin me Save/Create.

Përdor Edit image details për alt text në Media. Kjo hap faqen e metadata-s; mos harro të ruash edhe rekordin fillestar nëse ke ndryshuar përzgjedhjen. Remove image shkëput foton nga fusha, jo nga biblioteka.

### Galeria e rekordit

Galeria në formular shfaq vetëm fotot e lidhura me atë rekord. Add images hap bibliotekën e përbashkët, ku mund të zgjedhësh materiale nga Media; kjo nuk do të thotë se të gjitha fotot e bibliotekës janë pjesë e galerisë.

1. Kliko Add images.
2. Zgjidh disa imazhe ose ngarko të reja.
3. Kliko Use selected images.
4. Përdor shigjetat e miniaturave për rendin. Fotot që vijnë më herët në listë kanë rend më të hershëm në galeri.
5. Përdor Remove për të shkëputur një imazh.
6. Ruaj rekordin. Kufiri aktual i galerisë është 30 imazhe.

Events dhe Carousel posts kanë një foto kryesore dhe nuk kanë galeri. Careers nuk ka fushë media në formularin aktual. Për logo zgjidh një imazh me përmasa të përshtatshme; PNG/WebP me transparencë janë të dobishme kur dizajni e kërkon.

## 09. Pages: faqet dhe homepage

Hap Website > Pages. Ky modul menaxhon faqet, titujt, tekstin bazë dhe SEO. Për shtim të një faqeje përdor Create page; për një ekzistuese përdor Edit. View hap detajet në dashboard.

| Fusha | Udhëzimi për Editor-in |
| --- | --- |
| Status / Publication date | Përdor Draft gjatë punës; Published kërkon datë publikimi |
| Display order | Rendi editorial; për faqe gjenerike nën 100 përdoret për header, 100 e lart për footer |
| Featured image | Zgjidh foton e faqes, kur përdoret nga template-i |
| Homepage | Shënon faqen si faqja hyrëse. Mos e ndrysho pa koordinuar me Admin-in |
| Homepage YouTube video | URL HTTPS e videos YouTube për butonin Play të homepage, e përbashkët AL/EN |
| Title / Slug | Titulli dhe URL-ja e secilës gjuhë |
| Short description / Main content | Përmbledhja dhe teksti i plotë |
| SEO title / SEO description | Metadata për secilën gjuhë |

### Homepage dhe faqet prezantuese

Te faqja Homepage ndrysho videon në Homepage YouTube video, ruaj dhe testo Play në web. Nëse fusha është bosh, template-i përdor alternativën ekzistuese të seksionit/video-s ose lidhjen About. Përmbajtja që shfaqet në një template të posaçëm mund të vijë nga Page sections ose Website content, jo nga çdo fushë e Pages.

Pages menaxhon titullin/SEO të Edukim, Inovacion, Art & Kulturë dhe Biznes. Fotot/postet e carousel-it menaxhohen te Programs / Carousel posts për tre të parat, ndërsa Biznes përdor Businesses. Njoftimi në faqen e editimit të drejton te moduli i saktë.

### Mbrojtja e URL-ve dhe strukturës

Slug-et e sistemit, si admin, news, events, contact, leasing dhe event-space, nuk mund të përdoren për faqe të reja. Disa identifikues EN të template-it janë read-only. Mos ndrysho slug-un AL të një faqeje të publikuar pa koordinim; mund të thyhen lidhje ekzistuese.

Editor-i nuk ka Trash për Pages. Megjithatë Draft e fsheh faqen. Për të hequr një faqe nga struktura ose për të krijuar një template të ri, kërko Admin-in. Një Page e re gjenerike nuk krijon automatikisht një dizajn të ri të posaçëm.

## 10. Page sections: blloqet e faqeve

Hap Website > Page sections. Filtro sipas Parent page, Section type dhe Active/Inactive. Përdor Create section për shtim dhe Edit për ndryshim. Seksionet e vjetra të carousel-it nuk menaxhohen këtu; përdor modulin dinamik.

| Fusha | Udhëzimi |
| --- | --- |
| Parent page | Faqja ku lidhet seksioni; disa seksione të template-it nuk lejojnë ndryshim prindi |
| Internal name | Emër administrativ i qëndrueshëm; te blloqet e template-it mund të jetë i kyçur |
| Section type | Hero, Text and image, Features, Statistics, Gallery, Call to action, Partners ose Custom content |
| Display order | Rendi i seksionit brenda faqeve që përdorin renditje të përgjithshme |
| Active section | E aktivizon seksionin; heqja e shenjës e fsheh nga publikimi |
| Gallery / partner logos | Imazhet e lidhura me këtë bllok |
| Primary / Secondary image | Imazhi kryesor dhe dytësor |
| Video URL | Video YouTube ose URL e një MP4/WebM të ngarkuar, sipas bllokut |
| Primary / Secondary button URL | Destinacioni i butonave; kontrollo që lidhja të jetë e saktë |
| Optional structured data (JSON) | Strukturë opsionale e bllokut. Nuk është fusha e SEO-së së faqes dhe nuk është vend për script-e |

Në AL dhe EN plotëso Title, Subtitle, Description / body, Primary button label dhe Secondary button label. Një buton kërkon tekst të qartë dhe destinacion të vlefshëm. Ruaj me Create section ose Save section.

### About dhe videoja History

About përdor seksione të identifikuara si Overview, Mission, History dhe Timeline. Internal name dhe faqja prind mund të jenë të kyçura; ndrysho titujt e përkthyer, jo identifikuesit. Për History vendos Video URL dhe përdor Primary image si poster. Ngarko MP4/WebM deri në 10 MB te Media, ruaj dhe rihap formularin e seksionit për ta parë videon në sugjerime. Për video më të mëdha përdor YouTube.

### JSON dhe kufijtë e template-it

Lëre JSON bosh kur nuk kërkohet. Në një bllok që e përdor, ndrysho vetëm tekst/vlera të identifikuara sipas udhëzimit të Admin-it; ruaj çelësat, presjet dhe thonjëzat. Validimi kërkon JSON të vlefshëm. Lloji i bllokut nuk garanton që çdo template i posaçëm do të shfaqë automatikisht një seksion të ri; kjo verifikohet në web.

Editor-i nuk ka Trash për seksionet, por ka kontroll mbi Active. Mos çaktivizo blloqet kryesore gjatë një editimi të zakonshëm.

## 11. Programs / Carousel posts

Hap Website > Programs / Carousel posts. Ky është moduli i posteve dinamike për Edukim, Inovacion dhe Art & Kulturë. Nuk ka numër fiks postesh. Për carousel-in Biznes përdor Businesses.

### Shtimi i një slide/posti

1. Kliko Create carousel post.
2. Zgjidh Carousel page: Education, Innovation ose Art & Culture.
3. Vendos Status, Publication date dhe Display order.
4. Zgjidh Featured image.
5. Plotëso Title, Slug dhe Carousel caption në AL dhe EN.
6. Ruaj me Create carousel post. Për ndryshime përdor Save carousel post.
7. Hape faqen publike përkatëse dhe kontrollo imazhin, titullin, caption-in dhe rendin.

| Fusha | Përdorimi |
| --- | --- |
| Carousel page | Përcakton në cilën nga tre faqet shfaqet posti |
| Display order | Numër më i vogël = rend më i hershëm |
| Featured image | Një foto për kartën |
| Title AL/EN | Titulli i përmbajtjes së kartës |
| Slug AL/EN | Identifikues i vlefshëm dhe unik i rekordit |
| Carousel caption | Përshkrim i shkurtër, tekst i thjeshtë; shmang HTML dhe përmbajtje shumë të gjatë |

Lista ka Search translated name, Carousel page, Status dhe filtrin e Trash. Kopjet që mund të shohësh gjatë lëvizjes së carousel-it janë pjesë e loop-it vizual; mos krijo rekorde të dyfishta për këtë arsye.

Titulli i madh i faqes dhe SEO-ja menaxhohen te Pages. Teksti i kartës dhe fotoja menaxhohen këtu. Kategoritë e Businesses nuk e furnizojnë këtë modul.

## 12. Website content: tekste, imazhe dhe kontakt

Hap Website > Website content. Ky modul menaxhon elementet e përbashkëta të webit. Artikujt, eventet, bizneset dhe faqet individuale vazhdojnë të ndryshohen në modulet e tyre. Kliko një nga grupet në krye dhe ruaj me Save content në fund.

| Grupi | Çfarë menaxhon |
| --- | --- |
| Shared labels, navigation and forms | Etiketa, menu, butona, fusha dhe mesazhe të përbashkëta publike |
| Page and template texts | Tekste të template-it që nuk janë fusha të një rekordi individual |
| Default SEO and social text | Tekste rezervë të SEO-së dhe ndarjes sociale |
| Validation messages | Tekste të gabimeve të formularëve publikë |
| Pagination | Etiketa të navigimit mes faqeve |
| Images and logos | Zëvendësim i imazheve të template-it me Media publike |
| Public contact and footer | Email/telefon publik, rrjete sociale, hartë, adresë, orare dhe footer |

### Ndryshimi i teksteve

1. Zgjidh grupin dhe gjej elementin nga etiketa.
2. Plotëso fushat AL dhe EN.
3. Ruaj me Save content.
4. Kontrollo vendin ku përdoret në web, në të dyja gjuhët.

Vlerat janë tekst i thjeshtë. Lënia bosh mund të fshehë etiketën; përdore me kujdes. Ruaj pa ndryshim placeholders si :attribute, :max, :year dhe :value. Sistemi i zëvendëson me vlerat reale; heqja e tyre mund ta bëjë mesazhin të pasaktë. Ndryshimi i një etikete të përbashkët mund të ndikojë disa faqe njëkohësisht.

### Images and logos

Ngarko fillimisht imazhin te Media. Te karta e asetit zgjidh skedarin nga lista dhe ruaj. Original template image heq zëvendësimin dhe rikthen asetin e template-it; nuk fshin Media-n. Kontrollo përmasat dhe transparencën. Për planet e leasing mos ndrysho raportin e imazhit ose pozicionet e njësive: overlay-t e klikueshme nuk përshtaten automatikisht.

### Public contact and footer

Ndrysho Public email, Phone, Facebook URL, X URL, Instagram URL, LinkedIn URL dhe Map embed URL. Në AL/EN plotëso Address, Opening hours dhe Footer text. Vendos URL-në e embed-it për hartën kur kërkohet, jo një kod iframe të plotë.

Email-i publik i kontaktit është i ndryshëm nga adresa teknike e njoftimeve të formularëve. SMTP/Postmark, marrësit teknikë dhe Users mbeten Admin-only te Site settings; nuk konfigurohen nga Editor-i këtu.

## 13. News: krijimi, ndryshimi dhe Trash

Hap Activities & careers > News. Përdor Search/Status/Trash për të gjetur artikullin. Create article hap formularin e ri; Edit ndryshon ekzistuesin. View hap detajet në dashboard.

| Fusha | Përdorimi |
| --- | --- |
| Status / Publication date | Draft gjatë punës; datë/orë e vlefshme për publikim |
| Author name | Emri publik i autorit ose organizatës së miratuar |
| Featured image | Fotoja kryesore e artikullit |
| Article gallery | Fotot shtesë të këtij artikulli, me rend editorial |
| Title / Slug AL/EN | Titulli dhe adresa në secilën gjuhë |
| Excerpt | Përmbledhje e shkurtër; shmang përsëritjen identike të paragrafit të parë |
| Article content | Teksti i plotë, me paragrafë dhe nëntituj |
| SEO title / description | Metadata e artikullit në të dyja gjuhët |

### Rrjedha e rekomanduar

1. Krijo artikullin si Draft.
2. Plotëso AL/EN dhe autorin.
3. Vendos foto kryesore dhe galeri vetëm nëse janë të nevojshme.
4. Ruaj dhe rihape për një kontroll editorial.
5. Cakto Published dhe Publication date; ruaj.
6. Kontrollo /news dhe faqen e artikullit në AL/EN.

### Trash për Editor-in

Vetëm News mund të hidhet në Trash nga Editor-i. Përdor butonin Trash në listë ose te detajet dhe lexo konfirmimin. Artikulli nuk është më publik, por ky veprim nuk është fshirje e përhershme.

Nëse e bëre gabimisht, gjeje me Trash only dhe kontakto Admin-in për Restore. Editor-i nuk ka Restore ose Delete permanently. Për të tërhequr përkohësisht një lajm, Draft është shpesh më i përshtatshëm se Trash.

## 14. Events: manuale dhe të importuara

Hap Activities & careers > Events. Lista tregon numrin e eventeve WordPress dhe kohën e sinkronizimit të fundit, kur është e disponueshme. Filtro sipas titullit, statusit dhe All dates/Upcoming/Past. View hap detajet; Edit hap formularin.

### Dallimi kryesor i burimit

Një event i krijuar me Create event është manual dhe nuk mbishkruhet nga importi WordPress. Një event i importuar ka paralajmërim në editim: ndryshimet lokale mund të zëvendësohen në sinkronizimin pasues. Për ndryshime të qëndrueshme te një event i importuar, ndrysho burimin WordPress ose njofto personin përgjegjës.

Mos e krijo të njëjtin event si manual për të korrigjuar një të importuar pa miratim; mund të shfaqen dy rekorde publike.

| Fusha | Përdorimi |
| --- | --- |
| Category | Event, Exhibition ose Guided tour |
| Status / Publication date | Publikimi i rekordit, jo data e zhvillimit |
| Starts at / Ends at | Fillimi dhe mbarimi; mbarimi duhet të jetë i barabartë ose më vonë se fillimi |
| Display order | Rendi editorial ku përdoret; arkivi publik ka rend kronologjik |
| Featured image | Një foto kryesore; Events nuk ka galeri |
| Registration action | No booking, Internal request form, External link ose Internal form and external link |
| Registration / external URL | URL HTTP/HTTPS; kërkohet për External ose Both |
| Capacity | Numër pozitiv pjesëmarrësish; nuk është sistem automatik rezervimi |
| Featured | Shënim për veçim sipas template-it |

### Përmbajtja AL/EN e eventit

Plotëso Title, Slug, Location, Price label, Short description, Full description, SEO title dhe SEO description. Location është emri i lexueshëm i vendit. Price label është tekst publik, p.sh. Hyrje e lirë, vetëm nëse ky informacion është i konfirmuar.

Venue street address, Venue city, Postal code dhe Country code janë opsionale. Vendos vetëm adresë reale të konfirmuar. Country code përdor dy shkronja të mëdha, p.sh. AL. Rruga, qyteti dhe shteti mundësojnë adresën e strukturuar të eventit; këto fusha lokale ruhen nga sinkronizimi WordPress.

### Formulari dhe eventet e kaluara

Internal request form krijon kërkesë për stafin, jo konfirmim automatik pjesëmarrjeje. External link dërgon vizitorin te URL-ja e caktuar. Both ofron të dy veprimet kur template-i i përdor. No booking nuk ofron regjistrim.

Arkivi publik përfshin evente të ardhshme dhe të kaluara. Eventet që kanë përfunduar marrin overlay dhe etiketë përfundimi sipas Ends at. Mos i fshi ose mos u ndrysho datat vetëm sepse kanë përfunduar; historia është pjesë e arkivit.

### Sync WordPress

Butoni Sync WordPress është i përdorshëm nga Editor-i për sinkronizim manual. Përdore vetëm kur duhet përditësim nga burimi dhe shmang klikimet e përsëritura. Sistemi ka mbrojtje ndaj ekzekutimeve njëkohësisht.

Created, updated, drafted dhe skipped janë numërues të rezultatit. Drafted mund të tregojë evente të hequra nga përgjigjja e plotë e burimit. Në rast failed ose skipped, lexo mesazhin dhe njofto Admin-in; mos ndrysho çelësin API në dashboard. Orari normal është 06:00 dhe 18:00 Europe/Tirane, kur scheduler-i i serverit është aktiv. Konfigurimi teknik bëhet nga administratori.

## 15. Businesses: bizneset dhe kategoritë

Hap Places & spaces > Businesses. Përdor Create business ose Edit. Një biznes mund të ketë disa kategori të paracaktuara; Editor-i nuk krijon kategori të reja.

| Fusha | Përdorimi |
| --- | --- |
| Categories | Education, Innovation, Business, Art & Culture, Social Spaces; zgjidh një ose më shumë që përputhen me biznesin |
| Logo | Logoja e biznesit, e veçantë nga fotoja kryesore |
| Website URL | Faqja zyrtare e biznesit |
| Public email / Public phone | Kontakte që mund të publikohen |
| Featured | Veçim sipas template-it |
| Featured image / Gallery images | Fotoja kryesore dhe galeria e biznesit |
| Name / Slug AL/EN | Emri dhe adresa për secilën gjuhë |
| Short description / Description | Përmbledhja dhe përmbajtja e plotë |
| Address / Opening hours | Adresa dhe oraret reale të miratuara |
| SEO title / description | Metadata AL/EN |

### Ku shfaqet biznesi

- /businesses shfaq bizneset me kategorinë Social Spaces, kur janë të publikuara dhe me përkthimin përkatës.
- /biznes shfaq të gjitha bizneset e publikuara dhe të përkthyera, në carousel-in prezantues.
- Zgjedhja e Education/Innovation/Art & Culture te një biznes nuk e shton automatikisht te carousel posts e Programs.

### Shembull pune

Për një kafene zgjidh kategoritë reale të miratuara, duke përfshirë Social Spaces kur duhet të jetë në katalogun /businesses. Plotëso logon, foton, kontaktet dhe AL/EN. Ruaj si Draft, kontrollo përmbajtjen, pastaj publiko me datë. Nëse del në /biznes por jo në /businesses, kontrollo fillimisht kategorinë Social Spaces.

## 16. Attractions: pikat e interesit

Hap Places & spaces > Attractions. Ky modul është për pikat e interesit dhe informacionin për vizitorët, jo për bizneset ose njësitë me qira.

### Krijimi ose përditësimi

1. Kliko Create attraction ose Edit.
2. Vendos Status, Publication date, Display order dhe Featured sipas udhëzimit editorial.
3. Përzgjidh Featured image dhe Gallery images.
4. Në AL/EN plotëso Name, Slug, Short description, Description, Location dhe Visitor information.
5. Plotëso SEO title/description, ruaj dhe kontrollo katalogun /attractions.

| Fusha e veçantë | Udhëzimi |
| --- | --- |
| Location | Vendndodhja e lexueshme për vizitorët |
| Visitor information | Informacion praktik: akses, udhëzime, orare ose kufizime të konfirmuara |
| Description | Përshkrimi i plotë i pikës së interesit |
| Gallery images | Fotot që ndihmojnë vizitorin të kuptojë vendin |

Mos shkruaj çmime, aksesueshmëri ose orare të pakonfirmuara. Featured nuk garanton vetë publikim; kontrollo Status dhe datën. Për heqje nga struktura kontakto Admin-in; për fshehje të përkohshme mund të përdoret Draft me miratim.

## 17. Event spaces: hapësirat për evente

Hap Places & spaces > Event spaces. Moduli është i veçantë nga Leasing spaces; tipi caktohet nga sistemi. Përdor Create event space ose Edit.

| Fusha | Përdorimi |
| --- | --- |
| Capacity | Kapacitet pozitiv, kur është i konfirmuar |
| Area (m²) | Sipërfaqja reale; mos e vlerëso nga fotografitë |
| Price from | Vlerë numerike bazë kur ka informacion të miratuar |
| Currency | Kod me tre karaktere, p.sh. EUR |
| Request / booking action | Zgjedh mënyrën e kërkesës ose lidhjen e jashtme |
| External booking URL | Kërkohet për External link ose Both |
| Featured | Veçim sipas template-it |
| Featured image / Gallery images | Pamja kryesore dhe fotot e hapësirës |

Në AL/EN plotëso Name, Slug, Short description, Description, Features, Location, Price label dhe SEO title/description. Features mund të jetë listë e pajisjeve apo shërbimeve të konfirmuara. Price label është tekst publik; duhet të përputhet me çmimin real dhe kushtet e stafit.

### Rrjedha e përdorimit

1. Ruaj hapësirën si Draft dhe kontrollo të dhënat me stafin.
2. Për një kërkesë nga webi zgjidh Internal request form; për rezervim nga një shërbim tjetër vendos External dhe URL-në.
3. Publiko me datë dhe kontrollo /event-space në AL/EN.
4. Testimi i formularit duhet të koordinohet me Admin-in, sepse krijon një Submission reale.

Formulari në web është kërkesë për disponueshmëri dhe shqyrtim nga stafi. Ky modul nuk krijon automatikisht rezervime të konfirmuara dhe nuk kryen pagesa. Editor-i i lexon kërkesat te Submissions; Admin-i menaxhon shqyrtimin.

## 18. Leasing spaces: njësitë me qira

Hap Places & spaces > Leasing spaces. Formulari ndan tekstin editorial nga njësia reale e planit. Krijimi i një posti nuk krijon një njësi të re në hartë.

| Fusha | Udhëzimi |
| --- | --- |
| Floor / map unit | Zgjidh njësinë reale nga inventari. Kati caktohet sipas njësisë |
| Not assigned (unavailable) | Posti nuk është lidhur me hartën dhe nuk është i disponueshëm publikisht |
| Available for applications | Shëno vetëm kur stafi konfirmon që njësia mund të marrë aplikime |
| Area (m²) | Duhet sipërfaqe më e madhe se zero kur njësia është Available |
| Capacity, Price from, Currency | Vlera të miratuara për hapësirën; EUR është shembull kodi monetar |
| Request / booking action dhe External URL | Mënyra e aplikimit; External/Both kërkojnë URL |
| Featured image / Gallery images | Imazhet e njësisë |

Fushat AL/EN janë si te Event spaces: Name, Slug, Short description, Description, Features, Location, Price label dhe SEO. Ruaj me Create leasing space ose Save leasing space.

### Rregulli i hartës

Njësia është e gjelbër dhe e klikueshme vetëm kur posti është Published, data e publikimit ka ardhur, ekziston përkthimi për gjuhën e kërkuar, është lidhur një map unit dhe Available for applications është aktiv. Njësitë e tjera shfaqen të kuqe dhe nuk hapin aplikim.

Një map unit mund të lidhet vetëm me një post, përfshirë postet në Trash. Nëse del gabim që njësia është përdorur, kërko Admin-in të kontrollojë lidhjen ekzistuese; mos krijo njësi të sajuara. Kati nuk shkruhet manualisht dhe koordinatat e hartës nuk janë fushë editoriale.

### Inventari dhe aplikimet

Inventari aktual përfshin Basement -1, Ground, Third, Roof L+4 dhe Exterior. Ka 52 njësi të paracaktuara në planet e furnizuara. Njësitë që mungojnë në plan nuk duhen shpikur; p.sh. A16 dhe D2 nuk janë në inventarin e furnizuar.

Formulari i leasing mbledh informacion kompanie/kontakti, ofertë dhe dokumente. Llogaritja e qirasë së propozuar përdor sipërfaqen e njësisë dhe ofertën për m². Kjo është aplikim për shqyrtim, jo kontratë e pranuar. Dokumentet private menaxhohen nga Admin-i, jo te Media publike.

## 19. Careers: pozicionet e punës

Hap Activities & careers > Careers. Create career krijon një pozicion; Edit përditëson një ekzistues. Ky formular nuk ka foto kryesore ose galeri në versionin aktual.

| Fusha | Përdorimi |
| --- | --- |
| Status / Publication date / Display order | Publikimi dhe rendi editorial |
| Employment type | Full time, Part time, Contract ose Internship |
| Application deadline | Afati i aplikimit; kontrollo datën dhe orën |
| Application action | Formular i brendshëm, lidhje e jashtme, të dyja ose pa veprim |
| External application URL | Kërkohet për External/Both |
| Title / Slug AL/EN | Emri i pozicionit dhe URL-ja |
| Department / Location | Departamenti dhe vendndodhja e punës |
| Short description / Description | Përmbledhja dhe përshkrimi i pozicionit |
| Requirements | Kriteret, me listë të lexueshme |
| SEO title / description | Metadata për AL/EN |

### Publikimi i një pozicioni

1. Merr përshkrimin dhe afatin e miratuar nga personi përgjegjës.
2. Krijo Draft, plotëso AL/EN dhe kriteret.
3. Zgjidh mënyrën e aplikimit dhe kontrollo lidhjen e jashtme, nëse ka.
4. Vendos Publication date dhe Published; ruaj.
5. Kontrollo /careers dhe butonin e aplikimit. Pas afatit, pozicioni mund të mos jetë më i arritshëm sipas rregullave të publikimit.

Aplikimi krijon Submission. Editor-i nuk shkarkon CV-të dhe nuk ndryshon statuset e shqyrtimit. Mos ngarko CV në Media. Për të korrigjuar ose mbyllur një pozicion, koordino me stafin; mos zgjat një afat pa miratim.

## 20. Submissions: kërkesat, vetëm për lexim

Menuja Submissions është më vete. Editor-i sheh Read-only access. Këtu lexohen kërkesat nga formularët e kontaktit, eventeve, hapësirave, leasing dhe karrierave.

### Gjetja dhe leximi

1. Kërko sipas emrit, emailit ose subjektit.
2. Zgjidh Type dhe Status dhe kliko Filter.
3. Kliko një kërkesë për detajet.
4. Lexo emrin, emailin, telefonin, subjektin, mesazhin, kohën e krijimit dhe Request details që shfaqen.
5. Nëse duhet veprim, njofto Admin-in duke identifikuar kërkesën. Vetëm leximi nuk e ndryshon statusin në In review.

| Statusi | Kuptimi orientues |
| --- | --- |
| New | Kërkesë e re |
| In review | Në shqyrtim nga stafi |
| Replied | Stafi e ka shënuar si të përgjigjur |
| Closed | Stafi e ka mbyllur |

Statuset janë shënime pune të stafit; nuk provojnë automatikisht një rezervim ose kontratë. Një filtër Program application mund të shfaqet për rekorde historike; kjo nuk krijon një formular të ri te Carousel posts.

### Kufizimet dhe privatësia

Editor-i nuk ka Save review, ndryshim statusi, Internal notes, Download për CV/dokumente ose Export CSV. Këto i përkasin Admin-it. Mund të shfaqet emaili si lidhje mailto; kjo hap klientin e email-it dhe nuk regjistron automatikisht përgjigje në CMS.

Mos shpërnda screenshot-e me të dhëna personale. Kur raporton një problem, përdor vetëm informacionin minimal të nevojshëm dhe kanalin e miratuar të stafit.

## 21. SEO, lidhjet dhe kontrolli publik

### Metadata për Editor-in

SEO title përmbledh temën e faqes; SEO description shpjegon përmbajtjen pa premtime të pasakta. Shkruaji në gjuhën përkatëse dhe shmang kopjen identike për çdo rekord. Nëse janë bosh, projekti përdor metadata rezervë kur janë të disponueshme; kjo nuk zëvendëson kontrollin editorial.

Për ndarjen sociale, titulli/metadata dhe fotoja kryesore duhet të përputhen me përmbajtjen. Default SEO and social text te Website content është rezervë e përbashkët; një rekord individual ndryshohet në modulin e vet. Nuk ka nevojë të shtosh kod JSON-LD ose tag-e HTML në përmbajtje.

### Lista e kontrollit në web

- Kontrollo titullin, paragrafët, ë/ç dhe formatimin në AL/EN.
- Kontrollo foton dhe prerjen e saj, jo vetëm që është zgjedhur në Media.
- Kliko lidhjet e tekstit, kontaktit dhe veprimit. Mos lër shembuj ose URL të mjedisit tënd lokal në përmbajtje.
- Kontrollo faqen në desktop dhe në një ekran të vogël.
- Për publikim kontrollo Status, Publication date dhe kushtet e modulit.
- Mos ndrysho slug-e të publikuara pa plan ridrejtimi nga administratori.

Sitemap, robots, llms.txt, domaini, certifikata HTTPS dhe aktivizimi i indeksimit janë konfigurim i projektit/serverit. Nuk janë detyra të Editor-it dhe nuk bëhen nga fushat SEO të një artikulli. Në preview indeksimi mund të jetë i çaktivizuar qëllimisht. Asnjë plotësim metadata-je nuk garanton renditje ose indeksim të menjëhershëm.

## 22. Shembuj të plotë për punën e përditshme

### Shembull A: një lajm i ri

1. News > Create article; Status = Draft.
2. Vendos autorin, titujt dhe slug-et AL/EN, Excerpt dhe Article content.
3. Set / change image > zgjidh foton > Use selected image.
4. Add images > zgjidh galerinë > Use selected images; rregullo rendin.
5. Plotëso SEO dhe kliko Create article.
6. Rihap artikullin për kontroll; vendos datën dhe Published, pastaj Save article.
7. Kontrollo artikullin në AL/EN. Nëse u publikua gabim, përdor Draft me miratim ose Trash kur duhet heqje; Restore e bën Admin-i.

### Shembull B: një kartë në Inovacion

1. Programs / Carousel posts > Create carousel post.
2. Carousel page = Innovation; vendos një rend, p.sh. 30.
3. Plotëso Title, Slug dhe Carousel caption AL/EN dhe foton kryesore.
4. Ruaj si Draft; pastaj publiko me datë të vlefshme.
5. Kontrollo /inovacion dhe versionin EN përkatës. Nëse titulli i madh i faqes është i gabuar, ndrysho Page, jo titullin e kartës.

### Shembull C: një njësi me qira

1. Leasing spaces > Edit rekordin e njësisë reale, ose krijo një nëse nuk ekziston.
2. Zgjidh Floor / map unit të saktë; kontrollo që të mos jetë përdorur nga një post tjetër.
3. Vendos Area (m²) reale, përmbajtjen AL/EN dhe imazhet.
4. Me konfirmim të stafit, aktivizo Available for applications dhe publiko me datë.
5. Kontrollo hartën: njësia duhet të bëhet e gjelbër dhe e klikueshme. Nëse mbetet e kuqe, kontrollo të pesë kushtet e publikimit të leasing.

### Shembull D: ndryshimi i kontaktit publik

1. Website content > Public contact and footer.
2. Ndrysho telefonin/emailin publik dhe oraret AL/EN të miratuara.
3. Kliko Save content.
4. Kontrollo footer dhe Contact në web. Marrësi teknik i kërkesave nuk ndryshohet këtu; atë e menaxhon Admin-i.

## 23. Gabimet e zakonshme dhe zgjidhjet

| Simptoma | Çfarë kontrollon Editor-i |
| --- | --- |
| Rekordi nuk shfaqet në web | Status, Publication date, gjuhën dhe kushtet e modulit; pastaj njofto Admin-in |
| Slug është përdorur | Zgjidh slug tjetër në të njëjtën gjuhë/modul; mos ndrysho URL të publikuara pa miratim |
| Ruajtja refuzohet | Lexo gabimet poshtë fushave dhe kontrollo edhe panelin EN |
| Galeria/fotoja nuk u aplikua | Use selected image/images dhe më pas Save/Create të rekordit |
| Upload refuzohet | Formati i lejuar, madhësia 10 MB dhe lloji i përzgjedhësit; PDF/video nuk lejohen si Featured image |
| Faqja thotë 413 | Ngarkesa mund të kalojë kufirin e serverit; njofto Admin-in |
| Sesioni ka skaduar / 419 | Ruaj tekstin që mundesh pa të dhëna sensitive, rifresko ose rihyr dhe provo një herë |
| Forbidden / 403 | Kontrollo rolin dhe veprimin; Admin-only nuk zgjidhet duke ndryshuar URL-në |
| Eventi ndryshoi pas editimit | Kontrollo paralajmërimin WordPress dhe kërko korrigjim te burimi |
| Leasing mbetet i kuq | Published, data, përkthimi, njësia e hartës dhe Available |
| Businesses nuk shfaqet te /businesses | Kontrollo Social Spaces, publikimin dhe gjuhën |
| Një fushë read-only nuk ndryshohet | Identifikues i mbrojtur i template-it; ndrysho tekstin e përkthyer |
| Email resetimi nuk vjen | Inbox/Spam dhe Admin-i; SMTP duhet konfiguruar dhe testuar |
| Foto të prishura / Not secure | Mund të jetë URL, certifikatë ose akses serveri; raporto problemin, mos ndrysho .env |

### Raportimi i dobishëm

Dërgo emrin e modulit, titullin e rekordit, gjuhën, URL-në, orën e problemit, hapat që bëre dhe tekstin e gabimit. Screenshot-i duhet të fshehë të dhënat personale. Mos dërgo fjalëkalime, çelësa API, APP_KEY, skedarë .env ose çelësa privatë SSH.

Mos kliko Save ose Sync vazhdimisht kur duket se faqja po punon. Prit përgjigjen dhe kontrollo nëse ndryshimi është ruajtur përpara se të krijosh një kopje tjetër.

## 24. Checklist-i i dorëzimit dhe fjalori

### Para publikimit të çdo rekordi

- Moduli dhe rekordi janë të saktë; nuk po ndryshon një përmbajtje tjetër me titull të ngjashëm.
- AL dhe EN janë plotësuar dhe kontrolluar nga ana gjuhësore.
- Slug-et janë të vlefshme; një URL ekzistuese nuk është ndryshuar pa miratim.
- Fotoja kryesore, alt text dhe galeria janë të sakta dhe me të drejta përdorimi.
- Datat, oraret, çmimet, kapaciteti, adresat dhe kontaktet janë të konfirmuara.
- Published dhe Publication date janë të sakta; kushtet e modulit janë plotësuar.
- Formulari është ruajtur pa gabime dhe rezultati është kontrolluar në web.
- Nuk ka tekst demo, placeholders editorialë ose të dhëna personale që nuk duhet të jenë publike.

### Në fund të ditës

Ruaj punën, kontrollo rekordet e ndryshuara, njofto Admin-in për çështje të hapura dhe dil me Sign out në kompjuter të përbashkët. Koordino me kolegët që të mos ndryshoni të njëjtin rekord njëkohësisht; sistemi nuk ofron këtu një mekanizëm të garantuar të bashkëpunimit në kohë reale.

| Termi | Kuptimi |
| --- | --- |
| Rekord/post | Një artikull, event, biznes, hapësirë ose element tjetër i menaxhuar |
| Frontend / web publik | Faqet që shohin vizitorët |
| Dashboard | Ndërfaqja e menaxhimit |
| Draft | Përmbajtje e fshehur nga publikimi |
| Published | Status publikimi, me kushte shtesë sipas modulit |
| Slug | Pjesa e URL-së që identifikon përmbajtjen |
| Featured image | Fotoja kryesore e rekordit |
| Gallery | Fotot shtesë të lidhura me një rekord |
| Alt text | Përshkrimi alternativ i një imazhi |
| Submission | Kërkesë e dërguar nga formulari, për shqyrtim nga stafi |
| Trash / Restore | Heqje e kthyeshme / rikthim; për Editor-in Trash lejohet vetëm për News |
| Sync WordPress | Përditësimi i eventeve nga burimi WordPress |

### Kufiri i këtij manuali

Manuali është kontrolluar kundrejt formularëve, rregullave të validimit, politikave të autorizimit dhe vendimeve të projektit në versionin e datës 12 tetor 2026. Përmban udhëzime operacionale, jo kredenciale apo konfigurim serveri. Nuk u kryen ndryshime reale në dashboard gjatë hartimit. Para shpërndarjes së tij te ekipi, Admin-i duhet të japë adresën aktuale të aksesit dhe të konfirmojë që roli i secilit është Editor.

SMTP/Postmark mbetet për t'u konfiguruar dhe testuar nga Admin-i. Resetimi me email dhe njoftimet nuk duhen konsideruar funksionale vetëm sepse fushat e tyre ekzistojnë në CMS. Nëse ndërfaqja ndryshon, kërko versionin e përditësuar të manualit.

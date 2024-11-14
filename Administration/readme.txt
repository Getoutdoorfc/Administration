Plugin navn:  Administration
Virksomhed: Get Outdoor 
Hjemmeside: getoutdoor.dk 
Description: Et WordPress-plugin til integration med Microsoft Graph API, der muliggør automatisering af kalenderbegivenheder baseret på WooCommerce-ordredata og produktinformation.
Version: 1.0

1	Introduktion
Dette plugin ved navn Administration er udviklet for at understøtte Get Outdoors behov for effektiv planlægning og kundehåndtering i forbindelse med oplevelser og arrangementer. Pluginet optimerer og automatiserer WooCommerce’s integration med Microsofts økosystem ved hjælp af Microsoft Graph API, især Outlook Kalender. Dette tilpassede plugin reducerer administrationstiden, sikrer en problemfri bookingproces og skaber en mere intuitiv kundeoplevelse.
Pluginet fungerer som en langsigtet løsning, som løbende udvides med AI-assistance, hvilket gør det muligt at tilpasse og udvikle nye funktioner uden krav om avanceret teknisk erfaring. Visionen er, at Administration-pluginet fortsat kan opdateres og optimeres for at lette administrative opgaver og sikre Get Outdoor en effektiv oplevelse med planlægning og service for kunderne. Overordnet mål er at nedsinke virksomhedens adminstrationstid og og optimere kundeflow samt kunderservice.
Get Outdoors - Vision og Mission
Vision: "Vi forbinder mennesker gennem bæredygtige oplevelser i naturen."
Mission: "Vi udvikler udendørs aktiviteter, der skaber livsglæde og styrker fællesskabet, samtidig med at naturen formidles på inspirerende måder. Ved at tænke i cirkulære løsninger skaber vi merværdi for vores kunder og bidrager til både samfundet og den grønne dagsorden."
Primær komponent i struktur og opbygning i dokumentet:
0.	Plugin opsætning og fjernelse
1.	WooCommerce:
a.	Oplevelse: 	Ny produktgruppe
b.	Udlejning: 	Ny produktgruppe
c.	Email ordrebekræftelse layout
d.	Fjernelse af std woocommerce functioner
2.	Automatisk Kalendersynkronisering:
a.	Microsoft opsætning af api samt 2oAuth login 
b.	Ordre complet  kundeinfo sendes til begivenhed
c.	Opdatering produkt  begivenhed oprettelse i kalenderen 
3.	WordPress menu – Hovdemenu - Get Inddoor 
a.	Microsoft Opsætning: Selve opsætning med vejledning 
b.	Årshjul: se alle oplevelser i årshjulet
4.	Genneralt:
a.	Sikkerhed og cache
b.	Log struktur og beskeder 
c.	BRUGER VENLIGT – placeholdere og hovereffekter der hvor det giver mening
d.	Mobilvenligt og browser venlig
5.	Mappestruktur:

 	

2	Generelt
2.1	Overordnet Arkitektur
2.1.1	Anvendelse af Object-Oriented Programming (OOP):
Hvor: Generelt i hele pluginet, især i komponenterne under components/.
Hvordan og hvorfor: Strukturér koden i klasser og objekter for bedre genbrug og vedligeholdelse. For eksempel kan du oprette en baseklasse for produkttyper i components/product-types/class-product-type.php, som class-experiences.php og class-rentals.php kan nedarve fra. Dette reducerer kodegentagelse og gør det nemmere at tilføje nye produkttyper i fremtiden.

2.1.2	Modulær Arkitektur:
Hvor: Gennem hele pluginet.
Hvordan og hvorfor: Del funktionalitet op i mindre, genanvendelige moduler. For eksempel kan du opdele utilities/helpers.php i specifikke hjælpefiler såsom utilities/date-helper.php og utilities/string-helper.php.

2.1.3	Konsekvente Navngivningskonventioner:
Hvor: Overalt i koden.
Hvordan og hvorfor: Følg WordPress' kodestandarder for navngivning af filer, klasser og funktioner. For eksempel skal klassefiler navngives class-class-name.php, og klassenavnet skal være Class_Name.

2.1.4	Brug af WordPress Hooks og Filtre:
Hvor: I integrationer med WooCommerce og WordPress.
Hvordan og hvorfor: Udnyt eksisterende hooks og filtre i WordPress og WooCommerce for bedre integration og for at undgå at ændre kernekoden.


2.2	Log
Formål:
At implementere en robust og sikker logging-mekanisme, der centraliserer registreringen af alle hændelser, fejl, advarsler og vigtige brugerinteraktioner i pluginet. Dette sikrer effektiv fejlfinding, overvågning af systemets sundhed og overholdelse af sikkerheds- og databeskyttelseskrav.
2.2.1	Logstruktur og Implementering
”For detaljer om sikkerhedsforanstaltninger for logfiler, se afsnit med sikkerhed under genneralt”
Centraliseret Logger:
Fil: utilities/logger.php
Beskrivelse: En singleton-klasse, Logger, der håndterer alle logningsoperationer i pluginet. Klassen sikrer, at alle komponenter bruger den samme logging-mekanisme for konsistens. For implementering af logging i specifikke komponenter, se nederst i afsnit 3.3.1 under Microsoft komponent logging

Funktioner:

Logrotation: implementeres for at forhindre ubegrænset vækst af logfiler. utilities/logger.php
Standardindstilling: Roter logfiler månedligt eller når de når 5 MB.
Opret funktionalitet til at kontrollere filstørrelse og rotere filer automatisk

Logniveauer: Understøtter forskellige logniveauer som DEBUG, INFO, WARNING, ERROR, CRITICAL.
Metoder:
Log	($level, $message, $context = []): Generisk logningsmetode.
Debug	($message, $context = []): Logger debug-information.
Info	($message, $context = []): Logger generel information.
Warning	($message, $context = []): Logger advarsler.
Error	($message, $context = []): Logger fejl.
Critical	($message, $context = []): Logger kritiske fejl, der kræver øjeblikkelig opmærksomhed.
Formatering:
Logposter formateres med tidsstempel, logniveau, besked og kontekstdata.
Eksempel: [2024-11-07 12:34:56] ERROR: Fejlbesked her. Context: {"ordre_id":123}
Tråd-sikkerhed:
Logger-klassen er designet til at være tråd-sikker i miljøer, hvor flere processer kan skrive til logfilen samtidigt.
Logfil og Placering:
Placering: /wp-content/plugins/administration/logs/plugin-log.txt
Adgangsrettigheder:
Logs-mappen: Rettigheder sat til 0750.
Logfilen: Rettigheder sat til 0640.
Ejerskab: Skal ejes af webserverens bruger for at sikre korrekt adgangskontrol.
Dette forhindrer direkte adgang til logfiler via HTTP.
Dataanonymisering:
Følsomme data som adgangskoder, tokens og personlige oplysninger maskeres eller udelades.
”For detaljer om dataanonymisering, se afsnit 2.2.1.3 Beskyttelse af Logfiler”

2.2.2	Logging Best Practices & Integration med WordPress
Konsistens:
Alle logposter skal følge det samme format og struktur for at lette analyse og overvågning.
Relevans:
o	Log kun relevante oplysninger, der er nyttige for fejlfinding og overvågning.
o	Undgå overdreven logging af trivielle hændelser, da dette kan gøre logfilerne svære at gennemgå.
Performance:
o	Logfiler genereres gennem utilities/logger.php, som håndterer log-kald og gemmer data i plugin-log.txt med filrettighederne sat til 600, hvilket yderligere begrænser adgangen til logdataene. (Se afsnittet om loghåndtering og dataopbevaring for specifikke retningslinjer om loggingssikkerhed.)
o	Logging bør implementeres på en måde, der ikke påvirker pluginets ydeevne negativt.
o	Asynkron logging eller bufferning kan overvejes i miljøer med høj belastning.
Lovgivning og Overholdelse:
o	Sørg for, at logging overholder GDPR og andre relevante databeskyttelseslove.
o	Indhent samtykke fra brugere, hvis nødvendigt, og informer om logningspraksis i privatlivspolitikken.
Brug af WordPress-funktioner:
o	Integrer logging med WordPress' eget WP_Debug-system, hvis det er aktiveret.
o	Respekter indstillingerne for WP_DEBUG og WP_DEBUG_LOG for at undgå uønsket logging i produktionsmiljøer.
Hooks og Actions: 
Brug relevante hooks og actions til at indfange hændelser for logging uden at påvirke kernefunktionaliteten.
2.2.3	Eksempel på Implementering
 1. // Initialisering af logger
 2. $logger = Logger::getInstance();
 3.  
 4. // Logning af en informativ besked
 5. $logger->info('Plugin aktiveret. Version: ' . PLUGIN_VERSION);
 6.  
 7. // Logning af en fejl med kontekstdata
 8. try {
 9.     // Kode, der kan kaste en undtagelse
10. } catch (Exception $e) {
11.     $logger->error('Fejl under behandling af ordre.', [
12.         'ordre_id' => $order_id,
13.         'fejlbesked' => $e->getMessage(),
14.     ]);
15. }  
16.  



2.3	Sikkerhed
Formål: Beskytter følsomme oplysninger som API-nøgler og tokens og validerer input for at sikre pluginets integritet. (Relater til generelle sikkerhedskrav i dokumentets hovedafsnit om sikkerhed for at sikre konsistens i beskyttelsen af brugerdata.)

2.3.1	Token Håndtering
Fil: components/integrations/microsoft-graph/token-handler.php
Beskrivelse:
Tokens gemmes krypteret i WordPress options tabel ved hjælp af update_option() og get_option().
Kryptering sker ved hjælp af OpenSSL-funktioner såsom openssl_encrypt() og openssl_decrypt().
En sikker nøgle til kryptering gemmes i .env-filen eller i wp-config.php.

Automatisk Fornyelse:
En WordPress cron-job opsættes til at forny tokens inden udløb.
Interagerer med: WordPress' wp_schedule_event() funktion.

2.3.2	Sikkerhed i API-Kald
Interagerer med: components/integrations/microsoft-graph/api.php
Alle API-kald til Microsoft Graph API sker over HTTPS.
HTTP-fejl og undtagelser håndteres med try-catch blokke, og fejl logges uden at afsløre følsomme data.

2.3.3	Adgangskontrol og Autorisation
Kun brugere med manage_options kapabilitet kan tilgå opsætningssider og følsomme funktioner.
Nonce-verifikation bruges i alle formularer ved hjælp af wp_nonce_field() og check_admin_referer().

2.3.3.1	Dataopbevaring
.env-filen oprettes i pluginets rodmappe og bruges til at gemme følsomme data, med filrettigheder sat til 600, så kun serveren kan få adgang. Som backup gemmes oplysninger også i wp-config.php, hvilket sikrer, at data altid er beskyttet. (Se afsnittet om databeskyttelse og credential storage for flere detaljer om håndtering af API-nøgler.)
.env-filen placeres uden for webroot, hvis muligt.
2.3.3.2	.htaccess til beskyttelse: 
For at sikre, at .env-filen ikke er offentligt tilgængelig, tilføjes en .htaccess-fil med følgende konfiguration:
Functioner: Efter installation starter automatisk søgning efter .htaccess filen (normalt placeret i roden) og automatisk oprettelse af koden, finder sted der
1. apache
2. <Files ".env">
3. Order Allow,Deny
4. Deny from all
5. </Files>
6.  
Denne opsætning forhindrer uautoriseret adgang til følsomme data i .env. (Se sikkerhedsprotokoller for adgangskontrol og begrænsninger.) 
Offentlig adgang til logmappen blokeres ved hjælp af en .htaccess-fil med følgende:
1. apache
2. Order Allow,Deny
3. Deny from all
4.  
Dette sikrer, at logdata ikke kan tilgås udefra, hvilket forhindrer uautoriseret adgang. (Se afsnittet om adgangssikkerhed og sikkerhedsanbefalinger for yderligere oplysninger.)


2.3.3.3	Beskyttelse af Logfiler
Adgangskontrol:
Sørg for, at logfiler ikke er tilgængelige for uautoriserede brugere.
Kun administratorer og serveren selv skal have adgang.
Dataanonymisering:
Følsomme oplysninger som adgangskoder, tokens og personlige data må ikke logges i klartekst.
Brug hashing eller maskering, hvis det er nødvendigt at referere til sådanne data.
Overvågning:
Implementer overvågning af logfilens integritet.
Opdag og reager på uautoriserede ændringer eller adgangsforsøg.
Compliance:
Sørg for, at logging overholder relevante sikkerhedsstandarder og lovgivning, såsom GDPR.
Dokumenter logningspraksis og inkluder den i virksomhedens sikkerhedspolitik.
2.3.4	Input Sanitisation
Alle brugerinput sanitiseres ved hjælp af WordPress-funktioner:
Tekstfelter: sanitize_text_field()
URL'er: esc_url()
E-mails: sanitize_email()

2.4	caching
Formål: Forbedrer pluginets ydeevne og reducerer belastningen på serveren ved at begrænse antallet af API-kald og gentagne forespørgsler. (Se afsnittet om API-ydeevne og specifikke komponenter for caching for detaljer om implementering af cache-strategier.)
Interagerer med: components/integrations/microsoft-graph/helpers.php, components/product-types/experiences/helpers.php
2.4.1.1	Caching af API-responser og statiske data: 
Caches API-responser for ofte anvendt data som produktoplysninger, datoer og brugerindstillinger med passende udløbstider (f.eks. 24 timer). (Se specifikt afsnittet om datahåndtering i caching for detaljer om datatypens levetid og udløbstider.)
2.4.1.2	Cache-kontrol og validering:
Cachen opdateres automatisk ved ændringer i data, så oplysninger altid er opdaterede. WordPress’ transients-API anvendes for at styre cachelagring effektivt. (Se afsnittet om transients og API-håndtering for yderligere detaljer om automatisering og API-kald.)

2.5	Brugervenlig
Formål: Skaber en intuitiv brugeroplevelse, så brugerne hurtigt og uden forvirring kan navigere og konfigurere pluginet. (Se sektionen om brugergrænseflade og overordnede UX-principper for flere detaljer om designstrategier.)
Interagerer med: assets/css/admin.css, assets/js/admin.js, components/admin-interface/, components/product-types/experiences/
2.5.1.1	Intuitive Felter og Hovereffekter: 
Placeholder-tekster og hover-effekter i inputfelterne giver brugeren tydelige anvisninger. (Se afsnittet om brugergrænseflade for detaljer om visuelle hjælpemidler.)
2.5.1.2	Dynamiske Fejlmeddelelser og Validering i Real-Time: 
Validering sker i realtid og guider brugeren med klare fejlmeddelelser, hvilket reducerer fejl og frustration. (Se sektionen om brugerfeedback og fejlmeddelelser for detaljer om real-time validering.)
2.5.1.3	Responsive Elementer og Tilgængelighed: 
Pluginet er designet responsivt og følger tilgængelighedsstandarder for at sikre brugervenlighed på både desktop og mobile enheder. (Se afsnittet om tilgængelighedsstandarder for overholdelse af WCAG-retningslinjer og responsive design.)
Understøttelse af dansk og engelsk i både frontend og backend.
Brug WordPress' internationaliseringsfunktioner (__(), _e()) for alle tekster.
Sprogfiler: Opdater languages/ mappen med de nødvendige .mo og .po filer.


________________________________________
2.6	Opsætning, brugerretigheder og fjernelse
2.6.1.1	Installation
Ved installation skal der oprettes nødvendige database-tabeller og standardindstillinger. Brug register_activation_hook() til at håndtere disse, og sørg for korrekt SQL-syntax og fejlhåndtering. Inkluder evt. brugerroller og tilladelser, hvis nødvendigt.
2.6.2	bruger rettigheder
Tilføjelse af medarbejder rolle:
Fil: includes/roles.php
Har adgang til:
Alt med theme Salient 
Oprettelse og redigering af produkter.
Håndtering af ordrer.
Lageroversigt.
Ninja forms
Sider
kommentare
medier
Alle woocommerce plugin og forbindelser. 
Yoast SEO
Yoast Duplicate Post
Yoast SEO Premium
Popup builder 
Udesende
Indstillinger kun med google tag menager 
wp bakery page builder 
Facebook til WooCommerce
FreePay til WooCommerce
Google Analytics for WooCommerce
TranslatePress - Multilingual

Har ikke adgang til:
Deaktivering for visning af menupunkter som: 
Alle plugins hvor der ikke er givet adgang til. 
Microsoft opsætning i vores administrations plugin 
Installation eller redigering af andre plugins.

2.6.2.1	Aktivering
Ved aktivering skal du kontrollere WordPress- og PHP-versioner for kompatibilitet. Opret evt. nødvendige cache-rensningsrutiner eller permalinks-opdateringer. Brug register_activation_hook() til at sikre, at alt er klar til brug.
2.6.2.2	Deaktivering
Ved deaktivering bør alle midlertidige data eller sessions lukkes korrekt. Brug register_deactivation_hook() til at rydde op uden at slette vigtige indstillinger og data, hvis plugin’et skal kunne genaktiveres uden tab.
2.6.2.3	Afinstallation
Sørg for, at alle spor fjernes fra systemet, inklusive database-tabeller og brugerdefinerede indstillinger. Brug register_uninstall_hook() eller en separat afinstallationsfil til fuldstændig oprydning
2.7	Fejlhåndtering og Notifikationer
Formål: At sikre, at fejl håndteres korrekt, og at kritiske problemer kommunikeres til de relevante parter i tide.
Interagerer med: utilities/logger.php, components/admin-interface/settings.php, assets/js/admin.js (for visning af fejlmeddelelser)
2.7.1	Fejlhåndtering
Try-Catch Blokke:
Anvend try-catch blokke omkring kode, der kan generere undtagelser.
Log alle fangede undtagelser med detaljeret kontekst.
Graceful Degradation:
Hvis en funktion fejler, bør pluginet forsøge at fortsætte uden at påvirke brugeroplevelsen negativt.
Informer brugeren med en venlig fejlmeddelelse, hvis det er relevant.
2.7.2	Notifikationer
E-mail Notifikationer:
Ved kritiske fejl eller sikkerhedshændelser sendes en e-mail til administratoren.
E-mailen skal indeholde en klar beskrivelse af problemet og eventuelle anbefalede handlinger.
Tilføj en indstilling i pluginets indstillinger for at aktivere e-mail notifikationer ved kritiske fejl.
Fil: components/admin-interface/settings.php
Tilføj checkbox: "Modtag e-mail notifikationer ved kritiske fejl".
Standardindstilling: Deaktiveret.

Dashboard Alerts:
Vigtige meddelelser kan vises i WordPress admin-dashboardet for administratorer.
Brug WordPress' admin_notices hook til at implementere dette.


2.7.3	Samlet Plan for Validering
Formål: At sikre konsistent og effektiv validering af al brugerinput på tværs af pluginet for at forhindre fejl, sikre dataintegritet og forbedre sikkerheden.
Overordnet Valideringsstrategi:
•	Centralisere alle valideringsfunktioner i utilities/validation.php for genbrug og ensartethed.
•	Implementere både server-side (PHP) og client-side (JavaScript) validering for at forbedre brugeroplevelsen.
•	Anvende WordPress' indbyggede sanitiserings- og valideringsfunktioner.
2.7.3.1	Implementering af Validering
Server-side Validering:
Fil: utilities/validation.php
Funktioner:
validate_date_format($date): Validerer datoformater.
validate_numeric($value): Sikrer, at værdien er numerisk.
sanitize_text($text): Sanitiserer tekstinput.

Client-side Validering:
Fil: assets/js/admin.js og assets/js/frontend.js
Funktioner:
Real-time validering af felter ved hjælp af JavaScript.
Visning af øjeblikkelige fejlmeddelelser til brugeren.
2.7.4	Anvendelse i Komponenter
WooCommerce Produkttyper:
Interagerer med: components/product-types/experiences/validation.php
Validerer produktfelter såsom dato, varighed og SKU.

Microsoft Opsætning:
Interagerer med: components/admin-interface/microsoft-setup.php
Validerer API-legitimationsoplysninger og sørger for, at de har korrekt format.

Ordrebehandling:
Interagerer med: components/integrations/woocommerce/order-processing.php
Validerer ordredata før behandling og synkronisering med kalenderen.

2.7.5	Fejlmeddelelser og Feedback
Interagerer med: assets/css/admin.css, assets/js/admin.js
Ved valideringsfejl vises brugervenlige fejlmeddelelser, der forklarer problemet og hvordan det kan løses.
Fejlmeddelelser logges for at hjælpe med fejlfinding.

2.8	Versionskontrol og Samarbejde:
Værktøj: GitHub
Main Branch: Indeholder den stabile og færdige kode.
Working Branch: Bruges til udvikling og nye funktioner.
Commit-beskeder: Følg en konsistent og beskrivende stil for commit-beskeder.
Samarbejde:
Selvom der i øjeblikket kun er én udvikler, er dokumentationen og koden struktureret for at lette fremtidigt samarbejde.
Staging-site:
URL: https://staging.getoutdoor.dk/
Bruges til at teste nye funktioner inden de merges til "main".

2.9	Udviklingsmiljø og Afhængigheder:
Udviklingsværktøjer:
Editor: Visual Studio Code (VSCode)
Extensions: IntelliSense, Copilot, Prettier, ftp opkobling
Ekstra FTP-klient: FileZilla
Cloud Services: Azure Portal med fuld adgang
Afhængigheder:
PHP: Installeret lokalt med Composer
WordPress Core: Opsat med Intelephence for bedre autokomplettering
Microsoft Graph: wp_remote_post
Versionsstyring af Afhængigheder:
Brug Composer til at håndtere PHP-afhængigheder
Sikrer, at alle biblioteker er opdaterede og kompatible

2.10	Hosting-miljø og Serveropsætning:
Hostingudbyder: Simply
Adgang: Fuld adgang til serveren, inklusive FTP og database
Staging-site:
URL: https://staging.getoutdoor.dk/ - Bruges til test af nye funktioner
Serverkonfiguration:
Sørg for kompatibilitet med PHP-versioner og nødvendige udvidelser
SSL-certifikat er installeret for sikker kommunikation

2.11	GDPR Overholdelse:
Dataindsamling:
Pluginet indsamler kun data, der er nødvendige for ordrebehandling.
Persondata:
Navn, e-mail, telefonnummer, ordreoplysninger.
Databeskyttelse:
Følger WooCommerce's standarder for datahåndtering.
Ingen yderligere persondata opbevares uden brugerens samtykke.
Bemærkning: Ingen ekstra handling er nødvendig, da pluginet ikke indsamler yderligere data.

2.12	Performance Optimering
Kodeoptimering:
Minimering af unødvendige databasekald.
Effektiv håndtering af loops og betingelser.
Asset Loading: 
Indlæsning af CSS og JS kun på relevante sider.
Brug af minificerede filer.
Caching: Implementer caching, hvor det er relevant, som under caching .
Fil: assets/css/frontend.min.css, assets/js/frontend.min.js
Brug af wp_enqueue_script og wp_enqueue_style med betingelser.
2.13	Support og Vedligeholdelse
Ansvarlig: Get Outdoor 
Fremtidige Opdateringer: Planlagt efter behov med fokus på at opretholde funktionalitet og sikkerhed og indføring af flere funktioner.
Dokumentation: Fortsæt med at opdatere dokumentationen i takt med ændringer.
Deling: Pluginet er udviklet med henblik på mulig fremtidig deling, men primært til internt brug.

3	Componenter
Viser alle komponenter i dette plugin:
3.1	WordPress menuen
Formål: At give brugerne en intuitiv adgang til pluginets funktioner gennem WordPress' admin-dashboard.
3.1.1	Menu:
Hovedmenu: "Get Indoor"
Beskrivelse: Opretter en ny hovedmenu i WordPress admin-dashboardet for at samle alle plugin-relaterede funktioner.
Implementering:
Fil: components/admin-interface/menu.php
Funktioner:
Registrerer hovedmenuen og undermenuer ved hjælp af WordPress' add_menu_page() og add_submenu_page() funktioner.
Bruger kapabiliteten manage_options for at begrænse adgang til administratorer.

Undermenuer 1: Microsoft Opsætning
Formål: Giver adgang til opsætningssiden for Microsoft Graph API-integration.
Implementering:
Fil: components/admin-interface/microsoft-setup.php
Funktioner:
Indlæser opsætningssiden for Microsoft-integration.
Håndterer formularindsendelser til lagring af API-legitimationsoplysninger.
Validerer og sanitiserer brugerinput ved hjælp af sanitize_text_field().

Undermenuer 2: Årshjul
Formål: (Fremtidig funktionalitet) Viser en oversigt over alle planlagte oplevelser i løbet af året.
Implementering:
Fil: components/admin-interface/year-wheel.php
Nuværende Status: Viser en placeholder-tekst, der indikerer, at funktionen er under udvikling.

Undermenu 3: Lageroversigt (for tilvalg, oplevelser, udlejning)
Fil: components/admin-interface/tilvalg-stock.php
Beskrivelse: Denne side viser en samlet lageroversigt over alle produkter, inklusive tilvalg. Den giver mulighed for at se og redigere lagerbeholdningen direkte.
Funktioner:
Henter data: Bruger WooCommerce's standardfunktioner til at hente lagerdata for hvert ekstraudstyr.
Visuel Tabel: Viser dataene i en overskuelig tabel med kolonner for navn, SKU, lagerantal og muligheden for at redigere lager direkte.
Interaktivitet: Mulighed for at justere lagerantal og gemme ændringer direkte fra tabellen.

3.1.2	Brugervenlighed og Funktionalitet
Intuitiv Navigation:
Menupunkterne er navngivet klart for at guide brugeren til de ønskede funktioner.
Visuelle Elementer:
Fil: components/admin-interface/assets/css/admin-interface.css
Beskrivelse: Indeholder styles til admin-grænsefladen, herunder menuikoner og hover-effekter.
JavaScript Funktioner:
Fil: components/admin-interface/assets/js/admin-interface.js
Beskrivelse: Håndterer dynamiske elementer og interaktioner på opsætningssiderne.
3.1.3	Sikkerhedsovervejelser
Adgangskontrol:
Kun brugere med manage_options capability kan se og interagere med pluginets menuer.
Input Validering og Sanitisation:
Alle brugerinput fra opsætningssiderne sanitiseres ved hjælp af WordPress' funktioner som sanitize_text_field() og esc_html().
Nonce-verifikation: Brug af wp_nonce_field() og check_admin_referer() for at beskytte mod CSRF-angreb.

3.1.4	WordPress - Komponent Logging
Formål:
At registrere alle væsentlige hændelser relateret til menu interaktioner for revision og fejlfinding.
Fil: utilities/logger.php
Menu Oprettelse og Indlæsning:  ”Når menuen oprettes”
Log en informativ besked, når hovedmenuen "Get Indoor" og dens undermenuer oprettes.
1. $logger->info('WordPress-menuen "Get Indoor" initialiseret.');
Brugerinteraktioner: ”Når en bruger tilgår en side”
1. Log, når en bruger tilgår pluginets indstillingssider.
2. Registrer brugerens ID og rolle for sikkerheds- og auditformål.
3. $logger->info('Bruger tilgik Microsoft Opsætningssiden.', [
4.     'bruger_id' => get_current_user_id(),
5.     'rolle' => implode(', ', wp_get_current_user()->roles),
6.     'tidspunkt' => current_time('mysql'),
7. ]);
8.  
Fejl og Advarsler:
1. $logger->error('Fejl ved indlæsning af Microsoft Opsætningssiden.', [
2.     'fejlbesked' => $e->getMessage(),
3.     'bruger_id' => get_current_user_id(),
4. ]);
5.  
Log eventuelle fejl under indlæsning af menuer eller indstillingssider.
Dette inkluderer manglende filer, adgangsproblemer osv.



4	Integration 
4.1	WooCommerce 
4.1.1	Oplevelse - produkttype
Når “Oplevelse” er valgt som produkttype:
Formål: At håndtere booking af oplevelser ud fra WooCommerce standart med nye specifikke felter 
Implementering:
Hovedklasse:
Fil: components/product-types/experiences/class-experiences.php
Beskrivelse: Definerer 'Oplevelse' som en ny produkttype og registrerer nødvendige handlinger og filtre.

4.1.1.1	Fane 1: Generelt
Denne fane indeholder grundlæggende produktinformation for hovedproduktet og ikke de specifikke dato-varianter.

Fane Generel
Navnet på feltet: General
Fanetype: WooCommerce-standardfelt general
components/product-types/experiences/general-options.php
Fanen skal yderlige indeholde følgende felter:

Kategorifarve
Feltnavn: Kategorifarve
Beskrivelse: Synkroniser den valgte Kategorifarve direkte med Microsoft-kategorier, så farven vises konsekvent i både WooCommerce og Microsoft-kalenderen. Farvevalg begrænses til de kategorifarver, der er tilgængelige på Microsoft-kalenderkontoen (kontakt@getoutdoor.dk), som vil sikre, at alle opdateringer synkroniseres korrekt.
Fil: components/integrations/microsoft-graph/helpers.php
Felttype: Dropdown med muligheder som synkroniseres med Microsoft kalenderen
Funktioner: 
•	Henter kategorier fra Microsoft Kalender ved hjælp af Microsoft Graph API.
•	Cacher kategorierne ved hjælp af WordPress Transients API.

4.1.1.2	Fane 2: Dato og Tid
(Unikke Datoer som Varianter)
Opret dato-varianter, så de fungerer og vises som standard WooCommerce-varianter, så opsætningen er intuitiv og genkendelig for brugeren 
Ny fane Vælg dato/tid
Fanenavn: Vælg dato/tid
Felttype: Variant-dropdown-menu med WooCommerce-standardfelter
Fil: components/product-types/experiences/date-options.php

Fanen skal indeholde følgende felter i dropdown menuen:

Dato og Starttidspunkt 
Feltnavn: Dato og Starttidspunkt 
Beskrivelse: Brugeren indtaster dato og starttidspunkt som der er for oplevelsen tid i formatet YYYY-MM-DDTHH (f.eks. 2024-10-05T13:00). En besked vises, der angiver korrekt format.
Felttype: Tekstfelt med placeholder YYYY-MM-DDTHH:MM
Validering: Real-time validering ved hjælp af JavaScript, for at sikrer korrekt dataformat jf. Microsoft standart

Varighed
Feltnavn: 
Beskrivelse: Varighed i timer bruges til automatisk beregning af sluttidspunkt i kalenderen (decimaltal for præcision, f.eks. 1,5 for 1,5 timer).
Type: Numerisk felt
Validering: Tilføj automatisk udregning af slutdato/tid til validering af datoformatet (YYYY-MM-DDTHH), brugeren skal have en fejlbesked ved forkert udregning.

Dato/Variant SKU
Feltnavn: Dato/Variant SKU
Beskrivelse: Hver dato har en separat variant under hovedproduktet. Et unikt SKU for hver dato/variant, som bruges til kalenderintegration for at undgå dobbeltoprettelse af begivenheder.
Type: Tekstfelt WooCommerce standart 

Pris
Feltnavn: Pris
Beskrivelse: Hver dato har en separat pris mulighed som standart opsætning
Type: Tekstfelt WooCommerce standart

Bemærkninger
Feltnavn: Bemærkninger
Beskrivelse: Felt til ekstra oplysninger om specifikke datoer, f.eks. særlige instruktioner eller praktiske informationer.
Type: Tekstfelt WooCommerce standart

Lagerstyring pr. Dato/Variant
Feltnavn: Lagerstyring
Beskrivelse: Lagerstyres på variantniveau, så hver dato kan have et fast antal pladser, og kunderne kan se, hvor mange ledige pladser der er tilbage. Når en dato er fuldt booket, fjernes den automatisk fra produktsiden.
Felttyper:
Checkboks WooCommerce lagerstyring aktivering
Number WooCommerce lagerstyring Antal på lager
Functioner: Vi hvor mange placer der er tilbage på frontend 
Afhængighed: frontend.js      

Specifikke fjernede felter for produkttypen oplevelse
Når produkttypen “Oplevelse” er valgt, fjernes følgende WooCommerce-standardfelter automatisk:
•	Forsendelses- og Vægtfelter: Fjernes, da de ikke er relevante for oplevelsesprodukter.
•	Lagerstyring for Hovedprodukt: Fjernes, da lagerstyring sker på variantniveau og ikke på hovedproduktniveau.
•	Egenskaber: Fjernes for at holde oplevelsesproduktet enkelt og præcist, variant skal stadig virke uden


4.1.1.3	Fane 3: Tilvalg
Denne fane giver mulighed for at tilføje ekstraudstyr og størrelsesmuligheder til oplevelsen, som kan tilpasses efter behov. Det skal være nemt at booke til flere end 1 person og have mulighed for at vælge individuelle størrelser og udstyr pr. person.
Fil: components/product-types/experiences/tilvalg-options.php

Ny fane – Tilvalg
Fanenavn: Tilvalg
Type: WooCommerce-standardfelt (Start uden indhold)

Fanen skal indeholde følgende felter:
Ekstraudstyr
Feltnavn: Ekstraudstyr
Beskrivelse: Tilføj ekstraudstyr, brugeren definere og tilføjer ekstraudstyrsmuligheder via afkrydsning for standardvalgene (våddragt, tørdragt, MTB), brugeren kan også selvskrive inviduelle muligheder. Alle ekstraudstyr skal have et unikt SKU tilknyttet til lager styring der vises som visuelt uden begrænsninger eller styring. 

Felttyper: 
•	Checkbokse med standart ekstraudstyr
•	Tekstboks for nye muligheder, på linjer under checkboksen, initieres med et lille plustegn
•	tekstboks ud fra hver mulighed hvor brugere kan skriv unikt SKU ud fra hver mulighed. Ikke redigerebar start på sku skal være hovedproduktets SKU og dertil tillægges navnet på ekstraudstyret.

Validering: Ingen 
Admin Notices: Hovereffekt over plustegnet med forklarende tekst 
Standart ekstraudstyr: Våddragt, tørdragt, MTB

Størrelser
Feltnavn: Størrelser
Felttyper: 
•	Checkboks Aktiveret og synlig ved afkrydsning
•	Tekstfelt med checkbokse på siden under checkboksen bliver synlig ved tryk på et lille ikon plus knap
Muligheder: Standart er S, M, L, XL, XXL, XXXL, med mulighed for individuelle muligheder på produkt niveau, der etableres et ikon som plustegn som opretter af ny linje med tekstfelt under standarterne og checkbokse på siden. 
Beskrivelse: Brugeren vælger, hvilke muligheder der skal være synlige/tilgængelige ved at krydse af i checkboksene.

Individuelle valg pr. deltager
Beskrivelse: Tillad kunderne at vælge ekstraudstyr og størrelser individuelt for hver deltager på frontend. Brug en logisk tilgang til opsætningen på frontend. Valg gemmes i meta og skal være synligt hele vejen igennem købsprocessen, sådan at det komme med på ordren og bliver overført til den rigtige kalender. 

Lagstyring for tilvalg:
Beskrivelse: Lagerstyring for tilvalg skal i første omgang tilføjes som en visuel visning.
Tilføj en tæller eller indikator for bestilte tilvalg på siden med lagerinformation.

Fremtidige Planer: integration med Microsoft lagerstyring er planlagt for fremtiden.


4.1.2	Udlejning – produkttype
Denne type skal i første omgang bruges til kajak og mountainbike 

Formål: At håndtere udlejning af udstyr som en separat produkttype i WooCommerce, med specifikke felter og funktioner tilpasset udlejningsprocessen.
Hovedklasse:
Fil: components/product-types/rentals/class-rentals.php
Beskrivelse: Definerer 'Udlejning' som en ny produkttype og registrerer nødvendige handlinger og filtre.
4.1.3	Fane 1: Generelt
Beskrivelse: Indeholder grundlæggende produktinformation for udlejningsprodukter, såsom pris pr. dag, depositum og udlejningsbetingelser.
Felter i Generelt-fanen:
Pris pr. dag
Type: Numerisk felt
Beskrivelse: Angiver prisen for én dags udlejning.

Udlejningsbetingelser
Type: Tekstområde
Beskrivelse: Beskriver betingelserne for udlejningen.

4.1.4	Fane 2: Tilgængelighed
Beskrivelse: Håndterer tilgængelighedskalender, hvor administrator kan angive tilgængelige og ikke-tilgængelige datoer for udlejningsproduktets date picker og lokations muligheder der vises på frontend.
Fil: components/product-types/rentals/availability-options.php
Functioner: 
Mulighed for at brugeren kan skrive ind hvilke tidspunkter man kan leje i - tilgængelighedskalender
Muligheden for at angive åbne dage og timer.
Mulighed for at skrive lokations muligheder ind via tekstfelt 


4.1.5	Fane 3: Udlejningsindstillinger
Beskrivelse: Indeholder felter for udlejningsvarighed, minimum og maksimum lejeperiode samt eventuelle ekstra gebyrer.
Fil: components/product-types/rentals/rental-options.php
Functioner: tilbyd faste lejeperioder 3 timer og 1 dag
Bemærkning: Gør det nemt for administratoren at konfigurere tilgængelige perioder.
4.1.6	Frontend
Beskrivelse: Kunden skal have mulighed for en date picker der skal være koblet op med datafeltet udlejning, og Meta beskrivelse i kurven samt til overførelse og oprettelse af dato i Outlook kalender når en ordre opdateres til gennemførts. Datepinceren skal være ved prisen på frontend. Mulige datoer til date pickeren skal indstilles under fane 2: tilgængelighed
Fil: assets/js/frontend.js 
Date Picker: Flatpickr bruges som date picker-bibliotek.


Felt med et dropdown-felt for lokationer. Brug WooCommerce hooks til at tilføje feltet på produktsiden.
Fil: components/product-types/experiences/templates/single-experiences.php

4.1.7	Lokationsvalg
•	Tilføj et dropdown-felt for lokation.
•	Implementering:
o	Brug WooCommerce hooks til at tilføje feltet på produktsiden.


4.1.8	Specifikke fjernede felter for produkttypen Udlejning
•	Forsendelsesindstillinger fjernes, da udlejning typisk sker ved afhentning eller levering på anden vis.

4.1.9	Tilpasning af Ordrebekræftelse
Ordrebekræftelse skal sikre at kunden for alt den nødvendige information. Den skal se godt og virke professionel
Fil: components/integrations/woocommerce/email-customization.php
Interagerer med: order-confirmation.php samt i templates/emails/
Beskrivelse: Tilpasser ordrebekræftelses-e-mailen til at inkludere detaljer om tilvalg og individuelle valg pr. deltager.
E-mail Skabelon:
Fil: templates/emails/order-confirmation.php
Bruger: WooCommerce's e-mail override funktionalitet til at erstatte standard e-mail skabeloner.
Funktioner: skal nemt kunne aktiveres og deaktiveres 
4.1.10	WooCommerce - Komponent Logging 
Formål:
At overvåge og registrere alle hændelser relateret til WooCommerce-integration, herunder produktoprettelse, ordrebehandling og lagerstyring.
Fil: utilities/logger.php 

Produktoprettelse og -opdatering:
Log, når et nyt "Oplevelse" eller "Udlejning" produkt oprettes eller opdateres.
Medtag produkt-ID, navn og ændrede felter.
Hook: save_post
Beskrivelse: Logger, når et produkt gemmes eller opdateres.
Eksempel:
1. $logger->info('Oplevelse produkt opdateret.', [
2.     'produkt_id' => $product_id,
3.     'ændringer' => $ændringer_foretaget,
4.     'bruger_id' => get_current_user_id(),
5. ]);
6.  
Ordrebehandling:
Log, når en ordre gennemføres, herunder ordre-ID, kunde-ID og ordrebeløb.
Ved fejl under ordrebehandling, log fejlbesked og kontekst.
Hook: woocommerce_order_status_completed
Beskrivelse: Logger, når en ordre ændrer status til gennemført.

Eksempel:
1. php
2. $logger->info('Ordre gennemført.', [
3.     'ordre_id' => $order_id,
4.     'kunde_id' => $customer_id,
5.     'beløb' => $order_total,
6. ]);
7.  
Lagerstyring:
Log ændringer i lagerbeholdning for produkter og varianter.
Ved lav lagerbeholdning, log en advarsel.
Eksempel:
1. $logger->warning('Lager lavt for variant.', [
2.     'variant_id' => $variant_id,
3.     'resterende_lager' => $stock_quantity,
4. ]); 
5.  

Validering af Brugerinput:
Log valideringsfejl ved oprettelse eller opdatering af produkter.
Medtag detaljer om fejlede felter og forventede værdier.
Hook: woocommerce_reduce_order_stock
Beskrivelse: Logger ændringer i lagerbeholdning, når en ordre er placeret.

Eksempel: (kan laves med $product_id,)
1. $logger->error('Fejl ved behandling af ordre.', [
2.     'ordre_id' => $order_id,
3.     'fejlbesked' => $e->getMessage(),
4. ]);
5.  
4.1.11	General fjernelse af Irrelevante Felter:
Fil: components/product-types/experiences/remove-fields.php
Beskrivelse: Fjerner felter som forsendelses- og vægtfelter fra produktredigeringssiden ved hjælp af filtre som woocommerce_product_data_tabs og woocommerce_product_data_panels.

4.1.12	Sikkerhed og Validering
Input Validering:
Fil: utilities/validation.php
Beskrivelse: Indeholder funktioner til at validere brugerinput, såsom datoformater og numeriske værdier.
Sikker Datahåndtering:
Kundedata behandles i overensstemmelse med GDPR.
Data sanitiseres før lagring og visning.
Adgangskontrol:
Kun autoriserede brugere kan oprette eller redigere produkter.
Roller og kapabiliteter håndteres via WordPress' indbyggede system.

4.1.13	Caching
Cacher statiske data som produktkategorier og ekstraudstyr for at forbedre ydeevnen.
Fil: Anvender WordPress Transients API i relevante filer, såsom components/product-types/experiences/helpers.php
Cache Invalidering: Når data ændres, slettes relevante transients ved hjælp af handlinger som save_post og woocommerce_update_product.


4.1.14	Lageroversigt for Produkter og Tilvalg:
Formål: At give administratoren en centraliseret oversigt over lagerstatus for alle produkter og tilvalg.
Fil: components/admin-interface/stock-overview.php
Funktioner:
Henter lagerdata for alle produkter og tilvalg ved hjælp af WooCommerce's standardfunktioner.
Viser dataene i en overskuelig tabel med kolonner for produktnavn, SKU, lagerantal, og mulighed for at redigere lager direkte.
Inkluderer søge- og filtreringsfunktioner for nem navigation.

________________________________________

4.2	Microsoft
Formål: At integrere pluginet med Microsoft Graph API for at automatisere kalendersynkronisering baseret på produkt- og ordredata. Der skal også sendes data for kategorivalg til produkttype oplevelse. 

(integrationen med Microsoft Graph API sker via wp_remote_post
Fil: components/integrations/microsoft-graph/api.php)
4.2.1	Microsoft opsætning´s-siden
En særskilt menu bliver oprettes under WordPress for at konfigurere Microsoft API-indstillinger (se under WordPressmenuen)
Fil: components/admin-interface/microsoft-setup.php

4.2.1.1	Felter og Funktioner

Type: 3 Tekstfelter til Client ID, Client Secret og Tenant ID
Beskrivelse: Brugeren indtaster Microsoft API Client ID, Client Secret og Tenant ID. Efter første opsætning skjules disse felter for at beskytte informationen.

Log ind med Microsoft
Type: Knappen aktiverer OAuth 2.0 autorisation
Fil: components/integrations/microsoft-graph/oauth.php
Funktion: Brugeren klikker på knappen for at logge ind og oprette forbindelse til Microsoft Graph API.

Tokenopsætning
Systemet skal kunne opdatere tokenet automatisk, så brugeren undgår at logge ind gentagne gange.

Genstart af Autorisation
En knap til at nulstille cache og genstarte autorisationen, hvis forbindelsen fejler.

Microsoft Opsætning - Test af API-forbindelse
Tilføj en nulstillingsknap for at starte forfra med autorisationen.

Input Validering og Sanitisation:
Alle input felter valideres og sanitiseres.
Nonce-verifikation for formularindsendelser.

________________________________________

4.2.2	Automatisk Kalendersynkronisering
4.2.2.1	Kalendersynkronisering ved Produktopdatering
Fil: components/integrations/microsoft-graph/calendar-sync.php
Hooker ind i save_post for at trigge synkronisering
Ved oprettelse eller opdatering af oplevelsesproduktet overføres de specifikke dato-varianter (med SKU) til Microsoft-kalenderen som begivenheder samt kategori til begivenheden.
Pluginet tjekker via SKU, om en begivenhed allerede eksisterer i kalenderen for at undgå dublering af begivenheder.
Kalenderen opretter automatisk nye begivenheder ved tilføjelse af nye datoer og opdaterer eksisterende begivenheder, hvis datoerne rettes. Dette sikrer, at kalenderen altid viser de korrekte datoer uden ekstra manuelt arbejde.
Tilføj en indstilling pr. dato/variant for at aktivere eller deaktivere kalendersynkronisering. Et enkelt afkrydsningsfelt vil give brugeren mulighed for at bestemme, hvilke datoer der skal synkroniseres.
4.2.2.2	Kalendersynkronisering ved Ordreafslutning
Fil: components/integrations/woocommerce/order-processing.php
Hooker ind i woocommerce_order_status_completed for at trigge opdatering.
Når en ordre afsluttes og bliver sat som gennemført, opdateres den tilknyttede kalenderbegivenhed med kundeoplysninger.
Information som navn, telefonnummer, ordrenummer, antal personer samt eventuelle tilvalg og størrelser tilføjes som tekst i kalenderbegivenhedens noter.
Hver ordre tilføjes som en ny linje i kalendernoterne med detaljerede oplysninger om kunden og ordren.
Der skal være en totalt antal deltagere i toppen 
Layout skal have fokus på at man hurtigt danner sig et overblik
Oplysninger tilføjes løbende uden at overskrive eksisterende data.
________________________________________


4.2.3	Microsoft opsætningsvejledninger
4.2.3.1	Brugerbeskrivelse: 
Opsætning af Microsoft OAuth-forbindelse
**Formål:**
Opsætningen gør det muligt for pluginet at oprette forbindelse til Microsoft via OAuth 2.0, så du kan tilgå Microsoft API'er (f.eks. Microsoft Graph). Du skal kun gøre dette én gang. Når opsætningen er afsluttet, vil pluginet automatisk bruge de gemte oplysninger.

(Flere Redirect URI'er kan tilføjes for både staging og produktionsmiljøer.
Implementering:
Tilføj både staging og produktions-URLs i Azure Portal.
Sørg for, at pluginet kan håndtere forskellige miljøer ved at detektere sitets URL.)

**Trin-for-trin opsætning:**
#### Trin 1: Opret en applikation i Azure Portal
1.1 **Gå til Azure Portal**
   - Besøg [Azure Portal](https://portal.azure.com/).
   - Log ind med din Microsoft-konto.
1.2 **Opret en ny applikation**
   - I menuen til venstre, vælg **"Azure Active Directory"**.
   - Klik på **"App registrations"** under **"Manage"**.
   - Klik på **"New registration"** for at oprette en ny applikation.
1.3 **Indtast applikationens oplysninger:**
   - **Name**: Giv applikationen et navn (f.eks. "Mit Plugin").
   - **Supported account types**: Vælg "Accounts in this organizational directory only (Single tenant)".
   - **Redirect URI**: For din opsætning, skal du vælge **"Web"** og indtaste en URI som f.eks.:
     ```
     https://yourdomain.com/wp-admin/admin.php?page=plugin-settings
     ```
   - Klik på **"Register"** for at oprette applikationen.

#### Trin 2: Find de nødvendige oplysninger i Azure Portal
2.1 **Client ID:**
   - Efter oprettelsen af applikationen, vil du blive omdirigeret til applikationens "Overview"-side.
   - Kopiér **Application (client) ID**, som du vil bruge som **Client ID** i opsætningen i pluginet.
   - Delegated Permissions: Calendars.ReadWrite User.Read


2.2 **Client Secret:**
   - I venstre menu, vælg **"Certificates & secrets"**.
   - Klik på **"New client secret"** under sektionen **"Client secrets"**.
   - Tilføj en beskrivelse (f.eks. "Mit plugin secret").
   - Vælg en udløbstid (vælg gerne 1 år eller længere).
   - Klik på **"Add"**.
   - Kopiér den genererede **Client Secret** og gem den et sikkert sted. Du vil bruge den til at konfigurere dit plugin.

3. **Tenant ID:**
   - I venstre menu under **"Azure Active Directory"**, vælg **"Properties"**.
   - Kopiér **Directory (tenant) ID**, som du vil bruge som **Tenant ID** i opsætningen i pluginet.
#### Trin 3: Gå tilbage til din WordPress Admin og konfigurér pluginet
3.1 **Naviger til Microsoft Opsætning i Plugin-menuen**
   - I din WordPress admin, gå til plugin-menuen, som typisk vil hedde noget som **"Microsoft Integration"** eller **"Plugin Settings"**.
   - Vælg **"Microsoft OAuth Settings"**.

3.2 **Indtast oplysningerne i pluginet:**
   - **Client ID**: Indtast den **Client ID**, du kopierede fra Azure Portal.
   - **Client Secret**: Indtast den **Client Secret**, du kopierede fra Azure Portal.
   - **Tenant ID**: Indtast den **Tenant ID**, du kopierede fra Azure Portal.
   - **Redirect URI**: Denne URI skal være foruddefineret i pluginet og vil normalt være:
     ```https://yourdomain.com/wp-admin/admin.php?page=plugin-settings ```

3.3 **Klik på “Log ind med Microsoft”**
   - Når du har indtastet de nødvendige oplysninger, klik på **"Log ind med Microsoft"**. Dette åbner en Microsoft-login-side.

4. **Login med din Microsoft-konto**  
   - Du vil blive bedt om at logge ind med din Microsoft-konto og give de nødvendige tilladelser til pluginet for at få adgang til de relevante Microsoft-tjenester (f.eks. Microsoft Graph API).

5. **Bekræft og gem opsætningen**
   - Når du er logget ind og har accepteret tilladelserne, bliver du automatisk omdirigeret tilbage til WordPress admin.
   - Klik på **"Gem"**, og dine oplysninger vil blive gemt sikkert i pluginets `.env`-fil, og du er klar til at bruge Microsoft-integrationerne i dit plugin.

#### Trin 4: Opsætningen er færdig
- Du har nu konfigureret OAuth-forbindelsen én gang, og pluginet vil automatisk bruge de gemte oplysninger til at oprette forbindelse til Microsoft hver gang. Du behøver ikke at gøre noget yderligere.

4.2.3.2	Teknisk Funktionsbeskrivelse: 
Microsoft OAuth 2.0 Opsætning med .env og wp-config.php
**Formål:**
Opsætningen giver pluginet mulighed for at autentificere brugeren via OAuth 2.0, gemme API-nøglerne sikkert i en `.env`-fil og som backup i `wp-config.php`. Brugeren skal kun konfigurere forbindelsen én gang, og pluginet håndterer resten.
**Detaljeret Implementering:**
1. **Indtastning af Microsoft OAuth-credentials:**
   - **Client ID**, **Client Secret**, **Tenant ID** og **Redirect URI** gemmes i `.env`-filen, som er en sikker måde at håndtere følsomme data på. Hvis `.env` ikke er tilgængelig (f.eks. ved udvikling på lokale servere), kan vi vælge at gemme dem i `wp-config.php` som backup.

2. **Generering af .env-fil:**
   - `.env`-filen placeres i pluginets rodmappe og indeholder de nødvendige credentials:
     ```
     MICROSOFT_CLIENT_ID=your_client_id
     MICROSOFT_CLIENT_SECRET=your_client_secret
     MICROSOFT_TENANT_ID=your_tenant_id
     MICROSOFT_REDIRECT_URI=your_redirect_uri
     ```

3. **Backup i wp-config.php:**
   - Hvis `.env`-filen ikke er tilgængelig (fx på et shared hosting-miljø uden understøttelse for `.env`-filer), kan vi bruge `wp-config.php` til at gemme credentials som backup:
     ```php
     define('MICROSOFT_CLIENT_ID', 'your_client_id');
     define('MICROSOFT_CLIENT_SECRET', 'your_client_secret');
     define('MICROSOFT_TENANT_ID', 'your_tenant_id');
     define('MICROSOFT_REDIRECT_URI', 'your_redirect_uri');
     ```

4. **OAuth Flow:**
   - Når brugeren klikker på “Log ind med Microsoft”-knappen, oprettes der en OAuth 2.0-anmodning, som videresender brugeren til Microsofts login-side, hvor de skal godkende tilladelserne:
```https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/authorize?client_id={client_id}&redirect_uri={redirect_uri}&response_type=code&scope=offline_access     ```
   - Når brugeren godkender login, returneres en autorisationskode, som bruges til at få et **access token** og **refresh token** fra Microsofts token-endpoint:
     ```     https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token     ```

5. **Token Opbevaring:**
   - **Access Token** bruges til at sende API-anmodninger til Microsoft.
   - **Refresh Token** bruges til at opdatere access token, når det udløber. Begge tokens opbevares sikkert i pluginet, og de bruges til autentificering af efterfølgende API-anmodninger.
   
   Tokens gemmes:
   - I en session (for midlertidig brug) eller i pluginets database (for permanent opbevaring).

6. **Fejlhåndtering:**
   - Hvis der opstår fejl i autentificeringen eller OAuth-flowet (f.eks. forkert Client Secret), viser pluginet en fejlmeddelelse til brugeren. Dette hjælper med at diagnosticere, hvad der gik galt og muliggør hurtig reparation af eventuelle fejl.

7. **Automatisk Brug af Credentials:**
   - Når opsætningen er gennemført én gang, anvender pluginet automatisk de gemte oplysninger fra `.env` eller `wp-config.php`, og brugeren slipper for at indtaste dem igen.

8. **Sikkerhed:**
   - Alle følsomme oplysninger gemmes i en `.env`-fil, som er uden for webroot og dermed ikke tilgængelig for offentligheden. Hvis `.env`-filen ikke er tilgængelig, gemmes de som backup i `wp-config.php`.

**Med denne tilgang er opsætningen nem for brugeren, samtidig med at den tekniske funktionalitet sikrer, at oplysningerne gemmes sikkert og anvendes korrekt.**



Pluginet sikrer automatisk synkronisering af dato-varianter og kundeoplysninger med Microsoft-kalenderen.

4.2.4	Log (Specifikke Komponenter)
4.2.4.1	Microsoft API-logning
Fil: utilities/logger.php
•	Registrerer alle Microsoft API-kald for at sikre sporbarhed og muliggøre fejlfinding. API-interaktioner logges i integrations/microsoft-graph/api.php, og log-kald foretages gennem log_event() i utilities/logger.php.
•	Logposter gemmer detaljer som API-endpoint, HTTP-metode, statuskode og fejlmeddelelser. Følsomme oplysninger som tokens maskeres i loggen, så dataene forbliver sikre. (Se Microsoft API-integration for detaljer om specifikke API-kald og deres betydning for systemets funktionalitet.)
log-kald på: Autentificering og OAuth API-Kald, Tokenhåndtering, Kalenderbegivenheder, Fejlhåndtering.

4.2.4.2	Fejlregistrering og Debugging
•	Fejlmeddelelser logges og vises direkte i admin-dashboardet for hurtig adgang. try-catch-blokke omkring kritiske API-kald i api.php opfanger fejl, som derefter gemmes i plugin-log.txt.
•	I admin-interface/settings.php kan administratorer se og reagere på fejlmeddelelser, hvilket gør det nemt at spore problemer. (Se fejlhåndteringsprotokoller i afsnittet om systemovervågning for at sikre konsistens i opsætning af fejlmeddelelser.)

4.2.5	Sikkerhed (Specifikke Komponenter)
Interagerer med: components/integrations/microsoft-graph/token-handler.php, utilities/validation.php
4.2.5.1	Token Management
Fil: components/integrations/microsoft-graph/token-handler.php
"For detaljer om sikkerhed i tokenhåndtering, se afsnit 2.3.1 Token Håndtering."
4.2.5.2	Beskyttelse af Følsomme Data:
”For detaljer om beskyttelse af følsomme data, se afsnit under pkt. 2 sikkerhed, underafsnit med Adgangskontrol og Autorisation."
4.2.5.3	Fejlhåndtering:
Fejlmeddelelser til brugere er generiske for at undgå informationslækage.
Detaljerede fejl logges i logfilen.

4.2.6	Caching (Specifikke Komponenter)
4.2.6.1	API Cache Implementering
Fil: components/integrations/microsoft-graph/helpers.php
For at reducere API-kald og forbedre ydeevnen caches responser separat for forskellige typer data som datoer og statiske produktoplysninger, hvilket sikrer optimal performance og opdateret data. (Se afsnittet om API-ydeevne for detaljer om krav til API-kald og cachingstrategier.)
4.2.6.2	Automatisk Cache Rensning
Ved ændringer i pluginet, såsom produkt- eller datoændringer, slettes kun relevante delete_transient().i stedet for hele cachen. Dette sikrer opdaterede oplysninger uden unødvendig belastning på systemet. (Se afsnittet om datahåndtering for yderligere detaljer om, hvordan cacheopdatering påvirker brugervenligheden.)
Ydeevneoptimering: Reducerer antallet af API-kald ved kun at hente data, når cachen er udløbet.

4.2.7	Brugervenlighed (Specifikke Komponenter)
4.2.7.1	Dynamiske Vejledninger
Til komplekse opsætninger som Microsoft-integration tilføjes trin-for-trin-vejledninger i form af popup-hints. Vejledningen implementeres som JavaScript-modaler, der dukker op, når brugeren udfylder relevante felter i microsoft-setup.php. (Se afsnittet om brugergrænseflade og UX for overordnede vejledningsprincipper.)
4.2.7.2	Forudfyldte Felter og Drop-downs
Kategorifeltet henter muligheder direkte fra kalenderens oprettede kategorier og vises som en drop-down-menu. Dette reducerer brugerens behov for at gentage indtastninger og sikrer konsistens i kategorivalg. (Se afsnittet om datakilder for en oversigt over data, der bruges til drop-down menuer.)
4.2.7.3	Fejlbeskeder i Real-time
Valideringsfejl vises i real-time som brugeren udfylder felterne i opsætningsformularen. Ved fejl markeres feltet rødt, og en besked forklarer fejlen og angiver, hvordan den kan rettes. Real-time feedback minimerer brugerens frustration ved opsætningen. (Se sektionen om brugerfeedback for detaljer om real-time validering.)

4.2.8	Microsoft komponent logging
Formål:
At sikre fuld sporbarhed af alle handlinger relateret til Microsoft Graph API-integration, herunder autentificering, API-kald og fejlbehandling.
Implementering:
•	Autentificering og OAuth:
o	Log succesfuld og fejlslagen autentificering med Microsoft.
o	Medtag bruger-ID og tidspunkter.
o	Eksempel:
1. php
2. $logger->info('Succesfuld Microsoft OAuth-autentificering.', [
3.     'bruger_id' => get_current_user_id(),
4. ]);
5.  
API-Kald:
Log alle kald til Microsoft Graph API med endpoint, HTTP-metode, responsstatus og tid brugt.
Ved fejl, log fejlmeddelelse og eventuelle fejlresponser.
Eksempel:
1. php
2. $logger->debug('Microsoft Graph API kald.', [
3.     'endpoint' => $endpoint,
4.     'metode' => $http_method,
5.     'status' => $response_status,
6.     'tid_ms' => $response_time,
7. ]);
8.  
Tokenhåndtering:
Log opdateringer og fornyelser af access tokens.
Ved fejl under tokenfornyelse, log detaljer og udløs en advarsel til administrator.
Eksempel:
1. php
2. $logger->warning('Access token fornyelse fejlede.', [
3.     'fejl' => $error_message,
4. ]);
5.  
Kalendersynkronisering:
Log, når kalenderbegivenheder oprettes, opdateres eller slettes.
Medtag relevante data som begivenheds-ID, produkt-ID og datoer.
Eksempel:
1. php
2. $logger->info('Kalenderbegivenhed oprettet.', [
3.     'begivenhed_id' => $event_id,
4.     'produkt_id' => $product_id,
5.     'starttidspunkt' => $start_time,
6. ]);
7.  
Fejlhåndtering:
Alle fejl fra Microsoft API skal logges med detaljeret kontekst.
Overvej at implementere retry-mekanismer for transient errors og log disse forsøg.
Integration med Andre Systemer
Webhook Håndtering:
Hvis pluginet lytter til webhooks fra Microsoft eller andre tjenester, skal alle indgående anmodninger logges.
Valider anmodningernes oprindelse og log resultatet af denne validering.
Tredjeparts Biblioteker:
Log versioner og eventuelle fejl fra tredjeparts biblioteker, der anvendes.
Dette hjælper med at identificere problemer, der skyldes eksterne afhængigheder.

5	Mappestruktur og filer
5.1	Visuelt mappetræ
Komponenter Organiseret efter Funktionelle Domæner
administration/
 ├── administration.php                 # Hovedfilen for pluginet
 ├── composer.json                      # Composer afhængigheder
 ├── composer.lock                      # Låste versioner af afhængigheder
 ├── vendor/                            # Composer genererede filer og biblioteker
 ├── readme.txt                         # Dokumentation og installationsvejledning
 ├── assets/                            # Statiske filer: CSS, JS, billeder
 │   ├── css/
 │   │   ├── admin.css                  # Stylesheet til admin-grænsefladen
 │   │   └── frontend.css               # Stylesheet til frontend
 │   ├── js/
 │   │   ├── admin.js                   # JavaScript til admin-funktionalitet
 │   │   └── frontend.js                # JavaScript til frontend-funktionalitet
 │   └── images/
 │       └── icons/                     # Ikoner brugt i pluginet
 ├── components/
 │   ├── admin-interface/               # Admin-grænseflade og indstillinger
 │   │   ├── menu.php                   # Oprettelse af plugin-menuer
 │   │   ├── settings.php               # Indstillingssider for pluginet
 │   │   ├── microsoft-setup.php        # Opsætning af Microsoft API
 │   │   ├── year-wheel.php             # 'Årshjulet' funktionalitet
 │   │   ├── stock-overview.php         # Lageroversigt for produkter og tilvalg
 │   │   ├── assets/
 │   │   │   ├── css/
 │   │   │   │   ├── admin-interface.css # Styles til admin-grænsefladen
 │   │   │   │   └── stock-overview.css # Styles til lageroversigten
 │   │   │   └── js/
 │   │   │       ├── admin-interface.js # Scripts til admin-grænsefladen
 │   │   │       └── stock-overview.js  # Scripts til lageroversigten
 │   │   └── templates/
 │   │       ├── settings-page.php      # Skabelon til indstillingsside
 │   │       ├── microsoft-setup-page.php # Skabelon til Microsoft opsætningsside
 │   │       ├── year-wheel-page.php    # Skabelon til 'Årshjulet' side
 │   │       └── stock-overview-page.php # Skabelon til lageroversigten
 │   ├── ai-assistance/                 # AI-funktionalitet
 │   │   ├── ai-engine.php              # AI-relaterede funktioner
 │   │   └── templates/
 │   │       └── ai-interface.php       # Skabelon til AI-grænseflade
 │   ├── integrations/                  # Integrationer med eksterne systemer
 │   │   ├── microsoft-graph/           # Microsoft Graph API integration
 │   │   │   ├── api.php                # API-kald til Microsoft Graph
 │   │   │   ├── oauth.php              # OAuth-autentificering
 │   │   │   ├── token-handler.php      # Tokenhåndtering og fornyelse
 │   │   │   ├── calendar-sync.php      # Kalendersynkroniseringsfunktioner
 │   │   │   └── helpers.php            # Hjælpefunktioner til Microsoft-integration
 │   │   └── woocommerce/               # WooCommerce-specifikke tilpasninger
 │   │       ├── hooks.php              # Custom hooks til WooCommerce
 │   │       ├── filters.php            # Filtre til WooCommerce
 │   │       ├── order-processing.php   # Ordrebehandling og kalenderopdatering
 │   │       └── email-customization.php # Tilpasning af WooCommerce e-mails
 │   ├── product-types/                 # Håndtering af produkttyper i WooCommerce
 │   │   ├── class-product-type.php     # Baseklasse for produkttyper
 │   │   ├── experiences/               # 'Oplevelse' produkttypen
 │   │   │   ├── class-experiences.php  # Hovedklasse for 'Oplevelse' produkttypen
 │   │   │   ├── general-options.php    # Generelle indstillinger
 │   │   │   ├── date-options.php       # Dato og tid felter
 │   │   │   ├── tilvalg-options.php    # 'Tilvalg' fanen og felter
 │   │   │   ├── remove-fields.php      # Fjernelse af unødvendige felter
 │   │   │   ├── stock-management.php   # Lagerstyring pr. variant
 │   │   │   ├── validation.php         # Valideringsfunktioner
 │   │   │   └── templates/
 │   │   │       ├── single-experiences.php # Skabelon til visning på frontend
 │   │   │       └── tilvalg-fields.php # Skabelon til 'Tilvalg' på frontend
 │   │   └── rentals/                   # 'Udlejning' produkttypen
 │   │       ├── class-rentals.php      # Hovedklasse for 'Udlejning' produkttypen
 │   │       ├── rental-general-options.php    # Generelle indstillinger
 │   │       ├── availability-options.php # Tilgængelighedsfelter
 │   │       ├── rental-options.php     # Udlejningsindstillinger
 │   │       ├── validation.php         # Valideringsfunktioner
 │   │       └── templates/
 │   └── utilities/                     # Hjælpefunktioner og validering
 │       ├── helpers.php                # Generelle hjælpefunktioner
 │       ├── date-helper.php            # Hjælpefunktioner til datoer
 │       ├── string-helper.php          # Hjælpefunktioner til strenge
 │       ├── validation.php             # Valideringsfunktioner
 │       └── logger.php                 # Central logging-klasse
 ├── includes/                          # Aktivering, deaktivering og afinstallation
 │   ├── activation.php                 # Håndtering ved aktivering
 │   ├── deactivation.php               # Håndtering ved deaktivering
 │   ├── uninstall.php                  # Håndtering ved afinstallation
 │   └── database.php                   # Databasehåndtering og tabeller
 ├── languages/                         # Sprogfiler til oversættelser
 │   ├── administration-en_US.mo
 │   └── administration-da_DK.mo
 ├── logs/                              # Fejl- og aktivitetslogfiler
 │   ├── log.txt                        # Central logfil for pluginet
 │   └── archive/
 │       └── log-2024-11.txt            # Arkiverede logfiler med tidsstempler
 ├── tests/                             # Testfiler
 │   ├── test-logger.php                # Test af logger-klassen
 │   ├── test-validation.php            # Test af valideringsfunktioner
 │   ├── test-stock-overview.php        # Test af lageroversigten
 └── templates/                         # Overordnede skabeloner
     ├── admin/
     │   ├── header.php                 # Header til admin-sider
     │   └── footer.php                 # Footer til admin-sider
     ├── emails/
     │   └── order-confirmation.php     # E-mail skabelon til ordrebekræftelse
     └── frontend/
         ├── header.php                 # Header til frontend-sider
         └── footer.php                 # Footer til frontend-sider

5.2	Detaljeret beskrivelse af mapper og filer
5.2.1	administration.php 
Beskrivelse: Hovedfilen for pluginet, der initialiserer alle komponenter, registrerer hooks og sikrer, at alt kører korrekt.

5.2.2	readme.txt
Indeholder dokumentation, installationsinstruktioner og andre relevante oplysninger.
Ingen justering nødvendig.
5.2.3	composer.json
Denne fil placeres i rodmappen af dit plugin, ved siden af administration.php. Den definerer dine PHP-afhængigheder.
5.2.4	composer.lock
Efter at have kørt composer install, genereres også en composer.lock fil. Denne fil låser versionerne af dine afhængigheder og bør versionkontrolleres for at sikre, at alle udviklere bruger de samme versioner.
5.2.5	vendor/
Denne mappe oprettes automatisk af Composer, når du kører composer install. Den indeholder alle de installerede afhængigheder og skal typisk ikke versionkontrolleres (tilføj vendor/ til din .gitignore, hvis du bruger Git).
5.2.6	assets/

css/admin.css:	 Stylesheets til styling af admin-grænsefladen, herunder layout og farvetemaer.
 css/frontend.css:	 Stylesheets til frontend-elementer, såsom produktvisninger og formularer.
js/admin.js: 	JavaScript-filer til dynamisk adfærd i admin-området, såsom real-time validering.
js/frontend.js: 	JavaScript til frontend-funktioner, indeholder initialisering af date picker og håndtering af brugerinteraktion på produktsiden..
images/icons/:	 Indeholder ikoner brugt i pluginet, f.eks. til menuer og knapper.

5.2.7	components/

5.2.7.1	admin-interface/

menu.php: 		Tilføjer 'Get Indoor' menuen og undermenuer i WordPress admin.
settings.php: 	Håndterer generelle indstillinger for pluginet.
microsoft-setup.php: 	Grænseflade til opsætning af Microsoft API-legitimationsoplysninger.
year-wheel.php: 	Funktionalitet for 'Årshjulet', der viser kommende oplevelser.
assets/css/admin-interface.css:	 Styles specifikt til admin-grænsefladen.
assets/js/admin-interface.js: 	JavaScript til interaktive elementer i admin-grænsefladen.
templates/settings-page.php: 		Skabelon til indstillingsside.
templates/microsoft-setup-page.php: 	Skabelon til Microsoft opsætningsside.
templates/year-wheel-page.php: 		Skabelon til 'Årshjulet' side.

5.2.7.2	ai-assistance/

ai-engine.php: 		Placeholder til fremtidige AI-funktioner.
templates/ai-interface.php: 	Skabelon til AI-grænsefladen.

5.2.8	integrations/

5.2.8.1	microsoft-graph/
api.php:		 Funktioner til at interagere med Microsoft Graph API.
oauth.php: 		Håndterer OAuth-autentificeringsflowet.
token-handler.php: 	Håndterer tokenlagring, fornyelse og udløb.
calendar-sync.php: 	Synkroniserer kalenderbegivenheder ved produktændringer.
helpers.php: 	Hjælpefunktioner til Microsoft-integration, såsom caching af kategorier.

5.2.8.2	woocommerce/

hooks.php: 		Indeholder action hooks for at udvide WooCommerce-funktionalitet.
filters.php: 	Indeholder filterfunktioner til at ændre WooCommerce's standardadfærd.
order-processing.php:	 Behandler ordrer og opdaterer kalenderbegivenheder med kundedata.
email-customization.php:	 Tilpasser WooCommerce e-mails med yderligere information.

5.2.8.3	4.2.3.4 product-types/

class-product-type.php: Baseklasse for produkttyper, som 'Oplevelse' og 'Udlejning' nedarver fra.
5.2.8.3.1	experiences/
class-experiences.php: 	Definerer 'Oplevelse' produkttypen.
general-options.php: 		Håndterer generelle indstillinger i 'Generelt' fanen.
date-options.php: 		Håndterer 'Dato og Tid' fanen.
tilvalg-options.php: 		Håndterer 'Tilvalg' fanen.
remove-fields.php: 	Fjerner unødvendige felter fra produktredigeringssiden.
stock-management.php: 	Håndterer lagerstyring for hver variant (dato) af oplevelsesprodukterne.
validation.php: 	Valideringsfunktioner for 'Oplevelse' produkttypen.
templates/single-experiences.php: Skabelon til visning af 'Oplevelse' produkter på frontend.
templates/tilvalg-fields.php: 	Skabelon til 'Tilvalg' muligheder under checkout.
5.2.8.3.2	rentals/

class-rentals.php: 		Definerer 'Udlejning' produkttypen.
rental-general-options.php: 	Håndterer generelle indstillinger.
availability-options.php: 	Håndterer tilgængelighedsfelter.
rental-options.php: 		Indeholder udlejningsspecifikke indstillinger.
validation.php: 		Valideringsfunktioner for 'Udlejning' produkttypen.

5.2.8.4	utilities/

helpers.php: 	Generelle hjælpefunktioner brugt på tværs af pluginet.
date-helper.php: 	Funktioner til datoformatering og beregninger.
string-helper.php: 	Funktioner til strengmanipulation.
validation.php:	 Centraliserede valideringsfunktioner.
logger.php: 	Central logging-klasse for pluginet.

5.2.9	includes/

activation.php: 	Håndterer nødvendige handlinger ved aktivering af pluginet.
deactivation.php:	 Rydder op ved deaktivering af pluginet.
uninstall.php: 	Fjerner alle plugin-data ved afinstallation.
database.php: 	Håndterer oprettelse og vedligeholdelse af custom databasetabeller.

5.2.10	languages/

administration-en_US.mo:	Sprogfiler til engelske oversættelser.
administration-da_DK.mo: 	Sprogfiler til danske oversættelser.

5.2.11	logs/

log.txt: 			Central logfil for alle pluginets hændelser.
archive/log-YYYY-MM.txt: 	Arkiverede logfiler med tidsstempler for bedre logstyring.

5.2.12	tests/

test-logger.php: 		Enhedstest for logger-klassen.
test-validation.php: 		Enhedstest for valideringsfunktioner.  
test-stock-overview.php 	Test af lageroversigten

5.2.13	templates/

admin/header.php & footer.php: 	Fælles header og footer til admin-sider.
emails/order-confirmation.php:	 Tilpasset e-mail skabelon til ordrebekræftelse.
frontend/header.php & footer.php: Fælles header og footer til frontend-skabeloner.

________________________________________
5.3	Test og Kvalitetssikring
Formål: At sikre, at logging-funktionaliteten fungerer korrekt, og at alle hændelser logges som forventet.
Interagerer med: tests/ (opret en testmappe), utilities/logger.php (for test af logging)

5.4	Testplan for Logging
Unit Tests:
•	Implementer enhedstest for Logger-klassen for at sikre korrekt funktionalitet.
•	Test forskellige logniveauer, format og håndtering af store mængder data.
Integration Tests:
•	Test logging i sammenhæng med hver komponent.
•	Sørg for, at logning ikke introducerer performance-problemer.
Security Tests:
•	Gennemfør sikkerhedstest for at sikre, at logfiler er beskyttet mod uautoriseret adgang.
•	Test adgangsrettigheder og .htaccess-beskyttelse.
5.5	5.2 Kvalitetssikring
Code Reviews:
•	Alle ændringer i logging-koden skal gennemgås af en anden udvikler.
•	Fokus på sikkerhed, performance og overholdelse af standarder.
Continuous Integration (CI):
•	Integrer test af logging i CI/CD-pipelinen for automatisk at opdage problemer.
5.6	Automatiseret Test
Formål: At sikre, at alle komponenter fungerer som forventet gennem automatiserede tests.

Bemærkninger:
Brug af rigtige data i et kontrolleret testmiljø
Sørg for, at test ikke påvirker produktionsdata.

Værktøjer: PHPUnit til enhedstest af PHP-kode.
QUnit eller Jest: Til JavaScript-tests.
Implementering:
Hvor: tests/ mappen.
Hvordan: Skriv testfiler som test-logger.php og test-validation.php for at teste kritiske funktioner.
Integration med VSCode: Brug extensions som "PHPUnit Test Explorer" til at køre og debugge tests direkte i VSCode.


5.7	Test af Lageroversigt:
Unit Tests:
Test, at lagerdata hentes korrekt fra databasen.
Test, at ændringer i lagerbeholdningen gemmes korrekt.
Integration Tests:
Test, at lageroversigten korrekt interagerer med WooCommerce's lagerstyringssystem.
UI Tests:
Test, at tabelvisningen fungerer korrekt i forskellige browsere og skærmstørrelser.
Test, at søge- og filtreringsfunktioner fungerer som forventet.
5.8	Continuous Integration og Deployment (CI/CD):
Planlægning:
Opsætning af CI/CD pipelines for automatiseret test og deployment.
Brug af værktøjet GitHub Actions.

5.9	Fremtidig Kodedokumentation
•	Bemærkning: "Når koden er genereret og funktionel, vil vi overveje at tilføje phpDoc kommentarer for at forbedre vedligeholdelsen og læsbarheden. Dette kan gøres i en senere fase."



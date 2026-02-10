<?php
// includes/db.php
// Dit bestand maakt een verbinding met de database via PDO.
// PDO = "PHP Data Objects". Hiermee kun je veilig SQL queries uitvoeren.
//
// Belangrijk voor studenten:
// - We gebruiken PREPARED STATEMENTS om SQL-injection te voorkomen.
// - De database-gegevens (host, dbname, user, pass) kun je hieronder aanpassen.
//
// Didactische structuur van dit bestand:
// 1) Basis instellingen (werkt in XAMPP/MAMP/WAMP)
// 2) Optioneel: overschrijven met env vars (handig voor Docker)
// 3) DSN + PDO opties bouwen
// 4) Connectie maken ($pdo)
// 5) $baseUrl bepalen voor links/redirects (portable)

// 1) Database instellingen
$dbHost = '127.0.0.1';
$dbName = 'blogproject';
$dbUser = 'root';
$dbPass = ''; // XAMPP/MAMP: vaak leeg, maar dit kan verschillen
$dbCharset = 'utf8mb4';

// Docker tip (beginner-friendly):
// Als je dit project via Docker Compose draait, zetten we env vars.
// Dan hoef je deze waarden hierboven niet handmatig aan te passen.
//
// $_ENV / getenv(): hiermee kun je "instellingen" uit de omgeving ophalen.
// Als er geen env var bestaat, gebruiken we de defaults hierboven.
//
// Env vars die we ondersteunen:
// - DB_HOST, DB_NAME, DB_USER, DB_PASS
// - (optioneel) BASE_URL voor links
$dbHost = getenv('DB_HOST') ?: $dbHost;
$dbName = getenv('DB_NAME') ?: $dbName;
$dbUser = getenv('DB_USER') ?: $dbUser;
$dbPass = getenv('DB_PASS') ?: $dbPass;

// 2) DSN = "Data Source Name" (waar is de database?)
// Hierin staat host + dbname + charset.
// Charset utf8mb4 is belangrijk voor emoji en speciale tekens.
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

// 3) PDO opties
$options = [
    // Als er iets misgaat in SQL, gooien we een exception (handig om te leren/debuggen)
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Je kunt dit in productie aanpassen naar ERRMODE_SILENT en dan zelf fouten afhandelen.

    // Resultaten ophalen als associatieve arrays: ['kolomnaam' => waarde]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Handig voor beginners, want je kunt dan makkelijk $row['title'] gebruiken in plaats van $row[0].

    // Zet echte prepared statements aan (MySQL driver)
    PDO::ATTR_EMULATE_PREPARES => false, // Dit zorgt ervoor dat PDO echte prepared statements gebruikt, wat veiliger is tegen SQL-injection. In sommige omgevingen (zoals oudere MySQL versies) kan dit problemen geven, dus je kunt dit eventueel aanpassen als je dat nodig hebt.
];

// 4) Maak de connectie
try { // We maken een PDO object aan. Hiermee kunnen we later SQL queries uitvoeren.
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options); // $pdo is nu onze database connectie. We gebruiken deze variabele in andere bestanden (bijv. posts/index.php).
} catch (PDOException $e) { // Als er iets misgaat bij het verbinden, vangen we de fout op en tonen we een bericht.
    // In een echte website zou je dit netter afhandelen.
    // Voor beginners is het handig om te zien wat er misgaat.
    // Je kunt hier ook een custom error page tonen, maar zorg dan dat je de foutmelding logt (niet op de pagina zelf!).
    // Tip: als je dit ziet, klopt vaak één van deze dingen niet:
    // - DB draait niet (MySQL service uit / Docker container down)
    // - Host/username/password is fout
    // - Database naam klopt niet of is niet geïmporteerd
    exit('Database verbinding mislukt: ' . $e->getMessage());
}

// Handige variabele voor links/redirects (beginner-vriendelijk):
// We willen overal dezelfde links gebruiken, ongeacht waar je project draait.
//
// Situaties:
// - Docker / PHP built-in server (document root = map "project"):
//   URL is bijv. http://localhost:8080/index.php  => $baseUrl = ''
// - XAMPP/MAMP (map "project" staat in htdocs):
//   URL is bijv. http://localhost/project/index.php => $baseUrl = '/project'
//
// Belangrijk: we mogen $baseUrl NIET baseren op de map van de huidige pagina
// (zoals /posts of /categories), want dan krijg je foute links zoals /posts/index.php.

// Optioneel: je kunt BASE_URL handmatig zetten via een env var.
// Handig bij bijzondere setups.
// Voorbeeld: BASE_URL=/mijnblog
$baseUrl = getenv('BASE_URL') ?: '';

if ($baseUrl === '') {
    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));

    // Auto-detect voor deze starterkit:
    // - Als je in een submap zit zoals /iets/posts/..., dan is de app-root /iets
    // - Als je in de webroot zit (Docker/built-in server), dan is de app-root ''
    $baseUrl = '';

    // We kijken naar bekende submappen in dit project.
    // LET OP: als de submap op positie 0 staat (bijv. "/posts/..."), dan is $baseUrl "".
    // Dat is CORRECT (app draait dan in de webroot), dus we moeten dat niet zien als "geen match".
    $knownSubdirs = ['/posts/', '/categories/', '/includes/']; // Voeg hier andere submappen toe als je die hebt.
    $foundKnownSubdir = false; // We zoeken naar de eerste bekende submap in het script pad.
    foreach ($knownSubdirs as $subdir) { // Voorbeeld: $subdir = "/posts/"
        $pos = strpos($scriptName, $subdir); // Voorbeeld: strpos("/project/posts/create.php", "/posts/") => 8
        if ($pos !== false) { // We hebben een bekende submap gevonden in het script pad.
            $baseUrl = substr($scriptName, 0, $pos); // Voorbeeld: substr("/project/posts/create.php", 0, 8) => "/project"
            $foundKnownSubdir = true; // We hebben een match gevonden, dus we kunnen stoppen met zoeken.
            break; // We stoppen na de eerste match, want we willen de "hoogste" submap (dichtst bij root).
        }
    }

    // Als we geen submap-match vinden, gebruiken we de map van het script.
    // Voorbeeld:
    // - /project/index.php -> dirname = /project
    // - /index.php -> dirname = /
    if ($foundKnownSubdir === false) { // Geen bekende submap gevonden, dus we baseren ons op de map van het script.
        $baseUrl = str_replace('\\', '/', (string)dirname($scriptName)); // Voorbeeld: dirname("/project/index.php") => "/project", dirname("/index.php") => "/"
    }

    // Opruimen: trailing slash eraf en '/' wordt ''
    $baseUrl = rtrim($baseUrl, '/'); // Voorbeeld: rtrim("/project/", '/') => "/project", rtrim("/", '/') => ""
    if ($baseUrl === '/') { // Als we uiteindelijk '/' overhouden, maken we er '' van, want dat is de juiste baseUrl voor de webroot.
        $baseUrl = ''; // Voorbeeld: '/' wordt '' (webroot), '/project' blijft '/project'
    }
}

// Tip voor studenten:
// - Gebruik $baseUrl altijd in links en redirects:
//   Voorbeeld (conceptueel):
//   - Als $baseUrl leeg is (Docker):   /posts/create.php
//   - Als $baseUrl '/project' is:      /project/posts/create.php
// - Zo werkt alles in Docker én in een submap (XAMPP).

// TIP: Waarom een foreign key?
// In onze database heeft een "post" een category_id.
// Dat is een verwijzing naar categories.id.
// Zo weet je: deze post hoort bij categorie X.
// Dit zorgt voor dataconsistentie (je kunt geen category_id gebruiken die niet bestaat).
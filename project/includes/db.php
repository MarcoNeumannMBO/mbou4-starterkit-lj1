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
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

    // Resultaten ophalen als associatieve arrays: ['kolomnaam' => waarde]
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Zet echte prepared statements aan (MySQL driver)
    PDO::ATTR_EMULATE_PREPARES => false,
];

// 4) Maak de connectie
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    // In een echte website zou je dit netter afhandelen.
    // Voor beginners is het handig om te zien wat er misgaat.
    //
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
    $knownSubdirs = ['/posts/', '/categories/', '/includes/'];
    $foundKnownSubdir = false;
    foreach ($knownSubdirs as $subdir) {
        $pos = strpos($scriptName, $subdir);
        if ($pos !== false) {
            $baseUrl = substr($scriptName, 0, $pos);
            $foundKnownSubdir = true;
            break;
        }
    }

    // Als we geen submap-match vinden, gebruiken we de map van het script.
    // Voorbeeld:
    // - /project/index.php -> dirname = /project
    // - /index.php -> dirname = /
    if ($foundKnownSubdir === false) {
        $baseUrl = str_replace('\\', '/', (string)dirname($scriptName));
    }

    // Opruimen: trailing slash eraf en '/' wordt ''
    $baseUrl = rtrim($baseUrl, '/');
    if ($baseUrl === '/') {
        $baseUrl = '';
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
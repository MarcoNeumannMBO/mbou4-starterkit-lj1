<?php
// posts/search_filter_sort.php
// EINDOPDRACHT (READ): Zoeken + Filteren + Sorteren op één pagina
//
// Doel voor studenten:
// - Begrijpen hoe je één SELECT query stap voor stap opbouwt.
// - Begrijpen hoe GET werkt (URL parameters) en hoe je $_GET gebruikt.
// - Leren combineren van filters met WHERE + AND.
// - Leren zoeken met LIKE (% wildcards).
// - Leren sorteren met ORDER BY (ASC/DESC).
// - Leren hoe prepared statements werken met PDO.
//
// Belangrijk:
// - We gebruiken hier alleen READ (SELECT).
// - Geen OOP, geen classes. Gewoon procedural PHP.

// Didactische structuur:
// 1) Input lezen uit $_GET (zoeken/filteren/sorteren)
// 2) Dropdown vullen (categorieën)
// 3) Whitelist toepassen voor sortering (ASC/DESC)
// 4) SQL stap-voor-stap opbouwen (WHERE + AND)
// 5) Prepared statement uitvoeren
// 6) Optioneel debuggen met debug=1

require_once __DIR__ . '/../includes/db.php';

// ================================
// 1) GET uitleg + input ophalen
// ================================
// GET:
// - Bij een GET formulier komen de waarden in de URL.
//   Voorbeeld: search_filter_sort.php?q=php&category_id=2&sort=oldest
// - In PHP lees je GET via de superglobal array: $_GET
// - Handig voor zoeken/filteren/sorteren, want je kunt de URL kopiëren en delen.

$search = trim((string)($_GET['q'] ?? ''));           // zoekterm voor titel
$selectedCategoryId = (int)($_GET['category_id'] ?? 0); // gekozen categorie id
$sort = (string)($_GET['sort'] ?? 'newest');          // newest of oldest

// Kleine tip:
// - trim() haalt spaties weg
// - (int) zorgt dat category_id echt een getal wordt

// Optionele debug flag (voor studenten):
// - Zet ?debug=1 in de URL om de query + parameters te bekijken.
$debug = (int)($_GET['debug'] ?? 0);

// ================================
// 2) Dropdown data (categorieën)
// ================================
// We hebben categorieën nodig om de dropdown te vullen.
$categoryStmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// ================================
// 3) Sortering bepalen (whitelist)
// ================================
// ORDER BY uitleg:
// - ORDER BY bepaalt de volgorde van de resultaten.
// - DESC = aflopend (nieuwste eerst bij datum)
// - ASC  = oplopend (oudste eerst bij datum)
//
// Waarom whitelist?
// - Je kunt ASC/DESC meestal niet als prepared statement parameter binden.
// - Daarom kiezen we de richting met een if.
// - We staan alleen 'newest' of 'oldest' toe.

$orderDirection = 'DESC';
$sortLabel = 'Nieuwste eerst';

if ($sort === 'oldest') {
    $orderDirection = 'ASC';
    $sortLabel = 'Oudste eerst';
} else {
    // Alles wat niet 'oldest' is, behandelen we als 'newest'
    $sort = 'newest';
}

// ================================
// 4) Query stap voor stap opbouwen
// ================================
// We willen:
// - posts + categorie naam tonen (JOIN)
// - optioneel: zoeken op titel (LIKE)
// - optioneel: filteren op categorie (WHERE)
// - sorteren op created_at

$sql = "
    SELECT
        p.id,
        p.title,
        p.content,
        p.created_at,
        c.name AS category_name
    FROM posts p
    INNER JOIN categories c ON p.category_id = c.id
";

// WHERE uitleg:
// - WHERE is het filter-gedeelte van je query.
// - Zonder WHERE krijg je alle rijen.
// - Met WHERE krijg je alleen rijen die voldoen aan je voorwaarden.
//
// Filters combineren:
// - Soms heb je meerdere filters (zoeken + categorie).
// - Dan combineer je ze met AND.
// - Voorbeeld: WHERE p.title LIKE ... AND p.category_id = ...

$whereParts = []; // hierin bewaren we losse voorwaarden
$params = [];     // hierin bewaren we parameters voor prepared statement

// 4a) Zoekfilter (LIKE)
// LIKE uitleg:
// - LIKE gebruik je voor gedeeltelijke matches.
// - % betekent "maakt niet uit wat hier staat".
// - %php% vindt "php", "PHP", "ik leer php" (case kan verschillen per DB collation)
if ($search !== '') {
    $whereParts[] = 'p.title LIKE :search';
    $params[':search'] = '%' . $search . '%';
}

// 4b) Categorie filter
if ($selectedCategoryId > 0) {
    $whereParts[] = 'p.category_id = :category_id';
    $params[':category_id'] = $selectedCategoryId;
}

// 4c) WHERE toevoegen als er filters zijn
if (count($whereParts) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $whereParts);
}

// 4d) ORDER BY toevoegen
$sql .= " ORDER BY p.created_at {$orderDirection} ";

// ================================
// 5) Query uitvoeren (prepared statement)
// ================================
// Prepared statement uitleg (PDO):
// - $pdo->prepare($sql) maakt een statement met placeholders (:search, :category_id)
// - $stmt->execute($params) vult de placeholders in met echte waarden
// - Voordeel: veiliger (tegen SQL injection) + duidelijker
//
// Output tip:
// - Alles wat je toont uit $_GET of uit de database: gebruik htmlspecialchars().
// - Zo voorkom je dat HTML/JS “meeloopt” in je pagina (XSS).

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// ================================
// 6) Debuggen als het niet werkt
// ================================
// Tips voor studenten:
// - Werkt je filter niet? Check eerst wat er in $_GET zit.
// - Check of je WHERE voorwaarden goed zijn.
// - Zet debug=1 om de query en parameters te zien.
// - Probeer je SQL ook eens direct in phpMyAdmin.

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Eindopdracht: Zoek + Filter + Sorteer</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6 mb-6">
    <!--
        GET-formulier:
        - method="get" => waarden komen in de URL
        - name="q" => $_GET['q']
        - name="category_id" => $_GET['category_id']
        - name="sort" => $_GET['sort']
    -->
    <form method="get" class="grid gap-4">
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="q">Zoek op titel</label>
                <input
                    class="w-full border border-slate-300 rounded px-3 py-2"
                    type="text"
                    id="q"
                    name="q"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Bijv. PDO">
                <p class="text-xs text-slate-600 mt-1">SQL: <code>p.title LIKE %...%</code></p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="category_id">Filter categorie</label>
                <select class="w-full border border-slate-300 rounded px-3 py-2" id="category_id" name="category_id">
                    <option value="0">Alle categorieën</option>
                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?php echo (int)$category['id']; ?>"
                            <?php echo ($selectedCategoryId === (int)$category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-slate-600 mt-1">SQL: <code>p.category_id = ...</code></p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="sort">Sorteer op datum</label>
                <select class="w-full border border-slate-300 rounded px-3 py-2" id="sort" name="sort">
                    <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Nieuwste eerst</option>
                    <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Oudste eerst</option>
                </select>
                <p class="text-xs text-slate-600 mt-1">SQL: <code>ORDER BY created_at ASC/DESC</code></p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
                Zoeken
            </button>

            <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search_filter_sort.php">
                Reset
            </a>

            <!-- Debug knop via GET: zet debug=1 -->
            <label class="text-sm text-slate-700 flex items-center gap-2">
                <input type="checkbox" name="debug" value="1" <?php echo ($debug === 1) ? 'checked' : ''; ?>>
                Debug (toon SQL)
            </label>

            <div class="text-sm text-slate-600">
                Actieve sortering: <strong><?php echo htmlspecialchars($sortLabel); ?></strong>
            </div>
        </div>
    </form>
</div>

<?php if ($debug === 1): ?>
    <div class="mb-6 p-4 border border-amber-200 bg-amber-50 text-amber-900 rounded">
        <div class="font-semibold mb-2">Debug info</div>
        <div class="text-sm mb-2">Dit helpt als je query niet werkt.</div>
        <div class="text-xs text-slate-700">
            <div class="font-semibold">SQL</div>
            <pre class="whitespace-pre-wrap bg-white border border-amber-200 rounded p-3 mt-1"><?php echo htmlspecialchars($sql); ?></pre>
            <div class="font-semibold mt-3">Parameters (placeholders)</div>
            <pre class="whitespace-pre-wrap bg-white border border-amber-200 rounded p-3 mt-1"><?php echo htmlspecialchars(print_r($params, true)); ?></pre>
            <div class="font-semibold mt-3">$_GET</div>
            <pre class="whitespace-pre-wrap bg-white border border-amber-200 rounded p-3 mt-1"><?php echo htmlspecialchars(print_r($_GET, true)); ?></pre>
        </div>
    </div>
<?php endif; ?>

<div class="mb-4 text-sm text-slate-600">
    Resultaten: <strong><?php echo count($posts); ?></strong>
</div>

<?php if (count($posts) === 0): ?>
    <div class="p-6 bg-white border border-slate-200 rounded">
        Geen posts gevonden. Probeer andere filters.
    </div>
<?php else: ?>
    <div class="grid gap-4">
        <?php foreach ($posts as $post): ?>
            <article class="bg-white border border-slate-200 rounded p-5">
                <h2 class="text-xl font-semibold">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h2>
                <div class="text-sm text-slate-600 mt-1">
                    Categorie: <span class="font-medium"><?php echo htmlspecialchars($post['category_name']); ?></span>
                    •
                    <?php echo htmlspecialchars($post['created_at']); ?>
                </div>

                <p class="mt-4 text-slate-800 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
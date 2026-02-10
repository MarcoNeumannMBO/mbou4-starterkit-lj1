<?php
// posts/search.php
// READ OEFENING 1: Zoeken in posts (op titel)
//
// Wat leer je hier?
// - Een GET-formulier gebruiken (zoekterm staat in de URL)
// - Een LIKE query maken met wildcards (%)
// - PDO prepared statement gebruiken (veilig)
//
// Didactische structuur:
// 1) q uit $_GET lezen
// 2) SQL basisquery maken
// 3) Optioneel WHERE ... LIKE toevoegen
// 4) Prepared statement uitvoeren
// 5) Resultaat tonen (met htmlspecialchars)
//
// GET uitleg:
// - GET data zit in de URL (bijv. search.php?q=php)
// - Je leest dit in PHP via $_GET
// - Handig voor zoeken/filteren/sorteren, omdat je de URL kunt delen

require_once __DIR__ . '/../includes/db.php';

// 1) Zoekterm ophalen uit GET
$search = trim((string)($_GET['q'] ?? ''));

// Tip: trim() haalt spaties weg. "  php  " wordt "php".

// 2) SQL basisquery
// We halen posts op met hun categorie (JOIN), net als op de homepage.
//
// LIKE uitleg:
// - LIKE gebruik je om te zoeken met "gedeeltelijke" tekst.
// - % betekent: "maakt niet uit wat hier staat".
//   Voorbeeld: %php% matcht "PHP", "mijn php post", "ik leer php vandaag".
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

// 3) Als er een zoekterm is, voegen we een WHERE toe
$params = [];
if ($search !== '') {
    $sql .= " WHERE p.title LIKE :search ";

    // Prepared statement met parameter:
    // We zetten zelf de % wildcards om de zoekterm.
    $params[':search'] = '%' . $search . '%';
}

// 4) Sorteer standaard op nieuwste eerst
$sql .= " ORDER BY p.created_at DESC ";

// 5) Query uitvoeren
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// Output tip (XSS):
// Alles wat uit de database of uit $_GET komt, tonen we met htmlspecialchars().
// Zo kan iemand geen HTML/JS in jouw pagina injecteren.

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Oefening: Zoek posts</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6 mb-6">
    <!-- GET-formulier:
         - method="get" betekent: de invoer komt in de URL.
         - De input heeft name="q" dus je krijgt: ?q=...
    -->
    <form method="get" class="flex gap-3">
        <input
            class="flex-1 border border-slate-300 rounded px-3 py-2"
            type="text"
            name="q"
            value="<?php echo htmlspecialchars($search); ?>"
            placeholder="Zoek op titel, bijv. PDO">
        <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
            Zoeken
        </button>
        <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search.php">
            Reset
        </a>
    </form>

    <div class="text-sm text-slate-600 mt-3">
        Tip: probeer eens te zoeken op: <strong>PDO</strong> of <strong>week</strong>.
    </div>
</div>

<div class="mb-4 text-sm text-slate-600">
    <?php if ($search === ''): ?>
        Toon alle posts (geen zoekterm).
    <?php else: ?>
        Resultaten voor zoekterm: <strong><?php echo htmlspecialchars($search); ?></strong>
    <?php endif; ?>
</div>

<?php if (count($posts) === 0): ?>
    <div class="p-6 bg-white border border-slate-200 rounded">
        Geen posts gevonden.
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
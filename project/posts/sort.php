<?php
// posts/sort.php
// READ OEFENING 3: Sorteren (nieuwste/oudste)
//
// Wat leer je hier?
// - Sorteren met ORDER BY
// - Veilig omgaan met keuzes uit GET (whitelist)
//
// Didactische structuur:
// 1) sort uit $_GET lezen
// 2) whitelist toepassen (alleen newest/oldest)
// 3) ORDER BY richting kiezen (ASC/DESC)
// 4) Query uitvoeren en tonen
//
// ORDER BY uitleg:
// - ORDER BY bepaalt de volgorde van je resultaten.
// - DESC = aflopend (nieuwste eerst bij datum)
// - ASC  = oplopend (oudste eerst bij datum)

require_once __DIR__ . '/../includes/db.php';

// 1) Keuze ophalen uit GET
// Voorbeeld URL: sort.php?sort=newest
$sort = (string)($_GET['sort'] ?? 'newest');

// Tip: als iemand ?sort=hacker invult, komt dat ook binnen.
// Daarom doen we hieronder een whitelist.

// 2) Whitelist (alleen deze waardes mogen)
// Waarom?
// - Je kunt SQL keywords (ASC/DESC) meestal niet als prepared statement parameter binden.
// - Daarom kiezen we de ORDER BY richting met een simpele if.
// - We staan alleen bekende opties toe.
$orderDirection = 'DESC'; // standaard: nieuwste eerst
if ($sort === 'oldest') {
    $orderDirection = 'ASC';
    $sortLabel = 'Oudste eerst';
} else {
    // Alles wat niet 'oldest' is, behandelen we als 'newest'
    $sort = 'newest';
    $sortLabel = 'Nieuwste eerst';
}

// 3) Query bouwen
$sql = "
    SELECT
        p.id,
        p.title,
        p.content,
        p.created_at,
        c.name AS category_name
    FROM posts p
    INNER JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at {$orderDirection}
";

// Let op voor studenten:
// - {$orderDirection} komt NIET uit vrije user input.
// - Het komt uit onze whitelist (ASC of DESC).
// - Daarom is dit veilig in dit simpele voorbeeld.

$stmt = $pdo->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Oefening: Sorteer posts</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6 mb-6">
    <!-- GET-formulier: sorteer-keuze in de URL -->
    <form method="get" class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium mb-1" for="sort">Sorteer</label>
            <select class="w-full border border-slate-300 rounded px-3 py-2" id="sort" name="sort">
                <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Nieuwste eerst</option>
                <option value="oldest" <?php echo ($sort === 'oldest') ? 'selected' : ''; ?>>Oudste eerst</option>
            </select>
        </div>

        <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
            Sorteren
        </button>

        <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/sort.php">
            Reset
        </a>
    </form>

    <div class="text-sm text-slate-600 mt-3">
        Actieve sortering: <strong><?php echo htmlspecialchars($sortLabel); ?></strong>
    </div>
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
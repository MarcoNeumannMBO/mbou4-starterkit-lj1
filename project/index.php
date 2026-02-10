<?php
// index.php (homepage)
// Dit is de READ-pagina: we halen posts op uit de database en tonen ze.
//
// CRUD uitleg:
// - C = Create (toevoegen)
// - R = Read (lezen / tonen)
// - U = Update (wijzigen)
// - D = Delete (verwijderen)

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// READ: Posts ophalen met hun categorie via een JOIN.
// Wat doet een query?
// - Een SQL query is een opdracht aan de database.
// - Hier vragen we: geef alle posts + bijbehorende categorienaam.
$sql = "
    SELECT
        p.id,
        p.title,
        p.content,
        p.created_at,
        c.name AS category_name
    FROM posts p
    INNER JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
";

$stmt = $pdo->prepare($sql);
// Prepared statement uitleg:
// - Je "prepare"-t de query eerst.
// - Daarna "execute" je hem.
// - Dit is veiliger en werkt goed met variabelen.
$stmt->execute();

$posts = $stmt->fetchAll();

// Tip voor studenten (debug):
// Als je geen posts ziet, check dan:
// - Is de database geïmporteerd? (database.sql)
// - Klopt de database connectie in includes/db.php?
// - Staat er data in de tabel posts?

// Simpele success message via GET (optioneel)
$success = '';
if (isset($_GET['success'])) {
    // LET OP: Output altijd escapen om XSS te voorkomen.
    $success = (string)$_GET['success'];
}
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Posts</h1>
    <a href="<?php echo $baseUrl; ?>/posts/create.php" class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800">
        + Nieuwe post
    </a>
</div>

<div class="mb-6 bg-white border border-slate-200 rounded p-5">
    <h2 class="font-semibold">READ oefeningen (extra)</h2>
    <p class="text-sm text-slate-600 mt-1">
        Oefen eerst met GET + SELECT (zoeken/filteren/sorteren) voordat je meer CRUD bouwt.
    </p>
    <div class="mt-3 flex flex-wrap gap-2">
        <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search.php">Zoeken (LIKE)</a>
        <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/filter.php">Filter (categorie)</a>
        <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/sort.php">Sorteren (ORDER BY)</a>
        <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search_filter_sort.php">Eindopdracht (alles combineren)</a>
    </div>
</div>

<?php if ($success !== ''): ?>
    <div class="mb-6 p-4 border border-green-200 bg-green-50 text-green-800 rounded">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if (count($posts) === 0): ?>
    <div class="p-6 bg-white border border-slate-200 rounded">
        Er zijn nog geen posts. <a class="underline" href="<?php echo $baseUrl; ?>/posts/create.php">Maak je eerste post</a>.
    </div>
<?php else: ?>
    <div class="grid gap-4">
        <?php foreach ($posts as $post): ?>
            <article class="bg-white border border-slate-200 rounded p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h2>
                        <div class="text-sm text-slate-600 mt-1">
                            Categorie: <span class="font-medium"><?php echo htmlspecialchars($post['category_name']); ?></span>
                            •
                            <?php echo htmlspecialchars($post['created_at']); ?>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <!-- GET uitleg:
                             - GET data zit in de URL, bijv. delete.php?id=5
                             - Je leest dit in PHP via $_GET['id']
                        -->
                        <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/edit.php?id=<?php echo (int)$post['id']; ?>">
                            Bewerken
                        </a>
                        <a class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50" href="<?php echo $baseUrl; ?>/posts/delete.php?id=<?php echo (int)$post['id']; ?>">
                            Verwijderen
                        </a>
                    </div>
                </div>

                <p class="mt-4 text-slate-800 leading-relaxed">
                    <?php
                    // We tonen content. Voor een echte blog zou je hier ook line breaks verwerken.
                    // Voor beginners houden we het simpel.
                    //
                    // Waarom nl2br + htmlspecialchars?
                    // - nl2br maakt enters zichtbaar als <br>
                    // - htmlspecialchars zorgt dat HTML/JS niet uitgevoerd wordt (veilig)
                    echo nl2br(htmlspecialchars($post['content']));
                    ?>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';

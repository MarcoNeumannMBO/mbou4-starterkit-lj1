<?php
// posts/delete.php
// DELETE: hier verwijderen we een post.
//
// Didactisch punt:
// - Je ziet vaak een link "Verwijderen" (GET) die je naar een bevestigingspagina brengt.
// - Het echte verwijderen doen we met een POST (via een formulier).
//   Zo voorkom je dat iemand per ongeluk iets verwijdert door alleen een URL te openen.
//
// Didactische structuur:
// 1) id ophalen uit GET
// 2) post ophalen (zodat je weet wat je gaat verwijderen)
// 3) bij POST: DELETE uitvoeren
// 4) redirect terug naar homepage

require_once __DIR__ . '/../includes/db.php';

$postId = (int)($_GET['id'] ?? 0);

// Tip: casten naar int is een simpele vorm van "input schoonmaken".
// Daardoor wordt 'abc' automatisch 0.

if ($postId <= 0) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Geen geldige post id.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Post ophalen om te tonen welke je gaat verwijderen
$postStmt = $pdo->prepare('SELECT id, title FROM posts WHERE id = :id');
$postStmt->execute([':id' => $postId]);
$post = $postStmt->fetch();

if (!$post) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Post niet gevonden.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Als bevestiging is verstuurd (POST), verwijderen we echt.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DELETE query
    // LET OP: WHERE id = :id is belangrijk (anders verwijder je alle posts)
    $deleteStmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
    $deleteStmt->execute([':id' => $postId]);

    // PRG pattern: na POST redirecten naar een GET pagina.
    header('Location: ' . $baseUrl . '/index.php?success=' . urlencode('Post verwijderd!'));
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-red-700">Post verwijderen</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6">
    <p class="mb-4">
        Weet je zeker dat je deze post wilt verwijderen?
    </p>

    <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded">
        <strong><?php echo htmlspecialchars($post['title']); ?></strong>
    </div>

    <!-- POST formulier om echt te verwijderen -->
    <form method="post" class="flex gap-3">
        <!-- Waarom POST en geen GET?
             - GET zou betekenen: alleen de URL openen verwijdert data.
             - POST is expliciet: je klikt op een knop in een formulier.
        -->
        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" type="submit">
            Ja, verwijderen
        </button>
        <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/index.php">
            Nee, terug
        </a>
    </form>

    <!-- Wat doet DELETE?
         - In SQL is dat: DELETE FROM tabel WHERE ...
         - WHERE is belangrijk: zonder WHERE verwijder je ALLES!
    -->
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

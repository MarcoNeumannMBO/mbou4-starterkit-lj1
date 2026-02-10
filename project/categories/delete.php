<?php
// categories/delete.php
// DELETE: categorie verwijderen.
//
// Belangrijk om te leren:
// - Door de foreign key (posts.category_id -> categories.id)
//   kan MySQL het verwijderen blokkeren als er nog posts aan deze categorie hangen.
//   Dat heet referentiële integriteit.

// Didactische structuur:
// 1) id ophalen uit GET
// 2) categorie ophalen + tellen hoeveel posts eraan hangen
// 3) bij POST: proberen te verwijderen
// 4) redirect terug naar categorie-overzicht

require_once __DIR__ . '/../includes/db.php';

$categoryId = (int)($_GET['id'] ?? 0);

// Tip: (int) zorgt dat je altijd een getal hebt.
// Als iemand "abc" in de URL zet, wordt het 0 en stoppen we netjes.

if ($categoryId <= 0) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Geen geldige categorie id.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Categorie ophalen + aantal posts
// Waarom tellen we posts?
// - Als er posts aan deze categorie hangen, willen we studenten uitleg geven.
// - Ook voorkomt de database het meestal via de foreign key.
$stmt = $pdo->prepare('
    SELECT c.id, c.name, COUNT(p.id) AS post_count
    FROM categories c
    LEFT JOIN posts p ON p.category_id = c.id
    WHERE c.id = :id
    GROUP BY c.id, c.name
');
$stmt->execute([':id' => $categoryId]);
$category = $stmt->fetch();

if (!$category) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Categorie niet gevonden.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$postCount = (int)$category['post_count'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dit is het moment dat de student op "Ja, verwijderen" klikt.
    if ($postCount > 0) {
        // Didactisch: we blokkeren het alvast.
        $errors[] = 'Je kunt deze categorie niet verwijderen omdat er nog posts aan gekoppeld zijn.';
        $errors[] = 'Pas eerst de posts aan (kies een andere categorie) en probeer daarna opnieuw.';
    } else {
        $delete = $pdo->prepare('DELETE FROM categories WHERE id = :id');

        try {
            $delete->execute([':id' => $categoryId]);

            // PRG: na POST redirecten we naar een GET pagina.
            header('Location: ' . $baseUrl . '/categories/index.php?success=' . urlencode('Categorie verwijderd!'));
            exit;
        } catch (PDOException $e) {
            // Als de database het blokkeert (foreign key), tonen we een duidelijke melding.
            $errors[] = 'Verwijderen is mislukt. Waarschijnlijk zijn er nog posts gekoppeld aan deze categorie.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-red-700">Categorie verwijderen</h1>
    <a href="<?php echo $baseUrl; ?>/categories/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar categorieën</a>
</div>

<?php if (count($errors) > 0): ?>
    <div class="mb-6 p-4 border border-red-200 bg-red-50 text-red-800 rounded">
        <ul class="list-disc pl-5">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="bg-white border border-slate-200 rounded p-6">
    <p class="mb-4">Weet je zeker dat je deze categorie wilt verwijderen?</p>

    <div class="mb-4 p-4 bg-slate-50 border border-slate-200 rounded">
        <div><strong><?php echo htmlspecialchars($category['name']); ?></strong></div>
        <div class="text-sm text-slate-600 mt-1">Aantal posts in deze categorie: <?php echo $postCount; ?></div>
    </div>

    <?php if ($postCount > 0): ?>
        <div class="mb-6 p-4 border border-amber-200 bg-amber-50 text-amber-900 rounded">
            Deze categorie heeft nog posts. Door de <strong>foreign key</strong> mag je hem niet verwijderen.
        </div>
    <?php endif; ?>

    <form method="post" class="flex gap-3">
        <!-- Waarom staat de knop soms uit?
             - Als $postCount > 0, dan heeft deze categorie nog posts.
             - In dat geval is verwijderen niet toegestaan.
             - We zetten de knop disabled zodat de gebruiker snapt dat het niet kan.
        -->
        <button
            class="px-4 py-2 rounded text-white <?php echo ($postCount > 0) ? 'bg-slate-400 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700'; ?>"
            type="submit"
            <?php echo ($postCount > 0) ? 'disabled' : ''; ?>>
            Ja, verwijderen
        </button>
        <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/categories/index.php">
            Nee, terug
        </a>
    </form>

    <!-- Foreign key uitleg:
         - Een foreign key is een "regel" in de database.
         - Het zegt: posts.category_id moet verwijzen naar een bestaande categories.id.
         - Daardoor kun je niet zomaar een categorie verwijderen als er nog posts naar wijzen.
    -->
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
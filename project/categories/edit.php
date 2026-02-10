<?php
// categories/edit.php
// UPDATE: categorie wijzigen.

// Didactische structuur:
// 1) id ophalen uit GET
// 2) categorie ophalen (READ)
// 3) bij POST: valideren en UPDATE uitvoeren
// 4) redirect terug naar overzicht

require_once __DIR__ . '/../includes/db.php';

$categoryId = (int)($_GET['id'] ?? 0);

// Tip: (int) maakt van de string uit de URL een getal.

if ($categoryId <= 0) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Geen geldige categorie id.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Categorie ophalen
$stmt = $pdo->prepare('SELECT id, name FROM categories WHERE id = :id');
$stmt->execute([':id' => $categoryId]);
$category = $stmt->fetch();

if (!$category) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Categorie niet gevonden.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // name="name" in HTML => $_POST['name']
    $name = trim((string)($_POST['name'] ?? ''));

    if ($name === '') {
        $errors[] = 'Naam is verplicht.';
    }

    if (count($errors) === 0) {
        // WHERE id = :id is belangrijk: zo update je maar 1 rij.
        $update = $pdo->prepare('UPDATE categories SET name = :name WHERE id = :id');

        try {
            $update->execute([
                ':name' => $name,
                ':id' => $categoryId,
            ]);

            // PRG: na POST redirecten.
            header('Location: ' . $baseUrl . '/categories/index.php?success=' . urlencode('Categorie bijgewerkt!'));
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Deze categorienaam bestaat al. Kies een andere naam.';
        }
    }
}

$formName = (string)($_POST['name'] ?? $category['name']);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Categorie bewerken</h1>
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
    <form method="post" class="grid gap-4">
        <div>
            <label class="block text-sm font-medium mb-1" for="name">Naam</label>
            <input
                class="w-full border border-slate-300 rounded px-3 py-2"
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($formName); ?>">
        </div>

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
                Opslaan
            </button>
            <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/categories/index.php">
                Annuleren
            </a>
        </div>

        <!-- UPDATE uitleg:
             - SQL: UPDATE categories SET name = ... WHERE id = ...
             - WHERE is belangrijk zodat je alleen 1 categorie wijzigt.
        -->
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

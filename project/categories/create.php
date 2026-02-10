<?php
// categories/create.php
// CREATE: nieuwe categorie toevoegen.

// Didactische structuur:
// 1) bij POST: input lezen en valideren
// 2) INSERT uitvoeren met prepared statement
// 3) redirect terug naar overzicht (PRG)

require_once __DIR__ . '/../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // name="name" in HTML => $_POST['name'] in PHP
    $name = trim((string)($_POST['name'] ?? ''));

    if ($name === '') {
        $errors[] = 'Naam is verplicht.';
    }

    if (count($errors) === 0) {
        // INSERT met prepared statement
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');

        try {
            $stmt->execute([':name' => $name]);

            // PRG: na POST redirecten.
            header('Location: ' . $baseUrl . '/categories/index.php?success=' . urlencode('Categorie toegevoegd!'));
            exit;
        } catch (PDOException $e) {
            // Als de naam al bestaat, krijgen we door de UNIQUE constraint een error.
            // Voor beginners tonen we een vriendelijke melding.
            $errors[] = 'Deze categorienaam bestaat al. Kies een andere naam.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Nieuwe categorie</h1>
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
        <!-- Formulier uitleg:
             - method="post" => data komt in $_POST
             - input name="name" => $_POST['name']
        -->
        <div>
            <label class="block text-sm font-medium mb-1" for="name">Naam</label>
            <input
                class="w-full border border-slate-300 rounded px-3 py-2"
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars((string)($_POST['name'] ?? '')); ?>"
                placeholder="Bijv. Sport">
        </div>

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
                Opslaan
            </button>
            <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/categories/index.php">
                Annuleren
            </a>
        </div>

        <!-- CRUD context:
             - Dit is CREATE voor categories.
             - We doen een INSERT INTO categories...
        -->
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

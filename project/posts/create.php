<?php
// posts/create.php
// CREATE: hier voegen we een nieuwe post toe.

// Didactische structuur van deze pagina:
// 1) Data ophalen voor het formulier (categorieën)
// 2) Kijken of het formulier is verstuurd (POST)
// 3) Input lezen + valideren
// 4) INSERT query uitvoeren (prepared statement)
// 5) Redirect naar de homepage (zodat je niet per ongeluk dubbel opslaat)

require_once __DIR__ . '/../includes/db.php';

// Categories ophalen voor de dropdown
$categoryStmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// Fouten verzamelen (handig voor studenten)
$errors = [];

// POST uitleg:
// - Als je een formulier verstuurt met method="post", komen de waarden in $_POST.
// - POST data staat NIET in de URL (handig voor grotere/gevoelige data).
// - In HTML bepaalt het "name"-attribuut de sleutel in $_POST.
//   Voorbeeld: <input name="title"> => $_POST['title']
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpele input lezen
    $title = trim((string)($_POST['title'] ?? ''));
    $content = trim((string)($_POST['content'] ?? ''));
    $categoryId = (int)($_POST['category_id'] ?? 0);

    // Validatie (beginner-vriendelijk)
    if ($title === '') {
        $errors[] = 'Titel is verplicht.';
    }
    if ($content === '') {
        $errors[] = 'Content is verplicht.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Kies een categorie.';
    }

    // Als alles ok is: INSERT uitvoeren
    if (count($errors) === 0) {
        // Prepared statement uitleg:
        // - We gebruiken placeholders (:title, :content, :category_id)
        // - Daarna geven we de waarden mee in execute([...])
        // - Dit is veilig en voorkomt SQL-injection.
        $insertSql = 'INSERT INTO posts (title, content, category_id) VALUES (:title, :content, :category_id)';
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category_id' => $categoryId,
        ]);

        // Redirect uitleg (PRG pattern):
        // - PRG = Post/Redirect/Get
        // - Na een succesvolle POST doen we een redirect naar een GET pagina.
        // - Voordeel: als je daarna F5 (refresh) doet, wordt de INSERT niet opnieuw gedaan.
        header('Location: ' . $baseUrl . '/index.php?success=' . urlencode('Post toegevoegd!'));
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Nieuwe post toevoegen</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
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
             - method="post" => waarden komen in $_POST
             - action is leeg => formulier post naar dezelfde pagina
        -->
        <div>
            <label class="block text-sm font-medium mb-1" for="title">Titel</label>
            <input
                class="w-full border border-slate-300 rounded px-3 py-2"
                type="text"
                id="title"
                name="title"
                value="<?php echo htmlspecialchars((string)($_POST['title'] ?? '')); ?>"
                placeholder="Bijv. Mijn eerste blogpost">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="category_id">Categorie</label>
            <select class="w-full border border-slate-300 rounded px-3 py-2" id="category_id" name="category_id">
                <option value="0">-- Kies een categorie --</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?php echo (int)$category['id']; ?>"
                        <?php
                        $selectedId = (int)($_POST['category_id'] ?? 0);
                        echo ($selectedId === (int)$category['id']) ? 'selected' : '';
                        ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Foreign key uitleg (kort):
                 - posts.category_id verwijst naar categories.id
                 - Daardoor "hoort" een post bij een categorie
            -->
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="content">Content</label>
            <textarea
                class="w-full border border-slate-300 rounded px-3 py-2"
                id="content"
                name="content"
                rows="7"
                placeholder="Schrijf hier je tekst..."><?php echo htmlspecialchars((string)($_POST['content'] ?? '')); ?></textarea>
        </div>

        <!-- Waarom gebruiken we htmlspecialchars?
             - Gebruikers kunnen tekst invoeren.
             - Met htmlspecialchars voorkom je dat HTML/JS "meedraait" in je pagina.
             - Dit is belangrijk tegen XSS.
        -->

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
                Opslaan
            </button>
            <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/index.php">
                Annuleren
            </a>
        </div>

        <!-- Samenvatting CRUD:
             - CREATE: INSERT in de database
             - READ: SELECT uit de database
             - UPDATE: UPDATE in de database
             - DELETE: DELETE uit de database
        -->
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

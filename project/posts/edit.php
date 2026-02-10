<?php
// posts/edit.php
// UPDATE: hier wijzigen we een bestaande post.

// Didactische structuur:
// 1) id ophalen uit GET
// 2) huidige post ophalen uit de database (READ)
// 3) bij POST: valideren en UPDATE uitvoeren
// 4) redirect terug naar homepage

require_once __DIR__ . '/../includes/db.php';

// GET uitleg:
// - GET data zit in de URL, bijv. edit.php?id=3
// - Je leest dit in PHP via $_GET
// - Omdat $_GET tekst is, casten we naar int: (int)
$postId = (int)($_GET['id'] ?? 0);

if ($postId <= 0) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Geen geldige post id.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Categories ophalen voor dropdown
$categoryStmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// Post ophalen (READ van 1 record)
$postStmt = $pdo->prepare('
    SELECT id, title, content, category_id
    FROM posts
    WHERE id = :id
');
$postStmt->execute([':id' => $postId]);
$post = $postStmt->fetch();

if (!$post) {
    require_once __DIR__ . '/../includes/header.php';
    echo '<div class="p-6 bg-white border border-slate-200 rounded">Post niet gevonden.</div>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$errors = [];

// Als het formulier is verstuurd (POST), dan updaten we.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // name="title" in HTML => $_POST['title'] in PHP
    $title = trim((string)($_POST['title'] ?? ''));
    $content = trim((string)($_POST['content'] ?? ''));
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($title === '') {
        $errors[] = 'Titel is verplicht.';
    }
    if ($content === '') {
        $errors[] = 'Content is verplicht.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Kies een categorie.';
    }

    if (count($errors) === 0) {
        // UPDATE query: wijzig velden voor deze id
        // LET OP: WHERE id = :id is superbelangrijk.
        // Zonder WHERE zou je alle posts aanpassen.
        $updateStmt = $pdo->prepare('
            UPDATE posts
            SET title = :title,
                content = :content,
                category_id = :category_id
            WHERE id = :id
        ');

        $updateStmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':category_id' => $categoryId,
            ':id' => $postId,
        ]);

        // PRG pattern: na POST redirecten we naar een GET pagina.
        header('Location: ' . $baseUrl . '/index.php?success=' . urlencode('Post bijgewerkt!'));
        exit;
    }
}

// Waarden voor formulier:
// - Als er net een POST is mislukt (errors), tonen we $_POST.
// - Anders tonen we de waardes uit de database.
$formTitle = (string)($_POST['title'] ?? $post['title']);
$formContent = (string)($_POST['content'] ?? $post['content']);
$formCategoryId = (int)($_POST['category_id'] ?? $post['category_id']);

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Post bewerken</h1>
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
        <!-- Formulier tips:
             - Als er errors zijn, houden we de ingevulde waarden vast.
             - Daarom gebruiken we $formTitle/$formContent.
        -->
        <div>
            <label class="block text-sm font-medium mb-1" for="title">Titel</label>
            <input
                class="w-full border border-slate-300 rounded px-3 py-2"
                type="text"
                id="title"
                name="title"
                value="<?php echo htmlspecialchars($formTitle); ?>">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="category_id">Categorie</label>
            <select class="w-full border border-slate-300 rounded px-3 py-2" id="category_id" name="category_id">
                <option value="0">-- Kies een categorie --</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?php echo (int)$category['id']; ?>"
                        <?php echo ($formCategoryId === (int)$category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="content">Content</label>
            <textarea
                class="w-full border border-slate-300 rounded px-3 py-2"
                id="content"
                name="content"
                rows="7"><?php echo htmlspecialchars($formContent); ?></textarea>
        </div>

        <!-- nl2br gebruiken we bij het tonen van content (READ) om enters zichtbaar te maken.
             In een textarea hoeven we dat niet.
        -->

        <div class="flex gap-3">
            <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
                Opslaan
            </button>
            <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/index.php">
                Annuleren
            </a>
        </div>

        <!-- Wat doet een UPDATE?
             - Je past bestaande data aan.
             - In SQL is dat: UPDATE ... SET ... WHERE id = ...
             - WHERE is belangrijk: zonder WHERE wijzig je ALLE rijen!
        -->
    </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

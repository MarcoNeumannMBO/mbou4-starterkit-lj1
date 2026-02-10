<?php
// posts/filter.php
// READ OEFENING 2: Filteren op categorie
//
// Wat leer je hier?
// - Een dropdown vullen met data uit de database
// - Filteren met een WHERE in SQL
// - Werken met een foreign key: posts.category_id verwijst naar categories.id
//
// Didactische structuur:
// 1) categorieën ophalen voor dropdown
// 2) category_id uit $_GET lezen
// 3) SQL basisquery maken
// 4) Optioneel WHERE toevoegen
// 5) Resultaat tonen
//
// Foreign key uitleg:
// - posts.category_id is een getal (id van category)
// - categories.id is de "primaire sleutel" (uniek)
// - Zo weet je welke post bij welke categorie hoort

require_once __DIR__ . '/../includes/db.php';

// 1) Alle categorieën ophalen voor de dropdown
$categoryStmt = $pdo->prepare('SELECT id, name FROM categories ORDER BY name ASC');
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// 2) Gekozen categorie uit GET
// - In de URL staat bijvoorbeeld: filter.php?category_id=2
$selectedCategoryId = (int)($_GET['category_id'] ?? 0);

// Waarom (int)?
// - category_id hoort een getal te zijn.
// - Met (int) voorkom je gedoe met teksten in de URL.

// 3) Posts ophalen
// - Als category_id = 0, tonen we alles
// - Als category_id > 0, tonen we alleen posts van die categorie
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

$params = [];
if ($selectedCategoryId > 0) {
    // WHERE uitleg:
    // - WHERE zorgt ervoor dat je alleen de rijen krijgt die voldoen aan de voorwaarde.
    // - Hier: alleen posts waar category_id gelijk is aan de gekozen categorie.
    $sql .= " WHERE p.category_id = :category_id ";
    $params[':category_id'] = $selectedCategoryId;
}

$sql .= " ORDER BY p.created_at DESC ";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Oefening: Filter op categorie</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar posts</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6 mb-6">
    <!-- GET-formulier, zodat de keuze in de URL komt -->
    <form method="get" class="flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium mb-1" for="category_id">Kies categorie</label>
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
        </div>

        <button class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" type="submit">
            Filter
        </button>

        <a class="px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/filter.php">
            Reset
        </a>
    </form>

    <div class="text-sm text-slate-600 mt-3">
        SQL idee: <code>WHERE p.category_id = ...</code>
    </div>
</div>

<?php if (count($posts) === 0): ?>
    <div class="p-6 bg-white border border-slate-200 rounded">
        Geen posts gevonden voor deze filter.
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
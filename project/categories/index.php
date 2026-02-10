<?php
// categories/index.php
// READ: categorieën tonen.

// Wat leer je hier?
// - SELECT query uitvoeren
// - LEFT JOIN gebruiken
// - COUNT + GROUP BY gebruiken om "aantal posts" te tellen

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

// We halen categorieën op + hoeveel posts per categorie.
// LEFT JOIN zorgt ervoor dat categorieën zonder posts ook getoond worden.
// COUNT(p.id) telt hoeveel posts bij die categorie horen.
// GROUP BY is nodig omdat we COUNT gebruiken.
$sql = "
    SELECT
        c.id,
        c.name,
        c.created_at,
        COUNT(p.id) AS post_count
    FROM categories c
    LEFT JOIN posts p ON p.category_id = c.id
    GROUP BY c.id, c.name, c.created_at
    ORDER BY c.name ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$categories = $stmt->fetchAll();

$success = '';
if (isset($_GET['success'])) {
    $success = (string)$_GET['success'];
}
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Categorieën</h1>
    <a href="<?php echo $baseUrl; ?>/categories/create.php" class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800">
        + Nieuwe categorie
    </a>
</div>

<?php if ($success !== ''): ?>
    <div class="mb-6 p-4 border border-green-200 bg-green-50 text-green-800 rounded">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<div class="bg-white border border-slate-200 rounded overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left p-3 text-sm font-semibold">Naam</th>
                <th class="text-left p-3 text-sm font-semibold">Posts</th>
                <th class="text-left p-3 text-sm font-semibold">Aangemaakt</th>
                <th class="text-right p-3 text-sm font-semibold">Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($categories) === 0): ?>
                <tr>
                    <td class="p-3" colspan="4">Nog geen categorieën.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <tr class="border-b border-slate-100">
                        <td class="p-3">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </td>
                        <td class="p-3">
                            <?php echo (int)$category['post_count']; ?>
                        </td>
                        <td class="p-3 text-slate-600">
                            <?php echo htmlspecialchars($category['created_at']); ?>
                        </td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <a class="px-3 py-1 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/categories/edit.php?id=<?php echo (int)$category['id']; ?>">
                                    Bewerken
                                </a>
                                <a class="px-3 py-1 border border-red-300 text-red-700 rounded hover:bg-red-50" href="<?php echo $baseUrl; ?>/categories/delete.php?id=<?php echo (int)$category['id']; ?>">
                                    Verwijderen
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6 text-sm text-slate-600">
    Foreign key uitleg:
    In onze database verwijst <strong>posts.category_id</strong> naar <strong>categories.id</strong>.
    Daardoor kun je een categorie meestal niet verwijderen als er nog posts aan hangen (ON DELETE RESTRICT).
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';

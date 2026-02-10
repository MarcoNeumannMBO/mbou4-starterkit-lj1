<?php
// posts/index.php
// Overzichtspagina voor alle "posts"-pagina's.
//
// Waarom bestaat deze pagina?
// Beginners typen vaak zelf URL's in.
// Dit project heeft mappen op hetzelfde niveau:
// - /posts/
// - /categories/
// Dus: /categories/posts/... bestaat NIET.

// Tip voor studenten:
// - Als je een 404 krijgt, kijk dan eerst in de mappenstructuur.
// - Een URL volgt meestal de map + bestandsnaam.
// - Querystring (bijv. ?id=1) hoort bij GET.

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Posts — overzicht</h1>
    <a href="<?php echo $baseUrl; ?>/index.php" class="underline text-slate-700 hover:text-slate-900">Terug naar homepage</a>
</div>

<div class="bg-white border border-slate-200 rounded p-6">
    <h2 class="font-semibold">Snelle links</h2>
    <p class="text-sm text-slate-600 mt-1">
        In Docker is de basis-URL: <code>http://localhost:8080/</code>
    </p>

    <div class="mt-4 grid gap-2">
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/index.php">
            Posts tonen (READ) — <code>/index.php</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/create.php">
            Post toevoegen (CREATE) — <code>/posts/create.php</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/edit.php?id=1">
            Post bewerken (UPDATE) — voorbeeld: <code>/posts/edit.php?id=1</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/delete.php?id=1">
            Post verwijderen (DELETE) — voorbeeld: <code>/posts/delete.php?id=1</code>
        </a>
    </div>

    <h2 class="font-semibold mt-8">READ oefeningen</h2>
    <p class="text-sm text-slate-600 mt-1">
        Oefen met GET + SELECT: zoeken, filteren en sorteren.
    </p>

    <div class="mt-4 grid gap-2">
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search.php">
            Zoeken (LIKE) — <code>/posts/search.php</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/filter.php">
            Filter (WHERE) — <code>/posts/filter.php</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/sort.php">
            Sorteren (ORDER BY) — <code>/posts/sort.php</code>
        </a>
        <a class="px-3 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/search_filter_sort.php">
            Eindopdracht (alles combineren) — <code>/posts/search_filter_sort.php</code>
        </a>
    </div>

    <div class="mt-8 p-4 border border-amber-200 bg-amber-50 text-amber-900 rounded">
        <strong>Let op:</strong> <code>/categories/posts/search.php</code> bestaat niet.
        De mappen <code>posts</code> en <code>categories</code> zitten naast elkaar.
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
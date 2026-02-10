<?php
// posts/categories/index.php
// Deze pagina bestaat alleen om beginners te helpen.
//
// Wat leer je hier?
// - Hoe URL's meestal de mapstructuur volgen
// - Waarom $baseUrl handig is in links
// - Waarom we shared includes gebruiken (db/header/footer)
//
// Veel studenten typen per ongeluk URL's in.
// Bijvoorbeeld:
//   /posts/categories/index.php
//
// Waarom is dat "fout" in dit project?
// In onze projectstructuur staan de mappen NAAST elkaar (siblings):
//   /posts/
//   /categories/
//
// Dus:
// - /categories/index.php     ✅ bestaat
// - /posts/search.php         ✅ bestaat
// - /posts/categories/index.php ❌ (hoort niet te bestaan)
//
// We laten deze pagina expres bestaan als "hulppagina".
// Zo krijgen studenten uitleg in plaats van een 404.

// includes/db.php:
// - maakt de database connectie ($pdo)
// - maakt ook $baseUrl aan (handig voor links)
//
// $baseUrl uitleg:
// - Als je app in de webroot draait (Docker), dan is $baseUrl ''
// - Als je app in een submap draait (XAMPP/WAMP), dan is $baseUrl bijv. '/project'
// Daardoor kunnen we overal links schrijven als: $baseUrl . '/categories/index.php'

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="bg-white border border-slate-200 rounded p-6">
    <h1 class="text-2xl font-bold mb-2">Deze URL is niet de bedoeling</h1>

    <p class="text-slate-700">
        Je zit nu op: <code>/posts/categories/index.php</code>
    </p>

    <!-- Didactische tip:
         Probeer altijd eerst te kijken in de projectmappen:
         - Staat er een map 'categories'? Dan is de URL meestal /categories/...
         - Staat er een map 'posts'? Dan is de URL meestal /posts/...
         De URL volgt vaak de mapstructuur.
    -->

    <div class="mt-4 p-4 border border-amber-200 bg-amber-50 text-amber-900 rounded">
        <strong>Uitleg:</strong> De map <code>categories</code> staat niet in <code>posts</code>.
        De mappen staan naast elkaar.
    </div>

    <div class="mt-6">
        <a class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-800" href="<?php echo $baseUrl; ?>/categories/index.php">
            Ga naar categorieën
        </a>
        <a class="ml-2 px-4 py-2 border border-slate-300 rounded hover:bg-slate-50" href="<?php echo $baseUrl; ?>/posts/index.php">
            Ga naar posts overzicht
        </a>
    </div>

    <div class="mt-6 text-sm text-slate-600">
        Snelle check als je een 404 krijgt:
        <ul class="list-disc pl-5 mt-2">
            <li>Heb je de juiste mapnaam gebruikt in de URL?</li>
            <li>Bestaat het bestand echt in de map (bijv. <code>index.php</code>)?</li>
            <li>Heb je per ongeluk twee keer dezelfde map in je URL gezet?</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
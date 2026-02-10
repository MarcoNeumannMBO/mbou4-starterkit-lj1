<?php
// includes/header.php
// Dit bestand bevat de HTML <head> + navigatie.
// We gebruiken Tailwind CSS via CDN (geen installatie nodig).

// Let op:
// - Elke pagina die dit include gebruikt, heeft vaak ook db.php nodig.
// - In db.php maken we ook $baseUrl aan.
//
// Meestal is de volgorde:
// 1) require_once includes/db.php
// 2) require_once includes/header.php
// 3) jouw pagina HTML
// 4) require_once includes/footer.php
//
// $baseUrl is superhandig voor beginners:
// - In Docker is $baseUrl meestal '' (leeg)
// - In XAMPP/WAMP kan $baseUrl bijv. '/project' zijn
// Zo werken links in het menu overal.
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Starterkit (PHP + PDO)</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-900">

    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="<?php echo $baseUrl; ?>/index.php" class="font-bold text-lg">Blog Starterkit</a>

            <div class="flex gap-3">
                <!-- Menu links zijn relatief aan $baseUrl -->
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/index.php">Posts</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/posts/index.php">Posts overzicht</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/posts/create.php">Post toevoegen</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/categories/index.php">Categorieën</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/posts/search.php">Oefen READ</a>
                <a class="text-slate-700 hover:text-slate-900" href="<?php echo $baseUrl; ?>/posts/search_filter_sort.php">Eindopdracht READ</a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
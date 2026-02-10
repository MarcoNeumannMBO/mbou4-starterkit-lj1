# Blog Starterkit (PHP + MySQL + PDO)

Deze starterkit is bedoeld voor eerstejaars MBO Software Development studenten.
Je leert hier **CRUD** (Create, Read, Update, Delete) met **procedural PHP** en **PDO prepared statements**.

## Inhoudsopgave

- [Les-run sheet (voor docenten)](#les-run-sheet-voor-docenten)
- [Voor collega-docenten (weinig PHP ervaring)](#voor-collega-docenten-weinig-php-ervaring)
- [Projectstructuur (wat staat waar?)](#projectstructuur-wat-staat-waar)
- [GitHub (voor studenten)](#github-voor-studenten)
- [1) Database aanmaken](#1-database-aanmaken)
- [2) Database login instellen](#2-database-login-instellen)
- [3) Project starten](#3-project-starten)
- [Troubleshooting (snelle checks)](#troubleshooting-snelle-checks)
- [Pagina’s](#paginas)
- [Oefenopdrachten (voor studenten)](#oefenopdrachten-voor-studenten)
- [Bijdragen (docenten)](#bijdragen-docenten)
- [Tip voor studenten](#tip-voor-studenten)
- [Database (uitleg in 1 minuut)](#database-uitleg-in-1-minuut)

## Les-run sheet (voor docenten)

Snelle flow om in 5–10 minuten te controleren of alles werkt:

1) Start project (bij voorkeur Docker)
2) Open homepage en check dat er posts staan
3) Voeg een categorie toe
4) Voeg een post toe (kies categorie)
5) Open eindopdracht (READ) en test:
	- zoekterm
	- categorie filter
	- sortering

Als iets stuk is: ga naar “Troubleshooting (snelle checks)”.

## Voor collega-docenten (weinig PHP ervaring)

Kort idee van hoe dit project in elkaar zit:

- De "website" staat in de map `project/`.
	- Die map is de **document root** (webroot) bij Docker en bij `php -S -t project`.
- Elke pagina is een losse `.php` file (procedural).
- Bijna elke pagina start met:
	1) `require_once .../includes/db.php` (maakt `$pdo` en `$baseUrl` aan)
	2) `require_once .../includes/header.php` (HTML head + navigatie)
	3) pagina-inhoud
	4) `require_once .../includes/footer.php`

Aanrader als je snel wilt testen zonder PHP-setup: gebruik **Docker Compose**.

## Projectstructuur (wat staat waar?)

- `project/index.php` = homepage (READ posts)
- `project/includes/db.php` = database connectie + `$baseUrl`
- `project/includes/header.php` en `project/includes/footer.php` = layout + navigatie
- `project/posts/*` = posts CRUD + READ-oefeningen
- `project/categories/*` = categorie CRUD
- `project/database.sql` = database schema + voorbeeld-data

Let op: de map `project/` is de **webroot**.

## GitHub (voor studenten)

Als je dit project via GitHub gebruikt:

1) Clone de repo
```bash
git clone <REPO_URL>
cd ProjectP3SD
```

2) Kies hoe je wilt runnen:
- PHP built-in server (makkelijk)
- XAMPP/MAMP (in htdocs)
- Docker Compose (alles automatisch)

Let op:
- Dit is een leerproject voor lokaal gebruik.
- Zet nooit echte wachtwoorden/API keys in je repo. Gebruik daarvoor `.env` (en commit die niet).

## 1) Database aanmaken

- Open phpMyAdmin (of MySQL Workbench)
- Voer het bestand `database.sql` uit
- Er wordt een database gemaakt met de naam: `blogproject`

Tip:
- In `database.sql` staat ook voorbeeld-data (seed) zodat je direct posts/categorieën ziet.

## 2) Database login instellen

Pas je database instellingen aan in `includes/db.php`:

- `$dbHost`
- `$dbName`
- `$dbUser`
- `$dbPass`

Gebruik je Docker Compose? Dan hoef je dit meestal niet aan te passen:
- Docker zet DB instellingen via env vars (zie `docker-compose.yml`)
- `includes/db.php` leest die env vars automatisch

## 3) Project starten

### Optie A: PHP built-in server (aanrader voor snel testen)

Run in je terminal:

```bash
php -S localhost:8000 -t project
```

Tip:
- `-t project` betekent: “gebruik de map `project/` als webroot”.

Open daarna in je browser:

- `http://localhost:8000/index.php`

### Optie B: XAMPP/MAMP

- Zet de map `project` in je `htdocs`
- Open:
- `http://localhost/project/index.php`

Extra tip (als je links niet kloppen):
- Je kunt `BASE_URL` instellen (env var) om links te forceren.
- Voorbeeld: `BASE_URL=/project`

### Optie C: Docker Compose (alles in 1 keer)

Vereist: Docker Desktop.

Start containers:

```bash
docker compose up --build
```

Stoppen:
```bash
docker compose down
```

Database opnieuw vullen (handig in de les):

```bash
docker compose down -v
docker compose up --build
```

Open in je browser:

- `http://localhost:8080/`

Handige URL’s (Docker)

- Homepage (posts READ): `http://localhost:8080/index.php`
- Categorieën: `http://localhost:8080/categories/index.php`
- Posts overzicht: `http://localhost:8080/posts/index.php`
- READ oefeningen:
	- `http://localhost:8080/posts/search.php`
	- `http://localhost:8080/posts/filter.php`
	- `http://localhost:8080/posts/sort.php`
	- `http://localhost:8080/posts/search_filter_sort.php`

Let op: `http://localhost:8080/categories/posts/...` bestaat niet (mappen staan naast elkaar).

Uitleg:
- MySQL draait in een container (`db`).
- PHP+Apache draait in een container (`web`).
- De database wordt bij de eerste start gevuld met `database.sql`.
- Database instellingen komen via env vars (zie `docker-compose.yml`) en worden gelezen in `includes/db.php`.

## Troubleshooting (snelle checks)

- Krijg je een lege pagina of “rare tekst” bovenaan?
	- Controleer of er nergens per ongeluk tekst **buiten** `<?php ... ?>` staat in includes.
- “Database verbinding mislukt”:
	- Draait MySQL wel?
	- Bestaat de database `blogproject`?
	- Kloppen username/password in `includes/db.php` (of in `docker-compose.yml` bij Docker)?
- 404 of verkeerde links:
	- Gebruik altijd links met `$baseUrl`.
	- In XAMPP kun je `BASE_URL=/project` forceren.

## Database (uitleg in 1 minuut)

- Tabel `categories` heeft `id` en `name`
- Tabel `posts` heeft `category_id`
- `posts.category_id` verwijst naar `categories.id` (foreign key)
  - Daardoor kan MySQL een categorie blokkeren om te verwijderen als er nog posts aan hangen.

## Pagina’s

- Posts (READ): `index.php`
- Posts (CREATE/UPDATE/DELETE): `posts/create.php`, `posts/edit.php`, `posts/delete.php`
- READ oefeningen: `posts/search.php`, `posts/filter.php`, `posts/sort.php`
- Eindopdracht (READ alles combineren): `posts/search_filter_sort.php`
- Categorieën (CRUD): `categories/index.php`, `categories/create.php`, `categories/edit.php`, `categories/delete.php`

## Oefenopdrachten (voor studenten)

Kies er één of meer:

1) Validatie uitbreiden
- Maak titel minimaal 5 tekens en content minimaal 20 tekens.
- Toon een duidelijke foutmelding per veld.

2) Zoek uitbreiden
- Breid zoeken uit naar content (niet alleen titel).
- Tip: voeg een extra `OR p.content LIKE ...` toe.

3) Sorteren uitbreiden
- Voeg een extra sort-optie toe: titel A-Z.
- Tip: gebruik weer een whitelist.

4) Extra kolom toevoegen
- Voeg `author` toe aan posts (DB + formulier + queries).
- Laat op de homepage de auteur zien.

## Bijdragen (docenten)

- Dit project is bewust simpel gehouden (procedural PHP, geen frameworks).
- Houd wijzigingen didactisch: veel comments, kleine stappen, geen “magie”.
- Zet geen secrets in de repo; gebruik `.env` lokaal en commit die niet.

## Tip voor studenten

Lees de comments in de code: daar staat uitgelegd wat **GET**, **POST**, **queries**, **prepared statements**, **foreign keys** en **CRUD** zijn.

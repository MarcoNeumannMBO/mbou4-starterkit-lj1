# ProjectP3SD — Blog Starterkit (PHP + MySQL + PDO)

Dit is een **beginner-vriendelijke** starterkit voor MBO Software Development:
- Procedural PHP (geen frameworks)
- MySQL + PDO prepared statements
- Tailwind via CDN
- CRUD + extra READ-oefeningen (zoeken/filteren/sorteren)

## Voor collega’s (kort)

- De webroot (document root) is de map [project/](project/).
- Alle pagina’s zijn losse PHP files (procedural).
- De database + tabellen + voorbeeld-data staan in [project/database.sql](project/database.sql).
- Uitgebreide handleiding staat in [project/README.md](project/README.md).

## Requirements

Kies één van deze setups:

- Docker: Docker Desktop + `docker compose`
- Lokaal: PHP 8.x + MySQL (bijv. via XAMPP/MAMP/WAMP)

## Snel starten (aanrader: Docker)

1) Start met Docker Compose:
```bash
docker compose up --build
```

2) Open in je browser:
- http://localhost:8080/

De database wordt bij de eerste start automatisch gevuld met voorbeeld-data.

Veelgebruikte URL’s:
- http://localhost:8080/index.php
- http://localhost:8080/categories/index.php
- http://localhost:8080/posts/search_filter_sort.php

Database reset (handig als studenten “alles kapot” hebben):
```bash
docker compose down -v
docker compose up --build
```

## Alternatief: PHP built-in server + lokale MySQL

1) Zorg dat MySQL draait (bijv. via XAMPP/MAMP).
2) Importeer [project/database.sql](project/database.sql) in je MySQL.
3) Start PHP server:
```bash
php -S localhost:8000 -t project
```
4) Open:
- http://localhost:8000/index.php

## Documentatie

- Hoofdhandleiding (studenten + docenten): [project/README.md](project/README.md)

## Les-check (5 minuten)

1) Start project (Docker of `php -S ...`)
2) Open homepage en check of je posts ziet
3) Test 1 CRUD actie:
	- Post toevoegen → verschijnt op homepage
4) Test categorieën:
	- Categorie toevoegen → zichtbaar in dropdown bij post
5) Test eindopdracht (READ):
	- Zoek + filter + sorteer op http://localhost:8080/posts/search_filter_sort.php (Docker)

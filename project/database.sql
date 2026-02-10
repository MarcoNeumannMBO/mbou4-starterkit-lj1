-- database.sql
-- Voer dit uit in phpMyAdmin / MySQL Workbench / CLI om de database te maken.

DROP DATABASE IF EXISTS blogproject;
CREATE DATABASE blogproject CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blogproject;

-- Tabel: categories
-- Elke categorie heeft een id en een naam.
CREATE TABLE categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

-- Tabel: posts
-- Elke post hoort bij precies 1 categorie via category_id.
CREATE TABLE posts (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_posts_category_id (category_id),
    CONSTRAINT fk_posts_category
        FOREIGN KEY (category_id)
        REFERENCES categories (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Voorbeelddata
INSERT INTO categories (name) VALUES
('Algemeen'),
('School'),
('Tech');

INSERT INTO posts (title, content, category_id) VALUES
('Welkom bij de blog!', 'Dit is een voorbeeldpost. Pas de tekst gerust aan.', 1),
('PDO en Prepared Statements', 'Met prepared statements voorkom je SQL-injection. Je gebruikt placeholders zoals :title en bindt daarna de waarden.', 3),
('Eerste week op school', 'Vandaag hebben we geleerd wat GET en POST zijn en hoe formulieren werken.', 2);

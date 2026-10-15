CREATE DATABASE IF NOT EXISTS kepzesi_games
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kepzesi_games;

CREATE TABLE IF NOT EXISTS games (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    link VARCHAR(2048) NOT NULL,
    type VARCHAR(60) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Simon mondja', './simonsays/', 'Memória · Ritmus', './assets/img/simonsays-sm.png', 10
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './simonsays/');

INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Magasabb vagy alacsonyabb?', './higherlower/', 'Tipp · Számok', './assets/img/higherlower-sm.png', 20
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './higherlower/');

INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Ujjkoptató', './ujjkoptato/', 'Gyorsaság · Ügyesség', './assets/img/ujjkoptato-sm.png', 30
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './ujjkoptato/');

INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Akasztófa', './akasztofa/', 'Szavak · Találgatás', './assets/img/akasztofa-sm.png', 40
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './akasztofa/');

INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Kör-X', './korix/', 'Stratégia · Logika', './assets/img/korix.png', 50
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './korix/');

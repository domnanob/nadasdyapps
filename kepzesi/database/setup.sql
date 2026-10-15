CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    link TEXT NOT NULL,
    type TEXT NOT NULL,
    image_path TEXT NOT NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS higherorlower_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT NOT NULL,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    country TEXT NOT NULL,
    password TEXT NOT NULL,
    date TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS higherorlower_users_user_id ON higherorlower_users (user_id);
CREATE INDEX IF NOT EXISTS higherorlower_users_date ON higherorlower_users (date);
CREATE INDEX IF NOT EXISTS higherorlower_users_username ON higherorlower_users (username);
CREATE INDEX IF NOT EXISTS higherorlower_users_email ON higherorlower_users (email);

CREATE TABLE IF NOT EXISTS higherorlower_leaderboard (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id TEXT NOT NULL,
    score INTEGER NOT NULL,
    mode TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS simonsays_leaderboard (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    score INTEGER NOT NULL,
    username TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS ujjkoptato_leaderboard (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    score INTEGER NOT NULL,
    username TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS findtheball_scores (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player_name TEXT NOT NULL,
    score INTEGER NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS findtheball_scores_rank ON findtheball_scores (score, id);

INSERT OR IGNORE INTO higherorlower_leaderboard (id, user_id, score, mode)
VALUES (1, '86a934f8-315d-4145-834e-8088e03bc894', 15, 'spotify');
INSERT OR IGNORE INTO higherorlower_users (id, user_id, username, email, country, password, date)
VALUES (1, '86a934f8-315d-4145-834e-8088e03bc894', 'admin', 'admin@admin.com', 'HU', 'admin', '2025-05-25 20:29:23');

INSERT OR IGNORE INTO simonsays_leaderboard (id, score, username) VALUES
    (1, 0, 'Saci'),
    (2, 0, 'vIKI'),
    (3, 0, 'Miki');

INSERT OR IGNORE INTO ujjkoptato_leaderboard (id, score, username)
VALUES (66, 32, 'Nem jani');

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
INSERT INTO games (name, link, type, image_path, sort_order)
SELECT 'Hol a labda?', './findtheball/', 'Memória · Figyelmesség', './assets/img/findtheball.svg', 60
WHERE NOT EXISTS (SELECT 1 FROM games WHERE link = './findtheball/');

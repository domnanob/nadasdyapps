<?php
/**
 * Beállítások – itt módosíthatsz mindent, a többi fájlhoz nem kell nyúlni.
 */

// Osztálylista a legördülő menühöz. Írd át a valós osztályaitokra!
$classes = ["9A","10A", "11A", "12A", "13A", "13A", "3/11", "4/12"];
$classes[] = 'Egyéb';

return [
    // Admin jelszó HASH-e. Üresen hagyva az admin oldal le van tiltva.
    // Generálás (a saját jelszavaddal):
    //   php -r "echo password_hash('A_TITKOS_JELSZAVAD', PASSWORD_DEFAULT), PHP_EOL;"
    // Az eredményt (ami $2y$-nal kezdődik) másold ide, idézőjelek közé.
    'admin_password_hash' => '$2y$12$i1KRGDeV7oQa6GopVlW3o.iNNQnfMiAyhNjgBuDddS/Bq82U9ZG.2',

    // Az SQLite fájl helye. Ha tudod, tedd a webroot-on KÍVÜLRE.
    'db_path' => __DIR__ . '/data/nadasdy_cup.sqlite',

    'timezone' => 'Europe/Budapest',

    // Jelentkezési határidő (ezután az oldal automatikusan lezár). Üresen hagyva nincs határidő.
    'registration_deadline' => '2026-12-07 23:59:59',

    'event_date' => '2026.12.14',
    'event_time' => '13:00',

    'classes' => $classes,

    // GeoGuessr rangok (opcionális mező). A GeoGuessr Duels divíziói.
    'ranks' => ['Bronz', 'Silver', 'Gold', 'Master', 'Champion'],
];

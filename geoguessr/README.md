# Nádasdy GeoGuessr Cup ’26 – jelentkezési oldal

## Fájlok
| Fájl | Mire való |
|---|---|
| `index.php` | Nyilvános jelentkezési oldal |
| `admin.php` | Admin (jelszavas), **nincs rá link sehol** |
| `config.php` | Jelszó-hash, osztályok, rangok, dátum |
| `db.php`, `partials.php` | Közös kód |
| `assets/` | CSS, JS (ide teheted a `logo.png`-t is) |
| `data/` | Ide jön létre az SQLite fájl (automatikusan) |

## Beüzemelés
1. Követelmény: PHP 8.0+ `pdo_sqlite` és `mbstring` bővítménnyel.
2. Generálj jelszó-hash-t:
   `php -r "echo password_hash('A_TITKOS_JELSZAVAD', PASSWORD_DEFAULT), PHP_EOL;"`
   és másold a `config.php`-ban az `admin_password_hash` értékéhez.
3. Állítsd be a `config.php`-ban az osztályokat.
4. A `data/` mappának írhatónak kell lennie a webszerver számára.
5. Próba: `php -S localhost:8000` a mappában, majd `http://localhost:8000/`.

## Biztonság
- Az admin URL-t nem linkeljük; érdemes átnevezni az `admin.php`-t valami kitalálhatatlanra.
- Az `data/.htaccess` Apache-n tiltja az adatbázis letöltését. Nginx esetén tilts le a `/data/` mappát,
  vagy állítsd a `db_path`-ot a webroot-on kívülre.
- Éles használatnál HTTPS-t használj.
- 5 hibás belépés után 15 percre zár az adott IP-nek.
- A jelentkezés CSRF-védett, van rejtett csapda-mező a botok ellen, minden SQL előkészített lekérdezés.

## Logó
Ha a plakát iskolai címerét kivágod `assets/logo.png` néven, megjelenik az oldal tetején.

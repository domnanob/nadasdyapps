# Vasi SZC Nádasdy Tamás Technikum és Kollégium – Nyílt napi jelentkezés

Regisztrációs űrlap és admin panel a nyílt napokra, sima PHP-ban (PDO + SQLite + JSON API).

## Indítás Laragonnal

1. A projekt mappáját tedd ide: `C:\laragon\www\NadasdyJelentkezes`
2. Laragonban: **Start All**
3. Az SQLite adatbázis fájlja: `backend/db/iskola.sqlite`; a tábla sémája az `iskola.sql` fájlban található.
4. Böngészőben:
   - Űrlap: http://localhost/NadasdyJelentkezes/frontend/
   - Admin: http://localhost/NadasdyJelentkezes/frontend/admin/

A PHP SQLite PDO illesztőprogram legyen engedélyezve, és a `backend/db/iskola.sqlite` fájlnak írhatónak kell lennie a PHP számára.

Az admin felület jelszóval védett: **nadasdyadmin** (módosítható a `backend/config.php`-ban: `ADMIN_JELSZO`).

## Adatbázis

Az SQLite séma az [`iskola.sql`](./iskola.sql) fájlban található. Az adatbázis létrehozása vagy újraimportálása:

```powershell
@'
<?php
$pdo = new PDO('sqlite:backend/db/iskola.sqlite');
$pdo->exec(file_get_contents('iskola.sql'));
'@ | php
```

Az `iskola.sql` jelenleg csak a táblát és annak sémáját tartalmazza; jelentkezési rekordokat nem importál.

- `Major_Name`: a kiválasztott szakmák vesszővel elválasztva (pl. `CNC-programozó,Szakács`)
- `Dorm`: 0 = bejáró, 1 = kollégista, 2 = még nem tudja

## Felépítés

```
backend/
├── db/conn.php        PDO SQLite kapcsolat
├── db/iskola.sqlite   SQLite adatbázis
├── config.php         Admin jelszó, iskola neve, szakmák listája
├── applicant.php      Applicant osztály (egy jelentkező)
├── applicantlist.php  Applicantlist osztály (lekérdezések)
└── api/
    ├── create.php     POST   – új jelentkezés (JSON)
    ├── getall.php     GET    – összes jelentkező (admin)
    ├── stats.php      GET    – statisztika (admin)
    ├── delete.php     DELETE – ?id= törlés (admin)
    ├── login.php      POST   – admin belépés (JSON: password)
    └── logout.php     POST   – kijelentkezés

frontend/
├── index.php                  Regisztrációs űrlap
├── sikeres.php                Köszönő oldal (15 mp után visszaáll)
├── admin/belepes.php          Admin belépő oldal
├── admin/index.php            Áttekintés, szakmánkénti statisztika, lista + törlés
├── partials/                  Közös fejléc, logó, segédfüggvények
└── assets/                    CSS, JS, logó
```

A választható szakmákat a `backend/config.php` `SZAKOK` listájában lehet módosítani.

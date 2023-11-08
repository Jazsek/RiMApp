# RiMApp

Az alkalmazás a Laravel legújabb verizójában íródott dockerizálva. Mivel az app filament segítségével írodott, így a rendszerhez tartozik egy admin felület, amin keresztül kezelhetjük a karaktereket, epizódokat, illetve az api szinkronizálást. Az alábbi bekezdések segítenek a konfigurációban, illetve bemutatja a feladat elvégzéhez szükséges időket.

## Telepítés és konfigurálás

-   Docker alkalmazás elindítása

-   Repo pullolása

```
git clone https://github.com/Jazsek/RiMApp.git
```

-   Sikeres pullolás után szükséges beállítani a megfelelő környezeti változót. Ehhez célszerű lemásolni a repoban lévő **'.env.example'** elnevezésű fájlt, majd módosítani sima **'.env'**-re.

-   Terminálba fel kell nyitni azt a mappát, ahova klónoztuk a repot. Ha a klónozás is terminállal történt, akkor elég egyszerűen csak belenavigálni a 'cd' parancssal.

-   A mappában be kell állítani a Laravel sailt. Ehhez a Laravel oldalán található [dokumentációt](https://laravel.com/docs/10.x/sail#installing-sail-into-existing-applications) ajánlom megtekintésre. Előfordulhat, hogy az install elhasal, mert a filament miatt kell az 'ext-intl'. A parancssorban látható, hogy a composer parancsot ki kell egészíteni a '--ignor...' kezdetű paranccsal.

-   Sikeres telepítés után generálnunk kell egy app key-t

```
php artisan key:generate
```

-   Indítsuk el a docker containert. A gyökérkönyvtárban állva adjuk ki az indításhoz szükséges parancsot. (Én a -d parancsal szoktam kiadni, hogy tovább tudjam használni ugyan azt a terminál ablakot)

```
./vendor/bin/sail up -d

VAGY

docker-compose up -d
```

-   Indítás után lépjünk át a sail container termináljába (Sail-8.2/app).

```
./vendor/bin/sail bash
```

-   Futassuk le a migrációkat. Mivel az admin felület eléréshez szükségünk van egy felhasználóra, így célszerű a migrációkkal együtt az adatbázis seedereket is futtatni. Ez létre fog hozni nekünk egy admin felhasználót.

```php
/**
 * A seeder az alábbi adatokkal fogja létrehozni az admin felhasználót
 *
 * Name: Admin
 * Email: admin@example.com
 * Password: password
*/

php artisan migrate --seed
```

-   (opcionális) Nem muszáj futtatni a migráció során az adatbázis seedet. Az alábbi paranccsal van lehetőségünk külön a terminál ablakba létrehozni egy felhasználót.

```
php artisan make:filament-user
```

A [localhost](http://localhost) url-en keresztül már el is érhetjük az alkalmazást. A root URL azonnal átirányít az admin bejelentkező panelra. Bejelentkezni a seeder által létrehozott profillal lehet, vagy a make:filament-user parancs által létrehozottal.

## Felület és funkciók

### Dashboard

-   Ez csak egy sima felület amit a filamentphp generált automatikusan

### Characters

-   Az összes karakter listája tekinthető itt meg. Módosítás esetén látható a karakterekhez tartozó epizódok listája. Ez könnyedén módosítható, társítható, vagy éppen leválasztható a karakterről. Új létrehozására is van lehetőség.

### Episodes

-   Az összes epizód listája tekinthető itt meg. Funkcionalitás tekintetében majdnem ugyan az mint a karakterek oldal, csak éppen fordítva. Itt kapott helyet az api szinkronizáló gomb is.

## Funkciók

-   Api adatok szinkronizálása. Megjegyzésben látható a fájl elérési útvonala.

```php
/// app/Filament/Resources/EpisodeResource/Pages/ListEpisodes.php
private function importEpisodesAndCharacters(): void {}
```

-   A táblázatok az alábbi függvényben tekinthetőek meg.

```php
/// app/Filament/Resources/CharacterResource.php
/// app/Filament/Resources/EpisodeResource.php
public static function table(Table $table): Table {}
```

-   Módosításkor látható relációs táblázatokat a getRelations függvény hívja meg. A hozzájuk tartozó fájl megtalálható a resource/RelationManagers mappán belül.

```php
/// app/Filament/Resources/CharacterResource.php
/// app/Filament/Resources/EpisodeResource.php
public static function getRelations(): array {}

// app/Filament/Resources/CharacterResource/RelationManagers/EpisodesRelationManager.php
class EpisodesRelationManager extends RelationManager {}

// app/Filament/Resources/EpisodeResource/RelationManagers/CharactersRelationManager.php
class CharactersRelationManager extends RelationManager {}
```

## Elkészítési folyamat

-   Összesen: **~8-9 óra**

| Folyamatok                                        | Szükséges idő | Megjegyzés                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| ------------------------------------------------- | :------------ | :-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| _Tervezés_                                        | ~1 óra        | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Laravel telepítés_                               | ~5 perc       | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Filament telepítés_                              | ~5 perc       | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Modelek és migrációk létrehozása_                | ~1 óra        | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Resource-ok létrehozása_                         | ~5 perc       | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Táblázatok beállítása_                           | ~30 perc      | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Rick and Morty API PHP Client telepítés_         | ~5 perc       | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Api adatok adatbázisba mentése_                  | ~2 óra        | Sajnos itt sok időt veszítettem annak köszönhetően, hogy nem ismertem a sorozatot. Az importálás relatíve gyorsan megvolt, viszont teszteltem az adatokat és feltűnt, hogy néhány karaktert többször is attacholtam egy adott epizódhoz. Furcsa is volt, hogy az összes karakterszámom sem fedte le azt a valóságot, amit a karakter apival kérdeztem le. Végül sikerült megfejteni, hogy a probléma onnan eredt, hogy sok ideig úgy kezeltem a szinkronizálást, hogy a karakterek nevei egyediek. Ezt kezeltem is a karakter létrehozásánál, és mivel már volt ilyen nevű karakter az adatbázisban, így nem hozta létre az appom. Végül nem csak a nevet nézem, hogy van-e megegyező érték az adatbázisban, hanem minden elemét. |
| _Táblázat rendezése, keresése, szűrése beállítás_ | ~30 perc      | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |
| _Tesztelés_                                       | ~2 óra        | Manuális tesztelés. Funkciók kipróbálása a specifikációnak megfelelően. Applikáció telepítése másik számítógépre a readme alapján.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| _ReadME írás_                                     | ~1.5 óra      | -                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |

## Megjegyzés

Mivel nem volt különösebb kritérum és tiltás, így bátran mertem használni a filamentPhp-t. Amennyiben ez esetleg probléma lenne, akkor bármikor készen állok megvalósítani ezt a projektet a filamentphp nélkül is. Számomra ez a megoldás gyorsabb megvalósítást tett lehetővé. Remélem a tudásom, és a gondolkodásom ebből a verzióból is teljesen jól megállapítható.

Pár szóban azért felvázolnám a gondolatmenetem, ha csak sima laravel verziót használtam volna. A model felépítése ugyan úgy nézne ki, mint a mostani példában. Létrehoznék 1-1 controllert a karakterek és az epizódok számára. Ugyan úgy létrehoznék egy gombot, amin keresztül meg tudom hívni az api kéréseket, és hasonló módon dolgoznám fel az adatokat, mint most. Természetesen a szükséges route-ot beállítanám a web.php fájlban. A létrehozott controller fájlokba írnék egy index függvényt, ami renderelne egy bladet a megfelelő változókkal. Valószínűleg itt a CRUD műveletek megírását kihagynám. Ha esetleg erre is szükség lenne akkor persze a controllert resourceként hoznám létre, és a web.php-ba is így húznám be. Külön requestbe validálnám az adatokat az áttekinthetőség miatt. A változókat a blade-nek a compact függvény segítségével küldeném tovább. A szükséges változót a with függvénnyel együtt kérném le, hogy elkerüljem az n+1 problámát.

Végszóként pedig nagyon szépen köszönöm a lehetőséget az állásra, és ha esetleg nem túl nagy fáradtság, akkor utólag szívesen fogadok pár megjegyzést / észrevételt, hogy tudjam merre is hibáztam, vagy éppen a jövbőben mire kellene jobban odafigyelnem!

# Investīciju izglītības platforma

**Noslēguma darba projekts** — tīmekļa lietotne, kurā lietotāji var izveidot un palaist **izglītojošas investīciju simulācijas** ar grafikiem, parametriem un (pēc izvēles) “roku” režīmu tirdzniecības galdam. **Nav īstas naudas un nav tirgus prognožu** — mērķis ir izprast salikto procentu likmi, svārstības, inflāciju un risku.

---

## Galvenās iespējas

- **Konti:** reģistrācija, pieteikšanās, e-pasta verifikācija, paroles maiņa un atjaunošana.
- **Divu faktoru autentifikācija (2FA):** ieslēgšana, atslēgšana, atkopšanas kodi; administrātors var palīdzēt pēc atbalsta pieteikuma.
- **Simulācijas:** vairāki saglabāti scenāriji uz lietotāju; izveide, rediģēšana, dzēšana; palaišana, solis pa mēnesim, pauze, atiestatīšana; saglabāšana uz servera (momentuzņēmums un vēsture).
- **Režīmi:** klasiskā automātiskā ikmēneša loģika un **hands-on** režīms ar tirdzniecības galdu (pirkšana/pārdošana pret naudas maciņu).
- **Izglītošana:** iebūvēta pamācība, “ātrais ceļvedis”, brīdinājumi par simulācijas raksturu.
- **Iestatījumi:** profila vārds (e-pasts profilā netiek mainīts), parole, 2FA, valūtas preferences, konta dzēšana.
- **Valūtas:** kursu pārveidošana iestatījumos, izmantojot ārēju API (skat. kodu un `.env`).
- **Atbalsts:** vienota **Support** lapa — pieteikumi, kas piesaistīti kontam, publiska 2FA/konta palīdzība, **paroles atjaunošana** ar divām vēstulēm (drošības paziņojums + saite jaunai parolei).
- **Administrācija:** statistika, lietotāji, atbalsta pieteikumi, 2FA atslēgšana pēc pārbaudes.

---

## Tehnoloģijas

| Slānis | Lietots |
|--------|---------|
| Backend | **Laravel 12**, **PHP 8.2+** |
| Frontend | **Vite**, CSS (dizaina žetoni, komponentes), JS simulators (`simulation-runner.js`) |
| Datubāze | **MySQL** (ieteicams; piemēroti arī citi Laravel atbalstītie draiveri) |
| Auth | Laravel Breeze līdzīga plūsma, 2FA (Google2FA) |

---

## Prasības

- PHP **8.2 vai jaunāks**
- **Composer**
- **Node.js** un **npm** (priekš `npm run build` vai `npm run dev`)
- **MySQL** (vai cita konfigurācijā norādītā datubāze)
- Rakstīšanas tiesības mapēm `storage/` un `bootstrap/cache/`

---

## Uzstādīšana (izstrāde, piemēram, Laragon)

1. **Atkarības**

   ```bash
   composer install
   npm ci
   ```

2. **Vide**

   Nokopējiet `.env.example` uz `.env`.

   **Svarīgi:** `.env` satur slepenas vērtības (atslēgas, paroles) un **nedrīkst būt Git/GitHub**. Repozitorijā glabā tikai `.env.example`.

   Iestatiet:

   - `APP_NAME`, `APP_URL` (svarīgi saitēm e-pastos)
   - `APP_KEY` — `php artisan key:generate`
   - Datubāze: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_HOST`

   Projekts bieži izmanto `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database` — pirms vietnes atvēršanas **pārliecinieties, ka MySQL darbojas**.

3. **Shema**

   ```bash
   php artisan migrate
   ```

   Ja izmantojat kešatmiņu/rindu tabulas datubāzē, pēc migrācijām tabulām `cache`, `jobs` u.c. jābūt izveidotām — pretējā gadījumā var rasties kļūdas, piemēram, “Table '…cache' doesn't exist”.

4. **Frontend**

   Izstrādē:

   ```bash
   npm run dev
   ```

   Vai vienu reizi salikt produkcijas komplektu:

   ```bash
   npm run build
   ```

5. **Serveris**

   ```bash
   php artisan serve
   ```

   Atveriet `APP_URL` vai `http://127.0.0.1:8000`. Ja `npm run dev` nedarbojas, pēc `npm run build` pietiek ar saliktajiem failiem mapē `public/build/`.

6. **(Pēc izvēles) demonstrācijas dati**

   ```bash
   php artisan db:seed
   ```

   **Parole visiem pēc `db:seed` izveidotajiem demonstrācijas kontiem:** `password` (nepieļaujama ražošanā; nomainiet vai dzēsiet).

   | E-pasts | Loma |
   |---------|------|
   | `admin@school.demo` | Administrators |
   | `demo@school.demo` | Lietotājs ar paraugu simulācijām |
   | `test@example.com` | Tests |
   | Papildu konti | `SchoolProjectAccountsSeeder` |

---

## E-pasts un paroles atjaunošana

- **Piegāde:** `.env` iestatiet `MAIL_MAILER=smtp` un aizpildiet `MAIL_*` (hosts, ports, šifrēšana, `MAIL_FROM_ADDRESS`).
- **Pārbaude:** `php artisan mail:test jusu@epasts.lv`
- Ja `MAIL_MAILER=log`, vēstules rakstītas `storage/logs/laravel.log`.
- **Paroles atjaunošana:** lietotājs saņem **divas** vēstules: īsu drošības paziņojumu (ar iespēju atcelt pieprasījumu, ja tas nebijāt jūs) un standarta Laravel e-pastu ar atjaunošanas saiti. No Support lapas var pievienot brīvprātīgu piezīmi, kas parādās drošības vēstulē.

**Svarīgi:** `APP_URL` jāatbilst publiskajai vietnes adresei, citādi saites e-pastos būs nepareizas.

---

## Testēšana

```bash
php artisan test
```

---

## Izvietošana (kopīgots hostings / cPanel)

1. `composer install --no-dev --optimize-autoloader`
2. `.env` ražošanai, `APP_DEBUG=false`, `APP_KEY` (piemēram, `php artisan key:generate`)
3. `php artisan migrate --force`
4. `npm ci` un `npm run build`
5. Dokumentu sakne norādīta uz **`public/`**
6. E-pasts: SMTP no hostinga; pārbaudiet `APP_URL` un `MAIL_FROM_*`

---

## Juridisks un pedagoģisks paziņojums

Simulācija izmanto **vienkāršotu nejaušu modeli** mācību nolūkos. Tā **nav** investīciju, nodokļu vai juridiska padoma avots. Pagātnes rezultāti simulācijā neliecina par nākotnes tirgus attīstību.

---

## Projekta struktūra (īsi)

- `app/` — kontrolieri, modeļi, pasta klases, middleware
- `routes/web.php`, `routes/auth.php`, `routes/console.php` — maršruti un `mail:test` komanda
- `resources/views/` — Blade veidnes
- `resources/css/`, `resources/js/` — Vite ievaddati
- `resources/lang/` — tulkojumi (EN, LV)
- `database/migrations/`, `database/seeders/` — shēma un demo dati

---

*Šis README apraksta pašreizējo Laravel projektu; novecojušas atsauces uz citiem stekiem (piemēram, atsevišķu `src/` / `api/` sadalījumu šajā repozitorijā vairs neizmanto).*
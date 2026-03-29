#  Noslēguma darba tēmas apstiprināšana

**Investīciju izglītības vietne** — simulē, kā darbojas investīcijas izglītojošiem nolūkiem.

---

##  Vietnei nepieciešamās funkcijas:

1. Sistēmas pieteikšanās (lietotājvārds un parole, ar reģistrācijas iespēju).
2. Brīdinājuma paziņojumi vairākās vietās, ka šī ir izglītojoša platforma.
3. Lietotāja saskarnes iestatījumi: tumšs/gaišs režīms un fonta izvēle.
4. Vairāku saglabātu simulāciju pārvaldība, kas piesaistītas konkrētam lietotājam.
5. Iespēja apturēt atsevišķas simulācijas un mainīt to atjaunināšanas ātrumu.
6. Parametru iestatīšana pirms simulācijas sākuma: sākuma summa, investoru skaits, pieauguma ātrums, riska apetīte un tirgus ietekme.

---

##  Izveidotās pielāgotās simulācijas funkcijas:

1. Imitēta investīciju izmaiņu sistēma (random pieaugums/samazinājums pēc iestatītiem parametriem).
2. Papildinformācija uzbraucot ar kursoru uz grafika vai vērtībām.
3. Lietotājs var ietekmēt investīciju apjomu, risku un izmaiņu koeficientus.
4. Lietotāju pieslēgšanās statusa parādīšana (kurš pašlaik tiešsaistē) — ja funkcija aktīva.

---

##  Paveiktais darbs:

1. Pieslēgšanās un reģistrācijas lapas ar SQLite datubāzi un PHP backend.
2. Investīciju simulācijas lapa ar strādājošu grafiku, vērtību skaitīšanu un apturēšanas funkciju.
3. Tumšā/gaišā režīma pārslēgšanas funkcija visai vietnei.
4. Sistēma vairāku lietotāju kontiem.
5. Backend pilnībā pārbūvēts no localhost uz laravel, no docker, uz Node.js no beigām PHP ar SQLite.
6. Precīzi atdalīti frontend (`src/`) un backend (`api/`) ceļi vienā projektā.
7. Integrācija ar valūtas kursu API (exchangerate-api.com) reāllaika valūtas kursu iegūšanai.
8. Izglītojošie brīdinājumi par simulācijas raksturu un drošību.

---

##  Valūtas kursu sistēma:

Lai parādītu pašreizējo valūtas vērtību, programma izmanto tiešsaistes datu avotu (API), kas nodrošina aktuālus valūtas kursus. 
Programma pieprasa jaunākos datus, nolasa atbildi un parāda valūtas vērtību lietotājam. 
Šie dati var tikt automātiski atjaunināti, lai saglabātu aktuālu informāciju.

**API izmantošana:**
- Izmantots exchangerate-api.com bezmaksas API
- Automātiska kursu atjaunināšana katru stundu
- Kešošana localStorage 24 stundu garumā
- Fallback uz noklusējuma kursiem, ja API nav pieejams

---

##  Izglītojošā simulācijas raksturs:

Tā kā šis projekts ir investīciju simulācija, tas neizmanto īstu naudu un nemēģina prognozēt tirgu. 
Tā vietā tas imitē, kā investīcijas parasti darbojas reālajā dzīvē. 
Investīciju vērtība laika gaitā mainās, pamatojoties uz vidējo pieaugumu un nejaušām svārstībām, kas atspoguļo tirgus uzsprāgumus un kritumus. 
Dažādi riska līmeņi ietekmē to, cik stabila vai nestabila ir investīcija.

Šī pieeja padara simulāciju reālistisku, vienlaikus saglabājot to drošu un izglītojošu.

**Brīdinājumi:**
- Izglītojošie brīdinājumi tiek rādīti vairākās vietās platformā
- Simulācija neizmanto īstu naudu
- Rezultāti nav reālu tirgus prognožu
- Simulācija ir paredzēta tikai izglītībai un apmācībai

## Deployment (shared hosting / cPanel)

1. **PHP & Composer** — PHP 8.2+, install dependencies: `composer install --no-dev --optimize-autoloader`
2. **Environment** — copy `.env.example` to `.env`, set `APP_KEY` (`php artisan key:generate`), `APP_URL`, and **MySQL** credentials (typical on cPanel).
3. **Database** — `php artisan migrate --force`  
   If `.env` uses `CACHE_STORE=database` or `QUEUE_CONNECTION=database`, the repo includes migrations for **`cache`**, **`cache_locks`**, **`jobs`**, and **`failed_jobs`** (created via `php artisan cache:table` / `queue:table`). Run `migrate` so those tables exist — otherwise login can fail with “Table '…cache' doesn't exist”.
4. **Demo data (optional)** — `php artisan db:seed` creates sample users and simulations (see seeder for emails; password is `password`).
5. **Frontend assets** — this project uses Vite. On the server (or CI), run **`npm ci`** then **`npm run build`** so `public/build` contains compiled CSS/JS. `php artisan serve` alone does not build assets; for local dev you can use `npm run dev` *or* run `npm run build` once and rely on built files.
6. **Document root** — point the domain to the `public/` folder.
7. **Email** — set `MAIL_MAILER=smtp` and cPanel SMTP (`MAIL_HOST`, `MAIL_PORT`, usually `465` SSL or `587` TLS, plus `MAIL_USERNAME` / `MAIL_PASSWORD`). With `MAIL_MAILER=log`, messages are written to the log only (good for debugging).

### Email: what to set in `.env` so it really works

| Variable | Purpose |
|----------|---------|
| **`APP_URL`** | **Required for links inside emails** (sign-in, password reset). Must be the public site URL, e.g. `https://yourdomain.com` (no trailing slash). Wrong value = broken links in messages. |
| **`MAIL_MAILER`** | Use `smtp` for real delivery; `log` only writes to `storage/logs`. |
| **`MAIL_HOST`**, **`MAIL_PORT`** | From cPanel → *Email Accounts* → *Connect Devices* / SMTP settings. |
| **`MAIL_USERNAME`**, **`MAIL_PASSWORD`** | Full mailbox email and its password. |
| **`MAIL_ENCRYPTION`** | Often `tls` on port **587**, or `ssl` on **465** (match host instructions). |
| **`MAIL_FROM_ADDRESS`** | Should be an address that exists on your domain (reduces spam flags). |
| **`MAIL_FROM_NAME`** | Display name (often same as `APP_NAME`). |

**Quick test:** `php artisan mail:test your@email.com` — if `MAIL_MAILER=log`, check `storage/logs/laravel.log` for the message body; if `smtp`, check your inbox.

---

Local dev (example):

```bash
php artisan serve --host=127.0.0.1 --port=8080
```

**Demo accounts** (after `php artisan db:seed`): `demo@school.demo` student (with sample simulations), `admin@school.demo` admin, `test@example.com` — password **`password`**. Change or remove these in production.

---

admin remote
Email: admin@example.com
Password: School


BUGS:

The tutorial blackends everything, but the "Simulation tutorial" even tho its trying to highlight the parts where it wants to show you. And the tutorial ends after the 4 steps of "Simulation tutorial" which should continue on by telling how to create the simulation and what each one does.

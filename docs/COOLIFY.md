# Coolify izvietošana

Coolify **aizstāj** konteinera `.env` ar vides mainīgajiem no paneļa. Repozitorijā esošais `.env` **netiek** izmantots ražošanā. Jaunākās izmaiņas redzamas tikai pēc **git push** un veiksmīga **redeploy** (build + start).

## Kas notiek deploy laikā

1. **Install** (`nixpacks.toml`): `composer install`, `npm ci`, `npm run build` — Vite saliek `public/build/` **serverī**.
2. **Start**: migrācijas, `storage:link`, nginx/php-fpm.

`public/build/` ir `.gitignore` — **nav jācommitē** build mapi. Pietiek ar `resources/js`, `resources/css`, Blade un `resources/lang`.

## Obligātie mainīgie Coolify UI

Iestatiet **Environment** (nevis repo `.env`):

| Mainīgais | Piemērs / piezīme |
|-----------|-------------------|
| `APP_NAME` | Investify |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:...` (vienreiz: `php artisan key:generate --show` lokāli) |
| `APP_DEBUG` | `false` |
| `APP_URL` | Pilna publiskā URL, **bez** slīpsvītras beigās, piem. `https://investify.example.com` |
| `DB_*` | MySQL/MariaDB no Coolify datubāzes |
| `SESSION_DRIVER` | `database` (noklusējums projektā) |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |

## Ieteicamie mainīgie

| Mainīgais | Ieteikums |
|-----------|-----------|
| `APP_LOCALE` | `lv` (noklusējums kodā arī `lv`, ja nav iestatīts) |
| `APP_FALLBACK_LOCALE` | `en` |
| `BOOTSTRAP_DEFAULT_ACCOUNTS` | `true` tikai demo/klasei — izveido `admin@school.com` / `user@user.com` pēc migrate |
| `MAIL_*` | SMTP, ja vajag paroles atjaunošanu e-pastā |
| `HASH_VERIFY` | `true` (ja login kļūda par Bcrypt, skat. `.env.example`) |

Pēc `APP_URL`, `APP_KEY` vai `APP_LOCALE` maiņas Coolify panelī — **redeploy**, lai start skripts notīrītu kešatmiņu (skat. zemāk).

## Pēc push — pārbaude

1. Coolify **Build logs**: jābūt `npm run build` bez kļūdām.
2. **Deploy logs**: `php artisan migrate`, nginx start.
3. Pārlūkā **cietā pārlāde** (Ctrl+Shift+R) — jaunie CSS/JS faili ir ar jaunu hash (`public/build/manifest.json`).

## Lokālā izstrāde vs Coolify

| | Laragon (lokāli) | Coolify |
|--|------------------|---------|
| `.env` | Jūsu fails | Tikai panela mainīgie |
| Frontend | `npm run dev` vai `npm run build` | Tikai `npm run build` deploy laikā |
| Blade/CSS/JS izmaiņas | Uzreiz (vai pēc build) | Tikai pēc **push + redeploy** |

Ja testējat **tikai** ražošanas URL, lokālās izmaiņas neredzēsiet, kamēr neesat pushējis.

## Ja izmaiņas “neuzķeras”

1. Pārliecinieties, ka push ir uz pareizo branch (Coolify seko `main` vai jūsu branch).
2. Build logā meklējiet `vite v` / `built in`.
3. Pārlūka kešatmiņa / inkognito logs.
4. Ja kadreiz bijis `php artisan config:cache` ārpus šī start skripta — redeploy (start izpilda `optimize:clear`).

## E-pasts un saites

`APP_URL` **jāatbilst** faktiskajai domēna adresei. Nepareizs `APP_URL` bojā saites e-pastos un dažreiz asset ceļus.

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
7. Precīzi atdalīti frontend (`src/`) un backend (`api/`) ceļi vienā projektā.

php artisan serve --host=127.0.0.1 --port=8080

npm run dev

admin remote
Email: admin@example.com
Password: YourSecurePassword123!


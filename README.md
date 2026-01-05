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

php artisan serve --host=127.0.0.1 --port=8080

npm run dev

admin remote
Email: admin@example.com
Password: YourSecurePassword123!



Combine the simulation page and dashboard together.

while the simulation needs to give the 


Combine the Dashboard and simulation pages together. 
While also when a user clickes on a simulation, i will need you to rework visual look of the page to make it where the graph is in the front side, while everything stays on the sides and nor below the graph itself.
"Simulation Parameters" staying somewhere below.

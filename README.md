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


admin remote
Email: admin@example.com
Password: School


BUGS:
In the simulation creation page the buttonse that increase the numbers leave holding the button for too long it increases to a full number but leaves a 0.4. The timing is a bit off, it should only increase when its a 0 Then it should increase into a +1.0

The tutorial blackends everything, but the "Simulation tutorial" even tho its trying to highlight the parts where it wants to show you. And the tutorial ends after the 4 steps of "Simulation tutorial" which should continue on by telling how to create the simulation and what each one does.

When someone looses their 2FA there is no real way to gain access to it. Will create a gmail for a bot to handle those things of sending emails and responses.
Admins shouldn't be able to change the password themselves, but other buttons to disable 2FA and to ask the user to change their passwords. But only would work with gmail sender would work.
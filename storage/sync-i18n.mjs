import fs from 'fs';

const enPath = 'resources/lang/en.json';
const lvPath = 'resources/lang/lv.json';

const en = JSON.parse(fs.readFileSync(enPath, 'utf8'));
const lv = JSON.parse(fs.readFileSync(lvPath, 'utf8'));

/** English source text → Latvian (keys missing from one or both files). */
const lvByEn = {
    '(max 30 characters)': '(maks. 30 rakstzīmes)',
    'A new verification link has been sent to the email address you provided during registration.':
        'Jauna apstiprinājuma saite ir nosūtīta uz reģistrācijas laikā norādīto e-pasta adresi.',
    'Add exact emails or domains to prevent sign-up and sign-in. Example: block "spam@example.com" or domain "example.com".':
        'Pievieno precīzas e-pasta adreses vai domēnus, lai bloķētu reģistrāciju un pieteikšanos. Piemēram: bloķēt „spam@example.com” vai domēnu „example.com”.',
    'Administrators cannot change a user\'s email address.':
        'Administratori nevar mainīt lietotāja e-pasta adresi.',
    'Annual Growth Rate': 'Gada pieauguma likme',
    'Annual inflation percentage. Used to show the real (inflation-adjusted) value so you can compare purchasing power over time.':
        'Gada inflācijas procents. Izmanto reālās (pēc inflācijas koriģētās) vērtības rādīšanai, lai salīdzinātu pirktspēju laika gaitā.',
    'Arrow tip: under the big numbers, <span style="color:var(--c-primary); font-weight:800;">green ↑</span> means that number went up since the previous step, and <span style="color:#dc2626; font-weight:800;">red ↓</span> means it went down. The chart legend lines are separate.':
        'Padoms: zem lielajiem skaitļiem <span style="color:var(--c-primary); font-weight:800;">zaļš ↑</span> nozīmē, ka skaitlis pieauga kopš iepriekšējā soļa, un <span style="color:#dc2626; font-weight:800;">sarkans ↓</span> — ka samazinājās. Diagrammas leģendas līnijas ir atsevišķas.',
    'Balanced (default)': 'Līdzsvarots (noklusējums)',
    'Change password': 'Mainīt paroli',
    'Choose a new password to secure your account.': 'Izvēlies jaunu paroli, lai aizsargātu kontu.',
    'Choppy & volatile': 'Svārstīgs un nemierīgs',
    'Confirm': 'Apstiprināt',
    'Confirm Password': 'Apstiprināt paroli',
    'Confirm new password': 'Apstiprināt jauno paroli',
    'Controls & Parameters': 'Vadība un parametri',
    'Currency saved.': 'Valūta saglabāta.',
    'Current password': 'Pašreizējā parole',
    'Decrease': 'Samazināt',
    'Defensive / Bearish': 'Aizsargājošs / lāču',
    'Disabling 2FA clears the user\'s secret so they can sign in again without an authenticator code.':
        '2FA atslēgšana dzēš lietotāja noslēpumu, lai viņš varētu pierakstīties bez autentifikatora koda.',
    'Email Password Reset Link': 'Paroles atiestatīšanas saite e-pastā',
    'Email address cannot be changed.': 'E-pasta adresi nevar mainīt.',
    'Expected annual return in percent (0-100). Example: 7% annual growth. Higher rates raise average returns but also interact with volatility.':
        'Gaidāmā gada atdeve procentos (0–100). Piemēram: 7% gada pieaugums. Augstākas likmes palielina vidējo atdevi, bet arī ietekmē svārstīgumu.',
    'Explain Growth Rate': 'Skaidrojums: pieauguma likme',
    'Explain Inflation Rate': 'Skaidrojums: inflācijas likme',
    'Explain Initial Investment': 'Skaidrojums: sākuma investīcija',
    'Explain Investors': 'Skaidrojums: investoru skaits',
    'Explain Market Influence': 'Skaidrojums: tirgus ietekme',
    'Explain Monthly Contribution': 'Skaidrojums: ikmēneša iemaksa',
    'Explain Risk Appetite': 'Skaidrojums: riska apetīte',
    'Forgot Password': 'Aizmirsi paroli?',
    'Get started': 'Sākt',
    'Growth / Bullish': 'Izaugsmes / vēršu',
    'Growth Rate (annual)': 'Pieauguma likme (gada)',
    'Growth Rate (annual, % )': 'Pieauguma likme (gada, %)',
    'Hero': 'Sākums',
    'How much external market behavior affects your simulation (0-100%). Higher values amplify crowd waves and shocks, making paths more dynamic.':
        'Cik lielā mērā ārējais tirgus ietekmē simulāciju (0–100%). Augstākas vērtības pastiprina pūļa viļņus un šokus.',
    'How much volatility you are comfortable with (0-100%). Higher values mean bigger swings up and down.':
        'Cik lielu svārstīgumu tolerē (0–100%). Augstākas vērtības nozīmē lielākus kritumus un kāpumus.',
    'Increase': 'Palielināt',
    'Inflation Rate': 'Inflācijas likme',
    'Inflation Rate (annual)': 'Inflācijas likme (gada)',
    'Inflation Rate (annual, % )': 'Inflācijas likme (gada, %)',
    'Investment Growth Over Time': 'Investīciju izaugsme laika gaitā',
    'Investment Simulation': 'Investīciju simulācija',
    'Investors (count)': 'Investoru skaits',
    'Login': 'Pieteikties',
    'Many investors (crowd) are active: profit-taking and panic waves can amplify swings.':
        'Daudzi investori (pūlis) ir aktīvi: peļņas fiksēšana un panikas viļņi var pastiprināt svārstības.',
    'Market Influence': 'Tirgus ietekme',
    'Market Influence (%)': 'Tirgus ietekme (%)',
    'Market Regime': 'Tirgus režīms',
    'Max Drawdown': 'Maksimālais kritums',
    'Monthly Contribution': 'Ikmēneša iemaksa',
    'New password': 'Jaunā parole',
    'Password': 'Parole',
    'Password updated.': 'Parole atjaunināta.',
    'Please describe the issue you\'re experiencing...': 'Lūdzu, apraksti problēmu, ar kuru saskaries…',
    'Projected CAGR': 'Prognozētais CAGR',
    'Real Value (Inflation Adj.)': 'Reālā vērtība (pēc inflācijas)',
    'Register': 'Reģistrēties',
    'Resend Verification Email': 'Atkārtoti nosūtīt apstiprinājuma e-pastu',
    'Reset Password': 'Atiestatīt paroli',
    'Risk Appetite': 'Riska apetīte',
    'Risk Appetite (%)': 'Riska apetīte (%)',
    'Save': 'Saglabāt',
    'Save results and full monthly history to the server':
        'Saglabāt rezultātus un pilnu vēsturi serverī',
    'Sign in to report': 'Pieslēdzies, lai ziņotu',
    'Simulation': 'Simulācija',
    'Simulation Parameters': 'Simulācijas parametri',
    'Simulation actions': 'Simulācijas darbības',
    'Simulation chart': 'Simulācijas diagramma',
    'Simulation details': 'Simulācijas detaļas',
    'Stress test': 'Stresa tests',
    'Stress test (crash + recovery)': 'Stresa tests (kritums + atlabšana)',
    'Stress test simulates rare tail events and recovery. Use it to think about resilience, not timing.':
        'Stresa tests simulē retus ekstrēmus notikumus un atlabšanu. Domā par noturību, nevis laika izvēli.',
    'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.':
        'Paldies par reģistrāciju! Pirms sākt, lūdzu, apstiprini e-pasta adresi, noklikšķinot uz saites, ko nosūtījām. Ja e-pasts nav saņemts, nosūtīsim vēlreiz.',
    'This is a secure area of the application. Please confirm your password before continuing.':
        'Šī ir drošā lietotnes zona. Lūdzu, apstiprini paroli, pirms turpināt.',
    'Total Contributed': 'Kopā iemaksāts',
    'Update password': 'Atjaunināt paroli',
    'Verify Email': 'Apstiprināt e-pastu',
    'You': 'Tu',
    'You haven\'t submitted any support tickets yet.': 'Tu vēl neesi iesniedzis nevienu atbalsta pieteikumu.',
    'Your choice is saved on your account (not just this browser). Rates load from a public exchange API.':
        'Tava izvēle tiek saglabāta kontā (ne tikai šajā pārlūkā). Kursi tiek ielādēti no publiska valūtas API.',
    'Your display currency is synced to your account.': 'Tava attēlojamā valūta ir sinhronizēta ar kontu.',
    'Your password has been changed successfully.': 'Tava parole ir veiksmīgi nomainīta.',
    'Chart': 'Diagramma',
    'DoD': 'd/d',
    'Run': 'Palaist',
    'Pause': 'Pauze',
    'Sell part of holdings (priced at current day)': 'Pārdot daļu no pozīcijas (pēc šīs dienas cenas)',
    'Classic (auto daily)': 'Klasika (automātiski katru dienu)',
};

for (const [key, lvText] of Object.entries(lvByEn)) {
    if (!(key in en)) {
        en[key] = key;
    }
    if (!(key in lv)) {
        lv[key] = lvText;
    }
}

// Ensure EN-only keys that exist in LV get EN entries
for (const key of Object.keys(lv)) {
    if (!(key in en)) {
        en[key] = key;
    }
}

// Sort keys for stable diffs
const sortObj = (o) =>
    Object.fromEntries(Object.entries(o).sort(([a], [b]) => a.localeCompare(b)));

fs.writeFileSync(enPath, JSON.stringify(sortObj(en), null, 4) + '\n', 'utf8');
fs.writeFileSync(lvPath, JSON.stringify(sortObj(lv), null, 4) + '\n', 'utf8');
console.log('Synced', Object.keys(lvByEn).length, 'translation pairs.');

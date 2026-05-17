function clamp(n, min, max) {
  return Math.min(max, Math.max(min, n));
}

function fmtMoney(n) {
  const v = Number.isFinite(n) ? n : 0;
  return new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(v);
}

function mulberry32(seed) {
  let a = seed >>> 0;
  return function () {
    a |= 0;
    a = (a + 0x6d2b79f5) | 0;
    let t = Math.imul(a ^ (a >>> 15), 1 | a);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}

function randn(rng) {
  // Box–Muller nejaušo skaitļu ģenerēšana
  let u = 0;
  let v = 0;
  while (u === 0) u = rng();
  while (v === 0) v = rng();
  return Math.sqrt(-2.0 * Math.log(u)) * Math.cos(2.0 * Math.PI * v);
}

function simulatePath({ initial, monthly, months, meanMonthly, volMonthly, seed }) {
  const rng = mulberry32(seed);
  let value = initial;
  const series = new Array(months + 1);
  series[0] = value;

  for (let m = 1; m <= months; m += 1) {
    // Iemaksa mēneša sākumā, tad atdeve.
    value += monthly;
    const shock = randn(rng) * volMonthly;
    const r = meanMonthly + shock;
    value *= 1 + r;
    series[m] = value;
  }

  return series;
}

function pathToSvg(series, width, height) {
  const n = series.length;
  if (n <= 1) return '';

  let min = Infinity;
  let max = -Infinity;
  for (const v of series) {
    if (v < min) min = v;
    if (v > max) max = v;
  }
  if (!Number.isFinite(min) || !Number.isFinite(max) || max === min) {
    min = 0;
    max = 1;
  }

  const pad = 4;
  const xStep = width / (n - 1);
  const scaleY = (height - pad * 2) / (max - min);

  let d = '';
  for (let i = 0; i < n; i += 1) {
    const x = i * xStep;
    const y = height - pad - (series[i] - min) * scaleY;
    d += i === 0 ? `M ${x.toFixed(2)} ${y.toFixed(2)}` : ` L ${x.toFixed(2)} ${y.toFixed(2)}`;
  }
  return d;
}

function percentile(values, p) {
  if (!values.length) return 0;
  const sorted = [...values].sort((a, b) => a - b);
  const idx = (sorted.length - 1) * p;
  const lo = Math.floor(idx);
  const hi = Math.ceil(idx);
  if (lo === hi) return sorted[lo];
  const w = idx - lo;
  return sorted[lo] * (1 - w) + sorted[hi] * w;
}

function getRegime(name) {
  // Ilustratīvi mēneša parametri (nav tirgus dati)
  switch (name) {
    case 'growth':
      return { meanMonthly: 0.008, volMonthly: 0.035 };
    case 'defensive':
      return { meanMonthly: 0.004, volMonthly: 0.018 };
    case 'volatile':
      return { meanMonthly: 0.006, volMonthly: 0.055 };
    case 'stress':
      return { meanMonthly: 0.003, volMonthly: 0.08 };
    case 'balanced':
    default:
      return { meanMonthly: 0.006, volMonthly: 0.028 };
  }
}

function computeMiniSim(root) {
  const initial = clamp(Number(root.querySelector('[name="initial"]')?.value ?? 0), 0, 1e9);
  const monthly = clamp(Number(root.querySelector('[name="monthly"]')?.value ?? 0), 0, 1e9);
  const years = clamp(Number(root.querySelector('[name="years"]')?.value ?? 10), 1, 50);
  const regimeName = String(root.querySelector('[name="regime"]')?.value ?? 'balanced');
  const months = Math.round(years * 12);

  const { meanMonthly, volMonthly } = getRegime(regimeName);

  // Galvenā līnija ir vairāku skrējienu mediāna, lai pirmais iespaids neizskatītos pārāk nejaušs.
  const runs = 30;
  const endings = [];
  const paths = [];
  const baseSeed = (months * 13 + Math.floor(initial) + Math.floor(monthly) * 7) >>> 0;
  for (let i = 0; i < runs; i += 1) {
    const series = simulatePath({
      initial,
      monthly,
      months,
      meanMonthly,
      volMonthly,
      seed: (baseSeed + i * 9973) >>> 0,
    });
    paths.push(series);
    endings.push(series[series.length - 1]);
  }

  const endMedian = percentile(endings, 0.5);
  const endLow = percentile(endings, 0.2);
  const endHigh = percentile(endings, 0.8);
  const totalContrib = initial + monthly * months;

  const outEnd = root.querySelector('[data-mini-end]');
  const outContrib = root.querySelector('[data-mini-contrib]');
  const outRange = root.querySelector('[data-mini-range]');
  if (outEnd) outEnd.textContent = fmtMoney(endMedian);
  if (outContrib) outContrib.textContent = fmtMoney(totalContrib);
  if (outRange) outRange.textContent = `${fmtMoney(endLow)} – ${fmtMoney(endHigh)}`;

  // Veido reprezentatīvus ceļus: zems/medians/augsts pēc gala vērtību rangēšanas
  const ranked = paths
    .map((p, idx) => ({ p, end: endings[idx] }))
    .sort((a, b) => a.end - b.end);
  const pick = (q) => ranked[Math.round((ranked.length - 1) * q)]?.p ?? ranked[0]?.p ?? [0, 1];

  const sLow = pick(0.2);
  const sMid = pick(0.5);
  const sHigh = pick(0.8);

  const main = root.querySelector('[data-mini-path]');
  const low = root.querySelector('[data-mini-path-low]');
  const high = root.querySelector('[data-mini-path-high]');

  const w = 240;
  const h = 56;
  if (main) main.setAttribute('d', pathToSvg(sMid, w, h));
  if (low) low.setAttribute('d', pathToSvg(sLow, w, h));
  if (high) high.setAttribute('d', pathToSvg(sHigh, w, h));
}

function attachMiniSim() {
  const root = document.querySelector('[data-mini-sim]');
  if (!root) return;

  const onChange = () => computeMiniSim(root);
  root.addEventListener('input', onChange);
  root.addEventListener('change', onChange);

  computeMiniSim(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', attachMiniSim);
} else {
  attachMiniSim();
}


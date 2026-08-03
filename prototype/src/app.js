/* ============================================================
   অ্যাপ্লিকেশন লজিক — প্রফেসর ডা. আবু সুফিয়ান
   ------------------------------------------------------------
   এই প্রোটোটাইপে কনটেন্ট ব্রাউজারে রেন্ডার হয়, যাতে
   "ডাইনামিক" আচরণটা দেখা যায় — data.js-এ আইটেম যোগ/বিয়োগ
   করলেই লেআউট নিজে থেকে মানিয়ে নেয়।

   ⚠️ Laravel-এ এই রেন্ডারিং সার্ভার-সাইডে (Blade @foreach) হবে,
   কারণ গুগল সার্চে ভালো ফলের জন্য HTML সার্ভার থেকেই আসা দরকার।
   ============================================================ */

import { SETTINGS, CHAMBERS, SCHEDULES, HOLIDAYS, NOTICES, EXPERIENCES,
         QUALIFICATIONS, SERVICES, SPECIAL, GALLERY, TESTIMONIALS,
         FAQS, ICONS } from './data.js';
import { I18N } from './i18n.js';

/* ============================================================
   ১. অবস্থা ও সহায়ক ফাংশন
   ============================================================ */

let lang = localStorage.getItem('lang') || 'bn';

/* কোন পেজে আছি — প্রতিটি HTML ফাইলের <body data-page="..."> থেকে।
   একই app.js সব পেজে চলে; যে সেকশনের কনটেইনার পেজে নেই, সেটি এড়িয়ে যায়। */
const PAGE = document.body.dataset.page || 'home';

/* সাইটম্যাপ — নতুন পেজ যোগ করতে হলে শুধু এখানে একটি সারি বাড়াতে হবে।
   inNav: হেডার/মোবাইল মেনুতে দেখাবে কি না। */
const PAGES = [
  { id: 'home',         file: 'index.html',        key: 'nav.home',         inNav: true  },
  { id: 'about',        file: 'about.html',        key: 'nav.about',        inNav: true  },
  { id: 'services',     file: 'services.html',     key: 'nav.services',     inNav: true  },
  { id: 'chamber',      file: 'chamber.html',      key: 'nav.chamber',      inNav: true  },
  { id: 'booking',      file: 'booking.html',      key: 'nav.appointment',  inNav: true  },
  { id: 'gallery',      file: 'gallery.html',      key: 'nav.gallery',      inNav: true  },
  { id: 'faq',          file: 'faq.html',          key: 'nav.faq',          inNav: true  },
  { id: 'contact',      file: 'contact.html',      key: 'nav.contact',      inNav: true  },
  { id: 'testimonials', file: 'testimonials.html', key: 'nav.testimonials', inNav: false },
];

const pageOf = (id) => PAGES.find((p) => p.id === id);
const url    = (id) => pageOf(id).file;

const t = (key) => I18N[lang][key] ?? key;
const L = (obj) => (obj && typeof obj === 'object' ? (obj[lang] || obj.bn || '') : obj || '');

const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
  ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* বাংলা অঙ্ক — ভাষা বাংলা হলে ০-৯ বাংলা হরফে দেখাবে */
const BN_DIGITS = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
const num = (v) => lang === 'bn'
  ? String(v).replace(/\d/g, (d) => BN_DIGITS[+d])
  : String(v);

const BN_MONTHS = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন',
                   'জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
const EN_MONTHS = ['January','February','March','April','May','June',
                   'July','August','September','October','November','December'];
const BN_DAYS   = ['রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
const EN_DAYS   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
const BN_DAYS_S = ['রবি','সোম','মঙ্গল','বুধ','বৃহঃ','শুক্র','শনি'];
const EN_DAYS_S = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

const monthName = (m) => (lang === 'bn' ? BN_MONTHS : EN_MONTHS)[m];
const dayName   = (d) => (lang === 'bn' ? BN_DAYS   : EN_DAYS)[d];
const dayShort  = (d) => (lang === 'bn' ? BN_DAYS_S : EN_DAYS_S)[d];

const fmtDate = (d) => lang === 'bn'
  ? `${num(d.getDate())} ${monthName(d.getMonth())} ${num(d.getFullYear())}`
  : `${d.getDate()} ${monthName(d.getMonth())} ${d.getFullYear()}`;

/* "14:30" → বাংলা: "দুপুর ২:৩০"  |  English: "2:30 PM" */
function fmtTime(hhmm) {
  const [H, M] = hhmm.split(':').map(Number);
  const h12 = H % 12 === 0 ? 12 : H % 12;
  const mm = String(M).padStart(2, '0');
  if (lang !== 'bn') return `${h12}:${mm} ${H < 12 ? 'AM' : 'PM'}`;
  let part;
  if (H < 4)       part = 'রাত';
  else if (H < 6)  part = 'ভোর';
  else if (H < 12) part = 'সকাল';
  else if (H < 15) part = 'দুপুর';
  else if (H < 18) part = 'বিকাল';
  else if (H < 20) part = 'সন্ধ্যা';
  else             part = 'রাত';
  return `${part} ${num(h12)}:${num(mm)}`;
}

const toMin   = (hhmm) => { const [h, m] = hhmm.split(':').map(Number); return h * 60 + m; };
const fromMin = (mins) => `${String(Math.floor(mins / 60)).padStart(2, '0')}:${String(mins % 60).padStart(2, '0')}`;
const ymd     = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

/* SVG আইকন */
const icon = (name, cls = 'w-6 h-6') =>
  `<svg class="${cls}" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
        aria-hidden="true">${ICONS[name] || ICONS.stetho}</svg>`;

/* আইকন বাবলের রঙ — পূর্ণ ক্লাস নাম, যাতে Tailwind ধরতে পারে */
const TONES = {
  sky:     'bg-sky-50 text-sky-600',
  rose:    'bg-rose-50 text-rose-600',
  cyan:    'bg-cyan-50 text-cyan-600',
  amber:   'bg-amber-50 text-amber-600',
  green:   'bg-green-50 text-green-600',
  lime:    'bg-lime-50 text-lime-600',
  violet:  'bg-violet-50 text-violet-600',
  indigo:  'bg-indigo-50 text-indigo-600',
  teal:    'bg-teal-50 text-teal-600',
  blue:    'bg-blue-50 text-blue-600',
  orange:  'bg-orange-50 text-orange-600',
  red:     'bg-red-50 text-red-600',
  emerald: 'bg-emerald-50 text-emerald-600',
  brand:   'bg-brand-50 text-brand-700',
};
const tone = (k) => TONES[k] || TONES.brand;

/* কনটেন্ট এখনো যোগ হয়নি এমন পেজের জন্য খালি-অবস্থার কার্ড।
   হোমে ওই সেকশন লুকানোই থাকে, কিন্তু নিজের পেজ ফাঁকা রাখা যায় না। */
const emptyState = (ic, eyebrowKey, titleKey, msgKey) => `
  <div class="container-x">
    <div class="section-head">
      <p class="eyebrow">${esc(t(eyebrowKey))}</p>
      <h2 class="section-title">${esc(t(titleKey))}</h2>
    </div>
    <div class="card p-10 text-center max-w-xl mx-auto">
      <span class="icon-bubble bg-brand-50 text-brand-300 !w-14 !h-14 mx-auto !rounded-2xl">
        ${icon(ic, 'w-7 h-7')}</span>
      <p class="mt-4 text-slate-500 text-[0.95rem] leading-relaxed">${esc(t(msgKey))}</p>
    </div>
  </div>`;

/* ============================================================
   ২. সময়সূচি ও স্লট
   ============================================================ */

const scheduleFor = (date) =>
  SCHEDULES.find((s) => s.is_active && s.days.includes(date.getDay())) || null;

const holidayFor = (date) =>
  HOLIDAYS.find((h) => h.date === ymd(date)) || null;

/** একটি দিনের সব স্লট তৈরি করে — শুরুর সময় থেকে slot_minutes অন্তর */
function buildSlots(sch) {
  const out = [];
  const start = toMin(sch.start), end = toMin(sch.end);
  for (let i = 0; i < sch.max_serials; i++) {
    const at = start + i * sch.slot_minutes;
    if (at + sch.slot_minutes > end) break;
    out.push({ serial: i + 1, time: fromMin(at) });
  }
  return out;
}

/* ডেমো: কোন স্লটগুলো ইতিমধ্যে বুক হয়ে আছে।
   তারিখ থেকে হিসাব করা হয় যাতে রিফ্রেশ করলেও একই থাকে।
   Laravel-এ এটি `appointments` টেবিলের প্রকৃত কোয়েরি হবে। */
function hash32(str) {
  let h = 2166136261 >>> 0;                      // FNV-1a
  for (let i = 0; i < str.length; i++) {
    h ^= str.charCodeAt(i);
    h = Math.imul(h, 16777619) >>> 0;
  }
  h ^= h >>> 16; h = Math.imul(h, 2246822507) >>> 0;   // অ্যাভাল্যাঞ্চ —
  h ^= h >>> 13; h = Math.imul(h, 3266489909) >>> 0;   // নইলে পরপর তারিখে
  h ^= h >>> 16;                                       // পরপর সংখ্যা আসে
  return h >>> 0;
}

function takenSlots(dateStr, total) {
  const h = hash32(dateStr);
  /* মাঝে মাঝে দিনটি পুরো ভরে যাক — "সিরিয়াল শেষ" অবস্থাটিও যেন দেখা যায় */
  const count = Math.min(total, h % (total + 5));
  const set = new Set();
  let x = h;
  while (set.size < count) {
    x = hash32(String(x));
    set.add((x % total) + 1);
  }
  return set;
}

/** একটি তারিখের অবস্থা: বন্ধ / পূর্ণ / খালি আছে */
function dayState(date) {
  const today = new Date(); today.setHours(0, 0, 0, 0);
  const d = new Date(date); d.setHours(0, 0, 0, 0);

  if (d < today) return { status: 'past' };
  if (holidayFor(date)) return { status: 'holiday', reason: holidayFor(date).reason };

  const sch = scheduleFor(date);
  if (!sch) return { status: 'closed' };

  const slots = buildSlots(sch);
  const taken = takenSlots(ymd(date), slots.length);

  /* আজকের দিনে যে স্লটগুলোর সময় পেরিয়ে গেছে সেগুলো বাদ */
  const isToday = d.getTime() === today.getTime();
  const nowMin  = new Date().getHours() * 60 + new Date().getMinutes();

  const open = slots.filter((s) =>
    !taken.has(s.serial) && (!isToday || toMin(s.time) > nowMin + 30));

  return { status: open.length ? 'open' : 'full', sch, slots, taken, open, isToday, nowMin };
}

/* ============================================================
   ৩. সেকশন রেন্ডারার
   ============================================================ */

function renderNotice() {
  const el = document.getElementById('notice-bar');
  const n = NOTICES.find((x) => x.is_active);
  if (!el) return;
  if (!n || sessionStorage.getItem('noticeClosed')) { el.innerHTML = ''; el.hidden = true; return; }
  el.hidden = false;
  el.innerHTML = `
    <div class="bg-sky2-500 text-white">
      <div class="container-x flex items-center gap-3 py-2.5 text-sm">
        <span class="shrink-0 hidden sm:inline-flex">${icon('clock', 'w-4 h-4')}</span>
        <p class="flex-1 leading-snug">
          <strong class="font-semibold">${esc(L(n.title))}</strong>
          <span class="opacity-90 hidden md:inline"> — ${esc(L(n.body))}</span>
        </p>
        <button id="notice-close" class="shrink-0 rounded p-1 hover:bg-white/20"
                aria-label="${esc(t('common.close'))}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
    </div>`;
  document.getElementById('notice-close').onclick = () => {
    sessionStorage.setItem('noticeClosed', '1');
    el.hidden = true; el.innerHTML = '';
  };
}

function renderHeader() {
  const nav = PAGES.filter((p) => p.inNav);
  /* চলতি পেজের লিংক আলাদা রঙে — ব্যবহারকারী যেন বুঝতে পারে কোথায় আছে */
  const isOn = (p) => p.id === PAGE;

  document.getElementById('site-header').innerHTML = `
    <div class="container-x">
      <div class="flex items-center justify-between gap-4 h-[4.5rem]">

        <a href="${url('home')}" class="flex items-center gap-2.5 min-w-0">
          <span class="grid place-items-center w-10 h-10 rounded-xl bg-brand-900 text-white shrink-0">
            ${icon('stetho', 'w-5 h-5')}
          </span>
          <span class="min-w-0">
            <span class="block font-bold text-brand-900 leading-tight truncate text-[0.95rem]">
              ${esc(L(SETTINGS.doctor_short))}</span>
            <span class="block text-[0.7rem] text-slate-500 leading-tight truncate">
              ${esc(L(SETTINGS.degrees))}</span>
          </span>
        </a>

        <nav class="hidden xl:flex items-center gap-0.5" aria-label="${esc(t('nav.menu'))}">
          ${nav.map((p) => `
            <a href="${p.file}" ${isOn(p) ? 'aria-current="page"' : ''}
               class="px-2.5 py-2 rounded-lg text-sm font-medium transition
                      ${isOn(p) ? 'text-brand-900 bg-brand-50 font-semibold'
                                : 'text-slate-600 hover:text-brand-900 hover:bg-brand-50'}">
              ${esc(t(p.key))}</a>`).join('')}
        </nav>

        <div class="flex items-center gap-2">
          <button id="lang-toggle"
                  class="px-2.5 py-1.5 rounded-lg border border-brand-100 text-xs font-bold
                         text-brand-900 hover:bg-brand-50 transition"
                  aria-label="Switch language">
            ${lang === 'bn' ? 'EN' : 'বাং'}
          </button>
          <a href="${url('booking')}" class="btn btn-primary hidden sm:inline-flex !px-4 !py-2.5 !text-sm">
            ${icon('clock', 'w-4 h-4')} ${esc(t('nav.book'))}
          </a>
          <button id="menu-toggle" class="xl:hidden p-2 rounded-lg hover:bg-brand-50"
                  aria-label="${esc(t('nav.menu'))}" aria-expanded="false">
            <svg class="w-6 h-6 text-brand-900" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M3 6h18M3 12h18M3 18h18"/></svg>
          </button>
        </div>
      </div>

      <nav id="mobile-nav" class="xl:hidden hidden border-t border-brand-100 py-2">
        ${nav.map((p) => `
          <a href="${p.file}" ${isOn(p) ? 'aria-current="page"' : ''}
             class="block px-3 py-2.5 rounded-lg text-sm font-medium
                    ${isOn(p) ? 'text-brand-900 bg-brand-50 font-semibold'
                              : 'text-slate-700 hover:bg-brand-50'}">${esc(t(p.key))}</a>`).join('')}
        <a href="${url('testimonials')}" ${PAGE === 'testimonials' ? 'aria-current="page"' : ''}
           class="block px-3 py-2.5 rounded-lg text-sm font-medium
                  ${PAGE === 'testimonials' ? 'text-brand-900 bg-brand-50 font-semibold'
                                            : 'text-slate-700 hover:bg-brand-50'}">
          ${esc(t('nav.testimonials'))}</a>
      </nav>
    </div>`;

  document.getElementById('lang-toggle').onclick = () => {
    lang = lang === 'bn' ? 'en' : 'bn';
    localStorage.setItem('lang', lang);
    document.documentElement.lang = lang;
    renderAll();
  };
  const mt = document.getElementById('menu-toggle');
  const mn = document.getElementById('mobile-nav');
  mt.onclick = () => {
    const open = mn.classList.toggle('hidden');
    mt.setAttribute('aria-expanded', String(!open));
  };
  mn.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => {
    mn.classList.add('hidden'); mt.setAttribute('aria-expanded', 'false');
  }));
}

function renderHero() {
  const trust = [
    ['award', 'hero.trust1'],
    ['flask', 'hero.trust2'],
    ['heart', 'hero.trust3'],
  ];
  const el = document.getElementById('hero');
  if (!el) return;
  el.innerHTML = `
    <div class="relative overflow-hidden bg-brand-900">
      <div class="absolute inset-0 opacity-90
                  bg-[radial-gradient(48rem_28rem_at_78%_18%,#2e8bc0_0%,transparent_62%),radial-gradient(40rem_24rem_at_8%_92%,#12264a_0%,transparent_58%)]"></div>

      <div class="container-x relative py-12 md:py-20">
        <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-10 lg:gap-14 items-center">

          <div class="text-white">
            <p class="eyebrow !bg-white/15 !text-sky2-100">
              ${icon('stetho', 'w-3.5 h-3.5')} ${esc(L(SETTINGS.specialty))}
            </p>

            <h1 class="text-3xl md:text-5xl !text-white font-extrabold tracking-tight text-balance-x">
              ${esc(L(SETTINGS.doctor_name))}
            </h1>

            <p class="mt-3 text-sky2-100 text-base md:text-lg font-medium">
              ${esc(L(SETTINGS.degrees))}
            </p>
            <p class="mt-1.5 text-white/70 text-sm md:text-base">
              ${esc(L(SETTINGS.designation))}
              <span class="mx-2 opacity-40">•</span>
              ${esc(t('hero.bmdc'))} ${esc(L(SETTINGS.bmdc))}
            </p>

            <p class="mt-5 text-white/85 leading-relaxed max-w-xl text-[0.97rem]">
              ${esc(L(SETTINGS.tagline))}
            </p>

            <div class="mt-7 flex flex-wrap gap-3">
              <a href="${url('booking')}" class="btn btn-wa">
                ${icon('clock', 'w-4.5 h-4.5')} ${esc(t('common.bookNow'))}
              </a>
              <a href="tel:${SETTINGS.hotline}" class="btn btn-ghost">
                ${icon('phone', 'w-4.5 h-4.5')} ${esc(t('common.call'))} · ${num(SETTINGS.hotline)}
              </a>
            </div>

            <dl class="mt-9 grid sm:grid-cols-3 gap-3 max-w-2xl">
              ${trust.map(([ic, key]) => `
                <div class="flex items-center gap-2.5 rounded-xl bg-white/10 backdrop-blur
                            px-3.5 py-3 border border-white/15">
                  <span class="text-sky2-200 shrink-0">${icon(ic, 'w-5 h-5')}</span>
                  <dt class="text-[0.8rem] text-white/90 leading-snug">${esc(t(key))}</dt>
                </div>`).join('')}
            </dl>
          </div>

          <div class="relative">
            <div class="relative mx-auto max-w-sm">
              <div class="absolute -inset-4 bg-sky2-400/20 rounded-[2rem] blur-2xl"></div>
              <div class="relative aspect-square rounded-[1.75rem] overflow-hidden
                          bg-gradient-to-br from-white/95 to-sky2-50 border-4 border-white/25
                          shadow-2xl grid place-items-center">
                <img src="assets/images/doctor.jpg" alt="${esc(L(SETTINGS.doctor_name))}"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="hidden absolute inset-0 flex-col items-center justify-center
                            gap-3 text-brand-300 p-6 text-center">
                  ${icon('users', 'w-16 h-16')}
                  <p class="text-xs font-medium text-brand-400">${esc(t('hero.photoNote'))}</p>
                  <p class="text-[0.65rem] text-brand-300 font-mono">assets/images/doctor.jpg</p>
                </div>
              </div>

              <div class="absolute -bottom-4 -left-2 sm:left-2 bg-white rounded-xl shadow-lg
                          px-4 py-2.5 border border-brand-100 flex items-center gap-2.5">
                <span class="grid place-items-center w-8 h-8 rounded-lg bg-wa-500/10 text-wa-700">
                  ${icon('pin', 'w-4 h-4')}</span>
                <div class="leading-tight">
                  <p class="text-[0.7rem] text-slate-500">${esc(t('chm.eyebrow'))}</p>
                  <p class="text-[0.8rem] font-bold text-brand-900">
                    ${lang === 'bn' ? 'ইবনে সিনা, বাড্ডা' : 'Ibn Sina, Badda'}</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>`;
}

function renderAbout() {
  const el = document.getElementById('about');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-10 items-start">
        <div>
          <p class="eyebrow">${icon('users', 'w-3.5 h-3.5')} ${esc(t('about.eyebrow'))}</p>
          <h2 class="section-title">${esc(t('about.title'))}</h2>
          <p class="mt-3 text-[1.05rem] font-semibold text-sky2-700 leading-snug">
            ${esc(t('about.lead'))}</p>
          <p class="mt-3 text-slate-600 leading-relaxed">${esc(L(SETTINGS.intro))}</p>

          ${SETTINGS.show_fees ? `
            <div class="mt-6 card p-4">
              <p class="text-sm font-bold text-brand-900 mb-2">
                ${lang === 'bn' ? 'ভিজিট ফি' : 'Consultation Fee'}</p>
              <ul class="text-sm text-slate-600 space-y-1">
                <li>${lang === 'bn' ? 'নতুন রোগী' : 'New patient'}:
                    <strong>${num(SETTINGS.fee_new)}${lang === 'bn' ? ' টাকা' : ' BDT'}</strong></li>
                <li>${lang === 'bn' ? 'ফলো-আপ' : 'Follow-up'}:
                    <strong>${num(SETTINGS.fee_followup)}${lang === 'bn' ? ' টাকা' : ' BDT'}</strong></li>
              </ul>
            </div>` : ''}
        </div>

        <div>
          <div class="timeline space-y-4">
            ${EXPERIENCES.sort((a, b) => a.sort_order - b.sort_order).map((e) => `
              <div class="relative flex gap-4 ps-0">
                <span class="icon-bubble bg-white border-2 border-sky2-200 text-sky2-600 z-10 shadow-sm">
                  ${icon(e.icon, 'w-5 h-5')}
                </span>
                <div class="flex-1 pb-1 pt-1.5">
                  <p class="font-bold text-brand-900 text-[0.95rem] leading-snug">
                    ${esc(L(e.position))}</p>
                  <p class="text-sm text-slate-500 mt-0.5 leading-snug">
                    ${esc(L(e.organization))}</p>
                </div>
              </div>`).join('')}
          </div>

          ${QUALIFICATIONS.length ? `
            <div class="mt-7 card p-5">
              <p class="flex items-center gap-2 text-sm font-bold text-brand-900 mb-3">
                <span class="text-sky2-600">${icon('cap', 'w-4 h-4')}</span>
                ${esc(t('exp.qualTitle'))}
              </p>
              <ul class="grid sm:grid-cols-3 gap-3">
                ${QUALIFICATIONS.map((q) => `
                  <li class="rounded-lg bg-brand-50 px-3 py-2.5">
                    <p class="font-semibold text-brand-900 text-sm">${esc(L(q.degree))}</p>
                    ${L(q.institution) ? `<p class="text-[0.72rem] text-slate-500 mt-0.5 leading-snug">
                      ${esc(L(q.institution))}</p>` : ''}
                  </li>`).join('')}
              </ul>
            </div>` : ''}
        </div>
      </div>
    </div>`;
}

function renderServices() {
  const el = document.getElementById('services');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${icon('stetho', 'w-3.5 h-3.5')} ${esc(t('srv.eyebrow'))}</p>
        <h2 class="section-title text-balance-x">${esc(t('srv.title'))}</h2>
        <p class="section-sub">${esc(t('srv.sub'))}</p>
      </div>

      <!-- grid-auto: আইটেম সংখ্যা যাই হোক, লেআউট নিজে থেকেই সাজে -->
      <div class="grid-auto">
        ${SERVICES.map((s) => `
          <article class="card card-hover p-4 flex items-start gap-3.5">
            <span class="icon-bubble ${tone(s.tone)}">${icon(s.icon, 'w-5.5 h-5.5')}</span>
            <p class="text-[0.9rem] font-medium text-slate-700 leading-relaxed pt-1">
              ${esc(L(s.title))}</p>
          </article>`).join('')}
      </div>
    </div>`;
}

function renderSpecial() {
  const el = document.getElementById('special');
  if (!el) return;
  if (!SPECIAL.length) { el.hidden = true; return; }
  el.hidden = false;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${icon('award', 'w-3.5 h-3.5')} ${esc(t('spc.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('spc.title'))}</h2>
        <p class="section-sub">${esc(t('spc.sub'))}</p>
      </div>

      <div class="grid-auto-lg">
        ${SPECIAL.map((s) => `
          <article class="card card-hover p-6 text-center">
            <span class="icon-bubble ${tone(s.tone)} !w-14 !h-14 mx-auto !rounded-2xl">
              ${icon(s.icon, 'w-7 h-7')}</span>
            <h3 class="mt-4 text-lg">${esc(L(s.title))}</h3>
            <p class="mt-2 text-sm text-slate-500 leading-relaxed">${esc(L(s.note))}</p>
          </article>`).join('')}
      </div>
    </div>`;
}

function renderChamber() {
  const c = CHAMBERS.find((x) => x.is_active);
  if (!c) return;
  const mapSrc = `https://www.google.com/maps?q=${encodeURIComponent(c.map_query)}&output=embed`;

  const el = document.getElementById('chamber');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${icon('pin', 'w-3.5 h-3.5')} ${esc(t('chm.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('chm.title'))}</h2>
        <p class="section-sub">${esc(t('chm.sub'))}</p>
      </div>

      <div class="grid lg:grid-cols-2 gap-6">

        <div class="card p-5 sm:p-6">
          <h3 class="text-lg leading-snug">${esc(L(c.name))}</h3>

          <p class="mt-3 flex items-start gap-2.5 text-sm text-slate-600">
            <span class="text-sky2-600 shrink-0 mt-0.5">${icon('pin', 'w-4.5 h-4.5')}</span>
            <span>${esc(L(c.address))}</span>
          </p>

          <div class="mt-5 rounded-xl border border-brand-100 overflow-hidden">
            <p class="bg-brand-50 px-4 py-2.5 text-sm font-bold text-brand-900
                      flex items-center gap-2">
              ${icon('clock', 'w-4 h-4')} ${esc(t('chm.schedule'))}
            </p>
            <!-- টেবিলের বদলে flex-wrap তালিকা — ৩২০px চওড়া ফোনেও
                 সময়টা নিচের লাইনে নেমে যায়, কিন্তু ভাঙে না -->
            <ul class="text-sm">
              ${SCHEDULES.filter((s) => s.is_active).map((s) => `
                <li class="border-t border-brand-100 px-4 py-3 flex flex-wrap
                           items-baseline justify-between gap-x-3 gap-y-1">
                  <span class="text-slate-600">${esc(L(s.label))}</span>
                  <span class="font-semibold text-brand-900 whitespace-nowrap">
                    ${fmtTime(s.start)} – ${fmtTime(s.end)}</span>
                </li>`).join('')}
            </ul>
          </div>

          <p class="mt-3 flex items-center gap-2 text-[0.8rem] text-amber-700
                    bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
            ${icon('users', 'w-4 h-4')} ${esc(t('chm.serialLimit'))}
          </p>

          <div class="mt-5 pt-5 border-t border-brand-100">
            <p class="text-xs text-slate-500 mb-2">${esc(t('chm.hotline'))}</p>
            <div class="flex flex-wrap gap-2.5">
              <a href="tel:${SETTINGS.hotline}" class="btn btn-primary !py-2.5 !text-sm">
                ${icon('phone', 'w-4 h-4')} ${num(SETTINGS.hotline)}
              </a>
              <a href="https://wa.me/${SETTINGS.whatsapp_intl}" target="_blank" rel="noopener"
                 class="btn btn-wa !py-2.5 !text-sm">
                ${icon('phone', 'w-4 h-4')} ${num(SETTINGS.whatsapp)}
              </a>
            </div>
          </div>
        </div>

        <div class="card overflow-hidden flex flex-col">
          <iframe src="${mapSrc}" class="w-full flex-1 min-h-[19rem]" style="border:0"
                  loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                  title="${esc(L(c.name))}"></iframe>
          <div class="p-3 border-t border-brand-100">
            <a href="https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(c.map_query)}"
               target="_blank" rel="noopener" class="btn btn-outline w-full !py-2.5 !text-sm">
              ${icon('pin', 'w-4 h-4')} ${esc(t('common.getDirection'))}
            </a>
          </div>
        </div>

      </div>
    </div>`;
}

function renderGallery() {
  const el = document.getElementById('gallery');
  if (!el) return;
  /* ছবি না থাকলে হোমে সেকশনটি লুকিয়ে যায়, কিন্তু নিজের পেজে
     ফাঁকা পেজ দেখানো যাবে না — তাই সেখানে খালি-অবস্থার বার্তা। */
  if (!GALLERY.length) {
    if (PAGE !== 'gallery') { el.hidden = true; el.innerHTML = ''; return; }
    el.hidden = false;
    el.innerHTML = emptyState('book', 'gal.eyebrow', 'gal.title', 'gal.empty');
    return;
  }
  el.hidden = false;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${esc(t('gal.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('gal.title'))}</h2>
      </div>
      <div class="grid-auto">
        ${GALLERY.map((g) => `
          <figure class="card overflow-hidden card-hover">
            <img src="${esc(g.file_path)}" alt="${esc(L(g.title))}"
                 class="w-full aspect-[4/3] object-cover" loading="lazy">
          </figure>`).join('')}
      </div>
    </div>`;
}

function renderTestimonials() {
  const el = document.getElementById('testimonials');
  if (!el) return;
  /* সিদ্ধান্ত: প্রকৃত মতামত না পাওয়া পর্যন্ত সেকশনটি দেখানো হবে না */
  if (!TESTIMONIALS.length) {
    if (PAGE !== 'testimonials') { el.hidden = true; el.innerHTML = ''; return; }
    el.hidden = false;
    el.innerHTML = emptyState('users', 'tst.eyebrow', 'tst.title', 'tst.empty');
    return;
  }
  el.hidden = false;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${esc(t('tst.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('tst.title'))}</h2>
      </div>
      <div class="grid-auto-lg">
        ${TESTIMONIALS.map((r) => `
          <blockquote class="card p-5">
            <p class="text-slate-600 text-sm leading-relaxed">${esc(L(r.comment))}</p>
            <footer class="mt-3 text-sm font-semibold text-brand-900">— ${esc(r.patient_name)}</footer>
          </blockquote>`).join('')}
      </div>
    </div>`;
}

function renderFaq() {
  const el = document.getElementById('faq');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x max-w-3xl">
      <div class="section-head">
        <p class="eyebrow">${icon('book', 'w-3.5 h-3.5')} ${esc(t('faq.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('faq.title'))}</h2>
      </div>
      <div class="space-y-3">
        ${FAQS.map((f, i) => `
          <details class="card overflow-hidden group" ${i === 0 ? 'open' : ''}>
            <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer
                            list-none font-semibold text-brand-900 text-[0.95rem]
                            hover:bg-brand-50 transition">
              ${esc(L(f.q))}
              <svg class="w-5 h-5 shrink-0 text-sky2-500 transition-transform
                          group-open:rotate-180" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="m6 9 6 6 6-6"/></svg>
            </summary>
            <div class="px-5 pb-4 -mt-1 text-sm text-slate-600 leading-relaxed">
              ${esc(L(f.a))}</div>
          </details>`).join('')}
      </div>
    </div>`;
}

function renderContact() {
  const c = CHAMBERS.find((x) => x.is_active);
  const items = [
    { ic: 'phone', label: t('chm.hotline'), value: num(SETTINGS.hotline),
      href: `tel:${SETTINGS.hotline}`, tone: 'brand' },
    { ic: 'phone', label: t('common.whatsapp'), value: num(SETTINGS.whatsapp),
      href: `https://wa.me/${SETTINGS.whatsapp_intl}`, tone: 'green' },
    { ic: 'book', label: lang === 'bn' ? 'ইমেইল' : 'Email', value: SETTINGS.email,
      href: `mailto:${SETTINGS.email}`, tone: 'sky' },
    { ic: 'pin', label: t('chm.address'), value: L(c.address),
      href: `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(c.map_query)}`,
      tone: 'rose' },
  ];
  const el = document.getElementById('contact');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${icon('phone', 'w-3.5 h-3.5')} ${esc(t('cnt.eyebrow'))}</p>
        <h2 class="section-title">${esc(t('cnt.title'))}</h2>
        <p class="section-sub">${esc(t('cnt.sub'))}</p>
      </div>
      <div class="grid-auto">
        ${items.map((i) => `
          <a href="${i.href}" ${i.href.startsWith('http') ? 'target="_blank" rel="noopener"' : ''}
             class="card card-hover p-5 flex items-start gap-3.5">
            <span class="icon-bubble ${tone(i.tone)}">${icon(i.ic, 'w-5 h-5')}</span>
            <span class="min-w-0">
              <span class="block text-xs text-slate-500">${esc(i.label)}</span>
              <span class="block font-semibold text-brand-900 text-[0.9rem] mt-0.5 break-words">
                ${esc(i.value)}</span>
            </span>
          </a>`).join('')}
      </div>
    </div>`;
}

function renderFooter() {
  const social = [
    ['Facebook', SETTINGS.facebook,
     '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'],
    ['YouTube', SETTINGS.youtube,
     '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.8 4 12 4 12 4s-6.8 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2C5.2 20 12 20 12 20s6.8 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6z"/><path d="m10 15 5-3-5-3z"/>'],
    ['WhatsApp', `https://wa.me/${SETTINGS.whatsapp_intl}`,
     '<path d="M21 11.5a8.4 8.4 0 0 1-12.6 7.3L3 20.5l1.8-5.3A8.5 8.5 0 1 1 21 11.5z"/>'],
  ];
  /* ফুটারে সব পেজ — হেডারে যেগুলো জায়গা পায়নি (মতামত, সিরিয়াল) সেগুলোসহ */
  const nav = PAGES.filter((p) => p.id !== 'home');

  const el = document.getElementById('site-footer');
  if (!el) return;
  el.innerHTML = `
    <div class="bg-brand-900 text-white/75">
      <div class="container-x py-12">
        <div class="grid md:grid-cols-3 gap-9">

          <div>
            <div class="flex items-center gap-2.5">
              <span class="grid place-items-center w-10 h-10 rounded-xl bg-white/10 text-white">
                ${icon('stetho', 'w-5 h-5')}</span>
              <span>
                <span class="block font-bold text-white text-[0.95rem]">
                  ${esc(L(SETTINGS.doctor_name))}</span>
                <span class="block text-xs text-white/60">${esc(L(SETTINGS.degrees))}</span>
              </span>
            </div>
            <p class="mt-4 text-sm leading-relaxed">${esc(L(SETTINGS.specialty))}</p>
            <p class="mt-2 text-xs text-white/50">
              ${esc(t('hero.bmdc'))} ${esc(L(SETTINGS.bmdc))}</p>
          </div>

          <div>
            <p class="font-bold text-white text-sm mb-3">${esc(t('ft.quickLinks'))}</p>
            <ul class="space-y-2 text-sm">
              ${nav.map((p) => `
                <li><a href="${p.file}" class="hover:text-white transition">
                  ${esc(t(p.key))}</a></li>`).join('')}
            </ul>
          </div>

          <div>
            <p class="font-bold text-white text-sm mb-3">${esc(t('ft.contact'))}</p>
            <ul class="space-y-2 text-sm">
              <li><a href="tel:${SETTINGS.hotline}" class="hover:text-white transition
                     flex items-center gap-2">${icon('phone','w-4 h-4')} ${num(SETTINGS.hotline)}</a></li>
              <li><a href="https://wa.me/${SETTINGS.whatsapp_intl}" target="_blank" rel="noopener"
                     class="hover:text-white transition flex items-center gap-2">
                     ${icon('phone','w-4 h-4')} ${num(SETTINGS.whatsapp)}</a></li>
              <li><a href="mailto:${SETTINGS.email}" class="hover:text-white transition
                     flex items-center gap-2">${icon('book','w-4 h-4')} ${esc(SETTINGS.email)}</a></li>
            </ul>

            <p class="font-bold text-white text-sm mt-6 mb-3">${esc(t('ft.follow'))}</p>
            <div class="flex gap-2">
              ${social.map(([name, href, path]) => `
                <a href="${href}" target="_blank" rel="noopener" aria-label="${name}"
                   class="grid place-items-center w-9 h-9 rounded-lg bg-white/10
                          hover:bg-white/20 text-white transition">
                  <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                       stroke-width="1.75" stroke-linejoin="round">${path}</svg>
                </a>`).join('')}
            </div>
          </div>
        </div>

        <div class="mt-10 pt-6 border-t border-white/10">
          <p class="text-[0.72rem] leading-relaxed text-white/45">${esc(t('ft.disclaimer'))}</p>
          <p class="mt-3 text-xs text-white/50">
            © ${num(new Date().getFullYear())} ${esc(L(SETTINGS.doctor_name))}. ${esc(t('ft.rights'))}
          </p>
        </div>
      </div>
    </div>`;
}

/* ব্রেডক্রাম্ব — হোম ছাড়া বাকি পেজে হেডারের নিচে সরু একটি পট্টি।
   সেকশনের নিজস্ব শিরোনাম আগে থেকেই আছে, তাই এখানে বড় ব্যানার দেওয়া হয়নি। */
function renderBreadcrumb() {
  const el = document.getElementById('breadcrumb');
  if (!el) return;
  const here = pageOf(PAGE);
  if (!here || PAGE === 'home') { el.hidden = true; return; }
  el.hidden = false;
  el.innerHTML = `
    <div class="bg-brand-50 border-b border-brand-100">
      <div class="container-x py-2.5">
        <ol class="flex items-center gap-2 text-[0.78rem] text-slate-500">
          <li><a href="${url('home')}" class="hover:text-brand-900 transition">
            ${esc(t('nav.home'))}</a></li>
          <li aria-hidden="true" class="text-brand-300">›</li>
          <li><span class="font-semibold text-brand-900"
                    aria-current="page">${esc(t(here.key))}</span></li>
        </ol>
      </div>
    </div>`;
}

/* হোমপেজে সিরিয়ালের ব্যান্ড — পুরো বুকিং ফর্মটি এখন আলাদা পেজে */
function renderBookingCta() {
  const el = document.getElementById('booking-cta');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="relative overflow-hidden rounded-2xl bg-brand-900 text-white
                  px-6 py-10 md:px-12 md:py-12">
        <div class="absolute inset-0 opacity-90
                    bg-[radial-gradient(36rem_20rem_at_85%_10%,#2e8bc0_0%,transparent_60%)]"></div>
        <div class="relative flex flex-col md:flex-row md:items-center gap-6 md:gap-10">
          <div class="flex-1">
            <p class="eyebrow !bg-white/15 !text-sky2-100">
              ${icon('clock', 'w-3.5 h-3.5')} ${esc(t('cta.eyebrow'))}</p>
            <h2 class="text-2xl md:text-3xl !text-white font-bold">${esc(t('cta.title'))}</h2>
            <p class="mt-2.5 text-white/80 text-[0.97rem] leading-relaxed max-w-xl">
              ${esc(t('cta.sub'))}</p>
          </div>
          <div class="flex flex-wrap gap-3 shrink-0">
            <a href="${url('booking')}" class="btn btn-wa">
              ${icon('clock', 'w-4.5 h-4.5')} ${esc(t('common.bookNow'))}</a>
            <a href="tel:${SETTINGS.hotline}" class="btn btn-ghost">
              ${icon('phone', 'w-4.5 h-4.5')} ${num(SETTINGS.hotline)}</a>
          </div>
        </div>
      </div>
    </div>`;
}

function renderMobileBar() {
  const el = document.getElementById('mobile-bar');
  if (!el) return;
  el.innerHTML = `
    <a href="tel:${SETTINGS.hotline}" class="btn btn-outline !px-2 !py-2.5 !text-xs">
      ${icon('phone', 'w-4 h-4')} ${esc(t('common.call'))}</a>
    <a href="https://wa.me/${SETTINGS.whatsapp_intl}" target="_blank" rel="noopener"
       class="btn btn-wa !px-2 !py-2.5 !text-xs">
      ${icon('phone', 'w-4 h-4')} ${esc(t('common.whatsapp'))}</a>
    <a href="${url('booking')}" class="btn btn-primary !px-2 !py-2.5 !text-xs">
      ${icon('clock', 'w-4 h-4')} ${esc(t('nav.book'))}</a>`;
}

/* ============================================================
   ৪. বুকিং
   ============================================================ */

const booking = { date: null, slot: null, monthOffset: 0 };

function renderBooking() {
  const el = document.getElementById('booking');
  if (!el) return;
  el.innerHTML = `
    <div class="container-x">
      <div class="section-head">
        <p class="eyebrow">${icon('clock', 'w-3.5 h-3.5')} ${esc(t('nav.book'))}</p>
        <h2 class="section-title">${esc(t('bk.title'))}</h2>
        <p class="section-sub">${esc(t('bk.sub'))}</p>
      </div>
      <div id="booking-body"></div>
    </div>`;
  renderBookingBody();
}

function renderBookingBody() {
  document.getElementById('booking-body').innerHTML = `
    <div class="grid lg:grid-cols-[1fr_1fr] gap-6 items-start">
      <div class="card p-4 sm:p-5" id="cal-wrap"></div>
      <div class="card p-4 sm:p-5" id="form-wrap"></div>
    </div>`;
  renderCalendar();
  renderForm();
}

function renderCalendar() {
  const base = new Date();
  const view = new Date(base.getFullYear(), base.getMonth() + booking.monthOffset, 1);
  const first = new Date(view.getFullYear(), view.getMonth(), 1);
  const days  = new Date(view.getFullYear(), view.getMonth() + 1, 0).getDate();
  const lead  = first.getDay();

  const maxDate = new Date(); maxDate.setDate(maxDate.getDate() + 30);
  const today = new Date(); today.setHours(0, 0, 0, 0);

  let cells = '';
  for (let i = 0; i < lead; i++) cells += '<div></div>';

  for (let d = 1; d <= days; d++) {
    const date = new Date(view.getFullYear(), view.getMonth(), d);
    const st = dayState(date);
    const beyond = date > maxDate;
    const disabled = beyond || ['past', 'closed', 'holiday', 'full'].includes(st.status);
    const picked = booking.date === ymd(date);

    /* ঘরের ভেতর শুধু খালি সিরিয়ালের সংখ্যা — ৩৬০px চওড়া ফোনেও যেন ঠেসে না যায়।
       বন্ধ/পূর্ণ দিন এমনিতেই ধূসর দেখায়, তাই সেখানে লেখার দরকার নেই।
       পুরো বিবরণ aria-label-এ থাকে, স্ক্রিন রিডার ঠিকই পড়বে। */
    const label =
      st.status === 'open'    ? `${fmtDate(date)} — ${num(st.open.length)} ${t('bk.available')}`
    : st.status === 'full'    ? `${fmtDate(date)} — ${t('bk.full')}`
    : st.status === 'holiday' || st.status === 'closed'
                              ? `${fmtDate(date)} — ${t('bk.closed')}`
                              : fmtDate(date);

    cells += `
      <button type="button" class="cal-day" data-date="${ymd(date)}"
              aria-pressed="${picked}" ${disabled ? 'disabled' : ''}
              title="${esc(label)}" aria-label="${esc(label)}">
        <span class="text-[0.95rem] leading-none">${num(d)}</span>
        ${st.status === 'open'
          ? `<span class="cal-meta">${num(st.open.length)}</span>`
          : ''}
      </button>`;
  }

  const canPrev = booking.monthOffset > 0;
  const canNext = booking.monthOffset < 1;

  document.getElementById('cal-wrap').innerHTML = `
    <div class="flex flex-wrap items-center justify-between gap-x-2 gap-y-3 mb-4">
      <p class="font-bold text-brand-900 flex items-center gap-2">
        <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-900 text-white
                     text-xs font-bold shrink-0">${num(1)}</span>
        ${esc(t('bk.step1'))}
      </p>
      <div class="flex items-center gap-1 ms-auto">
        <button type="button" id="cal-prev" ${canPrev ? '' : 'disabled'}
                class="p-1.5 rounded-lg hover:bg-brand-50 disabled:opacity-30
                       disabled:cursor-not-allowed" aria-label="${esc(t('bk.prevMonth'))}">
          <svg class="w-5 h-5 text-brand-900" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <span class="text-sm font-semibold text-brand-900 min-w-[6.5rem] sm:min-w-[8.5rem]
                     text-center">
          ${monthName(view.getMonth())} ${num(view.getFullYear())}</span>
        <button type="button" id="cal-next" ${canNext ? '' : 'disabled'}
                class="p-1.5 rounded-lg hover:bg-brand-50 disabled:opacity-30
                       disabled:cursor-not-allowed" aria-label="${esc(t('bk.nextMonth'))}">
          <svg class="w-5 h-5 text-brand-900" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-7 gap-1 mb-1.5">
      ${[0,1,2,3,4,5,6].map((i) => `
        <div class="text-center text-[0.68rem] font-semibold text-slate-400 py-1">
          ${dayShort(i)}</div>`).join('')}
    </div>
    <div class="grid grid-cols-7 gap-1">${cells}</div>

    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-4 pt-4
                border-t border-brand-100 text-[0.7rem] text-slate-500">
      <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded border-[1.5px] border-brand-100 bg-white"></span>
        ${esc(t('bk.legendOpen'))}</span>
      <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded bg-slate-100"></span>${esc(t('bk.legendClosed'))}</span>
      <span class="flex items-center gap-1.5">
        <span class="w-3 h-3 rounded bg-brand-900"></span>${esc(t('bk.legendPicked'))}</span>
      <span class="w-full text-[0.68rem] text-slate-400">${esc(t('bk.legendCount'))}</span>
    </div>

    <div id="slot-wrap" class="mt-5 pt-5 border-t border-brand-100"></div>`;

  document.getElementById('cal-prev').onclick = () => { booking.monthOffset--; renderCalendar(); };
  document.getElementById('cal-next').onclick = () => { booking.monthOffset++; renderCalendar(); };

  document.querySelectorAll('.cal-day:not(:disabled)').forEach((b) => {
    b.onclick = () => {
      booking.date = b.dataset.date;
      booking.slot = null;
      renderCalendar();
      renderForm();
      document.getElementById('slot-wrap')?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    };
  });

  renderSlots();
}

function renderSlots() {
  const wrap = document.getElementById('slot-wrap');
  if (!wrap) return;

  if (!booking.date) {
    wrap.innerHTML = `
      <p class="text-center text-sm text-slate-400 py-3">${esc(t('bk.pickDateFirst'))}</p>`;
    return;
  }

  const [y, m, d] = booking.date.split('-').map(Number);
  const date = new Date(y, m - 1, d);
  const st = dayState(date);
  if (st.status !== 'open') { wrap.innerHTML = ''; return; }

  wrap.innerHTML = `
    <p class="font-bold text-brand-900 flex items-center gap-2 mb-1">
      <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-900 text-white
                   text-xs font-bold">${num(2)}</span>
      ${esc(t('bk.step2'))}
    </p>
    <p class="text-xs text-slate-500 mb-3 ms-9">${dayName(date.getDay())}, ${fmtDate(date)}</p>

    <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5">
      ${st.slots.map((s) => {
        const isTaken = st.taken.has(s.serial);
        const isPast  = st.isToday && toMin(s.time) <= st.nowMin + 30;
        const off = isTaken || isPast;
        return `
          <button type="button" class="slot" data-serial="${s.serial}" data-time="${s.time}"
                  aria-pressed="${booking.slot === s.serial}" ${off ? 'disabled' : ''}>
            ${fmtTime(s.time)}
          </button>`;
      }).join('')}
    </div>`;

  wrap.querySelectorAll('.slot:not(:disabled)').forEach((b) => {
    b.onclick = () => {
      booking.slot = Number(b.dataset.serial);
      renderSlots();
      renderForm();
    };
  });
}

function renderForm() {
  const wrap = document.getElementById('form-wrap');
  const ready = booking.date && booking.slot;

  let summary = `
    <p class="text-sm text-slate-400 text-center py-6">${esc(t('bk.pickDateFirst'))}</p>`;

  if (ready) {
    const [y, m, d] = booking.date.split('-').map(Number);
    const date = new Date(y, m - 1, d);
    const st = dayState(date);
    const slot = st.slots.find((s) => s.serial === booking.slot);
    summary = `
      <div class="rounded-xl bg-brand-50 border border-brand-100 p-4 mb-5">
        <p class="text-xs font-semibold text-brand-700 mb-2.5">${esc(t('bk.summary'))}</p>
        <dl class="grid grid-cols-3 gap-3 text-center">
          <div><dt class="text-[0.68rem] text-slate-500">${esc(t('ok.date'))}</dt>
            <dd class="text-sm font-bold text-brand-900 mt-0.5">
              ${num(d)} ${monthName(m - 1)}</dd></div>
          <div><dt class="text-[0.68rem] text-slate-500">${esc(t('ok.time'))}</dt>
            <dd class="text-sm font-bold text-brand-900 mt-0.5">${fmtTime(slot.time)}</dd></div>
          <div><dt class="text-[0.68rem] text-slate-500">${esc(t('ok.serial'))}</dt>
            <dd class="text-sm font-bold text-brand-900 mt-0.5">${num(slot.serial)}</dd></div>
        </dl>
      </div>`;
  }

  wrap.innerHTML = `
    <p class="font-bold text-brand-900 flex items-center gap-2 mb-4">
      <span class="grid place-items-center w-7 h-7 rounded-lg
                   ${ready ? 'bg-brand-900 text-white' : 'bg-slate-100 text-slate-400'}
                   text-xs font-bold">${num(3)}</span>
      ${esc(t('bk.step3'))}
    </p>

    ${summary}

    <form id="booking-form" class="space-y-3.5 ${ready ? '' : 'opacity-40 pointer-events-none'}"
          novalidate>
      <div class="grid sm:grid-cols-2 gap-3.5">
        <div>
          <label class="label req" for="f-name">${esc(t('bk.patientName'))}</label>
          <input class="input" id="f-name" name="name" required
                 placeholder="${lang === 'bn' ? 'শিশুর পূর্ণ নাম' : 'Full name of the child'}">
        </div>
        <div>
          <label class="label req" for="f-phone">${esc(t('bk.phone'))}</label>
          <input class="input" id="f-phone" name="phone" type="tel" required
                 inputmode="numeric" placeholder="01XXXXXXXXX">
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-3.5">
        <div>
          <label class="label req" for="f-age">${esc(t('bk.age'))}</label>
          <div class="flex gap-2">
            <input class="input flex-1" id="f-age" name="age" type="number" min="0" required
                   inputmode="numeric" placeholder="0">
            <select class="input w-28" name="age_unit" aria-label="${esc(t('bk.age'))}">
              <option value="year">${esc(t('bk.ageUnitYear'))}</option>
              <option value="month">${esc(t('bk.ageUnitMonth'))}</option>
              <option value="day">${esc(t('bk.ageUnitDay'))}</option>
            </select>
          </div>
        </div>
        <div>
          <label class="label" for="f-gender">${esc(t('bk.gender'))}</label>
          <select class="input" id="f-gender" name="gender">
            <option value="male">${esc(t('bk.male'))}</option>
            <option value="female">${esc(t('bk.female'))}</option>
          </select>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-3.5">
        <div>
          <label class="label" for="f-guardian">${esc(t('bk.guardianName'))}
            <span class="font-normal text-slate-400">(${esc(t('bk.optional'))})</span></label>
          <input class="input" id="f-guardian" name="guardian">
        </div>
        <div>
          <label class="label" for="f-visit">${esc(t('bk.visitType'))}</label>
          <select class="input" id="f-visit" name="visit_type">
            <option value="new">${esc(t('bk.visitNew'))}</option>
            <option value="followup">${esc(t('bk.visitFollow'))}</option>
            <option value="report">${esc(t('bk.visitReport'))}</option>
          </select>
        </div>
      </div>

      <div>
        <label class="label" for="f-problem">${esc(t('bk.problem'))}
          <span class="font-normal text-slate-400">(${esc(t('bk.optional'))})</span></label>
        <textarea class="input" id="f-problem" name="problem" rows="2"
                  placeholder="${esc(t('bk.problemHint'))}"></textarea>
      </div>

      <!-- হানিপট: বট এই ঘরটি পূরণ করে, মানুষ দেখতেই পায় না -->
      <input type="text" name="website" tabindex="-1" autocomplete="off"
             class="hidden" aria-hidden="true">

      <p id="form-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200
                                rounded-lg px-3 py-2"></p>

      <button type="submit" class="btn btn-primary w-full !py-3.5">
        ${icon('clock', 'w-4.5 h-4.5')} ${esc(t('bk.submit'))}
      </button>

      <p class="text-[0.7rem] text-slate-400 text-center leading-relaxed">
        ${esc(t('ok.arriveEarly'))}</p>
    </form>`;

  if (!ready) return;
  document.getElementById('booking-form').addEventListener('submit', onSubmit);
}

function onSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  const err = document.getElementById('form-error');

  if (fd.get('website')) return;                       // বট ধরা পড়ল

  const name  = String(fd.get('name') || '').trim();
  const phone = String(fd.get('phone') || '').replace(/\s|-/g, '');
  const age   = String(fd.get('age') || '').trim();

  const problems = [];
  if (!name) problems.push(lang === 'bn' ? 'শিশুর নাম লিখুন।' : 'Please enter the child\'s name.');
  if (!/^01[3-9]\d{8}$/.test(phone))
    problems.push(lang === 'bn'
      ? 'সঠিক মোবাইল নম্বর লিখুন (যেমন 01712345678)।'
      : 'Please enter a valid mobile number (e.g. 01712345678).');
  if (age === '') problems.push(lang === 'bn' ? 'শিশুর বয়স লিখুন।' : 'Please enter the age.');

  if (problems.length) {
    err.textContent = problems[0];
    err.classList.remove('hidden');
    return;
  }
  err.classList.add('hidden');

  const [y, m, d] = booking.date.split('-').map(Number);
  const date = new Date(y, m - 1, d);
  const st   = dayState(date);
  const slot = st.slots.find((s) => s.serial === booking.slot);

  const code = `ASF-${String(y).slice(2)}${String(m).padStart(2,'0')}${String(d).padStart(2,'0')}` +
               `-${String(slot.serial).padStart(2,'0')}`;

  showSuccess({
    code, date, slot,
    name, phone,
    age: num(age) + ' ' + t('bk.ageUnit' + ({year:'Year',month:'Month',day:'Day'}[fd.get('age_unit')])),
    guardian: String(fd.get('guardian') || '').trim(),
    visit: t('bk.visit' + ({new:'New',followup:'Follow',report:'Report'}[fd.get('visit_type')])),
    problem: String(fd.get('problem') || '').trim(),
  });
}

function showSuccess(b) {
  const c = CHAMBERS.find((x) => x.is_active);

  /* WhatsApp-এ যে বার্তাটি আগে থেকে লেখা থাকবে */
  const msg = lang === 'bn'
    ? `আসসালামু আলাইকুম।\nআমি অনলাইনে সিরিয়াল বুক করেছি।\n\n` +
      `▸ বুকিং কোড: ${b.code}\n` +
      `▸ রোগীর নাম: ${b.name}\n` +
      `▸ বয়স: ${b.age}\n` +
      `▸ মোবাইল: ${b.phone}\n` +
      `▸ তারিখ: ${fmtDate(b.date)} (${dayName(b.date.getDay())})\n` +
      `▸ সময়: ${fmtTime(b.slot.time)}\n` +
      `▸ সিরিয়াল নং: ${num(b.slot.serial)}\n` +
      `▸ ভিজিটের ধরন: ${b.visit}` +
      (b.problem ? `\n▸ সমস্যা: ${b.problem}` : '') +
      `\n\nধন্যবাদ।`
    : `Assalamu Alaikum.\nI have booked a serial online.\n\n` +
      `- Booking code: ${b.code}\n` +
      `- Patient: ${b.name}\n` +
      `- Age: ${b.age}\n` +
      `- Mobile: ${b.phone}\n` +
      `- Date: ${fmtDate(b.date)} (${dayName(b.date.getDay())})\n` +
      `- Time: ${fmtTime(b.slot.time)}\n` +
      `- Serial no: ${b.slot.serial}\n` +
      `- Visit type: ${b.visit}` +
      (b.problem ? `\n- Problem: ${b.problem}` : '') +
      `\n\nThank you.`;

  const waUrl = `https://wa.me/${SETTINGS.whatsapp_intl}?text=${encodeURIComponent(msg)}`;

  /* Google Calendar লিংক — সম্পূর্ণ ফ্রি, কোনো API লাগে না */
  const [sh, sm] = b.slot.time.split(':').map(Number);
  const startUTC = new Date(b.date.getFullYear(), b.date.getMonth(), b.date.getDate(), sh, sm);
  const endUTC   = new Date(startUTC.getTime() + 30 * 60000);
  const gfmt = (d) => d.toISOString().replace(/[-:]|\.\d{3}/g, '');
  const calUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
    `&text=${encodeURIComponent(L(SETTINGS.doctor_short) + ' — ' + t('ok.serial') + ' ' + num(b.slot.serial))}` +
    `&dates=${gfmt(startUTC)}/${gfmt(endUTC)}` +
    `&location=${encodeURIComponent(L(c.address))}` +
    `&details=${encodeURIComponent(t('ok.code') + ': ' + b.code)}`;

  const rows = [
    [t('ok.code'),    b.code,                    true],
    [t('ok.serial'),  num(b.slot.serial),        true],
    [t('ok.date'),    `${fmtDate(b.date)} (${dayName(b.date.getDay())})`, false],
    [t('ok.time'),    fmtTime(b.slot.time),      false],
    [t('ok.patient'), b.name,                    false],
    [t('ok.chamber'), L(c.name),                 false],
  ];

  /* ডাউনলোড করা ছবিতে স্ক্রিনের চেয়ে বেশি তথ্য থাকে —
     এটাই রোগী সংরক্ষণ করবেন, তাই সব কিছু এক জায়গায়। */
  const fullRows = [
    [t('ok.code'),      b.code],
    [t('ok.serial'),    num(b.slot.serial)],
    [t('ok.date'),      `${fmtDate(b.date)} (${dayName(b.date.getDay())})`],
    [t('ok.time'),      fmtTime(b.slot.time)],
    [t('ok.patient'),   b.name],
    [t('bk.age'),       b.age],
    [t('bk.phone'),     num(b.phone)],
    [t('bk.visitType'), b.visit],
    [t('ok.chamber'),   L(c.name)],
  ];

  const dlg = openSlipDialog(`
    <div class="card print-slip p-6 md:p-8">

      <button type="button" id="slip-close" aria-label="${esc(t('common.close'))}"
              class="absolute top-3.5 end-3.5 grid place-items-center w-9 h-9 rounded-lg
                     text-slate-400 hover:text-brand-900 hover:bg-brand-50 transition no-print">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
      </button>

      <div class="text-center">
        <span class="grid place-items-center w-16 h-16 rounded-full bg-wa-500/10
                     text-wa-600 mx-auto">
          <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <h3 id="slip-title" class="mt-4 text-2xl">${esc(t('ok.title'))}</h3>
        <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">${esc(t('ok.sub'))}</p>
      </div>

      <dl class="mt-7 divide-y divide-brand-100 border-y border-brand-100">
        ${rows.map(([k, v, strong]) => `
          <div class="flex items-center justify-between gap-4 py-3">
            <dt class="text-sm text-slate-500 shrink-0">${esc(k)}</dt>
            <dd class="text-end ${strong
              ? 'text-base font-extrabold text-brand-900 tracking-wide'
              : 'text-sm font-semibold text-brand-900'}">${esc(v)}</dd>
          </div>`).join('')}
      </dl>

      <p class="mt-4 text-[0.72rem] text-slate-400 text-center">${esc(t('ok.timeNote'))}</p>

      <div class="mt-6 rounded-xl bg-wa-500/8 border border-wa-500/25 p-4 no-print">
        <a href="${waUrl}" target="_blank" rel="noopener" class="btn btn-wa w-full !py-3.5">
          ${icon('phone', 'w-5 h-5')} ${esc(t('ok.waCta'))}
        </a>
        <p class="mt-2.5 text-[0.73rem] text-slate-600 text-center leading-relaxed">
          ${esc(t('ok.waNote'))}</p>
      </div>

      <div class="mt-3 no-print">
        <button type="button" id="slip-download" class="btn btn-primary w-full !py-3">
          ${icon('book', 'w-4.5 h-4.5')} ${esc(t('common.download'))}
        </button>
        <p id="slip-dl-note" class="mt-2 text-[0.73rem] text-slate-500 text-center" hidden></p>
      </div>

      <div class="mt-2.5 grid sm:grid-cols-2 gap-2.5 no-print">
        <a href="${calUrl}" target="_blank" rel="noopener" class="btn btn-outline !py-2.5 !text-sm">
          ${icon('clock', 'w-4 h-4')} ${esc(t('ok.addCal'))}
        </a>
        <button type="button" id="print-slip" class="btn btn-outline !py-2.5 !text-sm">
          ${icon('book', 'w-4 h-4')} ${esc(t('common.print'))}
        </button>
      </div>

      <p class="mt-5 text-xs text-amber-700 bg-amber-50 border border-amber-200
                rounded-lg px-3 py-2.5 text-center">${esc(t('ok.arriveEarly'))}</p>
    </div>

    <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 my-4 no-print">
      <button type="button" id="book-again"
              class="text-sm font-semibold text-white/90 hover:text-white underline">
        ${esc(t('common.bookAgain'))}</button>
      <a href="${url('home')}"
         class="text-sm font-semibold text-white/60 hover:text-white underline">
        ${esc(t('common.backHome'))}</a>
    </div>`);

  const q = (id) => dlg.querySelector('#' + id);

  q('slip-close').onclick  = () => closeSlip();
  q('book-again').onclick  = () => closeSlip();
  q('print-slip').onclick  = () => window.print();

  q('slip-download').onclick = async (e) => {
    const btn = e.currentTarget;
    const note = q('slip-dl-note');
    btn.disabled = true;
    try {
      await downloadSlip(b, c, fullRows);
      note.hidden = false;
      note.textContent = t('ok.dlDone');
      note.className = 'mt-2 text-[0.73rem] text-wa-700 text-center';
    } catch {
      /* কোনো কারণে ছবি বানানো না গেলে প্রিন্ট/PDF-ই ভরসা */
      note.hidden = false;
      note.textContent = t('ok.dlFail');
      note.className = 'mt-2 text-[0.73rem] text-amber-700 text-center';
    } finally {
      btn.disabled = false;
    }
  };
}

/* পপ-আপ তৈরি ও খোলা। বন্ধ হলে বুকিং ফর্ম আবার প্রথম ধাপে ফিরে যায়,
   যাতে পুরোনো তথ্য নিয়ে বিভ্রান্তি না হয়। */
function openSlipDialog(inner) {
  let dlg = document.getElementById('slip-modal');
  if (!dlg) {
    dlg = document.createElement('dialog');
    dlg.id = 'slip-modal';
    dlg.className = 'slip-dialog';
    dlg.setAttribute('aria-labelledby', 'slip-title');
    document.body.appendChild(dlg);

    /* ব্যাকড্রপে ক্লিক করলেও বন্ধ হবে */
    dlg.addEventListener('click', (e) => { if (e.target === dlg) closeSlip(); });
    /* Esc — এখানে ব্রাউজার নিজেই বন্ধ করে, আমরা শুধু ফর্মটি রিসেট করি */
    dlg.addEventListener('cancel', () => closeSlip());
  }
  dlg.innerHTML = `<div class="slip-scroll">${inner}</div>`;
  dlg.querySelector('.slip-scroll').classList.add('relative');
  dlg.showModal();
  return dlg;
}

/* পপ-আপ বন্ধ করে বুকিং ফর্মকে আবার প্রথম ধাপে ফিরিয়ে আনে।
   ⚠️ dialog-এর 'close' ইভেন্টের ভরসায় না থেকে প্রতিটি বন্ধ করার
   পথ থেকে সরাসরি এটাই ডাকা হয় — কিছু ব্রাউজারে ইভেন্টটি আসে না। */
function closeSlip() {
  const dlg = document.getElementById('slip-modal');
  if (dlg?.open) dlg.close();

  booking.date = null; booking.slot = null; booking.monthOffset = 0;
  renderBookingBody();
  document.getElementById('booking')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ============================================================
   স্লিপ ছবি হিসেবে ডাউনলোড
   ------------------------------------------------------------
   কোনো লাইব্রেরি ছাড়াই ক্যানভাসে স্লিপটি আঁকা হয়, তারপর PNG।
   বাংলা যুক্তাক্ষর ঠিকমতো আসে — ক্যানভাস ব্রাউজারের টেক্সট
   শেপিং ব্যবহার করে। ফন্ট লোড হওয়ার আগে আঁকলে ফলব্যাক ফন্টে
   চলে যেত, তাই document.fonts-এর জন্য অপেক্ষা করা হয়।
   ============================================================ */
async function downloadSlip(b, c, rows) {
  const FONT = lang === 'bn' ? '"Hind Siliguri"' : 'Inter';
  const f = (weight, size) => `${weight} ${size}px ${FONT}, sans-serif`;

  await document.fonts.ready;
  await Promise.all([400, 600, 700].map((w) =>
    document.fonts.load(`${w} 20px ${FONT}`, 'অআকখ ABC ১২৩')));

  const S = 2;                       /* রেটিনা স্কেল */
  const W = 760, PAD = 44;
  const HEAD = 132, ROW = 54, FOOT = 92;
  const H = HEAD + 38 + 66 + 42 + 34 + 16 + rows.length * ROW + 26 + 34 + FOOT;

  const cv = document.createElement('canvas');
  cv.width = W * S; cv.height = H * S;
  const x = cv.getContext('2d');
  x.scale(S, S);
  x.textBaseline = 'middle';

  const NAVY = '#1b3a6b', SKY = '#bde5f6', SLATE = '#64748b',
        LINE = '#e0ecf8', GREEN = '#1eb855', MUTED = '#94a3b8';

  /* কাগজ */
  x.fillStyle = '#fff'; x.fillRect(0, 0, W, H);

  /* হেডার */
  x.fillStyle = NAVY; x.fillRect(0, 0, W, HEAD);
  x.textAlign = 'center';
  x.fillStyle = '#fff'; x.font = f(700, 27);
  x.fillText(L(SETTINGS.doctor_name), W / 2, 50);
  x.fillStyle = SKY; x.font = f(500, 15);
  x.fillText(L(SETTINGS.degrees), W / 2, 82);
  x.font = f(400, 13.5);
  x.fillText(L(SETTINGS.specialty), W / 2, 107);

  let y = HEAD + 38;

  /* সবুজ টিক */
  x.beginPath(); x.arc(W / 2, y + 26, 26, 0, Math.PI * 2);
  x.fillStyle = 'rgba(37,211,102,.12)'; x.fill();
  x.strokeStyle = GREEN; x.lineWidth = 3.5;
  x.lineCap = 'round'; x.lineJoin = 'round';
  x.beginPath();
  x.moveTo(W / 2 - 11, y + 26); x.lineTo(W / 2 - 3, y + 34); x.lineTo(W / 2 + 12, y + 17);
  x.stroke();
  y += 66;

  x.fillStyle = NAVY; x.font = f(700, 28);
  x.fillText(t('ok.title'), W / 2, y + 14);
  y += 42;

  x.fillStyle = SLATE; x.font = f(400, 14);
  x.fillText(t('ok.sub'), W / 2, y + 10);
  y += 34 + 16;

  /* সারিগুলো — মান বেশি লম্বা হলে ফন্ট ছোট হয়ে ঘরের মধ্যে বসে যায় */
  const labelW = 190;
  const valMax = W - PAD * 2 - labelW - 16;
  rows.forEach(([k, v], i) => {
    const mid = y + ROW / 2;

    x.textAlign = 'start';
    x.fillStyle = SLATE; x.font = f(400, 15);
    x.fillText(k, PAD, mid);

    x.textAlign = 'end';
    x.fillStyle = NAVY;
    let size = i < 2 ? 19 : 16.5;
    x.font = f(i < 2 ? 800 : 600, size);
    while (x.measureText(v).width > valMax && size > 11) {
      size -= 0.5;
      x.font = f(i < 2 ? 800 : 600, size);
    }
    x.fillText(v, W - PAD, mid);

    if (i < rows.length - 1) {
      x.strokeStyle = LINE; x.lineWidth = 1;
      x.beginPath(); x.moveTo(PAD, y + ROW); x.lineTo(W - PAD, y + ROW); x.stroke();
    }
    y += ROW;
  });

  y += 26;
  x.textAlign = 'center';
  x.fillStyle = MUTED; x.font = f(400, 12.5);
  x.fillText(t('ok.arriveEarly'), W / 2, y + 8);
  y += 34;

  /* ফুটার */
  x.fillStyle = '#f1f6fc'; x.fillRect(0, H - FOOT, W, FOOT);
  x.fillStyle = NAVY; x.font = f(600, 14);
  x.fillText(L(c.name), W / 2, H - FOOT + 26);
  x.fillStyle = SLATE; x.font = f(400, 12.5);
  x.fillText(L(c.address), W / 2, H - FOOT + 50);
  x.fillText(`${t('ft.contact')}: ${num(SETTINGS.hotline)}`, W / 2, H - FOOT + 72);

  const blob = await new Promise((res, rej) =>
    cv.toBlob((bl) => (bl ? res(bl) : rej(new Error('toBlob failed'))), 'image/png'));

  const href = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = href;
  a.download = `${b.code}.png`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  setTimeout(() => URL.revokeObjectURL(href), 60000);
}

/* ============================================================
   ৫. চালু করা
   ============================================================ */

/* সব রেন্ডারার প্রতিটি পেজেই ডাকা হয় — যার কনটেইনার এই পেজে নেই,
   সেটি নিজে থেকেই ফিরে যায়। তাই নতুন পেজ বানাতে শুধু HTML ফাইলে
   ঠিক id-এর <section> বসালেই হয়, app.js-এ কিছু বদলাতে হয় না। */
function renderAll() {
  document.documentElement.lang = lang;
  renderNotice();
  renderHeader();
  renderBreadcrumb();
  renderHero();
  renderAbout();
  renderServices();
  renderSpecial();
  renderChamber();
  renderBooking();
  renderBookingCta();
  renderGallery();
  renderTestimonials();
  renderFaq();
  renderContact();
  renderFooter();
  renderMobileBar();
}

/* স্ক্রল করলে হেডারে ছায়া পড়বে */
function initScroll() {
  const h = document.getElementById('site-header');
  if (!h) return;
  const onScroll = () => h.classList.toggle('shadow-md', window.scrollY > 8);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

document.addEventListener('DOMContentLoaded', () => {
  renderAll();
  initScroll();
});

/* ============================================================
   কনটেন্ট ডেটা — প্রফেসর ডা. আবু সুফিয়ান
   ------------------------------------------------------------
   ⚠️ গুরুত্বপূর্ণ:
   এই ফাইলটি ভবিষ্যতের ডাটাবেস টেবিলগুলোর হুবহু প্রতিরূপ।
   প্রতিটি অবজেক্ট = একটি টেবিলের একটি রো।
   Laravel-এ রূপান্তরের সময় এই ফাইল মুছে গিয়ে Eloquent মডেল আসবে,
   কিন্তু ফিল্ডের নাম ও গঠন একই থাকবে — তাই টেমপ্লেট বদলাতে হবে না।

   ➤ কোনো তালিকার আইটেম সংখ্যা নির্দিষ্ট নয়।
     নিচে যোগ/বিয়োগ করলে ওয়েবসাইটের লেআউট নিজে থেকেই মানিয়ে নেবে।
   ➤ ডেমো মান "DEMO:" মন্তব্য দিয়ে চিহ্নিত।
   ============================================================ */

/* ---------- আইকন লাইব্রেরি (SVG path) ---------- */
export const ICONS = {
  baby:      '<circle cx="12" cy="12" r="9"/><path d="M9 10h.01M15 10h.01M9.5 14.5a3.5 3.5 0 0 0 5 0"/>',
  thermo:    '<path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/>',
  wind:      '<path d="M12.8 19.6A2 2 0 1 0 14 16H2M17.5 8a2.5 2.5 0 1 1 2 4H2M9.8 4.4A2 2 0 1 1 11 8H2"/>',
  droplet:   '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>',
  apple:     '<path d="M12 20.94c1.5 0 2.75 1.06 4 1.06 3 0 6-8 6-12.22A4.91 4.91 0 0 0 17 5c-2.22 0-4 1.44-5 2-1-.56-2.78-2-5-2a4.9 4.9 0 0 0-5 4.78C2 14 5 22 8 22c1.25 0 2.5-1.06 4-1.06Z"/><path d="M10 2c1 .5 2 2 2 5"/>',
  utensils:  '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>',
  pulse:     '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
  growth:    '<path d="M22 7 13.5 15.5 8.5 10.5 2 17"/><path d="M16 7h6v6"/>',
  gland:     '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/>',
  droplets:  '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>',
  shield:    '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>',
  heart:     '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>',
  syringe:   '<path d="m18 2 4 4M17 7l3-3M19 9 8.7 19.3c-1 1-2.5 1-3.4 0l-.6-.6c-1-1-1-2.5 0-3.4L15 5M9 11l4 4M5 19l-3 3M14 4l6 6"/>',
  stetho:    '<path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 12 0V4a2 2 0 0 0-2-2h-1a.3.3 0 1 0 .2.3"/><path d="M8 15v1a6 6 0 0 0 12 0v-4"/><circle cx="20" cy="10" r="2"/>',
  cloud:     '<path d="M17.5 19a4.5 4.5 0 1 0 0-9h-1.8A7 7 0 1 0 4 16.7"/>',
  clock:     '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
  pin:       '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
  phone:     '<path d="M13.8 10.2a11 11 0 0 0 5 5l1.7-1.7a1.4 1.4 0 0 1 1.4-.3 15 15 0 0 0 3.1.5V22a2 2 0 0 1-2.2 2A19 19 0 0 1 0 4.2 2 2 0 0 1 2 2h5.3a1.4 1.4 0 0 1 1.4 1.2c.1 1 .3 2.1.6 3.1a1.4 1.4 0 0 1-.3 1.4Z"/>',
  award:     '<path d="M12 15a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/><path d="m8.6 13.8-1.1 7.1 4.5-2.7 4.5 2.7-1.1-7.1"/>',
  book:      '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
  building:  '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M10 6h4M10 10h4M10 14h4M10 18h4"/>',
  flask:     '<path d="M10 2v7.5L4.6 18a2 2 0 0 0 1.7 3h11.4a2 2 0 0 0 1.7-3L14 9.5V2"/><path d="M8.5 2h7M6.8 14h10.4"/>',
  scalpel:   '<path d="M20 4 8.5 15.5 3 21"/><path d="M20 4v6h-6"/><path d="M3 21h6"/>',
  users:     '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/>',
  cap:       '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/>',
};

/* ============================================================
   settings — ভবিষ্যতের `settings` টেবিল (key/value)
   ============================================================ */
export const SETTINGS = {
  doctor_name:      { bn: 'প্রফেসর ডা. আবু সুফিয়ান',            en: 'Professor Dr. Md. Abu Sufian' },
  doctor_short:     { bn: 'ডা. আবু সুফিয়ান',                    en: 'Dr. Abu Sufian' },
  degrees:          { bn: 'এমবিবিএস, ডিসিএইচ, এফসিপিএস (শিশুরোগ)', en: 'MBBS, DCH, FCPS (Pediatrics)' },
  specialty:        { bn: 'সিনিয়র শিশু বিশেষজ্ঞ ও শিশুরোগ পরামর্শক', en: 'Senior Child Specialist & Pediatric Consultant' },
  designation:      { bn: 'অধ্যাপক, শিশু বিভাগ',                 en: 'Professor, Department of Pediatrics' },
  tagline:          { bn: 'আপনার শিশুর সুস্থতা, আমাদের অঙ্গীকার',  en: "Your child's wellbeing, our commitment" },
  bmdc:             { bn: '১৮১২৫',                              en: '18125' },

  intro: {
    bn: 'শিশুস্বাস্থ্যে দীর্ঘ পেশাগত অভিজ্ঞতাসম্পন্ন একজন চিকিৎসক। হবিগঞ্জ মেডিকেল কলেজের প্রতিষ্ঠাতা অধ্যক্ষ এবং ব্রাহ্মণবাড়িয়া মেডিকেল কলেজের সাবেক অধ্যাপক ও অধ্যক্ষ। আইসিডিডিআর,বি-তে গবেষণা এবং দেশের শীর্ষস্থানীয় মেডিকেল কলেজগুলোতে অধ্যাপনার অভিজ্ঞতা নিয়ে নবজাতক থেকে কিশোর বয়স পর্যন্ত শিশুদের সাধারণ ও জটিল রোগের আধুনিক, প্রমাণভিত্তিক চিকিৎসা ও পরামর্শ প্রদান করেন।',
    en: 'A physician with extensive professional experience in child health. Founder Principal of Habiganj Medical College and former Professor and Principal of Brahmanbaria Medical College. With a background in research at icddr,b and teaching at leading medical colleges of the country, he provides modern, evidence-based treatment and counselling for common and complex childhood illnesses — from newborns through adolescence.'
  },

  /* যোগাযোগ */
  hotline:          '01327804433',        // কল ও হোয়াটসঅ্যাপ — একই নম্বর
  whatsapp:         '01327804433',
  whatsapp_intl:    '8801327804433',
  email:            'info@drabusufian.com',        // DEMO: ইমেইল এখনো তৈরি হয়নি
  website:          'drabusufian.com',             // DEMO: ডোমেইন এখনো কেনা হয়নি

  facebook:         'https://www.facebook.com/share/1D69J2tEUT/',
  facebook_alt:     'https://www.facebook.com/share/185mAdFVFB/',
  youtube:          'https://youtube.com/@drabusufian-h6m',

  /* ভিজিট ফি — DEMO মান।
     show_fees মিথ্যা রাখা হয়েছে যাতে ভুল ফি ভুলেও লাইভে না যায়।
     প্রকৃত ফি পাওয়ার পর অ্যাডমিন প্যানেল থেকে বসিয়ে টগল চালু করতে হবে। */
  show_fees:        false,
  fee_new:          1000,
  fee_followup:     600,
  fee_report:       400,
  followup_days:    15,
};

/* ============================================================
   chambers — `chambers` টেবিল  (একাধিক চেম্বার সাপোর্টেড)
   ============================================================ */
export const CHAMBERS = [
  {
    id: 1,
    name:    { bn: 'ইবনে সিনা ডায়াগনস্টিক অ্যান্ড কনসালটেশন সেন্টার, বাড্ডা',
               en: 'Ibn Sina Diagnostic & Consultation Center, Badda' },
    address: { bn: 'বাড়ি-চ-৭২/১, প্রগতি সরণি, উত্তর বাড্ডা, ঢাকা-১২১২',
               en: 'House-Cha-72/1, Progoti Sharani, North Badda, Dhaka-1212' },
    hotline: '01327804433',
    map_query: 'Ibn Sina Diagnostic and Consultation Center, Progoti Sharani, North Badda, Dhaka 1212',
    is_active: true,
  },
];

/* ============================================================
   schedules — `schedules` টেবিল
   ------------------------------------------------------------
   সময় নেওয়া হয়েছে ✅ ভিজিটিং কার্ড থেকে (ক্লায়েন্টের সিদ্ধান্ত, ০১ আগস্ট ২০২৬)
   JS getDay(): 0=রবি, 1=সোম, 2=মঙ্গল, 3=বুধ, 4=বৃহস্পতি, 5=শুক্র, 6=শনি
   ============================================================ */
export const SCHEDULES = [
  {
    id: 1, chamber_id: 1,
    days: [6, 0, 1, 2, 3, 4],                    // শনি → বৃহস্পতি
    label: { bn: 'শনিবার – বৃহস্পতিবার', en: 'Saturday – Thursday' },
    start: '10:30', end: '14:00',
    slot_minutes: 8, max_serials: 25,
    is_active: true,
  },
  {
    id: 2, chamber_id: 1,
    days: [5],                                    // শুক্রবার
    label: { bn: 'শুক্রবার', en: 'Friday' },
    start: '17:00', end: '20:00',
    slot_minutes: 7, max_serials: 25,
    is_active: true,
  },
];

/* ছুটি / বন্ধের দিন — `holidays` টেবিল */
export const HOLIDAYS = [
  // DEMO: অ্যাডমিন প্যানেল থেকে যোগ করা যাবে
  // { date: '2026-08-15', reason: { bn: 'চেম্বার বন্ধ', en: 'Chamber closed' } },
];

/* ============================================================
   notices — `notices` টেবিল (হোমপেজের উপরে ব্যানার)
   ============================================================ */
export const NOTICES = [
  {
    id: 1,
    severity: 'info',
    title: { bn: 'অনলাইনে সিরিয়াল নেওয়া যাচ্ছে',
             en: 'Online serial booking is now available' },
    body:  { bn: 'ঘরে বসেই তারিখ ও সময় বেছে নিয়ে সিরিয়াল নিন। প্রতিদিন সর্বোচ্চ ২৫টি সিরিয়াল।',
             en: 'Pick your date and time from home. Maximum 25 serials per day.' },
    is_active: true,
  },
];

/* ============================================================
   experiences — `experiences` টেবিল
   সূত্র: ✅ প্রচারপত্র + ভিজিটিং কার্ড
   ============================================================ */
export const EXPERIENCES = [
  { id: 1, icon: 'building',
    position:     { bn: 'প্রতিষ্ঠাতা অধ্যক্ষ',  en: 'Founder Principal' },
    organization: { bn: 'হবিগঞ্জ মেডিকেল কলেজ', en: 'Habiganj Medical College' },
    is_current: false, sort_order: 1 },

  { id: 2, icon: 'cap',
    position:     { bn: 'সাবেক অধ্যক্ষ ও বিভাগীয় প্রধান, শিশু বিভাগ',
                    en: 'Former Principal & Head, Department of Pediatrics' },
    organization: { bn: 'ব্রাহ্মণবাড়িয়া মেডিকেল কলেজ', en: 'Brahmanbaria Medical College' },
    is_current: false, sort_order: 2 },

  { id: 3, icon: 'stetho',
    position:     { bn: 'সাবেক বিশেষজ্ঞ চিকিৎসক', en: 'Former Consultant' },
    organization: { bn: 'বাংলাদেশ মেডিকেল বিশ্ববিদ্যালয় (সাবেক পিজি হাসপাতাল)',
                    en: 'Bangladesh Medical University (former PG Hospital)' },
    is_current: false, sort_order: 3 },

  { id: 4, icon: 'flask',
    position:     { bn: 'সাবেক গবেষক', en: 'Former Researcher' },
    organization: { bn: 'আইসিডিডিআর,বি (icddr,b)', en: 'icddr,b' },
    is_current: false, sort_order: 4 },

  { id: 5, icon: 'scalpel',
    position:     { bn: 'সাবেক হাউস সার্জন', en: 'Former House Surgeon' },
    organization: { bn: 'বক্ষব্যাধি হাসপাতাল, মহাখালী, ঢাকা',
                    en: 'Chest Diseases Hospital, Mohakhali, Dhaka' },
    is_current: false, sort_order: 5 },

  { id: 7, icon: 'users',
    position:     { bn: 'সাবেক লেকচারার', en: 'Former Lecturer' },
    organization: { bn: 'স্যার সলিমুল্লাহ মেডিকেল কলেজ ও মিটফোর্ড হাসপাতাল',
                    en: 'Sir Salimullah Medical College & Mitford Hospital' },
    is_current: false, sort_order: 6 },

  { id: 6, icon: 'book',
    position:     { bn: 'সাবেক রেজিস্ট্রার', en: 'Former Registrar' },
    organization: { bn: 'সিলেট এম. এ. জি. ওসমানী মেডিকেল কলেজ হাসপাতাল',
                    en: 'Sylhet M. A. G. Osmani Medical College Hospital' },
    is_current: false, sort_order: 7 },
];

/* ============================================================
   qualifications — `qualifications` টেবিল
   ⚠️ DEMO: প্রতিষ্ঠান ও সাল এখনো পাওয়া যায়নি (সেকশন ১১, প্রশ্ন ৫)
   ============================================================ */
export const QUALIFICATIONS = [
  { id: 1, degree: { bn: 'এফসিপিএস (শিশুরোগ)', en: 'FCPS (Pediatrics)' },
    institution: { bn: 'বাংলাদেশ কলেজ অব ফিজিশিয়ানস অ্যান্ড সার্জনস', en: 'Bangladesh College of Physicians & Surgeons' },
    year: '', sort_order: 1 },
  { id: 2, degree: { bn: 'ডিসিএইচ (শিশু স্বাস্থ্য ডিপ্লোমা)', en: 'DCH (Diploma in Child Health)' },
    institution: { bn: '', en: '' }, year: '', sort_order: 2 },
  { id: 3, degree: { bn: 'এমবিবিএস', en: 'MBBS' },
    institution: { bn: '', en: '' }, year: '', sort_order: 3 },
];

/* ============================================================
   services — `services` টেবিল
   সূত্র: ✅ প্রচারপত্র (১৪টি)। যত ইচ্ছা যোগ/বিয়োগ করা যাবে।
   ============================================================ */
export const SERVICES = [
  { id: 1,  icon: 'baby',     tone: 'sky',    title: { bn: 'নবজাতকের সকল সমস্যা ও পরিচর্যা', en: 'Newborn care & neonatal problems' } },
  { id: 2,  icon: 'thermo',   tone: 'rose',   title: { bn: 'জ্বর, সর্দি-কাশি, নিউমোনিয়া ও শ্বাসকষ্ট', en: 'Fever, cough, pneumonia & respiratory distress' } },
  { id: 3,  icon: 'wind',     tone: 'cyan',   title: { bn: 'হাঁপানি (অ্যাজমা) ও অ্যালার্জিজনিত রোগ', en: 'Asthma & allergic diseases' } },
  { id: 4,  icon: 'droplet',  tone: 'amber',  title: { bn: 'ডায়রিয়া, আমাশয়, বমি ও পানিশূন্যতা', en: 'Diarrhoea, dysentery, vomiting & dehydration' } },
  { id: 5,  icon: 'apple',    tone: 'green',  title: { bn: 'অপুষ্টি, খাওয়ায় অরুচি ও বৃদ্ধি-সংক্রান্ত সমস্যা', en: 'Malnutrition, poor appetite & growth problems' } },
  { id: 6,  icon: 'utensils', tone: 'lime',   title: { bn: 'শিশুদের পুষ্টি ও খাদ্যাভ্যাস বিষয়ক পরামর্শ', en: 'Child nutrition & dietary counselling' } },
  { id: 7,  icon: 'pulse',    tone: 'violet', title: { bn: 'খিঁচুনি, জ্বরজনিত খিঁচুনি ও অন্যান্য স্নায়ুরোগ', en: 'Seizure, febrile convulsion & neurological disorders' } },
  { id: 8,  icon: 'growth',   tone: 'indigo', title: { bn: 'জন্মগত রোগ ও বিকাশগত বিলম্বের মূল্যায়ন', en: 'Congenital disorders & developmental delay assessment' } },
  { id: 9,  icon: 'gland',    tone: 'teal',   title: { bn: 'থাইরয়েড, হরমোন ও শিশুদের অন্যান্য জটিল রোগ', en: 'Thyroid, hormonal & other complex disorders' } },
  { id: 10, icon: 'droplets', tone: 'blue',   title: { bn: 'প্রস্রাবের সংক্রমণ (ইউটিআই), কিডনি ও মূত্রনালির সমস্যা', en: 'UTI, kidney & urinary tract problems' } },
  { id: 11, icon: 'shield',   tone: 'orange', title: { bn: 'লিভার, জন্ডিস ও পরিপাকতন্ত্রের রোগ', en: 'Liver, jaundice & gastrointestinal diseases' } },
  { id: 12, icon: 'heart',    tone: 'red',    title: { bn: 'হৃদরোগ, রক্তস্বল্পতা ও রক্তের বিভিন্ন রোগ', en: 'Cardiac disease, anaemia & blood disorders' } },
  { id: 13, icon: 'syringe',  tone: 'emerald',title: { bn: 'টিকাদান, বৃদ্ধি ও বিকাশের নিয়মিত ফলো-আপ', en: 'Immunisation, growth & development follow-up' } },
  { id: 14, icon: 'stetho',   tone: 'brand',  title: { bn: 'শিশুদের সকল সাধারণ ও জটিল রোগের আধুনিক চিকিৎসা', en: 'Modern treatment of all common & complex child diseases' } },
];

/* ============================================================
   বিশেষ চিকিৎসা — `services` টেবিলে is_special = true
   সূত্র: ✅ প্রচারপত্রের হাইলাইট বক্স
   ============================================================ */
export const SPECIAL = [
  { id: 101, icon: 'wind',   tone: 'sky',
    title: { bn: 'দীর্ঘমেয়াদি কাশি', en: 'Chronic Cough' },
    note:  { bn: 'দীর্ঘদিন ধরে না সারা কাশির কারণ নির্ণয় ও চিকিৎসা', en: 'Diagnosis and treatment of persistent, long-standing cough' } },
  { id: 102, icon: 'pulse',  tone: 'violet',
    title: { bn: 'অ্যাডিনয়েড', en: 'Adenoid' },
    note:  { bn: 'নাক বন্ধ, মুখ খুলে ঘুমানো ও নাক ডাকার আধুনিক চিকিৎসা', en: 'Modern care for blocked nose, mouth breathing and snoring' } },
  { id: 103, icon: 'gland',  tone: 'rose',
    title: { bn: 'টনসিলাইটিস', en: 'Tonsillitis' },
    note:  { bn: 'বারবার গলাব্যথা ও টনসিল ফোলার চিকিৎসা ও পরামর্শ', en: 'Treatment and advice for recurrent sore throat and swollen tonsils' } },
  { id: 104, icon: 'cloud',  tone: 'teal',
    title: { bn: 'সাইনুসাইটিস', en: 'Sinusitis' },
    note:  { bn: 'মাথাব্যথা, নাক দিয়ে পানি পড়া ও সাইনাস সংক্রমণের চিকিৎসা', en: 'Care for headache, runny nose and sinus infection' } },
];

/* ============================================================
   gallery — `gallery` টেবিল
   ⚠️ খালি রাখা হয়েছে → সেকশনটি স্বয়ংক্রিয়ভাবে লুকিয়ে থাকবে।
      চেম্বারের ছবি পাওয়া গেলে এখানে যোগ হবে।
   ============================================================ */
export const GALLERY = [];

/* ============================================================
   testimonials — `testimonials` টেবিল
   ⚠️ ইচ্ছাকৃতভাবে খালি → সেকশনটি লুকানো থাকবে।
      সিদ্ধান্ত: প্রকৃত রোগীর মতামত না পাওয়া পর্যন্ত কিছু লেখা হবে না।
      চিকিৎসা পেশায় বানানো রিভিউ নৈতিকভাবে গ্রহণযোগ্য নয়।
   ============================================================ */
export const TESTIMONIALS = [];

/* ============================================================
   faqs — `faqs` টেবিল
   ============================================================ */
export const FAQS = [
  { id: 1,
    q: { bn: 'সিরিয়াল কীভাবে নেব?', en: 'How do I book a serial?' },
    a: { bn: 'এই ওয়েবসাইটের ক্যালেন্ডার থেকে তারিখ ও সময় বেছে নিয়ে ফর্মটি পূরণ করুন। এরপর "হোয়াটসঅ্যাপে কনফার্ম করুন" বাটনে চাপ দিলে আপনার সিরিয়ালের তথ্যসহ একটি বার্তা তৈরি হয়ে যাবে — শুধু Send চাপলেই হবে। চাইলে সরাসরি হটলাইনে ফোন করেও সিরিয়াল নিতে পারেন।',
         en: 'Pick a date and time from the calendar on this website and fill in the form. Then tap "Confirm on WhatsApp" — a message with your serial details is created automatically, you just press Send. You may also call the hotline directly.' } },
  { id: 2,
    q: { bn: 'প্রতিদিন কতজন রোগী দেখা হয়?', en: 'How many patients are seen each day?' },
    a: { bn: 'প্রতিদিন সর্বোচ্চ ২৫টি সিরিয়াল দেওয়া হয়। সিরিয়াল শেষ হয়ে গেলে ক্যালেন্ডারে সেই তারিখটি বন্ধ দেখাবে।',
         en: 'A maximum of 25 serials are issued per day. Once they are full, that date shows as closed on the calendar.' } },
  { id: 3,
    q: { bn: 'কত সময় আগে চেম্বারে পৌঁছাব?', en: 'How early should I arrive?' },
    a: { bn: 'আপনার নির্ধারিত সময়ের অন্তত ১৫ মিনিট আগে পৌঁছানোর অনুরোধ করা হচ্ছে। ওয়েবসাইটে দেখানো সময়টি আনুমানিক — রোগীর অবস্থা অনুযায়ী কিছুটা আগে-পরে হতে পারে।',
         en: 'Please arrive at least 15 minutes before your scheduled time. The time shown is approximate and may shift slightly depending on patient needs.' } },
  { id: 4,
    q: { bn: 'সাথে কী কী নিয়ে যাব?', en: 'What should I bring?' },
    a: { bn: 'শিশুর আগের প্রেসক্রিপশন, করানো পরীক্ষার রিপোর্ট, টিকার কার্ড এবং বর্তমানে চলমান ওষুধের তালিকা সাথে আনুন।',
         en: "Bring the child's previous prescriptions, any test reports, the immunisation card, and a list of current medicines." } },
  { id: 5,
    q: { bn: 'সিরিয়াল বাতিল বা পরিবর্তন করা যাবে?', en: 'Can I cancel or reschedule?' },
    a: { bn: 'হ্যাঁ। হোয়াটসঅ্যাপ নম্বরে আপনার বুকিং কোডসহ বার্তা পাঠালে সিরিয়াল বাতিল বা পরিবর্তন করে দেওয়া হবে। আসতে না পারলে আগেভাগে জানালে অন্য একজন রোগী সেই সিরিয়ালটি পেতে পারেন।',
         en: 'Yes. Send a message with your booking code to the WhatsApp number and it will be cancelled or rescheduled. Letting us know early frees the slot for another patient.' } },
];

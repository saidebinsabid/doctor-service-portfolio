/*
|--------------------------------------------------------------------------
| পাবলিক সাইটের জাভাস্ক্রিপ্ট
|--------------------------------------------------------------------------
| ⭐ পুরো সাইট জাভাস্ক্রিপ্ট ছাড়াও কাজ করে।
|
|   কনটেন্ট সার্ভার থেকেই রেন্ডার হয়ে আসে — গুগল সবকিছু দেখতে পায়,
|   আর ধীর ইন্টারনেটেও লেখা সাথে সাথে দেখা যায়।
|
|   এখানকার কোড শুধু সুবিধা বাড়ায়: মোবাইল মেনু, নোটিশ বন্ধ করা,
|   আর বুকিং ক্যালেন্ডারে তারিখ বদলালে পুরো পাতা রিলোড না করা।
|   জাভাস্ক্রিপ্ট বন্ধ থাকলে ক্যালেন্ডার সাধারণ লিংক হিসেবেই চলবে।
*/

/* ---------- স্ক্রল করলে হেডারে ছায়া ---------- */
const header = document.getElementById('site-header');
if (header) {
    const onScroll = () => header.classList.toggle('shadow-md', window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

/* ---------- মোবাইল মেনু ---------- */
const menuToggle = document.getElementById('menu-toggle');
const mobileNav = document.getElementById('mobile-nav');

if (menuToggle && mobileNav) {
    menuToggle.addEventListener('click', () => {
        const hidden = mobileNav.classList.toggle('hidden');
        menuToggle.setAttribute('aria-expanded', String(!hidden));
    });

    mobileNav.querySelectorAll('a').forEach((a) =>
        a.addEventListener('click', () => {
            mobileNav.classList.add('hidden');
            menuToggle.setAttribute('aria-expanded', 'false');
        }),
    );
}

/* ---------- নোটিশ বন্ধ করা ----------
   একই সেশনে আর দেখাবে না, কিন্তু পরের বার এলে আবার দেখবে —
   গুরুত্বপূর্ণ ঘোষণা যেন চিরতরে হারিয়ে না যায় */
const noticeBar = document.getElementById('notice-bar');
const noticeClose = document.getElementById('notice-close');

if (noticeBar && noticeClose) {
    const key = 'notice-closed-' + noticeBar.dataset.noticeId;

    if (sessionStorage.getItem(key)) {
        noticeBar.remove();
    } else {
        noticeClose.addEventListener('click', () => {
            sessionStorage.setItem(key, '1');
            noticeBar.remove();
        });
    }
}

/* ---------- FAQ অ্যাকর্ডিয়ন ----------
   <details> ট্যাগ ব্যবহার করায় জাভাস্ক্রিপ্ট ছাড়াই খোলে-বন্ধ হয়।
   এখানে শুধু একসাথে একটির বেশি খোলা না রাখার ব্যবস্থা। */
document.querySelectorAll('[data-faq] details').forEach((d) => {
    d.addEventListener('toggle', () => {
        if (!d.open) return;
        d.closest('[data-faq]')
            .querySelectorAll('details')
            .forEach((other) => { if (other !== d) other.open = false; });
    });
});

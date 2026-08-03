/*
|--------------------------------------------------------------------------
| অ্যাডমিন প্যানেলের জাভাস্ক্রিপ্ট
|--------------------------------------------------------------------------
| মোবাইলে সাইডবার খোলা-বন্ধ, মুছে ফেলার আগে নিশ্চিতকরণ, ফ্ল্যাশ বার্তা
| কয়েক সেকেন্ড পর নিজে থেকে মিলিয়ে যাওয়া।
*/

/* ---------- মোবাইল সাইডবার ---------- */
const sidebar = document.getElementById('admin-sidebar');
const backdrop = document.getElementById('sidebar-backdrop');
const openBtn = document.getElementById('sidebar-open');
const closeBtn = document.getElementById('sidebar-close');

const setSidebar = (open) => {
    if (!sidebar) return;
    sidebar.classList.toggle('-translate-x-full', !open);
    backdrop?.classList.toggle('hidden', !open);
};

openBtn?.addEventListener('click', () => setSidebar(true));
closeBtn?.addEventListener('click', () => setSidebar(false));
backdrop?.addEventListener('click', () => setSidebar(false));

/* ---------- মুছে ফেলার নিশ্চিতকরণ ---------- */
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        if (!window.confirm(form.dataset.confirm || 'আপনি কি নিশ্চিত?')) {
            e.preventDefault();
        }
    });
});

/* ---------- ফ্ল্যাশ বার্তা নিজে থেকে মিলিয়ে যাওয়া ---------- */
document.querySelectorAll('[data-flash]').forEach((el) => {
    setTimeout(() => {
        el.style.transition = 'opacity .4s ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }, 4000);
});

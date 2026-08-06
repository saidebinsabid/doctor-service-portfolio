/*
|--------------------------------------------------------------------------
| বুকিং ক্যালেন্ডার
|--------------------------------------------------------------------------
| ⭐ জাভাস্ক্রিপ্ট ছাড়াও পুরো বুকিং কাজ করে।
|
|   ক্যালেন্ডারের তারিখগুলো সাধারণ <a> লিংক — ক্লিক করলে পাতা রিলোড
|   হয়ে সার্ভার থেকেই স্লট আসে। এখানকার কোড শুধু সেই রিলোডটুকু
|   বাঁচায়, যাতে ধীর ইন্টারনেটেও অভিজ্ঞতা মসৃণ থাকে।
*/

const grid = document.getElementById('calendar-grid');
const slotWrap = document.getElementById('slot-wrap');
const form = document.getElementById('booking-form');

if (grid && slotWrap && form) {
    const fDate = document.getElementById('f-date');
    const fTime = document.getElementById('f-time');
    const submit = document.getElementById('booking-submit');
    const summary = document.getElementById('booking-summary');
    const errorBox = document.getElementById('form-error');

    const setSummary = (key, value) => {
        const el = summary?.querySelector(`[data-summary="${key}"]`);
        if (el) el.textContent = value;
    };

    /* ---------- তারিখে ক্লিক ---------- */
    grid.addEventListener('click', async (e) => {
        const link = e.target.closest('a.cal-day');
        if (!link) return;

        e.preventDefault();

        grid.querySelectorAll('a.cal-day').forEach((a) =>
            a.setAttribute('aria-pressed', String(a === link)));

        const date = link.dataset.date;
        fDate.value = date;
        fTime.value = '';
        submit.disabled = false;   // সময় ঐচ্ছিক — তারিখ বাছলেই জমা দেওয়া যায় (না বাছলে অটো সময়)
        summary?.classList.remove('hidden');
        setSummary('time', '—');
        setSummary('serial', '—');

        /* ঠিকানায় তারিখটি রেখে দেওয়া, যাতে রিফ্রেশ করলেও নির্বাচন থাকে */
        const url = new URL(window.location);
        url.searchParams.set('date', date);
        window.history.replaceState({}, '', url);

        slotWrap.innerHTML = '<p class="text-center text-sm text-slate-400 py-4">…</p>';

        try {
            const res = await fetch(`${slotWrap.dataset.slotsUrl}?date=${date}`, {
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) throw new Error('slots');

            const data = await res.json();
            renderSlots(data);
        } catch {
            /* কিছু ভুল হলে সাধারণ লিংকের মতো পাতাই লোড করে দেওয়া হয় —
               রোগী যেন কোনোভাবেই আটকে না যান */
            window.location.href = link.href;
        }
    });

    function renderSlots(data) {
        if (data.status !== 'open') {
            slotWrap.innerHTML =
                `<p class="text-center text-sm text-slate-400 py-3">${escapeHtml(data.reason || '')}</p>`;
            return;
        }

        const buttons = data.slots.map((s) => `
            <button type="button" class="slot" data-time="${s.time}" data-serial="${s.serial}"
                    aria-pressed="false" ${s.available ? '' : 'disabled'}>${escapeHtml(s.label)}</button>
        `).join('');

        slotWrap.innerHTML = `
            <p class="font-bold text-brand-900 flex items-center gap-2 mb-1">
                <span class="grid place-items-center w-7 h-7 rounded-lg bg-brand-900 text-white
                             text-xs font-bold">${slotWrap.dataset.stepTwo || '২'}</span>
                ${escapeHtml(slotWrap.dataset.stepTwoLabel || '')}
            </p>
            <p class="text-xs text-slate-500 mb-3 ms-9">${escapeHtml(data.date_label)}</p>
            <div class="grid grid-cols-3 sm:grid-cols-4 gap-1.5">${buttons}</div>`;
    }

    /* ---------- সময়ে ক্লিক (নতুন ও পুরনো দুই ক্ষেত্রেই) ---------- */
    slotWrap.addEventListener('click', (e) => {
        const btn = e.target.closest('button.slot');
        if (!btn || btn.disabled) return;

        slotWrap.querySelectorAll('button.slot').forEach((b) =>
            b.setAttribute('aria-pressed', String(b === btn)));

        fTime.value = btn.dataset.time;
        submit.disabled = false;

        summary?.classList.remove('hidden');
        setSummary('time', btn.textContent.trim());
        setSummary('serial', btn.dataset.serialBn || btn.dataset.serial);
    });

    /* ---------- জমা দেওয়ার আগে যাচাই ----------
       সার্ভারেও একই যাচাই হয়; এটি শুধু রোগীকে দ্রুত জানানোর জন্য */
    form.addEventListener('submit', (e) => {
        const problems = [];
        const phone = form.patient_phone.value.replace(/\D/g, '');

        if (!form.patient_name.value.trim()) problems.push(form.dataset.errName);
        else if (!/^01[3-9]\d{8}$/.test(phone)) problems.push(form.dataset.errPhone);
        /* সময় ঐচ্ছিক — না বাছলে সার্ভার ওই দিনের পরের খালি সময় অটো বসিয়ে দেয় */

        if (problems.length) {
            e.preventDefault();
            errorBox.textContent = problems[0];
            errorBox.classList.remove('hidden');
            errorBox.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            return;
        }

        errorBox.classList.add('hidden');
        /* দুইবার চাপ দিয়ে দুটি সিরিয়াল নেওয়া ঠেকানো */
        submit.disabled = true;
        submit.classList.add('opacity-70');
    });

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, (c) =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    }
}

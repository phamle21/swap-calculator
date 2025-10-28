const $ = (s, p = document) => p.querySelector(s);
const $$ = (s, p = document) => [...p.querySelectorAll(s)];
const CSRF = $('meta[name="csrf-token"]')?.content || '';

const fmtMoney = n =>
    (n < 0 ? '-$' : '$') + Math.abs(Number(n || 0)).toFixed(2);

async function api(url, opts = {}) {
    const init = {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        ...opts
    };
    const res = await fetch(url, init);
    if (!res.ok) throw await res.json();
    return res.json();
}

async function loadHistory() {
    // build query from filters and pagination state
    const params = new URLSearchParams();
    const filterPair = $('#filterPair') ? $('#filterPair').value : '';
    const filterType = $('#filterType') ? $('#filterType').value : '';
    const filterFrom = $('#filterFrom') ? $('#filterFrom').value : '';
    const filterTo = $('#filterTo') ? $('#filterTo').value : '';
    const filterMin = $('#filterMin') ? $('#filterMin').value : '';
    const filterMax = $('#filterMax') ? $('#filterMax').value : '';
    const perPage = $('#perPage') ? $('#perPage').value : 10;
    const page = window.__historyPage || 1;

    if (filterPair) params.set('pair', filterPair);
    if (filterType) params.set('position_type', filterType);
    if (filterFrom) params.set('date_from', filterFrom);
    if (filterTo) params.set('date_to', filterTo);
    if (filterMin) params.set('min_total', filterMin);
    if (filterMax) params.set('max_total', filterMax);
    if (perPage) params.set('per_page', perPage);
    if (page) params.set('page', page);

    const data = await api('/history?' + params.toString());
    // API may respond with {data: [...], meta: {...}} or {items: {data: [...]}}
    const items = data.data || data.items?.data || [];
    const meta = data.meta || data.items?.meta || null;
    const body = $('#historyBody');

    body.innerHTML = items.map(h => `
        <tr>
        <td class="py-2">${h.pair}</td>
        <td>${Number(h.lot_size).toFixed(2)}</td>
        <td><span class="px-2 py-1 rounded text-xs font-semibold bg-green-900/30 text-green-400 ${h.position_type === 'Long' ? 'text-emerald-400' : 'text-blue-400'}">${h.position_type}</span></td>
        <td>${fmtMoney(h.swap_rate)}</td>
        <td>${h.days}</td>
        <td class="${h.total_swap < 0 ? 'text-red-400' : 'text-emerald-400'} font-medium">${fmtMoney(h.total_swap)}</td>
        <td>${h.created_at}</td>
                <td><button data-id="${h.id}" class="del-btn text-red-400 hover:text-red-300 text-xs">${(window.SWAP_I18N && window.SWAP_I18N.deleteLabel) || 'Delete'}</button></td>
        </tr>
    `).join('');

    $$('.del-btn').forEach(btn => btn.onclick = async () => {
        const title = (window.SWAP_I18N && window.SWAP_I18N.confirmDelete) || 'Delete?';
        const confirmYes = (window.SWAP_I18N && window.SWAP_I18N.confirmYes) || 'Yes';
        const confirmNo = (window.SWAP_I18N && window.SWAP_I18N.confirmNo) || 'Cancel';
        const res = await Swal.fire({
            title: title,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmYes,
            cancelButtonText: confirmNo,
        });
        if (!res.isConfirmed) return;
        await api(`/history/${btn.dataset.id}`, { method: 'DELETE' });
        await loadHistory();
        Swal.fire({ title: (window.SWAP_I18N && window.SWAP_I18N.deletedSuccess) || 'Deleted', icon: 'success', timer: 1200, showConfirmButton: false });
    });

    // render simple pagination controls if meta present
    const pager = $('#historyPager');
    if (pager) {
        if (!meta) {
            pager.innerHTML = '';
        } else {
            const cur = meta.current_page || meta.currentPage || 1;
            const last = meta.last_page || meta.lastPage || (meta.total ? Math.ceil((meta.total || 0) / (meta.per_page || meta.perPage || 10)) : 1);
            const prevLabel = (window.SWAP_I18N && window.SWAP_I18N.pagePrev) || 'Prev';
            const nextLabel = (window.SWAP_I18N && window.SWAP_I18N.pageNext) || 'Next';
            pager.innerHTML = `
                <div class="mt-3 flex items-center justify-between text-sm text-slate-300">
                    <div>Page ${cur} / ${last}</div>
                    <div class="flex gap-2">
                        <button id="pgPrev" class="px-2 py-1 rounded bg-slate-700">${prevLabel}</button>
                        <button id="pgNext" class="px-2 py-1 rounded bg-slate-700">${nextLabel}</button>
                    </div>
                </div>
            `;
            const prev = $('#pgPrev');
            const next = $('#pgNext');
            if (prev) prev.onclick = async () => { if (cur > 1) { window.__historyPage = cur - 1; await loadHistory(); } };
            if (next) next.onclick = async () => { if (cur < last) { window.__historyPage = cur + 1; await loadHistory(); } };
        }
    }

    // update visible filter tags
    try { renderFilterTags(); } catch (e) {/* ignore */ }
}

function renderResult(r) {
    // Defensive updates in case elements are not present
    const elPair = $('#r-pair');
    if (elPair) elPair.textContent = r.pair;

    const elPos = $('#r-position_type');
    if (elPos) elPos.innerHTML = `<span class="${r.position_type === 'Long' ? 'text-emerald-400' : 'text-blue-400'}">${r.position_type}</span>`;

    const elLot = $('#r-lot_size');
    if (elLot) elLot.textContent = Number(r.lot_size).toFixed(2);

    const elRate = $('#r-swap_rate');
    if (elRate) elRate.innerHTML = `<span class="${r.swap_rate < 0 ? 'text-red-400' : 'text-emerald-400'}">${fmtMoney(r.swap_rate)}</span>`;

    const elDays = $('#r-days');
    if (elDays) elDays.textContent = r.days;

    const elTotal = $('#r-total_swap');
    if (elTotal) elTotal.innerHTML = `<span class="${r.total_swap < 0 ? 'text-red-400' : 'text-emerald-400'}">${fmtMoney(r.total_swap)}</span>`;

    const adv = $('#advise');
    if (adv) {
        adv.classList.remove('hidden');
        adv.classList.remove('border-red-700', 'bg-red-900/30', 'border-green-700', 'bg-green-900/30');
        adv.querySelector('span')?.classList.remove('text-red-300', 'text-green-300');
        if (r.total_swap < 0) {
            adv.classList.add('border-red-700', 'bg-red-900/30');
            adv.querySelector('span') && (adv.querySelector('span').classList.add('text-red-300'), adv.querySelector('span').textContent = r.message);
        } else {
            adv.classList.add('border-green-700', 'bg-green-900/30');
            adv.querySelector('span') && (adv.querySelector('span').classList.add('text-green-300'), adv.querySelector('span').textContent = r.message);
        }
    }
}

function renderFilterTags() {
    const container = $('#filterTags');
    if (!container) return;
    container.innerHTML = '';
    const mappings = [
        { id: 'filterPair', label: (window.SWAP_I18N && window.SWAP_I18N.pair) || 'Pair' },
        { id: 'filterType', label: (window.SWAP_I18N && window.SWAP_I18N.type) || 'Type' },
        { id: 'filterFrom', label: (window.SWAP_I18N && window.SWAP_I18N.from) || 'From' },
        { id: 'filterTo', label: (window.SWAP_I18N && window.SWAP_I18N.to) || 'To' },
        { id: 'filterMin', label: (window.SWAP_I18N && window.SWAP_I18N.min) || 'Min' },
        { id: 'filterMax', label: (window.SWAP_I18N && window.SWAP_I18N.max) || 'Max' },
    ];

    mappings.forEach(m => {
        const el = document.getElementById(m.id);
        if (!el) return;
        const val = el.value;
        if (val === null || val === undefined || val === '') return;
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-2 bg-slate-700 text-white px-3 py-1 rounded text-sm';
        const text = document.createElement('span');
        text.textContent = `${m.label}: ${val}`;
        const btn = document.createElement('button');
        btn.className = 'ml-2 text-slate-300 hover:text-white';
        btn.setAttribute('data-filter-key', m.id);
        btn.innerHTML = '&times;';
        span.appendChild(text);
        span.appendChild(btn);
        container.appendChild(span);
    });

    // delegate clicks to remove a filter
    container.onclick = function (e) {
        const btn = e.target.closest('button[data-filter-key]');
        if (!btn) return;
        const key = btn.getAttribute('data-filter-key');
        const inp = document.getElementById(key);
        if (inp) {
            inp.value = '';
            // reset perPage when removing filters? keep as is
        }
        window.__historyPage = 1;
        loadHistory();
    };
}

async function onSubmit(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    try {
        const data = await api('/calculate', { method: 'POST', body: fd });
        renderResult(data.result);
        await loadHistory();
    } catch (err) {
        console.error(err);
        alert('Lỗi: ' + JSON.stringify(err.errors || err));
    }
}

function boot() {
    const form = $('#calcForm');
    if (form) form.addEventListener('submit', onSubmit);

    const reload = $('#reloadHistory');
    if (reload) reload.onclick = loadHistory;

    const filter = $('#filterPair');
    if (filter) filter.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const filterType = $('#filterType');
    if (filterType) filterType.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const filterFrom = $('#filterFrom');
    if (filterFrom) filterFrom.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const filterTo = $('#filterTo');
    if (filterTo) filterTo.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const filterMin = $('#filterMin');
    if (filterMin) filterMin.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const filterMax = $('#filterMax');
    if (filterMax) filterMax.onchange = () => { window.__historyPage = 1; loadHistory(); };
    const perPageSelect = $('#perPage');
    if (perPageSelect) perPageSelect.onchange = () => { window.__historyPage = 1; loadHistory(); };

    const clearBtn = $('#clearHistory');
    if (clearBtn) clearBtn.onclick = async () => {
        const title = (window.SWAP_I18N && window.SWAP_I18N.confirmClear) || 'Delete all?';
        const confirmYes = (window.SWAP_I18N && window.SWAP_I18N.confirmYes) || 'Yes';
        const confirmNo = (window.SWAP_I18N && window.SWAP_I18N.confirmNo) || 'Cancel';
        const res = await Swal.fire({
            title: title,
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmYes,
            cancelButtonText: confirmNo,
        });
        if (!res.isConfirmed) {
            Swal.fire({ title: (window.SWAP_I18N && window.SWAP_I18N.deletedCanceled) || 'Canceled', icon: 'info', timer: 800, showConfirmButton: false });
            return;
        }
        await api('/history', { method: 'DELETE' });
        await loadHistory();
        Swal.fire({ title: (window.SWAP_I18N && window.SWAP_I18N.deletedSuccess) || 'Deleted', icon: 'success', timer: 1200, showConfirmButton: false });
    };

    const applyBtn = $('#applyFilters');
    const resetBtn = $('#resetFilters');
    // SlideOver toggle logic
    const toggleFilterBtn = $('#toggleFilterBtn');
    const filterSlideOver = $('#filterSlideOver');
    const slidePanel = $('#slidePanel');
    const closeFilterSlide = $('#closeFilterSlide');
    const backdrop = $('#filterBackdrop');
    const closeSlide = () => {
        if (slidePanel) slidePanel.classList.add('translate-x-full');
        setTimeout(() => { if (filterSlideOver) filterSlideOver.classList.add('hidden'); }, 250);
    };

    if (applyBtn) applyBtn.onclick = () => { window.__historyPage = 1; loadHistory(); closeSlide(); };
    if (resetBtn) resetBtn.onclick = () => {
        const inputs = ['#filterPair', '#filterType', '#filterFrom', '#filterTo', '#filterMin', '#filterMax', '#perPage'];
        inputs.forEach(s => { const el = $(s); if (!el) return; if (el.tagName === 'SELECT' || el.tagName === 'INPUT') { el.value = ''; } });
        // reset perPage to default 10
        const per = $('#perPage'); if (per) per.value = '10';
        window.__historyPage = 1;
        loadHistory();
        closeSlide();
    };

    if (toggleFilterBtn && filterSlideOver && slidePanel) {
        toggleFilterBtn.addEventListener('click', () => {
            filterSlideOver.classList.remove('hidden');
            setTimeout(() => slidePanel.classList.remove('translate-x-full'), 10);
            const first = slidePanel.querySelector('select, input');
            if (first) first.focus();
        });
        if (closeFilterSlide) closeFilterSlide.addEventListener('click', closeSlide);
        if (backdrop) backdrop.addEventListener('click', closeSlide);
    }

    loadHistory();
}
document.addEventListener('DOMContentLoaded', boot);

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
                <td><button data-id="${h.id}" class="cursor-pointer del-btn text-red-400 hover:text-red-300 text-xs">${(window.SWAP_I18N && window.SWAP_I18N.deleteLabel) || 'Delete'}</button></td>
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

function toggleSwapInputs() {
    try {
        const pos = document.querySelector('input[name="position_type"]:checked')?.value || 'Long';
        const longInput = $('#swap_long');
        const shortInput = $('#swap_short');
        if (pos === 'Long') {
            if (longInput) {
                longInput.removeAttribute('disabled');
                longInput.classList.remove('opacity-50');
                longInput.setAttribute('aria-disabled', 'false');
            }
            if (shortInput) {
                shortInput.setAttribute('disabled', 'disabled');
                shortInput.classList.add('opacity-50');
                shortInput.setAttribute('aria-disabled', 'true');
            }
        } else {
            if (shortInput) {
                shortInput.removeAttribute('disabled');
                shortInput.classList.remove('opacity-50');
                shortInput.setAttribute('aria-disabled', 'false');
            }
            if (longInput) {
                longInput.setAttribute('disabled', 'disabled');
                longInput.classList.add('opacity-50');
                longInput.setAttribute('aria-disabled', 'true');
            }
        }
    } catch (e) { /* ignore */ }
}

async function onSubmit(e) {
    e.preventDefault();
    clearValidationErrors();
    const clientErrors = clientValidateForm();
    if (clientErrors && Object.keys(clientErrors).length) {
        showValidationErrors(clientErrors);
        return;
    }
    const fd = new FormData(e.target);
    try {
        const data = await api('/calculate', { method: 'POST', body: fd });
        renderResult(data.result);
        await loadHistory();
    } catch (err) {
        console.error(err);
        clearValidationErrors();
        if (err && err.errors) {
            showValidationErrors(err.errors);
        } else {
            alert('Lỗi: ' + JSON.stringify(err));
        }
    }
}

function clientValidateForm() {
    const t = (window.SWAP_I18N && window.SWAP_I18N.swapFormValidation) || {};
    const errors = {};

    const getMsg = (key, attr) => {
        const map = {
            required: t.required || 'This field is required.',
            numeric: t.numeric || 'Must be a valid number.',
            positive: t.positive || 'Must be greater than 0.',
            position: t.position || 'Position must be Long or Short.'
        };
        return (map[key] || '').replace(':attribute', attr || 'Field');
    };

    // pair
    const pair = $('#pair')?.value?.trim() || '';
    if (!pair) errors.pair = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.pairLabel) || 'Pair') ];

    // lot_size
    const lot = $('#lot_size')?.value;
    if (lot === undefined || lot === null || String(lot).trim() === '') {
        errors.lot_size = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.lotLabel) || 'Lot size') ];
    } else if (!isFinite(Number(lot))) {
        errors.lot_size = [ getMsg('numeric', (window.SWAP_I18N && window.SWAP_I18N.lotLabel) || 'Lot size') ];
    } else if (Number(lot) <= 0) {
        errors.lot_size = [ getMsg('positive', (window.SWAP_I18N && window.SWAP_I18N.lotLabel) || 'Lot size') ];
    }

    // swap rates (only validate the one used by selected position)
    const selectedPos = document.querySelector('input[name="position_type"]:checked')?.value;
    const sLong = $('#swap_long')?.value;
    const sShort = $('#swap_short')?.value;
    if (selectedPos === 'Long') {
        if (sLong === undefined || sLong === null || String(sLong).trim() === '') {
            errors.swap_long = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.swapLongLabel) || 'Swap Long') ];
        } else if (!isFinite(Number(sLong))) {
            errors.swap_long = [ getMsg('numeric', (window.SWAP_I18N && window.SWAP_I18N.swapLongLabel) || 'Swap Long') ];
        }
    } else if (selectedPos === 'Short') {
        if (sShort === undefined || sShort === null || String(sShort).trim() === '') {
            errors.swap_short = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.swapShortLabel) || 'Swap Short') ];
        } else if (!isFinite(Number(sShort))) {
            errors.swap_short = [ getMsg('numeric', (window.SWAP_I18N && window.SWAP_I18N.swapShortLabel) || 'Swap Short') ];
        }
    }

    // days
    const days = $('#days')?.value;
    if (days === undefined || days === null || String(days).trim() === '') {
        errors.days = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.daysLabel) || 'Days') ];
    } else if (!isFinite(Number(days))) {
        errors.days = [ getMsg('numeric', (window.SWAP_I18N && window.SWAP_I18N.daysLabel) || 'Days') ];
    } else if (Number(days) <= 0) {
        errors.days = [ getMsg('positive', (window.SWAP_I18N && window.SWAP_I18N.daysLabel) || 'Days') ];
    }

    // position_type
    const pos = document.querySelector('input[name="position_type"]:checked')?.value;
    if (!pos) {
        errors.position_type = [ getMsg('required', (window.SWAP_I18N && window.SWAP_I18N.positionLabel) || 'Position') ];
    } else if (!(pos === 'Long' || pos === 'Short')) {
        errors.position_type = [ getMsg('position') ];
    }

    return errors;
}

function clearValidationErrors() {
    // hide all error containers
    document.querySelectorAll('[data-error-for]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    // remove error class from inputs
    ['pair','lot_size','swap_long','swap_short','position_type','days'].forEach(k => {
        const inp = document.getElementById(k) || document.querySelector(`[name="${k}"]`);
        if (inp) inp.classList.remove('border-red-500');
        // for radio group, remove on all radios
        const radios = document.querySelectorAll(`[name="${k}"]`);
        radios.forEach(r => r.classList.remove('border-red-500'));
    });
    // remove ring/highlight from visible radio spans
    const posSpans = document.querySelectorAll('#pos_long + span, #pos_short + span');
    posSpans.forEach(s => s.classList.remove('ring-2', 'ring-red-500', 'border-red-500'));
}

function showValidationErrors(errors) {
    // errors is an object where values are arrays of messages
    let firstFocus = null;
    const alias = {
        holding_days: 'days',
        holdingDays: 'days'
    };

    for (const key in errors) {
        if (!errors.hasOwnProperty(key)) continue;
        const msgs = Array.isArray(errors[key]) ? errors[key] : [errors[key]];
        const tryKeys = [key];
        if (alias[key]) tryKeys.push(alias[key]);
        // also try replacing '.' nested keys
        if (key.indexOf('.') !== -1) tryKeys.push(key.split('.')[0]);

        let placed = false;
        for (const tk of tryKeys) {
            let container = document.querySelector(`[data-error-for="${tk}"]`);
            const inp = document.getElementById(tk) || document.querySelector(`[name="${tk}"]`);

            // If container not present, create it dynamically and insert after the input or its parent
            if (!container) {
                if (inp) {
                    container = document.createElement('p');
                    container.setAttribute('data-error-for', tk);
                    container.className = 'mt-1 text-sm text-red-400';
                    // insert after input (for radios, insert after the group container)
                    if (inp.type === 'radio') {
                        const group = inp.closest('div') || inp.parentElement;
                        if (group && group.parentElement) group.parentElement.insertBefore(container, group.nextSibling);
                        else inp.parentElement.insertBefore(container, inp.nextSibling);
                    } else {
                        const parent = inp.parentElement || inp.closest('div') || inp;
                        if (parent && parent.parentElement) parent.parentElement.insertBefore(container, parent.nextSibling);
                        else inp.insertAdjacentElement('afterend', container);
                    }
                } else {
                    // fallback: try to find a label with for=tk and insert after that label
                    const lbl = document.querySelector(`label[for="${tk}"]`) || document.querySelector(`label:contains("${tk}")`);
                    if (lbl && lbl.parentElement) {
                        container = document.createElement('p');
                        container.setAttribute('data-error-for', tk);
                        container.className = 'mt-1 text-sm text-red-400';
                        lbl.parentElement.insertBefore(container, lbl.nextSibling);
                    }
                }
            }

            if (container) {
                container.textContent = msgs.join(' ');
                container.classList.remove('hidden');
                // highlight input
                if (inp) inp.classList.add('border-red-500');
                // for radio groups, mark visible spans
                if (tk === 'position_type') {
                    const p1 = document.getElementById('pos_long');
                    const p2 = document.getElementById('pos_short');
                    if (p1 && p1.nextElementSibling) p1.nextElementSibling.classList.add('ring-2', 'ring-red-500');
                    if (p2 && p2.nextElementSibling) p2.nextElementSibling.classList.add('ring-2', 'ring-red-500');
                }
                // determine focus element
                const focusEl = inp || document.querySelector(`[name="${tk}"]`);
                if (!firstFocus && focusEl) firstFocus = focusEl;
                placed = true;
                break;
            }
        }
        if (!placed) {
            // fallback: show alert for unexpected errors
            console.warn('Unplaced validation error', key, msgs);
        }
    }

    if (firstFocus) {
        try { firstFocus.focus(); } catch (e) { /* ignore */ }
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

    // position radio change: toggle which swap input is active
    const posRadios = document.querySelectorAll('input[name="position_type"]');
    if (posRadios && posRadios.length) {
        posRadios.forEach(r => r.addEventListener('change', () => { toggleSwapInputs(); }));
        // set initial state
        toggleSwapInputs();
    }

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

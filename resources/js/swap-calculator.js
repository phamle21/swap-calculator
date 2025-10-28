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
    const data = await api('/history');
    const items = data.items?.data || data.items; // resource collection or plain
    const body = $('#historyBody');
    body.innerHTML = items.map(h => `
    <tr>
      <td class="py-2">${h.pair}</td>
      <td>${Number(h.lot_size).toFixed(2)}</td>
      <td><span class="${h.position_type === 'Long' ? 'text-emerald-400' : 'text-blue-400'}">${h.position_type}</span></td>
      <td>${fmtMoney(h.swap_rate)}</td>
      <td>${h.days}</td>
      <td class="${h.total_swap < 0 ? 'text-red-400' : 'text-emerald-400'} font-medium">${fmtMoney(h.total_swap)}</td>
      <td>${h.created_at}</td>
      <td><button data-id="${h.id}" class="del-btn text-red-400 hover:text-red-300 text-xs">Xóa</button></td>
    </tr>
  `).join('');

    $$('.del-btn').forEach(btn => btn.onclick = async () => {
        if (!confirm('Xóa bản ghi này?')) return;
        await api(`/history/${btn.dataset.id}`, { method: 'DELETE' });
        await loadHistory();
    });
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
        if (r.total_swap < 0) {
            adv.classList.remove('hidden');
            adv.querySelector('span') && (adv.querySelector('span').textContent = 'Swap âm, cân nhắc không nên giữ lệnh lâu.');
        } else {
            adv.classList.add('hidden');
        }
    }
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

    loadHistory();
}
document.addEventListener('DOMContentLoaded', boot);

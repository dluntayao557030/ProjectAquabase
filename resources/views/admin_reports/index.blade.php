{{-- resources/views/admin_reports/index.blade.php --}}
@extends('layouts.nav_admin')

@section('title', 'Reports')

@section('hero-text')
    Generate and export reports for the farm's inventory. 📊
@endsection

@push('styles')
<style>
    /* ── PAGE HEADER ─────────────────────────────────────────────────────── */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 0.8rem;
    }

    .page-title {
        font-family: var(--font);
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--teal-mid);
    }

    .btn-export {
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.1rem;
        font-family: var(--font);
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-export:hover    { background: var(--blue-dark); transform: translateY(-1px); }
    .btn-export:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

    /* ── REPORT TYPE BAR ─────────────────────────────────────────────────── */
    .report-type-bar {
        background: var(--cream);
        border: 1px solid var(--blue-border);
        border-radius: 10px;
        padding: 0.9rem 1.2rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        box-shadow: 0 1px 6px rgba(10,40,100,0.06);
    }

    .report-type-label {
        font-family: var(--font);
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-mid);
        white-space: nowrap;
    }

    .report-type-select {
        font-family: var(--font);
        font-size: 0.88rem;
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 7px;
        padding: 0.45rem 0.75rem;
        color: var(--text-dark);
        min-width: 280px;
        outline: none;
        flex: 1;
    }

    .report-type-select:focus { border-color: var(--blue-main); }

    .report-desc {
        font-family: var(--font);
        font-size: 0.78rem;
        color: #888;
        font-style: italic;
        margin-left: auto;
        max-width: 360px;
        text-align: right;
    }

    /* ── REPORT CARD ─────────────────────────────────────────────────────── */
    .report-card {
        background: var(--cream);
        border-radius: 12px;
        border: 1px solid var(--blue-border);
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(10,40,100,0.08);
    }

    /* ── FILTER BAR ──────────────────────────────────────────────────────── */
    .filter-bar {
        background: #dceefb;
        border-bottom: 1px solid var(--blue-border);
        padding: 0.8rem 1.2rem;
        display: flex;
        align-items: flex-end;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .filter-label {
        font-family: var(--font);
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .filter-control {
        font-family: var(--font);
        font-size: 0.82rem;
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 6px;
        padding: 0.38rem 0.65rem;
        color: var(--text-dark);
        outline: none;
        min-width: 130px;
    }

    .filter-control:focus { border-color: var(--blue-main); }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: auto;
        align-items: flex-end;
    }

    .btn-generate {
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 0.42rem 1rem;
        font-family: var(--font);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s;
    }

    .btn-generate:hover    { background: var(--blue-dark); }
    .btn-generate:disabled { opacity: 0.4; cursor: not-allowed; }

    .btn-summary {
        background: transparent;
        color: var(--blue-dark);
        border: 1.5px solid var(--blue-border);
        border-radius: 7px;
        padding: 0.42rem 0.9rem;
        font-family: var(--font);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s;
    }

    .btn-summary:hover { background: var(--blue-pale); }

    /* ── EMPTY STATE ─────────────────────────────────────────────────────── */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon {
        font-size: 3.5rem;
        color: var(--blue-main);
        margin-bottom: 0.8rem;
    }

    /* ── TABLE ───────────────────────────────────────────────────────────── */
    .rpt-table-wrap { overflow-x: auto; display: none; }
    .rpt-table-wrap.show { display: block; }

    .rpt-table {
        width: 100%;
        border-collapse: collapse;
        font-family: var(--font);
        font-size: 0.78rem;
    }

    .rpt-table thead tr { background: var(--blue-main); }
    .rpt-table thead th {
        padding: 0.55rem 0.8rem;
        color: #fff;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .rpt-table tbody tr { border-bottom: 1px solid #d0e8f8; transition: background 0.12s; }
    .rpt-table tbody tr:hover { background: #eaf4fd; }
    .rpt-table tbody td { padding: 0.55rem 0.8rem; color: var(--text-dark); vertical-align: middle; }

    .rpt-table tfoot tr { background: #dceefb; }
    .rpt-table tfoot td { padding: 0.55rem 0.8rem; font-weight: 800; color: var(--blue-dark); }

    .no-results td { text-align: center; padding: 2.5rem; color: #aaa; font-size: 0.85rem; }

    /* ── BADGES ──────────────────────────────────────────────────────────── */
    .badge-in    { background:#d4edbc; color:#2d5a1b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-out   { background:#fde8e8; color:#c0392b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-suf   { background:#d0e8f8; color:#0d3b6e; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-low   { background:#fde8e8; color:#c0392b; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }
    .badge-empty { background:#fff3cd; color:#856404; padding:0.15rem 0.5rem; border-radius:20px; font-size:0.7rem; font-weight:700; }

    /* ── LOADING ─────────────────────────────────────────────────────────── */
    .rpt-loading { padding: 3rem; text-align: center; color: var(--blue-mid); font-size: 0.88rem; display: none; }
    .rpt-loading.show { display: block; }

    /* ── SUMMARY PANEL ───────────────────────────────────────────────────── */
    .summary-panel { display: none; background: #dceefb; border-top: 1px solid var(--blue-border); padding: 1rem 1.4rem; }
    .summary-panel.show { display: block; }

    .summary-title {
        font-family: var(--font);
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--text-mid);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.7rem;
    }

    .summary-grid { display: flex; flex-wrap: wrap; gap: 0.8rem; }

    .summary-item {
        background: var(--cream);
        border: 1px solid var(--blue-border);
        border-radius: 8px;
        padding: 0.6rem 1rem;
        min-width: 130px;
        text-align: center;
    }

    .summary-value { font-family: var(--font); font-size: 1.4rem; font-weight: 800; color: var(--blue-dark); display: block; }
    .summary-label { font-size: 0.7rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.04em; }

    /* ── TOAST ───────────────────────────────────────────────────────────── */
    .fb-toast {
        position: fixed;
        bottom: 1.5rem; right: 1.5rem;
        z-index: 99999;
        background: var(--blue-dark);
        color: #fff;
        border-radius: 10px;
        padding: 0.8rem 1.2rem;
        font-family: var(--font);
        font-size: 0.88rem;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(10,40,100,0.3);
        transform: translateY(20px);
        opacity: 0;
        transition: transform 0.3s, opacity 0.3s;
        max-width: 320px;
        pointer-events: none;
    }

    .fb-toast.show  { transform: translateY(0); opacity: 1; }
    .fb-toast.error { background: #922b21; }

    /* ── MOBILE ──────────────────────────────────────────────────────────── */
    @media (max-width: 768px) {
        .report-type-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .report-type-select { min-width: unset; width: 100%; }
        .report-desc        { margin-left: 0; text-align: left; max-width: none; }

        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-control { min-width: unset; width: 100%; }

        .filter-actions {
            margin-left: 0;
            flex-direction: row;
            width: 100%;
        }

        .btn-generate,
        .btn-summary { flex: 1; text-align: center; }

        .page-header { flex-direction: column; align-items: stretch; }
        .btn-export  { width: 100%; justify-content: center; }

        .summary-grid { flex-direction: column; }
        .summary-item { min-width: unset; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Reports</div>
    <button class="btn-export" id="btnExport" disabled onclick="exportPDF()">
        📄 Export Report
    </button>
</div>

<div class="report-type-bar">
    <span class="report-type-label">Report Type:</span>
    <select class="report-type-select" id="reportTypeSelect" onchange="onReportTypeChange()">
        <option value="">— Select a report type —</option>
        <option value="stock_movement">Stock Movement History</option>
        <option value="current_inventory">Current Stock Inventory</option>
        <option value="low_stock">Low Stock / Reorder Alert</option>
        <option value="purchases_supplier">Total Purchases per Supplier</option>
        <option value="most_consumed">Most Consumed Supplies</option>
    </select>
    <span class="report-desc" id="reportDesc"></span>
</div>

<div class="report-card">
    <div class="filter-bar" id="filterBar">
        <div id="filterControls" style="display:flex;gap:0.7rem;flex-wrap:wrap;align-items:flex-end;flex:1;">
            <div style="font-family:var(--font);font-size:0.82rem;color:#aaa;align-self:center;">
                Select a report type to see its filters.
            </div>
        </div>
        <div class="filter-actions">
            <button class="btn-summary" id="btnSummary" onclick="toggleSummary()">📊 Summary</button>
            <button class="btn-generate" id="btnGenerate" onclick="generateReport()" disabled>Generate</button>
        </div>
    </div>

    <div class="rpt-loading" id="rptLoading">⏳ Generating report...</div>

    <div class="empty-state" id="emptyState">
        <div class="empty-icon">📄</div>
        <h3>Generate a Report</h3>
        <p>Select a report type and apply filters, then click <strong>Generate</strong>.</p>
    </div>

    <div class="rpt-table-wrap" id="rptTableWrap">
        <table class="rpt-table" id="rptTable">
            <thead id="rptThead"></thead>
            <tbody id="rptTbody"></tbody>
            <tfoot id="rptTfoot"></tfoot>
        </table>
    </div>

    <div class="summary-panel" id="summaryPanel">
        <div class="summary-title">📊 Report Summary</div>
        <div class="summary-grid" id="summaryGrid"></div>
    </div>
</div>

<div class="fb-toast" id="fbToast"></div>

<script>
    const categories  = {!! json_encode($categories) !!};
    const supplies    = {!! json_encode($supplies) !!};
    const staffList   = {!! json_encode($staffList) !!};
    const reportRoute = "{{ route('admin.reports.generate') }}";
    const csrfToken   = "{{ csrf_token() }}";
</script>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
const REPORTS = {
    stock_movement: {
        desc: 'Complete history of all stock-in and stock-out transactions, including expiry date.',
        filters: ['date_from','date_to','delivery_date_from','delivery_date_to','txn_type','category','supply','user'],
        columns: ['Date','Type','Supply','Category','Qty','Unit','Staff','Supplier','Cost','Receipt','Delivery Date','Expiry Date','Purpose','Remarks'],
        keys:    ['transaction_date','transaction_type','supply_name','category_name','quantity','unit_measure','performed_by','supplier_name','cost','receipt_no','delivery_date','expiry_date','purpose','remarks'],
    },
    current_inventory: {
        desc: 'Real-time snapshot of all active supplies with stock levels.',
        filters: ['category','stock_status'],
        columns: ['Supply','Category','Unit','Stock','Reorder Level','Status'],
        keys:    ['supply_name','category_name','unit_measure','current_stock','reorder_level','stock_alert'],
    },
    low_stock: {
        desc: 'Supplies at or below reorder level that need replenishment.',
        filters: ['category'],
        columns: ['Supply','Category','Unit','Stock','Reorder Level','Shortage','Status'],
        keys:    ['supply_name','category_name','unit_measure','current_stock','reorder_level','shortage_quantity','stock_status'],
    },
    purchases_supplier: {
        desc: 'Total purchases and amount spent grouped per supplier.',
        filters: ['date_from','date_to'],
        columns: ['Supplier','Total Stock Ins','Total Amount Spent','Avg Cost per Transaction'],
        keys:    ['supplier_name','total_stock_in','total_amount_spent','avg_transaction_cost'],
    },
    most_consumed: {
        desc: 'Supplies with the highest consumption — useful for planning.',
        filters: ['date_from','date_to','category'],
        columns: ['Supply','Unit','Total Consumed','No. of Usages','Avg per Usage'],
        keys:    ['supply_name','unit_measure','total_quantity_consumed','number_of_usage','average_per_usage'],
    },
};

const FILTER_TEMPLATES = {
    date_from:    () => fg('From Date',   `<input type="date" class="filter-control" id="ff_date_from">`),
    date_to:      () => fg('To Date',     `<input type="date" class="filter-control" id="ff_date_to">`),
    delivery_date_from: () => fg('Delivery From', `<input type="date" class="filter-control" id="ff_delivery_date_from">`),
    delivery_date_to:   () => fg('Delivery To',   `<input type="date" class="filter-control" id="ff_delivery_date_to">`),
    txn_type:     () => fg('Type',        `<select class="filter-control" id="ff_txn_type"><option value="">All</option><option value="stock_in">Stock In</option><option value="stock_out">Stock Out</option></select>`),
    category:     () => fg('Category',    `<select class="filter-control" id="ff_category"><option value="">All Categories</option>${categories.map(c=>`<option value="${c.category_id}">${c.category_name}</option>`).join('')}</select>`),
    supply:       () => fg('Supply',      `<select class="filter-control" id="ff_supply"><option value="">All Supplies</option>${supplies.map(s=>`<option value="${s.supply_id}">${s.supply_name}</option>`).join('')}</select>`),
    user:         () => fg('Staff',       `<select class="filter-control" id="ff_user"><option value="">All Staff</option>${staffList.map(u=>`<option value="${u.user_id}">${u.full_name}</option>`).join('')}</select>`),
    stock_status: () => fg('Stock Status',`<select class="filter-control" id="ff_stock_status"><option value="">All</option><option value="sufficient">Sufficient</option><option value="low_stock">Low Stock</option><option value="out_of_stock">Out of Stock</option></select>`),
};

function fg(label, html) {
    return `<div class="filter-group"><span class="filter-label">${label}</span>${html}</div>`;
}

function onReportTypeChange() {
    const type = document.getElementById('reportTypeSelect').value;

    document.getElementById('emptyState').style.display = 'block';
    document.getElementById('rptTableWrap').classList.remove('show');
    document.getElementById('summaryPanel').classList.remove('show');
    document.getElementById('btnExport').disabled  = true;
    document.getElementById('btnGenerate').disabled = true;
    document.getElementById('reportDesc').textContent = '';

    if (!type) {
        document.getElementById('filterControls').innerHTML =
            `<div style="font-family:var(--font);font-size:0.82rem;color:#aaa;align-self:center;">Select a report type to see its filters.</div>`;
        return;
    }

    const rpt = REPORTS[type];
    document.getElementById('reportDesc').textContent = rpt.desc;
    document.getElementById('filterControls').innerHTML =
        rpt.filters.map(f => FILTER_TEMPLATES[f] ? FILTER_TEMPLATES[f]() : '').join('');
    document.getElementById('btnGenerate').disabled = false;
}

function generateReport() {
    const type = document.getElementById('reportTypeSelect').value;
    if (!type) return;

    const ids = ['ff_date_from','ff_date_to','ff_delivery_date_from','ff_delivery_date_to','ff_txn_type','ff_category',
                 'ff_supply','ff_user','ff_stock_status'];
    const body = { report_type: type };
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) body[id.replace('ff_','')] = el.value;
    });

    document.getElementById('rptLoading').classList.add('show');
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('rptTableWrap').classList.remove('show');
    document.getElementById('summaryPanel').classList.remove('show');

    fetch(reportRoute, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept':'application/json' },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('rptLoading').classList.remove('show');
        renderTable(data, type);
        renderSummary(data.summary);
        document.getElementById('btnExport').disabled = false;
    })
    .catch(() => {
        document.getElementById('rptLoading').classList.remove('show');
        showToast('Failed to generate report. Please try again.', 'error');
    });
}

function renderTable(data, type) {
    const rpt   = REPORTS[type];
    const thead = document.getElementById('rptThead');
    const tbody = document.getElementById('rptTbody');
    const tfoot = document.getElementById('rptTfoot');

    thead.innerHTML = `<tr>${rpt.columns.map(c=>`<th>${c}</th>`).join('')}</tr>`;

    if (!data.rows || data.rows.length === 0) {
        tbody.innerHTML = `<tr class="no-results"><td colspan="${rpt.columns.length}">No data found for the selected filters.</td></tr>`;
        tfoot.innerHTML = '';
    } else {
        tbody.innerHTML = data.rows.map(row =>
            `<tr>${rpt.keys.map(k => `<td>${formatCell(k, row[k])}</td>`).join('')}</tr>`
        ).join('');
        tfoot.innerHTML = '';
    }

    document.getElementById('rptTableWrap').classList.add('show');
}

function formatCell(key, val) {
    if (val === null || val === undefined || val === '') return '<span style="color:#ccc;">—</span>';
    if (key === 'transaction_type') return val === 'stock_in'
        ? '<span class="badge-in">Stock In</span>'
        : '<span class="badge-out">Stock Out</span>';
    if (key === 'stock_alert' || key === 'stock_status') {
        if (val === 'Sufficient')   return '<span class="badge-suf">Sufficient</span>';
        if (val === 'Low Stock')    return '<span class="badge-low">Low Stock</span>';
        if (val === 'Out of Stock') return '<span class="badge-empty">Out of Stock</span>';
    }
    if (['cost','total_amount_spent','avg_transaction_cost'].includes(key)) {
        const n = parseFloat(val);
        return isNaN(n) ? val : '₱' + n.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
    }
    if (key === 'delivery_date' && val) {
        return new Date(val).toLocaleDateString('en-PH');
    }
    if (key === 'expiry_date' && val) {
        return new Date(val).toLocaleDateString('en-PH');
    }
    return val;
}

function renderSummary(summary) {
    if (!summary) return;
    document.getElementById('summaryGrid').innerHTML = Object.entries(summary).map(([label, value]) =>
        `<div class="summary-item">
            <span class="summary-value">${value}</span>
            <span class="summary-label">${label}</span>
         </div>`
    ).join('');
}

function toggleSummary() {
    document.getElementById('summaryPanel').classList.toggle('show');
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const type      = document.getElementById('reportTypeSelect').value;
    if (!type) return;

    const doc  = new jsPDF({ orientation: 'landscape' });
    const rpt  = REPORTS[type];
    const date = new Date().toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });

    doc.setFont('courier','bold');
    doc.setFontSize(16);
    doc.text("Aquabase – Claudio's Aquafarm", 14, 16);
    doc.setFontSize(11);
    doc.text(rpt.desc.split(':')[0].trim(), 14, 23);
    doc.setFont('courier','normal');
    doc.setFontSize(9);
    doc.setTextColor(100);
    doc.text('Generated: ' + date, 14, 29);
    doc.setTextColor(0);

    const summaryItems = document.querySelectorAll('.summary-item');
    let sy = 35;
    if (summaryItems.length > 0) {
        doc.setFontSize(8); doc.setFont('courier','bold');
        doc.text('SUMMARY', 14, sy); sy += 4;
        summaryItems.forEach((item, i) => {
            const val   = item.querySelector('.summary-value').textContent;
            const label = item.querySelector('.summary-label').textContent;
            doc.setFont('courier','normal'); doc.setFontSize(8);
            doc.text(`${label}: ${val}`, 14 + (i % 4) * 70, sy + Math.floor(i / 4) * 6);
        });
        sy += Math.ceil(summaryItems.length / 4) * 6 + 8;
    }

    const headers  = Array.from(document.querySelectorAll('#rptThead th')).map(th => th.textContent);
    const bodyRows = Array.from(document.querySelectorAll('#rptTbody tr')).map(tr =>
        Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim())
    );

    doc.autoTable({
        startY: sy,
        head: [headers], body: bodyRows,
        styles: { font:'courier', fontSize:7.5, cellPadding:2 },
        headStyles: { fillColor: [30,136,229], textColor: 255, fontStyle:'bold' },
        alternateRowStyles: { fillColor: [234,244,253] },
        margin: { left:14, right:14 },
    });

    const label = document.getElementById('reportTypeSelect').selectedOptions[0].text;
    doc.save(`Aquabase_${label.replace(/\s+/g,'_')}_${date.replace(/\s+/g,'_')}.pdf`);
    showToast('Report exported as PDF.');
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('fbToast');
    t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
    t.className   = 'fb-toast show' + (type === 'error' ? ' error' : '');
    setTimeout(() => t.classList.remove('show'), 3500);
}
</script>
@endpush
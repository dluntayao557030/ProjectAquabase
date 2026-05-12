@extends('layouts.nav_admin')

@section('title', 'Dashboard')

@section('hero-text')
    Welcome back, <strong>{{ session('first_name') }}</strong>. Here's what's happening at Claudio's Aquafarm today. 🌾
@endsection

@section('hero-text-mobile')
    📊 Look at your farm's stats!
@endsection

@push('styles')
<style>
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.8rem;
    }

    .kpi-card {
        background: linear-gradient(135deg, #7a5c00 0%, #5c4200 100%);
        border-radius: 14px;
        padding: 1.5rem 1.6rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        box-shadow: 0 4px 16px rgba(90, 60, 0, 0.25);
        border: 1px solid rgba(200, 150, 10, 0.3);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(90, 60, 0, 0.35);
    }

    .kpi-left { flex: 1; }
    .kpi-icon { width: 54px; height: 54px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    .kpi-label { font-size: 0.78rem; font-weight: 700; color: rgba(255,255,255,0.85); text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-value { font-size: 2.9rem; font-weight: 800; color: #fff; line-height: 1; }

    .bottom-section {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 1.5rem;
    }

    .chart-card, .alert-card {
        background: var(--cream);
        border-radius: 14px;
        border: 1px solid var(--green-border);
        padding: 1.4rem;
        box-shadow: 0 3px 12px rgba(20,50,8,0.08);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 1.1rem;
        padding-bottom: 0.6rem;
        border-bottom: 2px solid var(--green-pale);
    }

    .supply-alert-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
    }

    .supply-alert-table th {
        background: var(--blue-main);
        color: #fff;
        padding: 0.7rem 0.8rem;
        text-align: left;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .supply-alert-table td {
        padding: 0.75rem 0.8rem;
        border-bottom: 1px solid #e8f0e0;
    }

    .kpi-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,40,5,0.6);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .kpi-modal-backdrop.show { display: flex; }
    .kpi-modal {
        background: var(--cream);
        border-radius: 20px;
        width: 100%;
        max-width: 540px;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        border: 1px solid var(--green-border);
        animation: modalPop 0.25s ease;
    }
    @keyframes modalPop {
        from { transform: scale(0.95); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }
    .kpi-modal-header {
        background: var(--blue-main);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 800;
        font-size: 1.2rem;
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }
    .kpi-modal-count {
        font-size: 0.85rem;
        background: rgba(255,255,255,0.2);
        padding: 0.2rem 0.6rem;
        border-radius: 30px;
    }
    .kpi-modal-list {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    .kpi-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0;
        border-bottom: 1px solid #e8f0e0;
    }
    .kpi-item-info {
        flex: 2;
    }
    .kpi-item-name {
        font-weight: 800;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    .kpi-item-detail {
        font-size: 0.75rem;
        color: #777;
        margin-top: 2px;
    }
    .kpi-item-right {
        text-align: right;
        min-width: 100px;
    }
    .kpi-badge {
        display: inline-block;
        background: #f0f0f0;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        margin-bottom: 4px;
    }
    .kpi-badge-instock { background: #fff3cd; color: #856404; }
    .kpi-badge-lowstock { background: #fde8e8; color: #c0392b; }
    .kpi-badge-outstock { background: #e9ecef; color: #495057; }
    .kpi-badge-in  { background: #e8f5e0; color: #2e7d32; }
    .kpi-badge-out { background: #ffebee; color: #c62828; }
    .kpi-badge-ok  { background: #fff3cd; color: #856404; }
    .kpi-item-value {
        font-weight: 800;
        font-size: 1rem;
        display: block;
    }
    .kpi-empty {
        text-align: center;
        padding: 2rem;
        color: #888;
    }
    .kpi-modal-footer {
        padding: 0.8rem 1.5rem;
        border-top: 1px solid var(--green-border);
        text-align: right;
    }
    .kpi-close-btn {
        background: var(--blue-main);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.2rem;
        color: white;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }
    .kpi-close-btn:hover { background: var(--blue-dark); }

    .fb-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #2e7d32;
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        font-weight: 600;
        display: none;
        align-items: center;
        gap: 8px;
        z-index: 10000;
        min-width: 280px;
    }
    .fb-toast.show { display: flex; }
    .fb-toast.error { background: #c62828; }

    @media (max-width: 992px) {
        .bottom-section { grid-template-columns: 1fr; }
        .chart-card { margin-bottom: 0.5rem; }
    }
    @media (max-width: 768px) {
        .kpi-row { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
        .kpi-card { padding: 1.2rem; }
        .kpi-value { font-size: 2.4rem; }
        .chart-card, .alert-card { padding: 1.1rem; }
        .supply-alert-table thead { display: none; }
        .supply-alert-table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid var(--green-border);
            border-radius: 10px;
            padding: 1rem;
            background: #fff;
        }
        .supply-alert-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px dashed #e0e9d4;
        }
        .supply-alert-table td:last-child { border-bottom: none; }
        .supply-alert-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--green-mid);
            width: 140px;
        }
        .kpi-modal { max-width: 95%; max-height: 90vh; }
    }
    @media (max-width: 576px) {
        .kpi-row { grid-template-columns: 1fr; }
        .chart-card canvas { max-height: 240px !important; }
    }
</style>
@endpush

@section('content')
<div class="kpi-row">
    <div class="kpi-card" onclick="openKpiModal('inventory', 'Total Inventory', '📦')">
        <div class="kpi-left">
            <img src="/images/icons/KPITotalSupplies.png" class="kpi-icon" alt="">
            <div class="kpi-label">Inventory On Hand</div>
        </div>
        <div class="kpi-value">{{ $totalSupplies }}</div>
    </div>
    <div class="kpi-card" onclick="openKpiModal('stock-in', 'Stock In Today', '📥')">
        <div class="kpi-left">
            <img src="/images/icons/KPIStockIn.png" class="kpi-icon" alt="">
            <div class="kpi-label">Added Today</div>
        </div>
        <div class="kpi-value">{{ $suppliesAddedToday }}</div>
    </div>
    <div class="kpi-card" onclick="openKpiModal('stock-out', 'Stock Out Today', '📤')">
        <div class="kpi-left">
            <img src="/images/icons/KPIStockOut.png" class="kpi-icon" alt="">
            <div class="kpi-label">Used Today</div>
        </div>
        <div class="kpi-value">{{ $suppliesUsedToday }}</div>
    </div>
</div>

<div class="bottom-section">
    <div class="chart-card">
        <div class="card-title">🟢 Supply Distribution by Category</div>
        <div style="height: 340px; width: 100%;">
            @if($categoryData->count() > 0)
                <canvas id="categoryChart"></canvas>
            @else
                <div style="height:100%;display:flex;align-items:center;justify-content:center;color:#888;">No data available yet</div>
            @endif
        </div>
    </div>

    <div class="alert-card">
        <div class="card-title">🔴 Low Stock Alert</div>
        @if($lowStockSupplies->count() > 0)
            <table class="supply-alert-table" id="alertTable">
                <thead>
                    <tr><th>ID</th><th>Supply</th><th>Category</th><th>Stock</th><th>Reorder</th></tr>
                </thead>
                <tbody>
                    @foreach($lowStockSupplies as $supply)
                    @php $supId = strtoupper(substr($supply->category_name ?? 'SUP', 0, 3)) . str_pad($supply->id, 4, '0', STR_PAD_LEFT); @endphp
                    <tr>
                        <td data-label="ID"><strong class="supply-id">{{ $supId }}</strong></td>
                        <td data-label="Supply">{{ $supply->supply_name }}</td>
                        <td data-label="Category">{{ $supply->category_name ?? 'N/A' }}</td>
                        <td data-label="Stock" class="text-danger fw-bold">{{ $supply->stock }}</td>
                        <td data-label="Reorder">{{ $supply->reorder_level }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align:center; padding:3rem 1rem; color:#28a745; font-weight:600;">✅ All supplies are currently above reorder levels.</div>
        @endif
    </div>
</div>

<!-- KPI Detail Modal -->
<div class="kpi-modal-backdrop" id="kpiModal" onclick="if(event.target===this)closeKpiModal()">
    <div class="kpi-modal">
        <div class="kpi-modal-header">
            <span id="kpiModalTitle">Details</span>
            <span class="kpi-modal-count" id="kpiModalCount"></span>
        </div>
        <div class="kpi-modal-list" id="kpiModalList"></div>
        <div class="kpi-modal-footer">
            <button class="kpi-close-btn" onclick="closeKpiModal()">Close</button>
        </div>
    </div>
</div>
<div class="fb-toast" id="fbToast"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    @if($categoryData->count() > 0)
    const ctx = document.getElementById('categoryChart').getContext('2d');
    let categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($categoryData->pluck('category_name')) !!},
            datasets: [{
                data: {!! json_encode($categoryData->pluck('total')) !!},
                backgroundColor: ['#c8960a', '#4e9a30', '#8a6200', '#6bbf42', '#3d7a25', '#e8b820'],
                borderWidth: 3
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { font: { size: 12 } } } } }
    });
    function adjustChartLegend() {
        if (categoryChart) {
            const isMobile = window.innerWidth <= 768;
            categoryChart.options.plugins.legend.position = isMobile ? 'bottom' : 'right';
            categoryChart.update();
        }
    }
    window.addEventListener('resize', adjustChartLegend);
    adjustChartLegend();
    @endif

    const kpiRoutes = {
        'inventory': '{{ route("admin.dashboard.kpi.inventory") }}',
        'stock-in':  '{{ route("admin.dashboard.kpi.stockIn") }}',
        'stock-out': '{{ route("admin.dashboard.kpi.stockOut") }}'
    };

    function openKpiModal(type, title, icon = '') {
        document.getElementById('kpiModalTitle').innerHTML = icon + ' ' + title;
        document.getElementById('kpiModalList').innerHTML = `<div class="kpi-empty">⏳ Loading...</div>`;
        document.getElementById('kpiModalCount').textContent = '0 record(s)';
        document.getElementById('kpiModal').classList.add('show');

        fetch(kpiRoutes[type], { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                console.log(`KPI ${type} data:`, data);
                if (!Array.isArray(data)) data = [];
                renderKpiModal(data, type);
            })
            .catch(err => {
                console.error('KPI fetch error:', err);
                document.getElementById('kpiModalList').innerHTML = `<div class="kpi-empty">Failed to load data.</div>`;
            });
    }

    function renderKpiModal(items, type) {
        const list = document.getElementById('kpiModalList');
        document.getElementById('kpiModalCount').textContent = items.length + ' record(s)';
        if (!items.length) {
            list.innerHTML = `<div class="kpi-empty">No records found for today.</div>`;
            return;
        }
        let html = '';
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            let badgeClass, valueColor, displayStatus;
            if (type === 'inventory') {
                const s = (item.status || '').toLowerCase();
                if (s === 'in stock') { badgeClass = 'kpi-badge-instock'; valueColor = '#c8960a'; displayStatus = 'IN STOCK'; }
                else if (s === 'low stock') { badgeClass = 'kpi-badge-lowstock'; valueColor = '#c62828'; displayStatus = 'LOW STOCK'; }
                else if (s === 'out of stock') { badgeClass = 'kpi-badge-outstock'; valueColor = '#6c757d'; displayStatus = 'OUT OF STOCK'; }
                else { badgeClass = 'kpi-badge-ok'; valueColor = '#2e7d32'; displayStatus = item.status || 'INVENTORY'; }
            } else {
                if (item.status === 'stock_in') { badgeClass = 'kpi-badge-in'; valueColor = '#2e7d32'; displayStatus = 'STOCK IN'; }
                else if (item.status === 'stock_out') { badgeClass = 'kpi-badge-out'; valueColor = '#c62828'; displayStatus = 'STOCK OUT'; }
                else { badgeClass = 'kpi-badge-ok'; valueColor = '#2e7d32'; displayStatus = item.status || 'RECORD'; }
            }
            html += `<div class="kpi-item">
                        <div class="kpi-item-info">
                            <div class="kpi-item-name">${escapeHtml(item.name)}</div>
                            <div class="kpi-item-detail">${escapeHtml(item.detail)}</div>
                            ${item.unit_cost ? `<div class="kpi-item-detail">Unit Cost: ₱${item.unit_cost}</div>` : ''}
                        </div>
                        <div class="kpi-item-right">
                            <span class="kpi-badge ${badgeClass}">${displayStatus}</span>
                            <span class="kpi-item-value" style="color:${valueColor}">${item.value}</span>
                        </div>
                    </div>`;
        }
        list.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' })[m] || m);
    }

    function closeKpiModal() { document.getElementById('kpiModal').classList.remove('show'); }
    function showToast(msg, type = 'success') {
        const t = document.getElementById('fbToast');
        t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
        t.className = 'fb-toast show' + (type === 'error' ? ' error' : '');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeKpiModal(); });
    @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
    @if(session('success')) showToast('{{ session('success') }}'); @endif
</script>
@endpush
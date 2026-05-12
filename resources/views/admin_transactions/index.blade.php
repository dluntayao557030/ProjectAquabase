@extends('layouts.nav_admin')

@section('title', 'Stock Transactions')

@section('hero-text')
    Record stock movements for farm supplies. ➕➖
@endsection

@section('hero-text-mobile')
    ➕➖ Do stock in and stock out.
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
        gap: 0.8rem;
    }
    .page-title {
        font-size: 1.48rem;
        font-weight: 800;
        color: var(--blue-mid);
    }
    .btn-history {
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.2rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-history:hover { background: var(--blue-dark); transform: translateY(-1px); }

    .table-card {
        background: var(--blue-pale);
        border-radius: 14px;
        border: 1px solid var(--blue-border);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(10,40,100,0.08);
    }
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid var(--blue-border);
        background: #f0f7ff;
        flex-wrap: wrap;
        gap: 0.8rem;
    }
    .toolbar-filters {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: wrap;
    }
    .search-input {
        background: #fff;
        border: 1.6px solid var(--blue-border);
        border-radius: 25px;
        padding: 0.45rem 1rem 0.45rem 2.3rem;
        font-size: 0.84rem;
        width: 240px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231e88e5' stroke-width='2.6'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.85rem center;
    }
    .filter-select {
        background: #fff;
        border: 1.6px solid var(--blue-border);
        border-radius: 25px;
        padding: 0.45rem 1rem;
        font-size: 0.84rem;
        min-width: 150px;
    }
    .table-wrapper {
        max-height: 550px;
        overflow-y: auto;
        overflow-x: auto;
    }
    .txn-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
    }
    .txn-table thead th {
        padding: 0.75rem 0.9rem;
        background: var(--blue-main);
        color: #fff;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .txn-table tbody tr {
        border-bottom: 1px solid #d9eaff;
        transition: background 0.2s;
    }
    .txn-table tbody tr:hover { background: rgba(30,136,229,0.05); }
    .txn-table td {
        padding: 0.8rem 0.9rem;
        vertical-align: middle;
    }
    .supply-thumb, .supply-thumb-placeholder {
        width: 46px; height: 46px;
        border-radius: 8px;
        border: 1px solid var(--blue-border);
        object-fit: cover;
    }
    .supply-thumb-placeholder {
        background: #e3f2fd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #aaa;
    }
    .supply-id-tag {
        font-weight: 700;
        color: var(--blue-mid);
        font-size: 0.74rem;
        letter-spacing: 0.04em;
    }
    .badge-status {
        padding: 0.28rem 0.7rem;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 700;
        display: inline-block;
    }
    .badge-out { background: #fff3cd; color: #856404; }      /* Orange */
    .badge-low { background: #fde8e8; color: #c0392b; }     /* Red */
    .badge-ok  { background: #d4edbc; color: #0d3b6e; }     /* Greenish */

    .action-btns { display: flex; gap: 0.5rem; }
    .btn-action {
        padding: 0.45rem 0.95rem;
        border: none;
        border-radius: 7px;
        font-size: 0.81rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.1s;
    }
    .btn-stock-in  { background: var(--blue-main); color: #fff; }
    .btn-stock-out { background: #c0392b; color: #fff; }
    .btn-action:hover { transform: translateY(-1px); filter: brightness(1.1); }

    /* Inline error message inside modal */
    .stock-error {
        color: #c0392b;
        font-size: 0.75rem;
        margin-top: 0.2rem;
        display: none;
    }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: stretch; }
        .btn-history { width: 100%; justify-content: center; }
        .table-toolbar { flex-direction: column; align-items: stretch; }
        .toolbar-filters { flex-direction: column; align-items: stretch; }
        .search-input, .filter-select { width: 100%; }
        .txn-table thead { display: none; }
        .txn-table tbody tr {
            display: block;
            background: #fff;
            margin: 0.9rem 0.8rem;
            padding: 1.2rem;
            border-radius: 14px;
            border: 1px solid var(--blue-border);
        }
        .txn-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed #d9eaff;
            align-items: center;
        }
        .txn-table td:last-child { border-bottom: none; padding-top: 1.2rem; justify-content: center; }
        .txn-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--blue-mid);
            width: 135px;
            flex-shrink: 0;
        }
        .action-btns { width: 100%; justify-content: center; gap: 0.8rem; flex-wrap: wrap; }
        .btn-action { flex: 1; max-width: 160px; padding: 0.65rem 1rem; font-size: 0.86rem; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Inventory List</div>
    <button onclick="openHistoryModal()" class="btn-history">📜 Transaction History</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Supplies</span>
        <div class="toolbar-filters">
            <input type="text" class="search-input" id="searchInput" placeholder="Search supplies..." oninput="filterTable()">
            <select class="filter-select" id="categoryFilter" onchange="filterTable()">
                <option value="">All Categories</option>
                @php
                    $categories = $inventory->pluck('category_name')->unique()->sort();
                @endphp
                @foreach($categories as $cat)
                    <option value="{{ strtolower($cat) }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="txn-table" id="txnTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID</th>
                    <th>Supply</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $item)
                @php
                    $supId = 'SUP' . str_pad($item->supply_id, 4, '0', STR_PAD_LEFT);
                    $badgeClass = match($item->stock_alert) {
                        'Out of Stock' => 'badge-out',
                        'Low Stock'    => 'badge-low',
                        default        => 'badge-ok'
                    };
                    $hasImage = !empty($item->supply_img_path);
                @endphp
                <tr data-name="{{ strtolower($item->supply_name) }}"
                    data-category="{{ strtolower($item->category_name) }}">
                    <td data-label="Image">
                        @if($hasImage)
                            <img src="{{ asset('storage/' . $item->supply_img_path) }}" class="supply-thumb" alt="{{ $item->supply_name }}">
                        @else
                            <div class="supply-thumb-placeholder">📦</div>
                        @endif
                    </td>
                    <td data-label="ID"><span class="supply-id-tag">{{ $supId }}</span></td>
                    <td data-label="Supply"><strong>{{ $item->supply_name }}</strong></td>
                    <td data-label="Category">{{ $item->category_name }}</td>
                    <td data-label="Stock">{{ $item->current_stock }} {{ $item->unit_measure }}</td>
                    <td data-label="Reorder">{{ $item->reorder_level }}</td>
                    <td data-label="Status"><span class="badge-status {{ $badgeClass }}">{{ $item->stock_alert }}</span></td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            <button class="btn-action btn-stock-in" data-supply-id="{{ $item->supply_id }}" data-type="in">＋ Stock In</button>
                            <button class="btn-action btn-stock-out" data-supply-id="{{ $item->supply_id }}" data-type="out">− Stock Out</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:4rem 2rem;color:#888;">No supplies found. 📭</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('admin_transactions.modals.stock_in')
@include('admin_transactions.modals.stock_out')
@include('admin_transactions.modals.history')

<div class="fb-toast" id="fbToast"></div>

<script>
    const suppliesData = {!! json_encode(collect($inventory)->map(fn($i) => [
        'id' => $i->supply_id,
        'name' => $i->supply_name,
        'unit' => $i->unit_measure,
        'img_url' => !empty($i->supply_img_path) ? asset('storage/' . $i->supply_img_path) : null,
        'current_stock' => $i->current_stock
    ])) !!};

    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }

    document.querySelectorAll('.fb-modal-backdrop').forEach(b => {
        b.addEventListener('click', e => { if (e.target === b) closeModal(b.id); });
    });

    // Show inline error inside modal
    function showModalError(modalId, fieldId, message) {
        const modal = document.getElementById(modalId);
        let errorSpan = modal.querySelector('.stock-error');
        if (!errorSpan) {
            const inputField = modal.querySelector(`#${fieldId}`);
            if (inputField && inputField.parentNode) {
                errorSpan = document.createElement('div');
                errorSpan.className = 'stock-error';
                inputField.parentNode.appendChild(errorSpan);
            }
        }
        if (errorSpan) {
            errorSpan.textContent = message;
            errorSpan.style.display = 'block';
            setTimeout(() => { errorSpan.style.display = 'none'; }, 3000);
        }
    }

    function validateAndSubmit(modalId, formId, isStockOut = false) {
        const modal = document.getElementById(modalId);
        const qtyField = modal.querySelector(`#${isStockOut ? 'soQuantity' : 'siQuantity'}`);
        if (!qtyField) return false;
        let quantity = parseInt(qtyField.value);
        if (isNaN(quantity) || quantity <= 0) {
            showModalError(modalId, qtyField.id, 'Please enter a positive quantity.');
            return false;
        }
        if (isStockOut) {
            const supplyId = modal.querySelector('#soHiddenId').value;
            const supply = suppliesData.find(s => s.id == supplyId);
            if (supply && quantity > supply.current_stock) {
                showModalError(modalId, qtyField.id, `Only ${supply.current_stock} units available.`);
                return false;
            }
        }
        document.getElementById(formId).submit();
        return true;
    }

    // Attach event listeners to dynamically created buttons
    document.querySelectorAll('.btn-stock-in, .btn-stock-out').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.dataset.type;
            const supplyId = this.dataset.supplyId;
            const supply = suppliesData.find(s => s.id == supplyId);
            if (!supply) return;
            const prefix = type === 'in' ? 'si' : 'so';
            const modalId = type === 'in' ? 'stockInModal' : 'stockOutModal';
            const imgWrap = document.getElementById(prefix + 'ImgWrap');
            imgWrap.innerHTML = supply.img_url
                ? `<img src="${supply.img_url}" style="max-width:180px;max-height:160px;border-radius:10px;border:2px solid var(--blue-border);object-fit:cover;">`
                : `<div style="width:120px;height:120px;background:#e3f2fd;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2.5rem;">📦</div>`;
            document.getElementById(prefix + 'SupplyName').textContent = supply.name;
            document.getElementById(prefix + 'SupplyId').value = 'SUP' + String(supply.id).padStart(4, '0');
            document.getElementById(prefix + 'HiddenId').value = supply.id;
            const qtyField = document.getElementById(prefix + 'Quantity');
            qtyField.value = '';
            // remove previous error spans
            const modal = document.getElementById(modalId);
            const oldErr = modal.querySelector('.stock-error');
            if (oldErr) oldErr.remove();
            openModal(modalId);
        });
    });

    // Override modal submit buttons (must be defined after click handlers)
    window.validateStockOut = function() {
        return validateAndSubmit('stockOutModal', 'stockOutForm', true);
    };
    window.validateStockIn = function() {
        return validateAndSubmit('stockInModal', 'stockInForm', false);
    };

    function openHistoryModal() {
        openModal('historyModal');
        loadHistory();
    }

    function loadHistory() {
        const list = document.getElementById('historyList');
        list.innerHTML = '<div class="history-empty">Loading...</div>';
        fetch('{{ route("admin.transactions.historyData") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                list.innerHTML = '<div class="history-empty">No transactions yet.</div>';
                return;
            }
            list.innerHTML = data.map(txn => {
                const isIn = txn.transaction_type === 'stock_in';
                const typeClass = isIn ? 'history-in' : 'history-out';
                const typeText = isIn ? 'STOCK IN' : 'STOCK OUT';
                const sign = isIn ? '+' : '−';
                return `
                    <div class="history-item">
                        <div class="history-info">
                            <div class="history-supply">${escapeHtml(txn.supply_name)}</div>
                            <div class="history-detail">${txn.transaction_date}</div>
                            <div class="history-detail">By: ${txn.performed_by}</div>
                            ${isIn && txn.supplier_name ? `<div class="history-detail">Supplier: ${escapeHtml(txn.supplier_name)}</div>` : ''}
                            ${!isIn && txn.purpose ? `<div class="history-detail">Purpose: ${escapeHtml(txn.purpose)}</div>` : ''}
                            ${isIn && txn.expiry_date ? `<div class="history-detail">Expiry: ${txn.expiry_date}</div>` : ''}
                            <div class="history-detail">${txn.remarks || 'No remarks'}</div>
                        </div>
                        <div class="history-right">
                            <span class="history-type ${typeClass}">${typeText}</span>
                            <span class="history-quantity">${sign}${txn.quantity}</span>
                        </div>
                    </div>`;
            }).join('');
        })
        .catch(() => list.innerHTML = '<div class="history-empty">Failed to load history.</div>');
    }

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const cat = document.getElementById('categoryFilter').value.toLowerCase();
        document.querySelectorAll('#txnTable tbody tr').forEach(row => {
            const name = row.dataset.name || '';
            const category = row.dataset.category || '';
            let show = true;
            if (q && !name.includes(q) && !category.includes(q)) show = false;
            if (cat && category !== cat) show = false;
            row.style.display = show ? '' : 'none';
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function showToast(msg, type = 'success') {
        const t = document.getElementById('fbToast');
        t.textContent = (type === 'success' ? '✅ ' : '❌ ') + msg;
        t.className = 'fb-toast show' + (type === 'error' ? ' error' : '');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
    @if(session('error')) showToast('{{ session('error') }}', 'error'); @endif
    @if(session('success')) showToast('{{ session('success') }}'); @endif
</script>
@endsection
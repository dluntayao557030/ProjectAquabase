@extends('layouts.nav_admin')

@section('title', 'Supplies')

@section('hero-text')
    Register and manage your farm's supplies. 📦
@endsection

@section('hero-text-mobile')
    📦 Check your farm's supplies.
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--blue-mid);
    }

    .btn-add {
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.3rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: var(--blue-dark);
        transform: translateY(-1px);
    }

    .table-card {
        background: var(--blue-pale);
        border-radius: 14px;
        border: 1px solid var(--blue-border);
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(10, 40, 100, 0.08);
    }

    /* Search & filter toolbar */
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.3rem;
        border-bottom: 1px solid var(--blue-border);
        background: #ffffff;
        flex-wrap: wrap;
        gap: 0.8rem;
    }
    .tbl-title {
        font-weight: 800;
        color: var(--text-dark);
    }
    .toolbar-right {
        display: flex;
        gap: 0.7rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .search-input {
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 25px;
        padding: 0.45rem 1rem 0.45rem 2.2rem;
        font-size: 0.85rem;
        width: 220px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231e88e5' stroke-width='2.5'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.7rem center;
    }
    .filter-select {
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 20px;
        padding: 0.45rem 1rem;
        font-size: 0.85rem;
        color: var(--text-dark);
        cursor: pointer;
    }

    /* Scrollable table container */
    .table-wrapper {
        max-height: 550px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .inv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
    }

    .inv-table th {
        background: var(--blue-main);
        color: #fff;
        padding: 0.75rem 0.9rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.76rem;
        text-transform: uppercase;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .inv-table td {
        padding: 0.8rem 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #d9eaff;
    }

    .supply-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--blue-border);
    }

    .supply-id-tag {
        font-weight: 700;
        color: var(--blue-mid);
        font-size: 0.75rem;
    }

    .badge-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 700;
        display: inline-block;
        margin-right: 4px;
    }

    .badge-ok {
        background: #d4edbc;
        color: #0d3b6e;
    }
    .badge-low {
        background: #fde8e8;
        color: #c0392b;
    }
    .badge-out {
        background: #fff3cd;
        color: #856404;
    }
    .badge-active {
        background: #d4edbc;
        color: #0d3b6e;
    }
    .badge-inactive {
        background: #f0f0f0;
        color: #666;
    }

    .btn-action {
        background: #f0f7ff;
        border: 1px solid var(--blue-border);
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.1s;
        margin: 0 2px;
    }

    .btn-action:hover {
        background: var(--blue-light);
        transform: scale(1.05);
    }

    .row-highlight {
        background-color: rgba(30, 136, 229, 0.2) !important;
        transition: background-color 0.1s ease;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-right {
            width: 100%;
            flex-direction: column;
        }
        .search-input, .filter-select {
            width: 100%;
        }

        .inv-table thead {
            display: none;
        }
        .inv-table tr {
            display: block;
            margin: 1rem 0.8rem;
            padding: 1rem;
            border: 1px solid var(--blue-border);
            border-radius: 12px;
            background: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        .inv-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.7rem 0;
            border-bottom: 1px dashed #d9eaff;
        }
        .inv-table td:last-child {
            border-bottom: none;
        }
        .inv-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--blue-mid);
            width: 135px;
            flex-shrink: 0;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Supplies</div>
    <button class="btn-add" onclick="openModal('addSupplyModal')">+ Add Supply</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Supplies</span>
        <div class="toolbar-right">
            <input type="text" class="search-input" id="searchInput" placeholder="Search supplies..." oninput="filterTable()">
            <select class="filter-select" id="filterCategory" onchange="filterTable()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ strtolower($cat->category_name) }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="filterStockStatus" onchange="filterTable()">
                <option value="">All Stock Status</option>
                <option value="in stock">In Stock</option>
                <option value="low stock">Low Stock</option>
                <option value="out of stock">Out of Stock</option>
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="inv-table" id="invTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>ID</th>
                    <th>Supply</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder</th>
                    <th>Stock Status</th>
                    <th>Supply Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplies as $supply)
                @php
                    $catName = $supply->category_name ?? 'N/A';
                    $supId = strtoupper(substr($catName, 0, 3)) . str_pad($supply->supply_id, 4, '0', STR_PAD_LEFT);
                    $isLow = $supply->current_stock <= $supply->reorder_level;
                    $isOut = $supply->current_stock == 0;
                    $stockBadgeClass = $isOut ? 'badge-out' : ($isLow ? 'badge-low' : 'badge-ok');
                    $stockBadgeLabel = $isOut ? 'Out of Stock' : ($isLow ? 'Low Stock' : 'In Stock');
                    $isActive = ($supply->status ?? 'active') === 'active';
                    $supplyStatusBadge = $isActive ? 'badge-active' : 'badge-inactive';
                    $supplyStatusLabel = $isActive ? 'Active' : 'Inactive';
                @endphp
                <tr data-supply-id="{{ $supply->supply_id }}"
                    data-name="{{ strtolower($supply->supply_name) }}"
                    data-category="{{ strtolower($catName) }}"
                    data-stock-status="{{ strtolower($stockBadgeLabel) }}"
                    ondblclick="openViewSupplyModal({{ $supply->supply_id }})"
                    style="cursor: pointer;">
                    <td data-label="Image">
                        @if($supply->supply_img_path)
                            <img src="{{ asset('storage/' . $supply->supply_img_path) }}" class="supply-thumb" alt="{{ $supply->supply_name }}">
                        @else
                            <div class="supply-thumb" style="background:#e8f4fd;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📦</div>
                        @endif
                    </td>
                    <td data-label="ID"><span class="supply-id-tag">{{ $supId }}</span></td>
                    <td data-label="Supply"><strong>{{ $supply->supply_name }}</strong></td>
                    <td data-label="Category">{{ $catName }}</td>
                    <td data-label="Stock">{{ $supply->current_stock }} {{ $supply->unit_measure }}</td>
                    <td data-label="Reorder">{{ $supply->reorder_level }}</td>
                    <td data-label="Stock Status"><span class="badge-status {{ $stockBadgeClass }}">{{ $stockBadgeLabel }}</span></td>
                    <td data-label="Supply Status"><span class="badge-status {{ $supplyStatusBadge }}">{{ $supplyStatusLabel }}</span></td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            {{-- VIEW BUTTON REMOVED — only edit and status toggle remain --}}
                            <button class="btn-action" onclick="openEditSupplyModal({{ $supply->supply_id }})" title="Edit">✎</button>
                            <button class="btn-action" style="background:{{ $isActive ? '#c0392b' : '#28a745' }};color:white;" 
                                    onclick="toggleSupplyStatus({{ $supply->supply_id }})">
                                {{ $isActive ? '🗑️' : '↺' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;padding:4rem 2rem;color:#888;">No supplies registered yet. Add your first supply.📝</td></td>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modals -->
@include('admin_inventory.modals.add_supply')
@include('admin_inventory.modals.edit_supply')
@include('admin_inventory.modals.view_supply')
@include('admin_inventory.modals.delete_supply')

@endsection

@push('scripts')
<script>
    const suppliesData = {!! json_encode($suppliesData ?? []) !!};

    function openModal(id) { 
        document.getElementById(id).classList.add('show'); 
    }

    function closeModal(id) { 
        document.getElementById(id).classList.remove('show'); 
    }

    // Close modal when clicking backdrop
    document.querySelectorAll('.fb-modal-backdrop').forEach(b => {
        b.addEventListener('click', e => {
            if (e.target === b) closeModal(b.id);
        });
    });

    function openViewSupplyModal(id) {
        const s = suppliesData.find(x => x.id === id);
        if (!s) return;
        document.getElementById('viewSupTitle').textContent = s.supply_name;
        const imgWrap = document.getElementById('viewSupImgWrap');
        imgWrap.innerHTML = s.img_url 
            ? `<img src="${s.img_url}" class="view-img" alt="${s.supply_name}">` 
            : `<div class="view-img-placeholder">📦</div>`;
        document.getElementById('viewSupGrid').innerHTML = `
            <div class="view-detail-item">
                <span class="detail-label">ID</span>
                <span class="detail-value">${s.display_id}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Supply Name</span>
                <span class="detail-value">${s.supply_name}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Category</span>
                <span class="detail-value">${s.category_name}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Current Stock</span>
                <span class="detail-value">${s.stock} ${s.unit_measure || ''}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Reorder Level</span>
                <span class="detail-value">${s.reorder_level}</span>
            </div>
            <div class="view-detail-item">
                <span class="detail-label">Status</span>
                <span class="detail-value">${s.supply_status === 'active' ? 'Active' : 'Inactive'}</span>
            </div>
        `;
        openModal('viewSupplyModal');
    }

    function openEditSupplyModal(id) {
        const s = suppliesData.find(x => x.id === id);
        if (!s) return;
        document.getElementById('editSupplyId').value = s.id;
        document.getElementById('editSupplyName').value = s.supply_name;
        document.getElementById('editSupplyCategory').value = s.category_id;
        document.getElementById('editSupplyReorder').value = s.reorder_level;
        document.getElementById('editSupplyUnit').value = s.unit_measure || 'piece';
        document.getElementById('editSupplyForm').action = s.edit_url;
        openModal('editSupplyModal');
    }

    // Toggle active/inactive status using the toggle endpoint
    function toggleSupplyStatus(id) {
        const supply = suppliesData.find(s => s.id === id);
        if (!supply) return;
        const isActive = supply.supply_status === 'active';
        const title = isActive ? 'Deactivate Supply' : 'Reactivate Supply';
        const message = isActive 
            ? `Deactivate <strong>${supply.supply_name}</strong>? It will no longer appear in active inventory.`
            : `Reactivate <strong>${supply.supply_name}</strong>?`;
        document.getElementById('deleteSupplyTitle').textContent = title;
        document.getElementById('deleteSupplyMessage').innerHTML = message;
        document.getElementById('deleteSubmitBtn').textContent = isActive ? 'Yes, Deactivate' : 'Yes, Reactivate';
        document.getElementById('deleteSupplyForm').action = supply.toggle_url;
        openModal('deleteSupplyModal');
    }

    // Search + Filter
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const categoryFilter = document.getElementById('filterCategory').value.toLowerCase();
        const stockFilter = document.getElementById('filterStockStatus').value.toLowerCase();

        document.querySelectorAll('#invTable tbody tr:not(.empty-row)').forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const cat = (row.dataset.category || '').toLowerCase();
            const stockStatus = (row.dataset.stockStatus || '').toLowerCase();

            let show = true;
            if (searchTerm && !name.includes(searchTerm) && !cat.includes(searchTerm)) show = false;
            if (categoryFilter && cat !== categoryFilter) show = false;
            if (stockFilter && stockStatus !== stockFilter) show = false;
            row.style.display = show ? '' : 'none';
        });
    }

        // Add single‑click highlight effect to each supply row
    document.querySelectorAll('#invTable tbody tr:not(.empty-row)').forEach(row => {
        row.addEventListener('click', function(e) {
            // Do not highlight if the click was on a button or inside an action button
            if (e.target.closest('.btn-action')) return;
            this.classList.add('row-highlight');
            setTimeout(() => {
                this.classList.remove('row-highlight');
            }, 300);
        });
    });

    // Prevent double-click on action buttons from triggering row double-click
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('dblclick', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endpush
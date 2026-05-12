@extends('layouts.nav_admin')

@section('title', 'Suppliers')

@section('hero-text')
    Manage your preferred suppliers. 🚚
@endsection

@section('hero-text-mobile')
    🚚 Manage your suppliers.
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

    .search-input {
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 25px;
        padding: 0.45rem 1rem 0.45rem 2rem;
        font-size: 0.85rem;
        width: 260px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231e88e5' stroke-width='2.5'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.7rem center;
    }

    .table-wrapper {
        max-height: 550px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .sup-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
    }

    .sup-table th {
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

    .sup-table td {
        padding: 0.8rem 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #d9eaff;
    }

    .sup-id-tag {
        font-weight: 700;
        color: var(--blue-mid);
        font-size: 0.75rem;
    }

    .badge-status {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 700;
    }

    .badge-active {
        background: #d4edbc;
        color: #0d3b6e;
    }

    .badge-inactive {
        background: #f0f0f0;
        color: #666;
    }

    .action-btns {
        display: flex;
        gap: 0.4rem;
    }

    .btn-action {
        background: #f0f7ff;
        border: 1px solid var(--blue-border);
        border-radius: 6px;
        padding: 0.35rem 0.65rem;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.1s;
        margin: 0 2px;
    }

    .btn-action:hover {
        background: var(--blue-light);
        transform: scale(1.05);
    }

    /* Row highlight effect for single click */
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
        .search-input {
            width: 100%;
        }
        .sup-table thead {
            display: none;
        }
        .sup-table tr {
            display: block;
            margin: 1rem 0.8rem;
            padding: 1rem;
            border: 1px solid var(--blue-border);
            border-radius: 12px;
            background: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        .sup-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.7rem 0;
            border-bottom: 1px dashed #d9eaff;
        }
        .sup-table td:last-child {
            border-bottom: none;
        }
        .sup-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: var(--blue-mid);
            width: 140px;
            flex-shrink: 0;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-title">Suppliers</div>
    <button class="btn-add" onclick="openModal('addSupplierModal')">+ Add Supplier</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Suppliers</span>
        <input type="text" class="search-input" id="searchInput" placeholder="Search suppliers..." oninput="filterTable()">
    </div>

    <div class="table-wrapper">
        <table class="sup-table" id="supTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliersData as $supplier)
                @php $isActive = $supplier['supplier_status'] === 'active'; @endphp
                <tr data-supplier-id="{{ $supplier['id'] }}"
                    data-name="{{ strtolower($supplier['supplier_name']) }}"
                    ondblclick="openViewSupplierModal({{ $supplier['id'] }})"
                    style="cursor: pointer;">
                    <td data-label="ID"><span class="sup-id-tag">{{ $supplier['display_id'] }}</span></td>
                    <td data-label="Supplier Name"><strong>{{ $supplier['supplier_name'] }}</strong></td>
                    <td data-label="Contact Number">{{ $supplier['contact_number'] ?? '—' }}</td>
                    <td data-label="Email">{{ $supplier['email'] ?? '—' }}</td>
                    <td data-label="Status">
                        <span class="badge-status {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($supplier['supplier_status']) }}
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            {{-- VIEW BUTTON REMOVED --}}
                            <button class="btn-action" onclick="openEditSupplierModal({{ $supplier['id'] }})" title="Edit">✎</button>
                            <button class="btn-action" style="background:{{ $isActive ? '#c0392b' : '#28a745' }};color:white;" 
                                    onclick="toggleSupplierStatus({{ $supplier['id'] }})">
                                {{ $isActive ? '🗑️' : '↺' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:4rem 2rem;color:#888;">No suppliers yet. Add your first one. 📝</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@include('admin_suppliers.modals.add_supplier')
@include('admin_suppliers.modals.edit_supplier')
@include('admin_suppliers.modals.view_supplier')
@include('admin_suppliers.modals.delete_supplier')

@endsection

@push('scripts')
<script>
    const suppliersData = {!! json_encode($suppliersData ?? []) !!};

    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    document.querySelectorAll('.fb-modal-backdrop').forEach(b => {
        b.addEventListener('click', e => {
            if (e.target === b) closeModal(b.id);
        });
    });

    function openViewSupplierModal(id) {
        const s = suppliersData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('viewSupNameTitle').textContent = s.supplier_name;
        document.getElementById('viewSupId').textContent = s.display_id;
        document.getElementById('viewSupName').textContent = s.supplier_name;
        document.getElementById('viewSupContact').textContent = s.contact_number || '—';
        document.getElementById('viewSupEmail').textContent = s.email || '—';
        document.getElementById('viewSupAddress').textContent = s.address || '—';
        document.getElementById('viewSupDescription').textContent = s.description || '—';

        const statusEl = document.getElementById('viewSupStatus');
        statusEl.textContent = s.supplier_status === 'active' ? 'Active' : 'Inactive';
        statusEl.className = 'badge-status ' + (s.supplier_status === 'active' ? 'badge-active' : 'badge-inactive');

        openModal('viewSupplierModal');
    }

    function openEditSupplierModal(id) {
        const s = suppliersData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('editSupName').value = s.supplier_name || '';
        document.getElementById('editSupContact').value = s.contact_number || '';
        document.getElementById('editSupEmail').value = s.email || '';
        document.getElementById('editSupAddress').value = s.address || '';
        document.getElementById('editSupDescription').value = s.description || '';

        document.getElementById('editSupplierForm').action = s.edit_url;

        openModal('editSupplierModal');
    }

    function toggleSupplierStatus(id) {
        const s = suppliersData.find(x => x.id === id);
        if (!s) return;

        const isActive = s.supplier_status === 'active';
        const title = isActive ? 'Deactivate Supplier' : 'Reactivate Supplier';
        const message = isActive
            ? `Deactivate <strong>${s.supplier_name}</strong>?`
            : `Reactivate <strong>${s.supplier_name}</strong>?`;

        document.getElementById('deleteSupTitle').textContent = title;
        document.getElementById('deleteSupMessage').innerHTML = message;
        document.getElementById('deleteSupplierSubmitBtn').textContent = isActive ? 'Yes, Deactivate' : 'Yes, Reactivate';

        document.getElementById('deleteSupplierForm').action = s.delete_url;

        openModal('deleteSupplierModal');
    }

    // Search filter
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#supTable tbody tr').forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            row.style.display = !searchTerm || name.includes(searchTerm) ? '' : 'none';
        });
    }

    // Add single‑click highlight effect to each supplier row
    document.querySelectorAll('#supTable tbody tr:not(.empty-row)').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.btn-action')) return;
            this.classList.add('row-highlight');
            setTimeout(() => this.classList.remove('row-highlight'), 300);
        });
    });

    // Prevent double-click on action buttons from triggering row double-click
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('dblclick', e => e.stopPropagation());
    });
</script>
@endpush
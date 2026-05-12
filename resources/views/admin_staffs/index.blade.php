@extends('layouts.nav_admin')

@section('title', 'Staff Management')

@section('hero-text')
    Manage staff who handle daily stock transactions. 🧑‍🌾
@endsection

@section('hero-text-mobile')
    🧑‍🌾 Manage your staff.
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

    /* Search toolbar */
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
        width: 240px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%231e88e5' stroke-width='2.5'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: 0.7rem center;
    }

    /* Scrollable table container */
    .table-wrapper {
        max-height: 550px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .stf-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.84rem;
    }

    .stf-table th {
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

    .stf-table td {
        padding: 0.8rem 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #d9eaff;
    }

    .stf-id-tag {
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

    .badge-active { background: #d4edbc; color: #0d3b6e; }
    .badge-inactive { background: #f0f0f0; color: #666; }

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
        .stf-table thead {
            display: none;
        }
        .stf-table tr {
            display: block;
            margin: 1rem 0.8rem;
            padding: 1rem;
            border: 1px solid var(--blue-border);
            border-radius: 12px;
            background: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        .stf-table td {
            display: flex;
            justify-content: space-between;
            padding: 0.7rem 0;
            border-bottom: 1px dashed #d9eaff;
        }
        .stf-table td:last-child {
            border-bottom: none;
        }
        .stf-table td::before {
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
    <div class="page-title">Staffs</div>
    <button class="btn-add" onclick="openModal('addStaffModal')">+ Add Staff</button>
</div>

<div class="table-card">
    <div class="table-toolbar">
        <span class="tbl-title">All Staff</span>
        <input type="text" class="search-input" id="searchInput" placeholder="Search by name or username..." oninput="filterTable()">
    </div>

    <div class="table-wrapper">
        <table class="stf-table" id="stfTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($staffsData as $staff)
                @php
                    $fullName = $staff['full_name'];
                    $isActive = $staff['staff_status'] === 'active';
                @endphp
                <tr data-staff-id="{{ $staff['id'] }}"
                    data-name="{{ strtolower($fullName) }}"
                    data-username="{{ strtolower($staff['username']) }}"
                    ondblclick="openViewStaffModal({{ $staff['id'] }})"
                    style="cursor: pointer;">
                    <td data-label="ID"><span class="stf-id-tag">{{ $staff['display_id'] }}</span></td>
                    <td data-label="Name"><strong>{{ $fullName }}</strong></td>
                    <td data-label="Username">{{ $staff['username'] }}</td>
                    <td data-label="Email">{{ $staff['email'] }}</td>
                    <td data-label="Status">
                        <span class="badge-status {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($staff['staff_status']) }}
                        </span>
                    </td>
                    <td data-label="Actions">
                        <div class="action-btns">
                            {{-- VIEW BUTTON REMOVED --}}
                            <button class="btn-action" onclick="openEditStaffModal({{ $staff['id'] }})" title="Edit">✎</button>
                            <button class="btn-action" style="background:{{ $isActive ? '#c0392b' : '#28a745' }};color:white;" 
                                    onclick="toggleStaffStatus({{ $staff['id'] }})">
                                {{ $isActive ? '🗑️' : '↺' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:4rem 2rem;color:#888;">
                        No staff members yet. Add your first barn staff.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modals -->
@include('admin_staffs.modals.add_staff')
@include('admin_staffs.modals.edit_staff')
@include('admin_staffs.modals.view_staff')
@include('admin_staffs.modals.delete_staff')

@endsection

@push('scripts')
<script>
    const staffsData = {!! json_encode($staffsData ?? []) !!};

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

    function openViewStaffModal(id) {
        const s = staffsData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('viewStfTitle').textContent = s.full_name;
        document.getElementById('viewStfId').textContent = s.display_id;
        document.getElementById('viewStfName').textContent = s.full_name;
        document.getElementById('viewStfUsername').textContent = s.username;
        document.getElementById('viewStfEmail').textContent = s.email;

        const statusEl = document.getElementById('viewStfStatus');
        statusEl.textContent = s.staff_status === 'active' ? 'Active' : 'Inactive';
        statusEl.className = 'badge-status ' + (s.staff_status === 'active' ? 'badge-active' : 'badge-inactive');

        openModal('viewStaffModal');
    }

    function openEditStaffModal(id) {
        const s = staffsData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('editStfId').value = s.display_id;
        document.getElementById('editStfFirstName').value = s.first_name;
        document.getElementById('editStfLastName').value = s.last_name;
        document.getElementById('editStfUsername').value = s.username;
        document.getElementById('editStfStatus').value = s.staff_status;
        document.getElementById('editStfUserId').value = s.user_id;
        document.getElementById('editStaffForm').action = s.update_url;

        openModal('editStaffModal');
    }

    function toggleStaffStatus(id) {
        const staff = staffsData.find(s => s.id === id);
        if (!staff) return;

        const isActive = staff.staff_status === 'active';
        const title = isActive ? 'Deactivate Staff' : 'Reactivate Staff';
        const message = isActive 
            ? `Deactivate <strong>${staff.full_name}</strong>? They will no longer be able to log in.`
            : `Reactivate <strong>${staff.full_name}</strong>?`;

        document.getElementById('deleteStfTitle').textContent = title;
        document.getElementById('deleteStfMessage').innerHTML = message;
        document.getElementById('deleteStaffSubmitBtn').textContent = isActive ? 'Yes, Deactivate' : 'Yes, Reactivate';

        document.getElementById('deleteStaffForm').action = staff.delete_url;
        openModal('deleteStaffModal');
    }

    // Search filter
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#stfTable tbody tr').forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const username = (row.dataset.username || '').toLowerCase();
            const show = !searchTerm || name.includes(searchTerm) || username.includes(searchTerm);
            row.style.display = show ? '' : 'none';
        });
    }

    // Add single‑click highlight effect to each staff row
    document.querySelectorAll('#stfTable tbody tr:not(.empty-row)').forEach(row => {
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
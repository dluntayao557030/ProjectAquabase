@push('styles')
<style>
    .fb-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 40, 100, 0.55);
        z-index: 9000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .fb-modal-backdrop.show { display: flex; }

    .fb-modal {
        background: var(--blue-pale);
        border-radius: 16px;
        width: 100%;
        max-width: 460px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(10, 40, 100, 0.35);
        border: 1px solid var(--blue-border);
        animation: modalPop 0.28s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }

    @keyframes modalPop {
        from { transform: scale(0.88) translateY(20px); opacity: 0; }
        to   { transform: scale(1)    translateY(0);    opacity: 1; }
    }

    .modal-hero {
        height: 90px;
        background: url('/images/backgrounds/FishFarmImage2.jpg') center 40%/cover no-repeat;
        position: relative;
        flex-shrink: 0;
    }

    .modal-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(10, 40, 100, 0.3), rgba(10, 40, 100, 0.7));
    }

    .modal-hero-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .modal-hero-logo img {
        height: 36px;
        mix-blend-mode: screen;
        filter: brightness(1.0);
    }

    .modal-body-wrap {
        padding: 1.5rem 1.8rem 0.8rem;
        overflow-y: auto;
        flex: 1;
    }

    .modal-title {
        font-family: var(--font);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 1.2rem;
    }

    .view-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.9rem;
    }

    .view-detail-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .detail-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--blue-mid);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .detail-value {
        font-size: 0.9rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    .staff-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--blue-pale);
        border: 3px solid var(--blue-border);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        overflow: hidden;
    }

    .staff-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-footer-btns {
        display: flex;
        gap: 0.7rem;
        padding: 0.9rem 1.8rem 1.3rem;
        border-top: 1px solid var(--blue-border);
        flex-shrink: 0;
    }

    .btn-modal-back {
        flex: 1;
        background: transparent;
        color: var(--blue-dark);
        border: 1.5px solid var(--blue-border);
        border-radius: 8px;
        padding: 0.55rem;
        font-family: var(--font);
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-modal-back:hover { background: var(--blue-pale); }
</style>
@endpush

<div class="fb-modal-backdrop" id="viewStaffModal">
    <div class="fb-modal" style="max-width: 420px;">

        <div class="modal-hero">
            <div class="modal-hero-logo">
                <img src="/images/logos/AquabaseLogoOnly.png" alt="Aquabase">
            </div>
        </div>

        <div class="modal-body-wrap">
            <div class="staff-avatar">
                <img src="/images/icons/StaffBlue.png" alt="Staff">
            </div>
            <div class="modal-title" id="viewStfTitle">Staff Details</div>

            <div class="view-detail-grid">
                <div class="view-detail-item">
                    <span class="detail-label">Staff ID</span>
                    <span class="detail-value" id="viewStfId" style="color: var(--blue-mid);"></span>
                </div>
                <div class="view-detail-item">
                    <span class="detail-label">Full Name</span>
                    <span class="detail-value" id="viewStfName"></span>
                </div>
                <div class="view-detail-item">
                    <span class="detail-label">Username</span>
                    <span class="detail-value" id="viewStfUsername"></span>
                </div>
                <div class="view-detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" id="viewStfEmail" style="font-size:0.82rem;"></span>
                </div>
                <div class="view-detail-item" style="grid-column: 1 / -1;">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" id="viewStfStatus"></span>
                </div>
            </div>
        </div>

        <div class="modal-footer-btns">
            <button class="btn-modal-back" style="flex: 1;" onclick="closeModal('viewStaffModal')">Close</button>
        </div>

    </div>
</div>
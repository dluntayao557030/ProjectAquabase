@push('styles')
<style>
    .fb-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10,40,100,0.55);
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
        max-width: 420px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(10,40,100,0.35);
        border: 1px solid var(--blue-border);
        animation: modalPop 0.28s cubic-bezier(0.34,1.56,0.64,1) both;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }
    @keyframes modalPop {
        from { transform: scale(0.88) translateY(20px); opacity: 0; }
        to   { transform: scale(1)    translateY(0);    opacity: 1; }
    }
    .modal-body-wrap { padding: 1.5rem 1.8rem 0.8rem; overflow-y: auto; flex: 1; }
    .modal-title {
        font-family: var(--font);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 1.2rem;
    }
    .delete-warning {
        text-align: center;
        padding: 1rem 0 1.2rem;
    }
    .delete-warning .warn-icon {
        font-size: 3rem;
        margin-bottom: 0.8rem;
        display: block;
    }
    .delete-warning p {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-mid);
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
    }
    .btn-modal-back:hover { background: var(--blue-pale); }
    .btn-modal-submit.danger {
        flex: 2;
        background: #c0392b;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.55rem;
        font-family: var(--font);
        font-weight: 700;
        cursor: pointer;
    }
    .btn-modal-submit.danger:hover { background: #922b21; transform: translateY(-1px); }
</style>
@endpush

<div class="fb-modal-backdrop" id="deleteSupplyModal">
    <div class="fb-modal" style="max-width: 420px;">
        <div class="modal-body-wrap">
            <div class="delete-warning">
                <span class="warn-icon">⚠️</span>
                <div class="modal-title" id="deleteSupplyTitle">Deactivate Supply</div>
                <p id="deleteSupplyMessage"></p>
            </div>
            <form method="POST" id="deleteSupplyForm">
                @csrf
                @method('DELETE')
            </form>
        </div>
        <div class="modal-footer-btns">
            <button class="btn-modal-back" onclick="closeModal('deleteSupplyModal')">Cancel</button>
            <button class="btn-modal-submit danger" id="deleteSubmitBtn" onclick="document.getElementById('deleteSupplyForm').submit()">
                Yes, Deactivate
            </button>
        </div>
    </div>
</div>
@push('styles')
<style>
    .history-modal { max-width: 560px; }
    .history-header {
        background: var(--blue-main);
        color: #fff;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .history-list {
        max-height: 420px;
        overflow-y: auto;
        padding: 0.6rem 0.8rem;
    }
    .history-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.8rem;
        padding: 0.9rem 1rem;
        margin-bottom: 0.5rem;
        background: #fff;
        border-radius: 8px;
        border: 1px solid var(--blue-border);
        transition: background 0.15s;
    }
    .history-item:hover { background: #f0f7ff; }
    .history-info { flex: 1; min-width: 0; }
    .history-supply {
        font-weight: 800;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 0.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .history-detail {
        font-size: 0.73rem;
        color: #666;
        line-height: 1.4;
    }
    .history-right {
        flex-shrink: 0;
        text-align: right;
    }
    .history-type {
        display: inline-block;
        font-weight: 700;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.7rem;
        margin-bottom: 0.3rem;
    }
    .history-in  { background: #e3f2fd; color: #0d3b6e; }
    .history-out { background: #ffebee; color: #c62828; }
    .history-quantity {
        font-size: 1.05rem;
        font-weight: 800;
        display: block;
    }
    .history-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #888;
    }
    @media (max-width: 576px) {
        .history-modal { max-width: 100%; }
        .history-item { flex-direction: column; gap: 0.6rem; }
        .history-right { text-align: left; display: flex; gap: 1rem; align-items: center; }
        .history-quantity { margin-top: 0; }
    }
</style>
@endpush

<div class="fb-modal-backdrop" id="historyModal">
    <div class="fb-modal history-modal">
        <div class="history-header">📋 My Transaction History</div>
        <div class="history-list" id="historyList">
            <div class="history-empty">Loading your transactions...</div>
        </div>
        <div class="modal-footer-btns" style="padding:0.9rem 1.5rem 1.2rem; border-top:1px solid var(--blue-border);">
            <button class="btn-modal-back" style="flex:1;" onclick="closeModal('historyModal')">Close</button>
        </div>
    </div>
</div>
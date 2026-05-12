@once
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
        max-width: 480px;
        overflow: hidden;
        border: 1px solid var(--blue-border);
        animation: modalPop 0.28s ease both;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
    }
    @keyframes modalPop {
        from { transform: scale(0.88) translateY(20px); opacity: 0; }
        to   { transform: scale(1)    translateY(0);    opacity: 1; }
    }
    .modal-body-wrap {
        padding: 1.5rem 1.8rem 0.8rem;
        overflow-y: auto;
        flex: 1;
    }
    .stock-modal-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 0.2rem;
    }
    .stock-supply-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--blue-mid);
        text-align: center;
        margin-bottom: 1.2rem;
    }
    .stock-img-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .modal-form .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-mid);
        margin-bottom: 0.25rem;
    }
    .modal-form .form-control, .modal-form .form-select {
        font-family: var(--font);
        font-size: 0.88rem;
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 7px;
        padding: 0.55rem 0.8rem;
        width: 100%;
    }
    .modal-form .form-control:focus, .modal-form .form-select:focus {
        border-color: var(--blue-main);
        box-shadow: 0 0 0 3px rgba(30,136,229,0.15);
        outline: none;
    }
    .modal-footer-btns {
        display: flex;
        gap: 0.7rem;
        padding: 1rem 1.5rem 1.3rem;
        border-top: 1px solid var(--blue-border);
        flex-shrink: 0;
    }
    .btn-modal-back {
        flex: 1;
        background: transparent;
        color: var(--blue-dark);
        border: 1.5px solid var(--blue-border);
        border-radius: 8px;
        padding: 0.6rem;
        font-family: var(--font);
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-modal-back:hover { background: var(--blue-pale); }
    .btn-modal-submit {
        flex: 2;
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem;
        font-family: var(--font);
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-modal-submit:hover { background: var(--blue-dark); transform: translateY(-1px); }
    .error-message {
        color: #c0392b;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
    }
</style>
@endpush
@endonce

<div class="fb-modal-backdrop" id="stockInModal">
    <div class="fb-modal">
        <div class="modal-body-wrap">
            <div class="stock-modal-title">Stock In</div>
            <div class="stock-supply-name" id="siSupplyName">—</div>
            <div class="stock-img-wrap" id="siImgWrap"></div>

            <form action="{{ route('staff.transactions.stockIn') }}" method="POST" class="modal-form" id="stockInForm">
                @csrf
                <input type="hidden" name="supply_id" id="siHiddenId">

                <div class="mb-3">
                    <label class="form-label">Supply ID</label>
                    <input type="text" class="form-control" id="siSupplyId" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Supplier (optional)</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">— Select Supplier —</option>
                        @foreach($suppliers ?? [] as $sup)
                            <option value="{{ $sup->supplier_id }}">{{ $sup->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity <span style="color:#c0392b">*</span></label>
                    <input type="number" name="quantity" id="siQuantity" class="form-control" step="1" min="1" required>
                    <div class="error-message" id="siQuantityError"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit Cost (₱) <span style="color:#c0392b">*</span></label>
                    <input type="number" name="unit_cost" id="siUnitCost" class="form-control" step="0.01" min="0.01" required>
                    <div class="error-message" id="siUnitCostError"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Receipt No. (optional)</label>
                    <input type="text" name="receipt_no" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Expiry Date (optional)</label>
                    <input type="date" name="expiry_date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks (optional)</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer-btns">
            <button class="btn-modal-back" onclick="closeModal('stockInModal')">Cancel</button>
            <button class="btn-modal-submit" id="siSubmitBtn">＋ Confirm Stock In</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const siSubmitBtn = document.getElementById('siSubmitBtn');

        function showFieldError(fieldId, message) {
            const el = document.getElementById(fieldId + 'Error');
            if (!el) return;
            el.textContent = message;
            el.style.display = 'block';
            setTimeout(() => { el.style.display = 'none'; }, 3000);
        }

        function validateStockIn() {
            let valid = true;
            const qty = parseInt(document.getElementById('siQuantity').value);
            if (isNaN(qty) || qty < 1) {
                showFieldError('siQuantity', 'Enter a whole number of at least 1.');
                valid = false;
            }
            const cost = parseFloat(document.getElementById('siUnitCost').value);
            if (isNaN(cost) || cost <= 0) {
                showFieldError('siUnitCost', 'Enter a unit cost greater than zero.');
                valid = false;
            }
            return valid;
        }

        if (siSubmitBtn) {
            siSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (validateStockIn()) {
                    document.getElementById('stockInForm').submit();
                }
            });
        }
    })();
</script>
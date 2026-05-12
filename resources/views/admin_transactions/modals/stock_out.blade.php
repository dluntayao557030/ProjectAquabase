<div class="fb-modal-backdrop" id="stockOutModal">
    <div class="fb-modal">
        <div class="modal-body-wrap">
            <div class="stock-modal-title">Stock Out</div>
            <div class="stock-supply-name" id="soSupplyName">—</div>
            <div class="stock-img-wrap" id="soImgWrap"></div>

            <form action="{{ route('admin.transactions.stockOut') }}" method="POST" class="modal-form" id="stockOutForm">
                @csrf
                <input type="hidden" name="supply_id" id="soHiddenId">

                <div class="mb-3">
                    <label class="form-label">Supply ID</label>
                    <input type="text" class="form-control" id="soSupplyId" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quantity <span style="color:#c0392b">*</span></label>
                    <input type="number" name="quantity" id="soQuantity" class="form-control" step="1" min="1" required>
                    <div class="error-message" id="soQuantityError"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Purpose (optional)</label>
                    <input type="text" name="purpose" class="form-control" placeholder="e.g. Feeding, Maintenance">
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks (optional)</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer-btns">
            <button class="btn-modal-back" onclick="closeModal('stockOutModal')">Cancel</button>
            <button class="btn-modal-submit" id="soSubmitBtn" style="background:#c0392b;">− Confirm Stock Out</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const soQuantity = document.getElementById('soQuantity');
        const soSubmitBtn = document.getElementById('soSubmitBtn');

        function showError(fieldId, message) {
            const errorDiv = document.getElementById(fieldId + 'Error');
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.style.display = 'block';
                setTimeout(() => errorDiv.style.display = 'none', 3000);
            }
        }

        function validateStockOut() {
            let valid = true;
            const quantity = parseInt(soQuantity.value);
            if (isNaN(quantity) || quantity <= 0) {
                showError('soQuantity', 'Please enter a positive whole number quantity.');
                valid = false;
            }
            return valid;
        }

        if (soSubmitBtn) {
            soSubmitBtn.onclick = function(e) {
                e.preventDefault();
                if (validateStockOut()) {
                    document.getElementById('stockOutForm').submit();
                }
            };
        }
    })();
</script>
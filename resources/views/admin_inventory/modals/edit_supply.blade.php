@push('styles')
<style>
    /* Reuse same base modal styles – ensure hidden by default */
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
        max-width: 480px;
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
    .modal-hero {
        height: 90px;
        background: url('/images/backgrounds/FishFarmImage3.jpg') center 40%/cover no-repeat;
        position: relative;
        flex-shrink: 0;
    }
    .modal-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(10,40,100,0.3), rgba(10,40,100,0.7));
    }
    .modal-hero-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        z-index: 2;
    }
    .modal-hero-logo img { height: 36px; mix-blend-mode: screen; filter: brightness(1.0); }
    .modal-body-wrap { padding: 1.5rem 1.8rem 0.8rem; overflow-y: auto; flex: 1; }
    .modal-title {
        font-family: var(--font);
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-dark);
        text-align: center;
        margin-bottom: 1.2rem;
    }
    .modal-form .form-label {
        font-family: var(--font);
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text-mid);
        margin-bottom: 0.25rem;
    }
    .modal-form .form-control,
    .modal-form .form-select {
        font-family: var(--font);
        font-size: 0.88rem;
        background: #fff;
        border: 1.5px solid var(--blue-border);
        border-radius: 7px;
        padding: 0.5rem 0.75rem;
        color: var(--text-dark);
    }
    .modal-form .form-control:focus,
    .modal-form .form-select:focus {
        border-color: var(--blue-main);
        box-shadow: 0 0 0 3px rgba(30,136,229,0.15);
        outline: none;
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
    .btn-modal-submit {
        flex: 2;
        background: var(--blue-main);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 0.55rem;
        font-family: var(--font);
        font-weight: 700;
        cursor: pointer;
    }
    .btn-modal-submit:hover { background: var(--blue-dark); transform: translateY(-1px); }
</style>
@endpush

<div class="fb-modal-backdrop" id="editSupplyModal">
    <div class="fb-modal">
        <div class="modal-hero">
            <div class="modal-hero-logo">
                <img src="/images/logos/AquabaseLogoOnly.png" alt="Aquabase">
            </div>
        </div>

        <div class="modal-body-wrap">
            <div class="modal-title">Edit Supply</div>

            <form method="POST" enctype="multipart/form-data" class="modal-form" id="editSupplyForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="supply_id" id="editSupplyId">

                <div class="mb-3">
                    <label class="form-label">Supply Name <span style="color:#c0392b">*</span></label>
                    <input type="text" name="supply_name" id="editSupplyName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" id="editSupplyCategory" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reorder Level</label>
                    <input type="number" name="reorder_level" id="editSupplyReorder" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Unit of Measure <span style="color:#c0392b">*</span></label>
                    <select name="unit_measure" id="editSupplyUnit" class="form-select" required>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="g">Gram (g)</option>
                        <option value="lb">Pound (lb)</option>
                        <option value="piece">Piece (pc)</option>
                        <option value="bundle">Bundle</option>
                        <option value="liter">Liter (L)</option>
                        <option value="mL">Milliliter (mL)</option>
                        <option value="pack">Pack</option>
                        <option value="box">Box</option>
                        <option value="sack">Sack</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Image (Optional)</label>
                    <input type="file" name="supply_image" accept="image/*" class="form-control">
                </div>
            </form>
        </div>

        <div class="modal-footer-btns">
            <button class="btn-modal-back" onclick="closeModal('editSupplyModal')">Cancel</button>
            <button class="btn-modal-submit" onclick="document.getElementById('editSupplyForm').submit()">Update Supply</button>
        </div>
    </div>
</div>
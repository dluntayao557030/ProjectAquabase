@once
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
        max-width: 480px;
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
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .modal-form .form-control:focus,
    .modal-form .form-select:focus {
        border-color: var(--blue-main);
        box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.15);
        outline: none;
    }

    .modal-form .form-control[readonly] {
        background: #f0f7e8;
        color: var(--text-mid);
        cursor: default;
    }

    .pw-wrap { position: relative; }
    .pw-wrap .form-control { padding-right: 2.6rem; }
    .pw-toggle {
        position: absolute;
        right: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        color: var(--text-mid);
        padding: 0;
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
        font-size: 0.9rem;
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
        padding: 0.55rem;
        font-family: var(--font);
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
    }

    .btn-modal-submit:hover { background: var(--blue-dark); transform: translateY(-1px); }

    .pw-hint {
        font-size: 0.72rem;
        color: #999;
        margin-top: 3px;
    }

    .btn-modal-submit.danger { background: #c0392b; }
    .btn-modal-submit.danger:hover { background: #922b21; }
</style>
@endpush
@endonce

<div class="fb-modal-backdrop" id="addStaffModal">
    <div class="fb-modal">

        <div class="modal-hero">
            <div class="modal-hero-logo">
                <img src="/images/logos/AquabaseLogoOnly.png" alt="Aquabase">
            </div>
        </div>

        <div class="modal-body-wrap">
            <div class="modal-title">New Staff</div>

            <form action="{{ route('admin.staffs.store') }}" method="POST" class="modal-form" id="addStaffForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">ID <span style="color:#aaa;font-weight:400;">(auto-generated)</span></label>
                    <input type="text" class="form-control" readonly placeholder="Auto-assigned after save">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label">First Name <span style="color:#c0392b">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name') }}" placeholder="e.g. Juan">
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">Last Name <span style="color:#c0392b">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name') }}" placeholder="e.g. Dela Cruz">
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email <span style="color:#c0392b">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" placeholder="staff@farm.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Username <span style="color:#c0392b">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                           value="{{ old('username') }}" placeholder="e.g. jdelacruz">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span style="color:#c0392b">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password" id="addStfPw" class="form-control @error('password') is-invalid @enderror">
                        <button type="button" class="pw-toggle" onclick="togglePw('addStfPw','addStfPwEye')">
                            <span id="addStfPwEye">👁</span>
                        </button>
                    </div>
                    <div class="pw-hint">Minimum 8 characters</div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-1">
                    <label class="form-label">Re-type Password <span style="color:#c0392b">*</span></label>
                    <div class="pw-wrap">
                        <input type="password" name="password_confirmation" id="addStfPwC" class="form-control">
                        <button type="button" class="pw-toggle" onclick="togglePw('addStfPwC','addStfPwCEye')">
                            <span id="addStfPwCEye">👁</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer-btns">
            <button class="btn-modal-back" onclick="closeModal('addStaffModal')">Back</button>
            <button class="btn-modal-submit" onclick="document.getElementById('addStaffForm').submit()">
                Add Staff
            </button>
        </div>

    </div>
</div>

<script>
    function togglePw(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (input.type === 'password') {
            input.type = 'text';
            eye.textContent = '🙈';
        } else {
            input.type = 'password';
            eye.textContent = '👁';
        }
    }
</script>
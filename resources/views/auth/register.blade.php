@extends('layouts.app')

@section('title', ' - Register')

@php
    $hide_navbar = true;
    $bodyClass = 'registration-page';
@endphp

@section('styles')
<style>
    body {
        background: url('{{ asset("images/login-bg.png") }}') no-repeat center center fixed;
        background-size: 100% 100%;
        min-height: 100vh;
    }

    .register-shell {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        position: relative;
        padding: 120px 5% 36px;
    }

    .register-branding {
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 28px 0 0 5%;
        z-index: 10;
    }

    .register-branding img {
        height: 58px;
        width: auto;
        object-fit: contain;
    }

    .register-branding .brand-name {
        font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
        font-size: 30px;
        font-weight: 800;
        color: #F7D000;
        letter-spacing: 0.8px;
        text-shadow: 0 2px 14px rgba(247, 208, 0, 0.28);
        border-bottom: 3px solid rgba(247, 208, 0, 0.8);
        padding-bottom: 3px;
    }

    .register-right {
        width: 100%;
        max-width: 760px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 0;
        padding: 0;
        height: auto;
    }

    .register-panel {
        width: 100%;
        max-width: min(680px, 100%);
        max-height: none;
        overflow: visible;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 18px;
        box-shadow: 0 18px 60px rgba(0, 0, 0, 0.22);
        padding: 2rem;
    }

    .register-panel h1 {
        color: #10069f;
        font-size: clamp(1.65rem, 2.8vw, 2.25rem);
        font-weight: 700;
        text-align: center;
    }

    .register-subtitle {
        color: #64748B;
        margin-bottom: 1.5rem;
    }

    .section-label {
        color: #10069f;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
        margin: 1.5rem 0 .8rem;
    }

    h2.section-label {
        line-height: 1.2;
    }

    .proof-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: 1.5rem 0 .8rem;
        padding: .7rem .9rem;
        border-left: 4px solid #f7d000;
        border-radius: 10px;
        background: linear-gradient(135deg, #fff9d9 0%, #fffdf4 100%);
        box-shadow: inset 0 0 0 1px rgba(16, 6, 159, 0.08);
    }

    .proof-section-header .section-label {
        margin: 0;
    }

    .proof-required-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .38rem .72rem;
        border-radius: 999px;
        background: #10069f;
        color: #ffffff;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .proof-warning {
        margin: 0 0 1rem;
        padding: .75rem .9rem;
        border-radius: 10px;
        border: 1px solid rgba(247, 208, 0, 0.5);
        background: rgba(247, 208, 0, 0.08);
        color: #5f4b00;
        font-size: .84rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .form-label {
        color: #334155;
        font-weight: 600;
        font-size: .9rem;
    }

    .form-control, .form-select {
        border-color: #d9d8ee;
        min-height: 44px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #10069f;
        box-shadow: 0 0 0 .2rem rgba(16, 6, 159, .12);
    }

    .proof-intro {
        color: #64748B;
        font-size: .88rem;
        margin-bottom: 1rem;
    }

    .proof-card {
        position: relative;
        padding: 1rem;
        border: 1px solid #dddaf4;
        border-radius: 14px;
        background: #fafaff;
        height: 100%;
    }

    .proof-step {
        display: inline-grid;
        place-items: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        color: #10069f;
        background: #f7d000;
        font-weight: 800;
        font-size: .8rem;
        margin-right: .45rem;
    }

    .proof-title {
        color: #10069f;
        font-weight: 700;
    }

    .proof-preview {
        width: 100%;
        height: 130px;
        margin: .75rem 0;
        display: grid;
        place-items: center;
        overflow: hidden;
        border: 1px dashed #bbb8db;
        border-radius: 10px;
        background: #f0effb;
        color: #7a7895;
        font-size: .82rem;
    }

    .proof-preview.has-image {
        border-style: solid;
        background: #e8e7f5;
    }

    .proof-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .proof-status {
        display: block;
        color: #64748B;
        font-size: .75rem;
        margin-top: .45rem;
    }

    .proof-status.ready {
        color: #157347;
        font-weight: 700;
    }

    .proof-status.failed {
        color: #b42318;
        font-weight: 700;
    }

    .proof-status.scanning {
        color: #10069f;
        font-weight: 700;
    }

    .proof-status.failed {
        color: #b42318;
        font-weight: 700;
    }

    .proof-status.scanning {
        color: #10069f;
        font-weight: 700;
    }

    .proof-card .form-control {
        font-size: .82rem;
        min-height: auto;
        padding: .45rem;
    }

    .btn-primary {
        background-color: #10069f !important;
        border-color: #10069f !important;
        color: #ffffff !important;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #0d057a !important;
        border-color: #0d057a !important;
        color: #ffffff !important;
    }

    .login-link {
        color: #10069f;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .register-shell {
            display: block;
            text-align: center;
            min-height: auto;
            height: auto;
            max-height: none;
            padding-top: 96px;
        }

        .register-branding {
            position: static;
            width: 100%;
            justify-content: center;
            padding: 20px 0 0;
        }

        .register-right {
            width: 100%;
            max-width: none;
            margin-left: 0;
            padding: 0 20px 40px;
            height: auto;
        }

        .register-panel {
            max-width: 100%;
            max-height: none;
            overflow: visible;
        }
    }

    @media (max-width: 575px) {
        .register-shell {
            padding-right: 0.75rem;
            padding-left: 0.75rem;
        }

        .register-right {
            padding-right: 0;
            padding-left: 0;
        }

        .register-panel {
            padding: 1.25rem;
        }

        .proof-section-header {
            flex-wrap: wrap;
        }

        .register-branding img {
            height: 44px;
        }

        .register-branding .brand-name {
            font-size: 23px;
        }

        .headline-line1,
        .headline-line2 {
            font-size: 1.9rem;
        }

        .sub-headline {
            font-size: 1rem;
        }

        .register-panel { border-radius: 14px; }
    }
</style>
@endsection

@section('content')
<main class="register-shell">
    <div class="register-branding" aria-label="G-SCHED branding">
        <img src="{{ asset('images/bipsu_new.png') }}" alt="BiPSU">
        <img src="{{ asset('images/chatgpt_logo.png') }}" alt="G-SCHED">
        <span class="brand-name">G-SCHED</span>
    </div>


    <section class="register-right">
        <div class="register-panel" aria-labelledby="register-title">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <h1 id="register-title">Create your student account</h1>
            <p class="register-subtitle">Set up your secure access to appointments and guidance support.</p>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf
                <h2 class="section-label">About you</h2>
                <div class="row">
                    @foreach(['first_name' => 'First Name', 'middle_name' => 'Middle Name', 'last_name' => 'Last Name'] as $field => $label)
                        <div class="col-md-4 mb-3">
                            <label for="{{ $field }}" class="form-label">{{ $label }} @if($field !== 'middle_name')<span class="text-danger">*</span>@endif</label>
                            <input type="text" class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" @if($field !== 'middle_name') required @endif>
                            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('student_id') is-invalid @enderror" id="student_id" name="student_id" value="{{ old('student_id') }}" placeholder="00-0-0000" pattern="^\d{2}-\d-\d{4,5}$" required>
                        @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="school" class="form-label">School / Department <span class="text-danger">*</span></label>
                        <select class="form-select @error('school') is-invalid @enderror" id="school" name="school" required>
                            <option value="">Choose school</option>
                            @foreach(\App\Models\User::getSchoolOptions() as $school)
                                <option value="{{ $school }}" @selected(old('school') === $school)>{{ $school }}</option>
                            @endforeach
                        </select>
                        @error('school')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="date_of_birth" class="form-label">Date of birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" autocomplete="bday" required data-today="{{ now()->toDateString() }}">
                        @error('date_of_birth')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                            <option value="">Select</option>
                            @foreach(\App\Models\User::getGenderOptions() as $value => $label)
                                <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <p class="small text-muted mb-3">Your age is calculated from your date of birth when the account is created.</p>

                <div class="proof-section-header">
                    <h2 class="section-label mb-0">Proof of student ID</h2>
                    <span class="proof-required-badge">Required</span>
                </div>
                <p class="proof-warning">Required for registration: upload clear front and back images of your student ID. The System may miss or misread text. Blurry text may not be recognized.</p>
                <p class="proof-intro">Upload a sharp, full-resolution photo with the printed details in focus.</p>
                <div class="row g-3">
                    @foreach(['student_id_front' => ['Front of ID', 'frontPreview', 'frontStatus', '1'], 'student_id_back' => ['Back of ID', 'backPreview', 'backStatus', '2']] as $field => $proof)
                        <div class="col-md-6">
                            <div class="proof-card">
                                <div>
                                    <span class="proof-step">{{ $proof[3] }}</span>
                                    <span class="proof-title">{{ $proof[0] }}</span>
                                    <span class="text-danger ms-1" aria-label="required">*</span>
                                </div>
                                <div class="proof-preview" id="{{ $proof[1] }}">Your preview appears here</div>
                                <label for="{{ $field }}" class="form-label visually-hidden">{{ $proof[0] }} required</label>
                                <input type="file" class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" required aria-required="true" data-preview="{{ $proof[1] }}" data-status="{{ $proof[2] }}">
                                <span class="proof-status" id="{{ $proof[2] }}" role="status" aria-live="polite">Waiting for image</span>
                                <small class="d-block text-muted" id="{{ $proof[2] }}Checks">Checks pending</small>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" data-retry="{{ $field }}">Retry scan</button>
                                @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <h2 class="section-label">Secure your account</h2>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="alert alert-warning small" id="screeningNotice" role="status" tabindex="-1">
                    Select both ID images to start the local screening checks.
                </div>
                <input type="hidden" name="screening_passed" id="screening_passed" value="0">
                <button type="submit" class="btn btn-primary btn-lg w-100" id="registerSubmit" disabled><i class="bi bi-person-plus me-2"></i>Register</button>
            </form>
            <p class="text-center mt-3 mb-0">Already have an account? <a class="login-link" href="{{ route('login') }}">Login</a></p>
        </div>
    </section>
</main>
@endsection

@section('scripts')
@vite(['resources/js/app.js', 'resources/js/register-screening.js'])
<script>
(() => {
    const form = document.querySelector('form[action="{{ route('register') }}"]');
    const frontInput = document.getElementById('student_id_front');
    const backInput = document.getElementById('student_id_back');
    const dobInput = document.getElementById('date_of_birth');
    const firstNameInput = document.getElementById('first_name');
    const middleNameInput = document.getElementById('middle_name');
    const lastNameInput = document.getElementById('last_name');
    const studentIdInput = document.getElementById('student_id');
    const nameInputs = [firstNameInput, middleNameInput, lastNameInput];
    const submit = document.getElementById('registerSubmit');
    const passed = document.getElementById('screening_passed');
    const notice = document.getElementById('screeningNotice');
    const results = { front: false, back: false };
    const generations = { front: 0, back: 0 };
    const timers = { front: null, back: null };
    const imageUrls = { front: null, back: null };
    const imageFiles = { front: null, back: null };
    const maxBytes = 5 * 1024 * 1024;

    const updateSubmit = () => {
        const ready = results.front && results.back;
        passed.value = ready ? '1' : '0';
        submit.disabled = !ready;
        notice.className = `alert small ${ready ? 'alert-success' : 'alert-warning'}`;
        notice.textContent = ready
            ? 'Required student ID screening passed. You may continue with registration.'
            : 'Registration cannot continue until both student ID images pass the required screening.';
    };
    const syncDob = () => {
        if (typeof window.validateRegistrationDob !== 'function') return false;
        const validation = window.validateRegistrationDob(dobInput.value, dobInput.dataset.today);
        dobInput.setCustomValidity(validation.message);
        return validation.message === '';
    };
    const setStatus = (id, text, state) => {
        const element = document.getElementById(id);
        element.textContent = text;
        element.classList.remove('ready', 'failed', 'scanning');
        if (state) element.classList.add(state);
    };
    const setChecks = (side, checks) => {
        const element = document.getElementById(`${side}StatusChecks`);
        if (element) element.textContent = checks;
    };
    const revokeImageUrl = side => {
        if (!imageUrls[side]) return;
        URL.revokeObjectURL(imageUrls[side]);
        imageUrls[side] = null;
        imageFiles[side] = null;
    };
    const loadImage = (file, side) => new Promise((resolve, reject) => {
        if (imageFiles[side] !== file) {
            revokeImageUrl(side);
            imageFiles[side] = file;
            imageUrls[side] = URL.createObjectURL(file);
        }
        const image = new Image();
        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error('The selected image could not be opened.'));
        image.src = imageUrls[side];
    });
    const makeOcrCanvas = (image, contrast, threshold, cropTop = 0, cropBottom = 0) => {
        const canvas = document.createElement('canvas');
        const sourceWidth = Math.max(1, image.naturalWidth || image.width);
        const sourceHeight = Math.max(1, image.naturalHeight || image.height);
        const sourceTop = Math.min(sourceHeight - 1, Math.max(0, Math.floor(sourceHeight * cropTop)));
        const cropHeight = Math.max(
            1,
            sourceHeight - sourceTop - Math.floor(sourceHeight * cropBottom),
        );
        if (typeof window.getStudentIdOcrDimensions !== 'function') {
            throw new Error('The high-resolution local image processor is not ready. Reload and retry.');
        }
        const dimensions = window.getStudentIdOcrDimensions(sourceWidth, cropHeight);
        canvas.width = dimensions.width;
        canvas.height = dimensions.height;
        const context = canvas.getContext('2d', { willReadFrequently: threshold });
        if (!context) throw new Error('The image could not be prepared for local OCR.');
        context.fillStyle = '#fff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';
        context.filter = `grayscale(1) contrast(${contrast}) brightness(1.04)`;
        context.drawImage(
            image,
            0, sourceTop, sourceWidth, cropHeight,
            0, 0, canvas.width, canvas.height,
        );
        context.filter = 'none';

        if (threshold) {
            const pixels = context.getImageData(0, 0, canvas.width, canvas.height);
            const histogram = new Uint32Array(256);
            for (let index = 0; index < pixels.data.length; index += 4) {
                histogram[pixels.data[index]] += 1;
            }
            const pixelCount = canvas.width * canvas.height;
            let sum = 0;
            for (let value = 0; value < 256; value += 1) sum += value * histogram[value];
            let backgroundWeight = 0;
            let backgroundSum = 0;
            let thresholdValue = 155;
            let bestVariance = 0;
            for (let value = 0; value < 256; value += 1) {
                backgroundWeight += histogram[value];
                if (!backgroundWeight) continue;
                const foregroundWeight = pixelCount - backgroundWeight;
                if (!foregroundWeight) break;
                backgroundSum += value * histogram[value];
                const backgroundMean = backgroundSum / backgroundWeight;
                const foregroundMean = (sum - backgroundSum) / foregroundWeight;
                const variance = backgroundWeight * foregroundWeight * (backgroundMean - foregroundMean) ** 2;
                if (variance > bestVariance) {
                    bestVariance = variance;
                    thresholdValue = value;
                }
            }
            for (let index = 0; index < pixels.data.length; index += 4) {
                const value = pixels.data[index] > thresholdValue ? 255 : 0;
                pixels.data[index] = value;
                pixels.data[index + 1] = value;
                pixels.data[index + 2] = value;
            }
            context.putImageData(pixels, 0, 0);
        }
        return canvas;
    };
    const makeSurnameOcrCanvas = (image, rotationDegrees) => {
        const sourceWidth = Math.max(1, image.naturalWidth || image.width);
        const sourceHeight = Math.max(1, image.naturalHeight || image.height);
        const sourceDimensions = window.getStudentIdOcrDimensions(sourceWidth, sourceHeight);
        const scale = Math.min(1, sourceDimensions.width / sourceWidth, sourceDimensions.height / sourceHeight);
        const workingWidth = Math.max(1, Math.round(sourceWidth * scale));
        const workingHeight = Math.max(1, Math.round(sourceHeight * scale));
        const radians = rotationDegrees * Math.PI / 180;
        const rotatedWidth = Math.ceil(
            Math.abs(workingWidth * Math.cos(radians)) + Math.abs(workingHeight * Math.sin(radians)),
        );
        const rotatedHeight = Math.ceil(
            Math.abs(workingHeight * Math.cos(radians)) + Math.abs(workingWidth * Math.sin(radians)),
        );
        const rotated = document.createElement('canvas');
        rotated.width = rotatedWidth;
        rotated.height = rotatedHeight;
        const rotatedContext = rotated.getContext('2d');
        if (!rotatedContext) throw new Error('The surname image could not be prepared for local OCR.');
        rotatedContext.fillStyle = '#fff';
        rotatedContext.fillRect(0, 0, rotatedWidth, rotatedHeight);
        rotatedContext.imageSmoothingEnabled = true;
        rotatedContext.imageSmoothingQuality = 'high';
        rotatedContext.filter = 'grayscale(1) contrast(1.25) brightness(1.04)';
        rotatedContext.translate(rotatedWidth / 2, rotatedHeight / 2);
        rotatedContext.rotate(radians);
        rotatedContext.drawImage(image, -workingWidth / 2, -workingHeight / 2, workingWidth, workingHeight);

        const cropX = Math.floor(rotatedWidth * 0.30);
        const cropY = Math.floor(rotatedHeight * 0.63);
        const cropWidth = Math.max(1, Math.floor(rotatedWidth * 0.40));
        const cropHeight = Math.max(1, Math.floor(rotatedHeight * 0.10));
        const dimensions = window.getStudentIdOcrDimensions(cropWidth, cropHeight);
        const canvas = document.createElement('canvas');
        canvas.width = dimensions.width;
        canvas.height = dimensions.height;
        const context = canvas.getContext('2d');
        if (!context) throw new Error('The surname image could not be prepared for local OCR.');
        context.fillStyle = '#fff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';
        context.drawImage(
            rotated,
            cropX, cropY, cropWidth, cropHeight,
            0, 0, canvas.width, canvas.height,
        );
        rotated.width = 0;
        rotated.height = 0;
        return canvas;
    };
    const localOcr = async (image, pageSegmentationMode, characterWhitelist = '') => {
        if (typeof window.localStudentIdOcr !== 'function') {
            throw new Error('Local OCR is not available.');
        }
        return window.localStudentIdOcr(image, pageSegmentationMode, characterWhitelist);
    };
    const scan = async (input, side, generation) => {
        const file = input.files?.[0];
        const statusId = side === 'front' ? 'frontStatus' : 'backStatus';
        if (generation !== generations[side]) return;
        if (!file) {
            revokeImageUrl(side);
            setStatus(statusId, 'Waiting for image', null);
            setChecks(side, 'Checks pending');
            return;
        }
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > maxBytes) {
            setStatus(statusId, 'Use a JPG, PNG, or WEBP image up to 5 MB.', 'failed');
            setChecks(side, 'File type or size check failed');
            updateSubmit();
            return;
        }
        const preview = document.getElementById(side === 'front' ? 'frontPreview' : 'backPreview');
        setStatus(statusId, 'Checking...', 'scanning');
        setChecks(side, 'Checking...');
        try {
            const image = await loadImage(file, side);
            if (generation !== generations[side]) return;
            const previewImage = document.createElement('img');
            previewImage.alt = `${side} of selected student ID`;
            previewImage.src = imageUrls[side];
            preview.replaceChildren(previewImage);
            preview.classList.add('has-image');
            if (typeof window.setLocalStudentIdOcrProgressHandler === 'function') {
                window.setLocalStudentIdOcrProgressHandler(progress => {
                    if (generation === generations[side] && progress.status) setChecks(side, 'Checking...');
                });
            }
            const ocrResults = [];
            for (const [contrast, threshold] of [[1.25, false], [1.65, true]]) {
                const variant = makeOcrCanvas(image, contrast, threshold);
                try {
                    ocrResults.push(await localOcr(variant));
                } finally {
                    variant.width = 0;
                    variant.height = 0;
                }
                if (generation !== generations[side]) return;
            }
            if (side === 'front') {
                const details = makeOcrCanvas(image, 1.25, false, 0.62);
                try {
                    ocrResults.push(await localOcr(details, '11'));
                } finally {
                    details.width = 0;
                    details.height = 0;
                }
                if (generation !== generations[side]) return;

                const printedDetails = makeOcrCanvas(image, 1.25, false, 0.6, 0.08);
                try {
                    ocrResults.push(await localOcr(printedDetails, '6'));
                } finally {
                    printedDetails.width = 0;
                    printedDetails.height = 0;
                }
                if (generation !== generations[side]) return;

                for (const rotation of [-4, 4]) {
                    const surname = makeSurnameOcrCanvas(image, rotation);
                    try {
                        ocrResults.push(await localOcr(surname, '7', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'));
                    } finally {
                        surname.width = 0;
                        surname.height = 0;
                    }
                    if (generation !== generations[side]) return;
                }
            }
            const screen = side === 'front'
                ? window.screenStudentIdFront?.({
                    ocrResults,
                    firstName: firstNameInput.value.trim(),
                    middleName: middleNameInput.value.trim(),
                    lastName: lastNameInput.value.trim(),
                    studentId: studentIdInput.value.trim(),
                })
                : window.screenStudentIdBack?.({ ocrResults, dateOfBirth: dobInput.value });
            if (!screen) throw new Error('Local screening checks are unavailable. Reload the page and retry.');
            const valid = screen.passed;
            results[side] = valid;
            setChecks(side, 'Checked');
            const statusText = side === 'front'
                ? (valid ? 'Student ID (Front): PASSED' : 'Student ID (Front): FAILED')
                : (valid ? 'Student ID (Back): PASSED' : 'Student ID (Back): FAILED');
            setStatus(statusId, statusText, valid ? 'ready' : 'failed');
        } catch (error) {
            if (generation === generations[side]) {
                const message = error instanceof Error ? error.message : 'An unexpected scanning error occurred.';
                setStatus(statusId, `Could not scan this image: ${message} Select it again to retry.`, 'failed');
                setChecks(side, 'Local scan failed; select the image again to retry');
                results[side] = false;
            }
        } finally {
            if (generation === generations[side]) updateSubmit();
        }
    };
    const scheduleScan = (input, side, delay = 0) => {
        generations[side] += 1;
        const generation = generations[side];
        window.clearTimeout(timers[side]);
        results[side] = false;
        updateSubmit();
        timers[side] = window.setTimeout(() => scan(input, side, generation), delay);
    };
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', () => {
            const side = input.id === 'student_id_front' ? 'front' : 'back';
            revokeImageUrl(side);
            scheduleScan(input, side);
        });
    });
    document.querySelectorAll('[data-retry]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.retry);
            const side = input.id === 'student_id_front' ? 'front' : 'back';
            scheduleScan(input, side);
        });
    });
    [...nameInputs, studentIdInput].forEach(input => input.addEventListener('input', () => {
        if (frontInput.files?.[0]) scheduleScan(frontInput, 'front', 400);
    }));
    const onDobChange = () => {
        syncDob();
        if (backInput.files?.[0]) scheduleScan(backInput, 'back', 400);
    };
    dobInput.addEventListener('input', onDobChange);
    dobInput.addEventListener('change', onDobChange);
    syncDob();
    form.addEventListener('submit', event => {
        if (!syncDob()) {
            event.preventDefault();
            dobInput.reportValidity();
            return;
        }
        if (passed.value !== '1') {
            event.preventDefault();
            notice.focus();
        }
    });
    window.addEventListener('pagehide', () => {
        window.clearTimeout(timers.front);
        window.clearTimeout(timers.back);
        revokeImageUrl('front');
        revokeImageUrl('back');
    }, { once: true });
})();
</script>
@endsection
@extends('layouts.app')

@section('title', ' - Profile')

@section('styles')
<style>
    .profile-summary-card {
        border-top: 3px solid var(--medium-blue);
    }

    .profile-full-name {
        color: var(--text-primary);
        overflow-wrap: anywhere;
    }

    .profile-avatar-wrapper {
        width: 160px;
        height: 160px;
    }

    .camera-btn {
        position: absolute;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        bottom: -8px;
        right: -8px;
        box-shadow: 0 2px 8px rgba(5, 63, 92, 0.15);
    }

    .profile-photo-preview {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e2e8f0;
    }

    .profile-photo-placeholder {
        width: 160px;
        height: 160px;
    }

    #profileTabs .nav-link {
        color: var(--text-muted);
        font-weight: 500;
        padding: 0.75rem 1.5rem 0.5rem;
        border: none;
        border-bottom: 2px solid transparent;
    }

    #profileTabs .nav-link:hover {
        color: var(--medium-blue);
        border-color: var(--medium-blue) transparent transparent transparent;
    }

    #profileTabs .nav-link.active {
        color: var(--medium-blue);
        border-bottom-color: var(--medium-blue);
    }

    @media (max-width: 575.98px) {
        #profileTabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overscroll-behavior-inline: contain;
        }

        #profileTabs .nav-item {
            flex: 0 0 auto;
        }

        #profileTabs .nav-link {
            white-space: nowrap;
            padding-right: 0.875rem;
            padding-left: 0.875rem;
        }

        .profile-header-actions {
            flex-wrap: wrap;
            width: 100%;
        }

        .profile-header-actions .btn {
            flex: 1 1 8rem;
        }
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-label {
        font-weight: 500;
        color: var(--text-primary);
    }

    .section-heading {
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .compact-info-item .label {
        color: var(--text-primary);
    }

    @media (max-width: 767.98px) {
        .profile-avatar-wrapper {
            width: 140px;
            height: 140px;
        }

        .profile-photo-preview,
        .profile-photo-placeholder {
            width: 140px;
            height: 140px;
        }

        .camera-btn {
            width: 32px;
            height: 32px;
        }
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-3 mb-4 border-bottom">
    <div class="mb-2 mb-md-0">
        <h1 class="h2 mb-1">Profile Settings</h1>
        <p class="text-muted mb-0">Manage your guidance counselor credentials, background, and public profile details.</p>
    </div>
    <div class="d-flex gap-2 profile-header-actions">
        <button type="button" class="btn btn-outline-secondary" id="resetChangesBtn">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Changes
        </button>
        <button type="submit" form="profileForm" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Save Profile
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
    @csrf
    @method('PUT')
    <input type="hidden" name="remove_photo" id="removePhotoInput" value="0">

    <div class="row">
        <!-- LEFT COLUMN: Profile Summary -->
        <div class="col-lg-4 col-xl-3 mb-4">
            <div class="card shadow-sm h-100 profile-summary-card">
                <div class="card-body text-center">
                    <!-- Profile Photo with Camera Button -->
                    <div class="position-relative d-inline-block mb-3">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile Photo" class="profile-photo-preview" id="photoPreview" data-original-src="{{ asset('storage/' . auth()->user()->profile_photo) }}">
                        @else
                            <div class="bg-light bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center profile-photo-placeholder" id="photoPlaceholder" style="background: rgba(66, 158, 189, 0.15) !important;">
                                <i class="bi bi-person fs-1" style="color: var(--medium-blue);"></i>
                            </div>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary camera-btn d-inline-flex align-items-center justify-content-center" id="photoBtn" style="background: var(--card-bg);">
                            <i class="bi bi-camera" style="color: var(--medium-blue);"></i>
                        </button>
                    </div>

                    <!-- Remove Photo (if exists) -->
                    @if(auth()->user()->profile_photo)
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-danger btn-sm" id="removePhotoBtn">
                                <i class="bi bi-trash me-1"></i>Remove Photo
                            </button>
                        </div>
                    @endif

                    @error('profile_photo')
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                    @enderror

                    <!-- Name & Email -->
                    <h4 class="profile-full-name mb-1">{{ auth()->user()->full_name }}</h4>
                    <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>

                    <!-- Role Badges -->
                    <div class="d-flex flex-column gap-1 mb-3">
                        <span class="badge bg-{{ auth()->user()->role->name === 'admin' ? 'danger' : (auth()->user()->role->name === 'guidance_associate' ? 'info' : 'success') }} px-3 py-2 fw-medium">
                            {{ auth()->user()->display_role_name }}
                        </span>
                        @if(auth()->user()->isGuidanceAssociate())
                            <span class="badge bg-warning px-3 py-2 fw-medium" style="color: var(--navy);">
                                RGC Licensed
                            </span>
                        @endif
                    </div>

                    <hr class="my-3">

                    <!-- Profile Completeness -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0" style="color: var(--text-primary);">Profile Completeness</h6>
                        <span class="text-muted small fw-medium" id="completenessPercent">0%</span>
                    </div>
                    <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" id="completenessBar" style="width: 0%;"></div>
                        </div>

                    <!-- Compact Info -->
                    <div class="compact-info-section mb-4">
                        <div class="compact-info-item mb-3">
                            <div class="label small fw-medium mb-1" style="color: var(--text-primary);">Assigned School</div>
                            <div class="value small" style="color: var(--text-muted);">{{ auth()->user()->school ?: 'Not provided' }}</div>
                        </div>
                        @unless(auth()->user()->isStudent())
                            <div class="compact-info-item mb-3">
                                <div class="label small fw-medium mb-1" style="color: var(--text-primary);">Office</div>
                                <div class="value small" style="color: var(--text-muted);">{{ auth()->user()->office_location ?: 'Not provided' }}</div>
                            </div>
                            <div class="compact-info-item">
                                <div class="label small fw-medium mb-1" style="color: var(--text-primary);">License No.</div>
                                <div class="value small" style="color: var(--text-muted);">{{ auth()->user()->professional_credentials ?: 'Not provided' }}</div>
                            </div>
                        @endunless
                    </div>

                    <!-- Save Profile Changes Button -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-2"></i>Save Profile Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Profile Settings with Tabs -->
        <div class="col-lg-8 col-xl-9">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <!-- Hidden file input for photo upload -->
                    <input type="file" class="d-none" id="profile_photo" name="profile_photo" accept="image/*">

                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                Personal Details
                            </button>
                        </li>
                        @unless(auth()->user()->isStudent())
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button" role="tab" aria-controls="experience" aria-selected="false">
                                    Experience &amp; Bio
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="credentials-tab" data-bs-toggle="tab" data-bs-target="#credentials" type="button" role="tab" aria-controls="credentials" aria-selected="false">
                                    Credentials &amp; Expertise
                                </button>
                            </li>
                        @endunless
                    </ul>

                    <div class="tab-content pt-4">
                        <!-- Personal Details Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ auth()->user()->first_name }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" name="middle_name" value="{{ auth()->user()->middle_name }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ auth()->user()->last_name }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="{{ auth()->user()->phone ?: '' }}" placeholder="Add your phone number">
                                </div>
                            </div>

                            @if(!auth()->user()->isStudent())
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="professional_title" class="form-label">Professional Title</label>
                                        <input type="text" class="form-control" id="professional_title" name="professional_title" value="{{ auth()->user()->professional_title }}" placeholder="e.g. Registered Guidance Counselor">
                                        <p class="form-text text-muted mb-1">e.g. Registered Guidance Counselor</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="school" class="form-label">Assigned School</label>
                                        <input type="text" class="form-control" id="school" name="school" value="{{ auth()->user()->school }}">
                                        <p class="form-text text-muted mb-1">All schools. School assignments are managed by the Guidance Counselor.</p>
                                    </div>
                                </div>

                                @if(auth()->user()->isAdmin() || auth()->user()->isGuidanceAssociate())
                                    <div class="mb-3">
                                        <label for="facebook_profile_url" class="form-label">Facebook Profile Link</label>
                                        <input type="url" class="form-control @error('facebook_profile_url') is-invalid @enderror" id="facebook_profile_url" name="facebook_profile_url" value="{{ old('facebook_profile_url', auth()->user()->facebook_profile_url) }}" placeholder="https://www.facebook.com/your.profile">
                                        <div class="form-text">Shared with students when you approve an online appointment.</div>
                                        @error('facebook_profile_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="office_location" class="form-label">Office / Location</label>
                                    <p class="form-text text-muted mb-1">e.g. STCS Faculty Office</p>
                                    <input type="text" class="form-control" id="office_location" name="office_location" value="{{ auth()->user()->office_location }}" placeholder="e.g. STCS Faculty Office">
                                </div>
                            @else
                                <div class="mb-3">
                                    <label for="school" class="form-label">School / Department</label>
                                    <p class="form-text text-muted mb-1">Select your school</p>
                                    <select class="form-select @error('school') is-invalid @enderror" id="school" name="school">
                                        <option value="">Select your school</option>
                                        @foreach(\App\Models\User::getSchoolOptions() as $school)
                                            <option value="{{ $school }}" {{ auth()->user()->school == $school ? 'selected' : '' }}>{{ $school }}</option>
                                        @endforeach
                                    </select>
                                    @error('school')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ auth()->user()->age }}" min="12" max="100">
                                        @error('age')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Age must be between 12 and 100</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Select your gender</option>
                                            @foreach(\App\Models\User::getGenderOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ auth()->user()->gender == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="student_id" value="{{ auth()->user()->student_id }}" readonly aria-readonly="true">
                                    <div class="form-text">Your Student ID is set during registration and cannot be changed here.</div>
                                </div>

                                @include('shared.student-id-proof', [
                                    'studentIdProofUser' => $user,
                                    'studentIdProofRouteName' => 'student-id-proofs.show',
                                    'studentIdProofRouteTarget' => $user,
                                ])
                            @endif
                        </div>

                        @unless(auth()->user()->isStudent())
                            <!-- Experience & Bio Tab -->
                            <div class="tab-pane fade" id="experience" role="tabpanel" aria-labelledby="experience-tab">
                                <div class="mb-4">
                                    <h5 class="section-heading">Educational Background</h5>
                                    <p class="form-text text-muted mb-2">Degrees, universities and years</p>
                                    <textarea class="form-control" id="educational_background" name="educational_background" rows="3">{{ auth()->user()->educational_background }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <h5 class="section-heading">Professional Experience</h5>
                                    <p class="form-text text-muted mb-2">Years of practice, roles and institutions</p>
                                    <textarea class="form-control" id="professional_experience" name="professional_experience" rows="3">{{ auth()->user()->professional_experience }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <h5 class="section-heading">Professional Biography</h5>
                                    <p class="form-text text-muted mb-2">A short professional summary</p>
                                    <textarea class="form-control" id="professional_biography" name="professional_biography" rows="4">{{ auth()->user()->professional_biography }}</textarea>
                                </div>
                            </div>

                            <!-- Credentials & Expertise Tab -->
                            <div class="tab-pane fade" id="credentials" role="tabpanel" aria-labelledby="credentials-tab">
                                <div class="mb-4">
                                    <h5 class="section-heading">Professional Credentials &amp; Licenses</h5>
                                    <p class="form-text text-muted mb-2">Licenses and professional credentials</p>
                                    <textarea class="form-control" id="professional_credentials" name="professional_credentials" rows="3">{{ auth()->user()->professional_credentials }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <h5 class="section-heading">Certifications</h5>
                                    <p class="form-text text-muted mb-2">Relevant certifications</p>
                                    <textarea class="form-control" id="certifications" name="certifications" rows="3">{{ auth()->user()->certifications }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <h5 class="section-heading">Trainings &amp; Workshops</h5>
                                    <p class="form-text text-muted mb-2">Specialized training programs attended</p>
                                    <textarea class="form-control" id="trainings" name="trainings" rows="3">{{ auth()->user()->trainings }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <h5 class="section-heading">Areas of Expertise</h5>
                                    <p class="form-text text-muted mb-2">Counseling specialties</p>
                                    <textarea class="form-control" id="areas_of_expertise" name="areas_of_expertise" rows="3">{{ auth()->user()->areas_of_expertise }}</textarea>
                                </div>
                            </div>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row justify-content-end mt-4" id="password-settings">
    <div class="col-lg-8 col-xl-9">
        <section class="card shadow-sm" aria-labelledby="password-settings-title">
            <div class="card-header">
                <h2 class="h5 mb-0" id="password-settings-title">
                    <i class="bi bi-key me-2"></i>Change Password
                </h2>
            </div>
            <div class="card-body">
                <p class="text-muted">Choose a strong password to keep your account secure.</p>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-key me-2"></i>Change Password
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_photo');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');
    const removePhotoBtn = document.getElementById('removePhotoBtn');
    const removePhotoInput = document.getElementById('removePhotoInput');
    const photoBtn = document.getElementById('photoBtn');
    const form = document.getElementById('profileForm');
    const previewContainer = photoPreview ? photoPreview.parentElement : (photoPlaceholder ? photoPlaceholder.parentElement : null);
    const originalPhotoSrc = @json(auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : null);
    const hasOriginalPhoto = originalPhotoSrc !== null;

    function restorePhoto() {
        if (hasOriginalPhoto) {
            if (photoPlaceholder) photoPlaceholder.remove();
            let img = document.getElementById('photoPreview');
            if (!img) {
                img = document.createElement('img');
                img.id = 'photoPreview';
                img.alt = 'Profile Photo';
                img.className = 'profile-photo-preview';
                img.setAttribute('data-original-src', originalPhotoSrc);
                if (previewContainer) previewContainer.appendChild(img);
            }
            img.src = originalPhotoSrc;
            removePhotoInput.value = '0';
            if (removePhotoBtn) removePhotoBtn.style.display = 'inline-block';
        } else {
            const img = document.getElementById('photoPreview');
            if (img) {
                img.remove();
            }
            if (photoPlaceholder && previewContainer && !document.getElementById('photoPlaceholder')) {
                previewContainer.appendChild(photoPlaceholder);
            }
            removePhotoInput.value = '0';
            if (removePhotoBtn) removePhotoBtn.style.display = 'none';
        }
    }

    // Camera button opens file picker
    photoBtn?.addEventListener('click', function() {
        fileInput?.click();
    });

    fileInput?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            removePhotoInput.value = '0';
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById('photoPreview');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'photoPreview';
                    img.alt = 'Profile Photo';
                    img.className = 'profile-photo-preview';
                    img.setAttribute('data-original-src', originalPhotoSrc || '');
                    if (photoPlaceholder) photoPlaceholder.remove();
                    if (previewContainer) previewContainer.appendChild(img);
                }
                img.src = e.target.result;
                if (removePhotoBtn) removePhotoBtn.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    });

    removePhotoBtn?.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            removePhotoInput.value = '1';
            fileInput.value = '';
            const img = document.getElementById('photoPreview');
            if (img) img.remove();
            if (!document.getElementById('photoPlaceholder') && photoPlaceholder && previewContainer) {
                previewContainer.appendChild(photoPlaceholder);
            }
            if (removePhotoBtn) removePhotoBtn.style.display = 'none';
        }
    });

    // Reset Changes
    document.getElementById('resetChangesBtn')?.addEventListener('click', function() {
        if (confirm('Reset all changes to default values?')) {
            if (form) {
                form.reset();
            }
            restorePhoto();
            if (removePhotoBtn) {
                removePhotoBtn.style.display = hasOriginalPhoto ? 'inline-block' : 'none';
            }
            updateCompleteness();
        }
    });

    // Profile Completeness
    const completenessFields = @if(!auth()->user()->isStudent())
        ['first_name', 'middle_name', 'last_name', 'email', 'phone', 'professional_title', 'school', 'office_location', 'educational_background', 'professional_credentials', 'certifications', 'trainings', 'areas_of_expertise', 'professional_experience', 'professional_biography']
    @else
        ['first_name', 'middle_name', 'last_name', 'email', 'age', 'gender', 'student_id']
    @endif;

    function calculateCompleteness() {
        let filled = 0;
        completenessFields.forEach(function(field) {
            const el = document.getElementById(field);
            if (el && el.value && el.value.trim() !== '') {
                filled++;
            }
        });
        return completenessFields.length > 0 ? Math.round((filled / completenessFields.length) * 100) : 0;
    }

    function updateCompleteness() {
        const percent = calculateCompleteness();
        const percentText = document.getElementById('completenessPercent');
        const progressBar = document.getElementById('completenessBar');
        if (percentText) percentText.textContent = percent + '%';
        if (progressBar) {
            progressBar.style.width = percent + '%';
            if (percent < 25) {
                progressBar.style.backgroundColor = 'var(--orange)';
            } else if (percent < 50) {
                progressBar.style.backgroundColor = 'var(--yellow)';
            } else if (percent < 75) {
                progressBar.style.backgroundColor = 'var(--medium-blue)';
            } else {
                progressBar.style.backgroundColor = 'var(--medium-blue)';
            }
        }
    }

    updateCompleteness();

    completenessFields.forEach(function(field) {
        const el = document.getElementById(field);
        if (el) {
            el.addEventListener('input', updateCompleteness);
            el.addEventListener('change', updateCompleteness);
        }
    });
});
</script>
@endsection

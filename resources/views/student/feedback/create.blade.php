@extends('layouts.app')

@section('title', ' - Submit Feedback')

@section('content')
<div class="feedback-container">
    <div class="feedback-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="h2 mb-1" style="color: var(--text-primary); font-weight: 700;">
                <i class="bi bi-chat-text me-2" style="color: var(--medium-blue);"></i>Submit Feedback
            </h1>
            <p class="text-muted mb-0">Please share your experience with your guidance appointment.</p>
        </div>
        <a href="{{ route('student.feedback.index') }}" class="btn btn-outline-secondary" style="min-height: 44px; padding: 0.5rem 1.25rem;">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0" style="color: var(--text-primary); font-weight: 600;">
                        Feedback for Appointment
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="appointment-summary card bg-light border mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-muted" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Guidance Associate
                                    </div>
                                    <div class="fw-medium" style="color: var(--text-primary);">
                                        {{ $appointment->guidanceAssociate->full_name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Date & Time
                                    </div>
                                    <div class="fw-medium" style="color: var(--text-primary);">
                                        {{ $appointment->formatted_date }} <span class="text-muted fw-normal">at</span> {{ $appointment->formatted_time }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-muted" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Purpose
                                    </div>
                                    <div class="fw-medium" style="color: var(--text-primary);">
                                        {{ Str::limit($appointment->purpose, 200) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('student.feedback.store') }}">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div id="sqd-error" class="alert alert-danger" style="display: none;">
                            Please answer all SQD questions before submitting your feedback.
                        </div>

                        <div class="sqd-section cc-section mb-4">
                            <h4 class="sqd-title mb-2">
                                Citizen's Charter (CC) Question
                            </h4>
                            <p class="sqd-instruction text-muted mb-4">
                                press to 1 only to check mark (✔) your answer to the CC questions. The CC is an Official Document that Reflects the Services of a Government agency/office including its requirements, fees, and processing times among others
                            </p>

                            <div class="cc-question-list">
                                <fieldset class="cc-question">
                                    <legend><span class="sqd-label">CC1</span> Which of the following best describes your awareness of a CC?</legend>
                                    <div class="cc-options">
                                        <label class="cc-option">
                                            <input type="radio" name="cc1" value="1">
                                            <span>1. I know what a CC is and I saw this office's CC.</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc1" value="2">
                                            <span>2. I know what a CC is but I did NOT see this office's CC.</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc1" value="3">
                                            <span>3. I learned of the CC only when I saw this office's CC.</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc1" value="4">
                                            <span>4. I do not know what a CC is and I did not see one in this office. (Answer 'N/A' on CC2 and CC3)</span>
                                        </label>
                                    </div>
                                </fieldset>

                                <fieldset class="cc-question">
                                    <legend><span class="sqd-label">CC2</span> If aware of CC (answered 1-3 in CC1), would you say that the CC of this office was ...?</legend>
                                    <div class="cc-options cc-options-grid">
                                        <label class="cc-option">
                                            <input type="radio" name="cc2" value="1">
                                            <span>1. Easy to see</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc2" value="2">
                                            <span>2. Somewhat easy to see</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc2" value="3">
                                            <span>3. Difficult to see</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc2" value="4">
                                            <span>4. Not visible at all</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc2" value="5">
                                            <span>5. N/A</span>
                                        </label>
                                    </div>
                                </fieldset>

                                <fieldset class="cc-question">
                                    <legend><span class="sqd-label">CC3</span> If aware of CC (answered codes 1-3 in CC1), how much did the CC help you in your transaction?</legend>
                                    <div class="cc-options cc-options-grid">
                                        <label class="cc-option">
                                            <input type="radio" name="cc3" value="1">
                                            <span>1. Helped very much</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc3" value="2">
                                            <span>2. Somewhat helped</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc3" value="3">
                                            <span>3. Did not help</span>
                                        </label>
                                        <label class="cc-option">
                                            <input type="radio" name="cc3" value="4">
                                            <span>4. N/A</span>
                                        </label>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <div class="sqd-section mb-4">
                            <h4 class="sqd-title mb-2">
                                SERVICE QUALITY DIMENSION (SQD)
                            </h4>
                            <p class="sqd-instruction text-muted mb-4">
                                For SQD, please put a check mark (✔) on the column that best corresponds to your answer.
                            </p>

                            <div class="sqd-legend mb-4 d-none d-lg-flex justify-content-end gap-2">
                                <span class="sqd-legend-item"><span class="sqd-abbrev">SD</span> = Strongly Disagree</span>
                                <span class="sqd-legend-item"><span class="sqd-abbrev">D</span> = Disagree</span>
                                <span class="sqd-legend-item"><span class="sqd-abbrev">N</span> = Neither Agree nor Disagree</span>
                                <span class="sqd-legend-item"><span class="sqd-abbrev">A</span> = Agree</span>
                                <span class="sqd-legend-item"><span class="sqd-abbrev">SA</span> = Strongly Agree</span>
                                <span class="sqd-legend-item"><span class="sqd-abbrev">N/A</span> = Not Applicable</span>
                            </div>

                            <!-- Desktop Table Layout -->
                            <div class="d-none d-lg-block">
                                <div class="sqd-table-responsive">
                                    <table class="table sqd-table mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="sqd-th-question">Question</th>
                                                <th scope="col" class="sqd-th-rating">SD</th>
                                                <th scope="col" class="sqd-th-rating">D</th>
                                                <th scope="col" class="sqd-th-rating">N</th>
                                                <th scope="col" class="sqd-th-rating">A</th>
                                                <th scope="col" class="sqd-th-rating">SA</th>
                                                <th scope="col" class="sqd-th-rating">N/A</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(\App\Models\Feedback::SQD_QUESTIONS as $field => $question)
                                                <tr class="sqd-row">
                                                    <td class="sqd-td-question align-middle">
                                                        <span class="sqd-label">{{ strtoupper($field) }}</span>
                                                        <span class="sqd-text">{{ $question }}</span>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="strongly_disagree" id="{{ $field }}_strongly_disagree" required>
                                                            <label for="{{ $field }}_strongly_disagree" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="disagree" id="{{ $field }}_disagree" required>
                                                            <label for="{{ $field }}_disagree" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="neither" id="{{ $field }}_neither" required>
                                                            <label for="{{ $field }}_neither" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="agree" id="{{ $field }}_agree" required>
                                                            <label for="{{ $field }}_agree" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="strongly_agree" id="{{ $field }}_strongly_agree" required>
                                                            <label for="{{ $field }}_strongly_agree" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                    <td class="sqd-td-rating text-center align-middle">
                                                        <div class="sqd-radio-wrapper">
                                                            <input type="radio" name="{{ $field }}" value="not_applicable" id="{{ $field }}_not_applicable" required>
                                                            <label for="{{ $field }}_not_applicable" class="sqd-radio-label"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Mobile Card Layout -->
                            <div class="d-lg-none">
                                @foreach(\App\Models\Feedback::SQD_QUESTIONS as $field => $question)
                                    <div class="sqd-mobile-card mb-3">
                                        <div class="sqd-mobile-header">
                                            <span class="sqd-label">{{ strtoupper($field) }}</span>
                                            <p class="sqd-text mb-0">{{ $question }}</p>
                                        </div>
                                        <div class="sqd-options d-grid gap-2 mt-3">
                                            <label class="sqd-option" for="{{ $field }}_mobile_strongly_disagree">
                                                <input type="radio" name="{{ $field }}" value="strongly_disagree" id="{{ $field }}_mobile_strongly_disagree" required>
                                                <span>Strongly Disagree</span>
                                            </label>
                                            <label class="sqd-option" for="{{ $field }}_mobile_disagree">
                                                <input type="radio" name="{{ $field }}" value="disagree" id="{{ $field }}_mobile_disagree" required>
                                                <span>Disagree</span>
                                            </label>
                                            <label class="sqd-option" for="{{ $field }}_mobile_neither">
                                                <input type="radio" name="{{ $field }}" value="neither" id="{{ $field }}_mobile_neither" required>
                                                <span>Neither Agree nor Disagree</span>
                                            </label>
                                            <label class="sqd-option" for="{{ $field }}_mobile_agree">
                                                <input type="radio" name="{{ $field }}" value="agree" id="{{ $field }}_mobile_agree" required>
                                                <span>Agree</span>
                                            </label>
                                            <label class="sqd-option" for="{{ $field }}_mobile_strongly_agree">
                                                <input type="radio" name="{{ $field }}" value="strongly_agree" id="{{ $field }}_mobile_strongly_agree" required>
                                                <span>Strongly Agree</span>
                                            </label>
                                            <label class="sqd-option" for="{{ $field }}_mobile_not_applicable">
                                                <input type="radio" name="{{ $field }}" value="not_applicable" id="{{ $field }}_mobile_not_applicable" required>
                                                <span>N/A - Not Applicable</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="suggestions" class="form-label fw-medium" style="color: var(--text-primary);">
                                Suggestions on how we can further improve our services <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <textarea class="form-control" id="suggestions" name="suggestions" rows="5"
                                placeholder="Enter your suggestions here..."></textarea>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('student.feedback.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-send me-1"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .feedback-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .feedback-header h1 {
        font-size: 1.75rem;
    }

    .appointment-summary .card-body {
        padding: 1rem;
    }

    /* SQD Section */
    .sqd-section {
        margin-top: 1.5rem;
    }

    .sqd-title {
        color: var(--text-primary);
        font-size: 1.25rem;
        font-weight: 700;
        border-bottom: 2px solid var(--medium-blue);
        padding-bottom: 0.5rem;
    }

    .sqd-instruction {
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .cc-question {
        border: 0;
        margin: 0 0 1.25rem;
        padding: 0 0 1.25rem;
        border-bottom: 1px solid var(--border-color);
    }

    .cc-question:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .cc-question legend {
        color: var(--text-primary);
        float: none;
        width: 100%;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .cc-question .sqd-label {
        display: inline-block;
        min-width: 3.25rem;
        margin-right: 0.35rem;
        color: var(--medium-blue);
        font-weight: 700;
    }

    .cc-options {
        display: grid;
        gap: 0.5rem;
        margin-left: 3.6rem;
    }

    .cc-options-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .cc-option {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        min-height: 2.5rem;
        padding: 0.55rem 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        color: var(--text-primary);
        cursor: pointer;
        font-size: 0.9rem;
        line-height: 1.4;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .cc-option:hover,
    .cc-option:has(input:checked) {
        background: rgba(66, 158, 189, 0.1);
        border-color: var(--medium-blue);
    }

    .cc-option input[type="radio"] {
        width: 1.1rem;
        height: 1.1rem;
        flex: 0 0 auto;
        margin-top: 0.1rem;
        accent-color: var(--medium-blue);
    }

    .sqd-legend {
        font-size: 0.75rem;
    }

    .sqd-legend-item {
        color: var(--text-muted);
        font-size: 0.75rem;
    }

    .sqd-abbrev {
        font-weight: 600;
        color: var(--medium-blue);
        margin-right: 0.25rem;
    }

    /* Desktop Table */
    .sqd-table-responsive {
        overflow-x: auto;
    }

    .sqd-table {
        font-size: 0.875rem;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .sqd-table thead th {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 0.5rem;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .sqd-table td {
        padding: 0.75rem 0.5rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .sqd-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sqd-th-question {
        width: 40%!important;
        text-align: left;
    }

    .sqd-th-rating {
        width: 10%;
    }

    .sqd-td-question {
        width: 40%;
    }

    .sqd-td-rating {
        width: 10%;
    }

    .sqd-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--medium-blue);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.25rem;
    }

    .sqd-text {
        display: block;
        color: var(--text-primary);
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .sqd-radio-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .sqd-radio-label {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .sqd-radio-wrapper input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .sqd-radio-wrapper input[type="radio"]:checked + .sqd-radio-label {
        border-color: var(--medium-blue);
        background: var(--medium-blue);
    }

    .sqd-radio-wrapper input[type="radio"]:focus + .sqd-radio-label {
        border-color: var(--medium-blue);
        box-shadow: 0 0 0 3px rgba(66, 158, 189, 0.3);
    }

    .sqd-row:hover .sqd-radio-label {
        border-color: var(--medium-blue);
    }

    /* Mobile Card Layout */
    .sqd-mobile-card {
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1rem;
        background: var(--card-bg);
    }

    .sqd-mobile-header {
        margin-bottom: 0.75rem;
    }

    .sqd-mobile-question p {
        color: var(--text-primary);
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .sqd-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 44px;
    }

    .sqd-option:hover {
        background: rgba(66, 158, 189, 0.1);
        border-color: var(--medium-blue);
    }

    .sqd-option input[type="radio"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--medium-blue);
        margin: 0;
    }

    .sqd-option span {
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    /* Action buttons */
    .btn-primary {
        background: var(--medium-blue);
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        min-height: 44px;
        color: var(--badge-text-light);
    }

    .btn-primary:hover {
        background: var(--navy);
    }

    .btn-outline-secondary {
        border-radius: 0.5rem;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        min-height: 44px;
    }

    @media (max-width: 767.98px) {
        .cc-options,
        .cc-options-grid {
            grid-template-columns: 1fr;
            margin-left: 0;
        }

        .cc-question .sqd-label {
            display: block;
            margin-bottom: 0.25rem;
        }
    }
</style>

<script>
    document.getElementById('submitBtn').addEventListener('click', function(e) {
        const sqdFields = ['sqd0', 'sqd1', 'sqd2', 'sqd3', 'sqd4', 'sqd5', 'sqd6', 'sqd7', 'sqd8'];
        let allAnswered = true;
        let unansweredFields = [];

        sqdFields.forEach(function(field) {
            const radios = document.querySelectorAll('input[name="' + field + '"]');
            let answered = false;
            radios.forEach(function(radio) {
                if (radio.checked) {
                    answered = true;
                }
            });
            if (!answered) {
                allAnswered = false;
                unansweredFields.push(field);
            }
        });

        if (!allAnswered) {
            e.preventDefault();
            const errorDiv = document.getElementById('sqd-error');
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = 'Please answer all SQD questions before submitting your feedback. Unanswered: ' + unansweredFields.join(', ');
            window.location.hash = 'sqd-error';
        }
    });
</script>
@endsection

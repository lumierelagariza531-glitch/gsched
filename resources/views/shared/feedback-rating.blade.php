@php($rating = (float) $feedback->rating)
@if($feedback->rating === null)
    <p class="text-muted mb-0">No overall star rating was recorded for this feedback.</p>
@else
    <div class="d-flex align-items-center gap-2" aria-label="Student rating: {{ number_format($rating, 1) }} out of 5">
        <span class="d-inline-flex gap-1" aria-hidden="true">
            @for($star = 1; $star <= 5; $star++)
                @php($icon = $rating >= $star ? 'bi-star-fill' : ($rating >= $star - 0.5 ? 'bi-star-half' : 'bi-star'))
                <i class="bi {{ $icon }} fs-4" style="color: {{ $rating >= $star - 0.5 ? 'var(--yellow)' : 'var(--text-primary)' }}; opacity: {{ $rating >= $star - 0.5 ? '1' : '0.4' }};"></i>
            @endfor
        </span>
        <strong class="fs-5">{{ number_format($rating, 1) }}<span class="text-muted fs-6 fw-normal"> / 5.0</span></strong>
    </div>
@endif

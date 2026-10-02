@php
    $proofs = collect([
        'front' => ['label' => 'Front of student ID', 'path' => $studentIdProofUser->student_id_front],
        'back' => ['label' => 'Back of student ID', 'path' => $studentIdProofUser->student_id_back],
    ])->map(function ($proof, $side) use ($studentIdProofUser) {
        $expectedPath = '#\Astudent-id-proofs/' . $studentIdProofUser->id . '/' . $side . '\.(?:jpg|jpeg|png|webp)\z#D';
        $proof['available'] = is_string($proof['path'])
            && preg_match($expectedPath, $proof['path']) === 1
            && \Illuminate\Support\Facades\Storage::disk('local')->exists($proof['path']);

        return $proof;
    });
@endphp

<section class="mt-4" aria-labelledby="student-id-proof-title">
    <h3 class="h5 mb-2" id="student-id-proof-title">Proof of Student ID</h3>
    @if($proofs->contains('available', true))
        <p class="text-muted">Saved student ID images are available only through this secure account view.</p>
        <div class="row g-3">
            @foreach($proofs as $side => $proof)
                <div class="col-md-6">
                    <figure class="mb-0">
                        <figcaption class="form-label fw-semibold">{{ $proof['label'] }}</figcaption>
                        @if($proof['available'])
                            <img
                                src="{{ route($studentIdProofRouteName, [$studentIdProofRouteTarget, $side]) }}"
                                alt="{{ $proof['label'] }}"
                                class="img-fluid rounded border bg-light p-2"
                                style="width: 100%; max-height: 20rem; object-fit: contain;"
                            >
                        @else
                            <p class="text-muted mb-0">No {{ $side }} image saved.</p>
                        @endif
                    </figure>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted mb-0">No ID images saved.</p>
    @endif
</section>

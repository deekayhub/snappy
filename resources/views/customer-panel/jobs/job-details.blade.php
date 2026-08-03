@php
    $groupedDynamicFieldValues = $groupedDynamicFieldValues ?? [];
    $fields = $fields ?? collect();
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Job Title</div>
            <div class="fw-semibold">{{ $job->title ?: 'Not provided' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Category</div>
            <div class="fw-semibold">{{ $job->categoryId?->name ?? 'General' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Organisation</div>
            <div class="fw-semibold">{{ $job->organisation_name ?: 'Not provided' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Location</div>
            <div class="fw-semibold">{{ $job->location ?: 'Not provided' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Budget</div>
            <div class="fw-semibold">{{ $job->budget ? '£ '.number_format((float) $job->budget, 2) : 'Not shared' }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted">Needed By</div>
            <div class="fw-semibold">{{ $job->needed_by?->format('d M Y h:i A') ?? 'Not set' }}</div>
        </div>
    </div>
    <div class="col-12">
        <div class="border rounded-4 p-3 h-100">
            <div class="small text-muted mb-2">Description</div>
            <div class="fw-semibold">{{ $job->description ?: 'No description provided.' }}</div>
        </div>
    </div>
</div>

@if($fields && $fields->count())
    <div class="mt-4">
        <div class="fw-semibold mb-3">More details</div>

        @php
            $renderableItems = empty($groupedDynamicFieldValues) ? [[]] : $groupedDynamicFieldValues;
            $itemIndex = 0;
        @endphp

        @foreach($renderableItems as $itemNo => $itemValues)
            @php
                $itemIndex++;
                $itemNumber = is_int($itemNo) ? $itemNo : $itemIndex;
            @endphp
            <div class="border rounded-4 p-3 mb-3">
                <div class="badge bg-secondary rounded mb-3">#Item - {{ $itemNumber }}</div>
                <div class="row g-3">
                    @foreach($fields as $field)
                        @php
                            $fieldValue = $itemValues[$field->id] ?? null;
                            $rawValue = $fieldValue?->field_value;
                            $decodedValue = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;

                            if (json_last_error() !== JSON_ERROR_NONE && is_string($rawValue)) {
                                $decodedValue = $rawValue;
                            }

                            $displayValues = is_array($decodedValue) ? $decodedValue : ($decodedValue !== null && $decodedValue !== '' ? [$decodedValue] : []);
                        @endphp

                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 h-100">
                                <div class="small text-muted mb-2">
                                    {{ $field->field_label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </div>

                                @if(empty($displayValues))
                                    <div class="text-muted fst-italic">Not provided</div>
                                @elseif($field->field_type === 'file')
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($displayValues as $filePath)
                                            @php
                                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                            @endphp
                                            <div class="border rounded-3 p-2" style="max-width: 180px;">
                                                @if($isImage)
                                                    <img src="{{ asset($filePath) }}" alt="{{ $field->field_label }}" style="max-width: 160px; height: 110px; object-fit: cover;" class="rounded-3">
                                                @else
                                                    <a href="{{ asset($filePath) }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::afterLast($filePath, '/') }}</a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="fw-semibold">
                                        {{ implode(', ', array_map('strval', $displayValues)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif

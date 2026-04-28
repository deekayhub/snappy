@php
    $groupedDynamicFieldValues = $groupedDynamicFieldValues ?? [];
@endphp

<div class="row g-3">
    <div class="col-lg-12">
        <div class="border rounded-4 p-4 h-100">
            {{-- <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <div class="small text-muted">Job No. {{ str_pad((string) $job->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <h4 class="mb-1">{{ $job->title }}</h4>
                    <div class="text-muted">{{ ucfirst($job->categoryId?->name ?? 'General') }}</div>
                </div>
                <span class="badge bg-{{ $job->status === 'open' ? 'success' : 'secondary' }}">{{ ucfirst($job->status) }}</span>
            </div> --}}

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
        </div>
    </div>

    {{-- <div class="col-lg-4">
        <div class="border rounded-4 p-4 h-100">
            <div class="small text-muted mb-1">Supplier responses</div>
            <div class="display-6 fw-semibold mb-0">{{ $job->quotes->count() }}</div>
            <div class="text-muted">Quotes received so far</div>
        </div>
    </div> --}}

    @if(!empty($groupedDynamicFieldValues))
        <div class="col-12">
            <div class="border rounded-4 p-4">
                <div class="fw-semibold mb-3">More details</div>

                @foreach($groupedDynamicFieldValues as $itemNo => $itemValues)
                    <div class="border rounded-4 p-3 mb-3">
                        <div class="badge bg-secondary rounded mb-3">#Item - {{ $itemNo }}</div>
                        <div class="row g-3">
                            @foreach($itemValues as $fieldValue)
                                @php
                                    $field = $fieldValue->categoryFields;
                                    $rawValue = $fieldValue->field_value;
                                    $decodedValue = is_string($rawValue) ? json_decode($rawValue, true) : $rawValue;

                                    if (json_last_error() !== JSON_ERROR_NONE && is_string($rawValue)) {
                                        $decodedValue = $rawValue;
                                    }

                                    $displayValues = is_array($decodedValue) ? $decodedValue : ($decodedValue !== null ? [$decodedValue] : []);
                                @endphp

                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100">
                                        <div class="small text-muted mb-2">{{ $field?->field_label ?? 'Field' }}</div>

                                        @if(($field?->field_type ?? null) === 'file')
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($displayValues as $filePath)
                                                    @php
                                                        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                                    @endphp
                                                    <div class="border rounded-3 p-2" style="max-width: 180px;">
                                                        @if($isImage)
                                                            <img src="{{ asset($filePath) }}" alt="{{ $field?->field_label }}" style="max-width: 160px; height: 110px; object-fit: cover;" class="rounded-3">
                                                        @else
                                                            <a href="{{ asset($filePath) }}" target="_blank" rel="noopener">{{ \Illuminate\Support\Str::afterLast($filePath, '/') }}</a>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="fw-semibold">
                                                {{ is_array($displayValues) ? implode(', ', array_map('strval', $displayValues)) : (string) $rawValue }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

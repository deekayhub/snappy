@if($fields->count())
    @php
        $itemIndex = $itemIndex ?? 1;
        $itemValues = $itemValues ?? [];
    @endphp

    <div class="row m-0 border rounded pt-3 mb-3" data-item-index="{{ $itemIndex }}">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <div class="badge bg-secondary rounded">#Item - {{ $itemIndex }}</div>
        </div>

        @foreach($fields as $field)
            @php
                $required = $field->is_required ? 'required' : '';
                $baseName = "dynamic_fields[{$field->id}][{$itemIndex}]";
                $options = $field->field_options ? array_map('trim', explode(',', $field->field_options)) : [];
                $storedValue = $itemValues[$field->id] ?? null;
                $rawValue = $storedValue?->field_value;
                $decodedValue = null;

                if (is_string($rawValue)) {
                    $decodedValue = json_decode($rawValue, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $decodedValue = $rawValue;
                    }
                } elseif (is_array($rawValue)) {
                    $decodedValue = $rawValue;
                } else {
                    $decodedValue = $rawValue;
                }

                $currentValues = is_array($decodedValue) ? $decodedValue : ($decodedValue !== null ? [$decodedValue] : []);
                $currentValue = is_array($decodedValue) ? (count($decodedValue) ? reset($decodedValue) : null) : $decodedValue;
            @endphp

            <div class="col-md-6 mb-3">
                <label class="form-label mb-1">
                    {{ $field->field_label }}

                    @if($field->is_required)
                        <span class="text-danger">*</span>
                    @endif
                </label>

                @if($field->field_type == 'text')
                    <input type="text" name="{{ $baseName }}" class="form-control rounded" value="{{ $currentValue ?? '' }}" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'number')
                    <input type="number" name="{{ $baseName }}" class="form-control rounded" value="{{ $currentValue ?? '' }}" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'date')
                    <input type="date" name="{{ $baseName }}" class="form-control rounded" value="{{ $currentValue ?? '' }}" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'file')
                    <input type="file" name="{{ $baseName }}[]" class="form-control rounded" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }} multiple>
                    @if(!empty($currentValues))
                        <input type="hidden" name="dynamic_fields_existing[{{ $field->id }}][{{ $itemIndex }}]" value="{{ is_array($rawValue) ? e(json_encode($rawValue)) : e((string) $rawValue) }}">
                        <div class="mt-2">
                            <div class="small text-muted mb-2">Current files</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($currentValues as $filePath)
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
                        </div>
                    @endif
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'textarea')
                    <textarea name="{{ $baseName }}" rows="4" class="form-control rounded" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>{{ $currentValue ?? '' }}</textarea>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'select')
                    <select name="{{ $baseName }}" class="form-select text-dark" {{ $required }}>
                        <option value="">Select Option</option>
                        @foreach($options as $option)
                            <option value="{{ $option }}" @selected((string) $currentValue === (string) $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'radio')
                    <div>
                        @foreach($options as $option)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="{{ $baseName }}" value="{{ $option }}" {{ $required }} @checked((string) $currentValue === (string) $option)>
                                <label class="form-check-label">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'checkbox')
                    <div>
                        @foreach($options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="{{ $baseName }}[]" value="{{ $option }}" @checked(in_array((string) $option, array_map('strval', $currentValues), true))>
                                <label class="form-check-label">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">{{ $field->help_text ?? '' }}</div>
                @else
                    <input type="text" name="{{ $baseName }}" class="form-control rounded" value="{{ $currentValue ?? '' }}" {{ $required }}>
                @endif
            </div>
        @endforeach
    </div>
@endif

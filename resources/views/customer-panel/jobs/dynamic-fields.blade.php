@if($fields->count())
    
    <div class="row m-0 border rounded pt-3 mb-3">
        <div class="col-12 mb-3">
            <div class="badge bg-secondary rounded">#Item - 1</div>
        </div>
        @foreach($fields as $field)

            @php
                $required = $field->is_required ? 'required' : '';
                $fieldName = "dynamic_fields[{$field->id}][]";
                $options = $field->field_options ? explode(',', $field->field_options) : [];
            @endphp
            
            <div class="col-md-6 mb-3">
                <label class="form-label mb-1">
                    {{ $field->field_label }}

                    @if($field->is_required)
                        <span class="text-danger">*</span>
                    @endif
                </label>

                @if($field->field_type == 'text')
                    <input type="text" name="{{ $fieldName }}" class="form-control" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'number')
                    <input type="number" name="{{ $fieldName }}" class="form-control" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }} >
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>

                @elseif($field->field_type == 'date')
                    <input type="date" name="{{ $fieldName }}" class="form-control" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }}>
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'file')
                    <input type="file" name="{{ $fieldName }}" class="form-control" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }} >
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'textarea')
                    <textarea name="{{ $fieldName }}" rows="4" class="form-control" placeholder="{{ $field->placeholder ?? '' }}" {{ $required }} ></textarea>
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>
                @elseif($field->field_type == 'select')
                    <select name="{{ $fieldName }}" class="form-select text-dark" {{ $required }}>
                        <option value="">Select Option</option>
                        @foreach($options as $option)
                            <option value="{{ trim($option) }}">
                                {{ trim($option) }}
                            </option>
                        @endforeach
                    </select>
                    <div  class="form-text">{{ $field->help_text ?? '' }}</div>

                @elseif($field->field_type == 'radio')
                    <div>
                        @foreach($options as $option)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="{{ $fieldName }}" value="{{ trim($option) }}" {{ $required }}>
                                <label class="form-check-label">{{ trim($option) }}</label>
                            </div>
                        @endforeach
                    </div>

                @elseif($field->field_type == 'checkbox')
                    <div>
                        @foreach($options as $option)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="{{ $fieldName }}[]" value="{{ trim($option) }}">
                                <label class="form-check-label">{{ trim($option) }}</label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <input type="text" name="{{ $fieldName }}" class="form-control" {{ $required }}>
                @endif
            </div>
        @endforeach
    </div> 
@endif

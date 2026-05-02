@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
    <div class="content-wrapper p-3">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
            <h2>Add Dynamic Category field</h2>
            <button class="btn btn-primary rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                + Add New Field
            </button> 
        </div>
        <div class="collapse mb-3" id="collapseExample">
            <div class="card card-body">
                <form method="POST" action="{{ route('admin.category-fields.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Category</label>
                            <select name="category_id" class="form-control rounded" required>
                                <option value="">Choose Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ ucfirst($category->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Label</label>
                            <input type="text" name="field_label" class="form-control rounded" placeholder="Example: Material Type" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Type</label>
                            <select name="field_type" class="form-control rounded" required>
                                <option value="">Select Type</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="select">Select</option>
                                <option value="radio">Radio</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="file">File Upload</option>
                                <option value="date">Date</option>
                                <option value="time">Time</option>
                                <option value="url">URL</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Options<small>(comma separated)</small></label>
                            <input type="text" name="field_options" class="form-control rounded" placeholder="Wood, Metal, Glass">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placeholder</label>
                            <input type="text" name="placeholder" class="form-control rounded" placeholder="Enter placeholder">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Validation Rules</label>
                            <input type="text" name="validation_rules" class="form-control rounded" placeholder="required|max:255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Default Value</label>
                            <input type="text" name="default_value" class="form-control rounded" placeholder="Default Value">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control rounded" value="0">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Help Text</label>
                            <textarea name="help_text" class="form-control rounded" rows="3" placeholder="Helpful text for user"></textarea>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-check">                                
                                <label class="form-check-label" for="requiredField">
                                    <input type="checkbox" name="is_required" value="1" class="form-check-input rounded" id="requiredField" >
                                    Required Field
                                </label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary">
                                Save Field
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
         

        <div class="card shadow border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Dynamic Fields</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Field Label</th>
                                {{-- <th>Field Name</th> --}}
                                <th>Type</th>
                                <th>Required</th>
                                <th>Options</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $field)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ ucfirst($field->categoryId?->name) ?? 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $field->field_label }}
                                    </td>
                                    {{-- <td>
                                        <code>{{ $field->field_name }}</code>
                                    </td> --}}
                                    <td>
                                        <span class="badge bg-info rounded">
                                            {{ ucfirst($field->field_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge bg-success rounded">
                                                Yes
                                            </span>
                                        @else
                                            <span class="badge bg-secondary rounded">
                                                No
                                            </span>
                                        @endif
                                    </td>
                                    <td style="max-width: 250px;">
                                        {{ $field->field_options ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $field->sort_order }}
                                    </td>
                                    <td>
                                        @if($field->status)
                                            <span class="badge bg-success rounded">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-button" data-id="{{ $field->id }}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-button" data-id="{{ $field->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        No fields found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $fields->links() }}
                </div>

            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).on('click', '.edit-button', function () {
            let id = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.categories.fields.edit', ':id') }}".replace(':id', id),
                type: "GET",
                beforeSend: function () {
                    Swal.fire({
                        title: 'Loading...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close();

                    let collapseElement = document.getElementById('collapseExample');

                    if (!collapseElement.classList.contains('show')) {
                        let bsCollapse = new bootstrap.Collapse(collapseElement, {
                            show: true
                        });
                    }

                    $('select[name="category_id"]').val(response.data.category_id);
                    $('input[name="field_label"]').val(response.data.field_label);
                    $('select[name="field_type"]').val(response.data.field_type);
                    $('input[name="field_options"]').val(response.data.field_options);
                    $('input[name="placeholder"]').val(response.data.placeholder);
                    $('input[name="validation_rules"]').val(response.data.validation_rules);
                    $('input[name="default_value"]').val(response.data.default_value);
                    $('input[name="sort_order"]').val(response.data.sort_order);
                    $('textarea[name="help_text"]').val(response.data.help_text);

                    $('input[name="is_required"]').prop('checked', response.data.is_required == 1);

                    $('form').attr(
                        'action',
                        "{{ route('admin.categories.fields.update', ':id') }}".replace(':id', id)
                    );

                    if ($('input[name="_method"]').length === 0) {
                        $('form').append('<input type="hidden" name="_method" value="PUT">');
                    } else {
                        $('input[name="_method"]').val('PUT');
                    }

                    $('button.btn-primary').text('Update Field');
                    setTimeout(function () {
                        collapseElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 300);
                },
                error: function () {
                    alert('Failed to fetch field data.');
                }
            });
        });


        $(document).on('click', '.delete-button', function () {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This field will be deleted permanently.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.categories.fields.destroy', ':id') }}".replace(':id', id),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Field deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: 'Failed to delete field.'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong while deleting.'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
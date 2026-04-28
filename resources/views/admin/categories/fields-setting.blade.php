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
                            <select name="category_id" class="form-control" required>
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
                            <input type="text" name="field_label" class="form-control" placeholder="Example: Material Type" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Field Type</label>
                            <select name="field_type" class="form-control" required>
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
                            <input type="text" name="field_options" class="form-control" placeholder="Wood, Metal, Glass">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placeholder</label>
                            <input type="text" name="placeholder" class="form-control" placeholder="Enter placeholder">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Validation Rules</label>
                            <input type="text" name="validation_rules" class="form-control" placeholder="required|max:255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Default Value</label>
                            <input type="text" name="default_value" class="form-control" placeholder="Default Value">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Help Text</label>
                            <textarea name="help_text" class="form-control" rows="3" placeholder="Helpful text for user"></textarea>
                        </div>

                        <div class="col-md-12 mb-4">
                            <div class="form-check">                                
                                <label class="form-check-label" for="requiredField">
                                    <input type="checkbox" name="is_required" value="1" class="form-check-input" id="requiredField" >
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
                                <th>Field Name</th>
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
                                        {{ $field->categoryId?->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $field->field_label }}
                                    </td>
                                    <td>
                                        <code>{{ $field->field_name }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($field->field_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($field->is_required)
                                            <span class="badge bg-success">
                                                Yes
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
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
                                            <span class="badge bg-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#"
                                        class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <a href="#"
                                        class="btn btn-sm btn-danger">
                                            Delete
                                        </a>
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
@extends('admin.layouts.app')
@section('title', 'Categories')

@section('content')
<div class="content-wrapper p-3">
    @if (session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="mb-3">Create category</h4>
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Category name</label>
                                <input type="text" name="name" class="form-control rounded-4" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select rounded-4 text-dark" required>
                                    <option value="supplier">Supplier</option>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary rounded-4">Save category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h4 class="mb-4">Manage categories</h4>
                    @foreach (['supplier' => 'Supplier Categories', 'customer' => 'Customer Categories'] as $type => $title)
                        <h5 class="mb-3">{{ $title }}</h5>
                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse (($categories[$type] ?? collect()) as $category)
                                        <tr>
                                            <td>
                                                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="d-flex gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="text" name="name" value="{{ $category->name }}" class="form-control rounded-4" required>
                                            </td>
                                            <td>
                                                    <select name="type" class="form-select rounded-4 text-dark" required>
                                                        <option value="supplier" @selected($category->type === 'supplier')>Supplier</option>
                                                        <option value="customer" @selected($category->type === 'customer')>Customer</option>
                                                    </select>
                                            </td>
                                            <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary rounded-4">Update</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline-block ms-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger rounded-4" onclick="return confirm('Delete this category?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">No categories yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryField;
use App\Models\OrganisationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryFieldController extends Controller
{
    public function index()
    { 
        $categories = OrganisationCategory::where('type', 'supplier')->get();
        $fields = CategoryField::with('categoryId')
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->latest()
            ->paginate(20);

        return view('admin.categories.fields-setting', compact('categories', 'fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'field_label' => 'required',
            'field_type' => 'required',
        ]);

        CategoryField::create([
            'category_id' => $request->category_id,
            'field_label' => $request->field_label,
            'field_name' => Str::slug($request->field_label, '_'),
            'field_type' => $request->field_type,
            'field_options' => $request->field_options,
            'is_required' => $request->is_required ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Field Created Successfully');
    }
}

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
            'placeholder' => $request->placeholder,
            'help_text' => $request->help_text,
            'is_required' => $request->is_required ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Field Created Successfully');
    }

    public function edit($id)
    {
        $field = CategoryField::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $field
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'field_label' => 'required',
            'field_type' => 'required',
        ]);

        $field = CategoryField::findOrFail($id);

        $field->update([
            'category_id' => $request->category_id,
            'field_label' => $request->field_label,
            'field_name' => Str::slug($request->field_label, '_'),
            'field_type' => $request->field_type,
            'field_options' => $request->field_options,
            'placeholder' => $request->placeholder,
            'help_text' => $request->help_text,
            'is_required' => $request->is_required ?? 0,
            'sort_order' => $request->sort_order ?? 0,
            'status' => 1,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Field Updated Successfully');
    }

    public function destroy($id)
    {
        $field = CategoryField::findOrFail($id);

        $field->delete();

        return response()->json([
            'success' => true,
            'message' => 'Field Deleted Successfully'
        ]);
    }

    public function duplicate(Request $request)
    {
        $request->validate([
            'source_category_id' => 'required|exists:organisation_categories,id',
            'target_category_ids' => 'required|array',
            'target_category_ids.*' => 'exists:organisation_categories,id',
        ]);

        $sourceFields = CategoryField::where('category_id', $request->source_category_id)->get();

        if ($sourceFields->isEmpty()) {
            return redirect()->back()->with('error', 'Source category has no fields to duplicate.');
        }

        foreach ($request->target_category_ids as $targetId) {
            if ((int) $targetId === (int) $request->source_category_id) continue;

            foreach ($sourceFields as $field) {
                CategoryField::create([
                    'category_id' => $targetId,
                    'field_label' => $field->field_label,
                    'field_name' => $field->field_name,
                    'field_type' => $field->field_type,
                    'field_options' => $field->field_options,
                    'placeholder' => $field->placeholder,
                    'help_text' => $field->help_text,
                    'is_required' => $field->is_required,
                    'sort_order' => $field->sort_order,
                    'status' => $field->status,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Fields duplicated successfully.');
    }
}

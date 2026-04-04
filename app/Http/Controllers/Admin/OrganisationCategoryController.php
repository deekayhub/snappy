<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganisationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganisationCategoryController extends Controller
{
    public function index(): View
    {
        $categories = OrganisationCategory::query()
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:customer,supplier'],
        ]);

        OrganisationCategory::create($validated);

        return back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, OrganisationCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:customer,supplier'],
        ]);

        $category->update($validated);

        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(OrganisationCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}

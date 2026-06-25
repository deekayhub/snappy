<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::ordered()->get();
        return view('admin.features.index', compact('features'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', 'unique:features,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'], '_');
        $validated['is_active'] ??= true;
        $validated['sort_order'] ??= 0;

        $feature = Feature::create($validated);

        return response()->json([
            'message' => 'Feature created successfully.',
            'feature' => $this->formatFeature($feature),
        ], 201);
    }

    public function update(Request $request, Feature $feature): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', Rule::unique('features', 'slug')->ignore($feature->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'], '_');
        $validated['is_active'] ??= false;
        $validated['sort_order'] ??= 0;

        $feature->update($validated);

        return response()->json([
            'message' => 'Feature updated successfully.',
            'feature' => $this->formatFeature($feature->fresh()),
        ]);
    }

    public function destroy(Feature $feature): JsonResponse
    {
        $feature->delete();

        return response()->json([
            'message' => 'Feature deleted successfully.',
        ]);
    }

    private function formatFeature(Feature $feature): array
    {
        return [
            'id'          => $feature->id,
            'name'        => $feature->name,
            'slug'        => $feature->slug,
            'description' => $feature->description,
            'is_active'   => $feature->is_active,
            'sort_order'  => $feature->sort_order,
        ];
    }
}

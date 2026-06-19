<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionSettingController extends Controller
{
    public function index(Request $request)
    {
        // $plans = Plan::active()->ordered()->get();
        $plans = Plan::active()->get();

        return view('admin.subscription.index', compact('plans'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', 'unique:plans,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'duration'    => ['nullable', 'string', Rule::in(['monthly', '3_months', '6_months', 'yearly', 'lifetime'])],
            'is_free'     => ['boolean'],
            'is_popular'  => ['boolean'],
            'features'    => ['nullable', 'array'],
            'features.*'  => ['string', 'max:200'],
        ]);

        $validated['slug']     = Str::slug($validated['slug'], '_');
        $validated['is_free']  = (bool) ($validated['is_free']  ?? false);
        $validated['price']    = $validated['is_free'] ? 0 : (int) (($validated['price'] ?? 0) * 100);
        $validated['features'] = $validated['features'] ?? [];

        $plan = Plan::create($validated);

        Artisan::call('stripe:sync-plans');

        return response()->json([
            'message' => 'Plan created and synced with Stripe.',
            'plan'    => $this->formatPlan($plan),
        ], 201);
    }

    public function update(Request $request, Plan $plan): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['required', 'string', 'max:100', Rule::unique('plans', 'slug')->ignore($plan->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'duration'    => ['nullable', 'string', Rule::in(['monthly', '3_months', '6_months', 'yearly', 'lifetime'])],
            'is_free'     => ['boolean'],
            'is_popular'  => ['boolean'],
            'features'    => ['nullable', 'array'],
            'features.*'  => ['string', 'max:200'],
        ]);

        $validated['slug']     = Str::slug($validated['slug'], '_');
        $validated['is_free']  = (bool) ($validated['is_free']  ?? false);
        $validated['price']    = $validated['is_free'] ? 0 : (int) (($validated['price'] ?? 0) * 100);
        $validated['features'] = $validated['features'] ?? [];

        if (! empty($validated['is_popular'])) {
            Plan::where('id', '!=', $plan->id)->update(['is_popular' => false]);
        }

        $plan->update($validated);

        Artisan::call('stripe:sync-plans');

        return response()->json([
            'message' => 'Plan updated and synced with Stripe.',
            'plan'    => $this->formatPlan($plan->fresh()),
        ]);
    }

    public function destroy(Plan $plan): JsonResponse
    {
        $plan->delete();

        Artisan::call('stripe:sync-plans');

        return response()->json([
            'message' => 'Plan deleted and Stripe synced.',
        ]);
    }

    private function formatPlan(Plan $plan): array
    {
        return [
            'id'              => $plan->id,
            'name'            => $plan->name,
            'slug'            => $plan->slug,
            'description'     => $plan->description,
            'price'           => $plan->price,
            'price_formatted' => $plan->price_formatted,
            'duration'        => $plan->duration,
            'duration_label'  => $plan->duration_label,
            'is_free'         => $plan->is_free,
            'is_popular'      => $plan->is_popular,
            'features'        => $plan->features ?? [],
        ];
    }
}

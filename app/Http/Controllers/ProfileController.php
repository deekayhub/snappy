<?php

namespace App\Http\Controllers;

use App\Models\OrganisationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $organisation = OrganisationCategory::orderBy('type')->orderBy('name')->get();
        $user = $request->user()->load(['customerProfile', 'supplierProfile', 'organisationCategories']);

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return redirect()->route('admin.profile');
        }

        if ($user->hasRole('supplier')) {
            return redirect()->route('supplier-panel.profile');
        }

        return view('profile.edit', [
            'user' => $user,
            'organisation' => $organisation,
        ]);
    }

    public function adminEdit(Request $request): View
    {
        $organisation = OrganisationCategory::orderBy('type')->orderBy('name')->get();
        $user = $request->user()->load(['customerProfile', 'supplierProfile', 'organisationCategories']);

        return view('admin.profile.index', [
            'user' => $user,
            'organisation' => $organisation,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    // public function update(ProfileUpdateRequest $request): RedirectResponse
    // {
    //     $request->user()->fill($request->validated());

    //     if ($request->user()->isDirty('email')) {
    //         $request->user()->email_verified_at = null;
    //     }

    //     $request->user()->save();

    //     return Redirect::route('profile.edit')->with('status', 'profile-updated');
    // }


    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
        ];

        if ($user->hasRole('customer')) {
            $rules = array_merge($rules, [
                'customer_organisation' => 'required|array|min:1',
                'customer_organisation.*' => [
                    'integer',
                    Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
                ],
                'county' => 'nullable|string|max:255',
                'school_name' => 'nullable|string|max:255',
            ]);
        }

        if ($user->hasRole('supplier')) {
            $rules = array_merge($rules, [
                'supplier_organisation' => 'required|array|min:1',
                'supplier_organisation.*' => [
                    'integer',
                    Rule::exists('organisation_categories', 'id')->where('type', 'supplier'),
                ],
                'company_name' => 'required|string|max:255',
                'address' => 'required|string',
                'website' => 'nullable|url',
                'review_link' => 'nullable|url',
                'social_link' => 'nullable|url',
            ]);
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
            ]);

            if ($user->hasRole('customer')) {
                $user->customerProfile()->updateOrCreate([], [
                    'county' => $validated['county'] ?? null,
                    'school_name' => $validated['school_name'] ?? null,
                ]);

                $this->syncOrganisationCategories($user, 'customer', $validated['customer_organisation'] ?? []);
            }

            if ($user->hasRole('supplier')) {
                $user->supplierProfile()->updateOrCreate([], [
                    'company_name' => $validated['company_name'],
                    'address' => $validated['address'],
                    'website' => $validated['website'] ?? null,
                    'review_link' => $validated['review_link'] ?? null,
                    'social_link' => $validated['social_link'] ?? null,
                ]);

                $this->syncOrganisationCategories($user, 'supplier', $validated['supplier_organisation'] ?? []);
            }
        });

        return back()->with('success', 'Profile updated successfully!');
    }

    protected function syncOrganisationCategories($user, string $type, array $selectedIds): void
    {
        $typeCategoryIds = OrganisationCategory::where('type', $type)->pluck('id');
        $existingTypeIds = $user->organisationCategories()
            ->whereIn('organisation_categories.id', $typeCategoryIds)
            ->pluck('organisation_categories.id');

        $idsToDetach = $existingTypeIds->diff($selectedIds)->all();

        if (! empty($idsToDetach)) {
            $user->organisationCategories()->detach($idsToDetach);
        }

        $user->organisationCategories()->syncWithoutDetaching($selectedIds);
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

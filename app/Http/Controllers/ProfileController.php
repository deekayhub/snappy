<?php

namespace App\Http\Controllers;

use App\Models\OrganisationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
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
                'company_logo' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:20480',
                'company_description' => 'nullable|string|max:5000',
                'social_links' => 'nullable|array',
                'social_links.*.platform' => 'required_with:social_links.*.url|string|in:facebook,instagram,youtube,linkedin,x,tiktok,other',
                'social_links.*.url' => 'nullable|url',
            ]);
        }

        $request->merge([
            'website' => $this->formatUrl($request->input('website')),
            'review_link' => $this->formatUrl($request->input('review_link')),
            'social_link' => $this->formatUrl($request->input('social_link')),
            'social_links' => $this->normalizeSupplierSocialLinks($request->input('social_links')),
        ]);

        $validated = $request->validate($rules);

        DB::transaction(function () use ($user, $validated, $request) {
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
                $profile = $user->supplierProfile;
                $companyLogoPath = $profile?->company_logo;

                if ($request->hasFile('company_logo')) {
                    if ($companyLogoPath) {
                        Storage::disk('public')->delete($companyLogoPath);
                    }
                    $companyLogoPath = $request->file('company_logo')->store('supplier-logos', 'public');
                }

                $socialLinks = $validated['social_links'] ?? [];
                $firstSocialLink = ! empty($socialLinks)
                    ? ($socialLinks[0]['url'] ?? null)
                    : ($validated['social_link'] ?? null);

                $user->supplierProfile()->updateOrCreate([], [
                    'company_name' => $validated['company_name'],
                    'company_logo' => $companyLogoPath,
                    'address' => $validated['address'],
                    'company_description' => $validated['company_description'] ?? null,
                    'website' => $validated['website'] ?? null,
                    'review_link' => $validated['review_link'] ?? null,
                    'social_link' => $firstSocialLink,
                    'social_links' => ! empty($socialLinks) ? $socialLinks : null,
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

    private function formatUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (! preg_match('~^(?:f|ht)tps?://~i', $url)) {
            return 'https://'.$url;
        }

        return $url;
    }

    private function normalizeSupplierSocialLinks($links): array
    {
        if (! is_array($links)) {
            return [];
        }

        $normalized = [];

        foreach ($links as $link) {
            if (! is_array($link)) {
                continue;
            }

            $platform = isset($link['platform']) ? trim((string) $link['platform']) : '';
            $url = isset($link['url']) ? $this->formatUrl(trim((string) $link['url'])) : null;

            if (! $url) {
                continue;
            }

            $normalized[] = [
                'platform' => $platform ?: 'other',
                'url' => $url,
            ];
        }

        return $normalized;
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

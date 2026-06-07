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
use Illuminate\Support\Str;

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


    // public function update(Request $request): RedirectResponse
    // {
    //     $user = Auth::user();

    //     $rules = [
    //         'name' => 'required|string|max:255',
    //         'phone' => 'nullable|string|max:30',
    //     ];

    //     if ($user->hasRole('customer')) {
    //         $rules = array_merge($rules, [
    //             'customer_organisation' => 'required|array|min:1',
    //             'customer_organisation.*' => [
    //                 'integer',
    //                 Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
    //             ],
    //             'county' => 'nullable|string|max:255',
    //             'school_name' => 'nullable|string|max:255',
    //         ]);
    //     }

    //     if ($user->hasRole('supplier')) {
    //         $rules = array_merge($rules, [
    //             'supplier_organisation' => 'required|array|min:1',
    //             'supplier_organisation.*' => [
    //                 'integer',
    //                 Rule::exists('organisation_categories', 'id')->where('type', 'supplier'),
    //             ],
    //             'company_name' => 'required|string|max:255',
    //             'address' => 'required|string',
    //             'website' => 'nullable|url',
    //             'review_link' => 'nullable|url',
    //             'social_link' => 'nullable|url',
    //             'company_logo' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif|max:2048',
    //             'company_description' => 'nullable|string|max:5000',
    //             'social_links' => 'nullable|array',
    //             'social_links.*.platform' => 'required_with:social_links.*.url|string|in:facebook,instagram,youtube,linkedin,x,tiktok,other',
    //             'social_links.*.url' => 'nullable|url',
    //         ]);
    //     }

    //     $request->merge([
    //         'website' => $this->formatUrl($request->input('website')),
    //         'review_link' => $this->formatUrl($request->input('review_link')),
    //         'social_link' => $this->formatUrl($request->input('social_link')),
    //         'social_links' => $this->normalizeSupplierSocialLinks($request->input('social_links')),
    //     ]);

    //     $validated = $request->validate($rules);

    //     DB::transaction(function () use ($user, $validated, $request) {
    //         $user->update([
    //             'name' => $validated['name'],
    //             'phone' => $validated['phone'] ?? null,
    //         ]);

    //         if ($user->hasRole('customer')) {
    //             $user->customerProfile()->updateOrCreate([], [
    //                 'county' => $validated['county'] ?? null,
    //                 'school_name' => $validated['school_name'] ?? null,
    //             ]);

    //             $this->syncOrganisationCategories($user, 'customer', $validated['customer_organisation'] ?? []);
    //         }

    //         if ($user->hasRole('supplier')) {
    //             $profile = $user->supplierProfile;
    //             $companyLogoPath = $profile?->company_logo;

    //             if ($request->hasFile('company_logo')) {
    //                 if ($companyLogoPath) {
    //                     Storage::disk('public')->delete($companyLogoPath);
    //                 }
    //                 $companyLogoPath = $request->file('company_logo')->store('supplier-logos', 'public');
    //             }

    //             $socialLinks = $validated['social_links'] ?? [];
    //             $firstSocialLink = ! empty($socialLinks)
    //                 ? ($socialLinks[0]['url'] ?? null)
    //                 : ($validated['social_link'] ?? null);

    //             $user->supplierProfile()->updateOrCreate([], [
    //                 'company_name' => $validated['company_name'],
    //                 'company_logo' => $companyLogoPath,
    //                 'address' => $validated['address'],
    //                 'company_description' => $validated['company_description'] ?? null,
    //                 'website' => $validated['website'] ?? null,
    //                 'review_link' => $validated['review_link'] ?? null,
    //                 'social_link' => $firstSocialLink,
    //                 'social_links' => ! empty($socialLinks) ? $socialLinks : null,
    //             ]);

    //             $this->syncOrganisationCategories($user, 'supplier', $validated['supplier_organisation'] ?? []);
    //         }
    //     });

    //     return back()->with('success', 'Profile updated successfully!');
    // }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        try {

            // Common update
            $this->updateBasicProfile($user, $request);

            if ($user->hasRole('customer')) {
                $this->updateCustomer($user, $request);
            }

            if ($user->hasRole('supplier')) {
                $this->updateSupplier($user, $request);
            }

            return back()->with('success', 'Profile updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function updateBasicProfile($user, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'profile_picture' => 'nullable|file|mimetypes:image/jpeg,image/png|max:10240',
        ], [
            'name.required' => 'Name is required',
            'profile_picture.mimetypes' => 'Profile picture must be a JPG, JPEG, or PNG image.',
            'profile_picture.max' => 'Profile picture must not be larger than 10 MB.',
        ]);

        $profilePicturePath = $user->profile_picture;

        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $this->storeProfilePicture(
                $request->file('profile_picture'),
                $user->profile_picture
            );
        }

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'profile_picture' => $profilePicturePath,
        ]);
    }

    private function updateSupplier($user, Request $request)
    {
        try {
            DB::transaction(function () use ($user, $request) {

                $request->validate([
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

                    'company_logo' => 'nullable|file|max:20480|mimetypes:image/jpeg,image/png,image/webp,image/heic,image/heif',

                    'company_description' => 'nullable|string|max:5000',

                    'social_links' => 'nullable|array',
                    'social_links.*.platform' => 'required_with:social_links.*.url|in:facebook,instagram,youtube,linkedin,x,tiktok,other',
                    'social_links.*.url' => 'nullable|url',
                ], [
                    'supplier_organisation.required' => 'Please select at least one category',
                    'company_name.required' => 'Company name is required',
                    'address.required' => 'Address is required',
                    'company_logo.max' => 'Logo must be less than 2MB',
                ]);

                $profile = $user->supplierProfile;
                $companyLogoPath = $profile?->company_logo;

                // ✅ Handle file upload to public folder
                if ($request->hasFile('company_logo')) {

                    // delete old file if exists
                    if ($companyLogoPath && file_exists(public_path($companyLogoPath))) {
                        unlink(public_path($companyLogoPath));
                    }

                    $file = $request->file('company_logo');
                    $filename = time() . '_' . $file->getClientOriginalName();

                    // move file to public/supplier-logos
                    $file->move(public_path('supplier-logos'), $filename);

                    $companyLogoPath = 'supplier-logos/' . $filename;
                }

                // format links
                $website = $this->formatUrl($request->website ?? null);
                $reviewLink = $this->formatUrl($request->review_link ?? null);
                $socialLink = $this->formatUrl($request->social_link ?? null);

                $socialLinks = $this->normalizeSupplierSocialLinks($request->social_links ?? []);

                $firstSocialLink = !empty($socialLinks)
                    ? ($socialLinks[0]['url'] ?? null)
                    : $socialLink;

                // save/update profile
                $user->supplierProfile()->updateOrCreate([], [
                    'company_name' => $request->company_name,
                    'company_logo' => $companyLogoPath,
                    'address' => $request->address,
                    'company_description' => $request->company_description ?? null,
                    'website' => $website,
                    'review_link' => $reviewLink,
                    'social_link' => $firstSocialLink,
                    'social_links' => !empty($socialLinks) ? $socialLinks : null,
                ]);

                // sync categories
                $this->syncOrganisationCategories(
                    $user,
                    'supplier',
                    $request->supplier_organisation
                );
            });

        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }

    private function updateCustomer($user, Request $request)
    {
        
        $validated = $request->validate([
            'customer_organisation' => 'required|array|min:1',
            'customer_organisation.*' => [
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
            ],
            'county' => 'nullable|string|max:255',
            'school_name' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($user, $validated) {

            $user->customerProfile()->updateOrCreate([], [
                'county' => $validated['county'] ?? null,
                'school_name' => $validated['school_name'] ?? null,
            ]);

            $this->syncOrganisationCategories(
                $user,
                'customer',
                $validated['customer_organisation']
            );
        });
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

    private function storeProfilePicture(\Illuminate\Http\UploadedFile $file, ?string $currentPath = null): string
    {
        $directory = public_path('profile-pictures');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($currentPath && file_exists(public_path($currentPath))) {
            unlink(public_path($currentPath));
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::uuid()->toString() . '.' . $extension;
        $file->move($directory, $filename);

        return 'profile-pictures/' . $filename;
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\OrganisationCategory;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $organisation = OrganisationCategory::all();
        $user = User::with('organisations')->find(Auth::id());
        // dd($user->toArray());
        return view('profile.edit', [
            'user' => $user,
            'organisation' => $organisation
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


    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'organisation' => 'required|array',
            'organisation.*' => 'exists:organisation_categories,id',
            'county' => 'nullable|string',
            'school_name' => 'nullable|string',
        ];

        if ($user->role === 'supplier') {
            $rules = array_merge($rules, [
                'company_name' => 'required|string|max:255',
                'address' => 'required|string',
                'website' => 'nullable|url',
                'review_link' => 'nullable|url',
                'social_link' => 'nullable|url',
            ]);
        }

        $validated = $request->validate($rules);

        // update user
        $user->update($validated);

        // update pivot table
        if ($request->organisation) {

            $orgData = [];

            foreach ($request->organisation as $orgId) {
                $orgData[$orgId] = ['type' => $user->role];
            }

            $user->organisations()->sync($orgData);
        }

        return back()->with('success', 'Profile updated successfully!');
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

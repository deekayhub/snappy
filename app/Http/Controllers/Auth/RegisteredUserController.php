<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }


    public function createCustomer()
    {
        $organisation = OrganisationCategory::where('type', 'customer')->get();
        return view('auth.register-customer', compact('organisation'));
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'phone' => 'nullable|string|max:30',
            'organisation' => 'required|array',
            'organisation.*' => [
                'required',
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
            ],
            'county' => 'nullable|string',
            'school_name' => 'nullable|string',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('customer');
            $user->customerProfile()->create([
                'county' => $validated['county'] ?? null,
                'school_name' => $validated['school_name'] ?? null,
            ]);
            $user->organisationCategories()->sync($validated['organisation']);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('customer.jobs.create', absolute: false));
    }


    public function createSupplier()
    {
        $organisation = OrganisationCategory::where('type', 'supplier')->get();
        return view('auth.register-supplier', compact('organisation'));
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'phone' => 'nullable|string|max:30',
            'organisation' => 'required|array',
            'organisation.*' => [
                'required',
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'supplier'),
            ],
            'address' => 'required|string',
            'website' => 'nullable|url',
            'review_link' => 'nullable|url',
            'social_link' => 'nullable|url',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('supplier');
            $user->supplierProfile()->create([
                'company_name' => $validated['company_name'],
                'address' => $validated['address'],
                'website' => $validated['website'] ?? null,
                'review_link' => $validated['review_link'] ?? null,
                'social_link' => $validated['social_link'] ?? null,
            ]);
            $user->organisationCategories()->sync($validated['organisation']);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }

}

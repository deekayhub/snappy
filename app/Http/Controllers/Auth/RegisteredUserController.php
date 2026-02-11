<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OrganisationCategory;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        return redirect(route('home', absolute: false));
    }


    public function createCustomer()
    {
        $organisation = OrganisationCategory::where('type', 'customer')->get();
        return view('auth.register-customer', compact('organisation'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'phone' => 'nullable',
            'organisation' => 'required|array',
            'organisation.*' => 'required|string|exists:organisation_categories,id',
            'county' => 'required|string',
            'school_name' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'customer',
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('organisation')) {
            $user->organisations()->attach($request->organisation, [
                'type' => 'customer'
            ]);
        }

        Auth::login($user);
        // return redirect()->dashboard();
        return redirect(route('home', absolute: false));
    }


    public function createSupplier()
    {
        $organisation = OrganisationCategory::where('type', 'supplier')->get();
        return view('auth.register-supplier', compact('organisation'));
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'organisation' => 'required|array',
            'organisation.*' => 'required|string|exists:organisation_categories,id',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'review_link' => 'nullable|url',
            'social_link' => 'nullable|url',
        ]);


        $user = User::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
            'review_link' => $request->review_link,
            'social_link' => $request->social_link,
            'role' => 'supplier',
            'password' => Hash::make($request->password),
        ]);
        if ($request->has('organisation')) {
            $user->organisations()->attach($request->organisation, [
                'type' => 'supplier'
            ]);
        }
        Auth::login($user);
        // return redirect()->dashboard();
        return redirect(route('home', absolute: false));
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewUserRegisteredNotifyMail;
use App\Mail\WelcomeCustomerMail;
use App\Mail\WelcomeSupplierMail;
use App\Models\OrganisationCategory;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'organisation' => 'required',
            'organisation.*' => [
                'required',
                'integer',
                Rule::exists('organisation_categories', 'id')->where('type', 'customer'),
            ],
            'county' => 'nullable|string',
            'school_name' => 'nullable|string',
        ],[
            'organisation.required' => 'Please select organisation.',
            'organisation.*.exists' => 'The selected organisation category is invalid.',
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

        try {
            Mail::to($user->email)->send(new WelcomeCustomerMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email to customer.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $notifyEmail = config('app.notify_email');
        if ($notifyEmail) {
            try {
                Mail::to($notifyEmail)->send(new NewUserRegisteredNotifyMail($user, 'customer'));
            } catch (\Throwable $e) {
                Log::error('Failed to send new registration notification.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->intended(route('customer-panel.dashboard', absolute: false));
    }


    public function createSupplier()
    {
        $organisation = OrganisationCategory::where('type', 'supplier')->get();
        return view('auth.register-supplier', compact('organisation'));
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'website' => $this->formatUrl($this->website),
            'review_link' => $this->formatUrl($this->review_link),
            'social_link' => $this->formatUrl($this->social_link),
        ]);
    }

    private function formatUrl($url)
    {
        if (!$url) return null;

        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            return 'https://' . $url;
        }

        return $url;
    }
    public function storeSupplier(Request $request)
    {
        $request->merge([
            'website' => $this->formatUrl($request->website),
            'review_link' => $this->formatUrl($request->review_link),
            'social_link' => $this->formatUrl($request->social_link),
        ]);
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
        ],[
            'organisation.required' => 'Please select at least one organisation.',
            'organisation.*.exists' => 'The selected organisation category is invalid.',
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

        try {
            Mail::to($user->email)->send(new WelcomeSupplierMail($user));
        } catch (\Throwable $e) {
            Log::error('Failed to send welcome email to supplier.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $notifyEmail = config('app.notify_email');
        if ($notifyEmail) {
            try {
                Mail::to($notifyEmail)->send(new NewUserRegisteredNotifyMail($user, 'supplier'));
            } catch (\Throwable $e) {
                Log::error('Failed to send new registration notification.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->intended(route('home', absolute: false));
    }

}

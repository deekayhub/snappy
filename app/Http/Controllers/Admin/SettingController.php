<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allCached();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'app_name'       => ['nullable', 'string', 'max:255'],
            'help_email'     => ['nullable', 'email', 'max:255'],
            'notify_email'   => ['nullable', 'email', 'max:255'],
            'support_phone'  => ['nullable', 'string', 'max:50'],
            'support_address'=> ['nullable', 'string', 'max:500'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'facebook_url'   => ['nullable', 'url', 'max:500'],
            'twitter_url'    => ['nullable', 'url', 'max:500'],
            'instagram_url'  => ['nullable', 'url', 'max:500'],
            'site_logo'      => ['nullable', 'file', 'image', 'max:2048'],
            'favicon'        => ['nullable', 'file', 'image', 'max:1024'],
            'footer_logo'    => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        $values = collect($validated)
            ->filter(fn ($value, $key) => ! in_array($key, ['site_logo', 'favicon', 'footer_logo']))
            ->map(fn ($value) => $value ?? '')
            ->toArray();

        Setting::set($values);

        foreach (['site_logo', 'favicon', 'footer_logo'] as $imageKey) {
            if ($request->hasFile($imageKey)) {
                $this->storeImage($request->file($imageKey), $imageKey);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings saved successfully.',
        ]);
    }

    protected function storeImage($file, string $key): void
    {
        $directory = public_path('settings');

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = $key . '.' . $extension;
        $file->move($directory, $filename);

        Setting::set([$key => 'settings/' . $filename]);
    }
}

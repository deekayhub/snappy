<?php

namespace App\Http\Controllers;

use App\Support\PageSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.page-settings.index', [
            'settings' => PageSettings::all(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'faq_page.banner_title' => ['required', 'string', 'max:255'],
            'faq_page.banner_description' => ['nullable', 'string'],

            'faq_section.title' => ['required', 'string', 'max:255'],
            'faq_section.highlight_text' => ['nullable', 'string', 'max:100'],
            'faq_section.description' => ['nullable', 'string'],
            'faq_section.cta_title' => ['nullable', 'string', 'max:255'],
            'faq_section.cta_description' => ['nullable', 'string'],
            'faq_section.cta_button_text' => ['nullable', 'string', 'max:100'],
            'faq_section.cta_button_link' => ['nullable', 'string', 'max:255'],
            'faq_section.items' => ['required', 'array', 'min:1'],
            'faq_section.items.*.question' => ['required', 'string', 'max:255'],
            'faq_section.items.*.answer' => ['required', 'string'],

            'home_category_section.title' => ['required', 'string', 'max:255'],
            'home_category_section.highlight_text' => ['nullable', 'string', 'max:100'],
            'home_category_section.description' => ['nullable', 'string'],
            'home_category_section.items' => ['required', 'array', 'min:1'],
            'home_category_section.items.*.title' => ['required', 'string', 'max:255'],
            'home_category_section.items.*.image' => ['nullable', 'string', 'max:255'],
        ]);

        PageSettings::save($validated);

        return back()->with('success', 'Page settings updated successfully.');
    }
}

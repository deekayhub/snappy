<?php

namespace App\Http\Controllers\Admin;

use \App\Models\OrganisationCategorySetting;
use App\Http\Controllers\Controller;
use App\Models\OrganisationCategory;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    public function index()
    {
        $sections = PageSection::all();
        $orgCategories = OrganisationCategory::with('categorySetting')->where('type', 'supplier')->get();
        $homeContactSection = $sections->where('section_type', 'home_contact_section')->first();
        // dd($orgCategories->toArray());
        return view('admin.page-sections.index', compact('sections', 'orgCategories', 'homeContactSection'));
    }
    public function store(Request $request)
    {
        if ($request->section_type == 'faq') {
        $request->validate([
            'section_type' => 'required',
            'heading' => 'required|string|max:255',
            'description' => 'nullable|string',
            'faqs' => 'required|array|min:1',
            'faqs.*.question' => 'required|string',
            'faqs.*.answer' => 'required|string',
        ]);

        $data = [
            'heading' => $request->heading,
            'description' => $request->description,
            'faqs' => array_values($request->faqs),
        ];
        } elseif ($request->section_type == 'how_it_work') {
            $request->validate([
                'section_type' => 'required',
                'heading' => 'required|string|max:255',
                'description' => 'nullable|string',
                'steps' => 'required|array|min:1',
                'steps.*.heading' => 'required|string',
                'steps.*.description' => 'required|string',
            ]);

            $data = [
                'heading' => $request->heading,
                'description' => $request->description,
                'steps' => array_values($request->steps),
            ];
        } elseif ($request->section_type == 'home_contact_section') {
            $request->validate([
                'section_type' => 'required',
                'heading' => 'required|string|max:255',
                'description' => 'nullable|string',
                'button_text' => 'required|string|max:255',
            ]);

            $data = [
                'heading' => $request->heading,
                'description' => $request->description,
                'button_text' => $request->button_text,
            ];
        } else {
            return redirect()->back()->with('error', 'Invalid section type');
        }

        PageSection::updateOrCreate(
            [
                'section_type' => $request->section_type,
            ],
            [
                'data' => $data,
                'status' => 1,
            ]
        );

        return redirect()->back()->with('success', 'Section Saved Successfully');

    }
    
    public function organisationCategoryUpdate(Request $request, $categoryId)
    {
        $request->validate([
            'image'  => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'status' => 'required|in:active,inactive',
            'coming_soon' => 'nullable|boolean',
        ]);

        $setting = OrganisationCategorySetting::firstOrNew([
            'organisation_category_id' => $categoryId
        ]);

        if ($request->hasFile('image')) {

            if ($setting->image && file_exists(public_path($setting->image))) {
                unlink(public_path($setting->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            $image->move(public_path('organisation_category_images'), $imageName);

            $setting->image = 'organisation_category_images/' . $imageName;
        }

        $setting->status = $request->status;
        $setting->coming_soon = $request->boolean('coming_soon');
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }
}

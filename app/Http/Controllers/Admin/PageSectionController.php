<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    public function index()
    {
        $sections = PageSection::all();
        return view('admin.page-sections.index', compact('sections'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
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
}

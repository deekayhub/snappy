<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use App\Models\Plan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $faqs = PageSection::where('section_type', 'faq')->first();
        $howItWork = PageSection::where('section_type', 'how_it_work')->first();
        return view('home', compact('faqs', 'howItWork'));
    }
    public function supplier()
    {
        $plans = Plan::active()->ordered()->get();
        $faqs = PageSection::where('section_type', 'faq')->first();
        return view('supplier', compact('faqs', 'plans'));
    }

    public function howItWork()
    {
        $faqs = PageSection::where('section_type', 'faq')->first();
        return view('how-it-work', compact('faqs'));
    }

    public function faq()
    {
        $faqs = PageSection::where('section_type', 'faq')->first();
        return view('faq', compact('faqs'));
    }

    public function contactUs()
    {
        $faqs = PageSection::where('section_type', 'faq')->first();
        return view('contact-us', compact('faqs'));
    }

    public function pricing()
    {
        $plans = Plan::active()->ordered()->get();
        return view('pricing', compact('plans'));
    }
}

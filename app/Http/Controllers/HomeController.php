<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $faqs = PageSection::where('section_type', 'faq')->first();
        return view('home', compact('faqs'));
    }
    public function supplier()
    {
        return view('supplier');
    }
}

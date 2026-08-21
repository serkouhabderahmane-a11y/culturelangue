<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->with('category')
            ->orderBy('order')
            ->get();

        $categories = ServiceCategory::where('is_active', true)
            ->with(['activeServices'])
            ->orderBy('order')
            ->get();

        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('order')
            ->get();

        $faqs = Faq::where('is_active', true)
            ->orderBy('order')
            ->get();

        $statistics = Statistic::where('is_active', true)
            ->orderBy('order')
            ->get();

        $settings = Setting::pluck('value', 'key')->toArray();

        return view('home', compact(
            'services',
            'categories',
            'testimonials',
            'faqs',
            'statistics',
            'settings'
        ));
    }
}

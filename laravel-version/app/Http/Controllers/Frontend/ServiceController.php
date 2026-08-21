<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceController extends Controller
{
    public function category(string $slug)
    {
        $category = ServiceCategory::where('slug', $slug)
            ->where('is_active', true)
            ->with(['activeServices'])
            ->firstOrFail();

        return view('pages.service-category', compact('category'));
    }

    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        $relatedServices = Service::where('service_category_id', $service->service_category_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->get();

        return view('pages.service-detail', compact('service', 'relatedServices'));
    }
}

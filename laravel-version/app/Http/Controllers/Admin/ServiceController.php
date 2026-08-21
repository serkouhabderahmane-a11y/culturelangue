<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('category')->orderBy('order')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('order')->pluck('name_fr', 'id');
        return view('admin.services.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'slug' => 'required|string|max:255|unique:services',
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_description_fr' => 'nullable|string',
            'short_description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'banner_image' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::orderBy('order')->pluck('name_fr', 'id');
        return view('admin.services.form', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'slug' => 'required|string|max:255|unique:services,slug,' . $service->id,
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_description_fr' => 'nullable|string',
            'short_description_en' => 'nullable|string',
            'description_fr' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration' => 'nullable|string|max:255',
            'price' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'banner_image' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }
}

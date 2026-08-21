<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('order')->paginate(20);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'role_fr' => 'nullable|string|max:255',
            'role_en' => 'nullable|string|max:255',
            'content_fr' => 'required|string',
            'content_en' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage créé avec succès.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name_fr' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'role_fr' => 'nullable|string|max:255',
            'role_en' => 'nullable|string|max:255',
            'content_fr' => 'required|string',
            'content_en' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage mis à jour avec succès.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Témoignage supprimé avec succès.');
    }
}

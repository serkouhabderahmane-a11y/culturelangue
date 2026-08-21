<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function index()
    {
        $items = NavigationItem::with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.navigation.index', compact('items'));
    }

    public function create()
    {
        $parents = NavigationItem::whereNull('parent_id')->orderBy('order')->pluck('label_fr', 'id');
        return view('admin.navigation.form', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label_fr' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'target' => 'nullable|string|max:20',
        ]);

        NavigationItem::create($validated);

        return redirect()->route('admin.navigation.index')
            ->with('success', 'Élément de navigation créé avec succès.');
    }

    public function edit(NavigationItem $navigation)
    {
        $parents = NavigationItem::whereNull('parent_id')
            ->where('id', '!=', $navigation->id)
            ->orderBy('order')
            ->pluck('label_fr', 'id');

        return view('admin.navigation.form', compact('navigation', 'parents'));
    }

    public function update(Request $request, NavigationItem $navigation)
    {
        $validated = $request->validate([
            'label_fr' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'target' => 'nullable|string|max:20',
        ]);

        $navigation->update($validated);

        return redirect()->route('admin.navigation.index')
            ->with('success', 'Élément de navigation mis à jour avec succès.');
    }

    public function destroy(NavigationItem $navigation)
    {
        $navigation->delete();
        return redirect()->route('admin.navigation.index')
            ->with('success', 'Élément de navigation supprimé avec succès.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index()
    {
        $statistics = Statistic::orderBy('order')->paginate(20);
        return view('admin.statistics.index', compact('statistics'));
    }

    public function create()
    {
        return view('admin.statistics.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label_fr' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'value' => 'required|string|max:50',
            'suffix_fr' => 'nullable|string|max:20',
            'suffix_en' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        Statistic::create($validated);

        return redirect()->route('admin.statistics.index')
            ->with('success', 'Statistique créée avec succès.');
    }

    public function edit(Statistic $statistic)
    {
        return view('admin.statistics.form', compact('statistic'));
    }

    public function update(Request $request, Statistic $statistic)
    {
        $validated = $request->validate([
            'label_fr' => 'required|string|max:255',
            'label_en' => 'nullable|string|max:255',
            'value' => 'required|string|max:50',
            'suffix_fr' => 'nullable|string|max:20',
            'suffix_en' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $statistic->update($validated);

        return redirect()->route('admin.statistics.index')
            ->with('success', 'Statistique mise à jour avec succès.');
    }

    public function destroy(Statistic $statistic)
    {
        $statistic->delete();
        return redirect()->route('admin.statistics.index')
            ->with('success', 'Statistique supprimée avec succès.');
    }
}

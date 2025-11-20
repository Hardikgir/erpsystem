<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $modules = Module::with('subModules')->latest()->paginate(10);
        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.modules.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        Module::create($validated);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Module $module): View
    {
        $module->load('subModules');
        return view('admin.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module): View
    {
        return view('admin.modules.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module): RedirectResponse
    {
        $validated = $request->validate([
            'module_name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $module->update($validated);

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();

        return redirect()->route('admin.modules.index')
            ->with('success', 'Module deleted successfully.');
    }
}

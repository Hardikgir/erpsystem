<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\SubModule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $subModules = SubModule::with('module')->latest()->paginate(10);
        return view('admin.sub-modules.index', compact('subModules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $modules = Module::where('status', true)->get();
        return view('admin.sub-modules.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'sub_module_name' => 'required|string|max:255',
            'route' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9._-]+$/i', // Only allow alphanumeric, dots, underscores, and hyphens
            ],
            'status' => 'boolean',
        ], [
            'route.regex' => 'Route name cannot contain spaces. Use underscores (_) or hyphens (-) instead. Example: marks_check or marks-check',
        ]);

        SubModule::create($validated);

        return redirect()->route('admin.sub-modules.index')
            ->with('success', 'Sub Module created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubModule $subModule): View
    {
        $subModule->load('module');
        return view('admin.sub-modules.show', compact('subModule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubModule $subModule): View
    {
        $modules = Module::where('status', true)->get();
        return view('admin.sub-modules.edit', compact('subModule', 'modules'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubModule $subModule): RedirectResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'sub_module_name' => 'required|string|max:255',
            'route' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9._-]+$/i', // Only allow alphanumeric, dots, underscores, and hyphens
            ],
            'status' => 'boolean',
        ], [
            'route.regex' => 'Route name cannot contain spaces. Use underscores (_) or hyphens (-) instead. Example: marks_check or marks-check',
        ]);

        $subModule->update($validated);

        return redirect()->route('admin.sub-modules.index')
            ->with('success', 'Sub Module updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubModule $subModule): RedirectResponse
    {
        $subModule->delete();

        return redirect()->route('admin.sub-modules.index')
            ->with('success', 'Sub Module deleted successfully.');
    }
}

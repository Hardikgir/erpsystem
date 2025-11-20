<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class ModuleMasterController extends Controller
{
    /**
     * Display the module master page with form and list
     */
    public function index(): View
    {
        $modules = Module::whereNotNull('module_code')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('superadmin.module-master', compact('modules'));
    }

    /**
     * Store a newly created module
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'module_code' => 'required|string|max:255|unique:modules,module_code|regex:/^[A-Z0-9]+$/',
            'module_name' => 'required|string|max:255',
        ], [
            'module_code.required' => 'Module Code is required.',
            'module_code.unique' => 'Module Code already exists.',
            'module_code.regex' => 'Module Code must be uppercase letters and numbers only.',
            'module_name.required' => 'Module Name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Module::create([
            'module_code' => strtoupper($request->module_code),
            'module_name' => $request->module_name,
            'status' => true,
        ]);

        return redirect()->route('module.master')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Show the form for editing the specified module
     */
    public function edit($id): View
    {
        $module = Module::findOrFail($id);
        $modules = Module::whereNotNull('module_code')
            ->orderBy('id', 'desc')
            ->get();
        
        return view('superadmin.module-master', compact('modules', 'module'));
    }

    /**
     * Update the specified module
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $module = Module::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'module_code' => 'required|string|max:255|unique:modules,module_code,' . $id . '|regex:/^[A-Z0-9]+$/',
            'module_name' => 'required|string|max:255',
        ], [
            'module_code.required' => 'Module Code is required.',
            'module_code.unique' => 'Module Code already exists.',
            'module_code.regex' => 'Module Code must be uppercase letters and numbers only.',
            'module_name.required' => 'Module Name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $module->update([
            'module_code' => strtoupper($request->module_code),
            'module_name' => $request->module_name,
        ]);

        return redirect()->route('module.master')
            ->with('success', 'Module updated successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleColorController extends Controller
{
    /**
     * Display the role colors management page.
     */
    public function index(): View
    {
        $roles = Role::all();
        return view('admin.roles.role-colors', compact('roles'));
    }

    /**
     * Update role color.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'role_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'role_hover_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        // If hover color is not provided, generate a darker shade
        if (empty($validated['role_hover_color'])) {
            $validated['role_hover_color'] = $this->darkenColor($validated['role_color']);
        }

        $role->update($validated);

        return redirect()->route('admin.role-colors.index')
            ->with('success', 'Role color updated successfully.');
    }

    /**
     * Darken a hex color by 10%
     */
    private function darkenColor(string $hexColor): string
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Darken by 10%
        $r = max(0, min(255, $r * 0.9));
        $g = max(0, min(255, $g * 0.9));
        $b = max(0, min(255, $b * 0.9));
        
        // Convert back to hex
        return '#' . str_pad(dechex(round($r)), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($g)), 2, '0', STR_PAD_LEFT) .
                   str_pad(dechex(round($b)), 2, '0', STR_PAD_LEFT);
    }
}

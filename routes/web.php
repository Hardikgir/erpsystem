<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleColorController;
use App\Http\Controllers\Admin\SubModuleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentExamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student Routes
    Route::get('/student/exam-details', [StudentExamController::class, 'index'])->name('student.exam.details');

    // Admin routes - Super Admin only
    Route::middleware('isSuperAdmin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Role Management
        Route::resource('roles', RoleController::class);
        
        // Module Management
        Route::resource('modules', ModuleController::class);
        
        // Sub Module Management
        Route::resource('sub-modules', SubModuleController::class);
        
        // Permission Management
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/{role}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('permissions/{role}/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
        
        // User Management
        Route::resource('users', UserManagementController::class);
        Route::patch('users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Role Color Management
        Route::get('role-colors', [RoleColorController::class, 'index'])->name('role-colors.index');
        Route::put('role-colors/{role}', [RoleColorController::class, 'update'])->name('role-colors.update');
    });
});

require __DIR__.'/auth.php';

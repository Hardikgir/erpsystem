<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoleColorController;
use App\Http\Controllers\Admin\SubModuleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ModuleMasterController;
use App\Http\Controllers\UniversityMasterController;
use App\Http\Controllers\UniversityAdminDashboardController;
use App\Http\Controllers\ProgramMasterController;
use App\Http\Controllers\CourseMasterController;
use App\Http\Controllers\CollegeMasterController;
use App\Http\Controllers\UniversityRoleMasterController;
use App\Http\Controllers\SessionMasterController;
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

    // University Admin Dashboard
    Route::get('/university-admin/dashboard', [UniversityAdminDashboardController::class, 'index'])->name('university.admin.dashboard');

    // University Admin Master Pages
    Route::middleware('isUniversityAdmin')->prefix('university-admin')->name('university.admin.')->group(function () {
        // Program Master
        Route::get('/program-master', [ProgramMasterController::class, 'index'])->name('program.master');
        Route::post('/program-master/store', [ProgramMasterController::class, 'store'])->name('program.store');
        Route::get('/program-master/edit/{id}', [ProgramMasterController::class, 'edit'])->name('program.edit');
        Route::post('/program-master/update/{id}', [ProgramMasterController::class, 'update'])->name('program.update');
        
        // Course Master
        Route::get('/course-master', [CourseMasterController::class, 'index'])->name('course.master');
        Route::post('/course-master/store', [CourseMasterController::class, 'store'])->name('course.store');
        Route::get('/course-master/edit/{id}', [CourseMasterController::class, 'edit'])->name('course.edit');
        Route::post('/course-master/update/{id}', [CourseMasterController::class, 'update'])->name('course.update');
        
        // College Master
        Route::get('/college-master', [CollegeMasterController::class, 'index'])->name('college.master');
        Route::post('/college-master/store', [CollegeMasterController::class, 'store'])->name('college.store');
        Route::get('/college-master/edit/{id}', [CollegeMasterController::class, 'edit'])->name('college.edit');
        Route::post('/college-master/update/{id}', [CollegeMasterController::class, 'update'])->name('college.update');
        
        // Role Master
        Route::get('/role-master', [UniversityRoleMasterController::class, 'index'])->name('role.master');
        Route::post('/role-master/store', [UniversityRoleMasterController::class, 'store'])->name('role.store');
        Route::get('/role-master/edit/{id}', [UniversityRoleMasterController::class, 'edit'])->name('role.edit');
        Route::post('/role-master/update/{id}', [UniversityRoleMasterController::class, 'update'])->name('role.update');
        
        // Session Master
        Route::get('/session-master', [SessionMasterController::class, 'index'])->name('session.master');
        Route::post('/session-master/store', [SessionMasterController::class, 'store'])->name('session.store');
        Route::get('/session-master/edit/{id}', [SessionMasterController::class, 'edit'])->name('session.edit');
        Route::post('/session-master/update/{id}', [SessionMasterController::class, 'update'])->name('session.update');
    });

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

    // Module Master - Super Admin only
    Route::middleware('isSuperAdmin')->group(function () {
        Route::get('/module-master', [ModuleMasterController::class, 'index'])->name('module.master');
        Route::post('/module-master/store', [ModuleMasterController::class, 'store'])->name('module.master.store');
        Route::get('/module-master/edit/{id}', [ModuleMasterController::class, 'edit'])->name('module.master.edit');
        Route::post('/module-master/update/{id}', [ModuleMasterController::class, 'update'])->name('module.master.update');
        
        // University Master - Super Admin only
        Route::get('/university-master', [UniversityMasterController::class, 'index'])->name('university.master');
        Route::post('/university-master/store', [UniversityMasterController::class, 'store'])->name('university.master.store');
        Route::get('/university-master/view/{id}', [UniversityMasterController::class, 'show'])->name('university.master.show');
        Route::get('/university-master/edit/{id}', [UniversityMasterController::class, 'edit'])->name('university.master.edit');
        Route::post('/university-master/update/{id}', [UniversityMasterController::class, 'update'])->name('university.master.update');
        Route::delete('/university-master/delete/{id}', [UniversityMasterController::class, 'destroy'])->name('university.master.destroy');
        Route::post('/university-master/status/{id}', [UniversityMasterController::class, 'toggleStatus'])->name('university.master.status');
    });
});

require __DIR__.'/auth.php';

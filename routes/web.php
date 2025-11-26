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
use App\Http\Controllers\FeeElementController;
use App\Http\Controllers\FeePackageController;
use App\Http\Controllers\FeePlanController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\SuperAdmin\ProgramMasterController as SuperAdminProgramMasterController;
use App\Http\Controllers\SuperAdmin\CourseMasterController as SuperAdminCourseMasterController;
use App\Http\Controllers\SuperAdmin\CollegeMasterController as SuperAdminCollegeMasterController;
use App\Http\Controllers\SuperAdmin\UniversityRoleMasterController as SuperAdminUniversityRoleMasterController;
use App\Http\Controllers\SuperAdmin\SessionMasterController as SuperAdminSessionMasterController;
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
        Route::get('/program-master', [ProgramMasterController::class, 'index'])->middleware('permission:university.admin.program.master.view')->name('program.master');
        Route::post('/program-master/store', [ProgramMasterController::class, 'store'])->middleware('permission:university.admin.program.master.create')->name('program.store');
        Route::get('/program-master/edit/{id}', [ProgramMasterController::class, 'edit'])->middleware('permission:university.admin.program.master.edit')->name('program.edit');
        Route::post('/program-master/update/{id}', [ProgramMasterController::class, 'update'])->middleware('permission:university.admin.program.master.edit')->name('program.update');
        
        // Course Master
        Route::get('/course-master', [CourseMasterController::class, 'index'])->middleware('permission:university.admin.course.master.view')->name('course.master');
        Route::post('/course-master/store', [CourseMasterController::class, 'store'])->middleware('permission:university.admin.course.master.create')->name('course.store');
        Route::get('/course-master/edit/{id}', [CourseMasterController::class, 'edit'])->middleware('permission:university.admin.course.master.edit')->name('course.edit');
        Route::post('/course-master/update/{id}', [CourseMasterController::class, 'update'])->middleware('permission:university.admin.course.master.edit')->name('course.update');
        
        // College Master
        Route::get('/college-master', [CollegeMasterController::class, 'index'])->middleware('permission:university.admin.college.master.view')->name('college.master');
        Route::post('/college-master/store', [CollegeMasterController::class, 'store'])->middleware('permission:university.admin.college.master.create')->name('college.store');
        Route::get('/college-master/edit/{id}', [CollegeMasterController::class, 'edit'])->middleware('permission:university.admin.college.master.edit')->name('college.edit');
        Route::post('/college-master/update/{id}', [CollegeMasterController::class, 'update'])->middleware('permission:university.admin.college.master.edit')->name('college.update');
        
        // Role Master
        Route::get('/role-master', [UniversityRoleMasterController::class, 'index'])->middleware('permission:university.admin.role.master.view')->name('role.master');
        Route::post('/role-master/store', [UniversityRoleMasterController::class, 'store'])->middleware('permission:university.admin.role.master.create')->name('role.store');
        Route::get('/role-master/edit/{id}', [UniversityRoleMasterController::class, 'edit'])->middleware('permission:university.admin.role.master.edit')->name('role.edit');
        Route::post('/role-master/update/{id}', [UniversityRoleMasterController::class, 'update'])->middleware('permission:university.admin.role.master.edit')->name('role.update');
        
        // Session Master
        Route::get('/session-master', [SessionMasterController::class, 'index'])->middleware('permission:university.admin.session.master.view')->name('session.master');
        Route::post('/session-master/store', [SessionMasterController::class, 'store'])->middleware('permission:university.admin.session.master.create')->name('session.store');
        Route::get('/session-master/edit/{id}', [SessionMasterController::class, 'edit'])->middleware('permission:university.admin.session.master.edit')->name('session.edit');
        Route::post('/session-master/update/{id}', [SessionMasterController::class, 'update'])->middleware('permission:university.admin.session.master.edit')->name('session.update');
        
        // Fee Element
        Route::get('/fee-element', [FeeElementController::class, 'index'])->middleware('permission:university.admin.fee.element.view')->name('fee.element');
        Route::post('/fee-element/store', [FeeElementController::class, 'store'])->middleware('permission:university.admin.fee.element.create')->name('fee.element.store');
        Route::get('/fee-element/edit/{id}', [FeeElementController::class, 'edit'])->middleware('permission:university.admin.fee.element.edit')->name('fee.element.edit');
        Route::post('/fee-element/update/{id}', [FeeElementController::class, 'update'])->middleware('permission:university.admin.fee.element.edit')->name('fee.element.update');
        
        // Fee Package
        Route::get('/fee-package', [FeePackageController::class, 'index'])->middleware('permission:university.admin.fee.package.view')->name('fee.package');
        Route::post('/fee-package/store', [FeePackageController::class, 'store'])->middleware('permission:university.admin.fee.package.create')->name('fee.package.store');
        Route::get('/fee-package/edit/{id}', [FeePackageController::class, 'edit'])->middleware('permission:university.admin.fee.package.edit')->name('fee.package.edit');
        Route::post('/fee-package/update/{id}', [FeePackageController::class, 'update'])->middleware('permission:university.admin.fee.package.edit')->name('fee.package.update');
        
        // Fee Plan
        Route::get('/fee-plan', [FeePlanController::class, 'index'])->middleware('permission:university.admin.fee.plan.view')->name('fee.plan');
        Route::post('/fee-plan/store', [FeePlanController::class, 'store'])->middleware('permission:university.admin.fee.plan.create')->name('fee.plan.store');
        Route::get('/fee-plan/edit/{id}', [FeePlanController::class, 'edit'])->middleware('permission:university.admin.fee.plan.edit')->name('fee.plan.edit');
        Route::post('/fee-plan/update/{id}', [FeePlanController::class, 'update'])->middleware('permission:university.admin.fee.plan.edit')->name('fee.plan.update');
        
        // Bank Master
        Route::get('/bank', [BankController::class, 'index'])->middleware('permission:university.admin.bank.master.view')->name('bank.master');
        Route::post('/bank/store', [BankController::class, 'store'])->middleware('permission:university.admin.bank.master.create')->name('bank.store');
        Route::get('/bank/edit/{id}', [BankController::class, 'edit'])->middleware('permission:university.admin.bank.master.edit')->name('bank.edit');
        Route::post('/bank/update/{id}', [BankController::class, 'update'])->middleware('permission:university.admin.bank.master.edit')->name('bank.update');
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

    // Super Admin Permission Management (Spatie-based)
    Route::middleware('isSuperAdmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('permissions/manage', [\App\Http\Controllers\SuperAdmin\PermissionManagementController::class, 'manage'])->name('permissions.manage');
        Route::get('permissions/{role}/get', [\App\Http\Controllers\SuperAdmin\PermissionManagementController::class, 'getRolePermissions'])->name('permissions.get');
        Route::put('permissions/{role}/update', [\App\Http\Controllers\SuperAdmin\PermissionManagementController::class, 'update'])->name('permissions.update');
        Route::post('permissions/sync', function() {
            \Artisan::call('permissions:sync-modules');
            return redirect()->route('superadmin.permissions.manage')->with('success', 'Permissions synced successfully!');
        })->name('permissions.sync');
    });

    // Module Master - Super Admin only
    Route::middleware('isSuperAdmin')->group(function () {
        Route::get('/module-master', [ModuleMasterController::class, 'index'])->name('module.master');
        Route::post('/module-master/store', [ModuleMasterController::class, 'store'])->name('module.master.store');
        Route::get('/module-master/edit/{id}', [ModuleMasterController::class, 'edit'])->name('module.master.edit');
        Route::post('/module-master/update/{id}', [ModuleMasterController::class, 'update'])->name('module.master.update');
        
        // University Master - Super Admin only
        Route::get('/university-master', [UniversityMasterController::class, 'index'])->name('university.master');
        Route::get('/university-master/create', [UniversityMasterController::class, 'create'])->name('university.master.create');
        Route::post('/university-master/store', [UniversityMasterController::class, 'store'])->name('university.master.store');
        Route::get('/university-master/{id}/view', [UniversityMasterController::class, 'view'])->name('university.master.view');
        Route::get('/university-master/{id}/edit', [UniversityMasterController::class, 'edit'])->name('university.master.edit');
        Route::post('/university-master/{id}/update', [UniversityMasterController::class, 'update'])->name('university.master.update');
        Route::delete('/university-master/{id}/delete', [UniversityMasterController::class, 'destroy'])->name('university.master.destroy');
        
        // University Master Data - Super Admin only
        // Program Master
        Route::get('/superadmin/program-master', [SuperAdminProgramMasterController::class, 'index'])->name('superadmin.program.master');
        Route::post('/superadmin/program-master/store', [SuperAdminProgramMasterController::class, 'store'])->name('superadmin.program.store');
        Route::get('/superadmin/program-master/edit/{id}', [SuperAdminProgramMasterController::class, 'edit'])->name('superadmin.program.edit');
        Route::post('/superadmin/program-master/update/{id}', [SuperAdminProgramMasterController::class, 'update'])->name('superadmin.program.update');
        
        // Course Master
        Route::get('/superadmin/course-master', [SuperAdminCourseMasterController::class, 'index'])->middleware('permission:course.master.view')->name('superadmin.course.master');
        Route::post('/superadmin/course-master/store', [SuperAdminCourseMasterController::class, 'store'])->middleware('permission:course.master.create')->name('superadmin.course.store');
        Route::get('/superadmin/course-master/edit/{id}', [SuperAdminCourseMasterController::class, 'edit'])->middleware('permission:course.master.edit')->name('superadmin.course.edit');
        Route::post('/superadmin/course-master/update/{id}', [SuperAdminCourseMasterController::class, 'update'])->middleware('permission:course.master.edit')->name('superadmin.course.update');
        
        // College Master
        Route::get('/superadmin/college-master', [SuperAdminCollegeMasterController::class, 'index'])->name('superadmin.college.master');
        Route::post('/superadmin/college-master/store', [SuperAdminCollegeMasterController::class, 'store'])->name('superadmin.college.store');
        Route::get('/superadmin/college-master/edit/{id}', [SuperAdminCollegeMasterController::class, 'edit'])->name('superadmin.college.edit');
        Route::post('/superadmin/college-master/update/{id}', [SuperAdminCollegeMasterController::class, 'update'])->name('superadmin.college.update');
        
        // University Role Master
        Route::get('/superadmin/universityrole-master', [SuperAdminUniversityRoleMasterController::class, 'index'])->name('superadmin.universityrole.master');
        Route::post('/superadmin/universityrole-master/store', [SuperAdminUniversityRoleMasterController::class, 'store'])->name('superadmin.universityrole.store');
        Route::get('/superadmin/universityrole-master/edit/{id}', [SuperAdminUniversityRoleMasterController::class, 'edit'])->name('superadmin.universityrole.edit');
        Route::post('/superadmin/universityrole-master/update/{id}', [SuperAdminUniversityRoleMasterController::class, 'update'])->name('superadmin.universityrole.update');
        
        // Session Master
        Route::get('/superadmin/session-master', [SuperAdminSessionMasterController::class, 'index'])->name('superadmin.session.master');
        Route::post('/superadmin/session-master/store', [SuperAdminSessionMasterController::class, 'store'])->name('superadmin.session.store');
        Route::get('/superadmin/session-master/edit/{id}', [SuperAdminSessionMasterController::class, 'edit'])->name('superadmin.session.edit');
        Route::post('/superadmin/session-master/update/{id}', [SuperAdminSessionMasterController::class, 'update'])->name('superadmin.session.update');
    });
});

require __DIR__.'/auth.php';

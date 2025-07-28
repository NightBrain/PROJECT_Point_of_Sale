<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Livewire\Dashboard;
use App\Livewire\Pos;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [AdminController::class, 'login'])->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    // route สำหรับเจ้าของร้านเท่านั้น
    Route::get('/admin', Dashboard::class)->name('admin');

    Route::resource('/admin/products', ProductController::class);
    Route::post('/admin/products/import', [ProductController::class, 'importCSV'])->name('products.import');
    Route::post('/admin/products/delete-selected', [ProductController::class, 'deleteSelected'])->name('products.deleteSelected');

    Route::get('/admin/sales/history', [SaleController::class, 'history'])->name('sales.history');
    Route::get('/admin/sales/{id}/bill-json', [SaleController::class, 'billJson']);
    Route::get('/admin/sales/{id}/share', [SaleController::class, 'share'])->name('sales.share');
    Route::get('/admin/sales/{id}/export', [SaleController::class, 'export'])->name('sales.export');
    Route::post('/admin/sales/delete-selected', [SaleController::class, 'deleteSelected'])->name('sales.deleteSelected');

    Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/admin/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::patch('/admin/employees/{employee}/role', [EmployeeController::class, 'updateRole'])->name('employees.updateRole');
    Route::delete('/admin/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

});

Route::middleware('auth')->group(function () {
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');
    Route::post('/activity-logs/delete-selected', [ActivityLogController::class, 'deleteSelected'])->name('activityLogs.deleteSelected');

    Route::get('/pos', Pos::class)->name('pos');
    Route::get('/sales/print/{id}', [SaleController::class, 'print'])->name('sales.print');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

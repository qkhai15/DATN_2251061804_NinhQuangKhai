<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\IssueController as AdminIssueController;
use App\Http\Controllers\Admin\ParkingController;
use App\Http\Controllers\Admin\MeterReadingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\IssueController as TenantIssueController;
use App\Http\Controllers\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Tenant\ProfileController as TenantProfileController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Tenant\NotificationController as TenantNotificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\AIController;

Route::post('/ai/ask', [AIController::class, 'ask'])->name('ai.ask');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('buildings', BuildingController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('contracts', ContractController::class);
    Route::post('invoices/preview', [AdminInvoiceController::class, 'preview'])->name('invoices.preview');
    Route::get('invoices/{invoice}/print', [AdminInvoiceController::class, 'print'])->name('invoices.print');
    Route::resource('invoices', AdminInvoiceController::class);

    // Tenant Management
    Route::resource('tenants', AdminTenantController::class);

    // System Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('services', ServiceController::class);
    Route::get('issues/report', [AdminIssueController::class, 'report'])->name('issues.report');
    Route::resource('issues', AdminIssueController::class);
    Route::resource('parking', ParkingController::class);
    Route::resource('notifications', AdminNotificationController::class);
    Route::get('meter-readings/latest-value', [MeterReadingController::class, 'getLatestValue'])->name('meter-readings.latest-value');
    Route::resource('meter-readings', MeterReadingController::class);
});

Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');
    Route::resource('issues', TenantIssueController::class);
    Route::resource('invoices', TenantInvoiceController::class)->only(['index', 'show']);
    Route::get('profile', [TenantProfileController::class, 'index'])->name('profile');
    Route::put('profile', [TenantProfileController::class, 'update'])->name('profile.update');

    // Tenant Notifications
    Route::get('notifications', [TenantNotificationController::class, 'index'])->name('notifications.index');
    Route::put('notifications/{notification}', [TenantNotificationController::class, 'update'])->name('notifications.update');
    Route::post('notifications/mark-all-read', [TenantNotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});
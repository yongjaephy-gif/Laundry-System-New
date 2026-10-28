<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PickupAndDeliveryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DemoController;

// 1. Home Route (When opening first: redirect to Login if guest, or Dashboard if authenticated)
Route::get('/', function () {
    if (Auth::check() || session('is_logged_in') === true) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// 2. Guest Routes (Login, Register, Forgot Password)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// 3. Logout Route (Accessible via GET or POST)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// 4. Authenticated Application Routes (Protected by Auth: login once, accessible across ALL pages)
Route::middleware('auth')->group(function () {

    // Dashboard Module
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/switch-role/{role}', [DashboardController::class, 'switchRole'])->name('dashboard.switch-role');
    Route::get('/dashboard-preview', [DashboardController::class, 'index'])->name('dashboard.preview');

    // Service Management Module
    Route::resource('services', ServiceController::class);
    Route::get('/service', [ServiceController::class, 'index'])->name('service.index')->middleware('check.role:admin,staff,receptionist');
    Route::get('/services-preview', [ServiceController::class, 'index'])->name('services.preview');

    // Employee Management Module (Supports both /employees and /employee endpoints seamlessly)
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('check.role:admin');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::match(['put', 'patch', 'post'], '/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::match(['put', 'patch', 'post'], '/employees/{id}/update', [EmployeeController::class, 'update'])->name('employees.update.post');
    Route::match(['delete', 'post'], '/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::match(['delete', 'post'], '/employees/{id}/delete', [EmployeeController::class, 'destroy'])->name('employees.destroy.post');
    Route::match(['get', 'post'], '/employees/{id}/attendance', [EmployeeController::class, 'toggleAttendance'])->name('employees.attendance');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
    Route::get('/employee/{id}', [EmployeeController::class, 'show'])->name('employee.show');
    Route::match(['put', 'patch', 'post'], '/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::match(['put', 'patch', 'post'], '/employee/{id}/update', [EmployeeController::class, 'update'])->name('employee.update.post');
    Route::match(['delete', 'post'], '/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
    Route::match(['delete', 'post'], '/employee/{id}/delete', [EmployeeController::class, 'destroy'])->name('employee.destroy.post');
    Route::match(['get', 'post'], '/employee/{id}/attendance', [EmployeeController::class, 'toggleAttendance'])->name('employee.attendance');
    Route::get('/employees-preview', [EmployeeController::class, 'index'])->name('employees.preview');

    // Customer Management Module
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index')->middleware('check.role:admin,receptionist');
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index')->middleware('check.role:admin,receptionist');
    Route::get('/customers-preview', [CustomerController::class, 'index'])->name('customers.preview');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store')->middleware('check.role:admin,receptionist');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/{id}/orders', [CustomerController::class, 'orders'])->name('customers.orders');

    // Laundry Order Management Module
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::get('/order', [OrderController::class, 'index'])->name('order.index')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::get('/orders-preview', [OrderController::class, 'index'])->name('orders.preview');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{id}/print', [OrderController::class, 'printReceipt'])->name('orders.print');

    // Payment Management Module
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index')->middleware('check.role:admin,receptionist');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index')->middleware('check.role:admin,receptionist');
    Route::get('/payment-preview', [PaymentController::class, 'index'])->name('payment.preview');
    Route::post('/payment/record', [PaymentController::class, 'recordPayment'])->name('payment.record');
    Route::post('/payment/invoice', [PaymentController::class, 'generateInvoice'])->name('payment.invoice');
    Route::post('/payment/remind', [PaymentController::class, 'sendReminders'])->name('payment.remind');
    Route::get('/payment/export', [PaymentController::class, 'export'])->name('payment.export');
    Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{id}/refund', [PaymentController::class, 'refund'])->name('payment.refund');
    Route::delete('/payment/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');

    // Pickup & Delivery Logistics Module
    Route::get('/pickup-and-delivery', [PickupAndDeliveryController::class, 'index'])->name('pickup-and-delivery.index')->middleware('check.role:admin,receptionist,delivery');
    Route::get('/PickupAndDelivery', [PickupAndDeliveryController::class, 'index'])->name('PickupAndDelivery');
    Route::get('/pickup-delivery', [PickupAndDeliveryController::class, 'index'])->name('pickup-delivery.index')->middleware('check.role:admin,receptionist,delivery');
    Route::post('/pickup-and-delivery', [PickupAndDeliveryController::class, 'store'])->name('pickup-and-delivery.store')->middleware('check.role:admin,receptionist,delivery');
    Route::post('/pickup-and-delivery/schedule', [PickupAndDeliveryController::class, 'scheduleTask'])->name('pickup-and-delivery.schedule')->middleware('check.role:admin,receptionist,delivery');
    Route::get('/pickup-and-delivery/{id}', [PickupAndDeliveryController::class, 'show'])->name('pickup-and-delivery.show')->middleware('check.role:admin,receptionist,delivery');
    Route::match(['put', 'post'], '/pickup-and-delivery/{id}', [PickupAndDeliveryController::class, 'update'])->name('pickup-and-delivery.update')->middleware('check.role:admin,receptionist,delivery');
    Route::post('/pickup-and-delivery/{id}/update', [PickupAndDeliveryController::class, 'update'])->name('pickup-and-delivery.update-post')->middleware('check.role:admin,receptionist,delivery');
    Route::post('/pickup-and-delivery/assign/{id}', [PickupAndDeliveryController::class, 'assignStaff'])->name('pickup-and-delivery.assign')->middleware('check.role:admin,receptionist,delivery');
    Route::post('/pickup-and-delivery/{id}/status', [PickupAndDeliveryController::class, 'updateStatus'])->name('pickup-and-delivery.status')->middleware('check.role:admin,receptionist,delivery');
    Route::delete('/pickup-and-delivery/{id}', [PickupAndDeliveryController::class, 'destroy'])->name('pickup-and-delivery.destroy');
    Route::get('/pickup-and-delivery/export', [PickupAndDeliveryController::class, 'export'])->name('pickup-and-delivery.export');

    // Report Management Module (Supports both /reports and /report)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('check.role:admin');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store')->middleware('check.role:admin');
    Route::get('/reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/print-view', [ReportController::class, 'printView'])->name('reports.print');
    Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
    Route::put('/reports/{id}', [ReportController::class, 'update'])->name('reports.update')->middleware('check.role:admin');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::post('/reports/reset', [ReportController::class, 'reset'])->name('reports.reset');
    Route::get('/reports-preview', [ReportController::class, 'index'])->name('reports.preview');
    Route::post('/reports-preview', [ReportController::class, 'store'])->name('reports.preview.store');
    Route::put('/reports-preview/{id}', [ReportController::class, 'update'])->name('reports.preview.update');
    Route::delete('/reports-preview/{id}', [ReportController::class, 'destroy'])->name('reports.preview.destroy');
    Route::get('/reports-preview/export/{format}', [ReportController::class, 'export'])->name('reports.preview.export');

    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');
    Route::get('/report/export/{format}', [ReportController::class, 'export'])->name('report.export');
    Route::get('/report/print-view', [ReportController::class, 'printView'])->name('report.print');
    Route::post('/report/reset', [ReportController::class, 'reset'])->name('report.reset');
    Route::get('/report/{id}', [ReportController::class, 'show'])->name('report.show');
    Route::delete('/report/{id}', [ReportController::class, 'destroy'])->name('report.destroy');

    // Machine Management Module (Supports both /machines and /machine)
    Route::get('/machines', [MachineController::class, 'index'])->name('machines.index')->middleware('check.role:admin,staff');
    Route::post('/machines', [MachineController::class, 'store'])->name('machines.store')->middleware('check.role:admin,staff');
    Route::get('/machines/{id}', [MachineController::class, 'show'])->name('machines.show');
    Route::match(['put', 'patch', 'post'], '/machines/{id}', [MachineController::class, 'update'])->name('machines.update');
    Route::match(['put', 'patch', 'post'], '/machines/{id}/update', [MachineController::class, 'update'])->name('machines.update.post');
    Route::match(['delete', 'post'], '/machines/{id}', [MachineController::class, 'destroy'])->name('machines.destroy');
    Route::match(['delete', 'post'], '/machines/{id}/delete', [MachineController::class, 'destroy'])->name('machines.destroy.post');
    Route::post('/machines/{id}/status', [MachineController::class, 'updateStatus'])->name('machines.status')->middleware('check.role:admin,staff');
    Route::post('/machines/reset', [MachineController::class, 'reset'])->name('machines.reset');
    Route::get('/machines-preview', [MachineController::class, 'index'])->name('machines.preview');

    Route::get('/machine', [MachineController::class, 'index'])->name('machine');
    Route::post('/machine', [MachineController::class, 'store'])->name('machine.store');
    Route::get('/machine/{id}', [MachineController::class, 'show'])->name('machine.show');
    Route::match(['put', 'patch', 'post'], '/machine/{id}', [MachineController::class, 'update'])->name('machine.update');
    Route::match(['put', 'patch', 'post'], '/machine/{id}/update', [MachineController::class, 'update'])->name('machine.update.post');
    Route::match(['delete', 'post'], '/machine/{id}', [MachineController::class, 'destroy'])->name('machine.destroy');
    Route::match(['delete', 'post'], '/machine/{id}/delete', [MachineController::class, 'destroy'])->name('machine.destroy.post');
    Route::post('/machine/{id}/status', [MachineController::class, 'updateStatus'])->name('machine.status');
    Route::post('/machine/reset', [MachineController::class, 'reset'])->name('machine.reset');

    // Inventory Management Module (Supports both /inventory and /inventories endpoints)
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index')->middleware('check.role:admin,staff');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store')->middleware('check.role:admin,staff');
    Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show')->middleware('check.role:admin,staff');
    Route::match(['put', 'patch', 'post'], '/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update')->middleware('check.role:admin,staff');
    Route::match(['delete', 'post'], '/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('check.role:admin,staff');
    Route::post('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in')->middleware('check.role:admin,staff');
    Route::post('/inventory/{id}/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out')->middleware('check.role:admin,staff');
    Route::post('/inventory/reset', [InventoryController::class, 'reset'])->name('inventory.reset')->middleware('check.role:admin,staff');
    Route::get('/inventory-preview', [InventoryController::class, 'index'])->name('inventory.preview')->middleware('check.role:admin,staff');

    Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
    Route::post('/inventories', [InventoryController::class, 'store'])->name('inventories.store');
    Route::get('/inventories/{id}', [InventoryController::class, 'show'])->name('inventories.show');
    Route::match(['put', 'patch', 'post'], '/inventories/{id}', [InventoryController::class, 'update'])->name('inventories.update');
    Route::match(['put', 'patch', 'post'], '/inventories/{id}/update', [InventoryController::class, 'update'])->name('inventories.update.post');
    Route::match(['delete', 'post'], '/inventories/{id}', [InventoryController::class, 'destroy'])->name('inventories.destroy');
    Route::match(['delete', 'post'], '/inventories/{id}/delete', [InventoryController::class, 'destroy'])->name('inventories.destroy.post');
    Route::post('/inventories/{id}/stock-in', [InventoryController::class, 'stockIn'])->name('inventories.stock-in');
    Route::post('/inventories/{id}/stock-out', [InventoryController::class, 'stockOut'])->name('inventories.stock-out');
    Route::post('/inventories/reset', [InventoryController::class, 'reset'])->name('inventories.reset');

    // Notification Management Module
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::match(['put', 'patch', 'post'], '/notifications/{id}', [NotificationController::class, 'update'])->name('notifications.update')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::match(['put', 'patch', 'post'], '/notifications/{id}/update', [NotificationController::class, 'update'])->name('notifications.update.post')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::match(['delete', 'post'], '/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy')->middleware('check.role:admin,staff,receptionist,delivery');
    Route::match(['delete', 'post'], '/notifications/{id}/delete', [NotificationController::class, 'destroy'])->name('notifications.destroy.post');
    Route::post('/notifications/reset', [NotificationController::class, 'reset'])->name('notifications.reset');
    Route::get('/notification', [NotificationController::class, 'index'])->name('notification');
    Route::get('/notifications-preview', [NotificationController::class, 'index'])->name('notifications.preview');
    Route::post('/notifications/broadcast', [NotificationController::class, 'sendBroadcast'])->name('notifications.broadcast');
    Route::post('/notifications/toggle-trigger', [NotificationController::class, 'toggleTrigger'])->name('notifications.toggle');
    Route::post('/notifications/dry-run', [NotificationController::class, 'dryRunSandbox'])->name('notifications.sandbox');

    // Discounts & Promotions Module
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index')->middleware('check.role:admin,receptionist');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store')->middleware('check.role:admin,receptionist');
    Route::get('/promotions/{id}', [PromotionController::class, 'show'])->name('promotions.show')->middleware('check.role:admin,receptionist');
    Route::match(['put', 'patch', 'post'], '/promotions/{id}', [PromotionController::class, 'update'])->name('promotions.update')->middleware('check.role:admin,receptionist');
    Route::match(['put', 'patch', 'post'], '/promotions/{id}/update', [PromotionController::class, 'update'])->name('promotions.update.post')->middleware('check.role:admin,receptionist');
    Route::match(['delete', 'post'], '/promotions/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy')->middleware('check.role:admin,receptionist');
    Route::match(['delete', 'post'], '/promotions/{id}/delete', [PromotionController::class, 'destroy'])->name('promotions.destroy.post')->middleware('check.role:admin,receptionist');
    Route::post('/promotions/reset', [PromotionController::class, 'reset'])->name('promotions.reset');

    // Aliases for discount & promotion routes
    Route::get('/discounts', [PromotionController::class, 'index'])->name('discounts.index')->middleware('check.role:admin,receptionist');
    Route::get('/discount-promotion', [PromotionController::class, 'index'])->name('discount-promotion.index')->middleware('check.role:admin,receptionist');
    Route::get('/discount&promotion', [PromotionController::class, 'index'])->name('discount_promotion.index')->middleware('check.role:admin,receptionist');

    // Settings Module (Supports both /settings and /setting)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index')->middleware('check.role:admin');
    Route::get('/setting', [SettingController::class, 'index'])->name('setting')->middleware('check.role:admin');
    Route::post('/settings/update-info', [SettingController::class, 'updateInfo'])->name('settings.update-info');
    Route::post('/settings/update-hours', [SettingController::class, 'updateHours'])->name('settings.update-hours');
    Route::post('/settings/update-pricing', [SettingController::class, 'updatePricing'])->name('settings.update-pricing');
    Route::post('/settings/update-tax', [SettingController::class, 'updateTax'])->name('settings.update-tax');
    Route::post('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
    Route::get('/settings/download-backup/{filename}', [SettingController::class, 'downloadBackup'])->name('settings.download-backup');
    Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore');
    Route::post('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');

    // Aliases with singular /setting/
    Route::post('/setting/update-info', [SettingController::class, 'updateInfo'])->name('setting.update-info')->middleware('check.role:admin');
    Route::post('/setting/update-hours', [SettingController::class, 'updateHours'])->name('setting.update-hours')->middleware('check.role:admin');
    Route::post('/setting/update-pricing', [SettingController::class, 'updatePricing'])->name('setting.update-pricing')->middleware('check.role:admin');
    Route::post('/setting/update-tax', [SettingController::class, 'updateTax'])->name('setting.update-tax')->middleware('check.role:admin');
    Route::post('/setting/backup', [SettingController::class, 'backup'])->name('setting.backup')->middleware('check.role:admin');
    Route::get('/setting/download-backup/{filename}', [SettingController::class, 'downloadBackup'])->name('setting.download-backup')->middleware('check.role:admin');
    Route::post('/setting/restore', [SettingController::class, 'restore'])->name('setting.restore')->middleware('check.role:admin');
    Route::post('/setting/reset', [SettingController::class, 'reset'])->name('setting.reset')->middleware('check.role:admin');

});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CounterAssignmentController;

Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/status', [PublicController::class, 'getQueueStatus'])->name('public.status');
Route::get('/config-version', [PublicController::class, 'getConfigVersion'])->name('public.config-version');
Route::post('/register/{service}', [PublicController::class, 'registerQueue'])->name('queue.register');
Route::get('/print/{queue}', [PublicController::class, 'printTicket'])->name('queue.print');

// Staff Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/counter/select', [CounterAssignmentController::class, 'index'])->name('counter.select');
    Route::post('/counter/select', [CounterAssignmentController::class, 'select'])->name('counter.select.post');

    Route::prefix('counter')->group(function () {
        Route::get('/', [CounterController::class, 'index'])->name('counter.index');
        Route::post('/call-next', [CounterController::class, 'callNext'])->name('counter.call-next');
        Route::post('/finish/{queue}', [CounterController::class, 'finish'])->name('counter.finish');
        Route::post('/skip/{queue}', [CounterController::class, 'skip'])->name('counter.skip');
        Route::post('/recall/{queue}', [CounterController::class, 'recall'])->name('counter.recall');
        Route::post('/next/{queue}', [CounterController::class, 'next'])->name('counter.next');
        Route::post('/update-service', [CounterController::class, 'updateService'])->name('counter.update-service');
        Route::post('/register-queue/{service}', [PublicController::class, 'registerQueue'])->name('queue.register');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Service Management
    Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
    Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
    Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
    Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('admin.services.delete');

    // Counter Management
    Route::get('/counters', [AdminController::class, 'counters'])->name('admin.counters');
    Route::post('/counters', [AdminController::class, 'storeCounter'])->name('admin.counters.store');
    Route::put('/counters/{counter}', [AdminController::class, 'updateCounter'])->name('admin.counters.update');
    // Quota Management
    Route::get('/quotas', [AdminController::class, 'quotas'])->name('admin.quotas');
    Route::post('/quotas', [AdminController::class, 'updateQuota'])->name('admin.quotas.update');
    Route::post('/quotas/reset', [AdminController::class, 'resetQuota'])->name('admin.quotas.reset');

    Route::delete('/counters/{counter}', [AdminController::class, 'deleteCounter'])->name('admin.counters.delete');
});
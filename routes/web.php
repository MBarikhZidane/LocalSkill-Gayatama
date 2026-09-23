<?php

use App\Http\Controllers\Admin\CategoryserviceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PortofolioadminController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\CompleteProfileController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Users\ExplorController;
use App\Http\Controllers\Users\OrderuserController;
use App\Http\Controllers\Users\PortofolioController;
use App\Http\Controllers\Users\ServiceuserController;
use App\Http\Controllers\Users\SkilluserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('users.welcome');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


});

Route::prefix('user')
    ->middleware('auth')
    ->group(function () {
        Route::get('/services/{id}', [LandingController::class, 'show'])
            ->whereNumber('id')
            ->name('services.show');

        Route::get('/myorders', [LandingController::class, 'index'])
            ->name('myorders.index');

        Route::patch('/myorders/{order}/cancel', [LandingController::class, 'cancel'])
            ->name('myorders.cancel');

        Route::get('/profile/{id}', [LandingController::class, 'viewprofile'])
            ->name('profile.viewprofile');
    });
    
Route::prefix('user')
    ->middleware('auth')
    ->name('user.')
    ->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::get('/dashboard', [DashboardController::class, 'dashboarduser'])->name('dashboarduser');

        Route::delete('/services/bulk-delete', [ServiceuserController::class, 'destroyBulk'])
    ->name('services.destroy-bulk');

Route::resource('services', ServiceuserController::class)
    ->except(['show']);

        Route::delete('/orders/bulk-delete', [OrderuserController::class, 'destroyBulk'])
            ->name('orders.destroy-bulk');

        Route::resource('orders', OrderuserController::class)
            ->except(['create', 'store', 'show']);

        Route::delete('/myskill/bulk-delete', [SkilluserController::class, 'destroyBulk'])
            ->name('myskill.destroy-bulk');

        Route::resource('myskill', SkilluserController::class)
            ->except(['show']);

        Route::get('/myskill/search', [SkilluserController::class, 'search'])
            ->name('myskill.search');

        Route::delete('/portofolios/bulk-delete', [PortofolioController::class, 'destroyBulk'])
            ->name('portofolios.destroy-bulk');

        Route::resource('portofolios', PortofolioController::class)
            ->except(['show']);

        Route::post('/services/{service}/reviews', [LandingController::class, 'storeReview'])->name('services.reviews.store');

        Route::post('/orders-service', [LandingController::class, 'store'])->name('orders-service.store');

        Route::get('/messages', [ChatController::class, 'index'])->name('chat.index');

        Route::get('/chat/conversations', [ChatController::class, 'getConversations']);

        Route::get('/chat/conversations/{id}', [ChatController::class, 'getMessages']);

        Route::post('/chat/conversations/{id}/send', [ChatController::class, 'sendMessage']);

        Route::get('/settings', [LandingController::class, 'edit'])->name('profile.edit');
        Route::put('/settings', [LandingController::class, 'update'])->name('profile.update');
        Route::post('/settings/register-provider', [LandingController::class, 'registerProvider'])->name('profile.register-provider');
        Route::post('/logout', [LandingController::class, 'logout'])->name('logout');
    });



Route::get('/explore', [ExplorController::class, 'index'])->name('explore.index');
// Route::get('/services/{service}', [ServiceController::class, 'show'])
//     ->name('services.show');

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::delete('/skills/bulk-delete', [SkillController::class, 'destroyBulk'])
            ->name('skills.destroy-bulk');
        Route::resource('skills', SkillController::class)
            ->except(['show']);
        Route::delete('/categories/bulk-delete', [CategoryserviceController::class, 'destroyBulk'])
            ->name('categories.destroy-bulk');
        Route::resource('categories', CategoryserviceController::class)
            ->parameters(['categories' => 'skillCategory'])
            ->except(['show']);
        Route::delete('/orders/bulk-delete', [OrderController::class, 'destroyBulk'])
            ->name('orders.destroy-bulk');
        Route::resource('orders', OrderController::class);
        Route::delete('/users/bulk-delete', [UserController::class, 'destroyBulk'])
            ->name('users.destroy-bulk');
        Route::resource('users', UserController::class);
        Route::delete('/services/bulk-delete', [ServiceController::class, 'destroyBulk'])
            ->name('services.destroy-bulk');
        Route::resource('services', ServiceController::class);
        Route::delete('/portofolios/bulk-delete', [PortofolioadminController::class, 'destroyBulk'])
            ->name('portofolios.destroy-bulk');
        Route::resource('portofolios', PortofolioadminController::class);
    });

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/auth/complete-profile', [CompleteProfileController::class, 'create'])->name('complete-profile');
Route::post('/auth/complete-profile', [CompleteProfileController::class, 'store'])->name('complete-profile.store');
require __DIR__ . '/auth.php';

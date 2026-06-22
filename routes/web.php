<?php

use App\Http\Controllers\Admin\AdminCardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPreferenceController;
use App\Http\Controllers\Admin\AdminTemplateController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiftController;
use App\Http\Controllers\User\UserCardController;
use App\Http\Controllers\User\UserPreferenceController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard and Analytics
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/activity', [AdminDashboardController::class, 'activity'])->name('activity');

    // User Management
    Route::resource('users', AdminUserController::class)->except(['show'])->parameters(['users' => 'user']);
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');

    // Template Management - Custom routes first to avoid conflicts
    Route::post('templates/upload-variable-photo', [AdminTemplateController::class, 'uploadVariablePhoto'])->name('templates.upload-variable-photo');
    Route::post('templates/upload-variable-video', [AdminTemplateController::class, 'uploadVariableVideo'])->name('templates.upload-variable-video');
    Route::post('templates/upload-variable-audio', [AdminTemplateController::class, 'uploadVariableAudio'])->name('templates.upload-variable-audio');
    Route::delete('templates/delete-variable-file', [AdminTemplateController::class, 'deleteVariableFile'])->name('templates.delete-variable-file');
    Route::get('malaysian-templates', [AdminTemplateController::class, 'malaysian'])->name('templates.malaysian');
    Route::get('templates/{template}/preview', [AdminTemplateController::class, 'preview'])->name('templates.preview');
    Route::get('templates/{template}/full-preview', [AdminTemplateController::class, 'fullPreview'])->name('templates.full-preview');
    Route::resource('templates', AdminTemplateController::class)->parameters(['templates' => 'template']);

    // Wedding Card Management
    Route::resource('cards', AdminCardController::class)->except(['create', 'store'])->parameters(['cards' => 'card']);
    Route::get('published-cards', [AdminCardController::class, 'published'])->name('cards.published');
    Route::get('pending-cards', [AdminCardController::class, 'pendingApproval'])->name('cards.pending');
    Route::post('cards/{card}/approve', [AdminCardController::class, 'approve'])->name('cards.approve');
    Route::post('cards/{card}/reject', [AdminCardController::class, 'reject'])->name('cards.reject');
    Route::post('cards/{card}/toggle-published', [AdminCardController::class, 'togglePublished'])->name('cards.toggle-published');
    Route::get('cards/{card}/preview', [AdminCardController::class, 'preview'])->name('cards.preview');

    // Settings and Profile
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('profile');

    // Preferences
    Route::get('/preferences', [AdminPreferenceController::class, 'index'])->name('preferences.index');
    Route::post('/preferences', [AdminPreferenceController::class, 'update'])->name('preferences.update');
    Route::post('/preferences/reset', [AdminPreferenceController::class, 'reset'])->name('preferences.reset');
    Route::get('/preferences/json', [AdminPreferenceController::class, 'json'])->name('preferences.json');
});

// User routes
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'userDashboard'])->name('dashboard');

    // Wedding Card Management
    Route::resource('cards', UserCardController::class)->parameters(['cards' => 'card']);
    Route::get('cards/{card}/preview', [UserCardController::class, 'preview'])->name('cards.preview');
    Route::post('cards/{card}/toggle-published', [UserCardController::class, 'togglePublished'])->name('cards.toggle-published');
    Route::get('cards/{card}/share', [UserCardController::class, 'share'])->name('cards.share');
    Route::get('cards/{card}/analytics', [UserCardController::class, 'analytics'])->name('cards.analytics');
    Route::get('cards/{card}/rsvps', [UserCardController::class, 'rsvps'])->name('cards.rsvps');
    Route::get('templates/{template}/preview', [UserCardController::class, 'templatePreview'])->name('templates.preview');
    Route::get('templates/{template}/data', [UserCardController::class, 'getTemplateData'])->name('templates.data');

    // Preferences
    Route::get('/preferences', [UserPreferenceController::class, 'index'])->name('preferences.index');
    Route::post('/preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');
    Route::post('/preferences/reset', [UserPreferenceController::class, 'reset'])->name('preferences.reset');
    Route::get('/preferences/json', [UserPreferenceController::class, 'json'])->name('preferences.json');
});

// Public wedding card view (no authentication required)
Route::get('/wedding-card/{unique_url}', [\App\Http\Controllers\WeddingCardViewController::class, 'show'])->name('wedding-card.view');

// RSVP routes (no authentication required)
Route::post('/wedding-card/{unique_url}/rsvp', [\App\Http\Controllers\RsvpController::class, 'store'])->name('rsvp.store');
Route::put('/wedding-card/{unique_url}/rsvp/{rsvp}', [\App\Http\Controllers\RsvpController::class, 'update'])->name('rsvp.update');
Route::post('/wedding-card/{unique_url}/rsvp/check-email', [\App\Http\Controllers\RsvpController::class, 'checkEmail'])->name('rsvp.check-email');

// Analytics tracking route (no authentication required)
Route::post('/analytics/track', [\App\Http\Controllers\WeddingCardViewController::class, 'track'])->name('analytics.track');

// Gift payment routes (no authentication required)
Route::prefix('gift')->name('gift.')->group(function () {
    Route::post('/create', [GiftController::class, 'create'])->name('create');
    Route::post('/callback', [GiftController::class, 'callback'])->name('callback');
    Route::get('/{gift}/return', [GiftController::class, 'return'])->name('return');
    Route::get('/{gift}/receipt', [GiftController::class, 'receipt'])->name('receipt');
    Route::get('/{gift}/status', [GiftController::class, 'status'])->name('status');
});

// Redirect authenticated users based on their type
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});

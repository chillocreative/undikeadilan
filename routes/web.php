<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\NominationController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [ElectionController::class, 'home'])->name('home');
Route::get('/undi/{slug}', [ElectionController::class, 'show'])->name('election.show');
Route::post('/undi/{slug}/nominate', [NominationController::class, 'store'])->name('nomination.store');
Route::post('/undi/{slug}/vote', [VoteController::class, 'store'])->name('vote.store');
Route::get('/undi/{slug}/keputusan', [ResultController::class, 'show'])->name('result.show');
Route::get('/api/undi/{slug}/keputusan', [ResultController::class, 'data'])->name('result.data');

// Auth routes
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/elections/{slug}', [AdminController::class, 'show'])->name('election');
    Route::post('/elections/{slug}/phase', [AdminController::class, 'updatePhase'])->name('phase');
    Route::get('/elections/{slug}/nominees', [AdminController::class, 'nominees'])->name('nominees');
    Route::post('/elections/{slug}/candidates', [AdminController::class, 'syncCandidates'])->name('candidates.sync');
    Route::post('/elections/{slug}/candidates/add', [AdminController::class, 'addCandidate'])->name('candidate.add');
    Route::put('/elections/{slug}/candidates/{id}', [AdminController::class, 'updateCandidate'])->name('candidate.update');
    Route::delete('/elections/{slug}/candidates/{id}', [AdminController::class, 'deleteCandidate'])->name('candidate.delete');
    Route::post('/elections/{slug}/reset', [AdminController::class, 'resetVotes'])->name('reset');
    Route::get('/elections/{slug}/qr', [AdminController::class, 'qrCode'])->name('qr');
    Route::get('/elections/{slug}/qr/pdf', [AdminController::class, 'qrPdf'])->name('qr.pdf');
});

// Temporary setup route for cPanel deployment (remove after first run)
Route::get('/setup-init', function () {
    if (request('token') !== 'KEADILAN2026') {
        abort(404);
    }

    $output = [];

    // Generate APP_KEY if missing
    if (empty(config('app.key'))) {
        Artisan::call('key:generate', ['--force' => true]);
        $output[] = 'APP_KEY generated.';
    } else {
        $output[] = 'APP_KEY already exists.';
    }

    // Run migrations
    Artisan::call('migrate', ['--force' => true]);
    $output[] = 'Migrations completed: ' . Artisan::output();

    // Run seeders
    Artisan::call('db:seed', ['--force' => true]);
    $output[] = 'Seeding completed: ' . Artisan::output();

    // Clear caches
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    $output[] = 'Caches cleared.';

    // Create storage link
    try {
        Artisan::call('storage:link');
        $output[] = 'Storage linked.';
    } catch (\Exception $e) {
        $output[] = 'Storage link: ' . $e->getMessage();
    }

    return '<pre>' . implode("\n", $output) . "\n\nSetup complete! Remove this route now.</pre>";
});

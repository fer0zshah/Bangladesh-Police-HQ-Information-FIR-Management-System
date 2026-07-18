<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StationController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicWantedCriminalController;
use App\Http\Middleware\CheckRole;
/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Guests & Registered Users)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome'); // Your global landing page
});

// Publicly browse stations and individual public profiles
Route::get('/stations', [StationController::class, 'index'])->name('stations.index');
Route::get('/stations/{station}', [StationController::class, 'show'])->name('stations.show');

// Guests may browse the wanted list; profile details require authentication.
Route::get('/wanted-criminals', [PublicWantedCriminalController::class, 'index'])
    ->name('wanted-criminals.index');
Route::get('/public-officers', [OfficerController::class, 'publicList']);


/*
|--------------------------------------------------------------------------
| CITIZEN PORTAL (Only 'citizen' can access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':citizen'])->prefix('citizen')->group(function () {
    Route::redirect('/my-complaints', '/profile');
    Route::get('/station-options/districts', [App\Http\Controllers\Citizen\ComplaintController::class, 'districts'])
        ->name('citizen.station-options.districts');
    Route::get('/station-options/thanas', [App\Http\Controllers\Citizen\ComplaintController::class, 'thanas'])
        ->name('citizen.station-options.thanas');
    Route::get('/station-options/map', [App\Http\Controllers\Citizen\ComplaintController::class, 'mapStations'])
        ->name('citizen.station-options.map');
    Route::get('/complaints/create', [App\Http\Controllers\Citizen\ComplaintController::class, 'create'])
        ->name('citizen.complaints.create');
    Route::post('/complaints', [App\Http\Controllers\Citizen\ComplaintController::class, 'store'])
        ->name('citizen.complaints.store');
    Route::get('/complaints/{complaint}', [App\Http\Controllers\Citizen\ComplaintController::class, 'show'])
        ->name('citizen.complaints.show');
});

/*|--------------------------------------------------------------------------
| STATION OC DASHBOARD (Only 'station_oc' can access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':station_oc'])->prefix('oc')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Oc\DashboardController::class, 'index'])
        ->name('oc.dashboard');
    Route::get('/complaints', [App\Http\Controllers\Oc\ComplaintController::class, 'index'])
        ->name('oc.complaints.index');
    Route::patch('/complaints/{complaint}/status', [App\Http\Controllers\Oc\ComplaintController::class, 'updateStatus'])
        ->name('oc.complaints.status');
    Route::post('/complaints/{complaint}/escalate', [App\Http\Controllers\Oc\ComplaintController::class, 'escalate'])
        ->name('oc.complaints.escalate');
    Route::get('/cases', [App\Http\Controllers\Oc\CaseController::class, 'index'])->name('oc.cases.index');
    Route::get('/cases/create', [App\Http\Controllers\Oc\CaseController::class, 'create'])->name('oc.cases.create');
    Route::post('/cases', [App\Http\Controllers\Oc\CaseController::class, 'store'])->name('oc.cases.store');
    Route::get('/cases/{case}', [App\Http\Controllers\Oc\CaseController::class, 'show'])->name('oc.cases.show');
    Route::get('/cases/{case}/edit', [App\Http\Controllers\Oc\CaseController::class, 'edit'])->name('oc.cases.edit');
    Route::put('/cases/{case}', [App\Http\Controllers\Oc\CaseController::class, 'update'])->name('oc.cases.update');
    Route::get('/criminals', [App\Http\Controllers\Oc\CriminalController::class, 'index'])->name('oc.criminals.index');
    Route::get('/criminals/create', [App\Http\Controllers\Oc\CriminalController::class, 'create'])->name('oc.criminals.create');
    Route::post('/criminals', [App\Http\Controllers\Oc\CriminalController::class, 'store'])->name('oc.criminals.store');
    Route::get('/criminals/{criminal}', [App\Http\Controllers\Oc\CriminalController::class, 'show'])->name('oc.criminals.show');
    Route::get('/criminals/{criminal}/edit', [App\Http\Controllers\Oc\CriminalController::class, 'edit'])->name('oc.criminals.edit');
    Route::put('/criminals/{criminal}', [App\Http\Controllers\Oc\CriminalController::class, 'update'])->name('oc.criminals.update');
    Route::patch('/criminals/{criminal}/wanted', [App\Http\Controllers\Oc\CriminalController::class, 'toggleWanted'])->name('oc.criminals.wanted');
    Route::post('/criminals/{criminal}/cases', [App\Http\Controllers\Oc\CriminalController::class, 'linkCase'])->name('oc.criminals.cases');
    Route::get('/evidence', [App\Http\Controllers\Oc\EvidenceController::class, 'index'])->name('oc.evidence.index');
    Route::get('/evidence/create', [App\Http\Controllers\Oc\EvidenceController::class, 'create'])->name('oc.evidence.create');
    Route::post('/evidence', [App\Http\Controllers\Oc\EvidenceController::class, 'store'])->name('oc.evidence.store');
    Route::get('/evidence/{evidence}/edit', [App\Http\Controllers\Oc\EvidenceController::class, 'edit'])->name('oc.evidence.edit');
    Route::put('/evidence/{evidence}', [App\Http\Controllers\Oc\EvidenceController::class, 'update'])->name('oc.evidence.update');
    Route::get('/officers', [App\Http\Controllers\Oc\OfficerController::class, 'index'])->name('oc.officers.index');
});

Route::middleware(['auth', CheckRole::class.':metro_head,district_head'])
    ->prefix('command')
    ->name('command.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Command\DashboardController::class, 'index'])
            ->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| SUPER ADMIN HQ (Only 'super_admin' can access)
| Controllers live in App\Http\Controllers\Admin\ namespace
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Phase 1: Dashboard overview ───────────────────────────────
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        // ── Phase 2: Station CRUD ─────────────────────────────────────
        Route::patch('stations/{station}/toggle-status',
            [App\Http\Controllers\Admin\StationController::class, 'toggleStatus'])
            ->name('stations.toggle-status');
        Route::resource('stations', App\Http\Controllers\Admin\StationController::class)
            ->except('destroy')
            ->names('stations');

        // ── Phase 2: Officer CRUD + OC toggle ────────────────────────
        Route::resource('officers', App\Http\Controllers\Admin\OfficerController::class)
            ->names('officers');
        Route::patch('officers/{officer}/toggle-oc',
            [App\Http\Controllers\Admin\OfficerController::class, 'toggleOc'])
            ->name('officers.toggleOc');

        // ── Phase 3: Criminal registry (read + edit + wanted toggle) ──
        Route::get('criminals',
            [App\Http\Controllers\Admin\CriminalController::class, 'index'])
            ->name('criminals.index');
        Route::get('criminals/{criminal}/edit',
            [App\Http\Controllers\Admin\CriminalController::class, 'edit'])
            ->name('criminals.edit');
        Route::patch('criminals/{criminal}',
            [App\Http\Controllers\Admin\CriminalController::class, 'update'])
            ->name('criminals.update');
        Route::patch('criminals/{criminal}/toggle-wanted',
            [App\Http\Controllers\Admin\CriminalController::class, 'toggleWanted'])
            ->name('criminals.toggleWanted');

        // ── Phase 3: Cases — read-only with filters ───────────────────
        Route::get('cases',
            [App\Http\Controllers\Admin\CaseController::class, 'index'])
            ->name('cases.index');

        // ── Phase 3: Complaints — read-only with filters ──────────────
        Route::get('complaints',
            [App\Http\Controllers\Admin\ComplaintController::class, 'index'])
            ->name('complaints.index');

        // ── Phase 4: Analytics ────────────────────────────────────────
        Route::get('analytics',
            [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])
            ->name('analytics');
    });

/*
|--------------------------------------------------------------------------
| 2. CENTRAL DYNAMIC DASHBOARD REDIRECT
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user) {
        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'station_oc') {
            return redirect()->route('oc.dashboard');
        }
    }
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| 3. AUTHENTICATED PROFILE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/wanted-criminals/{criminal}', [PublicWantedCriminalController::class, 'show'])
        ->name('wanted-criminals.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

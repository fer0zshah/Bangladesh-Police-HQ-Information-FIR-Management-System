<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StationController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\ProfileController;
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
Route::get('/stations', [StationController::class, 'index']);
Route::get('/stations/{id}', [StationController::class, 'show']); // Profile with crime stats & public case list

// Publicly viewable records (restricted fields hidden in the views)
Route::get('/public-cases', [StationController::class, 'publicCases']); 
Route::get('/public-officers', [OfficerController::class, 'publicList']);


/*
|--------------------------------------------------------------------------
| CITIZEN PORTAL (Only 'citizen' can access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':citizen'])->prefix('citizen')->group(function () {
    Route::get('/my-complaints', function () {
        return view('citizen.dashboard');
    })->name('citizen.dashboard');
});

/*|--------------------------------------------------------------------------
| STATION OC DASHBOARD (Only 'station_oc' can access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':station_oc'])->prefix('oc')->group(function () {
    Route::get('/dashboard', function () {
        return view('oc.dashboard');
    })->name('oc.dashboard');
    // You will put the OC's manage complaints/cases routes here later!
});

/*
|--------------------------------------------------------------------------
| SUPER ADMIN HQ DASHBOARD (Only 'super_admin' can access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', CheckRole::class.':super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
       return view('admin.dashboard');
    })->name('admin.dashboard');
    // You will put the promote/demote officer routes here later!
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
            return redirect('/admin/dashboard');
        } elseif ($user->role === 'station_oc') {
            return redirect('/oc/dashboard');
        }
    }
    return redirect('/citizen/my-complaints');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

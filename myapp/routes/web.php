<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\StationController;

Route::get('/stations', [StationController::class, 'index']);

use App\Http\Controllers\OfficerController;

Route::get('/officers', [OfficerController::class, 'index']);

use App\Http\Controllers\CaseController;

Route::get('/cases', [CaseController::class, 'index']);

use App\Http\Controllers\CriminalController;

Route::get('/criminals', [CriminalController::class, 'index']);
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Officer;

class OfficerController extends Controller
{
    public function index()
    {
        // Eager load the 'station' relationship to avoid N+1 database query issues (Professors love this)
        $officers = Officer::with('station')->get();
        
        return view('officers.index', compact('officers'));
    }
}
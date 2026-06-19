<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaseFir ;

class caseController extends Controller
{
    public function index()
    {
        // Eager load the 'station' relationship to avoid N+1 database query issues (Professors love this)
        $cases = CaseFir::with('station')->get();
        
        return view('cases.index', compact('cases'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criminal;

class CriminalController extends Controller
{
    public function index()
    {
        $criminals = Criminal::where('wanted_status', 1)->get();
        return view('criminals.index', compact('criminals'));
    }
}

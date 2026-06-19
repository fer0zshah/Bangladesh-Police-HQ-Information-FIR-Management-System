<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Station;

class StationController extends Controller
{
    public function index()
    {
        // Fetch all stations from the database
        $stations = Station::all();
        
        // Send the data to a view file named 'index' inside a 'stations' folder
        return view('stations.index', compact('stations'));
    }
}
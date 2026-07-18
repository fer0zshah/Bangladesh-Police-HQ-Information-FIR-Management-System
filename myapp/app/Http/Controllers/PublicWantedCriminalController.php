<?php

namespace App\Http\Controllers;

use App\Models\Criminal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicWantedCriminalController extends Controller
{
    public function index(Request $request): View
    {
        $criminals = Criminal::query()
            ->where('wanted_status', true)
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search')->toString().'%';

                $query->where(fn ($query) => $query
                    ->where('name', 'like', $term)
                    ->orWhere('alias', 'like', $term));
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('wanted-criminals.index', compact('criminals'));
    }

    public function show(Criminal $criminal): View
    {
        abort_unless((bool) $criminal->wanted_status, 404);

        return view('wanted-criminals.show', compact('criminal'));
    }
}

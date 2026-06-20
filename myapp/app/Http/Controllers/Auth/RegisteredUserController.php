<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *

     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate all incoming data, including your custom fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'nid_number' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Save the user to the database with the 'citizen' role automatically assigned
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nid_number' => $request->nid_number,
            'phone' => $request->phone,
            'role' => 'citizen', // Hardcoded here to ensure public registrants only get citizen access
            'password' => Hash::make($request->password),
        ]);

        // 3. Fire the registered event and log them in
        event(new Registered($user));

        Auth::login($user);

        // 4. Redirect them to the dashboard
        return redirect('/citizen/my-complaints');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show()
    {
        if (session()->has('user')) {
             return session('user')['type'] === 'owner' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('index');
        }
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email est requis.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Mot de passe est requis.',
        ]);

        // MOCK AUTHENTICATION: Using hardcoded owner credentials for testing 
        // as data comes from an external API (avoiding database tables).
        if ($request->email === 'admin@promatch.ma' && $request->password === 'password') {
            
            $user = [
                'id' => 1,
                'email' => 'admin@promatch.ma',
                'name' => 'Adnane',
                'role' => 'admin',
                'type' => 'owner'
            ];
            
            // Set session user data for consistency across views
            session(['user' => $user]);

            return redirect()->route('admin.dashboard');
        }

        // Add more logic here to call external APIs for authentication
        // Sample for common users
        if ($request->email === 'user@example.com' && $request->password === 'password') {
            $user = [
                'id' => 2,
                'email' => 'user@example.com',
                'name' => 'Joueur Test',
                'type' => 'tenant'
            ];
            session(['user' => $user]);
            return redirect()->route('index');
        }

        return back()->with('error', 'Identifiants incorrects (Essayez admin@promatch.ma/password).')->withInput($request->only('email'));
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('user');
        
        return redirect()->route('index');
    }
}

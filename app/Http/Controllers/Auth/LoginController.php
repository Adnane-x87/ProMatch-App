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
        // REAL AUTHENTICATION: Call the Backend API to get a Sanctum Token
        try {
            $apiUrl = env('API_URL', 'http://localhost:8000/api');
            $response = \Illuminate\Support\Facades\Http::post($apiUrl . '/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Save user and token in session
                session([
                    'user' => $data['data'],
                    'api_token' => $data['token'] ?? null
                ]);

                return redirect()->route('admin.dashboard');
            }
        } catch (\Exception $e) {
            // Fallback to error if API is down
            return back()->with('error', 'Erreur de connexion à l\'API.')->withInput($request->only('email'));
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

    /**
     * Bypass login for testing (Owner Access).
     */
    public function bypass()
    {
        try {
            $apiUrl = env('API_URL', 'http://localhost:8000/api');
            $response = \Illuminate\Support\Facades\Http::post($apiUrl . '/login', [
                'email' => 'admin@promatch.ma',
                'password' => 'password',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                session([
                    'user' => $data['data'],
                    'api_token' => $data['token'] ?? null
                ]);
            } else {
                // Fallback dummy session to allow access if API returns error
                session([
                    'user' => [
                        'id' => 1,
                        'first_name' => 'Admin',
                        'last_name' => 'Bypass',
                        'email' => 'admin@promatch.ma',
                        'type' => 'owner'
                    ]
                ]);
            }
        } catch (\Exception $e) {
            // Fallback dummy session and token for local testing if API is unreachable
            session([
                'user' => [
                    'id' => 1,
                    'first_name' => 'Admin',
                    'last_name' => 'Offline',
                    'email' => 'admin@promatch.ma',
                    'type' => 'owner'
                ],
                'api_token' => 'bypass-token-123'
            ]);
        }

        return redirect()->route('admin.dashboard');
    }
}

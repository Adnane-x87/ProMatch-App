<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        Log::info('Login request details:', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'email' => $request->input('email'),
        ]);

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email est requis.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Mot de passe est requis.',
        ]);

        try {
            $apiUrl = $this->apiUrl($request);
            Log::info('Sending POST to API URL: ' . $apiUrl . '/login');

            $response = Http::timeout(15)->acceptJson()->post($apiUrl . '/login', [
                'email'    => $request->email,
                'password' => $request->password,
            ]);

            Log::info('API response received.', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $userData = $this->normalizeApiUser($data['data'] ?? []);

                $apiToken = $data['token'] ?? $data['access_token'] ?? null;

                session([
                    'user'      => $userData,
                    'api_token' => $apiToken,
                ]);
                session()->save();

                Log::info('Session saved. type=' . $userData['type'] . ' user=' . ($userData['email'] ?? '?'));

                if ($userData['type'] === 'owner') {
                    Log::info('Redirecting to admin.dashboard');
                    if ($request->expectsJson()) {
                        return response()->json(['redirect' => route('admin.dashboard')]);
                    }
                    return redirect()->route('admin.dashboard');
                }

                Log::info('Redirecting to index');
                if ($request->expectsJson()) {
                    return response()->json(['redirect' => route('index')]);
                }
                return redirect()->route('index');

            } else {
                $message = $this->apiErrorMessage($response, 'Identifiants incorrects.');
                Log::warning('API login failed: ' . $message);
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], $response->status());
                }
                return back()->with('error', $message)->withInput($request->only('email'));
            }
        } catch (\Exception $e) {
            Log::error('API login exception: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Erreur de connexion : ' . $e->getMessage()], 502);
            }
            return back()->with('error', 'Erreur de connexion : ' . $e->getMessage())->withInput($request->only('email'));
        }
    }


    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (session()->has('user')) {
             return session('user')['type'] === 'owner' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('index');
        }
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'type' => ['required', 'string', 'in:tenant,owner,employee'],
        ], [
            'first_name.required' => 'Le prénom est requis.',
            'last_name.required' => 'Le nom est requis.',
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez entrer un email valide.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'type.required' => 'Le rôle est requis.',
        ]);

        try {
            $apiUrl = $this->apiUrl($request);
            
            // Post registration request to backend API
            $response = Http::timeout(10)->acceptJson()->post($apiUrl . '/register', [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'type' => $request->type,
                'role' => $request->type,
            ]);

            if ($response->successful()) {
                // Automatically log the user in after registration
                $loginResponse = Http::timeout(10)->acceptJson()->post($apiUrl . '/login', [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

                if ($loginResponse->successful()) {
                    $loginData = $loginResponse->json();
                    $userData = $this->normalizeApiUser($loginData['data'] ?? [], $request->type);
                    $apiToken = $loginData['token'] ?? $loginData['access_token'] ?? null;

                    session([
                        'user' => $userData,
                        'api_token' => $apiToken
                    ]);

                    if ($userData['type'] === 'owner') {
                        if ($request->expectsJson()) {
                            return response()->json(['redirect' => route('admin.dashboard')], 201);
                        }
                        return redirect()->route('admin.dashboard');
                    }
                    if ($request->expectsJson()) {
                        return response()->json(['redirect' => route('index')], 201);
                    }
                    return redirect()->route('index');
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Votre compte a ete cree. Veuillez vous connecter.',
                        'redirect' => route('login'),
                    ], 201);
                }

                return redirect()->route('login')->with('success', 'Votre compte a été créé. Veuillez vous connecter.');
            } else {
                $message = $this->apiErrorMessage($response, 'Erreur lors de l\'inscription.');
                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], $response->status());
                }
                return back()->with('error', $message)->withInput($request->except('password', 'password_confirmation'));
            }
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Erreur de connexion a l\'API : ' . $e->getMessage()], 502);
            }
            return back()->with('error', 'Erreur de connexion à l\'API : ' . $e->getMessage())->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $token = session('api_token');
        if ($token) {
            try {
                $apiUrl = $this->apiUrl($request);
                Http::timeout(10)->withToken($token)->post($apiUrl . '/logout');
            } catch (\Exception $e) {
                // Ignore API failure on logout, proceed to local session clear
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('user');
        session()->forget('api_token');
        
        return redirect()->route('index');
    }

    /**
     * Bypass login for testing (Owner Access).
     */
    public function bypass()
    {
        try {
            $apiUrl = $this->apiUrl(request());
            $response = Http::timeout(10)->acceptJson()->post($apiUrl . '/login', [
                'email' => 'adnane@promatch.com',
                'password' => 'password',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $apiToken = $data['token'] ?? $data['access_token'] ?? null;

                session([
                    'user' => $data['data'],
                    'api_token' => $apiToken
                ]);
            } else {
                // Fallback dummy session to allow access if API returns error
                session([
                    'user' => [
                        'id' => 1,
                        'first_name' => 'Admin',
                        'last_name' => 'Bypass',
                        'email' => 'adnane@promatch.com',
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
                    'email' => 'adnane@promatch.com',
                    'type' => 'owner'
                ],
                'api_token' => 'bypass-token-123'
            ]);
        }

        return redirect()->route('admin.dashboard');
    }


    private function apiErrorMessage($response, string $fallback): string
    {
        $errorData = $response->json();

        if (is_array($errorData) && !empty($errorData['message'])) {
            return $errorData['message'];
        }

        $body = trim(strip_tags($response->body()));
        $details = $body !== '' ? ' - ' . mb_substr($body, 0, 180) : '';

        return $fallback . ' (API HTTP ' . $response->status() . $details . ')';
    }

    private function normalizeApiUser(array $userData, ?string $fallbackType = null): array
    {
        $roles = array_column($userData['roles'] ?? [], 'name');

        $isOwner = !empty($userData['owner']) || in_array('owner', $roles, true);
        $isEmployee = !empty($userData['employee']) || in_array('employee', $roles, true);

        $userData['type'] = $isOwner
            ? 'owner'
            : ($isEmployee ? 'employee' : ($fallbackType ?: 'tenant'));

        return $userData;
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show()
    {
        if (session()->has('user')) {
            return redirect()->route($this->homeRouteForType(session('user.type')));
        }

        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        Log::info('LOGIN REQUEST', [
            'email' => $request->input('email'),
            'password_present' => $request->filled('password'),
            'password_length' => strlen((string) $request->input('password')),
        ]);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Entrez votre email.',
            'email.email' => 'Entrez un email valide.',
            'password.required' => 'Entrez votre mot de passe.',
        ]);

        try {
            $apiUrl = $this->apiUrl($request);

            $credentials = [
                'email' => trim((string) $validated['email']),
                'password' => (string) $validated['password'],
            ];

            $response = Http::timeout(15)
                ->acceptJson()
                ->post($apiUrl . '/login', $credentials);

            if ($response->successful()) {
                $data = $response->json();
                $userData = $this->normalizeApiUser($data['data']['user'] ?? $data['user'] ?? $data['data'] ?? []);

                $apiToken = $data['token'] ?? $data['access_token'] ?? null;

                session([
                    'user' => $userData,
                    'api_token' => $apiToken,
                ]);
                session()->save();

                return $this->redirectForUser($request, $userData);
            }

            $message = $this->apiErrorMessage($response, 'Identifiants incorrects.');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => $this->apiValidationErrors($response),
                ], $response->status());
            }

            return back()
                ->withErrors($this->apiValidationErrors($response) ?: ['email' => $message])
                ->withInput($request->only('email'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'API indisponible. Vérifiez que le serveur ProMatch est lancé.'], 502);
            }

            return back()
                ->with('error', 'API indisponible. Vérifiez que le serveur ProMatch est lancé.')
                ->withInput($request->only('email'));
        }
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (session()->has('user')) {
            return redirect()->route($this->homeRouteForType(session('user.type')));
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

            $response = Http::timeout(10)->acceptJson()->post($apiUrl . '/register', [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'type' => $request->type,
                'role' => $request->type,
            ]);

            if ($response->successful()) {
                $loginResponse = Http::timeout(10)->acceptJson()->post($apiUrl . '/login', [
                    'email' => $request->email,
                    'password' => $request->password,
                ]);

                if ($loginResponse->successful()) {
                    $loginData = $loginResponse->json();
                    $userData = $this->normalizeApiUser($loginData['data']['user'] ?? $loginData['user'] ?? $loginData['data'] ?? [], $request->type);
                    $apiToken = $loginData['token'] ?? $loginData['access_token'] ?? null;

                    session([
                        'user' => $userData,
                        'api_token' => $apiToken,
                    ]);

                    return $this->redirectForUser($request, $userData, 201);
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Votre compte a été créé. Veuillez vous connecter.',
                        'redirect' => route('login'),
                    ], 201);
                }

                return redirect()->route('login')->with('success', 'Votre compte a été créé. Veuillez vous connecter.');
            }

            $message = $this->apiErrorMessage($response, 'Erreur lors de l\'inscription.');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], $response->status());
            }

            return back()
                ->with('error', $message)
                ->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'API indisponible. Vérifiez que le serveur ProMatch est lancé.'], 502);
            }

            return back()
                ->with('error', 'API indisponible. Vérifiez que le serveur ProMatch est lancé.')
                ->withInput($request->except('password', 'password_confirmation'));
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
                // Ignore API failure on logout, proceed to local session clear.
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('user');
        session()->forget('api_token');

        return redirect()->route('index');
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

    private function apiValidationErrors($response): array
    {
        $errorData = $response->json();

        return is_array($errorData) && is_array($errorData['errors'] ?? null)
            ? $errorData['errors']
            : [];
    }

    private function redirectForUser(Request $request, array $userData, int $status = 200)
    {
        $route = $this->homeRouteForType($userData['type'] ?? null);

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route($route)], $status);
        }

        return redirect()->route($route);
    }

    private function homeRouteForType(?string $type): string
    {
        return match ($type) {
            'owner' => 'admin.dashboard',
            'employee' => Route::has('employee.dashboard') ? 'employee.dashboard' : 'index',
            default => 'index',
        };
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

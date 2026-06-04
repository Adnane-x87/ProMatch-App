<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['type']) && !isset($data['role'])) {
                $data['role'] = $data['type'];
            }

            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/register', $data);

            return $this->proxyJsonResponse($response, 201);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function login(Request $request)
    {
        try {
            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/login', $request->all());

            if (!$response->successful()) {
                return $this->proxyJsonResponse($response);
            }

            $data = $response->json();

            return response()->json([
                'success' => true,
                'message' => $data['message'] ?? 'Connexion reussie',
                'data' => $data['data'] ?? [],
                'token' => $data['token'] ?? $data['access_token'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function logout(Request $request)
    {
        try {
            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/logout');

            return $this->proxyJsonResponse($response, null, ['message' => 'Logged out successfully']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

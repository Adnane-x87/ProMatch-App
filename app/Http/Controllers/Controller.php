<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract class Controller
{
    protected function apiUrl(Request $request = null): string
    {
        $request = $request ?? request();
        // Accept API_URL as either:
        // - full API path:  http://host:port/api
        // - app base URL:   http://host:port
        // In both cases we normalize to a URL ending with /api.
        $configuredUrl = env('API_URL')
            ?? env('EXTERNAL_API_URL')
            ?? 'http://127.0.0.1:8000';

        $apiUrl = rtrim($configuredUrl, '/');
        $apiPath = parse_url($apiUrl, PHP_URL_PATH) ?? '';
        if (!str_ends_with($apiPath, '/api')) {
            $apiUrl .= '/api';
        }

        if ($request && parse_url($apiUrl, PHP_URL_HOST) === '10.0.2.2') {
            $isAndroid = str_contains(strtolower($request->userAgent() ?? ''), 'android');
            if (!$isAndroid && in_array($request->getHost(), ['localhost', '127.0.0.1'], true)) {
                $apiUrl = str_replace('10.0.2.2', '127.0.0.1', $apiUrl);
            }
        }

        $this->guardAgainstSelfReferentialApiUrl($apiUrl, $request);

        return $apiUrl;
    }

    protected function guardAgainstSelfReferentialApiUrl(string $apiUrl, Request $request): void
    {
        $apiHost = parse_url($apiUrl, PHP_URL_HOST);
        $apiPort = parse_url($apiUrl, PHP_URL_PORT);
        $apiScheme = parse_url($apiUrl, PHP_URL_SCHEME) ?: $request->getScheme();
        $apiPath = parse_url($apiUrl, PHP_URL_PATH) ?? '';

        $requestHost = $request->getHost();
        $requestPort = $request->getPort();
        $defaultPort = $apiScheme === 'https' ? 443 : 80;

        $normalizedApiPort = $apiPort ?? $defaultPort;
        $loopbackHosts = ['localhost', '127.0.0.1', '::1'];
        $sameLoopbackHost = in_array($apiHost, $loopbackHosts, true)
            && in_array($requestHost, $loopbackHosts, true);

        if (
            $sameLoopbackHost
            && (int) $normalizedApiPort === (int) $requestPort
            && str_starts_with($request->path(), 'api/')
            && str_ends_with(rtrim($apiPath, '/'), '/api')
        ) {
            throw new \RuntimeException(
                'Configuration API invalide: API_URL pointe vers cette application. ' .
                'Lancez le backend sur un autre port ou mettez API_URL vers le vrai serveur API.'
            );
        }
    }

    protected function apiClient(Request $request = null)
    {
        $client = Http::timeout(10)->acceptJson();

        // Prefer the session token (set after login).
        // Fall back to the Bearer token sent by the JS frontend (Authorization header),
        // so that direct fetch() calls from the browser are also proxied correctly.
        $token = session('api_token') ?? (($request ?? request())->bearerToken());

        if ($token) {
            $client = $client->withToken($token);
        }

        return $client;
    }

    protected function proxyJsonResponse(ClientResponse $response, ?int $successStatus = null, array $fallback = []): JsonResponse
    {
        $payload = $response->json();

        if ($response->successful()) {
            return response()->json(
                is_array($payload) ? $payload : array_merge(['success' => true], $fallback),
                $successStatus ?? $response->status()
            );
        }

        $body = trim(strip_tags($response->body()));
        $message = is_array($payload) && !empty($payload['message'])
            ? $payload['message']
            : ($body !== '' ? mb_substr($body, 0, 220) : 'Erreur API.');

        $error = [
            'success' => false,
            'message' => $message,
        ];

        if (is_array($payload) && !empty($payload['errors'])) {
            $error['errors'] = $payload['errors'];
        }

        return response()->json($error, $response->status());
    }

    protected function apiUnavailableResponse(Throwable $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'API indisponible. Verifiez que le serveur ProMatch est lance.',
            'detail' => $exception->getMessage(),
        ], 502);
    }
}

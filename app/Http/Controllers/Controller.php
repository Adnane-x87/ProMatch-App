<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class Controller
{
    protected function apiUrl(Request $request = null): string
    {
        $request = $request ?? request();
        $configuredUrl = config('services.promatch.api_url')
            ?? env('API_URL')
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

        if ($sameLoopbackHost && (int) $normalizedApiPort === (int) $requestPort && str_ends_with(rtrim($apiPath, '/'), '/api')) {
            throw new \RuntimeException(
                'Configuration API invalide: API_URL pointe vers cette application. ' .
                'Lancez le backend sur un autre port ou mettez API_URL vers le vrai serveur API.'
            );
        }
    }

    protected function apiClient(Request $request = null)
    {
        $request = $request ?? request();
        $client = Http::timeout(10)->acceptJson();

        $apiHost = parse_url($this->apiUrl($request), PHP_URL_HOST);

        if ($apiHost) {
            $client = $client->withCookies($request->cookies->all(), $apiHost);
        }

        $token = session('api_token') ?? $request->bearerToken();

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

        if (is_array($payload) && !empty($payload['errors']) && is_array($payload['errors'])) {
            $firstError = collect($payload['errors'])->flatten()->first();

            if ($firstError) {
                $message = $message !== 'Invalid reservation data'
                    ? $message . ': ' . $firstError
                    : $firstError;
            }
        }

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
        $payload = [
            'success' => false,
            'message' => 'API indisponible. Vérifiez que le serveur ProMatch est lancé.',
        ];

        if (config('app.debug')) {
            $payload['detail'] = $exception->getMessage();
        }

        return response()->json($payload, 502);
    }
}

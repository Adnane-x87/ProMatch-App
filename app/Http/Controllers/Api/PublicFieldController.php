<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicFieldController extends Controller
{
    /**
     * List publicly visible fields, optionally filtered by search query.
     * GET /api/public-fields?query=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/public-fields', array_filter([
                'query' => $request->query('query')
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    /**
     * Show a single public field with its time slots.
     * GET /api/public-fields/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/public-fields/' . $id);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

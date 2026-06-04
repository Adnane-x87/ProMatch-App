<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get aggregated dashboard statistics.
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/dashboard/stats');

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    /**
     * Get all time slots, filtered by date / field_id.
     * GET /api/dashboard/slots?date=&field_id=
     */
    public function getSlots(Request $request): JsonResponse
    {
        $filters = array_filter([
            'date'     => $request->query('date'),
            'field_id' => $request->query('field_id'),
        ]);

        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/dashboard/slots', $filters);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    /**
     * Create a new time slot.
     * POST /api/dashboard/slots
     */
    public function storeSlot(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/dashboard/slots', $request->all());

            return $this->proxyJsonResponse($response, 201);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    /**
     * Update an existing time slot.
     * PUT /api/dashboard/slots/{id}
     */
    public function updateSlot(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->put($this->apiUrl($request) . '/dashboard/slots/' . $id, $request->all());

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    /**
     * Delete a time slot.
     * DELETE /api/dashboard/slots/{id}
     */
    public function destroySlot(int $id): JsonResponse
    {
        try {
            $response = $this->apiClient()->delete($this->apiUrl() . '/dashboard/slots/' . $id);

            return $this->proxyJsonResponse($response, null, ['message' => 'Slot deleted successfully']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

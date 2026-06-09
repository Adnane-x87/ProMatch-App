<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MobileController extends Controller
{
    public function useCase(): JsonResponse
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/mobile/use-case');

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function fields(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/mobile/fields', array_filter([
                'query' => $request->query('query'),
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function fieldDetails(int $id): JsonResponse
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/mobile/fields/' . $id);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $request->merge([
            'field_id' => $request->input('field_id')
                ?? $request->input('fieldId')
                ?? $request->input('terrain_id')
                ?? $request->input('terrainId'),
        ]);

        $request->validate([
            'field_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/mobile/available-slots', [
                'field_id' => $request->integer('field_id'),
                'date' => $request->query('date') ?? $request->input('date'),
            ]);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function storeReservation(Request $request): JsonResponse
    {
        try {
            Log::info('RESERVATION ROUTE HIT', [
                'route' => optional($request->route())->getName(),
                'path' => $request->path(),
                'method' => $request->method(),
                'all' => $request->except(['cni_image']),
            ]);

            $data = $this->reservationPayload($request);
            $client = $this->apiClient($request);

            if ($request->hasFile('cni_image')) {
                $cni = $request->file('cni_image');
                $client = $client->attach('cni_image', file_get_contents($cni->path()), $cni->getClientOriginalName());
            }

            $response = $client->post($this->apiUrl($request) . '/mobile/reservations', $data);

            return $this->proxyJsonResponse($response, 201, ['message' => 'Reservation created successfully']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function myReservations(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/mobile/reservations', array_filter([
                'status' => $request->query('status'),
                'date' => $request->query('date'),
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function sync(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/mobile/sync', array_filter([
                'query' => $request->query('query'),
                'status' => $request->query('status'),
                'date' => $request->query('date'),
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function ownerReservations(Request $request): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/mobile/admin/reservations', array_filter([
                'status' => $request->query('status'),
                'date' => $request->query('date'),
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function stats(): JsonResponse
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/mobile/admin/stats');

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function validateReservation(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->apiClient($request)->put($this->apiUrl($request) . '/mobile/admin/reservations/' . $id . '/validate', [
                'status' => $request->input('status', 'APPROVED'),
            ]);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    private function reservationPayload(Request $request): array
    {
        $data = $request->except(['cni_image', 'cni_image_base64']);

        $data['field_id'] = $data['field_id']
            ?? $data['terrain_id']
            ?? $data['fieldId']
            ?? $data['terrainId']
            ?? null;

        $data['time_slot_id'] = $data['time_slot_id']
            ?? $data['timeSlotId']
            ?? $data['slot_id']
            ?? $data['slotId']
            ?? null;

        $data['date'] = $data['date']
            ?? $data['request_date']
            ?? $data['reservation_date']
            ?? $data['reservationDate']
            ?? null;

        $data['selected_time'] = $data['selected_time']
            ?? $data['selectedTime']
            ?? $data['start_time']
            ?? $data['startTime']
            ?? $data['time']
            ?? null;

        if (isset($data['time_slot_id']) && (int) $data['time_slot_id'] >= 9000) {
            unset($data['time_slot_id']);
        }

        if (isset($data['selected_time'], $data['date']) && strlen($data['selected_time']) <= 8) {
            $time = strlen($data['selected_time']) === 5 ? $data['selected_time'] . ':00' : $data['selected_time'];
            $data['selected_time'] = $data['date'] . ' ' . $time;
        }

        if ($request->filled('cni_image_base64')) {
            $data['cni_image'] = $request->input('cni_image_base64');
        } elseif ($request->filled('cni_image') && !$request->hasFile('cni_image')) {
            $data['cni_image'] = $request->input('cni_image');
        }

        return array_filter($data, fn ($value) => $value !== null);
    }
}

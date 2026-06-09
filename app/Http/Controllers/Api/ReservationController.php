<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/reservations', array_filter([
                'status' => $request->query('status'),
                'date' => $request->query('date'),
            ]));

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->except(['cni_image', 'cni_image_base64']);

            // Normalize terrain_id -> field_id (for mobile app compatibility)
            if (empty($data['field_id']) && !empty($data['terrain_id'])) {
                $data['field_id'] = $data['terrain_id'];
            }

            // Early validation: return a friendly error if field_id is missing
            $fieldId = $data['field_id'] ?? null;
            if (empty($fieldId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => [
                        'field_id' => ['Veuillez sélectionner un terrain.'],
                    ],
                ], 422);
            }

            // Normalize time_slot_id (IDs >= 9000 are fake/generated client-side)
            if (isset($data['time_slot_id']) && (int) $data['time_slot_id'] >= 9000) {
                unset($data['time_slot_id']);
            }

            // Expand HH:MM selected_time to a full datetime string
            if (isset($data['selected_time'], $data['date']) && strlen((string) $data['selected_time']) <= 8) {
                $time = strlen($data['selected_time']) === 5 ? $data['selected_time'] . ':00' : $data['selected_time'];
                $data['selected_time'] = $data['date'] . ' ' . $time;
            }

            // Strip out null/empty values so they are not forwarded as JSON null
            $data = array_filter($data, fn($v) => $v !== null && $v !== '');

            $client = $this->apiClient($request);

            if ($request->hasFile('cni_image')) {
                $cni = $request->file('cni_image');
                $response = $client
                    ->attach('cni_image', file_get_contents($cni->path()), $cni->getClientOriginalName())
                    ->post($this->apiUrl($request) . '/reservations', $data);
            } else {
                // Use form-encoded body (not JSON) for consistency with the backend's $request->all()
                $response = $client->asForm()->post($this->apiUrl($request) . '/reservations', $data);
            }

            return $this->proxyJsonResponse($response, 201, ['message' => 'Réservation créée avec succès.']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'field_id' => 'required|integer',
            'date' => 'required|date',
        ]);

        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/available-slots', [
                'field_id' => $request->integer('field_id'),
                'date' => $request->query('date'),
            ]);

            if (!$response->successful()) {
                return $this->proxyJsonResponse($response);
            }

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function validateReservation(Request $request, int $id)
    {
        try {
            $response = $this->apiClient($request)->put($this->apiUrl($request) . '/reservations/' . $id . '/validate', [
                'status' => $request->input('status', 'APPROVED')
            ]);

            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function planning(Request $request)
    {
        try {
            $response = $this->apiClient($request)->get($this->apiUrl($request) . '/planning', array_filter(['date' => $request->query('date')]));
            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

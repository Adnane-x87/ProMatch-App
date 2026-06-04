<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            if ($request->has('terrain_id')) {
                $data['field_id'] = $request->terrain_id;
            }

            if (isset($data['time_slot_id']) && (int) $data['time_slot_id'] >= 9000) {
                $data['time_slot_id'] = null;
            }

            if (isset($data['selected_time'], $data['date']) && strlen($data['selected_time']) <= 8) {
                $time = strlen($data['selected_time']) === 5 ? $data['selected_time'] . ':00' : $data['selected_time'];
                $data['selected_time'] = $data['date'] . ' ' . $time;
            }

            $client = $this->apiClient($request);

            if ($request->hasFile('cni_image')) {
                $cni = $request->file('cni_image');
                $client = $client->attach('cni_image', file_get_contents($cni->path()), $cni->getClientOriginalName());
            }

            $response = $client->post($this->apiUrl($request) . '/reservations', $data);

            return $this->proxyJsonResponse($response, 201, ['message' => 'Reservation created successfully']);
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

            $slots = collect($response->json('data') ?? []);

            if ($slots->isEmpty()) {
                $slots = collect(['08:00', '10:00', '14:00', '16:00', '18:00', '20:00'])
                    ->map(fn($time, $index) => [
                        'id' => 9990 + $index,
                        'field_id' => $request->integer('field_id'),
                        'date' => $request->query('date'),
                        'start_time' => $time,
                        'end_time' => sprintf('%02d:00', ((int) substr($time, 0, 2)) + 2),
                        'status' => 'AVAILABLE',
                    ])->values();
            }

            return response()->json(['success' => true, 'data' => $slots]);
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

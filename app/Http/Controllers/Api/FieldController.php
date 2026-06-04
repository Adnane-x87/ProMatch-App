<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/fields');
            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function store(Request $request)
    {
        try {
            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/fields', $request->all());
            return $this->proxyJsonResponse($response, 201);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function show(int $id)
    {
        try {
            $response = $this->apiClient()->get($this->apiUrl() . '/fields/' . $id);
            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $response = $this->apiClient($request)->put($this->apiUrl($request) . '/fields/' . $id, $request->all());
            return $this->proxyJsonResponse($response);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function destroy(int $id)
    {
        try {
            $response = $this->apiClient()->delete($this->apiUrl() . '/fields/' . $id);
            return $this->proxyJsonResponse($response, null, ['message' => 'Field deleted']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }

    public function addSlots(Request $request, int $id)
    {
        try {
            $response = $this->apiClient($request)->post($this->apiUrl($request) . '/fields/' . $id . '/slots', $request->all());
            return $this->proxyJsonResponse($response, 201);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

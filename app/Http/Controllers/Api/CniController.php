<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CniController extends Controller
{
    /**
     * Upload a CNI document for the authenticated user.
     * POST /api/cni/upload
     * Body: document (file — jpeg, jpg, png, pdf, max 2MB)
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'document' => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        try {
            $file = $request->file('document');

            $response = $this->apiClient($request)
                ->attach('document', file_get_contents($file->path()), $file->getClientOriginalName())
                ->post($this->apiUrl($request) . '/cni/upload');

            return $this->proxyJsonResponse($response, null, ['message' => 'CNI uploaded successfully']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

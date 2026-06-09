<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CniController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'document' => 'required_without_all:cni_image,cni_document|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'cni_image' => 'required_without_all:document,cni_document|file|mimes:jpeg,jpg,png,pdf|max:2048',
            'cni_document' => 'required_without_all:document,cni_image|file|mimes:jpeg,jpg,png,pdf|max:2048',
        ]);

        try {
            $file = $request->file('cni_image')
                ?? $request->file('cni_document')
                ?? $request->file('document');

            $field = $request->hasFile('cni_document') ? 'cni_document' : 'cni_image';
            $endpoint = $request->is('api/mobile/*') ? '/mobile/cni/upload' : '/cni/upload';

            $response = $this->apiClient($request)
                ->attach($field, file_get_contents($file->path()), $file->getClientOriginalName())
                ->post($this->apiUrl($request) . $endpoint);

            return $this->proxyJsonResponse($response, null, ['message' => 'CNI uploaded successfully']);
        } catch (\Throwable $exception) {
            return $this->apiUnavailableResponse($exception);
        }
    }
}

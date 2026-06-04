<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiProxyTest extends TestCase
{
    public function test_public_field_proxy_preserves_backend_errors(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => false,
                'message' => 'Backend exploded',
            ], 500),
        ]);

        $response = $this->getJson('/api/public-fields');

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Backend exploded',
            ]);
    }

    public function test_public_field_proxy_rejects_self_referential_api_url(): void
    {
        config(['app.url' => 'http://127.0.0.1:8000']);
        putenv('API_URL=http://127.0.0.1:8000/api');
        $_ENV['API_URL'] = 'http://127.0.0.1:8000/api';
        $_SERVER['API_URL'] = 'http://127.0.0.1:8000/api';

        $response = $this->getJson('http://127.0.0.1:8000/api/public-fields');

        $response
            ->assertStatus(502)
            ->assertJson([
                'success' => false,
                'message' => 'API indisponible. Verifiez que le serveur ProMatch est lance.',
            ])
            ->assertJsonPath('detail', 'Configuration API invalide: API_URL pointe vers cette application. Lancez le backend sur un autre port ou mettez API_URL vers le vrai serveur API.');

        Http::assertNothingSent();
    }

    public function test_registration_proxy_sends_type_as_role(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => ['id' => 12],
            ], 201),
        ]);

        $this->postJson('/api/register', [
            'first_name' => 'Sara',
            'last_name' => 'Owner',
            'email' => 'sara@example.test',
            'phone' => '0600000000',
            'password' => 'secret123',
            'type' => 'owner',
        ])->assertCreated();

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->method() === 'POST'
                && str_ends_with($request->url(), '/api/register')
                && ($payload['type'] ?? null) === 'owner'
                && ($payload['role'] ?? null) === 'owner';
        });
    }
}

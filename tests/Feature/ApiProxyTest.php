<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApiProxyTest extends TestCase
{
    public function test_api_url_is_normalized_to_api_path(): void
    {
        foreach ([
            'http://127.0.0.1:8000',
            'http://127.0.0.1:8000/api',
        ] as $apiUrl) {
            config(['services.promatch.api_url' => $apiUrl]);

            Http::fake([
                '*' => Http::response(['success' => true, 'data' => []]),
            ]);

            $this->getJson('/api/public-fields')->assertOk();

            Http::assertSent(fn ($request) => $request->url() === 'http://127.0.0.1:8000/api/public-fields');
        }

        config(['services.promatch.api_url' => 'http://10.0.2.2:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this
            ->withHeader('User-Agent', 'Mozilla/5.0 Android')
            ->getJson('/api/public-fields')
            ->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'http://10.0.2.2:8000/api/public-fields');
    }

    public function test_public_field_proxy_preserves_backend_errors(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

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
        config([
            'app.debug' => false,
            'services.promatch.api_url' => 'http://127.0.0.1:8000/api',
        ]);

        $response = $this->getJson('http://127.0.0.1:8000/api/public-fields');

        $response
            ->assertStatus(502)
            ->assertJson([
                'success' => false,
                'message' => 'API indisponible. Vérifiez que le serveur ProMatch est lancé.',
            ])
            ->assertJsonMissingPath('detail');

        Http::assertNothingSent();
    }

    public function test_registration_proxy_sends_type_as_role(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

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

    public function test_public_reservation_proxy_does_not_require_login(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => ['id' => 99]], 201),
        ]);

        $this->post('/api/reservations', [
            'field_id' => '4',
            'date' => '2026-06-09',
            'time_slot_id' => '12',
            'selected_time' => '18:00',
            'first_name' => 'Sara',
            'last_name' => 'Client',
            'email' => 'sara@example.test',
            'phone' => '0600000000',
            'cni_image' => UploadedFile::fake()->createWithContent('cni.jpg', 'fake-image-content'),
        ])->assertCreated();

        Http::assertSent(function ($request) {
            $payload = collect($request->data())->mapWithKeys(fn ($part) => [
                $part['name'] => $part['contents'] ?? null,
            ]);

            return $request->method() === 'POST'
                && $request->url() === 'http://127.0.0.1:8000/api/reservations'
                && !$request->hasHeader('Authorization')
                && $payload->get('field_id') === '4'
                && $payload->get('date') === '2026-06-09'
                && $payload->get('time_slot_id') === '12'
                && $payload->get('selected_time') === '2026-06-09 18:00:00'
                && $payload->get('first_name') === 'Sara'
                && $payload->get('cni_image') === 'fake-image-content';
        });
    }

    public function test_public_reservation_proxy_omits_mock_slot_ids(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => ['id' => 100]], 201),
        ]);

        $this->post('/api/reservations', [
            'field_id' => '1',
            'date' => '2026-06-09',
            'time_slot_id' => '9995',
            'selected_time' => '20:00',
            'first_name' => 'Youssef',
            'last_name' => 'Tazi',
            'email' => 'youssef@example.com',
            'phone' => '0600000004',
        ])->assertCreated();

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->method() === 'POST'
                && $request->url() === 'http://127.0.0.1:8000/api/reservations'
                && !array_key_exists('time_slot_id', $payload)
                && ($payload['selected_time'] ?? null) === '2026-06-09 20:00:00';
        });
    }

    public function test_mobile_public_routes_forward_to_backend_mobile_contract(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this->getJson('/api/mobile/fields?query=Tangier')->assertOk();
        $this->getJson('/api/mobile/fields/8')->assertOk();
        $this->getJson('/api/mobile/available-slots?fieldId=8&date=2026-06-24')->assertOk();

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/fields?query=Tangier');

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/fields/8');

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/available-slots?field_id=8&date=2026-06-24');
    }

    public function test_mobile_reservation_proxy_accepts_mobile_payload_aliases(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => ['id' => 55]], 201),
        ]);

        $this->postJson('/api/mobile/reservations', [
            'terrain_id' => 4,
            'timeSlotId' => 9001,
            'reservationDate' => '2026-06-24',
            'selectedTime' => '18:00',
            'first_name' => 'Ali',
            'last_name' => 'Amrani',
            'email' => 'ali@test.com',
            'phone' => '0611111111',
            'cni_image' => 'data:image/png;base64,ZmFrZQ==',
        ])->assertCreated();

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->method() === 'POST'
                && $request->url() === 'http://127.0.0.1:8000/api/mobile/reservations'
                && ($payload['field_id'] ?? null) === 4
                && !array_key_exists('time_slot_id', $payload)
                && ($payload['date'] ?? null) === '2026-06-24'
                && ($payload['selected_time'] ?? null) === '2026-06-24 18:00:00'
                && ($payload['cni_image'] ?? null) === 'data:image/png;base64,ZmFrZQ==';
        });
    }

    public function test_mobile_protected_routes_forward_bearer_token(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this
            ->withHeader('Authorization', 'Bearer mobile-token')
            ->getJson('/api/mobile/reservations?date=2026-06-24')
            ->assertOk();

        $this
            ->withHeader('Authorization', 'Bearer mobile-token')
            ->getJson('/api/mobile/admin/stats')
            ->assertOk();

        $this
            ->withHeader('Authorization', 'Bearer mobile-token')
            ->putJson('/api/mobile/admin/reservations/55/validate', ['status' => 'APPROVED'])
            ->assertOk();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer mobile-token')
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/reservations?date=2026-06-24');

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer mobile-token')
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/admin/stats');

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer mobile-token')
            && $request->method() === 'PUT'
            && $request->url() === 'http://127.0.0.1:8000/api/mobile/admin/reservations/55/validate');
    }

    public function test_mobile_cni_upload_forwards_expected_file_field(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => ['path' => 'cnis/cni.jpg']]),
        ]);

        $this
            ->withHeader('Authorization', 'Bearer mobile-token')
            ->post('/api/mobile/cni/upload', [
                'cni_image' => UploadedFile::fake()->createWithContent('cni.jpg', 'fake-cni'),
            ])
            ->assertOk();

        Http::assertSent(function ($request) {
            $payload = collect($request->data())->mapWithKeys(fn ($part) => [
                $part['name'] => $part['contents'] ?? null,
            ]);

            return $request->hasHeader('Authorization', 'Bearer mobile-token')
                && $request->url() === 'http://127.0.0.1:8000/api/mobile/cni/upload'
                && $payload->get('cni_image') === 'fake-cni';
        });
    }

    public function test_proxy_validation_errors_use_backend_detail(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response([
                'success' => false,
                'message' => 'Invalid reservation data',
                'errors' => [
                    'selected_time' => ['The selected time is invalid.'],
                ],
            ], 422),
        ]);

        $this->post('/api/reservations', [
            'field_id' => '1',
            'date' => '2026-06-09',
            'selected_time' => 'bad-time',
            'first_name' => 'Youssef',
            'last_name' => 'Tazi',
            'email' => 'youssef@example.com',
            'phone' => '0600000004',
        ])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'The selected time is invalid.',
            ])
            ->assertJsonPath('errors.selected_time.0', 'The selected time is invalid.');
    }

    public function test_protected_admin_proxy_forwards_session_token(): void
    {
        config(['services.promatch.api_url' => 'http://127.0.0.1:8000/api']);

        Http::fake([
            '*' => Http::response(['success' => true, 'data' => []]),
        ]);

        $this
            ->withSession(['api_token' => 'admin-token'])
            ->getJson('/api/fields')
            ->assertOk();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer admin-token'));
    }

    public function test_backend_unavailable_response_is_professional(): void
    {
        config([
            'app.debug' => false,
            'services.promatch.api_url' => 'http://127.0.0.1:8000/api',
        ]);

        Http::fake(fn () => throw new ConnectionException('Connection refused'));

        $this->getJson('/api/public-fields')
            ->assertStatus(502)
            ->assertJson([
                'success' => false,
                'message' => 'API indisponible. Vérifiez que le serveur ProMatch est lancé.',
            ])
            ->assertJsonMissingPath('detail');
    }
}

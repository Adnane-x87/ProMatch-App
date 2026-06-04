<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    public function test_owner_login_stores_api_session_and_redirects_to_dashboard(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'first_name' => 'Adnane',
                    'last_name' => 'Kesksu',
                    'email' => 'adnane@promatch.com',
                    'owner' => ['id' => 1],
                    'tenant' => null,
                    'employee' => null,
                    'roles' => [
                        ['name' => 'owner'],
                    ],
                ],
                'token' => 'test-token',
            ]),
        ]);

        $response = $this->post('/login', [
            'email' => 'adnane@promatch.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertSame('test-token', session('api_token'));
        $this->assertSame('owner', session('user.type'));
    }

    public function test_ajax_login_validation_returns_json_without_redirect(): void
    {
        $this->postJson('/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_ajax_owner_login_returns_redirect_target(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'email' => 'adnane@promatch.com',
                    'owner' => ['id' => 1],
                    'roles' => [
                        ['name' => 'owner'],
                    ],
                ],
                'token' => 'ajax-token',
            ]),
        ]);

        $this->postJson('/login', [
            'email' => 'adnane@promatch.com',
            'password' => 'password',
        ])->assertOk()
            ->assertJson(['redirect' => route('admin.dashboard')]);

        $this->assertSame('ajax-token', session('api_token'));
    }

    public function test_quick_admin_login_uses_seeded_owner_account(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 1,
                    'email' => 'adnane@promatch.com',
                    'owner' => ['id' => 1],
                    'roles' => [
                        ['name' => 'owner'],
                    ],
                ],
                'token' => 'quick-token',
            ]),
        ]);

        $this->get('/login/bypass')->assertRedirect(route('admin.dashboard'));

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return str_ends_with($request->url(), '/api/login')
                && ($payload['email'] ?? null) === 'adnane@promatch.com'
                && ($payload['password'] ?? null) === 'password';
        });
    }
}

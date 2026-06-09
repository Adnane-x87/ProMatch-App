<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    public function test_login_form_uses_app_validation_instead_of_browser_tooltips(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('novalidate', false)
            ->assertSee('x-model="email"', false)
            ->assertSee('x-model="password"', false)
            ->assertSee('@submit.prevent="submit($event)"', false)
            ->assertSee("console.log('LOGIN DATA BEFORE SEND'", false)
            ->assertSee('body: JSON.stringify({', false)
            ->assertDontSee(' required', false)
            ->assertDontSee('Please fill out this field');
    }

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

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/api/login')
                && $request->hasHeader('Content-Type', 'application/json')
                && ($request->data()['email'] ?? null) === 'adnane@promatch.com';
        });
    }

    public function test_logged_in_user_without_type_redirects_safely(): void
    {
        $this
            ->withSession(['user' => ['first_name' => 'Safe']])
            ->get('/login')
            ->assertRedirect(route('index'));
    }

    public function test_ajax_login_validation_returns_json_without_redirect(): void
    {
        Http::fake([
            '*' => Http::response([
                'message' => 'The email field is required.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ], 422),
        ]);

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

    public function test_tenant_login_stores_api_session_and_redirects_home(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 2,
                    'first_name' => 'Sara',
                    'last_name' => 'Client',
                    'email' => 'sara@example.test',
                    'tenant' => ['id' => 7],
                    'roles' => [
                        ['name' => 'tenant'],
                    ],
                ],
                'token' => 'tenant-token',
            ]),
        ]);

        $this->post('/login', [
            'email' => 'sara@example.test',
            'password' => 'password',
        ])->assertRedirect(route('index'));

        $this->assertSame('tenant-token', session('api_token'));
        $this->assertSame('tenant', session('user.type'));
    }

    public function test_employee_login_uses_employee_type_and_redirects_home_when_no_employee_dashboard_exists(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 3,
                    'first_name' => 'Imane',
                    'email' => 'imane@example.test',
                    'employee' => ['id' => 9],
                    'roles' => [
                        ['name' => 'employee'],
                    ],
                ],
                'token' => 'employee-token',
            ]),
        ]);

        $this->post('/login', [
            'email' => 'imane@example.test',
            'password' => 'password',
        ])->assertRedirect(route('index'));

        $this->assertSame('employee-token', session('api_token'));
        $this->assertSame('employee', session('user.type'));
    }

    public function test_register_forwards_password_confirmation_to_backend(): void
    {
        Http::fakeSequence()
            ->push([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => 4,
                        'email' => 'new-user@example.test',
                        'tenant' => ['id' => 12],
                        'roles' => [
                            ['name' => 'tenant'],
                        ],
                    ],
                ],
            ], 201)
            ->push([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => 4,
                        'email' => 'new-user@example.test',
                        'tenant' => ['id' => 12],
                        'roles' => [
                            ['name' => 'tenant'],
                        ],
                    ],
                    'token' => 'new-token',
                ],
                'token' => 'new-token',
            ]);

        $this->post('/register', [
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'new-user@example.test',
            'phone' => '0611111111',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'type' => 'tenant',
        ])->assertRedirect(route('index'));

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return str_ends_with($request->url(), '/api/register')
                && ($payload['password_confirmation'] ?? null) === 'secret123'
                && ($payload['role'] ?? null) === 'tenant';
        });
    }
}

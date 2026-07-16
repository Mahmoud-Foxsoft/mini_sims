<?php

use App\Http\Services\PhoneServiceService;
use App\Models\Admin;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;

uses(RefreshDatabase::class);

it('extracts only one-time-plan services from FoxSims plans', function () {
    $services = PhoneServiceService::extractOneTimePlanServices([
        'data' => [
            'plans' => [
                [
                    'id' => 2,
                    'type' => 2,
                    'name' => 'Subscription Plan',
                    'services' => [
                        [
                            'service_code' => 999,
                            'name' => 'ignored',
                            'amount_cents' => 123,
                        ],
                    ],
                ],
                [
                    'id' => 1,
                    'type' => 1,
                    'name' => 'One Time Plan',
                    'services' => [
                        [
                            'service_code' => 39,
                            'name' => 'microsoft / outlook / hotmail',
                            'amount_cents' => 17,
                        ],
                        [
                            'service_code' => 7,
                            'name' => 'whatsapp',
                            'amount_cents' => 200,
                        ],
                    ],
                ],
            ],
        ],
    ]);

    expect($services->all())->toBe([
        [
            'name' => 'microsoft / outlook / hotmail',
            'code' => '39',
            'price_cents' => 17,
        ],
        [
            'name' => 'whatsapp',
            'code' => '7',
            'price_cents' => 200,
        ],
    ]);
});

it('serves user services as a flat name code price payload', function () {
    Service::create([
        'name' => 'telegram',
        'code' => '5',
        'price_cents' => 220,
    ]);

    Passport::actingAs(User::factory()->create(), ['user-api'], 'api');

    $response = $this->getJson('/api/v1/services');

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.services.0.name', 'telegram')
        ->assertJsonPath('data.services.0.code', '5')
        ->assertJsonPath('data.services.0.price', 2.2);

    expect(array_keys($response->json('data.services.0')))->toBe(['name', 'code', 'price']);
});

it('allows admins to create update and delete services', function () {
    Passport::actingAs(Admin::factory()->create(), ['admin-api'], 'admin');

    $create = $this->postJson('/api/admin/services', [
        'name' => 'telegram',
        'code' => '5',
        'price_cents' => 220,
    ]);

    $create->assertOk()
        ->assertJsonPath('data.service.name', 'telegram')
        ->assertJsonPath('data.service.code', '5')
        ->assertJsonPath('data.service.price_cents', 220);

    $serviceId = $create->json('data.service.id');

    $update = $this->putJson("/api/admin/services/{$serviceId}", [
        'name' => 'telegram premium',
        'price_cents' => 330,
    ]);

    $update->assertOk()
        ->assertJsonPath('data.service.name', 'telegram premium')
        ->assertJsonPath('data.service.price_cents', 330);

    $delete = $this->deleteJson("/api/admin/services/{$serviceId}");

    $delete->assertOk();

    $this->assertDatabaseMissing('services', [
        'id' => $serviceId,
    ]);
});

it('syncs services from the FoxSims plans endpoint', function () {
    Http::fake([
        '*' => Http::response([
            'success' => true,
            'message' => 'Your Plans Retrieved Successfully',
            'data' => [
                'plans' => [
                    [
                        'id' => 1,
                        'type' => 1,
                        'name' => 'One Time Plan',
                        'services' => [
                            [
                                'service_code' => 5,
                                'name' => 'telegram',
                                'amount_cents' => 220,
                            ],
                            [
                                'service_code' => 7,
                                'name' => 'whatsapp',
                                'amount_cents' => 200,
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $this->artisan('services:sync-foxsims')
        ->expectsOutput('FoxSims services synced successfully. Updated 2 services.')
        ->assertExitCode(0);

    $this->assertDatabaseHas('services', [
        'code' => '5',
        'name' => 'telegram',
        'price_cents' => 220,
    ]);

    $this->assertDatabaseHas('services', [
        'code' => '7',
        'name' => 'whatsapp',
        'price_cents' => 200,
    ]);
});

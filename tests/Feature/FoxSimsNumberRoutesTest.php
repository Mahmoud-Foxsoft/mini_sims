<?php

use App\Http\Services\PhoneNumberService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\User;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Facades\UserFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.centralServer.api_url', 'http://localhost:3000');
    Config::set('services.centralServer.phone_numbers_url', '/api/v1/request_service');
    Config::set('services.centralServer.cancel_url', '/api/v1/cancel_number');
    Config::set('services.centralServer.reuse_url', '/api/v1/reuse_number');
});

it('posts request number to the FoxSims request_service route', function () {
    Http::fake([
        '*' => Http::response([
            [
                'request_id' => 'req-1',
                'number' => '+1234567890',
            ],
        ]),
    ]);

    $response = PhoneNumberService::requestPhoneNumbers('39', 2, 10);

    expect($response)->toBe([
        [
            'request_id' => 'req-1',
            'number' => '+1234567890',
        ],
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:3000/api/v1/request_service'
            && $request->method() === 'POST'
            && $request['service'] === '39'
            && $request['numbers_count'] === 2
            && ! isset($request['user_id']);
    });
});

it('posts cancel number to the FoxSims cancel_number route without user id', function () {
    Http::fake([
        '*' => Http::response(['success' => true]),
    ]);

    $response = PhoneNumberService::cancelPhoneNumber('req-cancel');

    expect($response)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:3000/api/v1/cancel_number'
            && $request->method() === 'POST'
            && $request['request_id'] === 'req-cancel'
            && $request['not_working'] === 0
            && ! isset($request['user_id']);
    });
});

it('posts reuse number to the FoxSims reuse_number route', function () {
    Http::fake([
        '*' => Http::response([
            'external_order_id' => 'req-new',
            'price_cents' => 220,
        ]),
    ]);

    $response = PhoneNumberService::reusePhoneNumber('req-old');

    expect($response)->toBe([
        'external_order_id' => 'req-new',
        'price_cents' => 220,
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:3000/api/v1/reuse_number'
            && $request->method() === 'POST'
            && $request['request_id'] === 'req-old';
    });
});

it('uses the FoxSims cancel payload from the cancel controller action', function () {
    Http::fake([
        '*' => Http::response(['success' => true]),
    ]);

    $user = User::factory()->create();
    Passport::actingAs($user, ['user-api'], 'api');

    $order = Order::create([
        'id' => '11111111-1111-1111-1111-111111111111',
        'user_id' => $user->id,
        'total_cent_price' => 220,
        'status' => 'completed',
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'user_id' => $user->id,
        'external_order_id' => 'req-cancel-controller',
        'service_name' => 'telegram',
        'phone_number' => '+1234567890',
        'price_cents' => 220,
        'status' => 'pending',
        'created_at' => now()->subMinutes(5),
        'updated_at' => now()->subMinutes(5),
    ]);

    OrderItemFacade::shouldReceive('cancel')
        ->once()
        ->withArgs(fn ($item) => $item->is($orderItem))
        ->andReturn($orderItem);

    $response = $this->postJson("/api/v1/phone-numbers/{$orderItem->id}/cancel");

    $response->assertOk()
        ->assertJsonPath('message', 'Phone number cancelled successfully.');

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:3000/api/v1/cancel_number'
            && $request['request_id'] === 'req-cancel-controller'
            && $request['not_working'] === 0
            && ! isset($request['user_id']);
    });
});

it('uses the FoxSims reuse payload from the reuse controller action', function () {
    Http::fake([
        '*' => Http::response([
            'external_order_id' => 'req-reused',
            'price_cents' => 330,
        ]),
    ]);

    $user = User::factory()->create();
    Passport::actingAs($user, ['user-api'], 'api');

    Service::create([
        'name' => 'telegram',
        'code' => '5',
        'price_cents' => 220,
    ]);

    $order = Order::create([
        'id' => '22222222-2222-2222-2222-222222222222',
        'user_id' => $user->id,
        'total_cent_price' => 220,
        'status' => 'completed',
    ]);

    $orderItem = OrderItem::create([
        'order_id' => $order->id,
        'user_id' => $user->id,
        'external_order_id' => 'req-reuse-controller',
        'service_name' => 'telegram',
        'phone_number' => '+1234567890',
        'price_cents' => 220,
        'status' => 'completed',
    ]);

    OrderItemFacade::shouldReceive('countPendingNumbers')
        ->once()
        ->with($user->id)
        ->andReturn(0);

    OrderItemFacade::shouldReceive('reuse')
        ->once()
        ->withArgs(function ($item, $payload) use ($orderItem) {
            return $item->is($orderItem)
                && $payload['external_order_id'] === 'req-reused'
                && $payload['price_cents'] === 330;
        })
        ->andReturn($orderItem);

    UserFacade::shouldReceive('checkBalance')
        ->once()
        ->withArgs(fn ($authUser, $price) => $authUser->is($user) && $price === 2.2)
        ->andReturn(true);

    $response = $this->postJson("/api/v1/phone-numbers/{$orderItem->id}/reuse");

    $response->assertOk()
        ->assertJsonPath('message', 'Phone number reused successfully.');

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:3000/api/v1/reuse_number'
            && $request['request_id'] === 'req-reuse-controller';
    });
});

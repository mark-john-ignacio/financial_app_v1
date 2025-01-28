<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\WooCommerceWebhook\Livewire\Settings;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\WooCommerceSetting;
use Livewire\Livewire;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('it can update default customer', function () {
    $customer = Customer::factory()->create();

    Livewire::test(Settings::class)
        ->set('defaultCustomerCode', $customer->cempid)
        ->set('selectedCustomerText', "{$customer->cempid} - {$customer->cname}")
        ->call('updateDefaultCustomer')
        ->assertDispatched('updateDefaultCustomer');

    expect(WooCommerceSetting::where('key', 'default_customer_code')->first()->value)
        ->toBe($customer->cempid);
});

test('it can search customers', function () {
    $customer = Customer::factory()->create();

    $response = $this->getJson(route('api.customers.search', [
        'term' => $customer->cname
    ]));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'results' => [
                '*' => ['id', 'text']
            ],
            'pagination' => ['more']
        ]);
});

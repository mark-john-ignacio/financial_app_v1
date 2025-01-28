<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\WooCommerceWebhook\Services\WooCommerceService;
use Illuminate\Support\Facades\Cache;
use Automattic\WooCommerce\Client;
use Mockery;

uses(TestCase::class);
uses(RefreshDatabase::class);

beforeEach(function () {
    // Create mock WooCommerce client
    $this->mockWooClient = Mockery::mock(Client::class);
    
    // Bind mock to container
    $this->app->instance(Client::class, $this->mockWooClient);
    
    // Create service with mocked client
    $this->service = $this->app->make(WooCommerceService::class);
});

test('it can get product name and cache it', function () {
    // Arrange
    $mockProduct = (object)[
        'id' => 123,
        'name' => 'Test Product'
    ];

    $this->mockWooClient
        ->shouldReceive('get')
        ->with("products/123")
        ->once()
        ->andReturn($mockProduct);

    // Act
    $name = $this->service->getProductName(123);
    
    // Assert
    expect($name)->toBe('Test Product');
    expect(Cache::has('woo_product_123'))->toBeTrue();
});

test('it can get multiple product names in batch', function () {
    // Arrange
    $mockProducts = [
        (object)['id' => 123, 'name' => 'Product 1'],
        (object)['id' => 124, 'name' => 'Product 2']
    ];

    $this->mockWooClient
        ->shouldReceive('get')
        ->with('products', ['include' => '123,124', 'per_page' => 100])
        ->once()
        ->andReturn($mockProducts);

    // Act
    $names = $this->service->getProductNames([123, 124]);
    
    // Assert
    expect($names)->toBe([
        123 => 'Product 1',
        124 => 'Product 2'
    ]);
});

afterEach(function () {
    Mockery::close();
});
<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\WooCommerceWebhook\Actions\HandleOrder;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\DeliveryReceipt;
use Modules\WooCommerceWebhook\Models\Item;
use Modules\WooCommerceWebhook\Models\SalesOrder;
use Modules\WooCommerceWebhook\Models\SalesOrderItem;
use Modules\WooCommerceWebhook\Models\WooCommerceAudit as Audit;
use Modules\WooCommerceWebhook\Models\WoocommerceProductMapping as ProductMapping;
use Illuminate\Testing\TestResponse;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);
beforeEach(function () {
    $this->orderData = [
        "id" => 11220,
        "parent_id" => 0,
        "status" => "processing",
        "currency" => "PHP",
        "version" => "9.5.2",
        "prices_include_tax" => false,
        "date_created" => "2025-01-16T07:09:14",
        "date_modified" => "2025-01-16T07:09:21",
        "discount_total" => "0.00",
        "discount_tax" => "0.00",
        "shipping_total" => "0.00",
        "shipping_tax" => "0.00",
        "cart_tax" => "0.00",
        "total" => "180.00",
        "total_tax" => "0.00",
        "customer_id" => 1,
        "order_key" => "wc_order_fw67s1SgR5bJB",
        "billing" => [
            "first_name" => "Leo",
            "last_name" => "Batumbakal",
            "company" => "HRweb Julius",
            "address_1" => "Bagumbayan",
            "address_2" => "",
            "city" => "Manila",
            "state" => "ZAS",
            "postcode" => "4107",
            "country" => "PH",
            "email" => "dev4@hrweb.ph",
            "phone" => "+639123456789"
        ],
        "shipping" => [
            "first_name" => "Leo",
            "last_name" => "Batumbakal",
            "company" => "HRweb Julius",
            "address_1" => "Bagumbayan",
            "address_2" => "",
            "city" => "Manila",
            "state" => "ZAS",
            "postcode" => "4107",
            "country" => "PH",
            "phone" => ""
        ],
        "payment_method" => "paymongo_gcash",
        "payment_method_title" => "GCash via PayMongo",
        "transaction_id" => "pay_5yG69HM2dFFnFzX2mcjVNk9r",
        "customer_ip_address" => "::1",
        "customer_user_agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
        "created_via" => "checkout",
        "customer_note" => "",
        "date_completed" => null,
        "date_paid" => "2025-01-16T07:09:21",
        "cart_hash" => "dcdde136a13dade6005ccb8e4adcbdf2",
        "number" => "11220",
        "meta_data" => [
            ["id" => 38, "key" => "_wc_order_attribution_device_type", "value" => "Desktop"],
            ["id" => 36, "key" => "_wc_order_attribution_session_count", "value" => "1"],
            ["id" => 33, "key" => "_wc_order_attribution_session_entry", "value" => "http://sertshop.local/"],
            ["id" => 35, "key" => "_wc_order_attribution_session_pages", "value" => "7"],
            ["id" => 34, "key" => "_wc_order_attribution_session_start_time", "value" => "2025-01-16 06:54:01"],
            ["id" => 31, "key" => "_wc_order_attribution_source_type", "value" => "typein"],
            ["id" => 37, "key" => "_wc_order_attribution_user_agent", "value" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36"],
            ["id" => 32, "key" => "_wc_order_attribution_utm_source", "value" => "(direct)"],
            ["id" => 28, "key" => "is_vat_exempt", "value" => "no"],
            ["id" => 40, "key" => "paymongo_client_key", "value" => "pi_F56pqHhxAAPeZvUskWmAhPAH_client_arHAa49oQtKfWXS653hUeH6D"],
            ["id" => 39, "key" => "paymongo_payment_intent_id", "value" => "pi_F56pqHhxAAPeZvUskWmAhPAH"]
        ],
        "line_items" => [
            [
                "id" => 5,
                "name" => "A4 TECH Wired Mouse  OP-720 / OP-720S",
                "product_id" => 11123,
                "variation_id" => 0,
                "quantity" => 1,
                "tax_class" => "",
                "subtotal" => "180.00",
                "subtotal_tax" => "0.00",
                "total" => "180.00",
                "total_tax" => "0.00",
                "taxes" => [],
                "meta_data" => [
                    ["id" => 49, "key" => "_reduced_stock", "value" => "1", "display_key" => "_reduced_stock", "display_value" => "1"]
                ],
                "sku" => "",
                "price" => 180,
                "image" => [
                    "id" => "11124",
                    "src" => "http://sertshop.local/wp-content/uploads/2025/01/0301471525955931-1.jpg"
                ],
                "parent_name" => null
            ]
        ],
        "tax_lines" => [],
        "shipping_lines" => [
            [
                "id" => 6,
                "method_title" => "Free shipping",
                "method_id" => "free_shipping",
                "instance_id" => "1",
                "total" => "0.00",
                "total_tax" => "0.00",
                "taxes" => [],
                "meta_data" => [
                    ["id" => 48, "key" => "Items", "value" => "A4 TECH Wired Mouse OP-720 / OP-720S × 1", "display_key" => "Items", "display_value" => "A4 TECH Wired Mouse OP-720 / OP-720S × 1"]
                ]
            ]
        ],
        "fee_lines" => [],
        "coupon_lines" => [],
        "refunds" => [],
        "payment_url" => "http://sertshop.local/checkout/order-pay/11220/?pay_for_order=true&key=wc_order_fw67s1SgR5bJB",
        "is_editable" => false,
        "needs_payment" => false,
        "needs_processing" => true,
        "date_created_gmt" => "2025-01-16T07:09:14",
        "date_modified_gmt" => "2025-01-16T07:09:21",
        "date_completed_gmt" => null,
        "date_paid_gmt" => "2025-01-16T07:09:21",
        "currency_symbol" => "₱",
        "_links" => [
            "self" => [
                ["href" => "http://sertshop.local/wp-json/wc/v3/orders/11220", "targetHints" => ["allow" => ["GET", "POST", "PUT", "PATCH", "DELETE"]]]
            ],
            "collection" => [
                ["href" => "http://sertshop.local/wp-json/wc/v3/orders"]
            ],
            "customer" => [
                ["href" => "http://sertshop.local/wp-json/wc/v3/customers/1"]
            ]
        ]
    ];

    // Create necessary database records
    try {
        // Create necessary database records
        $customer = Customer::factory()->create(['cname' => 'CASH SALES', 'cempid' => 'CUST001']);
        Item::factory()->create(['nid' => 11123, 'cpartno' => 'ITEM001', 'cunit' => 'pcs']);
        ProductMapping::factory()->create(['woocommerce_product_id' => 11123, 'myxfin_product_id' => 11123]);
    } catch (\Exception $e) {
        Log::error('Error in beforeEach: ' . $e->getMessage());
        throw $e;
    }
});

it('handles orders successfully', function () {
    $response = HandleOrder::run(new \Illuminate\Http\Request($this->orderData));

    $testResponse = TestResponse::fromBaseResponse($response);

    $testResponse->assertJson([
        'status' => 'success',
        'data' => [
            'sales_order_ctranno' => true,
            'delivery_receipt_ctranno' => true,
            'sales_invoice_ctranno' => true,
        ],
    ]);


    $this->assertDatabaseHas('so', ['cpono' => 'wc_order_fw67s1SgR5bJB']);
    $this->assertDatabaseHas('dr', ['cremarks' => 'from_woocommerce']);
    $this->assertDatabaseHas('sales', ['cremarks' => 'from_woocommerce']);
});

it('logs an audit record for successful orders', function () {
    HandleOrder::run(new \Illuminate\Http\Request($this->orderData));

    $this->assertDatabaseHas('woocommerce_audits', [
        'request_data' => json_encode($this->orderData),
        'status' => 'success',
    ]);
});

it('logs an audit record for failed orders', function () {
    // Simulate a failure by removing the product mapping
    ProductMapping::where('woocommerce_product_id', 11123)->delete();

    try {
        HandleOrder::run(new \Illuminate\Http\Request($this->orderData));
    } catch (\Exception $e) {
        // Expected exception
    }

    $this->assertDatabaseHas('woocommerce_audits', [
        'request_data' => json_encode($this->orderData),
        'status' => 'failed',
    ]);
});

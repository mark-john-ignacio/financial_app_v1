<?php

namespace Modules\WooCommerceWebhook\Services;

use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Cache;

class WooCommerceService
{
    protected $woocommerce;

    public function __construct()
    {
        $this->woocommerce = new Client(
            config('woocommercewebhook.store_url'),
            config('woocommercewebhook.consumer_key'),
            config('woocommercewebhook.consumer_secret'),
            [
                'version' => 'wc/v3',
                'verify_ssl' => false
            ]
        );
    }

    public function getProductName($productId)
    {
        $cacheKey = "woo_product_{$productId}";

        return Cache::remember($cacheKey, 3600, function () use ($productId) {
            try {
                $product = $this->woocommerce->get("products/{$productId}");
                return $product->name ?? null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    public function getProductNames(array $productIds)
    {
       $names = [];
       $uncachedIds = [];

       foreach ($productIds as $id) {
        $cached = Cache::get("woo_product_{$id}");
        if ($cached) {
            $names[$id] = $cached;
        } else{
            $uncachedIds[] = $id;
        }
       }

       if(!empty($uncachedIds)){
        try {
            $chunks = array_chunk($uncachedIds, 100);
            foreach ($chunks as $chunk){
                $products = $this->woocommerce->get('products', [
                   'include' => implode(',', $chunk)
                ]);

                foreach ($products as $product){
                    $names[$product->id] = $product->name;
                    Cache::put("woo_product_{$product->id}", $product->name, 3600);
                }
            }
        } catch (\Exception $e){
            Log::error($e->getMessage());
        }
       }

    }
}

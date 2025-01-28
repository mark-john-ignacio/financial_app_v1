<?php

namespace Modules\WooCommerceWebhook\Services;

use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    protected $woocommerce;

    public function __construct(Client $woocommerce)
    {
        $this->woocommerce = $woocommerce;
    }

    public function getProductName($productId)
    {
        $cacheKey = "woo_product_{$productId}";

        return Cache::remember($cacheKey, 3600, function () use ($productId, $cacheKey) {
            try {
                $product = $this->woocommerce->get("products/{$productId}");
                // Store actual product name or special "not_found" marker
                return $product->name ?? '__NOT_FOUND__';
            } catch (\Exception $e) {
                // Cache negative result for 5 minutes only
                Cache::put($cacheKey, '__NOT_FOUND__', 300);
                return '__NOT_FOUND__';
            }
        });
    }

    public function getProductNames(array $productIds)
    {
        if (empty($productIds)) {
            return [];
        }
    
        $names = [];
        $uncachedIds = [];
    
        // Check cache first
        foreach ($productIds as $id) {
            $cached = Cache::get("woo_product_{$id}");
            if ($cached && $cached !== '__NOT_FOUND__') {
                $names[$id] = $cached;
            } else {
                $uncachedIds[] = $id;
            }
        }
    
        // Fetch uncached products
        if (!empty($uncachedIds)) {
            try {
                $products = $this->woocommerce->get('products', [
                    'include' => implode(',', $uncachedIds),
                    'per_page' => 100
                ]);
    
                foreach ($products as $product) {
                    $names[$product->id] = $product->name;
                    Cache::put("woo_product_{$product->id}", $product->name, 3600);
                }

                $foundIds = collect($products)->pluck('id')->toArray();
                $notFoundIds = array_diff($chunk, $foundIds);
                foreach ($notFoundIds as $id){
                    Cache::put("woo_product_{$id}", '__NOT_FOUND__', 3600);
                }
                
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    
        return $names;
    }
}

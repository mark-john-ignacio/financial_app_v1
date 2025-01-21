<?php

namespace Modules\WooCommerceWebhook\App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Http\Request;
use Modules\WooCommerceWebhook\App\Services\OrderService;
use Modules\WooCommerceWebhook\Models\WoocommerceProductMapping as ProductMapping;

class HandleOrderAction
{
    use AsAction;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function handle(Request $request)
    {
        $orderData = $request->all();

        $woocommerceProductIds = array_map(function($item){
            return $item['product_id'];
        }, $orderData['line_items']);

        $myxfinProductIds = $this->orderService->getMyxfinProductIds($woocommerceProductIds);
        $created_data = $this->orderService->processOrder($orderData, $myxfinProductIds);

        return response()->json([
            'status' => 'success',
            'data' => $created_data,
        ]);
    }
}

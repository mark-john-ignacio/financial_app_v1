<?php

namespace Modules\WooCommerceWebhook\Actions;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\WooCommerceWebhook\Services\OrderService;

class HandleOrder
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

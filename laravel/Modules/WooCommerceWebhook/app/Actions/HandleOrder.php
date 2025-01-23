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
        $created_data = $this->orderService->processOrder($orderData);

        return response()->json([
            'status' => 'success',
            'data' => $created_data,
        ]);
    }
}

<?php


namespace Modules\WooCommerceWebhook\Http\Controllers;

use Modules\WooCommerceWebhook\Actions\DeleteAll;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\WooCommerceWebhook\Actions\HandleOrder;


class WooCommerceWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return HandleOrder::run($request);
    }

    public function deleteAll()
    {
        return DeleteAll::run();
    }
}

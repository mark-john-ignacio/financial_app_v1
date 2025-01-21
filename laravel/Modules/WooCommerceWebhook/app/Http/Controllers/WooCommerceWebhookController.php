<?php


namespace Modules\WooCommerceWebhook\Http\Controllers;

use Modules\WooCommerceWebhook\App\Actions\HandleOrderAction;
use Modules\WooCommerceWebhook\App\Actions\DeleteAllAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WooCommerceWebhookController extends Controller
{
    public function handle(Request $request)
    {
        return HandleOrderAction::run($request);
    }

    public function deleteAll()
    {
        return DeleteAllAction::run();
    }
}

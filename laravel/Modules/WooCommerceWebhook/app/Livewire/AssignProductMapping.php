<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class AssignProductMapping extends Component
{

    public function render()
    {
        return view('woocommercewebhook::livewire.assign-product-mapping');
    }
}

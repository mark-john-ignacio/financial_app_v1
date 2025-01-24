<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;
use Modules\WooCommerceWebhook\Models\WooCommerceProductMapping;

class AssignProductMapping extends Component
{

    public $woocommerceProductId;
    public $myxfinProductId;
    public $mappings;

    public function mount()
    {
        $this->mappings = WooCommerceProductMapping::all();
    }

    public function assign()
    {
        $this->validate([
            'woocommerceProductId' => 'required|integer',
            'myxfinProductId' => 'required|integer',
        ]);

        WooCommerceProductMapping::create([
            'woocommerce_product_id' => $this->woocommerceProductId,
            'myxfin_product_id' => $this->myxfinProductId,
        ]);

        $this->mappings = WooCommerceProductMapping::all();
        $this->reset(['woocommerceProductId', 'myxfinProductId']);
        session()->flash('message', 'Product mapping assigned successfully.');
    }
    public function render()
    {
        return view('woocommercewebhook::livewire.assign-product-mapping');
    }
}

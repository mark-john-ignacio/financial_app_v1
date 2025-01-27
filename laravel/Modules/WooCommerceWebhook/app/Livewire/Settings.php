<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\WooCommerceSetting;

class Settings extends Component
{
    public $defaultCustomerId;
    public $customers;

    public function mount()
    {
        $this->customers = Customer::all();
        $this->defaultCustomerId = WooCommerceSetting::where('key', 'default_customer_id')->first()?->value;
    }

    public function updateDefaultCustomer()
    {
        WooCommerceSetting::updateOrCreate(
            ['key' => 'default_customer_id'],
            ['value' => $this->defaultCustomerId]
        );

        session()->flash('message', 'Default customer updated successfully');
    }
    public function render()
    {
        return view('woocommercewebhook::livewire.settings')
            ->layout('woocommercewebhook::layouts.app', ['title' => 'WooCommerce Settings']);
    }
}

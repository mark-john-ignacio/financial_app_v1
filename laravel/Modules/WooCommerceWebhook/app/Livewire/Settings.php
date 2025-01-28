<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\WooCommerceSetting;

class Settings extends Component
{
    public $defaultCustomerId;
    public $selectedCustomerText;

    public function mount()
    {
        $setting = WooCommerceSetting::where('key', 'default_customer_id')->first();
        if ($setting) {
            $this->defaultCustomerId = $setting->value;
            $customer = Customer::find($this->defaultCustomerId);
            $this->selectedCustomerText = $customer ? "{$customer->cempid} - {$customer->cname}" : '';
        }
    }

    public function apiCustomersSearch(Request $request)
    {
        $search = $request->get('term');
        $page = $request->get('page', 1);

        $customers = Customer::where('cname', 'like', "%{$search}%")
            ->orWhere('cempid', 'like', "%{$search}%")
            ->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'results' => collect($customers->items())->map(fn($customer) => [
                'id' => $customer->cempid,
                'text' => $customer->cempid . " - " . $customer->cname
            ]),
            'pagination' => [
                'more' => $customers->hasMorePages()
            ]
        ]);
    }

    public function updateDefaultCustomer()
    {
        WooCommerceSetting::updateOrCreate(
            ['key' => 'default_customer_id'],
            ['value' => $this->defaultCustomerId]
        );

        $this->dispatch('updateDefaultCustomer');
        session()->flash('message', 'Default customer updated successfully');
    }
    public function render()
    {
        return view('woocommercewebhook::livewire.settings')
            ->layout('woocommercewebhook::layouts.app', ['title' => 'WooCommerce Settings']);
    }
}

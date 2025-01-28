<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Modules\WooCommerceWebhook\Models\Customer;
use Modules\WooCommerceWebhook\Models\WooCommerceSetting;

class Settings extends Component
{
    public $defaultCustomerCode;
    public $selectedCustomerText;

    public function mount()
    {
        $setting = WooCommerceSetting::where('key', 'default_customer_code')->first();
        if ($setting && $setting->value) {
            $this->defaultCustomerCode = $setting->value;
            $customer = Customer::where('cempid', $this->defaultCustomerCode)->first();
            if ($customer) {
                $this->selectedCustomerText = "{$customer->cempid} - {$customer->cname}";
            }
        }
    }

    public function updateDefaultCustomer()
    {
        if ($this->defaultCustomerCode) {
            WooCommerceSetting::updateOrCreate(
                ['key' => 'default_customer_code'],
                ['value' => $this->defaultCustomerCode]
            );
            session()->flash('message', 'Default customer updated successfully');
        }
        $this->dispatch('updateDefaultCustomer');
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

    public function render()
    {
        return view('woocommercewebhook::livewire.settings')
            ->layout('woocommercewebhook::layouts.app', ['title' => 'WooCommerce Settings']);
    }
}

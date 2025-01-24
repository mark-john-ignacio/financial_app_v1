<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('woocommercewebhook::livewire.counter')->layout('woocommercewebhook::layouts.app');
    }
}

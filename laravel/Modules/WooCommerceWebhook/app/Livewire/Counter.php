<?php

namespace Modules\WooCommerceWebhook\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public function render()
    {
        return <<<'blade'
            <div>
                <h3>The <code>Counter</code> livewire component is loaded from the <code>WooCommerceWebhook</code> module.</h3>
            </div>
        blade;
    }
}

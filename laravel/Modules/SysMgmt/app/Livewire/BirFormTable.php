<?php

namespace Modules\SysMgmt\Livewire;

use Livewire\Component;
use Modules\SysMgmt\Models\BirForm;

class BirFormTable extends Component
{
    public function render()
    {
        $records = BirForm::all();

        return view('sysmgmt::livewire.form-table', compact('records'));
    }
}
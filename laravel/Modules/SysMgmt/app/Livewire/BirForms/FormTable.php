<?php

namespace Modules\SysMgmt\App\Livewire\BirForms;

use Modules\SysMgmt\Models\BirForm;
use Livewire\Component;

class FormTable extends Component
{
    public $forms;
    
    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount()
    {
        $this->forms = BirForm::all();
    }

    public function delete($id)
    {
        $form = BirForm::find($id);
        if ($form->yearForms()->exists()) {
            $this->dispatchBrowserEvent('swal:error', [
                'title' => 'Error!',
                'text' => 'Cannot delete form referenced in Year-Form.'
            ]);
            return;
        }

        $form->delete();
        $this->dispatchBrowserEvent('swal:success', [
            'title' => 'Success!',
            'text' => 'Form deleted successfully.'
        ]);
        $this->emit('refreshTable');
    }

    public function render()
    {
        return view('sysmgmt::livewire.bir-forms.form-table');
    }
}
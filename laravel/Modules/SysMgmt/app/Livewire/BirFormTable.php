<?php

namespace Modules\SysMgmt\Livewire;

use Modules\SysMgmt\Models\BirForm;
use Livewire\Component;
use Illuminate\Http\Request;

class BirFormTable extends Component
{
    public $forms;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function mount()
    {

    }

    public function getData(Request $request)
    {
        $query = BirForm::query();
    
        // Apply search if provided
        if ($request->has('search')) {
            $searchValue = $request->search;
            $query->where(function ($query) use ($searchValue) {
                $query->where('form_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('form_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('filter', 'like', '%' . $searchValue . '%');
            });
        }
    
        // Apply sorting
        $sortColumn = $request->input('sort', 'form_code');
        $sortDirection = $request->input('order', 'asc');
        $query->orderBy($sortColumn, $sortDirection);
    
        // Get all records for Simple-DataTables
        $birForms = $query->get();
    
        return response()->json([
            'data' => $birForms->map(function($form) {
                return [
                    'id' => $form->id,	
                    'form_code' => $form->form_code,
                    'form_name' => $form->form_name,
                    'filter' => $form->filter,
                    'cstatus' => $form->cstatus,
                    'actions' => '' // Will be populated by frontend
                ];
            })
        ]);
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
        return view('sysmgmt::livewire.form-table')
            ->layout('base::components.layouts.app', ['title' => 'Test']);
    }
}

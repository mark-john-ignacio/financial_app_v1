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
        $columns = [
            'form_code',
            'form_name',
            'filter',
            'cstatus',
        ];
        $length = $request->input('length');
        $start = $request->input('start');
        $column = $request->input('order.0.column');
        $dir = $request->input('order.0.dir') ?: 'desc';
        $searchValue = $request->input('search')['value'] ?? '';
        if (!isset($columns[$column])) {
            $column = 0;
        }

        $query = BirForm::query()
            ->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('form_code', 'like', '%' . $searchValue . '%')
                    ->orWhere('form_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('filter', 'like', '%' . $searchValue . '%');
            });
        }

        $total = $query->count();
        $birForms = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $birForms
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

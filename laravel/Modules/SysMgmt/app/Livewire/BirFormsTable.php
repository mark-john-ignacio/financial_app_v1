<?php

namespace Modules\SysMgmt\Livewire;

use Modules\SysMgmt\Models\BirForm;
use Modules\SysMgmt\Models\BirYearForm;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class BirFormsTable extends DataTableComponent
{
    protected $model = BirForm::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Form Code", "form_code")
                ->sortable()
                ->searchable(),
            Column::make("Form Name", "form_name")
                ->sortable()
                ->searchable(),
            Column::make("Filter", "filter")
                ->sortable(),
            Column::make("Status", "cstatus")
                ->sortable(),
            ButtonGroupColumn::make('Actions')
                ->attributes(function($row) {
                    return [
                        'class' => 'space-x-2',
                    ];
                })
                ->buttons([
                    // LinkColumn::make('View')
                    //     ->title(fn($row) => 'View')
                    //     ->location(fn($row) => route('bir-forms.show', ['bir_form' => $row->id]))
                    //     ->attributes(function($row) {
                    //         return [
                    //             'class' => 'btn btn-sm btn-primary',
                    //         ];
                    //     }),
                    // LinkColumn::make('Edit')
                    //     ->title(fn($row) => 'Edit')
                    //     ->location(fn($row) => route('bir-forms.edit', $row))
                    //     ->attributes(function($row) {
                    //         return [
                    //             'class' => 'btn btn-sm btn-warning',
                    //         ];
                    //     }),
                    // LinkColumn::make('Delete')
                    //     ->title(fn($row) => 'Delete')
                    //     ->location(fn($row) => '#')
                    //     ->attributes(function($row) {
                    //         return [
                    //             'class' => 'btn btn-sm btn-danger',
                    //             'wire:click' => "deleteForm($row->id)",
                    //         ];
                    //     }),
                ]),
        ];
    }

    public function deleteForm($id)
    {
        $formRegistration = BirYearForm::where('form_id', $id)->exists();

        if ($formRegistration) {
            $this->emit('showAlert', [
                'type' => 'error',
                'message' => 'Cannot delete the form because it is referenced in Year-Form.'
            ]);
            return;
        }

        BirForm::destroy($id);

        $this->emit('showAlert', [
            'type' => 'success',
            'message' => 'Form deleted successfully'
        ]);
    }
}

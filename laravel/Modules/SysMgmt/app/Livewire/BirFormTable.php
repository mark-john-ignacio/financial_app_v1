<?php

namespace Modules\SysMgmt\Livewire;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Modules\SysMgmt\Models\BirForm;
use Livewire\Component;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class BirFormTable extends Component implements Tables\Contracts\HasTable, HasForms
{
    use Tables\Concerns\InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(BirForm::query())
            ->columns([
                Tables\Columns\TextColumn::make('form_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('form_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('filter')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cstatus')
                    ->label('Status'),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                // View action with a modal displaying details
                Tables\Actions\ViewAction::make()
                ->modalHeading('View Bir Form')
                ->modalContent(function (BirForm $record): \Illuminate\Contracts\View\View {
                    // Return the view instance directly
                    return view('sysmgmt::livewire.view-bir-form', ['record' => $record]);
                }),
                // Edit action with a modal form for editing
                Tables\Actions\EditAction::make()
                ->modalHeading('Edit Bir Form')
                ->form([
                    TextInput::make('form_code')
                        ->required()
                        ->label('Form Code'),
                    TextInput::make('form_name')
                        ->required()
                        ->label('Form Name'),
                    TextInput::make('filter')
                        ->label('Filter'),
                    Select::make('cstatus')
                        ->label('Status')
                        ->options([
                            'Active'   => 'Active',
                            'Inactive' => 'Inactive',
                        ]),
                ]),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }

    public function render()
    {
        return view('sysmgmt::livewire.form-table')
        ->layout('base::components.layouts.app', ['title' => 'BIR Forms']);
    }
}

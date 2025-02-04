<?php

namespace Modules\SysMgmt\Livewire;

use Filament\Tables;
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
        ->layout('base::components.layouts.app', ['title' => 'Test']);
    }
}
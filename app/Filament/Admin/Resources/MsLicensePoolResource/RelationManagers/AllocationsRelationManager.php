<?php

namespace App\Filament\Admin\Resources\MsLicensePoolResource\RelationManagers;

use App\Models\Customer;
use App\Models\ProjectVm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'allocations';
    protected static ?string $title       = 'Alocações';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Cliente')
                ->options(Customer::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->live(),

            Forms\Components\Select::make('project_vm_id')
                ->label('VM')
                ->options(fn (Get $get) => $get('customer_id')
                    ? ProjectVm::whereHas('project', fn ($q) => $q->where('customer_id', $get('customer_id')))
                        ->pluck('name', 'id')
                    : [])
                ->searchable()
                ->nullable(),

            Forms\Components\TextInput::make('allocated_cores')
                ->label('Cores Alocados')
                ->numeric()
                ->required(),

            Forms\Components\DatePicker::make('allocated_at')
                ->label('Data de Alocação')
                ->nullable(),

            Forms\Components\DatePicker::make('released_at')
                ->label('Data de Liberação')
                ->nullable(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'active'   => 'Ativo',
                    'released' => 'Liberado',
                ])
                ->default('active')
                ->required()
                ->native(false),

            Forms\Components\Textarea::make('notes')
                ->label('Observações')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable(),

                Tables\Columns\TextColumn::make('projectVm.name')
                    ->label('VM')
                    ->default('-'),

                Tables\Columns\TextColumn::make('allocated_cores')
                    ->label('Cores Alocados')
                    ->numeric(),

                Tables\Columns\TextColumn::make('allocated_at')
                    ->label('Alocado em')
                    ->date('d/m/Y')
                    ->default('-'),

                Tables\Columns\TextColumn::make('released_at')
                    ->label('Liberado em')
                    ->date('d/m/Y')
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'gray'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

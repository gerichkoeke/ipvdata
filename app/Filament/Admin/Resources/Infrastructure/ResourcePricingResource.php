<?php

namespace App\Filament\Admin\Resources\Infrastructure;

use App\Models\ResourcePricing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\ResourcePricingResource\Pages;

class ResourcePricingResource extends Resource
{
    protected static ?string $model            = ResourcePricing::class;
    protected static ?string $navigationIcon   = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel  = 'Preços de Recursos';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Preço de Recurso';
    protected static ?string $pluralModelLabel = 'Preços de Recursos';
    protected static ?int    $navigationSort   = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Descrição')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\TextInput::make('resource_type')
                    ->label('Tipo (chave)')->required()->maxLength(50),
                Forms\Components\TextInput::make('unit')
                    ->label('Unidade')->required()->maxLength(20),
                Forms\Components\TextInput::make('price')
                    ->label('Preço/Unidade (R$)')->numeric()->prefix('R$')->step('0.0001')->required(),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Recurso')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('resource_type')->label('Tipo')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('price')->label('Preço/Unidade')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 4, ',', '.')),
                Tables\Columns\TextColumn::make('unit')->label('Unidade'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourcePricing::route('/'),
            'edit'  => Pages\EditResourcePricing::route('/{record}/edit'),
        ];
    }
}

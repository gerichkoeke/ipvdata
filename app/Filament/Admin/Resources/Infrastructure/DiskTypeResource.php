<?php

namespace App\Filament\Admin\Resources\Infrastructure;

use App\Models\DiskType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\DiskTypeResource\Pages;

class DiskTypeResource extends Resource
{
    protected static ?string $model           = DiskType::class;
    protected static ?string $navigationIcon  = 'heroicon-o-circle-stack';
    protected static ?string $navigationLabel = 'Tipos de Disco';
    protected static ?string $navigationGroup = 'Infraestrutura';
    protected static ?string $modelLabel      = 'Tipo de Disco';
    protected static ?string $pluralModelLabel = 'Tipos de Disco';
    protected static ?int    $navigationSort  = 12;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                Forms\Components\TextInput::make('price_per_gb')
                    ->label('Preço/GB (R$)')->numeric()->prefix('R$')->step('0.0001')->default(0),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\TextColumn::make('price_per_gb')
                    ->label('Preço/GB')
                    ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 4, ',', '.')),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordem')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiskTypes::route('/'),
            'edit'  => Pages\EditDiskType::route('/{record}/edit'),
        ];
    }
}

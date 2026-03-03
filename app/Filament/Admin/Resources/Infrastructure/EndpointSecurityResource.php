<?php
namespace App\Filament\Admin\Resources\Infrastructure;
use App\Models\EndpointSecurityOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\EndpointSecurityResource\Pages;

class EndpointSecurityResource extends Resource
{
    protected static ?string $model            = EndpointSecurityOption::class;
    protected static ?string $navigationIcon   = 'heroicon-o-bug-ant';
    protected static ?string $navigationLabel  = 'Endpoint Security';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Endpoint Security';
    protected static ?string $pluralModelLabel = 'Endpoint Security';
    protected static ?int    $navigationSort   = 16;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                Forms\Components\TextInput::make('price_per_vm')
                    ->label('Preço/VM/mês (R$)')->numeric()->prefix('R$')->default(0),
                Forms\Components\TextInput::make('sort_order')->label('Ordem')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->sortable(),
                Tables\Columns\TextColumn::make('price_per_vm')->label('Preço/VM')->money('BRL'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEndpointSecurity::route('/')];
    }
}

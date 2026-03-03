<?php
namespace App\Filament\Admin\Resources\Infrastructure;
use App\Models\BandwidthOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\BandwidthOptionResource\Pages;

class BandwidthOptionResource extends Resource
{
    protected static ?string $model            = BandwidthOption::class;
    protected static ?string $navigationIcon   = 'heroicon-o-signal';
    protected static ?string $navigationLabel  = 'Opções de Banda';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Banda';
    protected static ?string $pluralModelLabel = 'Opções de Banda';
    protected static ?int    $navigationSort   = 18;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nome')->required()
                    ->helperText('Ex: 100 Mbps'),
                Forms\Components\TextInput::make('mbps')->label('Velocidade (Mbps)')
                    ->numeric()->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Preço/mês (R$)')->numeric()->prefix('R$')->default(0),
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
                Tables\Columns\TextColumn::make('mbps')->label('Mbps')->sortable(),
                Tables\Columns\TextColumn::make('price')->label('Preço/mês')->money('BRL'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBandwidthOptions::route('/')];
    }
}

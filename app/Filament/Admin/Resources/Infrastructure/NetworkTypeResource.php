<?php
namespace App\Filament\Admin\Resources\Infrastructure;

use App\Models\NetworkType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\NetworkTypeResource\Pages;

class NetworkTypeResource extends Resource
{
    protected static ?string $model            = NetworkType::class;
    protected static ?string $navigationIcon   = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel  = 'Tipos de Rede';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Tipo de Rede';
    protected static ?string $pluralModelLabel = 'Tipos de Rede';
    protected static ?int    $navigationSort   = 13;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')->required(),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Preço Mensal (R$)')
                    ->numeric()->prefix('R$')->step('0.01')->default(0)
                    ->helperText('Custo mensal deste tipo de rede (ex: LAN-to-LAN)'),
                Forms\Components\TextInput::make('default_ips')
                    ->label('IPs Padrão inclusos')->numeric()->default(1),
                Forms\Components\Toggle::make('has_public_ip')
                    ->label('Tem IP Público')->default(true),
                Forms\Components\Toggle::make('requires_firewall')
                    ->label('Requer Firewall')->default(false),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Preço/mês')
                    ->formatStateUsing(fn ($state) => $state > 0
                        ? 'R$ ' . number_format($state, 2, ',', '.')
                        : '—')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'gray'),
                Tables\Columns\IconColumn::make('has_public_ip')
                    ->label('IP Público')->boolean(),
                Tables\Columns\IconColumn::make('requires_firewall')
                    ->label('Requer Firewall')->boolean(),
                Tables\Columns\TextColumn::make('default_ips')
                    ->label('IPs Padrão'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')->boolean(),
            ])
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListNetworkTypes::route('/')];
    }
}

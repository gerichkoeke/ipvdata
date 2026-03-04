<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MsLicenseSkuResource\Pages;
use App\Models\MsLicenseSku;
use App\Models\OsDistribution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MsLicenseSkuResource extends Resource
{
    protected static ?string $model            = MsLicenseSku::class;
    protected static ?string $navigationIcon   = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel  = 'SKUs de Licença';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'SKU de Licença';
    protected static ?string $pluralModelLabel = 'SKUs de Licença';
    protected static ?int    $navigationSort   = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('os_distribution_id')
                    ->label('Distribuição OS')
                    ->options(OsDistribution::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('part_number')
                    ->label('Part Number')
                    ->maxLength(100)
                    ->nullable(),

                Forms\Components\Select::make('license_type')
                    ->label('Tipo de Licença')
                    ->options([
                        'standard'   => 'Standard',
                        'datacenter' => 'Datacenter',
                        'enterprise' => 'Enterprise',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('cores_per_license')
                    ->label('Cores por Licença')
                    ->numeric()
                    ->default(16)
                    ->required(),

                Forms\Components\TextInput::make('pack_size')
                    ->label('Tamanho do Pack')
                    ->numeric()
                    ->nullable(),

                Forms\Components\TextInput::make('threshold_cores')
                    ->label('Threshold de Cores')
                    ->numeric()
                    ->default(16)
                    ->required()
                    ->helperText('Mínimo de cores para licença própria'),

                Forms\Components\Select::make('billing_period')
                    ->label('Período de Cobrança')
                    ->options([
                        'monthly' => 'Mensal',
                        '1year'   => '1 Ano',
                        '3year'   => '3 Anos',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\Toggle::make('is_cal')
                    ->label('É CAL')
                    ->default(false)
                    ->live(),

                Forms\Components\TextInput::make('cal_type')
                    ->label('Tipo CAL')
                    ->maxLength(100)
                    ->nullable()
                    ->visible(fn (Get $get) => $get('is_cal')),

                Forms\Components\Toggle::make('sa_available')
                    ->label('SA Disponível')
                    ->default(false),

                Forms\Components\TextInput::make('cost_price')
                    ->label('Preço de Custo (R$/core)')
                    ->numeric()
                    ->prefix('R$')
                    ->nullable(),

                Forms\Components\TextInput::make('sale_price')
                    ->label('Preço de Venda (R$/core)')
                    ->numeric()
                    ->prefix('R$')
                    ->nullable(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')
                    ->default(true),

                Forms\Components\Textarea::make('notes')
                    ->label('Observações')
                    ->columnSpan(2)
                    ->nullable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('part_number')
                    ->label('Part Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'datacenter' => 'danger',
                        'enterprise' => 'warning',
                        default      => 'info',
                    }),

                Tables\Columns\TextColumn::make('cores_per_license')
                    ->label('Cores/Lic.')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('threshold_cores')
                    ->label('Threshold')
                    ->numeric(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Custo')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-'),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Venda')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 2, ',', '.') : '-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMsLicenseSkus::route('/'),
            'edit'  => Pages\EditMsLicenseSku::route('/{record}/edit'),
        ];
    }
}

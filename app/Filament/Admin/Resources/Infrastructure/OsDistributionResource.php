<?php
namespace App\Filament\Admin\Resources\Infrastructure;
use App\Models\OsDistribution;
use App\Models\OsFamily;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\OsDistributionResource\Pages;

class OsDistributionResource extends Resource
{
    protected static ?string $model            = OsDistribution::class;
    protected static ?string $navigationIcon   = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel  = 'Sistemas Operacionais';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Sistema Operacional';
    protected static ?string $pluralModelLabel = 'Sistemas Operacionais';
    protected static ?int    $navigationSort   = 15;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('os_family_id')
                    ->label('Família')
                    ->options(OsFamily::where('is_active', true)->pluck('name', 'id'))
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('version')
                    ->label('Versão')
                    ->nullable(),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->default(0),

                // Licenciamento
                Forms\Components\Section::make('Licenciamento')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Toggle::make('requires_license')
                                ->label('Requer Licença')
                                ->default(false)
                                ->live()
                                ->helperText('Marque para Windows e outros OS pagos'),

                            Forms\Components\Toggle::make('license_per_core')
                                ->label('Licença por Core')
                                ->default(false)
                                ->live()
                                ->helperText('Windows Server: licenciado por core')
                                ->visible(fn (Get $get) => $get('requires_license')),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Ativo')
                                ->default(true),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('price')
                                ->label(fn (Get $get) => $get('license_per_core')
                                    ? 'Preço por Core/mês (R$)'
                                    : 'Preço de Licença/mês (R$)')
                                ->numeric()
                                ->prefix('R$')
                                ->default(0)
                                ->visible(fn (Get $get) => $get('requires_license'))
                                ->helperText(fn (Get $get) => $get('license_per_core')
                                    ? 'Valor multiplicado pelo nº de cores da VM'
                                    : 'Valor fixo mensal'),

                            Forms\Components\TextInput::make('min_cores')
                                ->label('Mínimo de Cores (licenciamento)')
                                ->numeric()
                                ->default(8)
                                ->visible(fn (Get $get) => $get('requires_license') && $get('license_per_core'))
                                ->helperText('Microsoft exige mínimo 8 cores por VM'),
                        ]),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('family.name')
                    ->label('Família')
                    ->badge()
                    ->color(fn ($state) => $state === 'Linux' ? 'success' : 'warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('version')
                    ->label('Versão'),

                Tables\Columns\IconColumn::make('requires_license')
                    ->label('Licença')
                    ->boolean(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Preço')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$record->requires_license || $state == 0) return 'Gratuito';
                        $suffix = $record->license_per_core
                            ? '/core/mês (mín. ' . $record->min_cores . ' cores)'
                            : '/mês';
                        return 'R$ ' . number_format($state, 2, ',', '.') . $suffix;
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->defaultSort('os_family_id')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListOsDistributions::route('/')];
    }
}

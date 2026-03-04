<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MsLicensePoolResource\Pages;
use App\Filament\Admin\Resources\MsLicensePoolResource\RelationManagers\AllocationsRelationManager;
use App\Models\MsLicensePool;
use App\Models\MsLicenseSku;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MsLicensePoolResource extends Resource
{
    protected static ?string $model            = MsLicensePool::class;
    protected static ?string $navigationIcon   = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel  = 'Pools de Licença';
    protected static ?string $navigationGroup  = 'Licenças Windows';
    protected static ?string $modelLabel       = 'Pool de Licença';
    protected static ?string $pluralModelLabel = 'Pools de Licença';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('sku_id')
                    ->label('SKU')
                    ->options(MsLicenseSku::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('invoice_number')
                    ->label('Número da Nota')
                    ->maxLength(100)
                    ->nullable(),

                Forms\Components\TextInput::make('purchased_cores')
                    ->label('Cores Adquiridos')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('cost_per_core')
                    ->label('Custo por Core (R$)')
                    ->numeric()
                    ->prefix('R$')
                    ->nullable(),

                Forms\Components\TextInput::make('sa_years')
                    ->label('Anos de SA')
                    ->numeric()
                    ->nullable(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active'   => 'Ativo',
                        'expired'  => 'Expirado',
                        'depleted' => 'Esgotado',
                    ])
                    ->default('active')
                    ->required()
                    ->native(false),

                Forms\Components\DatePicker::make('purchased_at')
                    ->label('Data de Compra')
                    ->nullable(),

                Forms\Components\DatePicker::make('sa_expires_at')
                    ->label('Expiração do SA')
                    ->nullable(),

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
                Tables\Columns\TextColumn::make('sku.name')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Nota Fiscal')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('purchased_cores')
                    ->label('Cores Comprados')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('allocated_cores')
                    ->label('Cores Alocados')
                    ->getStateUsing(fn ($record) => $record->allocated_cores)
                    ->numeric(),

                Tables\Columns\TextColumn::make('available_cores')
                    ->label('Cores Disponíveis')
                    ->getStateUsing(fn ($record) => $record->available_cores)
                    ->numeric(),

                Tables\Columns\TextColumn::make('cost_per_core')
                    ->label('Custo/Core')
                    ->formatStateUsing(fn ($state) => $state ? 'R$ ' . number_format($state, 4, ',', '.') : '-'),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Comprado em')
                    ->date('d/m/Y')
                    ->default('-'),

                Tables\Columns\TextColumn::make('sa_expires_at')
                    ->label('SA Expira em')
                    ->date('d/m/Y')
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active'   => 'success',
                        'expired'  => 'warning',
                        'depleted' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            AllocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMsLicensePools::route('/'),
            'edit'  => Pages\EditMsLicensePool::route('/{record}/edit'),
        ];
    }
}

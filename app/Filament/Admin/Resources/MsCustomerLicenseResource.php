<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MsCustomerLicenseResource\Pages;
use App\Models\Customer;
use App\Models\MsCustomerLicense;
use App\Models\MsLicensePool;
use App\Models\MsLicenseSku;
use App\Models\ProjectVm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MsCustomerLicenseResource extends Resource
{
    protected static ?string $model            = MsCustomerLicense::class;
    protected static ?string $navigationIcon   = 'heroicon-o-identification';
    protected static ?string $navigationLabel  = 'Licenças de Cliente';
    protected static ?string $navigationGroup  = 'Licenças Windows';
    protected static ?string $modelLabel       = 'Licença de Cliente';
    protected static ?string $pluralModelLabel = 'Licenças de Cliente';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Licença')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('Cliente')
                            ->options(Customer::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('sku_id')
                            ->label('SKU')
                            ->options(MsLicenseSku::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('project_vm_id')
                            ->label('VM')
                            ->options(fn (Get $get) => $get('customer_id')
                                ? ProjectVm::whereHas('project', fn ($q) => $q->where('customer_id', $get('customer_id')))
                                    ->pluck('name', 'id')
                                : [])
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('cores')
                            ->label('Cores')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $cores = (float) ($get('cores') ?? 0);
                                $costPerCore = (float) ($get('cost_per_core') ?? 0);
                                $set('total_cost', round($cores * $costPerCore, 2));
                            }),

                        Forms\Components\Select::make('license_modality')
                            ->label('Modalidade')
                            ->options([
                                'pool' => 'Pool (compartilhado)',
                                'own'  => 'Própria (≥ 16 cores)',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),
                    ]),
                ]),

            Forms\Components\Section::make('Pool')
                ->visible(fn (Get $get) => $get('license_modality') === 'pool')
                ->schema([
                    Forms\Components\Select::make('pool_id')
                        ->label('Pool de Licença')
                        ->options(
                            MsLicensePool::where('status', 'active')
                                ->with('sku')
                                ->get()
                                ->mapWithKeys(fn ($p) => [
                                    $p->id => "{$p->sku->name} — {$p->available_cores} cores disponíveis",
                                ])
                        )
                        ->searchable()
                        ->nullable()
                        ->helperText('Selecione o pool com cores disponíveis'),
                ]),

            Forms\Components\Section::make('Tenant Microsoft')
                ->visible(fn (Get $get) => $get('license_modality') === 'own')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('tenant_id')
                            ->label('Tenant ID')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('tenant_name')
                            ->label('Nome do Tenant')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('ms_customer_id')
                            ->label('MS Customer ID')
                            ->maxLength(255)
                            ->nullable()
                            ->helperText('ID do cliente no portal Microsoft para download de licença'),
                    ]),
                ]),

            Forms\Components\Section::make('Aquisição')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('part_number_purchased')
                            ->label('Part Number Comprado')
                            ->maxLength(100)
                            ->nullable(),

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Número da Nota')
                            ->maxLength(100)
                            ->nullable(),

                        Forms\Components\TextInput::make('sa_years')
                            ->label('Anos de SA')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\DatePicker::make('purchased_at')
                            ->label('Data de Compra')
                            ->nullable(),

                        Forms\Components\DatePicker::make('sa_expires_at')
                            ->label('Expiração do SA')
                            ->nullable(),

                        Forms\Components\TextInput::make('cost_per_core')
                            ->label('Custo por Core (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $cores = (float) ($get('cores') ?? 0);
                                $costPerCore = (float) ($get('cost_per_core') ?? 0);
                                $set('total_cost', round($cores * $costPerCore, 2));
                            }),

                        Forms\Components\TextInput::make('total_cost')
                            ->label('Custo Total (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->nullable()
                            ->readOnly(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active'   => 'Ativo',
                                'expired'  => 'Expirado',
                                'canceled' => 'Cancelado',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Observações')
                            ->columnSpan(2)
                            ->nullable(),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku.name')
                    ->label('SKU')
                    ->searchable(),

                Tables\Columns\TextColumn::make('projectVm.name')
                    ->label('VM')
                    ->default('-'),

                Tables\Columns\TextColumn::make('cores')
                    ->label('Cores')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('license_modality')
                    ->label('Modalidade')
                    ->badge()
                    ->color(fn ($state) => $state === 'own' ? 'success' : 'info'),

                Tables\Columns\TextColumn::make('tenant_name')
                    ->label('Tenant')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'active'   => 'success',
                        'expired'  => 'warning',
                        'canceled' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('purchased_at')
                    ->label('Comprado em')
                    ->date('d/m/Y')
                    ->default('-'),

                Tables\Columns\TextColumn::make('sa_expires_at')
                    ->label('SA Expira em')
                    ->date('d/m/Y')
                    ->default('-'),
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
            'index' => Pages\ListMsCustomerLicenses::route('/'),
            'edit'  => Pages\EditMsCustomerLicense::route('/{record}/edit'),
        ];
    }
}

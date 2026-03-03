<?php

namespace App\Filament\Distributor\Resources;

use App\Models\Customer;
use App\Models\Partner;
use App\Filament\Distributor\Resources\CustomerResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $distId = auth()->user()->distributor_id;
        return parent::getEloquentQuery()
            ->with('partner')
            ->whereHas('partner', fn(Builder $q) => $q->where('distributor_id', $distId));
    }

    protected static function getPartnerOptions(): array
    {
        $distId = auth()->user()->distributor_id;
        return Partner::where('distributor_id', $distId)
            ->orderBy('trade_name')
            ->get()
            ->mapWithKeys(function ($p) {
                $label = trim($p->trade_name ?: $p->company_name ?: '');
                return [$p->id => $label ?: 'Parceiro #' . $p->id];
            })
            ->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados do Cliente')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('partner_id')
                            ->label('Parceiro')
                            ->options(fn() => static::getPartnerOptions())
                            ->searchable()
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('document_type')
                            ->label('Tipo de Documento')
                            ->options(['cnpj' => 'CNPJ', 'cpf' => 'CPF'])
                            ->default('cnpj')
                            ->native(false),

                        Forms\Components\TextInput::make('name')
                            ->label('Razão Social')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('trade_name')
                            ->label('Nome Fantasia')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('document')
                            ->label('CNPJ / CPF')
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('contact_name')
                            ->label('Contato Principal')
                            ->maxLength(255),
                    ]),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Ativo')
                        ->default(true),
                    Forms\Components\Textarea::make('notes')
                        ->label('Observações')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->description(fn($record) => $record->trade_name ?: '')
                    ->searchable(['name', 'trade_name'])
                    ->sortable()
                    ->wrap()
                    ->grow(true),

                Tables\Columns\TextColumn::make('partner_id')
                    ->label('Parceiro')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state, $record) => $record->partner
                        ? trim($record->partner->trade_name ?: $record->partner->company_name)
                        : '-')
                    ->sortable()
                    ->grow(false),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->copyable()
                    ->grow(false)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->grow(false)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->grow(false),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('partner_id')
                    ->label('Filtrar por Parceiro')
                    ->options(fn() => static::getPartnerOptions())
                    ->searchable()          // ← digitar para filtrar
                    ->native(false)
                    ->placeholder('Todos os parceiros')
                    ->indicator('Parceiro'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos')
                    ->indicator('Status'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Filtros')
                    ->icon('heroicon-o-funnel'),
            )
            ->actions([
                Tables\Actions\ViewAction::make()->label(''),
                Tables\Actions\EditAction::make()->label(''),
                Tables\Actions\DeleteAction::make()->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn ($record) => static::getUrl('dashboard', ['record' => $record]))
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index'     => Pages\ListCustomers::route('/'),
            'create'    => Pages\CreateCustomer::route('/create'),
            'edit'      => Pages\EditCustomer::route('/{record}/edit'),
            'dashboard' => Pages\CustomerDashboard::route('/{record}/dashboard'),
            'infra'     => Pages\CustomerInfra::route('/{record}/infra'),
        ];
    }
}

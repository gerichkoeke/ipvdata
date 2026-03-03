<?php

namespace App\Filament\Distributor\Resources;

use App\Models\Partner;
use App\Filament\Distributor\Resources\PartnerResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Meus Parceiros';
    protected static ?string $modelLabel = 'Parceiro';
    protected static ?string $pluralModelLabel = 'Parceiros';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('distributor_id', auth()->user()->distributor_id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados do Parceiro')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->label('Razão Social')->required()->maxLength(255),
                    Forms\Components\TextInput::make('trade_name')
                        ->label('Nome Fantasia')->maxLength(255),
                    Forms\Components\TextInput::make('cnpj')
                        ->label('CNPJ')->maxLength(20),
                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')->email()->required()->unique(Partner::class, 'email', ignoreRecord: true)->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telefone')->maxLength(20),
                    Forms\Components\TextInput::make('website')
                        ->label('Website')->url()->maxLength(255),
                ]),
            ]),
            Forms\Components\Section::make('Comissão e Configurações')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Select::make('commission_model')
                        ->label('Modelo de Comissão')
                        ->options(['fixed' => 'Fixo', 'variable' => 'Variável'])
                        ->default('fixed')->native(false)->required()->live(),
                    Forms\Components\TextInput::make('commission_rate')
                        ->label('Taxa Fixa (%)')
                        ->numeric()->default(10)->minValue(0)->maxValue(100)->suffix('%')
                        ->visible(fn(Forms\Get $get) => $get('commission_model') === 'fixed'),
                    Forms\Components\TextInput::make('commission_min')
                        ->label('Mín (%)')
                        ->numeric()->default(5)->minValue(0)->maxValue(100)->suffix('%')
                        ->visible(fn(Forms\Get $get) => $get('commission_model') === 'variable'),
                    Forms\Components\TextInput::make('commission_max')
                        ->label('Máx (%)')
                        ->numeric()->default(15)->minValue(0)->maxValue(100)->suffix('%')
                        ->visible(fn(Forms\Get $get) => $get('commission_model') === 'variable'),
                    Forms\Components\Select::make('currency')
                        ->label('Moeda')
                        ->options(['BRL' => 'Real (R$)', 'USD' => 'Dólar (US$)', 'PYG' => 'Guarani (₲)'])
                        ->default('BRL')->native(false)->required(),
                    Forms\Components\Select::make('locale')
                        ->label('Idioma')
                        ->options(['pt_BR' => 'Português (BR)', 'en_US' => 'English (US)', 'es' => 'Español'])
                        ->default('pt_BR')->native(false)->required(),
                ]),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')->searchable()->sortable()
                    ->description(fn(Partner $r) => $r->company_name),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable(),
                Tables\Columns\TextColumn::make('customers_count')
                    ->label('Clientes')->counts('customers')->badge()->color('success'),
                Tables\Columns\TextColumn::make('currency')->label('Moeda')->badge(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->date('d/m/Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit'   => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources;

use App\Models\Distributor;
use App\Filament\Admin\Resources\DistributorResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistributorResource extends Resource
{
    protected static ?string $model = Distributor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Distribuidores';
    protected static ?string $modelLabel = 'Distribuidor';
    protected static ?string $pluralModelLabel = 'Distribuidores';
    protected static ?string $navigationGroup = 'Gestão Comercial';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Dados do Distribuidor')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Razão Social')->required()->maxLength(255),
                    Forms\Components\TextInput::make('trade_name')
                        ->label('Nome Fantasia')->maxLength(255),
                    Forms\Components\TextInput::make('document')
                        ->label('CNPJ')->maxLength(20),
                    Forms\Components\TextInput::make('email')
                        ->label('E-mail')->email()->required()
                        ->unique(Distributor::class, 'email', ignoreRecord: true)->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Telefone')->maxLength(20),
                    Forms\Components\TextInput::make('contact_name')
                        ->label('Contato Principal')->maxLength(255),
                ]),
            ]),
            Forms\Components\Section::make('Configurações Financeiras')->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('commission_pct')
                        ->label('Comissão (%)')->numeric()->default(10)
                        ->minValue(0)->maxValue(100)->suffix('%'),
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
            Forms\Components\Section::make('Observações')->schema([
                Forms\Components\Textarea::make('notes')->label('Notas internas')->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trade_name')
                    ->label('Nome Fantasia')->searchable()->sortable()
                    ->description(fn(Distributor $r) => $r->name),
                Tables\Columns\TextColumn::make('email')->label('E-mail')->searchable(),
                Tables\Columns\TextColumn::make('partners_count')
                    ->label('Parceiros')->counts('partners')->badge()->color('primary'),
                Tables\Columns\TextColumn::make('commission_pct')
                    ->label('Comissão')->suffix('%')->sortable(),
                Tables\Columns\TextColumn::make('currency')->label('Moeda')->badge(),
                Tables\Columns\TextColumn::make('locale')->label('Idioma'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criado em')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Ativo'),
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
            'index'  => Pages\ListDistributors::route('/'),
            'create' => Pages\CreateDistributor::route('/create'),
            'edit'   => Pages\EditDistributor::route('/{record}/edit'),
        ];
    }
}

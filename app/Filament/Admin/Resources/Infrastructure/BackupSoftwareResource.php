<?php
namespace App\Filament\Admin\Resources\Infrastructure;

use App\Models\BackupSoftwareOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\BackupSoftwareResource\Pages;

class BackupSoftwareResource extends Resource
{
    protected static ?string $model            = BackupSoftwareOption::class;
    protected static ?string $navigationIcon   = 'heroicon-o-archive-box-arrow-down';
    protected static ?string $navigationLabel  = 'Software de Backup';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Software de Backup';
    protected static ?string $pluralModelLabel = 'Softwares de Backup';
    protected static ?int    $navigationSort   = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome')->required()->columnSpan(2),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required(),

                Forms\Components\TextInput::make('edition')
                    ->label('Edição')
                    ->helperText('Ex: Community, Essentials, Universal, Enterprise Plus'),

                Forms\Components\Select::make('license_model')
                    ->label('Modelo de Licença')
                    ->options([
                        'per_vm'     => 'Por VM',
                        'per_socket' => 'Por Socket',
                        'per_tb'     => 'Por TB',
                    ])
                    ->default('per_vm')
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('price_per_unit')
                    ->label(fn(Get $get) => match($get('license_model')) {
                        'per_vm'     => 'Preço por VM/mês (R$)',
                        'per_socket' => 'Preço por Socket/mês (R$)',
                        'per_tb'     => 'Preço por TB/mês (R$)',
                        default      => 'Preço por Unidade (R$)',
                    })
                    ->numeric()->prefix('R$')->default(0),

                Forms\Components\TextInput::make('included_units')
                    ->label('Unidades Inclusas')
                    ->numeric()->default(0)
                    ->helperText('Ex: Community = 10 VMs grátis'),

                Forms\Components\Toggle::make('has_agent')
                    ->label('Tem Agente (para backup standalone)')
                    ->default(false)
                    ->live(),

                Forms\Components\TextInput::make('price_per_agent')
                    ->label('Preço por Agente/mês (R$)')
                    ->numeric()->prefix('R$')->default(0)
                    ->visible(fn(Get $get) => $get('has_agent')),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')->numeric()->default(0),

                Forms\Components\Toggle::make('is_active')
                    ->label('Ativo')->default(true),

                Forms\Components\Textarea::make('notes')
                    ->label('Observações')->columnSpan(2)->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('edition')
                    ->label('Edição')->badge()->color('info'),

                Tables\Columns\TextColumn::make('license_model')
                    ->label('Modelo')
                    ->formatStateUsing(fn($state) => match($state) {
                        'per_vm'     => 'Por VM',
                        'per_socket' => 'Por Socket',
                        'per_tb'     => 'Por TB',
                        default      => $state,
                    })
                    ->badge()->color('gray'),

                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label('Preço/Unidade')
                    ->formatStateUsing(fn($state) => $state > 0
                        ? 'R$ ' . number_format($state, 2, ',', '.')
                        : 'A definir'),

                Tables\Columns\IconColumn::make('has_agent')
                    ->label('Agente')->boolean(),

                Tables\Columns\TextColumn::make('price_per_agent')
                    ->label('Preço/Agente')
                    ->formatStateUsing(fn($state) => $state > 0
                        ? 'R$ ' . number_format($state, 2, ',', '.')
                        : '-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')->boolean(),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBackupSoftware::route('/')];
    }
}

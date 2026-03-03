<?php
namespace App\Filament\Admin\Resources\Infrastructure;
use App\Models\RdsLicenseMode;
use App\Models\RemoteDesktopType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\RdsLicenseModeResource\Pages;

class RdsLicenseModeResource extends Resource
{
    protected static ?string $model            = RdsLicenseMode::class;
    protected static ?string $navigationIcon   = 'heroicon-o-key';
    protected static ?string $navigationLabel  = 'Licenças RDS/TSPLUS';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Modo de Licença';
    protected static ?string $pluralModelLabel = 'Modos de Licença';
    protected static ?int    $navigationSort   = 19;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('remote_desktop_type_id')
                    ->label('Tipo Remote Desktop')
                    ->options(RemoteDesktopType::where('is_active', true)->pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                Forms\Components\TextInput::make('price_per_unit')
                    ->label('Preço/Licença/mês (R$)')->numeric()->prefix('R$')->default(0),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('remoteDesktopType.name')
                    ->label('Tipo')->badge()->color('info')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Modo')->sortable(),
                Tables\Columns\TextColumn::make('price_per_unit')->label('Preço/Licença')->money('BRL'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListRdsLicenseModes::route('/')];
    }
}

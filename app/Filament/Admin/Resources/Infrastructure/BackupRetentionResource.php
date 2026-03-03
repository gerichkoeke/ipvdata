<?php
namespace App\Filament\Admin\Resources\Infrastructure;
use App\Models\BackupRetentionOption;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Admin\Resources\Infrastructure\BackupRetentionResource\Pages;

class BackupRetentionResource extends Resource
{
    protected static ?string $model            = BackupRetentionOption::class;
    protected static ?string $navigationIcon   = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel  = 'Retenção de Backup';
    protected static ?string $navigationGroup  = 'Infraestrutura';
    protected static ?string $modelLabel       = 'Retenção';
    protected static ?string $pluralModelLabel = 'Retenções de Backup';
    protected static ?int    $navigationSort   = 17;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')->label('Nome')->required(),
                Forms\Components\TextInput::make('days')->label('Dias')->numeric()->nullable()
                    ->helperText('Deixe vazio para Full'),
                Forms\Components\Toggle::make('is_full')->label('É Full Backup')->default(false),
                Forms\Components\TextInput::make('price_multiplier')
                    ->label('Multiplicador de Custo')->numeric()->step('0.01')->default(1.00)
                    ->helperText('Ex: 1.50 = 50% a mais do armazenamento da VM'),
                Forms\Components\TextInput::make('sort_order')->label('Ordem')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->sortable(),
                Tables\Columns\TextColumn::make('days')->label('Dias')->placeholder('Full'),
                Tables\Columns\IconColumn::make('is_full')->label('Full')->boolean(),
                Tables\Columns\TextColumn::make('price_multiplier')->label('Multiplicador'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->headerActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListBackupRetention::route('/')];
    }
}

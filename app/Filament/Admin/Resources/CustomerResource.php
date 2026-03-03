<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model            = Customer::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationLabel  = 'Clientes';
    protected static ?string $navigationGroup  = 'Parceiros';
    protected static ?string $modelLabel       = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?int    $navigationSort   = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Dados do Cliente')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('partner_id')
                            ->label('Parceiro')
                            ->options(Partner::orderBy('company_name')->pluck('company_name', 'id'))
                            ->searchable()->native(false)->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Razão social / Nome')
                            ->required()->maxLength(255),
                        Forms\Components\TextInput::make('trade_name')
                            ->label('Nome fantasia')->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')->tel()->maxLength(20),
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Nome do contato')->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Cliente ativo')->default(true)->inline(false),
                    ]),
                ]),

            Section::make('Endereço')->icon('heroicon-o-map-pin')->collapsed()->schema([
                Grid::make(3)->schema([
                    Forms\Components\TextInput::make('zipcode')
                        ->label('CEP')->mask('99999-999')->maxLength(10),
                    Forms\Components\TextInput::make('city')->label('Cidade')->maxLength(255),
                    Forms\Components\Select::make('state')->label('UF')
                        ->options(['AC'=>'AC','AL'=>'AL','AM'=>'AM','AP'=>'AP','BA'=>'BA','CE'=>'CE','DF'=>'DF','ES'=>'ES','GO'=>'GO','MA'=>'MA','MG'=>'MG','MS'=>'MS','MT'=>'MT','PA'=>'PA','PB'=>'PB','PE'=>'PE','PI'=>'PI','PR'=>'PR','RJ'=>'RJ','RN'=>'RN','RO'=>'RO','RR'=>'RR','RS'=>'RS','SC'=>'SC','SE'=>'SE','SP'=>'SP','TO'=>'TO'])
                        ->searchable()->native(false),
                    Forms\Components\TextInput::make('address')->label('Endereço')->maxLength(255)->columnSpanFull(),
                ]),
            ]),

            Section::make('Observações')->icon('heroicon-o-chat-bubble-left-ellipsis')->collapsed()->schema([
                Forms\Components\Textarea::make('notes')->label('Notas')->rows(4)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->description(fn ($record) => $record->trade_name ?? '')
                    ->searchable(['name', 'trade_name'])
                    ->sortable()
                    ->weight('bold')
                    ->grow(),

                Tables\Columns\TextColumn::make('document_formatted')
                    ->label('CNPJ/CPF')
                    ->searchable('document')
                    ->copyable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('monthly_value')
                    ->label('MRR')
                    ->formatStateUsing(fn ($record) =>
                        'R$ ' . number_format($record->monthly_value ?? 0, 2, ',', '.'))
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Ativos')->falseLabel('Inativos')->native(false),
            ])
            ->emptyStateHeading('Selecione um parceiro')
            ->emptyStateDescription('Use o seletor acima para filtrar os clientes por parceiro.')
            ->emptyStateIcon('heroicon-o-briefcase')
            ->recordUrl(fn ($record) => static::getUrl('dashboard', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('edit_btn')
                    ->label('Editar')
                    ->tooltip('Editar cliente')
                    ->icon('heroicon-m-pencil-square')
                    ->color('gray')
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make()
                    ->label('')->tooltip('Excluir')
                    ->modalHeading('Excluir cliente')
                    ->modalSubmitActionLabel('Sim, excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir selecionados'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
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

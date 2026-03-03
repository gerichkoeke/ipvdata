<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model            = User::class;
    protected static ?string $navigationIcon   = 'heroicon-o-users';
    protected static ?string $navigationGroup  = 'Configurações';
    protected static ?string $navigationLabel  = 'Usuários';
    protected static ?string $modelLabel       = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';
    protected static ?int    $navigationSort   = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Section::make('Informações Pessoais')
                ->description('Dados básicos do usuário')
                ->icon('heroicon-o-user')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->maxLength(20),

                        Forms\Components\FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('avatars')
                            ->maxSize(2048),
                    ]),
                ]),

            Section::make('Acesso e Permissões')
                ->description('Configurações de acesso ao sistema')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\Select::make('panel')
                            ->label('Painel de acesso')
                            ->options([
                                'admin'   => '🔴 Admin — IPV ERP',
                                'partner' => '🔵 Partner — Portal do Parceiro',
                            ])
                            ->required()
                            ->live()
                            ->native(false),

                        Forms\Components\Select::make('partner_id')
                            ->label('Parceiro vinculado')
                            ->relationship('partner', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (Forms\Get $get) => $get('panel') === 'partner')
                            ->helperText('Obrigatório para usuários do painel Partner'),

                        Forms\Components\Select::make('roles')
                            ->label('Perfil / Role')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->native(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Usuário ativo')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Select::make('locale')
                            ->label('Idioma')
                            ->options([
                                'pt_BR' => '🇧🇷 Português (Brasil)',
                                'es'    => '🇪🇸 Español',
                                'en'    => '🇺🇸 English',
                            ])
                            ->default('pt_BR')
                            ->required()
                            ->native(false),
                    ]),
                ]),

            Section::make('Senha')
                ->description('Deixe em branco para manter a senha atual')
                ->icon('heroicon-o-lock-closed')
                ->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Nova senha')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->same('password_confirmation'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar senha')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation) => $operation === 'create'),
                    ]),
                ]),

            Section::make('Informações do Sistema')
                ->icon('heroicon-o-information-circle')
                ->collapsed()
                ->schema([
                    Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('email_verified_at')
                            ->label('E-mail verificado em')
                            ->content(fn ($record) => $record?->email_verified_at?->format('d/m/Y H:i') ?? 'Não verificado'),

                        Forms\Components\Placeholder::make('last_login_at')
                            ->label('Último acesso')
                            ->content(fn ($record) => $record?->last_login_at?->format('d/m/Y H:i') ?? 'Nunca acessou'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Criado em')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i') ?? '-'),
                    ]),
                ])
                ->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Avatar + Nome + Email agrupados
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=ffffff&background=4f46e5&size=64'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Usuário')
                    ->description(fn ($record) => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('panel')
                    ->label('Painel')
                    ->colors([
                        'danger'  => 'admin',
                        'primary' => 'partner',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'admin'   => 'Admin',
                        'partner' => 'Partner',
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('gray')
                    ->separator(','),

                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Parceiro')
                    ->placeholder('-')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último acesso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca acessou')
                    ->since()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('panel')
                    ->label('Painel')
                    ->options([
                        'admin'   => 'Admin',
                        'partner' => 'Partner',
                    ]),

                Tables\Filters\SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Apenas ativos')
                    ->falseLabel('Apenas inativos')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Editar usuário')
                    ->icon('heroicon-m-pencil-square')
                    ->iconSize('md')
                    ->color('primary'),

                Tables\Actions\Action::make('toggle_active')
                    ->label('')
                    ->tooltip(fn ($record) => $record->is_active ? 'Desativar usuário' : 'Ativar usuário')
                    ->icon(fn ($record) => $record->is_active
                        ? 'heroicon-m-x-circle'
                        : 'heroicon-m-check-circle')
                    ->iconSize('md')
                    ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->is_active ? 'Desativar usuário' : 'Ativar usuário')
                    ->modalDescription(fn ($record) => $record->is_active
                        ? 'Tem certeza que deseja desativar este usuário? Ele perderá o acesso ao sistema.'
                        : 'Tem certeza que deseja ativar este usuário?')
                    ->modalSubmitActionLabel('Confirmar')
                    ->action(fn ($record) => $record->update(['is_active' => !$record->is_active])),

                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Excluir usuário')
                    ->icon('heroicon-m-trash')
                    ->iconSize('md'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Ativar selecionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desativar selecionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['roles', 'partner']);
    }
}

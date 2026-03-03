<?php

namespace App\Filament\Partner\Widgets;

use App\Models\Customer;
use App\Filament\Partner\Resources\CustomerResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PartnerClients extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Meus Clientes';

    public function table(Table $table): Table
    {
        $partnerId = filament()->auth()->user()?->partner_id;

        return $table
            ->query(
                Customer::query()
                    ->where('partner_id', $partnerId)
                    ->withCount(['projects'])
                    ->latest()
            )
            ->emptyStateHeading('Nenhum cliente cadastrado')
            ->emptyStateDescription('Seus clientes aparecerão aqui.')
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->description(fn($record) => $record->trade_name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document')
                    ->label('CNPJ/CPF')
                    ->formatStateUsing(fn($record) => $record->document_formatted)
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label('Projetos')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('city')
                    ->label('Cidade')
                    ->formatStateUsing(fn($record) => $record->city . '/' . $record->state)
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->recordUrl(
                fn(Customer $record) => CustomerResource::getUrl('dashboard', ['record' => $record])
            );
    }
}

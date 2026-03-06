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
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('app.customers.title');
    }

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
            ->emptyStateHeading(__('app.customers.empty_state_heading'))
            ->emptyStateDescription(__('app.customers.empty_state_description'))
            ->emptyStateIcon('heroicon-o-users')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('app.customers.singular'))
                    ->description(fn($record) => $record->trade_name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('document')
                    ->label(__('app.customers.document'))
                    ->formatStateUsing(fn($record) => $record->document_formatted)
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('app.email'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('projects_count')
                    ->label(__('app.projects.title'))
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('app.customers.city_state'))
                    ->formatStateUsing(fn($record) => $record->city . '/' . $record->state)
                    ->placeholder('-'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('app.active'))
                    ->boolean(),
            ])
            ->recordUrl(
                fn(Customer $record) => CustomerResource::getUrl('dashboard', ['record' => $record])
            );
    }
}

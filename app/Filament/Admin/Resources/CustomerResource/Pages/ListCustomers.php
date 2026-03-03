<?php

namespace App\Filament\Admin\Resources\CustomerResource\Pages;

use App\Filament\Admin\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Partner;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected static string $view = 'filament.admin.resources.customer-resource.pages.list-customers';

    #[Url(as: 'parceiro')]
    public ?int $selectedPartnerId = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTableQuery(): Builder
    {
        if ($this->selectedPartnerId) {
            return Customer::query()->where('partner_id', $this->selectedPartnerId);
        }

        return Customer::query()->whereRaw('0 = 1');
    }

    public function updatedSelectedPartnerId(): void
    {
        $this->resetTable();
    }

    public function getPartners()
    {
        return Partner::orderBy('company_name')->get();
    }
}

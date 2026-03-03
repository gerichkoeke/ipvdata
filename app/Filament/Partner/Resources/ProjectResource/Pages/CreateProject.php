<?php
namespace App\Filament\Partner\Resources\ProjectResource\Pages;

use App\Filament\Partner\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = auth()->user()?->partner_id;
        return $data;
    }
}

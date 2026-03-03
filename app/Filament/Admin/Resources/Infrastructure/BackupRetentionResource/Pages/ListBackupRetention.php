<?php
namespace App\Filament\Admin\Resources\Infrastructure\BackupRetentionResource\Pages;
use App\Filament\Admin\Resources\Infrastructure\BackupRetentionResource;
use Filament\Resources\Pages\ListRecords;
class ListBackupRetention extends ListRecords
{
    protected static string $resource = BackupRetentionResource::class;
}

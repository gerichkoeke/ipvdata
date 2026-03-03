<?php
namespace App\Filament\Partner\Resources;

use App\Models\Project;
use Filament\Resources\Resource;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static bool $shouldRegisterNavigation = false; // oculto do menu
    
    public static function getPages(): array { return []; }
}

<?php

namespace App\Filament\Resources\GrainAmortResource\Pages;

use App\Filament\Resources\GrainAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGrainAmort extends ViewRecord
{
    protected static string $resource = GrainAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

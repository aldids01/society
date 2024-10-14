<?php

namespace App\Filament\Resources\ApprovedGrainResource\Pages;

use App\Filament\Resources\ApprovedGrainResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageApprovedGrains extends ManageRecords
{
    protected static string $resource = ApprovedGrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Member\Resources\GrainAmortResource\Pages;

use App\Filament\Member\Resources\GrainAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGrainAmorts extends ManageRecords
{
    protected static string $resource = GrainAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}

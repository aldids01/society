<?php

namespace App\Filament\Resources\GrainAmortResource\Pages;

use App\Filament\Resources\GrainAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrainAmort extends EditRecord
{
    protected static string $resource = GrainAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

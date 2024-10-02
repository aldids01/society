<?php

namespace App\Filament\Member\Resources\GrainResource\Pages;

use App\Filament\Member\Resources\GrainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrain extends EditRecord
{
    protected static string $resource = GrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

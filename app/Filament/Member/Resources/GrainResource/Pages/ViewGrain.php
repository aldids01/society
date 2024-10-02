<?php

namespace App\Filament\Member\Resources\GrainResource\Pages;

use App\Filament\Member\Resources\GrainResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGrain extends ViewRecord
{
    protected static string $resource = GrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->visible(fn($record)=>$record->status === 'pending'),
        ];
    }
}

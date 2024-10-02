<?php

namespace App\Filament\Member\Resources\GrainResource\Pages;

use App\Filament\Member\Resources\GrainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGrains extends ListRecords
{
    protected static string $resource = GrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Loan Request'),
        ];
    }
}

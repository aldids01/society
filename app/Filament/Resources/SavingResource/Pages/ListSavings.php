<?php

namespace App\Filament\Resources\SavingResource\Pages;

use App\Filament\Imports\SavingImporter;
use App\Filament\Resources\SavingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSavings extends ListRecords
{
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make('update_saving')
                ->importer(SavingImporter::class)
                ->color('info')
                ->slideOver(),
        ];
    }
}

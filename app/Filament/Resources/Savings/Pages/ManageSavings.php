<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use App\Filament\Resources\Savings\Widgets\QuarterlySaving;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;

class ManageSavings extends ManageRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            QuarterlySaving::class,
        ];
    }
}

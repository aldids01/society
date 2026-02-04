<?php

namespace App\Filament\Resources\Grains\Pages;

use App\Filament\Resources\Grains\GrainResource;
use App\Filament\Resources\LoanAmorts\Widgets\LoanReportStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageGrains extends ManageRecords
{
    protected static string $resource = GrainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Large)
                ->slideOver(),
        ];
    }


}

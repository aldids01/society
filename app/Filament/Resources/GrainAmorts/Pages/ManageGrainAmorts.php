<?php

namespace App\Filament\Resources\GrainAmorts\Pages;

use App\Filament\Resources\GrainAmorts\GrainAmortResource;
use App\Filament\Resources\GrainAmorts\Widgets\GrainReportStats;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;

class ManageGrainAmorts extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = GrainAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GrainReportStats::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\LoanAmorts\Pages;

use App\Filament\Resources\LoanAmorts\LoanAmortResource;
use App\Filament\Resources\LoanAmorts\Widgets\LoanReportStats;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;

class ManageLoanAmorts extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = LoanAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoanReportStats::class,
        ];
    }
}

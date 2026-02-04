<?php

namespace App\Filament\Resources\SavingReports\Pages;

use App\Filament\Exports\SavingExporter;
use App\Filament\Imports\SavingImporter;
use App\Filament\Resources\SavingReports\SavingReportResource;
use App\Filament\Resources\SavingReports\Widgets\MemberReportStats;
use App\Filament\Resources\SavingReports\Widgets\QuarterlyReportStats;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;

class ManageSavingReports extends ManageRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = SavingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
            ImportAction::make()
                ->importer(SavingImporter::class)
                ->modalWidth(Width::ExtraSmall)
                ->modalCancelAction(false)
                ->color('primary')
                ->modalFooterActionsAlignment(Alignment::Center)
                ->slideOver(),
            ExportAction::make()
                ->exporter(SavingExporter::class)
                ->modalWidth(Width::ExtraSmall)
                ->modalCancelAction(false)
                ->color('primary')
                ->modalFooterActionsAlignment(Alignment::Center)
                ->slideOver(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QuarterlyReportStats::class,
            MemberReportStats::class,
        ];
    }
}

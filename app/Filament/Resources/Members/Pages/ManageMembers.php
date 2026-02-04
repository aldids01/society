<?php

namespace App\Filament\Resources\Members\Pages;

use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Members\Widgets\MemberStats;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageMembers extends ManageRecords
{
    protected static string $resource = MemberResource::class;
    use ExposesTableToWidgets;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Medium)
                ->slideOver(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MemberStats::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\Guarantors\Pages;

use App\Filament\Resources\Guarantors\GuarantorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGuarantors extends ManageRecords
{
    protected static string $resource = GuarantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }
}

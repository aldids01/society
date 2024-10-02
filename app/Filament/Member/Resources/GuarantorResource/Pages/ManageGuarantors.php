<?php

namespace App\Filament\Member\Resources\GuarantorResource\Pages;

use App\Filament\Member\Resources\GuarantorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGuarantors extends ManageRecords
{
    protected static string $resource = GuarantorResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // Actions\CreateAction::make(),
        ];
    }
}

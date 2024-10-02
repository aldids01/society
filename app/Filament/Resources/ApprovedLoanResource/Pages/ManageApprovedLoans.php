<?php

namespace App\Filament\Resources\ApprovedLoanResource\Pages;

use App\Filament\Resources\ApprovedLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageApprovedLoans extends ManageRecords
{
    protected static string $resource = ApprovedLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver(),
        ];
    }
}

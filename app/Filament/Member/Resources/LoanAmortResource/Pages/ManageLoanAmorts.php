<?php

namespace App\Filament\Member\Resources\LoanAmortResource\Pages;

use App\Filament\Member\Resources\LoanAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLoanAmorts extends ManageRecords
{
    protected static string $resource = LoanAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}

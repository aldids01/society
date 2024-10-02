<?php

namespace App\Filament\Resources\LoanAmortResource\Pages;

use App\Filament\Resources\LoanAmortResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoanAmorts extends ListRecords
{
    protected static string $resource = LoanAmortResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}

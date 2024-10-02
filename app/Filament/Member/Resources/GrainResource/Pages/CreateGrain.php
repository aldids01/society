<?php

namespace App\Filament\Member\Resources\GrainResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Member\Resources\GrainResource;
use App\Models\GrainAmort;

class CreateGrain extends CreateRecord
{
    protected static string $resource = GrainResource::class;
    protected function afterCreate(): void
    {
        GrainAmort::where('grain_id', $this->record->slug)->delete();

        $amount = $this->record->amount;
        $terms = $this->record->terms;
        $rate = $this->record->rate / 100 / $terms;
        $totalInterest = $amount * $rate * $terms;
        $payment = ($amount + $totalInterest) / $terms;

        $remainingBalance = $amount;
        $startDate = Carbon::parse($this->record->start_date);
        $fixedInterest = $totalInterest / $terms;

        for ($i = 0; $i < $terms; $i++) {

            $principal = $payment - $fixedInterest;

            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principal;

            GrainAmort::create([
                'grain_id' => $this->record->slug,
                'grain_owner' => $this->record->applicant_id,
                'annual' => $paymentDate->format('Y'),
                'period' => $paymentDate->format('F'),
                'interest' => $fixedInterest,
                'principal' => $principal,
                'payment' => $payment,
                'start_balance' => $remainingBalance,
                'end_balance' => $endBalance,
            ]);

            $remainingBalance = $endBalance;
        }
    }

}

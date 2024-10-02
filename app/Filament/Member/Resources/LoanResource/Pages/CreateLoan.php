<?php

namespace App\Filament\Member\Resources\LoanResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\LoanAmort;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Member\Resources\LoanResource;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
    protected function afterCreate(): void
    {
        LoanAmort::where('loan_id', $this->record->slug)->delete();
        $amount = $this->record->amount;
        $rate = $this->record->rate / 100 / 12;
        $terms = $this->record->terms;
        $payment = $amount * ($rate / (1 - pow(1 + $rate, -$terms)));

        $remainingBalance = $amount;
        $startDate = Carbon::parse($this->record->start_date);

        for ($i = 0; $i < $terms; $i++) {
            $interest = $remainingBalance * $rate;
            $principal = $payment - $interest;

            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principal;

            LoanAmort::create([
                'loan_id' =>$this->record->slug,
                'loan_owner' =>$this->record->applicant_id,
                'annual' => $paymentDate->format('Y'),
                'period' => $paymentDate->format('F'),
                'interest' => $interest,
                'principal' => $principal,
                'payment' => $payment,
                'start_balance' => $remainingBalance,
                'end_balance' => $endBalance,
            ]);

            $remainingBalance = $endBalance;
        }
    }
}

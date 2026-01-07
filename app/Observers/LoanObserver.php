<?php

namespace App\Observers;

use App\Models\Loan;
use App\Models\LoanAmort;
use App\Models\LoanApproval;
use App\Models\Saving;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     */
    public function created(Loan $loan): void
    {
        static::generateAmortizationSchedule($loan);
    }

    /**
     * Handle the Loan "updated" event.
     */
    public function updated(Loan $loan): void
    {
        // Check if the 'amount' attribute has changed
        if ($loan->isDirty('amount') || $loan->isDirty('terms') || $loan->isDirty('rate') || $loan->isDirty('start_date')) {
            // Delete existing LoanAmort records for this loan
            LoanAmort::where('loan_id', $loan->slug)->forceDelete();

            // Recreate the amortization schedule
            static::generateAmortizationSchedule($loan);
        }
    }

    /**
     * Handle the Loan "deleted" event.
     */
    public function deleted(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "restored" event.
     */
    public function restored(Loan $loan): void
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     */
    public function forceDeleted(Loan $loan): void
    {
        LoanAmort::where('loan_id', $loan->slug)->forceDelete();
        LoanApproval::where('loan_id', $loan->slug)->forceDelete();
    }
    public function generateAmortizationSchedule(Loan $loan): void
    {
        // 1. Ensure Approval Record Exists
        LoanApproval::firstOrCreate(['loan_id' => $loan->slug]);

        // 2. Calculate Financial Variables
        $amount = (float) $loan->amount;
        $terms  = (int) $loan->terms;
        $rate   = $this->calculatePeriodicRate($loan);

        $totalInterest = $amount * $rate * $terms;
        $totalPayable  = $amount + $totalInterest;
        $monthlyPayment = $terms > 0 ? ($totalPayable / $terms) : 0;

        $startDate = Carbon::parse($loan->start_date);

        // 3. Process Schedule within a Transaction for Safety
        DB::transaction(function () use ($loan, $amount, $terms, $totalInterest, $monthlyPayment, $startDate) {

            // Handle Simple Interest (Flat) vs. Amortized Schedule
            if ($loan->rate < 6) {
                $this->createSingleEntry($loan, $amount, $totalInterest, $monthlyPayment, $startDate);
            } else {
                $this->createMonthlyEntries($loan, $amount, $terms, $totalInterest, $monthlyPayment, $startDate);
            }
        });
    }

    /**
     * Logic to determine interest rate based on loan type/savings
     */
    private function calculatePeriodicRate(Loan $loan): float
    {
        $annualRate = $loan->rate / 100;

        if ($loan->saved < 1) {
            return $annualRate / $loan->terms;
        }

        if ($loan->rate < 6) {
            return $annualRate;
        }

        return $annualRate / 12;
    }
    /**
     * Creates a single summary record for the entire loan duration.
     */
    private function createSingleEntry($loan, $amount, $totalInterest, $monthlyPayment, $startDate): void
    {
        Saving::query()->where('member_id', '=', $loan->member_id)->forceDelete();

        $new_saving = max(0, $loan->saved - ($amount + $totalInterest));
        Saving::create([
            'member_id' => $loan->member_id,
            'annual' => $startDate->format('Y'),
            $startDate->format('F') => $new_saving,
        ]);

        LoanAmort::create([
            'loan_id'       => $loan->slug,
            'member_id'     => $loan->member_id,
            'annual'        => $startDate->format('Y'),
            'period'        => $startDate->format('F'),
            'interest'      => $totalInterest,
            'principal'     => $amount,
            'payment'       => $monthlyPayment,
            'start_balance' => $amount,
            'end_balance'   => 0,
            'status' => 'paid',
        ]);
    }
    private function createMonthlyEntries($loan, $amount, $terms, $totalInterest, $payment, $startDate): void
    {
        $remainingBalance = $amount;
        $fixedInterestPerMonth = $totalInterest / $terms;

        for ($i = 0; $i < $terms; $i++) {
            $principalPerMonth = $payment - $fixedInterestPerMonth;
            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principalPerMonth;

            LoanAmort::create([
                'loan_id'       => $loan->slug,
                'member_id'     => $loan->member_id,
                'annual'        => $paymentDate->format('Y'),
                'period'        => $paymentDate->format('F'),
                'interest'      => $fixedInterestPerMonth,
                'principal'     => $principalPerMonth,
                'payment'       => $payment,
                'start_balance' => $remainingBalance,
                'end_balance'   => max(0, $endBalance), // Prevent negative small floating point errors
            ]);

            $remainingBalance = $endBalance;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Grain;
use App\Models\GrainAmort;
use App\Models\GrainApproval;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class GrainObserver
{
    /**
     * Handle the Grain "created" event.
     */
    public function created(Grain $grain): void
    {
        static::AmortizationScheduled($grain);
    }

    /**
     * Handle the Grain "updated" event.
     */
    public function updated(Grain $grain): void
    {
        // Check if the 'amount' attribute has changed
        if ($grain->isDirty('amount') || $grain->isDirty('terms') || $grain->isDirty('rate') || $grain->isDirty('start_date')) {
            // Delete existing LoanAmort records for this loan
            GrainAmort::where('grain_id', $grain->slug)->forceDelete();

            // Recreate the amortization schedule
            static::AmortizationScheduled($grain);
        }
    }

    /**
     * Handle the Grain "deleted" event.
     */
    public function deleted(Grain $grain): void
    {
        //
    }

    /**
     * Handle the Grain "restored" event.
     */
    public function restored(Grain $grain): void
    {
        //
    }

    /**
     * Handle the Grain "force deleted" event.
     */
    public function forceDeleted(Grain $grain): void
    {
        GrainAmort::where('grain_id', $grain->slug)->forceDelete();
        GrainApproval::where('grain_id', $grain->slug)->forceDelete();
    }

    public function AmortizationScheduled(Grain $grain): void
    {
        if (GrainApproval::where('grain_id', $grain->slug)->doesntExist()) {
            GrainApproval::create([
                'grain_id' => $grain->slug,
            ]);
        }

        $amount = $grain->amount;
        $terms = $grain->terms;
        $rate = $grain->rate / 100 / $terms;
        $totalInterest = $amount * $rate * $terms;
        $payment = ($amount + $totalInterest) / $terms;

        $remainingBalance = $amount;
        $startDate = Carbon::parse($grain->start_date);
        $fixedInterest = $totalInterest / $terms;

        for ($i = 0; $i < $terms; $i++) {

            $principal = $payment - $fixedInterest;

            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principal;

            GrainAmort::create([
                'grain_id' => $grain->slug,
                'member_id' => $grain->member_id,
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

        $user = $grain->member->user;
        Notification::make()
            ->title('Grain Request Successful')
            ->body("{$grain->member->name} Grain request  Successfully")
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url("loans")
            ])
            ->sendToDatabase($user);

        $admins = User::whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->get();
        Notification::make()
            ->title("{$grain->member->name} Requested Grain")
            ->body("{$grain->member->name} Requested grain for your action sir.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->markAsRead()
                    ->url("loans")
            ])
            ->sendToDatabase($admins);
    }
}

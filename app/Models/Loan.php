<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Loan extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'staff_id');
    }

    public function loanAmort():HasMany
    {
        return $this->hasMany(LoanAmort::class, 'loan_id', 'slug');
    }
    public function guarantors():HasMany
    {
        return $this->hasMany(Guarantor::class, 'loan_id', 'slug');
    }
    public function approvals():HasOne
    {
        return $this->hasOne(ApprovedLoan::class, 'loan_id', 'slug');
    }
    protected $casts = [
        'start_date' => 'date',
    ];
    protected static function boot()
    {
        parent::boot();

        // Add the updated event to trigger afterUpdate
        static::updated(function ($loan) {
            $loan->afterUpdate();
        });
    }

    // The afterUpdate method to recalculate the amortization schedule
    protected function afterUpdate(): void
    {
        // Same logic as the afterCreate
        LoanAmort::where('loan_id', $this->slug)->delete();

        $amount = $this->amount;
        $rate = $this->rate / 100 / 12;
        $terms = $this->terms;
        $payment = $amount * ($rate / (1 - pow(1 + $rate, -$terms)));

        $remainingBalance = $amount;
        $startDate = Carbon::parse($this->start_date);

        for ($i = 0; $i < $terms; $i++) {
            $interest = $remainingBalance * $rate;
            $principal = $payment - $interest;

            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principal;

            LoanAmort::create([
                'loan_id' => $this->slug,
                'loan_owner' => $this->applicant_id,
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

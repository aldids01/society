<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Applicant;
use App\Models\Guarantor;
use App\Models\LoanAmort;
use App\Models\ApprovedLoan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grain extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'staff_id');
    }

    public function amorts():HasMany
    {
        return $this->hasMany(GrainAmort::class, 'grain_id', 'slug');
    }
    public function approvals():HasOne
    {
        return $this->hasOne(ApprovedGrain::class, 'grain_id', 'slug');
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
        GrainAmort::where('grain_id', $this->slug)->delete();

        $amount = $this->amount;
        $terms = $this->terms;
        $rate = $this->rate / 100 / $terms;
        $totalInterest = $amount * $rate * $terms;
        $payment = ($amount + $totalInterest) / $terms;

        $remainingBalance = $amount;
        $startDate = Carbon::parse($this->start_date);
        $fixedInterest = $totalInterest / $terms;

        for ($i = 0; $i < $terms; $i++) {

            $principal = $payment - $fixedInterest;

            $paymentDate = $startDate->copy()->addMonths($i);
            $endBalance = $remainingBalance - $principal;

            GrainAmort::create([
                'grain_id' => $this->slug,
                'grain_owner' => $this->applicant_id,
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

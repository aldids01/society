<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Loan extends Model
{
    /** @use HasFactory<\Database\Factories\LoanFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }

    public function guarantors(): HasMany
    {
        return $this->hasMany(Guarantor::class, 'loan_id', 'slug');
    }

    public function loanAmorts(): HasMany
    {
        return $this->hasMany(LoanAmort::class, 'loan_id', 'slug');
    }

    public function loanApproval(): HasOne
    {
        return $this->hasOne(LoanApproval::class, 'loan_id', 'slug');
    }

    protected $casts = [
        'start_date' => 'date',
    ];
}

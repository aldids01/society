<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanApproval extends Model
{
    protected $guarded = [];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'slug');
    }

    public function check(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'checkby', 'slug');
    }

    public function approve(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'approvedby', 'slug');
    }

    public function disburse(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'disbursedby', 'slug');
    }

    protected $casts = [
        'checkdate' => 'datetime',
        'approveddate' => 'datetime',
        'disburseddate' => 'datetime',
    ];
}

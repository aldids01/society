<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrainApproval extends Model
{
    protected $guarded = [];
    protected $casts = [
        'checkeddate' => 'datetime',
        'approveddate' => 'datetime',
        'disburseddate' => 'datetime',
    ];

    public function grain(): BelongsTo
    {
        return $this->belongsTo(Grain::class, 'loan_id', 'slug');
    }

    public function check(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'checkedby', 'slug');
    }

    public function approve(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'approvedby', 'slug');
    }

    public function disburse(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'disbursedby', 'slug');
    }
}

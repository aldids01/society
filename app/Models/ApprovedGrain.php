<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApprovedGrain extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function loan() : BelongsTo {
        return $this->belongsTo(Grain::class, 'grain_id', 'slug');
    }
    public function approve() : BelongsTo {
        return $this->belongsTo(Applicant::class, 'approvedby', 'staff_id');
    }
    public function check() : BelongsTo {
        return $this->belongsTo(Applicant::class, 'checkedby', 'staff_id');
    }
    public function disburse() : BelongsTo {
        return $this->belongsTo(Applicant::class, 'disbursedby', 'staff_id');
    }

    protected $casts = [
        'checkeddate' => 'datetime',
        'approveddate' => 'datetime',
        'disburseddate' => 'datetime',
    ];

}

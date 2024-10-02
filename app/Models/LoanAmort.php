<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAmort extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'loan_owner', 'staff_id');
    }
    public function loan():BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'slug');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guarantor extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'guarantor_name', 'staff_id');
    }

    public function loan():BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'slug');
    }
}

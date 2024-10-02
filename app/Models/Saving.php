<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saving extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'staff_id');
    }
}

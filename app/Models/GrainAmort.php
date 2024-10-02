<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrainAmort extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function applicant():BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'grain_owner', 'staff_id');
    }
    public function grain():BelongsTo
    {
        return $this->belongsTo(Grain::class, 'grain_id', 'slug');
    }
}

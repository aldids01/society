<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function tsaved():HasMany
    {
        return $this->hasMany(Saving::class, 'applicant_id', 'staff_id');
    }
    public function loan() : HasMany {
        return $this->hasMany(Loan::class, 'applicant_id', 'staff_id');
    }
    public function grain() : HasMany {
        return $this->hasMany(Grain::class, 'applicant_id', 'staff_id');
    }

    public function loanAmort(): HasMany
    {
        return $this->hasMany(LoanAmort::class, 'loan_owner', 'staff_id');
    }
    public function grainAmort(): HasMany
    {
        return $this->hasMany(GrainAmort::class, 'grain_owner', 'staff_id');
    }

    protected array $dates = ['deleted_at'];
}

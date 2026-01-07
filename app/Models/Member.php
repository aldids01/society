<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanAmorts(): HasMany
    {
        return $this->hasMany(LoanAmort::class, 'member_id', 'slug');
    }
    public function grainAmorts(): HasMany
    {
        return $this->hasMany(GrainAmort::class, 'member_id', 'slug');
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class, 'member_id', 'slug');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'member_id', 'slug');
    }
    public function grains(): HasMany
    {
        return $this->hasMany(Grain::class, 'member_id', 'slug');
    }

    public function checkby(): HasMany
    {
        return $this->hasMany(LoanApproval::Class, 'checkby', 'slug');
    }

    public function approvedby(): HasMany
    {
        return $this->hasMany(LoanApproval::Class, 'approvedby', 'slug');
    }

    public function disbursedby(): HasMany
    {
        return $this->hasMany(LoanApproval::Class, 'disbursedby', 'slug');
    }
}

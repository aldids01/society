<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;
use Rupadana\ApiService\Contracts\HasAllowedSorts;

class Member extends Model implements HasAllowedFields, HasAllowedSorts, HasAllowedFilters
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];
// Which fields can be selected from the database through the query string
    public static function getAllowedFields(): array
    {
        return [
            'slug',
            'name',
            'gender',
            'phone',
            'kin_name',
            'kin_phone',
            'saving',
            'status'
        ];
    }

    // Which fields can be used to sort the results through the query string
    public static function getAllowedSorts(): array
    {
        return [
            'name'
        ];
    }

    // Which fields can be used to filter the results through the query string
    public static function getAllowedFilters(): array
    {
       return [
           'slug',
           'name',
       ];
    }
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rupadana\ApiService\Contracts\HasAllowedFilters;
use Spatie\QueryBuilder\AllowedFilter;

class LoanAmort extends Model
{
    /** @use HasFactory<\Database\Factories\LoanAmortFactory> */
    use HasFactory, SoftDeletes;


    protected $guarded = [];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'slug');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }
}

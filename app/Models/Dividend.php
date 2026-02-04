<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dividend extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function dividend_rate(): BelongsTo
    {
        return $this->belongsTo(DividendRate::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }
}

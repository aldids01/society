<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrainAmort extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function grain(): BelongsTo
    {
        return $this->belongsTo(Grain::class, 'grain_id', 'slug');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::Class, 'member_id', 'slug');
    }
}

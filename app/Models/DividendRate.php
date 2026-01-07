<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DividendRate extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function dividend(): HasOne
    {
        return $this->hasOne(Dividend::class);
    }
}

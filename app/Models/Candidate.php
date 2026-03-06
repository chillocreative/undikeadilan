<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = ['election_id', 'name', 'nomination_count', 'votes', 'sort_order'];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}

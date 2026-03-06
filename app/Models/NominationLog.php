<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NominationLog extends Model
{
    protected $fillable = ['election_id', 'ip_address', 'session_token'];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}

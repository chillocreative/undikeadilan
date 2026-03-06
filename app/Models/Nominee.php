<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nominee extends Model
{
    protected $fillable = ['election_id', 'name', 'submitted_by_ip', 'session_token'];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}

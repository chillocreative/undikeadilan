<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'phase',
        'max_nominations', 'max_winners', 'winner_labels',
        'nomination_ends_at', 'voting_ends_at', 'votes_reset_at',
    ];

    protected $casts = [
        'winner_labels' => 'array',
        'nomination_ends_at' => 'datetime',
        'voting_ends_at' => 'datetime',
        'votes_reset_at' => 'datetime',
    ];

    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function nominationLogs(): HasMany
    {
        return $this->hasMany(NominationLog::class);
    }
}

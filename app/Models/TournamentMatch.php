<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentMatchFactory> */
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'round',
        'match_number',
        'team1',
        'team2',
        'winner',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}

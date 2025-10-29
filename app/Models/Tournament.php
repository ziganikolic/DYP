<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    /** @use HasFactory<\Database\Factories\TournamentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'teams',
    ];

    protected function casts(): array
    {
        return [
            'teams' => 'array',
        ];
    }

    public function matches()
    {
        return $this->hasMany(TournamentMatch::class);
    }
}

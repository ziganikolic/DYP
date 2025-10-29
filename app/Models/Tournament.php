<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tournament) {
            if (!$tournament->code) {
                $tournament->code = strtoupper(Str::random(8));
            }
        });
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class)->orderBy('order');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match::class)->orderBy('round_number')->orderBy('position');
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}

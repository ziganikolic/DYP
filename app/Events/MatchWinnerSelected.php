<?php

namespace App\Events;

use App\Models\Match;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MatchWinnerSelected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Match $match
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('tournament.' . $this->match->tournament->code),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'match' => [
                'id' => $this->match->id,
                'round_number' => $this->match->round_number,
                'team1' => $this->match->team1,
                'team2' => $this->match->team2,
                'winner' => $this->match->winner,
                'position' => $this->match->position,
            ],
        ];
    }
}

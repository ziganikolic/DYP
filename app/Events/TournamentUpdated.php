<?php

namespace App\Events;

use App\Models\Tournament;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TournamentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Tournament $tournament
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('tournament.' . $this->tournament->code),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'tournament' => [
                'id' => $this->tournament->id,
                'code' => $this->tournament->code,
                'status' => $this->tournament->status,
                'teams' => $this->tournament->teams,
                'matches' => $this->tournament->matches()->with(['team1', 'team2', 'winner'])->get(),
                'updated_at' => $this->tournament->updated_at,
            ],
        ];
    }
}

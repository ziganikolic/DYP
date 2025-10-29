<?php

namespace App\Http\Controllers;

use App\Events\TournamentUpdated;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public function index()
    {
        return Inertia::render('Tournament/Index', [
            'tournaments' => Tournament::latest()->get(),
        ]);
    }

    public function show(Tournament $tournament)
    {
        return Inertia::render('Tournament/Show', [
            'tournament' => $tournament->load('matches'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'players' => 'required|array|min:4',
            'players.*' => 'required|string',
        ]);

        // Shuffle players and create teams
        $players = $validated['players'];
        shuffle($players);

        $teams = [];
        for ($i = 0; $i < count($players); $i += 2) {
            if (isset($players[$i + 1])) {
                $teams[] = "{$players[$i]} & {$players[$i + 1]}";
            } else {
                $teams[] = "{$players[$i]} & (bez partnerja)";
            }
        }

        // Create tournament
        $tournament = Tournament::create([
            'name' => $validated['name'],
            'status' => 'in_progress',
            'teams' => $teams,
        ]);

        // Create first round matches
        $this->createMatches($tournament, $teams, 1);

        broadcast(new TournamentUpdated($tournament->load('matches')))->toOthers();

        return redirect()->route('tournaments.show', $tournament);
    }

    public function selectWinner(Tournament $tournament, TournamentMatch $match, Request $request)
    {
        $validated = $request->validate([
            'winner' => 'required|string',
        ]);

        $match->update(['winner' => $validated['winner']]);

        // Check if all matches in current round are complete
        $currentRoundMatches = $tournament->matches()->where('round', $match->round)->get();
        $allComplete = $currentRoundMatches->every(fn ($m) => $m->winner !== null);

        if ($allComplete) {
            $winners = $currentRoundMatches->pluck('winner')->toArray();

            // If only one winner, tournament is complete
            if (count($winners) === 1) {
                $tournament->update(['status' => 'completed']);
            } else {
                // Create next round
                $this->createMatches($tournament, $winners, $match->round + 1);
            }
        }

        $updatedTournament = $tournament->fresh()->load('matches');

        // Broadcast to ALL users including the one who clicked
        broadcast(new TournamentUpdated($updatedTournament));

        return back();
    }

    protected function createMatches(Tournament $tournament, array $teams, int $round): void
    {
        // Ensure even number of teams
        if (count($teams) % 2 !== 0) {
            $teams[] = '(prosta ekipa)';
        }

        $matchNumber = 1;
        for ($i = 0; $i < count($teams); $i += 2) {
            TournamentMatch::create([
                'tournament_id' => $tournament->id,
                'round' => $round,
                'match_number' => $matchNumber,
                'team1' => $teams[$i],
                'team2' => $teams[$i + 1],
            ]);
            $matchNumber++;
        }
    }
}

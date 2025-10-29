<?php

namespace App\Http\Controllers;

use App\Events\MatchWinnerSelected;
use App\Events\TournamentUpdated;
use App\Models\Match;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TournamentController extends Controller
{
    public function index()
    {
        return view('tournament.index');
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'players' => 'required|array|min:4',
            'players.*' => 'required|string|max:100',
        ]);

        $tournament = DB::transaction(function () use ($validated) {
            $tournament = Tournament::create([
                'status' => 'setup',
            ]);

            // Shuffle players
            $players = $validated['players'];
            shuffle($players);

            // Create teams (pairs of 2)
            $teams = [];
            for ($i = 0; $i < count($players); $i += 2) {
                $player1 = $players[$i];
                $player2 = $players[$i + 1] ?? null;

                $teamName = $player2
                    ? "{$player1} & {$player2}"
                    : "{$player1} (brez partnerja)";

                $team = Team::create([
                    'tournament_id' => $tournament->id,
                    'player1_name' => $player1,
                    'player2_name' => $player2,
                    'name' => $teamName,
                ]);

                $teams[] = $team;
            }

            // Create first round matches
            $this->createRoundMatches($tournament, $teams, 1);

            $tournament->update(['status' => 'in_progress']);

            return $tournament;
        });

        broadcast(new TournamentUpdated($tournament->fresh()));

        return response()->json([
            'success' => true,
            'code' => $tournament->code,
        ]);
    }

    public function show($code)
    {
        $tournament = Tournament::where('code', $code)
            ->with(['teams', 'matches.team1', 'matches.team2', 'matches.winner'])
            ->firstOrFail();

        return view('tournament.show', [
            'tournament' => $tournament,
        ]);
    }

    public function data($code)
    {
        $tournament = Tournament::where('code', $code)
            ->with(['teams', 'matches.team1', 'matches.team2', 'matches.winner'])
            ->firstOrFail();

        return response()->json([
            'tournament' => $tournament,
        ]);
    }

    public function selectWinner(Request $request, $code, $matchId)
    {
        $validated = $request->validate([
            'winner_team_id' => 'required|exists:teams,id',
        ]);

        $tournament = Tournament::where('code', $code)->firstOrFail();
        $match = Match::where('tournament_id', $tournament->id)
            ->where('id', $matchId)
            ->firstOrFail();

        // Check if already has winner
        if ($match->winner_team_id) {
            return response()->json([
                'error' => 'Match already has a winner',
            ], 400);
        }

        DB::transaction(function () use ($match, $validated, $tournament) {
            // Update match with winner
            $match->update([
                'winner_team_id' => $validated['winner_team_id'],
            ]);

            broadcast(new MatchWinnerSelected($match->fresh()));

            // Check if round is complete
            $roundNumber = $match->round_number;
            $roundMatches = Match::where('tournament_id', $tournament->id)
                ->where('round_number', $roundNumber)
                ->get();

            $allMatchesComplete = $roundMatches->every(fn($m) => $m->winner_team_id !== null);

            if ($allMatchesComplete) {
                $winners = $roundMatches->map(fn($m) => $m->winner)->filter();

                if ($winners->count() === 1) {
                    // Tournament complete
                    $tournament->update(['status' => 'completed']);
                } else {
                    // Create next round
                    $this->createRoundMatches($tournament, $winners->all(), $roundNumber + 1);
                }

                broadcast(new TournamentUpdated($tournament->fresh()));
            }
        });

        return response()->json([
            'success' => true,
        ]);
    }

    private function createRoundMatches(Tournament $tournament, array $teams, int $roundNumber): void
    {
        // Add bye if odd number of teams
        if (count($teams) % 2 !== 0) {
            $teams[] = null; // Bye team
        }

        $position = 0;
        for ($i = 0; $i < count($teams); $i += 2) {
            $team1 = $teams[$i];
            $team2 = $teams[$i + 1] ?? null;

            Match::create([
                'tournament_id' => $tournament->id,
                'round_number' => $roundNumber,
                'team1_id' => $team1?->id,
                'team2_id' => $team2?->id,
                'position' => $position,
            ]);

            $position++;
        }
    }
}

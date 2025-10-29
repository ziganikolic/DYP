<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->integer('round_number');
            $table->foreignId('team1_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('team2_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->foreignId('winner_team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->integer('position')->default(0); // Position in round
            $table->timestamps();

            $table->index(['tournament_id', 'round_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};

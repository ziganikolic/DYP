<template>
  <div class="min-h-screen bg-gray-100 p-4">
    <div class="max-w-7xl mx-auto">
      <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">
          🏆 Turnir dvojic – Izpadanje
        </h1>
        <div class="text-center">
          <span class="inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-mono">
            Koda turnirja: {{ code }}
          </span>
        </div>
        <div v-if="tournament.status === 'completed'" class="mt-4 text-center">
          <div class="inline-block bg-green-100 border-2 border-green-400 rounded-lg p-4">
            <p class="text-lg font-semibold text-gray-700">🏅 Zmagovalca turnirja sta</p>
            <p class="text-2xl font-bold text-green-700 mt-2">{{ winner?.name }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-6 overflow-x-auto">
        <div class="flex space-x-8 min-w-max">
          <div
            v-for="(round, roundIndex) in rounds"
            :key="roundIndex"
            class="flex-shrink-0"
            style="min-width: 280px"
          >
            <h2 class="text-xl font-bold text-center mb-4 text-gray-700">
              {{ getRoundName(roundIndex + 1, rounds.length) }}
            </h2>
            <div class="space-y-4">
              <div
                v-for="match in round"
                :key="match.id"
                class="bg-gray-50 rounded-lg border-2 border-gray-200 p-3"
              >
                <div
                  v-if="match.team1"
                  @click="selectWinner(match, match.team1_id)"
                  class="p-3 mb-2 rounded-lg cursor-pointer transition-all"
                  :class="getTeamClass(match, match.team1_id)"
                >
                  {{ match.team1.name }}
                </div>
                <div v-else class="p-3 mb-2 rounded-lg bg-gray-100 text-gray-400 text-center">
                  (prosta ekipa)
                </div>

                <div
                  v-if="match.team2"
                  @click="selectWinner(match, match.team2_id)"
                  class="p-3 rounded-lg cursor-pointer transition-all"
                  :class="getTeamClass(match, match.team2_id)"
                >
                  {{ match.team2.name }}
                </div>
                <div v-else class="p-3 rounded-lg bg-gray-100 text-gray-400 text-center">
                  (prosta ekipa)
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 text-center text-sm text-gray-600">
        <p>Klikni na ekipo, da jo označiš kot zmagovalca tekme</p>
        <p class="mt-1">Rezultati se posodabljajo v realnem času za vse udeležence</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  code: {
    type: String,
    required: true
  },
  initialTournament: {
    type: Object,
    required: true
  }
});

const tournament = ref(props.initialTournament);

const rounds = computed(() => {
  if (!tournament.value.matches) return [];

  const matchesByRound = {};
  tournament.value.matches.forEach(match => {
    if (!matchesByRound[match.round_number]) {
      matchesByRound[match.round_number] = [];
    }
    matchesByRound[match.round_number].push(match);
  });

  return Object.values(matchesByRound);
});

const winner = computed(() => {
  if (tournament.value.status !== 'completed') return null;

  const lastRound = rounds.value[rounds.value.length - 1];
  if (!lastRound || !lastRound[0]) return null;

  return lastRound[0].winner;
});

const getRoundName = (roundNumber, totalRounds) => {
  const teamsInRound = Math.pow(2, totalRounds - roundNumber + 1);

  if (roundNumber === totalRounds) return 'Finale';
  if (roundNumber === totalRounds - 1) return 'Polfinale';
  if (roundNumber === totalRounds - 2) return 'Četrtfinale';

  return `Krog ${roundNumber}`;
};

const getTeamClass = (match, teamId) => {
  if (!match.winner_team_id) {
    return 'bg-white hover:bg-blue-50 border border-gray-300';
  }

  if (match.winner_team_id === teamId) {
    return 'bg-green-200 border-2 border-green-500 font-bold';
  }

  return 'bg-gray-200 border border-gray-300 opacity-50';
};

const selectWinner = async (match, teamId) => {
  if (match.winner_team_id) return; // Already has winner
  if (!teamId) return; // Bye team

  try {
    await window.axios.post(`/api/tournaments/${props.code}/matches/${match.id}/winner`, {
      winner_team_id: teamId
    });

    // Update will come via websocket
  } catch (err) {
    console.error('Error selecting winner:', err);
    alert('Napaka pri izbiri zmagovalca. Poskusi ponovno.');
  }
};

onMounted(() => {
  // Subscribe to tournament updates via Laravel Echo
  window.Echo.channel(`tournament.${props.code}`)
    .listen('TournamentUpdated', (e) => {
      console.log('Tournament updated:', e);
      tournament.value = e.tournament;
    })
    .listen('MatchWinnerSelected', (e) => {
      console.log('Match winner selected:', e);

      // Update the specific match
      const matchIndex = tournament.value.matches.findIndex(m => m.id === e.match.id);
      if (matchIndex !== -1) {
        tournament.value.matches[matchIndex] = {
          ...tournament.value.matches[matchIndex],
          ...e.match
        };
      }
    });
});
</script>

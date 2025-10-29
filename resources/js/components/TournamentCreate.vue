<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-xl p-8">
      <h1 class="text-4xl font-bold text-center mb-2 text-gray-800">
        🏆 Turnir dvojic – Izpadanje
      </h1>
      <p class="text-center text-gray-600 mb-8">
        Ustvari nov turnir in povabi svoje kolege!
      </p>

      <div v-if="!created">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Vnesi imena igralcev (enega na vrstico):
        </label>
        <textarea
          v-model="playersText"
          class="w-full h-48 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          placeholder="Janez&#10;Mojca&#10;Peter&#10;Ana&#10;..."
        ></textarea>

        <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
          {{ error }}
        </div>

        <button
          @click="startTournament"
          :disabled="loading"
          class="mt-6 w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-md"
        >
          {{ loading ? 'Pripravljam...' : '🎲 Začni žreb' }}
        </button>
      </div>

      <div v-else class="text-center">
        <div class="mb-6 p-6 bg-green-50 border border-green-200 rounded-lg">
          <p class="text-lg text-gray-700 mb-2">Turnir je ustvarjen!</p>
          <p class="text-sm text-gray-600 mb-4">Deli ta link s kolegi:</p>
          <div class="flex items-center justify-center space-x-2">
            <input
              ref="linkInput"
              type="text"
              :value="tournamentUrl"
              readonly
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-white"
            />
            <button
              @click="copyLink"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
            >
              {{ copied ? '✓ Kopirano' : 'Kopiraj' }}
            </button>
          </div>
        </div>
        <a
          :href="tournamentUrl"
          class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-md"
        >
          Pojdi na turnir →
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const playersText = ref('');
const loading = ref(false);
const error = ref('');
const created = ref(false);
const tournamentCode = ref('');
const tournamentUrl = ref('');
const copied = ref(false);
const linkInput = ref(null);

const startTournament = async () => {
  error.value = '';

  const players = playersText.value
    .split('\n')
    .map(p => p.trim())
    .filter(p => p);

  if (players.length < 4) {
    error.value = 'Za turnir dvojic potrebuješ vsaj 4 igralce.';
    return;
  }

  loading.value = true;

  try {
    const response = await window.axios.post('/api/tournaments', {
      players: players
    });

    tournamentCode.value = response.data.code;
    tournamentUrl.value = `${window.location.origin}/tournament/${response.data.code}`;
    created.value = true;
  } catch (err) {
    error.value = 'Napaka pri ustvarjanju turnirja. Poskusi ponovno.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const copyLink = () => {
  if (linkInput.value) {
    linkInput.value.select();
    document.execCommand('copy');
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  }
};
</script>

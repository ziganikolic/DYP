<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import tournamentsRoutes from '@/routes/tournaments';
import { ref, computed } from 'vue';

interface Tournament {
    id: number;
    name: string;
    status: string;
    teams: string[];
    created_at: string;
}

defineProps<{
    tournaments: Tournament[];
}>();

const showNewModal = ref(false);
const name = ref('Turnir ' + new Date().toLocaleDateString('sl-SI'));
const players = ref<string[]>(['', '', '', '']); // Start with 4 empty fields

const addPlayer = () => {
    players.value.push('');
};

const removePlayer = (index: number) => {
    if (players.value.length > 2) {
        players.value.splice(index, 1);
    }
};

const addMultiplePlayers = (count: number) => {
    for (let i = 0; i < count; i++) {
        players.value.push('');
    }
};

const validPlayersCount = computed(() => {
    return players.value.filter((p) => p.trim()).length;
});

const canSubmit = computed(() => {
    return validPlayersCount.value >= 4;
});

const createTournament = () => {
    const validPlayers = players.value.map((p) => p.trim()).filter((p) => p);

    if (validPlayers.length < 4) {
        alert('Za turnir dvojic potrebuješ vsaj 4 igralce.');
        return;
    }

    router.post(
        tournamentsRoutes.store.url(),
        {
            name: name.value,
            players: validPlayers,
        },
        {
            onSuccess: () => {
                showNewModal.value = false;
                name.value = 'Turnir ' + new Date().toLocaleDateString('sl-SI');
                players.value = ['', '', '', ''];
            },
        },
    );
};
</script>

<template>
    <Head title="Turnir Dvojic" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
        <!-- Animated background pattern -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-30">
            <div class="absolute -left-4 -top-4 size-72 animate-blob rounded-full bg-purple-300 mix-blend-multiply blur-xl filter dark:bg-purple-900"></div>
            <div class="animation-delay-2000 absolute -right-4 -top-4 size-72 animate-blob rounded-full bg-yellow-300 mix-blend-multiply blur-xl filter dark:bg-yellow-900"></div>
            <div class="animation-delay-4000 absolute -bottom-8 left-20 size-72 animate-blob rounded-full bg-pink-300 mix-blend-multiply blur-xl filter dark:bg-pink-900"></div>
        </div>

        <div class="relative px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div class="mb-12 text-center">
                    <div class="mb-4 inline-flex items-center gap-3 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-2 text-white shadow-lg">
                        <span class="text-3xl">🏆</span>
                        <span class="font-bold">Turnir Dvojic</span>
                    </div>
                    <h1 class="mb-3 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-4xl font-extrabold text-transparent sm:text-5xl md:text-6xl">
                        Izpadanje
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Ustvari turnir in sledi zmagovalcem v realnem času
                    </p>
                </div>

                <!-- New Tournament Button -->
                <div class="mb-12 text-center">
                    <button
                        @click="showNewModal = true"
                        class="group relative inline-flex items-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-4 font-bold text-white shadow-lg transition-all hover:scale-105 hover:shadow-xl active:scale-95"
                    >
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ustvari Nov Turnir
                        </span>
                        <div class="absolute inset-0 -z-0 bg-gradient-to-r from-blue-700 to-purple-700 opacity-0 transition-opacity group-hover:opacity-100"></div>
                    </button>
                </div>

                <!-- Tournament List -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="tournament in tournaments"
                        :key="tournament.id"
                        :href="tournamentsRoutes.show.url({ tournament: tournament.id })"
                        class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-800"
                    >
                        <!-- Gradient overlay on hover -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5 opacity-0 transition-opacity group-hover:opacity-100"></div>

                        <div class="relative">
                            <!-- Status badge -->
                            <div class="mb-4 flex items-center justify-between">
                                <span
                                    v-if="tournament.status === 'in_progress'"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400"
                                >
                                    <span class="relative flex size-2">
                                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex size-2 rounded-full bg-green-500"></span>
                                    </span>
                                    V teku
                                </span>
                                <span
                                    v-else-if="tournament.status === 'completed'"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                >
                                    <svg class="size-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Zaključen
                                </span>
                            </div>

                            <!-- Tournament name -->
                            <h3 class="mb-3 text-xl font-bold text-gray-900 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                                {{ tournament.name }}
                            </h3>

                            <!-- Meta info -->
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="font-medium">{{ tournament.teams.length }} ekip</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>{{ new Date(tournament.created_at).toLocaleDateString('sl-SI') }}</span>
                                </div>
                            </div>

                            <!-- Arrow icon -->
                            <div class="mt-4 flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400">
                                <span>Odpri turnir</span>
                                <svg class="ml-1 size-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <!-- Empty state -->
                    <div
                        v-if="tournaments.length === 0"
                        class="col-span-full rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50/50 p-16 text-center backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/50"
                    >
                        <div class="mb-4 text-6xl">🎯</div>
                        <p class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300">
                            Ni še turnirjev
                        </p>
                        <p class="text-gray-500 dark:text-gray-400">
                            Klikni zgoraj in ustvari prvi turnir!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Tournament Modal -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showNewModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="showNewModal = false"
        >
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="scale-95 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-95 opacity-0"
            >
                <div v-if="showNewModal" class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                        <h2 class="text-2xl font-bold">Ustvari Nov Turnir</h2>
                        <p class="mt-1 text-sm text-blue-100">Vnesi igralce in začni žrebanje</p>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="createTournament" class="p-6">
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Ime turnirja
                            </label>
                            <input
                                v-model="name"
                                type="text"
                                required
                                class="w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-gray-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-400"
                            />
                        </div>

                        <div class="mb-4">
                            <div class="mb-3 flex items-center justify-between">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    Igralci
                                </label>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="
                                            canSubmit
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                                        "
                                    >
                                        {{ validPlayersCount }} / minimum 4
                                    </span>
                                </div>
                            </div>

                            <!-- Quick add buttons -->
                            <div class="mb-3 flex gap-2">
                                <button
                                    type="button"
                                    @click="addMultiplePlayers(4)"
                                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                >
                                    + 4 igralce
                                </button>
                                <button
                                    type="button"
                                    @click="addMultiplePlayers(8)"
                                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                >
                                    + 8 igralcev
                                </button>
                            </div>

                            <!-- Players list with max height and scroll -->
                            <div class="max-h-96 space-y-2 overflow-y-auto rounded-xl border-2 border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700/50">
                                <div v-for="(player, index) in players" :key="index" class="flex items-center gap-2">
                                    <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-bold">
                                        {{ index + 1 }}
                                    </div>
                                    <input
                                        v-model="players[index]"
                                        type="text"
                                        :placeholder="`Igralec ${index + 1}`"
                                        class="flex-1 rounded-lg border-2 border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 transition focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400"
                                    />
                                    <button
                                        type="button"
                                        @click="removePlayer(index)"
                                        :disabled="players.length <= 2"
                                        class="flex-shrink-0 rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-gray-400 dark:hover:bg-red-900/20"
                                        title="Odstrani igralca"
                                    >
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Add player button -->
                            <button
                                type="button"
                                @click="addPlayer"
                                class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border-2 border-dashed border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:border-blue-400 hover:bg-blue-50 hover:text-blue-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-blue-500 dark:hover:bg-blue-900/20 dark:hover:text-blue-400"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Dodaj igralca
                            </button>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex gap-3">
                            <button
                                type="submit"
                                :disabled="!canSubmit"
                                class="group relative flex-1 overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 font-bold text-white shadow-lg transition-all hover:shadow-xl active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:shadow-lg disabled:active:scale-100"
                            >
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Začni Žreb
                                </span>
                            </button>
                            <button
                                type="button"
                                @click="showNewModal = false"
                                class="rounded-xl border-2 border-gray-200 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 active:scale-95 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Prekliči
                            </button>
                        </div>
                    </form>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>

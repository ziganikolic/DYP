<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import tournamentsRoutes from '@/routes/tournaments';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface TournamentMatch {
    id: number;
    tournament_id: number;
    round: number;
    match_number: number;
    team1: string;
    team2: string;
    winner: string | null;
}

interface Tournament {
    id: number;
    name: string;
    status: string;
    teams: string[];
    matches: TournamentMatch[];
}

const props = defineProps<{
    tournament: Tournament;
}>();

const tournamentData = ref<Tournament>(props.tournament);
const showQRModal = ref(false);
const shareUrl = ref('');


const shareTournament = async () => {
    const url = window.location.href;
    const text = `Poglej turnir: ${tournamentData.value.name}`;

    // Try native share first (works on mobile)
    if (navigator.share) {
        try {
            await navigator.share({
                title: tournamentData.value.name,
                text: text,
                url: url,
            });
        } catch (err) {
            // User cancelled or error occurred
            console.log('Share cancelled or failed');
        }
    } else {
        // Fallback: show QR code modal
        showQRModal.value = true;
    }
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href);
        alert('Link kopiran!');
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};

const generateQRCode = () => {
    // Using a simple QR code API
    return `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(shareUrl.value)}`;
};

// Group matches by round
const rounds = computed(() => {
    const grouped: Record<number, TournamentMatch[]> = {};

    tournamentData.value.matches.forEach((match) => {
        if (!grouped[match.round]) {
            grouped[match.round] = [];
        }
        grouped[match.round].push(match);
    });

    return Object.keys(grouped)
        .map(Number)
        .sort((a, b) => a - b)
        .map((round) => ({
            number: round,
            matches: grouped[round],
        }));
});

const getRoundTitle = (round: number, totalRounds: number) => {
    if (round === totalRounds) {
        return 'Finale';
    } else if (round === totalRounds - 1) {
        return 'Polfinale';
    } else if (round === totalRounds - 2) {
        return 'Četrtfinale';
    }
    return `Krog ${round}`;
};

const selectWinner = (match: TournamentMatch, winner: string) => {
    if (match.winner || winner === '(prosta ekipa)') {
        return;
    }

    router.post(
        tournamentsRoutes.selectWinner.url({
            tournament: tournamentData.value.id,
            match: match.id,
        }),
        { winner },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Update will come via broadcast event for all users
            },
        },
    );
};

// Real-time updates via Echo
let channel: any = null;

onMounted(() => {
    // Set share URL
    shareUrl.value = window.location.href;

    // Setup Echo for real-time updates
    if (typeof window !== 'undefined' && (window as any).Echo) {
        console.log('🔌 Connecting to tournament channel:', tournamentData.value.id);
        channel = (window as any).Echo.channel(`tournament.${tournamentData.value.id}`);

        // Listen to the event - Laravel broadcasts events with full namespace
        channel.listen('TournamentUpdated', (e: { tournament: Tournament }) => {
            console.log('📡 Received tournament update:', e);
            tournamentData.value = e.tournament;
        });

        // Also listen with dot notation (fallback)
        channel.listen('.App\\Events\\TournamentUpdated', (e: { tournament: Tournament }) => {
            console.log('📡 Received tournament update (full namespace):', e);
            tournamentData.value = e.tournament;
        });
    } else {
        console.error('❌ Echo not initialized!');
    }
});

onUnmounted(() => {
    if (channel) {
        console.log('🔌 Leaving tournament channel');
        channel.stopListening('TournamentUpdated');
        channel.stopListening('.App\\Events\\TournamentUpdated');
        (window as any).Echo.leave(`tournament.${tournamentData.value.id}`);
    }
});

const finalWinner = computed(() => {
    if (tournamentData.value.status !== 'completed') {
        return null;
    }
    const lastRound = rounds.value[rounds.value.length - 1];
    return lastRound?.matches[0]?.winner || null;
});
</script>

<template>
    <Head :title="tournament.name" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
        <!-- Animated background -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-20">
            <div class="absolute -left-4 -top-4 size-96 animate-blob rounded-full bg-purple-300 mix-blend-multiply blur-xl filter dark:bg-purple-900"></div>
            <div class="animation-delay-2000 absolute right-0 top-0 size-96 animate-blob rounded-full bg-yellow-300 mix-blend-multiply blur-xl filter dark:bg-yellow-900"></div>
        </div>

        <div class="relative">
            <!-- Header -->
            <div class="border-b border-gray-200 bg-white/80 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/80">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex-1">
                            <a
                                :href="tournamentsRoutes.index.url()"
                                class="group mb-2 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 transition hover:gap-2 dark:text-blue-400"
                            >
                                <svg class="size-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Nazaj na vse turnirje
                            </a>
                            <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl dark:text-white">
                                {{ tournamentData.name }}
                            </h1>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Share button -->
                            <button
                                @click="shareTournament"
                                class="group flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 font-semibold text-white shadow-lg transition-all hover:scale-105 hover:shadow-xl active:scale-95"
                                title="Deli turnir"
                            >
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span class="hidden sm:inline">Deli</span>
                            </button>

                            <!-- Status badge -->
                            <span
                                v-if="tournamentData.status === 'in_progress'"
                                class="inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400"
                            >
                                <span class="relative flex size-2">
                                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex size-2 rounded-full bg-green-500"></span>
                                </span>
                                V teku
                            </span>
                            <span
                                v-else-if="tournamentData.status === 'completed'"
                                class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                            >
                                <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Zaključen
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Winner Banner -->
            <Transition
                enter-active-class="transition duration-500 ease-out"
                enter-from-class="scale-95 opacity-0 -translate-y-4"
                enter-to-class="scale-100 opacity-100 translate-y-0"
            >
                <div
                    v-if="finalWinner"
                    class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
                >
                    <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 p-8 text-center shadow-2xl">
                        <div class="mb-4 text-6xl animate-bounce">🏆</div>
                        <h2 class="mb-2 text-xl font-bold text-white sm:text-2xl">Zmagovalca turnirja</h2>
                        <p class="text-3xl font-extrabold text-white sm:text-4xl">{{ finalWinner }}</p>
                    </div>
                </div>
            </Transition>

            <!-- Bracket Display -->
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Mobile scroll hint -->
                <div class="mb-4 flex items-center gap-2 text-sm text-gray-600 md:hidden dark:text-gray-400">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    </svg>
                    <span>Potegni levo/desno za ogled</span>
                </div>

                <!-- Bracket container -->
                <div class="overflow-x-auto rounded-2xl bg-white/50 p-4 backdrop-blur-sm sm:p-6 dark:bg-gray-900/50">
                    <div class="flex min-w-max gap-4 sm:gap-8">
                        <TransitionGroup
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="scale-90 opacity-0"
                            enter-to-class="scale-100 opacity-100"
                            name="round"
                        >
                            <div
                                v-for="(round, index) in rounds"
                                :key="round.number"
                                class="flex min-w-[280px] flex-col sm:min-w-[320px]"
                            >
                                <!-- Round title -->
                                <div class="mb-4 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 p-3 text-center shadow-lg">
                                    <h2 class="text-lg font-bold text-white sm:text-xl">
                                        {{ getRoundTitle(round.number, rounds.length) }}
                                    </h2>
                                </div>

                                <!-- Matches -->
                                <div class="flex flex-1 flex-col justify-around gap-4">
                                    <div
                                        v-for="match in round.matches"
                                        :key="match.id"
                                        class="rounded-xl bg-white p-4 shadow-lg transition-all hover:shadow-xl dark:bg-gray-800"
                                    >
                                        <!-- Team 1 -->
                                        <button
                                            @click="selectWinner(match, match.team1)"
                                            :disabled="match.winner !== null || match.team1 === '(prosta ekipa)'"
                                            :class="[
                                                'group mb-3 w-full rounded-lg border-2 p-4 text-left text-sm font-semibold transition-all sm:text-base',
                                                match.winner === match.team1
                                                    ? 'animate-pulse border-green-500 bg-gradient-to-r from-green-50 to-emerald-50 text-green-900 shadow-lg dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-100'
                                                    : match.winner
                                                      ? 'cursor-not-allowed border-gray-200 bg-gray-50 text-gray-400 opacity-60 dark:border-gray-700 dark:bg-gray-900/50'
                                                      : match.team1 === '(prosta ekipa)'
                                                        ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-500 dark:border-gray-700 dark:bg-gray-800'
                                                        : 'border-gray-200 bg-white hover:scale-105 hover:border-blue-400 hover:bg-blue-50 hover:shadow-lg active:scale-95 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-500 dark:hover:bg-blue-900/20',
                                            ]"
                                        >
                                            <div class="flex items-center justify-between">
                                                <span class="flex-1 dark:text-white">{{ match.team1 }}</span>
                                                <svg
                                                    v-if="match.winner === match.team1"
                                                    class="size-5 text-green-600 dark:text-green-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- VS Badge -->
                                        <div class="relative mb-3 flex items-center justify-center">
                                            <div class="absolute inset-0 flex items-center">
                                                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                                            </div>
                                            <div class="relative bg-white px-3 dark:bg-gray-800">
                                                <span class="rounded-full bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-1 text-xs font-bold text-white">VS</span>
                                            </div>
                                        </div>

                                        <!-- Team 2 -->
                                        <button
                                            @click="selectWinner(match, match.team2)"
                                            :disabled="match.winner !== null || match.team2 === '(prosta ekipa)'"
                                            :class="[
                                                'group w-full rounded-lg border-2 p-4 text-left text-sm font-semibold transition-all sm:text-base',
                                                match.winner === match.team2
                                                    ? 'animate-pulse border-green-500 bg-gradient-to-r from-green-50 to-emerald-50 text-green-900 shadow-lg dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-100'
                                                    : match.winner
                                                      ? 'cursor-not-allowed border-gray-200 bg-gray-50 text-gray-400 opacity-60 dark:border-gray-700 dark:bg-gray-900/50'
                                                      : match.team2 === '(prosta ekipa)'
                                                        ? 'cursor-not-allowed border-gray-200 bg-gray-100 text-gray-500 dark:border-gray-700 dark:bg-gray-800'
                                                        : 'border-gray-200 bg-white hover:scale-105 hover:border-blue-400 hover:bg-blue-50 hover:shadow-lg active:scale-95 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-blue-500 dark:hover:bg-blue-900/20',
                                            ]"
                                        >
                                            <div class="flex items-center justify-between">
                                                <span class="flex-1 dark:text-white">{{ match.team2 }}</span>
                                                <svg
                                                    v-if="match.winner === match.team2"
                                                    class="size-5 text-green-600 dark:text-green-400"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </TransitionGroup>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-100">
                    <div class="flex gap-3">
                        <svg class="size-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <strong class="font-bold">Navodila:</strong>
                            <p class="mt-1">Klikni na ekipo, ki je zmagala v tekmi. Spremembe se bodo takoj prikazale vsem gledalcem v realnem času.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showQRModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            @click.self="showQRModal = false"
        >
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="scale-95 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-95 opacity-0"
            >
                <div v-if="showQRModal" class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white">
                        <h2 class="text-2xl font-bold">Deli Turnir</h2>
                        <p class="mt-1 text-sm text-blue-100">Skeniraj QR kodo ali kopiraj povezavo</p>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6">
                        <!-- QR Code -->
                        <div class="mb-6 flex justify-center rounded-xl bg-white p-4 shadow-inner">
                            <img :src="generateQRCode()" alt="QR Code" class="size-64 rounded-lg" />
                        </div>

                        <!-- URL -->
                        <div class="mb-4">
                            <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                Povezava do turnirja
                            </label>
                            <div class="flex gap-2">
                                <input
                                    :value="shareUrl"
                                    readonly
                                    class="flex-1 rounded-lg border-2 border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                />
                                <button
                                    @click="copyLink"
                                    class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white transition hover:bg-blue-700 active:scale-95"
                                    title="Kopiraj povezavo"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="rounded-lg bg-blue-50 p-3 text-xs text-blue-900 dark:bg-blue-900/20 dark:text-blue-100">
                            <p><strong>Nasvet:</strong> Vsak, ki ima povezavo, lahko v realnem času spremlja potek turnirja.</p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t border-gray-200 p-4 dark:border-gray-700">
                        <button
                            @click="showQRModal = false"
                            class="w-full rounded-xl border-2 border-gray-200 px-6 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 active:scale-95 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Zapri
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes blob {
    0%,
    100% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>

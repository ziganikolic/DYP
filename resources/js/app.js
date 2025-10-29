import './bootstrap';
import { createApp } from 'vue';
import TournamentCreate from './components/TournamentCreate.vue';
import TournamentShow from './components/TournamentShow.vue';

const app = createApp({});

app.component('tournament-create', TournamentCreate);
app.component('tournament-show', TournamentShow);

// Mount TournamentCreate if element exists
const createEl = document.getElementById('tournament-create');
if (createEl) {
    const createApp = createApp(TournamentCreate);
    createApp.mount('#tournament-create');
}

// Mount TournamentShow if element exists
const showEl = document.getElementById('tournament-show');
if (showEl) {
    const code = showEl.dataset.code;
    const tournament = JSON.parse(showEl.dataset.tournament || '{}');

    const showApp = createApp(TournamentShow, {
        code,
        initialTournament: tournament
    });
    showApp.mount('#tournament-show');
}

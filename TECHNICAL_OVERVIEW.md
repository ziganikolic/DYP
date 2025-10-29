# 🏗️ Technical Overview - Tournament Bracket Application

## Arhitektura

### Backend (Laravel 11)

#### Models
- **Tournament** (`app/Models/Tournament.php`)
  - Glavna entiteta turnirja
  - Unique `code` za deljenje (8 znakov)
  - Status: `setup`, `in_progress`, `completed`
  - Avtomatska generacija kode pri ustvarjanju

- **Team** (`app/Models/Team.php`)
  - Dvojica igralcev
  - `player1_name`, `player2_name`, `name` (kombinirano ime)
  - Pripada tournamentu

- **Match** (`app/Models/Match.php`)
  - Tekma med dvema ekipama
  - `round_number` - kateri krog (1, 2, 3...)
  - `position` - pozicija v krogu
  - `winner_team_id` - zmagovalec (null = še ni izbran)

- **Player** (`app/Models/Player.php`)
  - Posamezni igralec (trenutno ne aktivno uporabljen, rezervirano za bodoče funkcionalnosti)

#### Controllers
- **TournamentController** (`app/Http/Controllers/TournamentController.php`)
  - `index()` - Prikaz strani za kreiranje turnirja
  - `create()` - Kreiranje novega turnirja (API)
    - Validacija igralcev (min 4)
    - Shuffle igralcev
    - Kreiranje ekip po 2
    - Generacija prvega kroga
  - `show($code)` - Prikaz turnirja po kodi
  - `data($code)` - API za podatke turnirja
  - `selectWinner($code, $matchId)` - Izbira zmagovalca tekme
    - Preveri če match že ima zmagovalca
    - Posodobi match
    - Preveri če je krog končan
    - Ustvari naslednji krog ali zaključi turnir

#### Events (Real-time)
- **TournamentUpdated** (`app/Events/TournamentUpdated.php`)
  - Broadcasta celoten turnir
  - Se sproži ob:
    - Kreaciji turnirja
    - Koncu kroga
    - Zaključku turnirja

- **MatchWinnerSelected** (`app/Events/MatchWinnerSelected.php`)
  - Broadcasta posamezen match
  - Se sproži takoj po izbiri zmagovalca

#### Routes
**Web Routes** (`routes/web.php`):
- `GET /` - Domača stran (kreiranje turnirja)
- `GET /tournament/{code}` - Prikaz turnirja

**API Routes** (`routes/api.php`):
- `POST /api/tournaments` - Kreiranje turnirja
- `GET /api/tournaments/{code}` - Podatki turnirja
- `POST /api/tournaments/{code}/matches/{matchId}/winner` - Izbira zmagovalca

**Channels** (`routes/channels.php`):
- `tournament.{code}` - Public channel za vsak turnir

### Frontend (Vue.js 3)

#### Components

**TournamentCreate.vue** (`resources/js/components/TournamentCreate.vue`)
- Input za imena igralcev (textarea)
- Validacija min 4 igralcev
- API call za kreiranje turnirja
- Prikaz shareable URL-ja
- Copy-to-clipboard funkcionalnost

**TournamentShow.vue** (`resources/js/components/TournamentShow.vue`)
- Prikaz bracket tree
- Computed rounds iz matches
- Real-time posodabljanje preko Laravel Echo
- Click-to-select winner funkcionalnost
- Dinamično imenovanje krogov (Četrtfinale, Polfinale, Finale)
- Prikaz končnega zmagovalca

#### Real-time Integration
**Laravel Echo** (`resources/js/bootstrap.js`):
```javascript
window.Echo.channel(`tournament.${code}`)
  .listen('TournamentUpdated', (e) => {
    tournament.value = e.tournament;
  })
  .listen('MatchWinnerSelected', (e) => {
    // Update specific match
  });
```

### Database Schema

```sql
-- tournaments
id, code (unique), status (enum), created_at, updated_at

-- teams
id, tournament_id, player1_name, player2_name, name, created_at, updated_at

-- matches
id, tournament_id, round_number, team1_id, team2_id,
winner_team_id, position, created_at, updated_at

-- players (reserved for future)
id, tournament_id, name, order, created_at, updated_at
```

## Logika turnirja

### 1. Kreiranje turnirja
```
Input: ["Janez", "Ana", "Peter", "Maja", "Luka", "Eva"]
↓ Shuffle
["Eva", "Peter", "Janez", "Luka", "Ana", "Maja"]
↓ Pair (teams of 2)
[
  "Eva & Peter",
  "Janez & Luka",
  "Ana & Maja"
]
↓ Create matches (Round 1)
Match 1: "Eva & Peter" vs "Janez & Luka"
Match 2: "Ana & Maja" vs (bye - if odd)
```

### 2. Napredovanje
```
Round 1: 4 teams → 2 matches
  Winner 1, Winner 2
↓
Round 2 (Finale): 2 teams → 1 match
  Winner (Champion)
```

### 3. Poimenovanje krogov
```javascript
if (teamsInRound === 2) → "Finale"
else if (teamsInRound === 4) → "Polfinale"
else if (teamsInRound === 8) → "Četrtfinale"
else → "Krog N"
```

## Real-time Flow

### Izbira zmagovalca
```
User clicks team in browser
↓
POST /api/tournaments/{code}/matches/{matchId}/winner
↓
Controller updates match
↓
Broadcast MatchWinnerSelected event
↓
Laravel Reverb broadcasts to channel
↓
All connected clients receive update via WebSocket
↓
Vue component updates UI
```

### Zaključek kroga
```
Last match of round completes
↓
Controller checks all matches in round
↓
If all complete:
  - Collect winners
  - If 1 winner: Tournament complete
  - If >1 winners: Create next round
↓
Broadcast TournamentUpdated event
↓
All clients refresh bracket
```

## Styling

- **Tailwind CSS** za utility-first styling
- **Responsive design** - deluje na mobilnih napravah
- **Color coding**:
  - White/Blue hover - Neizbrano
  - Green - Zmagovalec
  - Gray - Poraženec
  - Blue badge - Tournament code

## Build Process

### Development
```bash
npm run dev      # Vite dev server s HMR
php artisan serve
php artisan reverb:start
```

### Production
```bash
npm run build    # Builds assets v public/build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Performance Considerations

### Database
- **SQLite** za development in manjše deploymente
- Indexes na `tournament_id`, `round_number`
- Foreign key constraints za data integrity
- Lahko se migrira na MySQL/PostgreSQL za večje scale

### Broadcasting
- **Laravel Reverb** - moderne WebSocket implementacija
- Public channels (brez auth overhead)
- Efficient selective broadcasting (samo relevantni podatki)

### Frontend
- **Vite** za hitro bundling
- **Vue 3 Composition API** za bolj reactive komponente
- Computed properties za efficient re-rendering
- Minimal re-renders (samo spremenjena match data)

## Security

- **CSRF Protection** - Laravel tokens
- **Input Validation** - Server-side validation
- **SQL Injection Protection** - Eloquent ORM
- **XSS Protection** - Vue.js automatic escaping
- **Public tournaments** - No auth required (feature, not bug)

## Scalability

### Horizontal Scaling
- Stateless aplikacija
- Database-backed sessions
- Reverb lahko uporablja Redis za scaling

### Capacity
- Single instance: ~100 concurrent users
- With Redis + Load balancer: ~1000+ users
- Database bottleneck pri ~10000 tournaments

## Future Enhancements

Možne izboljšave:
1. **User Authentication** - Lastništvo turnirjev
2. **Tournament History** - Archive starih turnirjev
3. **Custom Rules** - Different bracket types (Swiss, Double elimination)
4. **Statistics** - Win rates, player stats
5. **Match Scheduling** - Time slots za tekme
6. **Score Tracking** - Ne samo zmagovalec, tudi rezultat
7. **Notifications** - Email/Push notifications
8. **Mobile App** - Native iOS/Android app
9. **Video Integration** - Stream link za vsak match
10. **Tournament Templates** - Predefined setups

## Testing

Framework vključuje:
- PHPUnit za unit in feature teste
- Vue Test Utils za component testing (needs setup)
- Browser testing z Laravel Dusk (needs setup)

Primer testa:
```php
public function test_tournament_creation()
{
    $response = $this->post('/api/tournaments', [
        'players' => ['A', 'B', 'C', 'D']
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['code']);
}
```

## Monitoring

Za produkcijo priporočamo:
- **Laravel Telescope** - Debugging assistant
- **Laravel Horizon** - Queue monitoring
- **Sentry** - Error tracking
- **New Relic** / **DataDog** - APM

## API Documentation

API je RESTful z JSON responses:

**Create Tournament:**
```http
POST /api/tournaments
Content-Type: application/json

{
  "players": ["Player1", "Player2", "Player3", "Player4"]
}

Response: { "success": true, "code": "ABC12345" }
```

**Get Tournament:**
```http
GET /api/tournaments/ABC12345

Response: {
  "tournament": {
    "id": 1,
    "code": "ABC12345",
    "status": "in_progress",
    "teams": [...],
    "matches": [...]
  }
}
```

**Select Winner:**
```http
POST /api/tournaments/ABC12345/matches/1/winner
Content-Type: application/json

{
  "winner_team_id": 5
}

Response: { "success": true }
```

---

## Summary

Aplikacija je zasnovana za:
- **Enostavnost uporabe** - Minimal clicks do igre
- **Real-time experience** - Instant updates
- **Reliability** - Robust error handling
- **Scalability** - Easy to expand
- **Modern stack** - Laravel 11 + Vue 3 + Reverb

Perfect za office tournaments, gaming events, sports competitions!

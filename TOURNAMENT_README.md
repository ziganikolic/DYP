# Turnir Dvojic - Tournament Pairs Elimination

Laravel application for running real-time tournament brackets with pairs elimination system.

## Features

- **Create tournaments** with player input
- **Random draw** - players are automatically shuffled and paired into teams
- **Knockout bracket system** - Quarterfinals, Semifinals, Finals
- **Real-time updates** - All viewers see changes instantly via Laravel Reverb
- **No authentication required** - Publicly accessible for all participants
- **Responsive design** - Works on desktop and mobile devices
- **Dark mode support**

## How to Run Locally

1. **Start the development servers:**
   ```bash
   # Terminal 1 - Start Laravel Reverb WebSocket server
   php artisan reverb:start

   # Terminal 2 - Start the Laravel development server
   php artisan serve

   # Terminal 3 - Watch frontend assets (optional, only if making changes)
   npm run dev
   ```

2. **Access the application:**
   - Open your browser to: `http://localhost:8000`

## How to Use

### Creating a Tournament

1. Click "Ustvari Nov Turnir" (Create New Tournament)
2. Enter tournament name
3. Add player names (one per line, minimum 4 players)
4. Click "Začni Žreb" (Start Draw)

### Playing the Tournament

1. Click on a team name to select them as the winner
2. Winners automatically advance to the next round
3. All connected users see updates in real-time
4. Tournament completes when there's a final winner

### Viewing Tournaments

- Homepage shows all tournaments (current and completed)
- Click any tournament to view its bracket
- Share the URL with others to view together in real-time

## Real-time Synchronization

The app uses Laravel Reverb for WebSocket broadcasting:
- When a winner is selected, all viewers see it immediately
- New rounds are created automatically
- No page refresh needed
- Perfect for group viewing during tournaments

## Database

The app uses SQLite by default. The database is located at:
```
database/database.sqlite
```

To reset all tournaments:
```bash
php artisan migrate:fresh
```

## Deploying to Laravel Cloud

### Prerequisites
- Laravel Cloud account
- GitHub repository (optional but recommended)

### Deployment Steps

1. **Prepare environment variables:**
   The `.env` file already has Reverb configured. On Laravel Cloud, ensure these are set:
   ```env
   BROADCAST_CONNECTION=reverb
   QUEUE_CONNECTION=database
   ```

2. **Database:**
   Laravel Cloud supports SQLite by default, so no changes needed.

3. **Build command:**
   ```bash
   npm run build
   php artisan migrate --force
   ```

4. **Start command:**
   Laravel Cloud will automatically run:
   - `php artisan serve` (web server)
   - `php artisan reverb:start` (WebSocket server)
   - `php artisan queue:work` (queue worker)

5. **Environment Configuration:**
   On Laravel Cloud dashboard, configure:
   - `APP_URL` - Your Laravel Cloud URL
   - `REVERB_HOST` - Your Laravel Cloud domain (without http/https)
   - `REVERB_SCHEME` - Should be `https` on production
   - `REVERB_PORT` - Usually `443` for https

### Testing Real-time After Deploy

1. Open the tournament URL in multiple browser windows/devices
2. Select a winner in one window
3. Verify all other windows update immediately

## Project Structure

```
app/
├── Events/
│   └── TournamentUpdated.php       # Broadcast event
├── Http/Controllers/
│   └── TournamentController.php     # Main controller
└── Models/
    ├── Tournament.php               # Tournament model
    └── TournamentMatch.php          # Match model

database/migrations/
├── *_create_tournaments_table.php
└── *_create_tournament_matches_table.php

resources/js/
├── pages/Tournament/
│   ├── Index.vue                    # Tournament list page
│   └── Show.vue                     # Bracket view with real-time
├── echo.ts                          # WebSocket configuration
└── app.ts                           # Main JS entry

routes/
└── web.php                          # Application routes
```

## Troubleshooting

### WebSocket not connecting
- Ensure Reverb server is running: `php artisan reverb:start`
- Check browser console for connection errors
- Verify `VITE_REVERB_*` variables in `.env`

### Changes not reflecting
- Clear browser cache
- Rebuild frontend: `npm run build`
- Restart Reverb server

### Database errors
- Run migrations: `php artisan migrate`
- Check SQLite file permissions

## Technical Stack

- **Backend:** Laravel 12, PHP 8.4
- **Frontend:** Vue 3, Inertia.js 2, Tailwind CSS 4
- **Real-time:** Laravel Reverb, Laravel Echo, Pusher JS
- **Database:** SQLite
- **Build:** Vite

---

Built with Laravel and Vue.js for real-time tournament management.

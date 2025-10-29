# 🏆 Tournament Bracket Application

> **Full-featured Laravel + Vue.js tournament management system with real-time updates**

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js)
![Tailwind](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Pregled

Aplikacija za organizacijo turnirjev dvojic s sistemom izpadanja (elimination bracket). Podpira real-time posodabljanje rezultatov, večuporabniški dostop in deljenje turnirjev preko URL-jev.

**Idealno za:**
- Office tekmovanja
- Gaming tournaments
- Sports events
- Zabavne igre s prijatelji

---

## ✨ Funkcionalnosti

### Core Features
- ✅ **Kreiranje turnirjev** - Vnos igralcev in avtomatski žreb
- ✅ **Bracket sistem** - Elimination bracket z vizualizacijo
- ✅ **Real-time updates** - Instant sinhronizacija preko WebSockets
- ✅ **Večuporabniška podpora** - Več ljudi lahko spremlja isti turnir
- ✅ **Shareable links** - Deli turnir preko URL-ja
- ✅ **Responsive design** - Deluje na vseh napravah

### Technical Features
- 🚀 **Laravel 11** backend z Eloquent ORM
- 🎨 **Vue.js 3** frontend z Composition API
- 🔄 **Laravel Reverb** za WebSocket komunikacijo
- 💅 **Tailwind CSS** za styling
- ⚡ **Vite** za hitro bundling
- 💾 **SQLite** baza (lahko MySQL/PostgreSQL)

---

## 🚀 Quick Start

### Predpogoji
- PHP 8.2+
- Composer
- Node.js 18+ in NPM

### Namestitev (5 min)

```bash
# 1. Kloniraj repozitorij
git clone <repo-url>
cd DYP

# 2. Namesti odvisnosti
composer install
npm install

# 3. Nastavi okolje
cp .env.example .env
php artisan key:generate

# 4. Ustvari bazo
touch database/database.sqlite
php artisan migrate

# 5. Zgradi frontend
npm run build
```

### Zagon (2 terminala)

**Terminal 1 - Laravel:**
```bash
php artisan serve
# ➜ http://localhost:8000
```

**Terminal 2 - Reverb:**
```bash
php artisan reverb:start
# ➜ WebSocket na portu 8080
```

### Uporaba

1. Odpri http://localhost:8000
2. Vnesi imena igralcev (min 4)
3. Klikni "Začni žreb"
4. Deli link s kolegi
5. Izberi zmagovalce s klikom

---

## 📖 Dokumentacija

- **[QUICKSTART.md](QUICKSTART.md)** - Hitri start za jutri
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Laravel Cloud deployment
- **[TECHNICAL_OVERVIEW.md](TECHNICAL_OVERVIEW.md)** - Tehnična dokumentacija

---

## 🏗️ Arhitektura

```
┌─────────────────────────────────────────┐
│           Frontend (Vue.js 3)           │
│  ┌──────────────┐   ┌─────────────────┐│
│  │ Tournament   │   │ Tournament      ││
│  │ Create       │   │ Show (Bracket)  ││
│  └──────────────┘   └─────────────────┘│
└───────────┬──────────────┬──────────────┘
            │              │
            │ HTTP API     │ WebSocket
            ▼              ▼
┌─────────────────┐  ┌──────────────────┐
│  Laravel API    │  │ Laravel Reverb   │
│  (Controllers)  │  │  (WebSockets)    │
└────────┬────────┘  └──────────────────┘
         │
         ▼
┌─────────────────────────────────────────┐
│     Database (SQLite/MySQL)             │
│  tournaments | teams | matches          │
└─────────────────────────────────────────┘
```

---

## 🎮 Kako deluje

### 1. Kreiranje turnirja
```
Igralci → Shuffle → Pairs (Teams) → Round 1 Matches
["A","B","C","D"] → ["B","A","D","C"] → ["B&A","D&C"] → Match(B&A vs D&C)
```

### 2. Bracket napredovanje
```
Round 1: 4 teams → 2 matches → 2 winners
Round 2: 2 teams → 1 match   → 1 winner (Champion!)
```

### 3. Real-time sync
```
User clicks winner → API call → Database update
                             ↓
                    Broadcast event (Reverb)
                             ↓
                    All clients receive update
                             ↓
                    Vue component updates UI
```

---

## 🎯 Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Frontend | Vue.js 3 (Composition API) |
| Real-time | Laravel Reverb |
| Database | SQLite (dev) / MySQL (prod) |
| Styling | Tailwind CSS |
| Build Tool | Vite |
| Package Manager | Composer, NPM |

---

## 📁 Struktura projekta

```
DYP/
├── app/
│   ├── Events/
│   │   ├── TournamentUpdated.php
│   │   └── MatchWinnerSelected.php
│   ├── Http/Controllers/
│   │   └── TournamentController.php
│   └── Models/
│       ├── Tournament.php
│       ├── Team.php
│       ├── Match.php
│       └── Player.php
├── database/
│   ├── migrations/
│   └── database.sqlite
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   ├── TournamentCreate.vue
│   │   │   └── TournamentShow.vue
│   │   ├── app.js
│   │   └── bootstrap.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       ├── app.blade.php
│       └── tournament/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── channels.php
└── config/
    ├── broadcasting.php
    └── reverb.php
```

---

## 🛠️ Development

### Development mode (s hot reload)
```bash
# Terminal 1
php artisan serve

# Terminal 2
php artisan reverb:start

# Terminal 3
npm run dev  # Vite HMR
```

### Code style
```bash
# Laravel Pint (PHP)
./vendor/bin/pint

# ESLint (JavaScript) - če želiš dodati
npm run lint
```

### Testing
```bash
# PHP tests
php artisan test

# Vue component tests (needs setup)
npm run test
```

---

## 🚢 Deployment

### Laravel Cloud (priporočeno)

1. **Poveži repozitorij**
   ```bash
   git push origin claude/laravel-tournament-bracket-app-011CUbZoHGdxfmUcofaPutEs
   ```

2. **Ustvari projekt** na [Laravel Cloud](https://cloud.laravel.com)

3. **Nastavi spremenljivke**
   - APP_KEY, APP_URL
   - DB_CONNECTION (MySQL ali SQLite)
   - REVERB_* spremenljivke

4. **Deploy** - Avtomatski build in deployment

Glej [DEPLOYMENT.md](DEPLOYMENT.md) za podrobna navodila.

---

## 🔧 Configuration

### Environment spremenljivke

```env
# App
APP_NAME="Tournament Bracket"
APP_ENV=production
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=sqlite  # ali mysql

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=your-host
REVERB_PORT=443
REVERB_SCHEME=https
```

---

## 🤝 Contributing

Contributions so dobrodošle!

1. Fork repo
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📝 License

MIT License - see [LICENSE](LICENSE) file for details

---

## 🙏 Credits

Razvito z:
- [Laravel](https://laravel.com)
- [Vue.js](https://vuejs.org)
- [Laravel Reverb](https://reverb.laravel.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Vite](https://vitejs.dev)

---

## 📞 Support

- 📖 [Dokumentacija](TECHNICAL_OVERVIEW.md)
- 🚀 [Quick Start](QUICKSTART.md)
- 🚢 [Deployment Guide](DEPLOYMENT.md)
- 🐛 [Report Issue](https://github.com/your-repo/issues)

---

## 🎉 Ready to play!

```bash
php artisan serve & php artisan reverb:start
```

Visit http://localhost:8000 and start your tournament! 🏆

---

**Made with ❤️ using Laravel and Vue.js**

🤖 *Developed with [Claude Code](https://claude.com/claude-code)*

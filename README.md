# 🏆 Turnir Dvojic - Tournament Bracket System

Sistem za ustvarjanje in sledenje turnirjev v realnem času z Laravel Reverb WebSockets.

## ✨ Funkcionalnosti

- **Dinamičen vnos igralcev** - intuitivni input z možnostjo dodajanja/odstranjevanja
- **Real-time updates** - vse spremembe se takoj prikažejo vsem gledalcem
- **Share funkcionalnost** - deljenje s QR kodo ali native share
- **Mobile-optimized** - horizontal scroll bracket, responsive design
- **Modern UI** - gradienti, animacije, dark mode
- **WebSocket podpora** - Laravel Reverb za instant updates

## 🛠️ Tehnologije

- **Backend**: Laravel 12, PHP 8.4, PostgreSQL
- **Frontend**: Vue 3, Inertia.js v2, Tailwind CSS 4
- **Real-time**: Laravel Reverb (WebSockets)
- **UI Components**: Radix Vue, shadcn-vue

## 🚀 Deployment na Laravel Cloud

### Avtomatski deployment

Laravel Cloud avtomatsko zazna `.laravel/deploy` script in ga izvede ob vsakem deploymentu.

Deploy script:
1. Instalira npm dependencies (`npm ci`)
2. Builda frontend assets (`npm run build`)
3. Požene migrations (`php artisan migrate --force`)
4. Optimizira aplikacijo (config, route, view cache)

### Environment Variables

Nastavi naslednje environment variable na Laravel Cloud:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=reverb.your-domain.com
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Reverb Setup

Laravel Reverb mora biti nameščen in running na produkciji:

```bash
php artisan reverb:start
```

Na Laravel Cloud se to nastavi v "Workers" sekciji.

## 🏁 Local Development

### Predpogoji

- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL ali MySQL

### Instalacija

```bash
# Clone repository
git clone https://github.com/ziganikolic/DYP.git
cd DYP

# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Setup database (če uporabljaš Laravel Herd, PostgreSQL je že nastavljeno)
# Ustvari bazo "kitio-game" ali spremeni DB_DATABASE v .env

# Run migrations
php artisan migrate

# Build frontend
npm run build
```

### Zagon

#### Enostavni način (brez Reverb):
```bash
# Start dev server
php artisan serve

# Start Vite dev server (v drugi terminal tab)
npm run dev
```

#### Z Reverb (za real-time):
```bash
# Start Reverb server
php artisan reverb:start

# Start aplikacija z queue worker in vite (v drugi terminal tab)
composer run dev
```

Odpri http://localhost:8000

## 📝 Uporaba

1. **Ustvari turnir** - klikni "Ustvari Nov Turnir"
2. **Vnesi igralce** - dodaj vsaj 4 igralce z individualnimi input polji
3. **Quick actions** - uporabi "+ 4 igralce" ali "+ 8 igralcev" za hitro dodajanje
4. **Žrebanje** - klikni "Začni Žreb" in sistem avtomatsko ustvari pare
5. **Deli turnir** - uporabi "Deli" gumb za QR kodo ali share
6. **Izberi zmagovalce** - klikni na ekipo ki je zmagala
7. **Real-time** - vse spremembe se avtomatsko prikažejo vsem gledalcem

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 📄 License

MIT License

## 👨‍💻 Author

Created with [Claude Code](https://claude.com/claude-code)

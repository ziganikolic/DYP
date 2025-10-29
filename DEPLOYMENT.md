# 🚀 Deployment Guide - Laravel Cloud

> **⚠️ Important:** Before deploying, you need to generate proper `composer.lock` and `package-lock.json` files.
>
> **Quick Start:** Run `./prepare-deployment.sh` (requires PHP 8.2+ and Composer)
>
> **Having Issues?** See [DEPLOYMENT_TROUBLESHOOTING.md](DEPLOYMENT_TROUBLESHOOTING.md)

---

## Priprava aplikacije

### 1. Lokalni razvoj

```bash
# Instaliraj odvisnosti
composer install
npm install

# Kopiraj .env datoteko
cp .env.example .env

# Generiraj aplikacijski ključ
php artisan key:generate

# Ustvari bazo
touch database/database.sqlite

# Poženi migracije
php artisan migrate

# Zgradi frontend assets
npm run build

# Zaženi aplikacijo
php artisan serve

# V drugem terminalu zaženi Reverb
php artisan reverb:start
```

### 2. Priprava za produkcijo

```bash
# Optimiziraj konfiguracije
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Zgradi produkcijske assets
npm run build

# Preveri da vse deluje
php artisan about
```

## Deployment na Laravel Cloud

### Korak 1: Pripravi Git repozitorij

```bash
git add .
git commit -m "Initial commit - Tournament Bracket App"
git push origin claude/laravel-tournament-bracket-app-011CUbZoHGdxfmUcofaPutEs
```

### Korak 2: Laravel Cloud Setup

1. Pojdi na [Laravel Cloud](https://cloud.laravel.com)
2. Ustvari nov projekt
3. Poveži svoj GitHub repozitorij
4. Izberi branch: `claude/laravel-tournament-bracket-app-011CUbZoHGdxfmUcofaPutEs`

### Korak 3: Konfiguracija okolja

V Laravel Cloud dashboard nastavi naslednje environment spremenljivke:

```env
APP_NAME="Tournament Bracket"
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generiraj nov ključ>
APP_URL=https://your-domain.com

DB_CONNECTION=sqlite

BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=<nastavi v dashboard>
REVERB_APP_KEY=<nastavi v dashboard>
REVERB_APP_SECRET=<nastavi v dashboard>
REVERB_HOST=<nastavi v dashboard>
REVERB_PORT=443
REVERB_SCHEME=https
```

### Korak 4: Build in Deploy

Laravel Cloud bo avtomatsko:
- Namestil Composer odvisnosti
- Namestil NPM odvisnosti
- Zgradil assets z `npm run build`
- Pognal migracije
- Optimiziral aplikacijo

### Korak 5: Reverb Configuration

V Laravel Cloud dashboard:
1. Pojdi na "Services"
2. Dodaj "Reverb" service
3. Nastavi port in hostname
4. Posodobi environment spremenljivke

## Post-Deployment

### Preverjanje

```bash
# Preveri zdravje aplikacije
curl https://your-domain.com/up

# Preveri če Reverb deluje
# Odpri aplikacijo in ustvari turnir
# Preveri da se spremembe prikazujejo real-time
```

### Monitoring

Laravel Cloud zagotavlja:
- Logs (Aplikacijski in Reverb)
- Metrics (CPU, Memory, Requests)
- Alerts

## Troubleshooting

### Reverb ne deluje

1. Preveri da je Reverb service zagnan
2. Preveri environment spremenljivke (VITE_* spremenljivke)
3. Preveri browser console za WebSocket napake
4. Preveri Reverb logs v dashboard

### Database napake

1. Preveri da so migracije poganjene
2. Za SQLite preveri file permissions
3. Za produkcijo razmisli o uporabi MySQL

### Asset napake

1. Preveri da je `npm run build` uspešen
2. Preveri da Vite manifest obstaja
3. Preveri public/build direktorij

## Scaling

Za večje število uporabnikov:
- Uporabi MySQL namesto SQLite
- Dodaj Redis za cache in sessions
- Uporabi Queue workers za background jobs
- Razširi Reverb instances

## Backup

Priporočljivo je redno backup:
- SQLite database datoteke
- Environment spremenljivk
- Uploaded files (če obstajajo)

## Security

1. Uporabi HTTPS (avtomatsko z Laravel Cloud)
2. Nastavi močan APP_KEY
3. Uporabi močne Reverb credentials
4. Onemogoči APP_DEBUG v produkciji
5. Redno posodabljaj odvisnosti

## Performance

1. Uporabi cache za konfiguracijo
2. Optimiziraj database queries
3. Uporabi CDN za statične assets (opcijsko)
4. Monitor memory usage

---

Za več informacij: https://cloud.laravel.com/docs

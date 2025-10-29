# ⚡ Quick Start Guide

## Za jutri - Namestitev in uporaba

### Predpogoji

Potrebuješ:
- PHP 8.2+
- Composer
- Node.js 18+ in NPM
- Git

### 1. Kloniraj in namesti (5 min)

```bash
# Kloniraj repozitorij
git clone <repo-url>
cd DYP

# Namesti odvisnosti
composer install
npm install

# Kopiraj in nastavi .env
cp .env.example .env
php artisan key:generate

# Ustvari bazo
touch database/database.sqlite
php artisan migrate

# Zgradi frontend
npm run build
```

### 2. Poženi aplikacijo (2 min)

Potrebuješ **DVA terminala**:

**Terminal 1 - Laravel aplikacija:**
```bash
php artisan serve
# Aplikacija bo dostopna na http://localhost:8000
```

**Terminal 2 - Reverb (WebSocket):**
```bash
php artisan reverb:start
# Reverb bo poslušal na portu 8080
```

### 3. Uporaba aplikacije

1. **Ustvari turnir:**
   - Odpri http://localhost:8000
   - Vnesi imena igralcev (vsaj 4) - vsak v svoji vrstici
   - Klikni "Začni žreb"
   - Kopiraj link turnirja

2. **Deli s kolegi:**
   - Pošlji link kolegom
   - Vsi lahko spremljajo isti turnir v realnem času

3. **Igraj:**
   - Klikaj na zmagovalce posameznih tekem
   - Spremembe se takoj prikazujejo vsem udeležencem
   - Nadaljuj dokler ni določen zmagovalec

## Primeri igralcev za testiranje

```
Janez Novak
Ana Kovač
Peter Horvat
Maja Zajc
Luka Mlakar
Eva Simončič
Marko Kralj
Nina Petrič
```

## Troubleshooting

### Reverb connection failed

1. Preveri da Reverb teče na portu 8080:
   ```bash
   php artisan reverb:start
   ```

2. Preveri da so VITE spremenljivke pravilne v `.env`:
   ```
   VITE_REVERB_APP_KEY=local-app-key
   VITE_REVERB_HOST=localhost
   VITE_REVERB_PORT=8080
   VITE_REVERB_SCHEME=http
   ```

3. Preveri browser console za napake

### Assets ne naložijo

```bash
# Ponovno zgradi assets
npm run build

# Ali za development z hot reload
npm run dev
```

### Database napake

```bash
# Ponovno ustvari bazo
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh
```

## Development mode (z hot reload)

Za razvoj uporabi:

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
php artisan reverb:start
```

**Terminal 3:**
```bash
npm run dev
# Vite bo imel hot reload na http://localhost:5173
```

## Deployment na Laravel Cloud

Glej [DEPLOYMENT.md](DEPLOYMENT.md) za podrobne navodila.

Hitri koraki:
1. Push na GitHub
2. Poveži z Laravel Cloud
3. Nastavi environment spremenljivke
4. Deploy!

## Funkcionalnosti

✅ Real-time posodabljanje
✅ Večuporabniška podpora
✅ Deljenje preko URL-ja
✅ Avtomatski žreb
✅ Bracket vizualizacija
✅ Responsive design

## Support

Za vprašanja in probleme odpri issue na GitHub-u ali poglej README.md za več informacij.

---

**Pomembno za jutri:**
- Prepričaj se da oba terminala (Laravel in Reverb) tečeta
- Testiraj s kolegi pred začetkom turnirja
- Imej backup plan (original HTML demo) če pride do težav

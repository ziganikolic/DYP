# 🏆 Turnir Dvojic - Navodila za Uporabo

Laravel aplikacija za igranje turnirjev v parih z izpadanjem in **real-time posodobitvami**.

## ✨ Kaj aplikacija omogoča

- **Ustvari turnir** - vnesi igralce, avtomatsko žrebanje v pare
- **Igraj turnir** - klikni na zmagovalce, vsi vidijo v realnem času
- **Brez prijave** - vsi lahko dostopajo in spremljajo
- **Avtomatski bracket** - četrtfinale, polfinale, finale
- **Real-time** - vse spremembe se prikažejo vsem takoj
- **Темни način** - podpira light/dark mode

---

## 🚀 Kako zagnati (Laravel Herd)

Ker uporabljaš **Laravel Herd**, je aplikacija že na voljo na:

### 🌐 **https://kitio-game.test**

Samo odprи browser in greš!

### Če Herd NE deluje avtomatsko:

Laravel Herd bi moral avtomatsko zagnati:
- ✅ Web server (Nginx)
- ✅ PostgreSQL baza
- ❌ **Reverb WebSocket server** - TO MORAŠ ROČNO ZAGNATI

**Zaženi Reverb WebSocket server:**

```bash
# V terminalu navigiraj v projekt folder in poženi:
php artisan reverb:start
```

Pustи ta terminal odprt medtem ko uporabljaš aplikacijo!

---

## 📖 Kako uporabljati aplikacijo

### 1️⃣ Ustvari Nov Turnir

1. Obišči **https://kitio-game.test**
2. Klikni **"Ustvari Nov Turnir"**
3. Vnesi ime turnirja (npr. "Turnir 29.10.2025")
4. Vnesi imena igralcev - **en igralec na vrstico**, minimum 4:
   ```
   Žiga
   Marko
   Ana
   Peter
   Maja
   Luka
   Nina
   Jan
   ```
5. Klikni **"Začni Žreb"**

Aplikacija bo:
- Naključno premešala igralce
- Ustvarila pare (Žiga & Marko, Ana & Peter, itd.)
- Zgenerirala bracket (četrtfinale, polfinale, finale)

### 2️⃣ Igraj Turnir

1. Klikni na ime ekipe ki je **zmagala** v tekmi
2. Zmagovalec postane **zelen** in napreduje
3. Ko so vse tekme v krogu končane, se avtomatsko ustvari **naslednji krog**
4. Nadaljuj dokler ni **samo še en zmagovalec**

### 3️⃣ Deli z Kolegi (Real-time!)

1. Kopiraj URL turnirja (npr. `https://kitio-game.test/tournaments/1`)
2. Pošlji kolegom
3. **VSI vidijo spremembe v realnem času** - brez refresha!

Ko kdo klikne zmagovalca, se to **takoj prikaže vsem ostalim**.

---

## 🔥 Real-time funkcionalnost

### Kako deluje:

- **Laravel Reverb** - WebSocket server za live povezave
- **Laravel Echo** - JavaScript knjižnica za povezavo
- Ko nekdo izbere zmagovalca → vsi vidijo v < 1 sekundi

### Kaj vidiš v real-time:

✅ Izbor zmagovalcev
✅ Nov krog se ustvari
✅ Finalni zmagovalec
✅ Vse spremembe na bracketu

### Če real-time ne deluje:

1. Preveri ali Reverb teče:
   ```bash
   php artisan reverb:start
   ```

2. Preveri v browser konzoli (F12) ali so napake

3. Preveri `.env` file - mora biti:
   ```env
   REVERB_HOST="reverb.herd.test"
   REVERB_PORT=443
   REVERB_SCHEME=https
   ```

---

## 🗄️ Baza podatkov (PostgreSQL)

Aplikacija uporablja **PostgreSQL** bazo z imenom **kitio-game**.

### Če želiš resetirati vse turnirje:

```bash
php artisan migrate:fresh
```

⚠️ **POZOR:** To bo izbrisalo VSE turnirje!

---

## 🎮 Prikaz uporabe

### Začetna stran:
```
┌────────────────────────────────────┐
│  🏆 Turnir Dvojic - Izpadanje     │
│                                    │
│  [Ustvari Nov Turnir]             │
│                                    │
│  📋 Obstoječi turnirji:           │
│  ┌─────────────────────┐          │
│  │ Turnir 29.10.2025   │ V teku  │
│  │ 8 ekip              │          │
│  └─────────────────────┘          │
└────────────────────────────────────┘
```

### Bracket prikaz:
```
Četrtfinale     Polfinale       Finale
┌──────────┐
│ A & B  ✓ │──┐
└──────────┘  │  ┌──────────┐
              ├─→│ A & B    │──┐
┌──────────┐  │  └──────────┘  │
│ C & D    │──┘                 │    ┌──────────┐
└──────────┘                    ├───→│ A & B  🏆│
                                │    └──────────┘
┌──────────┐                    │
│ E & F  ✓ │──┐                 │
└──────────┘  │  ┌──────────┐  │
              ├─→│ E & F    │──┘
┌──────────┐  │  └──────────┘
│ G & H    │──┘
└──────────┘
```

---

## 🛠️ Razvojni način (development)

Če želiš spreminjati kodo:

```bash
# Terminal 1 - Reverb WebSocket
php artisan reverb:start

# Terminal 2 - Vite dev server (hot reload)
npm run dev
```

S tem dobiš **hot reload** - spremembe se takoj prikažejo.

---

## 🌐 Deljenje z ostalimi v omrežju

### Za testiranje z več napravami:

1. **Najdi svoj IP naslov:**
   ```bash
   ipconfig getifaddr en0
   # Primer: 192.168.1.100
   ```

2. **Posodobi .env:**
   ```env
   APP_URL=http://192.168.1.100
   REVERB_HOST="192.168.1.100"
   REVERB_PORT=8080
   REVERB_SCHEME=http
   ```

3. **Zaženi Reverb z IP:**
   ```bash
   php artisan reverb:start --host=0.0.0.0 --port=8080
   ```

4. **Ponovno zgradi frontend:**
   ```bash
   npm run build
   ```

5. **Kolegi lahko dostopajo preko:**
   - `http://192.168.1.100` (če uporabljaš Herd)
   - Ali: `http://192.168.1.100:8000` (če uporabljaš php artisan serve)

---

## 🚨 Troubleshooting

### Problem: Stran ne deluje
**Rešitev:** Preveri ali Herd teče in ali je domena `.test` dostopna

### Problem: Real-time ne dela
**Rešitev:**
```bash
php artisan reverb:start
```
Pustи terminal odprt!

### Problem: PostgreSQL napaka
**Rešitev:** Preveri ali je PostgreSQL zagnan v Herdu:
- Odpri **Herd aplikacijo**
- Preveri status PostgreSQL
- Preveri `.env` da je `DB_DATABASE=kitio-game`

### Problem: "Class not found" napaka
**Rešitev:**
```bash
composer dump-autoload
php artisan optimize:clear
```

### Problem: Stile se ne naložijo
**Rešitev:**
```bash
npm run build
php artisan optimize:clear
```

---

## 📦 Deployment na Laravel Cloud

Ko želiš dati v produkcijo:

1. **Pushaj na GitHub**
2. **Poveži z Laravel Cloud**
3. **Nastavi Environment Variables:**
   ```env
   BROADCAST_CONNECTION=reverb
   DB_CONNECTION=pgsql
   REVERB_SCHEME=https
   ```
4. **Deploy!**

Laravel Cloud bo avtomatsko:
- Zagnal Reverb WebSocket server
- Nastavil PostgreSQL bazo
- Zgradil frontend assets

---

## 💡 Tips & Tricks

### Hitre bližnjice:

- **Novi turnir:** Ctrl+N / Cmd+N na glavni strani
- **Dark mode:** Avtomatsko zaznava sistem preference
- **Deli link:** Enostavno kopiraj URL iz browserja

### Najboljše prakse:

✅ Vedno imej vsaj 4 igralce
✅ Pusti Reverb terminal odprt
✅ Deli URL turnirja pred začetkom
✅ Testiraj real-time z dvema browserji

### Za večje turnirje:

- 8 igralcev = Četrtfinale → Polfinale → Finale
- 16 igralcev = Osmina → Četrtfinale → Polfinale → Finale
- Aplikacija avtomatsko prilagodi število krogov

---

## 🎉 Enjoy!

Aplikacija je pripravljena za jutri! Želim ti uspešen turnir s kolegi! 🏆

**Quick Start jutri:**
1. Odpri terminal
2. `cd /Users/ziganikolic/Herd/kitio-game`
3. `php artisan reverb:start`
4. Obišči **https://kitio-game.test**
5. Ustvari turnir in pošlji link kolegom!

---

**Tech Stack:** Laravel 12, Vue 3, Inertia, Tailwind 4, PostgreSQL, Laravel Reverb
**Avtor:** Built by Claude Code
**Datum:** 29.10.2025

# 🚀 HITRI START ZA JUTRI

## ✅ Vse je PRIPRAVLJENO!

Aplikacija je 100% konfigurirana in pripravljena za uporabo.

---

## 🎯 3 KORAKI ZA ZAČETEK:

### 1. Zaženi Reverb WebSocket Server

Odpri **Terminal** in zaženi:

```bash
cd /Users/ziganikolic/Herd/kitio-game
php artisan reverb:start
```

⚠️ **POMEMBNO:** Pustи ta terminal **ODPRT** med uporabo aplikacije!

### 2. Odpri Browser

Pojdi na: **https://kitio-game.test**

### 3. Ustvari Turnir

Klikni **"Ustvari Nov Turnir"** in sledi navodilom!

---

## 🎮 Kako Uporabiti

### Ustvari Turnir:
1. Klikni "Ustvari Nov Turnir"
2. Vnesi ime turnirja
3. Vnesi igralce (enega na vrstico, min 4)
4. Klikni "Začni Žreb"

### Igraj:
- Klikni na ime ekipe, ki je **zmagala**
- Zmagovalec postane **zelen**
- Naslednji krog se ustvari avtomatsko

### Deli s Kolegi:
- Kopiraj URL turnirja
- Pošlji kolegom
- **VSI vidijo spremembe TAKOJ!** ⚡

---

## 🔥 Real-Time

Ko **kdorkoli** klikne na zmagovalca → **VSI ostali vidijo v < 1 sekundi!**

Ni potrebe po refreshu - WebSocket poskrbi za vse! 🎉

---

## ❓ Če Nekaj Ne Dela

### Stran ne dela?
```bash
# Preveri ali je Herd zagnan
# Poskusi v terminalu:
curl https://kitio-game.test
```

### Real-time ne dela?
```bash
# Preveri ali Reverb teče:
php artisan reverb:start
```

### Napake v konzoli?
```bash
# Počisti cache:
php artisan optimize:clear
npm run build
```

### PostgreSQL napaka?
```bash
# Zaženi migracije:
php artisan migrate
```

---

## 📱 Testiranje Pred Jutri

1. Odpri **2 browser okna** (ali Chrome + Safari)
2. Obiskaj `https://kitio-game.test` v **obeh**
3. Ustvari testni turnir
4. V **prvem oknu**: klikni na zmagovalca
5. V **drugem oknu**: vidiš spremembo TAKOJ!

Če vidiš spremembe v obeh → **VSE DELA!** ✅

---

## 🏆 Srečno Jutri!

Vse je nastavljeno in pripravljeno. Uživaj v turnirju s kolegi! 🎉

**Quick checklist pred začetkom:**
- ✅ Reverb server teče (`php artisan reverb:start`)
- ✅ Browser odprt na `https://kitio-game.test`
- ✅ Testirano z 2 okni

---

**Za več info:** Glej `KAKO_UPORABLJATI.md`

**Tech Support:** https://github.com/anthropics/claude-code/issues

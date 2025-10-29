# 🏆 Tournament Bracket Application

Laravel aplikacija za organizacijo turnirjev dvojic s sistemom izpadanja.

## Funkcionalnosti

- ✅ Kreiranje turnirjev
- ✅ Vnos igralcev
- ✅ Naključni žreb in ustvarjanje dvojic
- ✅ Bracket sistem izpadanja
- ✅ Real-time posodabljanje rezultatov
- ✅ Deljenje turnirjev preko URL-ja
- ✅ Multi-user support

## Tehnologije

- Laravel 11
- Vue.js 3
- Laravel Reverb (Real-time)
- Tailwind CSS
- SQLite

## Namestitev

```bash
# Kloniraj repozitorij
git clone <repo-url>
cd DYP

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

# Zaženi strežnik
php artisan serve

# V drugem terminalu zaženi Reverb
php artisan reverb:start
```

## Uporaba

1. Odpri aplikacijo v brskalniku
2. Vnesi imena igralcev (vsaj 4)
3. Klikni "Začni žreb"
4. Klikaj na zmagovalce posameznih tekem
5. Deli link turnirja s kolegi za skupno spremljanje

## Deployment na Laravel Cloud

```bash
# Pripravi produkcijsko verzijo
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## License

MIT

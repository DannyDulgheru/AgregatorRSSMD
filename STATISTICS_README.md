# 📊 Sistem de Statistici - Documentație

## Despre

Sistemul de statistici oferă tracking detaliat al vizitatorilor și analize comprehensive pentru site-ul de agregare de știri.

## Caracteristici

### 📈 Tracking Automat
- **Vizitatori Online**: Afișează numărul de utilizatori activi în ultimele 5 minute
- **Vizite Zilnice**: Total vizite și vizitatori unici pe zi
- **Vizite Lunare**: Statistici cumulative pentru luna curentă
- **Istoric**: Date istorice pentru perioade de 7, 30, 90, sau 365 zile

### 🔍 Analize Detaliate

#### Dispozitive
- Desktop
- Mobile
- Tablet
- Procente și grafice vizuale

#### Browsere
- Chrome, Firefox, Safari, Edge, Opera, etc.
- Top 10 browsere utilizate
- Procente de utilizare

#### Sisteme de Operare
- Windows, macOS, Linux, Android, iOS
- Distribuție și procente

#### Locații Geografice
- Țări (Moldova implicit pentru IPs locale)
- Posibilitate de integrare cu servicii GeoIP

#### Pagini Populare
- Top 15 pagini cel mai des vizitate
- Număr de vizite totale și vizitatori unici per pagină

#### Surse de Trafic
- Direct (fără referrer)
- Link-uri externe
- Motoare de căutare
- Social media

### 📊 Dashboard Visual

- **Card-uri Statistice**: Overview rapid cu date cheie
- **Grafice Interactive**: Folosind Chart.js pentru vizualizări clare
- **Tabele Detaliate**: Date complete cu procente și bare de progres
- **Auto-refresh**: Actualizare automată a utilizatorilor online la fiecare 30 secunde

## Structura Bazei de Date

### Tabelul `visits`
Înregistrează fiecare vizită individuală:
- `visitor_id`: ID unic al vizitatorului (cookie-based)
- `session_id`: ID sesiune PHP
- `ip_address`: Adresa IP
- `user_agent`: Browser și sistem de operare
- `device_type`: Mobile/Tablet/Desktop
- `browser`: Tip browser
- `os`: Sistem de operare
- `country`: Țara (din IP)
- `page_url`: URL-ul vizitat
- `referrer`: Sursa traficului
- `visit_date`: Data vizitei
- `visit_time`: Ora exactă

### Tabelul `daily_stats`
Sumar zilnic pentru performanță:
- `stat_date`: Data
- `total_visits`: Total vizite
- `unique_visitors`: Vizitatori unici
- `total_pageviews`: Total pagini vizualizate

## Cum Funcționează

### 1. Tracking Automat
Fișierul `includes/tracker.php` este inclus în toate paginile publice și înregistrează automat fiecare vizită.

```php
require_once __DIR__ . '/includes/tracker.php';
```

### 2. Detectare Automată
Sistemul detectează automat:
- Tip dispozitiv (din User-Agent)
- Browser (din User-Agent)
- Sistem de operare (din User-Agent)
- Visitor ID (cookie persistent, 1 an)
- Session ID (sesiune PHP)

### 3. Stocare Eficientă
- Vizitele sunt stocate în `visits`
- Sumarele zilnice în `daily_stats` pentru queries mai rapide
- Indexuri pe coloanele frecvent folosite

### 4. Privacy-First
- Nu colectează date personale
- IP-urile pot fi anonimizate
- Cookie-uri doar pentru funcționalitate
- Conform GDPR

## Acces la Statistici

### Admin Panel
Accesează: `/admin/statistics.php`

**Autentificare necesară**: Da (admin/pass)

### Filtre Disponibile
- Ultima săptămână (7 zile)
- Ultima lună (30 zile)
- Ultimele 3 luni (90 zile)
- Ultimul an (365 zile)

## Optimizări Performanță

### Indexuri Create
```sql
CREATE INDEX idx_visits_date ON visits(visit_date);
CREATE INDEX idx_visits_visitor ON visits(visitor_id);
CREATE INDEX idx_visits_session ON visits(session_id);
CREATE INDEX idx_visits_page ON visits(page_url);
```

### Cache
Tabelul `daily_stats` servește ca un cache pre-calculat pentru reducerea timpului de query.

## Integrări Posibile

### GeoIP
Pentru locații precise, poți integra:
- **MaxMind GeoIP2**: https://www.maxmind.com/
- **IP2Location**: https://www.ip2location.com/
- **ipapi.co**: https://ipapi.co/

Exemplu integrare în `includes/analytics.php`:
```php
function getCountryFromIP($ip) {
    // API call
    $data = file_get_contents("https://ipapi.co/{$ip}/json/");
    $geo = json_decode($data, true);
    return $geo['country_name'] ?? 'Unknown';
}
```

### Export Date
Poți adăuga funcții de export:
- CSV
- Excel
- PDF Reports
- JSON API

## Întreținere

### Curățare Date Vechi
Pentru a preveni creșterea excesivă a bazei de date:

```sql
-- Șterge vizite mai vechi de 2 ani
DELETE FROM visits WHERE visit_date < date('now', '-2 years');

-- Curăță stats zilnice vechi
DELETE FROM daily_stats WHERE stat_date < date('now', '-2 years');
```

### Vacuum Database
Periodic (lunar):
```bash
php -r "require 'config/database.php'; getDB()->exec('VACUUM;');"
```

## Securitate

- ✅ Tracking doar pentru pagini publice (exclude `/admin`)
- ✅ Prepared statements pentru toate query-urile SQL
- ✅ Validare și sanitizare date input
- ✅ Error handling cu logging (nu afișare erori)
- ✅ Rate limiting poate fi adăugat pentru a preveni abuse

## Performance Tips

1. **Folosește daily_stats** pentru rapoarte overview
2. **Limitează range-ul de date** la maxim 1 an pentru queries complexe
3. **Implementează caching** (Redis/Memcached) pentru stats dashboard
4. **Arhivează date vechi** în tabele separate
5. **Monitorizează dimensiunea** bazei de date

## Troubleshooting

### Statisticile nu se actualizează
- Verifică că `tracker.php` este inclus în toate paginile
- Verifică permisiunile bazei de date
- Verifică logs pentru erori PHP

### Utilizatori online incorect
- Verifică că sesiunile PHP funcționează corect
- Ajustează timeout-ul (default: 5 minute)

### Performanță slabă
- Adaugă mai multe indexuri
- Optimizează queries (EXPLAIN)
- Implementează caching
- Arhivează date vechi

## Viitor - Features Planificate

- 📧 Rapoarte automate prin email
- 📱 Dashboard mobile
- 🔔 Alerte pentru trafic neobișnuit
- 🎯 Goal tracking și conversii
- 🔥 Heatmaps pentru click patterns
- ⚡ Real-time dashboard cu WebSockets
- 📊 Export PDF al rapoartelor
- 🌍 Hartă interactivă cu vizitatori
- 📈 Predicții AI pentru trafic

---

**Versiune**: 1.0.0  
**Data**: 30 Ianuarie 2026  
**Autor**: AgregatorRSSMD Team

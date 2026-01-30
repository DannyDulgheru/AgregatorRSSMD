# 🚀 Quick Start - Sistem de Statistici

## Setup Rapid

### 1. Creează Tabelele
```bash
php config/create_stats_table.php
```

Răspuns: `✓ Statistics tables created successfully!`

### 2. Tracking Automat
Tracking-ul este deja activ! Fiecare vizită pe paginile publice este înregistrată automat.

Pagini cu tracking:
- ✅ `/` - Pagina principală
- ✅ `/zen` - Modul ZEN
- ✅ `/tags` - Pagina de taguri
- ✅ `/article/*` - Pagini de articole
- ✅ `/about` - Despre
- ✅ `/privacy` - Politica de confidențialitate
- ✅ `/terms` - Termeni și condiții

Pagini FĂRĂ tracking:
- ❌ `/admin/*` - Panoul de administrare

### 3. Vezi Statisticile

**Accesează**: http://localhost:8000/admin/statistics.php

**Login**: 
- Username: `admin`
- Password: `pass`

**Navigare**:
1. Click pe **"Statistici"** în sidebar-ul admin
2. Selectează perioada dorită (7/30/90/365 zile)
3. Explorează datele!

## Ce Poți Vedea

### 📊 Card-uri Overview
- **Utilizatori Online** (ultima 5 minute) - cu auto-refresh la 30s
- **Vizite Astăzi** (total + unici)
- **Vizite Luna Curentă** (total + unici)
- **Sesiuni Luna Curentă**

### 📈 Grafic Evoluție
- Linie pentru total vizite
- Linie pentru vizitatori unici
- Interactiv (hover pentru detalii)

### 📱 Analiză Dispozitive
- Desktop
- Mobile  
- Tablet
- % utilizare cu bare vizuale

### 🌐 Browsere Top
- Top 10 browsere
- Număr vizite
- % utilizare

### 💻 Sisteme de Operare
- Windows, macOS, Linux, Android, iOS
- Distribuție completă

### 🌍 Țări
- Top 10 țări
- Număr vizitatori per țară

### 📄 Top Pagini
- 15 cele mai vizitate pagini
- Total vizite
- Vizitatori unici

### 🔗 Surse Trafic
- Direct
- Referrers externi
- Top 10 surse

## Filtre Disponibile

Schimbă perioada din dropdown:
- **7 zile** - Ultima săptămână
- **30 zile** - Ultima lună  
- **90 zile** - Ultimele 3 luni
- **365 zile** - Ultimul an

## Testare

### Generează Trafic de Test
Vizitează diferite pagini pentru a genera date:

```bash
# Pagina principală
http://localhost:8000/

# ZEN mode
http://localhost:8000/zen

# Tags
http://localhost:8000/tags

# Articole (schimbă ID-ul)
http://localhost:8000/article/1
http://localhost:8000/article/2
```

### Verifică Tracking
```bash
# Din alt terminal
sqlite3 data/newsdb.sqlite "SELECT COUNT(*) FROM visits;"
```

### Vezi Date Recente
```bash
sqlite3 data/newsdb.sqlite "SELECT page_url, device_type, browser, visit_time FROM visits ORDER BY visit_time DESC LIMIT 10;"
```

## Auto-Refresh

Dashboard-ul se actualizează automat:
- **Utilizatori Online**: La fiecare 30 secunde
- **Alte statistici**: Manual (refresh browser sau schimbă perioada)

## Mobile Friendly

Dashboard-ul este complet responsive:
- Card-uri stack vertical pe mobile
- Tabele scroll orizontal
- Grafice adaptive
- Touch-friendly

## Tips & Tricks

### Vezi Stats în Timp Real
Deschide 2 ferestre:
1. `/admin/statistics.php` - Dashboard
2. `/` - Navighează normal pe site

Dashboard-ul va afișa "1 Utilizator Online" (tu!)

### Compară Perioade
- Deschide statistici în multiple tab-uri
- Setează perioade diferite în fiecare
- Compară vizual rezultatele

### Export Date (Manual)
```bash
# Export toate vizitele din ultima lună în CSV
sqlite3 -header -csv data/newsdb.sqlite "SELECT * FROM visits WHERE visit_date >= date('now', '-30 days');" > stats_export.csv
```

### Top Ore de Trafic
```bash
sqlite3 data/newsdb.sqlite "
SELECT 
    strftime('%H', visit_time) as hour,
    COUNT(*) as visits
FROM visits
WHERE visit_date >= date('now', '-7 days')
GROUP BY hour
ORDER BY visits DESC;
"
```

## Troubleshooting

### Nu văd date în dashboard
1. Verifică că ai vizitat pagini publice (nu doar admin)
2. Refresh browser-ul (Ctrl+F5)
3. Verifică că tabelele există: `sqlite3 data/newsdb.sqlite ".tables"`

### "0 Utilizatori Online" dar eu sunt pe site
- Cookie-urile trebuie activate
- JavaScript activat
- Navighează pe o pagină publică (nu admin)

### Erori PHP
Verifică logs:
```bash
tail -f php_errors.log
```

## Next Steps

După ce statisticile funcționează:

1. **Monitorizează regulat** - Verifică statisticile zilnic
2. **Identifică tendințe** - Ce conținut atrage trafic?
3. **Optimizează** - Focus pe dispozitivele și browserele populare
4. **Planifică** - Postează conținut când ai cel mai mult trafic

---

**Ai nevoie de ajutor?** 
- 📖 Vezi `STATISTICS_README.md` pentru detalii complete
- 🐛 Report issues în repository
- 💡 Sugestii pentru îmbunătățiri sunt binevenite!

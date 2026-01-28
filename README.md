# Agregator Știri Republica Moldova

Platformă PHP cu SQLite pentru agregarea știrilor din Republica Moldova.

## Caracteristici

- ✅ Scraping automat de știri de la site-uri din Moldova
- ✅ Suport pentru RSS feeds și HTML scraping
- ✅ Baza de date SQLite (ușor de gestionat)
- ✅ Interfață modernă și responsive
- ✅ Multiple moduri de vizualizare (Grid, Listă, Compact)
- ✅ Panou de administrare complet
- ✅ Căutare și filtrare știri
- ✅ Design inspirat din culorile Moldovei

## Cerințe

- PHP 7.4 sau mai nou
- Extensii PHP: PDO, SQLite3, cURL, DOMDocument, libxml
- Server web (Apache cu mod_rewrite sau Nginx)
- Acces la internet pentru scraping

## Instalare

1. **Clonează sau descarcă proiectul**

2. **Rulează scriptul de inițializare:**
   ```bash
   php init.php
   ```
   
   Acest script va:
   - Creează baza de date SQLite
   - Creează toate tabelele necesare
   - Adaugă site-uri demo de știri din Moldova
   - Creează contul admin (username: `admin`, password: `pass`)

3. **Configurează serverul web:**
   - Asigură-te că directorul `data/` este accesibil pentru scriere
   - Verifică că `.htaccess` este activat (pentru Apache)

4. **Configurează cron job pentru scraping automat:**
   
   **Linux/Mac:**
   ```bash
   */5 * * * * /usr/bin/php /calea/catre/proiect/cron/scrape.php
   ```
   
   **Windows (Task Scheduler):**
   - Creează o sarcină nouă
   - Rulează la fiecare 5 minute
   - Comandă: `php.exe C:\calea\catre\proiect\cron\scrape.php`

## Utilizare

### Acces Public
- Deschide `http://localhost/` în browser
- Navighează prin știri, folosește filtrele și schimbă modul de vizualizare

### Panou Admin
- Accesează `http://localhost/admin/`
- Login: `admin` / `pass`
- Din panou poți:
  - Gestiona site-urile de știri
  - Vizualiza și șterge articole
  - Configura setările platformei
  - Rula scraping manual

## Structura Proiectului

```
newsmd/
├── config/          # Configurare baza de date și setări
├── includes/        # Funcții helper, auth, scraper
├── admin/           # Panou administrare
├── assets/          # CSS și JavaScript
├── cron/            # Script-uri pentru scraping automat
├── data/            # Baza de date SQLite (se creează automat)
├── index.php        # Pagină principală
├── article.php      # Pagină articol individual
├── init.php         # Script inițializare
└── .htaccess        # Configurare Apache
```

## Site-uri Demo

Platforma vine cu următoarele site-uri demo preconfigurate:
- Unimedia
- Jurnal.md
- Publika.md
- Ziarul de Gardă
- Deschide.md
- TV8
- ProTV Chișinău

## Securitate

- Parole hash-uite cu bcrypt
- Protecție CSRF pentru form-uri admin
- Sanitizare input/output
- SQL prepared statements
- Protecție directoare sensibile

## Personalizare

### Modificare culori
Editează variabilele CSS din `assets/css/style.css`:
```css
:root {
    --primary: #003d82;
    --secondary: #ffd700;
    --accent: #cc0000;
}
```

### Adăugare site-uri noi
1. Accesează Admin Panel → Site-uri Știri
2. Adaugă URL-ul site-ului și RSS feed (dacă există)
3. Selectează tipul de scraping (RSS sau HTML)

## Notă

Acest proiect este destinat pentru uz educațional și personal. Respectă termenii și condițiile site-urilor de știri când faci scraping.

## Licență

Proiect open source pentru uz educațional.

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

3. **Pornește serverul:**

   **Dezvoltare (PHP Built-in Server):**
   ```bash
   php -S localhost:8000 router.php
   ```
   
   **Producție (Apache/Nginx):**
   - Asigură-te că directorul `data/` este accesibil pentru scriere
   - Verifică că `.htaccess` este activat (pentru Apache)
   - Configurează serverul să redirecționeze toate cererile către `index.php`

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

## Migrare la Clean URLs

**Important:** Proiectul utilizează acum URL-uri clean (fără extensia `.php`).

### Format nou URL-uri:
- Homepage: `/`
- Articol: `/article/123` (în loc de `/article.php?id=123`)
- Taguri: `/tags` (în loc de `/tags.php`)
- Despre: `/about` (în loc de `/about.php`)
- Admin: `/admin`, `/admin/sites`, `/admin/articles`, `/admin/settings`

### Redirects 301 (SEO Friendly)
Toate vechile URL-uri cu `.php` redirectează automat la versiunile clean:
- `/article.php?id=123` → `/article/123` (permanent redirect)
- `/tags.php` → `/tags`
- `/about.php` → `/about`

### Post-Migrare: Curățare Cache

După migrare, efectuați următoarele:

1. **Clear Browser Cache:**
   - Chrome/Edge: `Ctrl + Shift + Del` → Clear browsing data
   - Firefox: `Ctrl + Shift + Del` → Clear recent history
   - Safari: `Cmd + Option + E`

2. **Restart Web Server:**
   - Apache (Linux): `sudo systemctl restart apache2`
   - Apache (Windows): restart din Services
   - PHP Built-in: opriți și reporniți `php -S localhost:8000`

3. **Verificare Redirects:**
   Testați că vechile URL-uri redirectează corect:
   ```bash
   curl -I http://localhost:8000/article.php?id=123
   # Should return: HTTP/1.1 301 Moved Permanently
   # Location: /article/123
   ```

4. **Verificare Sitemap:**
   Accesați `/sitemap.xml` pentru a vedea toate URL-urile clean generate dinamic.

5. **Submit Sitemap to Search Engines:**
   - Google Search Console: Submit `http://your-domain.com/sitemap.xml`
   - Bing Webmaster Tools: Submit sitemap URL

### Troubleshooting

**Problema:** 404 errors la URL-uri clean
- **Soluție:** Verificați că Apache mod_rewrite este activat:
  ```bash
  sudo a2enmod rewrite
  sudo systemctl restart apache2
  ```

**Problema:** Redirects nu funcționează
- **Soluție:** Verificați că `.htaccess` este citit de Apache (AllowOverride All în virtual host)

**Problema:** Cache-ul afișează URL-uri vechi
- **Soluție:** Clear cache complet (vezi pașii de mai sus) și verificați headerele Cache-Control în `.htaccess`

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

## SEO & Indexing

### Sitemap
Sitemap-ul este generat dinamic la `/sitemap.xml` și include:
- Homepage
- Toate paginile statice (/tags, /about)
- Toate articolele în format `/article/123`

### Robots.txt
Fișierul `robots.txt` exclude directoarele admin și cron de la indexare, dar permite crawlere-lor să indexeze conținutul public.

## Notă

Acest proiect este destinat pentru uz educațional și personal. Respectă termenii și condițiile site-urilor de știri când faci scraping.

## Licență

Proiect open source pentru uz educațional.

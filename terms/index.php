<?php
/**
 * Terms and Conditions Page
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/tracker.php';

$siteTitle = getSetting('site_title', SITE_NAME);
$activeTheme = getSetting('active_theme', 'default');

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termeni și Condiții - <?php echo e($siteTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/themes.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <script>document.documentElement.setAttribute('data-theme', '<?php echo $activeTheme; ?>');</script>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="/"><?php echo e($siteTitle); ?></a></h1>
                </div>
                <nav class="main-nav">
                    <a href="/" class="nav-link">Acasă</a>
                    <a href="/zen" class="nav-link">🧘 ZEN Mode</a>
                    <a href="/tags" class="nav-link">Taguri</a>
                    <a href="/about" class="nav-link">Despre</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <div class="about-hero">
                <div class="about-hero-content">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor" class="hero-icon">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h1 class="about-hero-title">Termeni și Condiții</h1>
                    <p class="about-hero-subtitle">Regulile de utilizare a platformei noastre</p>
                </div>
            </div>

            <!-- Terms and Conditions Content -->
            <div class="legal-section">
                <div class="legal-content">
                    <h3>1. Acceptarea Termenilor</h3>
                    <p>
                        Prin accesarea și utilizarea platformei <strong><?php echo e($siteTitle); ?></strong>, acceptați automat 
                        acești termeni și condiții. Dacă nu sunteți de acord cu acești termeni, vă rugăm să nu utilizați platforma.
                    </p>

                    <h3>2. Descrierea Serviciului</h3>
                    <p>
                        <strong><?php echo e($siteTitle); ?></strong> este o platformă de agregare automată a știrilor care:
                    </p>
                    <ul>
                        <li>Colectează automat articole de știri din surse publice din Republica Moldova</li>
                        <li>Afișează titluri, imagini preview și extrase scurte din articole</li>
                        <li>Oferă link-uri directe către articolele complete de pe site-urile surselor originale</li>
                        <li>Organizează conținutul prin taguri și categorii pentru navigare ușoară</li>
                        <li>Oferă funcții de căutare și filtrare a conținutului</li>
                    </ul>

                    <h3>3. Drepturi de Proprietate Intelectuală</h3>
                    <p><strong>Conținutul agregatat:</strong></p>
                    <ul>
                        <li>Tot conținutul (articole, imagini, texte) aparține surselor originale respective</li>
                        <li>Nu revendicăm drepturile de autor asupra conținutului agregatat</li>
                        <li>Afișăm titluri, imagini preview și extrase scurte în baza dreptului de citare (fair use)</li>
                        <li>Oferim întotdeauna link-uri către articolele complete de pe site-urile sursă</li>
                    </ul>

                    <p><strong>Platforma:</strong></p>
                    <ul>
                        <li>Design-ul, codul sursă și funcționalitățile platformei sunt proprietatea noastră</li>
                        <li>Este interzisă copierea, reproducerea sau distribuirea codului platformei fără permisiune</li>
                    </ul>

                    <h3>4. Utilizarea Platformei</h3>
                    <p><strong>Utilizatorii au dreptul să:</strong></p>
                    <ul>
                        <li>Navigheze liber prin conținutul agregatat</li>
                        <li>Folosească funcțiile de căutare și filtrare</li>
                        <li>Acceseze link-urile către articolele complete</li>
                        <li>Partajeze link-uri către platforma noastră</li>
                    </ul>

                    <p><strong>Este interzis să:</strong></p>
                    <ul>
                        <li>Folosiți platforma pentru activități ilegale sau neautorizate</li>
                        <li>Încercați să compromiteți securitatea sau funcționalitatea platformei</li>
                        <li>Folosiți roboți, scripturi sau alte metode automate pentru a extrage date în masă</li>
                        <li>Reproduceți sau distribuiți conținutul platformei fără atribuire corectă</li>
                        <li>Interferați cu funcționarea normală a platformei</li>
                    </ul>

                    <h3>5. Disclaimer și Limitarea Răspunderii</h3>
                    <p><strong>Platforma este oferită "ca atare" (as-is):</strong></p>
                    <ul>
                        <li>Nu garantăm acuratețea, completitudinea sau actualitatea conținutului agregatat</li>
                        <li>Nu suntem responsabili pentru conținutul, erorile sau omisiunile din articolele sursă</li>
                        <li>Nu garantăm disponibilitatea neîntreruptă a platformei</li>
                        <li>Nu suntem responsabili pentru pagube directe, indirecte sau consecințe ale utilizării platformei</li>
                    </ul>

                    <h3>6. Link-uri către Site-uri Terțe</h3>
                    <p>
                        Platforma conține link-uri către site-uri externe (surse de știri). Nu controlăm și nu suntem responsabili 
                        pentru conținutul, politicile sau practicile acestor site-uri externe. Accesarea lor se face pe propria 
                        răspundere a utilizatorului.
                    </p>

                    <h3>7. Modificarea Serviciului</h3>
                    <p>
                        Ne rezervăm dreptul de a modifica, suspenda sau întrerupe orice aspect al platformei, temporar sau permanent, 
                        cu sau fără notificare prealabilă. Nu vom fi răspunzători față de dvs. sau terți pentru astfel de modificări.
                    </p>

                    <h3>8. Dreptul de Autor - DMCA</h3>
                    <p>
                        Respectăm drepturile de autor. Dacă considerați că conținutul de pe platforma noastră încalcă drepturile dvs. 
                        de autor, vă rugăm să ne contactați cu următoarele informații:
                    </p>
                    <ul>
                        <li>Identificarea lucrării protejate prin drepturi de autor</li>
                        <li>Identificarea materialului care încalcă drepturile</li>
                        <li>Informații de contact (email, telefon)</li>
                        <li>O declarație de bună-credință că utilizarea nu este autorizată</li>
                    </ul>
                    <p>Vom investiga și vom lua măsurile necesare în cel mai scurt timp posibil.</p>

                    <h3>9. Modificarea Termenilor</h3>
                    <p>
                        Ne rezervăm dreptul de a actualiza acești termeni și condiții în orice moment. Modificările vor fi publicate 
                        pe această pagină cu data actualizării. Utilizarea continuă a platformei după modificări constituie acceptarea 
                        noilor termeni.
                    </p>

                    <h3>10. Legislație Aplicabilă</h3>
                    <p>
                        Acești termeni și condiții sunt guvernați de și interpretați în conformitate cu legislația Republicii Moldova. 
                        Orice dispute vor fi rezolvate prin negociere amiabilă sau, în cazul eșecului, în instanțele competente 
                        din Republica Moldova.
                    </p>

                    <h3>11. Contact</h3>
                    <p>
                        Pentru întrebări despre acești termeni și condiții, vă rugăm să ne contactați la:
                    </p>
                    <p><strong>Email:</strong> contact@<?php echo strtolower(str_replace(' ', '', $siteTitle)); ?>.md</p>
                    <p><strong>Data ultimei actualizări:</strong> <?php echo date('d.m.Y'); ?></p>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e($siteTitle); ?>. Toate drepturile rezervate.</p>
            <p>Agregator de știri din Republica Moldova</p>
            <div class="footer-links">
                <a href="/about">Despre</a>
                <a href="/privacy">Politica de Confidențialitate</a>
                <a href="/terms">Termeni și Condiții</a>
            </div>
        </div>
    </footer>
</body>
</html>

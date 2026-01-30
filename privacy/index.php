<?php
/**
 * Privacy Policy Page
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
    <title>Politica de Confidențialitate - <?php echo e($siteTitle); ?></title>
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
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h1 class="about-hero-title">Politica de Confidențialitate</h1>
                    <p class="about-hero-subtitle">Protecția datelor tale personale este importantă pentru noi</p>
                </div>
            </div>

            <!-- Privacy Policy Content -->
            <div class="legal-section">
                <div class="legal-content">
                    <h3>1. Despre Platformă</h3>
                    <p>
                        <strong><?php echo e($siteTitle); ?></strong> este o platformă de agregare automată a știrilor din Republica Moldova, 
                        creată pentru a oferi utilizatorilor acces centralizat și rapid la informații de actualitate din diverse surse media.
                    </p>

                    <h3>2. Colectarea și Utilizarea Datelor</h3>
                    <p><strong>Date pe care le colectăm:</strong></p>
                    <ul>
                        <li><strong>Cookies tehnice:</strong> Pentru funcționalitatea site-ului (preferințe de vizualizare, temă aleasă)</li>
                        <li><strong>LocalStorage:</strong> Pentru stocarea locală a preferințelor (articole vizitate, mod de afișare)</li>
                        <li><strong>Date de navigare:</strong> Informații anonime despre paginile vizitate și timpul petrecut pe site</li>
                    </ul>

                    <p><strong>Cum folosim datele:</strong></p>
                    <ul>
                        <li>Pentru a îmbunătăți experiența utilizatorului pe platformă</li>
                        <li>Pentru a personaliza afișarea conținutului (articole vizitate, preferințe de sortare)</li>
                        <li>Pentru a analiza traficul și a optimiza performanța site-ului</li>
                        <li>Pentru a detecta și preveni abuzuri sau utilizări neautorizate</li>
                    </ul>

                    <h3>3. Partajarea Datelor</h3>
                    <p>
                        <strong>NU vindem, NU închiriem și NU partajăm datele personale</strong> cu terți pentru scopuri comerciale. 
                        Datele sunt stocate local în browser-ul dvs. și nu sunt transmise către servere externe, 
                        cu excepția datelor necesare pentru funcționarea tehnică a platformei.
                    </p>

                    <h3>4. Cookie-uri și Tehnologii Similare</h3>
                    <p>Utilizăm următoarele tipuri de cookie-uri:</p>
                    <ul>
                        <li><strong>Cookie-uri esențiale:</strong> Necesare pentru funcționarea de bază a site-ului</li>
                        <li><strong>Cookie-uri de preferințe:</strong> Salvează setările dvs. (temă, mod de vizualizare)</li>
                        <li><strong>LocalStorage:</strong> Stochează local lista articolelor vizitate pentru a le marca vizual</li>
                    </ul>
                    <p>Puteți șterge cookie-urile și datele din localStorage oricând din setările browser-ului dvs.</p>

                    <h3>5. Drepturile Utilizatorilor</h3>
                    <p>Aveți următoarele drepturi:</p>
                    <ul>
                        <li><strong>Dreptul de acces:</strong> Puteți solicita informații despre datele stocate</li>
                        <li><strong>Dreptul la ștergere:</strong> Puteți șterge datele stocate local oricând</li>
                        <li><strong>Dreptul la portabilitate:</strong> Datele sunt stocate local și pot fi exportate din browser</li>
                        <li><strong>Dreptul de opoziție:</strong> Puteți dezactiva cookie-urile din setările browser-ului</li>
                    </ul>

                    <h3>6. Securitatea Datelor</h3>
                    <p>
                        Implementăm măsuri de securitate tehnice și organizatorice pentru a proteja datele împotriva accesului neautorizat, 
                        pierderii sau modificării. Platforma folosește conexiuni securizate și nu colectează date sensibile personale.
                    </p>

                    <h3>7. Link-uri către Site-uri Externe</h3>
                    <p>
                        Platforma conține link-uri către site-urile surselor de știri. Nu suntem responsabili pentru practicile de 
                        confidențialitate ale acestor site-uri externe. Vă recomandăm să citiți politicile de confidențialitate 
                        ale fiecărui site pe care îl vizitați.
                    </p>

                    <h3>8. Modificări ale Politicii</h3>
                    <p>
                        Ne rezervăm dreptul de a actualiza această politică de confidențialitate. Modificările vor fi publicate pe 
                        această pagină cu data actualizării. Utilizarea continuă a platformei după modificări constituie acceptarea 
                        noii politici.
                    </p>

                    <h3>9. Contact</h3>
                    <p>
                        Pentru întrebări despre această politică de confidențialitate, vă rugăm să ne contactați la:
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

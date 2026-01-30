<?php
/**
 * About Page - Project Information
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
    <title>Despre Noi - <?php echo e($siteTitle); ?></title>
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
                    <a href="/about" class="nav-link active">Despre</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <div class="about-hero">
                <div class="about-hero-content">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="hero-icon">
                        <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <h1 class="about-hero-title">Despre Noi</h1>
                    <p class="about-hero-subtitle">Platformă modernă de agregare a știrilor din Republica Moldova</p>
                </div>
            </div>

            <!-- Mission Statement -->
            <div class="about-mission">
                <div class="mission-card">
                    <h2>Misiunea Noastră</h2>
                    <p>
                        <strong><?php echo e($siteTitle); ?></strong> oferă acces rapid și centralizat la informații de actualitate 
                        din cele mai importante surse media din Republica Moldova. Colectăm automat știri de la publicații de încredere, 
                        organizându-le într-un format ușor de navigat.
                    </p>
                </div>
            </div>

            <!-- Features Section -->
            <div class="about-features">
                <h2 class="section-title-about">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    Caracteristici Principale
                </h2>
                <div class="features-grid-modern">
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <h3>Actualizare Automată</h3>
                        <p>Știrile sunt colectate și actualizate continuu pentru informații mereu la zi</p>
                    </div>
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        <h3>Organizare Inteligentă</h3>
                        <p>Fiecare articol este etichetat automat pentru o navigare rapidă și intuitivă</p>
                    </div>
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        <h3>Design Responsive</h3>
                        <p>Interfață optimizată perfect pentru desktop, tablete și smartphone-uri</p>
                    </div>
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3>Căutare Avansată</h3>
                        <p>Găsește rapid orice articol folosind filtre și funcția de căutare</p>
                    </div>
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h3>Performanță Ridicată</h3>
                        <p>Platformă ultrarapidă construită cu tehnologii moderne</p>
                    </div>
                    <div class="feature-card-modern">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <h3>Moduri de Vizualizare</h3>
                        <p>Alege între Grid, Listă sau Compact pentru experiență personalizată</p>
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="about-contact">
                <h2 class="section-title-about">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contactează-ne
                </h2>
                <div class="contact-cards">
                    <div class="contact-card">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3>Email</h3>
                        <p>contact@<?php echo strtolower(str_replace(' ', '', $siteTitle)); ?>.md</p>
                    </div>
                    <div class="contact-card">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                        <h3>Website</h3>
                        <p><a href="/"><?php echo e($siteTitle); ?></a></p>
                    </div>
                    <div class="contact-card">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3>Feedback</h3>
                        <p>Suntem deschiși la sugestii și îmbunătățiri</p>
                    </div>
                </div>
            </div>

            <!-- Support Section -->
            <div class="about-support">
                <h2 class="section-title-about">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Susține Proiectul
                </h2>
                <div class="support-card">
                    <div class="support-content">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="support-icon">
                            <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3>Ajută-ne să Menținem Proiectul</h3>
                        <p>
                            <?php echo e($siteTitle); ?> este un proiect independent care necesită resurse pentru 
                            mentenanță, hosting și dezvoltare continuă. Dacă apreciezi munca noastră și dorești 
                            să contribui la susținerea platformei, orice donație este binevenită și ne ajută să 
                            oferim în continuare un serviciu de calitate.
                        </p>
                        <div class="support-benefits">
                            <div class="benefit-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Menține serviciul gratuit și accesibil</span>
                            </div>
                            <div class="benefit-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Permite dezvoltarea de funcționalități noi</span>
                            </div>
                            <div class="benefit-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Asigură actualizări și îmbunătățiri constante</span>
                            </div>
                        </div>
                        <div class="paypal-section">
                            <p class="paypal-text">Poți face o donație prin PayPal:</p>
                            <a href="https://www.paypal.com/paypalme/yourpaypalid" target="_blank" rel="noopener" class="paypal-button">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.067 8.478c.492.88.556 2.014.3 3.327-.74 3.806-3.276 5.12-6.514 5.12h-.5a.805.805 0 00-.794.68l-.04.22-.63 3.993-.028.15a.805.805 0 01-.794.68H7.72a.483.483 0 01-.477-.558L7.418 21h1.518l.95-6.02h1.385c4.678 0 7.75-2.203 8.796-6.502z"/>
                                    <path d="M2.379 0h5.94a3.96 3.96 0 013.959 3.96v.04l.343 2.16c.149.943-.03 1.758-.48 2.408-.614.882-1.653 1.433-2.961 1.547L8.84 10h-.542a1.09 1.09 0 00-1.076.922l-.038.205-.96 6.097a.483.483 0 00.477.558h2.612a.805.805 0 00.794-.68l.028-.15.63-3.993.04-.22a.805.805 0 01.794-.68h.5c3.238 0 5.774-1.314 6.514-5.12.256-1.313.192-2.446-.3-3.327C17.897 1.647 15.776.5 12.896.5H2.379z"/>
                                </svg>
                                Donează prin PayPal
                            </a>
                            <p class="support-note">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Mulțumim pentru susținere! Fiecare contribuție contează.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disclaimer -->
            <div class="about-disclaimer">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3>Politica Conținutului</h3>
                    <p>
                        Acest site este un agregator de știri. Tot conținutul aparține surselor originale respective. 
                        Oferim linkuri directe către articolele complete pentru a respecta drepturile de autor. 
                        Pentru discuții despre prezența conținutului pe platformă, contactați-ne.
                    </p>
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

<?php
/**
 * Tags Page - All Popular Tags
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/tracker.php';

$siteTitle = getSetting('site_title', SITE_NAME);
$activeTheme = getSetting('active_theme', 'default');

// Get all popular tags
$popularTags = getPopularTags(100);

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taguri Populare - <?php echo e($siteTitle); ?></title>
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
                    <a href="/tags" class="nav-link active">Taguri</a>
                    <a href="/about" class="nav-link">Despre</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <div class="tags-hero">
                <div class="tags-hero-content">
                    <svg class="hero-icon" width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="7" cy="7" r="1.5" fill="currentColor"/>
                    </svg>
                    <h1 class="tags-hero-title">Explorează Tagurile</h1>
                    <p class="tags-hero-subtitle">Descoperă știrile organizate după subiecte și teme de interes</p>
                </div>
            </div>
            
            <?php if (empty($popularTags)): ?>
                <div class="empty-state-modern">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="7" cy="7" r="1.5" fill="currentColor"/>
                    </svg>
                    <h2>Nu există taguri disponibile</h2>
                    <p>Tagurile vor apărea pe măsură ce articolele sunt adăugate în platformă.</p>
                </div>
            <?php else: ?>
                <!-- Tags Cloud Modern -->
                <div class="tags-section">
                    
                    <div class="tags-grid-modern">
                        <?php 
                        $maxCount = max($popularTags);
                        foreach ($popularTags as $tag => $count): 
                            // Calculate popularity level (1-5)
                            $popularity = ceil(($count / $maxCount) * 5);
                            
                            // Get sample articles for this tag
                            $tagArticles = getArticlesByTag($tag, 1, 5);
                            $articleTitles = array_map(function($a) { return $a['title']; }, $tagArticles);
                            $articlesJson = htmlspecialchars(json_encode($articleTitles), ENT_QUOTES, 'UTF-8');
                        ?>
                            <a href="/?tag=<?php echo urlencode($tag); ?>" 
                               class="tag-card-modern popularity-<?php echo $popularity; ?>"
                               data-tag-name="<?php echo e($tag); ?>"
                               data-tag-count="<?php echo $count; ?>"
                               data-tag-articles='<?php echo $articlesJson; ?>'>
                                <div class="tag-card-header">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="tag-name"><?php echo e($tag); ?></span>
                                    <span class="tag-count-badge"><?php echo $count; ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Tag Preview Tooltip -->
    <div id="tagPreview" class="tag-preview-tooltip">
        <div class="tag-preview-header">
            <h4 class="tag-preview-title"></h4>
            <span class="tag-preview-count"></span>
        </div>
        <div class="tag-preview-articles">
            <ul class="tag-articles-list"></ul>
        </div>
    </div>
    
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
    
    <script src="/assets/js/tags.js"></script>
</body>
</html>


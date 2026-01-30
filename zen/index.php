<?php
/**
 * ZEN Mode - Compact Reading Experience
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/tracker.php';

$siteTitle = getSetting('site_title', SITE_NAME);
$activeTheme = getSetting('active_theme', 'default');

// Get filters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

// Get articles
$articles = getArticles($page, 50, null, $search, 'date'); // 50 articles per page in zen mode
$totalArticles = getArticlesCount(null, $search);
$totalPages = ceil($totalArticles / 50);

// Handle AJAX request
if ($isAjax) {
    ob_start();
    foreach ($articles as $article): 
        $isNew = (time() - strtotime($article['created_at'])) < 86400; // 24 hours
?>
        <div class="zen-article-item"
             data-article-image="<?php echo e($article['featured_image'] ?? ''); ?>"
             data-article-title="<?php echo e($article['title']); ?>"
             data-article-content="<?php echo e($article['content'] ?? ''); ?>">
            <div class="zen-article-meta">
                <span class="zen-source"><?php echo e($article['source_name'] ?? 'Necunoscut'); ?></span>
                <span class="zen-separator">•</span>
                <span class="zen-date"><?php echo timeAgo($article['created_at']); ?></span>
                <?php if ($isNew): ?>
                    <span class="zen-new-badge">NOU</span>
                <?php endif; ?>
            </div>
            <h3 class="zen-article-title">
                <a href="<?php echo e($article['source_url']); ?>" target="_blank" rel="noopener">
                    <?php echo e($article['title']); ?>
                </a>
            </h3>
        </div>
<?php 
    endforeach;
    $html = ob_get_clean();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'html' => $html,
        'total' => $totalArticles,
        'page' => $page,
        'totalPages' => $totalPages
    ]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZEN Mode - <?php echo e($siteTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/themes.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="zen-mode">
    <script>document.documentElement.setAttribute('data-theme', '<?php echo $activeTheme; ?>');</script>
    
    <header class="zen-header">
        <div class="zen-container">
            <div class="zen-header-content">
                <h1 class="zen-title">
                    <span class="zen-icon">🧘</span>
                    ZEN Mode
                </h1>
                <nav class="zen-nav">
                    <button id="zenRefresh" class="zen-refresh-btn" title="Actualizează articolele">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
                        </svg>
                        Actualizează
                    </button>
                    <a href="/" class="zen-nav-link">← Înapoi</a>
                </nav>
            </div>
            
            <!-- Search Bar -->
            <?php if ($search): ?>
                <div class="zen-search-active">
                    <span>Căutare: <strong><?php echo e($search); ?></strong></span>
                    <a href="/zen" class="zen-clear-search">✕ Șterge</a>
                </div>
            <?php else: ?>
                <form method="GET" class="zen-search-form">
                    <input type="text" name="search" placeholder="Caută articole..." class="zen-search-input">
                    <button type="submit" class="zen-search-btn">Căutare</button>
                </form>
            <?php endif; ?>
        </div>
    </header>
    
    <main class="zen-main">
        <div class="zen-container">
            <?php if (empty($articles)): ?>
                <div class="zen-empty">
                    <p>Nu s-au găsit articole</p>
                </div>
            <?php else: ?>
                <div class="zen-articles-list">
                    <?php foreach ($articles as $article): ?>
                        <article class="zen-article-item"
                                 data-article-image="<?php echo e($article['image_url'] ?? ''); ?>"
                                 data-article-content="<?php echo e($article['content'] ?? ''); ?>"
                                 data-article-title="<?php echo e($article['title']); ?>">
                            <div class="zen-article-meta">
                                <span class="zen-source"><?php echo e($article['site_name'] ?? 'N/A'); ?></span>
                                <span class="zen-separator">•</span>
                                <span class="zen-date"><?php echo formatDate($article['published_at']); ?></span>
                            </div>
                            <h2 class="zen-article-title">
                                <?php 
                                // Show NEW badge if article was published in last 15 minutes
                                $publishedTime = strtotime($article['published_at']);
                                $fifteenMinutesAgo = time() - (15 * 60);
                                if ($publishedTime > $fifteenMinutesAgo): 
                                ?>
                                    <span class="zen-badge-new">NOU</span>
                                <?php endif; ?>
                                <a href="<?php echo e($article['source_url']); ?>" target="_blank" rel="noopener">
                                    <?php echo e($article['title']); ?>
                                </a>
                            </h2>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="zen-pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="zen-pagination-btn">← Anterior</a>
                        <?php endif; ?>
                        
                        <span class="zen-pagination-info">
                            Pagina <?php echo $page; ?> din <?php echo $totalPages; ?>
                        </span>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="zen-pagination-btn">Următorul →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
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
    
    <!-- Zen Preview Tooltip -->
    <div id="zenPreview" class="zen-preview-tooltip">
        <div class="zen-preview-image"></div>
        <div class="zen-preview-content">
            <h4 class="zen-preview-title"></h4>
            <p class="zen-preview-text"></p>
        </div>
    </div>
    
    <!-- Success Notification -->
    <div id="zenNotification" class="zen-notification"></div>
    
    <script src="/assets/js/zen.js"></script>
</body>
</html>


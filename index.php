<?php
/**
 * Main News Page
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/tracker.php';

// Get active theme
$activeTheme = getSetting('active_theme', 'default');

// Get filters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$siteFilter = isset($_GET['site']) ? intval($_GET['site']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'date';
$tagFilter = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// Get view mode from cookie/localStorage (will be handled by JS)
$viewMode = isset($_COOKIE['view_mode']) ? $_COOKIE['view_mode'] : 'grid';

// Get top articles (only on first page, not when filtering by tag)
$topArticles = ($page == 1 && !$tagFilter) ? getTopArticles(6) : [];

// Get articles - check if filtering by tag
if ($tagFilter) {
    $articles = getArticlesByTag($tagFilter, $page, ARTICLES_PER_PAGE);
    $totalArticles = count($articles); // Simplified count for tag filter
    $search = ''; // Clear search when using tag filter
} else {
    $articles = getArticles($page, ARTICLES_PER_PAGE, $siteFilter, $search, $sortBy);
    $totalArticles = getArticlesCount($siteFilter, $search);
}
$totalPages = ceil($totalArticles / ARTICLES_PER_PAGE);

// Get news sites for filter
$sites = getNewsSites(true);

$siteTitle = getSetting('site_title', SITE_NAME);

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($siteTitle); ?></title>
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
            <!-- Filters and Search (Hidden - moved to bottom) -->
            <div class="filters-section">
                <form method="GET" class="filters-form" id="filtersForm">
                    <div class="filter-group">
                        <label for="search">Căutare:</label>
                        <input type="text" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Caută știri...">
                    </div>
                    
                    <div class="filter-group">
                        <label for="site">Sursă:</label>
                        <select id="site" name="site">
                            <option value="">Toate sursele</option>
                            <?php foreach ($sites as $site): ?>
                                <option value="<?php echo $site['id']; ?>" <?php echo $siteFilter == $site['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($site['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                    <?php if ($search || $siteFilter): ?>
                        <a href="/" class="btn btn-outline">Resetează</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- View Mode Toggle (Hidden - moved to bottom) -->
            <div class="view-controls hidden">
                <span class="view-label">Vizualizare:</span>
                <button class="view-btn active" data-view="grid" title="Grid">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <rect x="2" y="2" width="7" height="7" rx="1"/>
                        <rect x="11" y="2" width="7" height="7" rx="1"/>
                        <rect x="2" y="11" width="7" height="7" rx="1"/>
                        <rect x="11" y="11" width="7" height="7" rx="1"/>
                    </svg>
                </button>
                <button class="view-btn" data-view="list" title="Listă">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <rect x="2" y="3" width="16" height="2" rx="1"/>
                        <rect x="2" y="9" width="16" height="2" rx="1"/>
                        <rect x="2" y="15" width="16" height="2" rx="1"/>
                    </svg>
                </button>
                <button class="view-btn" data-view="compact" title="Compact">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <rect x="2" y="4" width="16" height="1.5" rx="0.75"/>
                        <rect x="2" y="8" width="12" height="1.5" rx="0.75"/>
                        <rect x="2" y="12" width="16" height="1.5" rx="0.75"/>
                        <rect x="2" y="16" width="10" height="1.5" rx="0.75"/>
                    </svg>
                </button>
            </div>
            
            <!-- Fixed Bottom Controls -->
            <div class="fixed-bottom-controls">
                <form method="GET" class="filters-form" id="filtersFormBottom" style="display: contents;">
                    <div class="filter-group">
                        <input type="text" id="search-bottom" name="search" value="<?php echo e($search); ?>" placeholder="Caută știri..." style="width: 100%;">
                    </div>
                    
                    <div class="filter-group">
                        <select id="site-bottom" name="site" style="width: 100%;">
                            <option value="">Toate sursele</option>
                            <?php foreach ($sites as $site): ?>
                                <option value="<?php echo $site['id']; ?>" <?php echo $siteFilter == $site['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($site['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">Filtrează</button>
                    <?php if ($search || $siteFilter): ?>
                        <a href="/" class="btn btn-outline btn-sm">Resetează</a>
                    <?php endif; ?>
                </form>
                
                <div class="view-controls">
                    <span class="view-label">Vizualizare:</span>
                    <button class="view-btn active" data-view="grid" title="Grid">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="2" width="7" height="7" rx="1"/>
                            <rect x="11" y="2" width="7" height="7" rx="1"/>
                            <rect x="2" y="11" width="7" height="7" rx="1"/>
                            <rect x="11" y="11" width="7" height="7" rx="1"/>
                        </svg>
                    </button>
                    <button class="view-btn" data-view="list" title="Listă">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="3" width="16" height="2" rx="1"/>
                            <rect x="2" y="9" width="16" height="2" rx="1"/>
                            <rect x="2" y="15" width="16" height="2" rx="1"/>
                        </svg>
                    </button>
                    <button class="view-btn" data-view="compact" title="Compact">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="4" width="16" height="1.5" rx="0.75"/>
                            <rect x="2" y="8" width="12" height="1.5" rx="0.75"/>
                            <rect x="2" y="12" width="16" height="1.5" rx="0.75"/>
                            <rect x="2" y="16" width="10" height="1.5" rx="0.75"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Top Știri (only on first page) -->
            <?php if ($page == 1 && !empty($topArticles)): ?>
                <section class="top-news-section">
                    <h2 class="section-title">🔥 Top Știri</h2>
                    <div class="top-news-grid">
                        <?php foreach ($topArticles as $top): ?>
                            <article class="top-news-card">
                                <?php if ($top['image_url']): ?>
                                <div class="top-news-image">
                                    <a href="/article?id=<?php echo $top['id']; ?>">
                                        <img src="<?php echo e($top['image_url']); ?>" 
                                             alt="<?php echo e($top['title']); ?>"
                                             loading="lazy"
                                             onerror="this.parentElement.parentElement.style.display='none';">
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div class="top-news-content">
                                    <div class="top-news-meta">
                                        <span class="article-source"><?php echo e($top['site_name'] ?? 'N/A'); ?></span>
                                        <span class="article-date"><?php echo formatDate($top['published_at']); ?></span>
                                    </div>
                                    <h3 class="top-news-title">
                                        <?php 
                                        // Show NEW badge if article was published in last 15 minutes
                                        $publishedTime = strtotime($top['published_at']);
                                        $fifteenMinutesAgo = time() - (15 * 60);
                                        if ($publishedTime > $fifteenMinutesAgo): 
                                        ?>
                                            <span class="badge-new">NOU</span>
                                        <?php endif; ?>
                                        <a href="/article/<?php echo $top['id']; ?>">
                                            <?php echo e($top['title']); ?>
                                        </a>
                                    </h3>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            
            <!-- Sort and Filter Section -->
            <div class="sort-section">
                <!-- Filters on Mobile (inside sort section) -->
                <div class="filters-section">
                    <form method="GET" class="filters-form" id="filtersFormTop">
                        <div class="filter-group">
                            <label for="search-top">Căutare:</label>
                            <input type="text" id="search-top" name="search" value="<?php echo e($search); ?>" placeholder="Caută știri...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="site-top">Sursă:</label>
                            <select id="site-top" name="site">
                                <option value="">Toate sursele</option>
                                <?php foreach ($sites as $site): ?>
                                    <option value="<?php echo $site['id']; ?>" <?php echo $siteFilter == $site['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($site['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Filtrează</button>
                        <?php if ($search || $siteFilter): ?>
                            <a href="/" class="btn btn-outline">Resetează</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <!-- View Mode Toggle for Mobile -->
                <div class="view-controls-top hidden">
                    <span class="view-label">Vizualizare:</span>
                    <button class="view-btn active" data-view="grid" title="Grid">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="2" width="7" height="7" rx="1"/>
                            <rect x="11" y="2" width="7" height="7" rx="1"/>
                            <rect x="2" y="11" width="7" height="7" rx="1"/>
                            <rect x="11" y="11" width="7" height="7" rx="1"/>
                        </svg>
                    </button>
                    <button class="view-btn" data-view="list" title="Listă">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="3" width="16" height="2" rx="1"/>
                            <rect x="2" y="9" width="16" height="2" rx="1"/>
                            <rect x="2" y="15" width="16" height="2" rx="1"/>
                        </svg>
                    </button>
                    <button class="view-btn" data-view="compact" title="Compact">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                            <rect x="2" y="4" width="16" height="1.5" rx="0.75"/>
                            <rect x="2" y="8" width="12" height="1.5" rx="0.75"/>
                            <rect x="2" y="12" width="16" height="1.5" rx="0.75"/>
                            <rect x="2" y="16" width="10" height="1.5" rx="0.75"/>
                        </svg>
                    </button>
                </div>
                
                <div class="results-info">
                    <p>Găsite <strong><?php echo number_format($totalArticles); ?></strong> articole</p>
                </div>
                <div class="sort-controls">
                    <label for="sort">Sortează după:</label>
                    <select id="sort" name="sort" onchange="window.location.href='?<?php echo http_build_query(array_merge($_GET, ['sort' => ''])); ?>&sort='+this.value">
                        <option value="date" <?php echo $sortBy == 'date' ? 'selected' : ''; ?>>Data (cel mai recent)</option>
                        <option value="site" <?php echo $sortBy == 'site' ? 'selected' : ''; ?>>Sursă</option>
                        <option value="title" <?php echo $sortBy == 'title' ? 'selected' : ''; ?>>Titlu (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <!-- Articles -->
            <?php if (empty($articles)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📰</div>
                    <h2>Nu s-au găsit articole</h2>
                    <p>Încearcă să modifici filtrele sau să revii mai târziu.</p>
                </div>
            <?php else: ?>
                <div class="articles-container" id="articlesContainer" data-view-mode="<?php echo e($viewMode); ?>" style="max-width: 100%;">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-card" data-view-mode="<?php echo e($viewMode); ?>"
                                 data-article-image="<?php echo e($article['image_url'] ?? ''); ?>"
                                 data-article-content="<?php echo e($article['content'] ?? ''); ?>"
                                 data-article-title="<?php echo e($article['title']); ?>">
                            <?php if ($article['image_url']): ?>
                            <div class="article-image">
                                <a href="/article?id=<?php echo $article['id']; ?>">
                                    <img src="<?php echo e($article['image_url']); ?>" 
                                         alt="<?php echo e($article['title']); ?>"
                                         loading="lazy"
                                         onerror="this.parentElement.parentElement.style.display='none';">
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <div class="article-content">
                                <div class="article-meta">
                                    <span class="article-source"><?php echo e($article['site_name'] ?? 'N/A'); ?></span>
                                    <span class="article-date"><?php echo formatDate($article['published_at']); ?></span>
                                </div>
                                
                                <h2 class="article-title">
                                    <?php 
                                    // Show NEW badge if article was published in last 15 minutes
                                    $publishedTime = strtotime($article['published_at']);
                                    $fifteenMinutesAgo = time() - (15 * 60);
                                    if ($publishedTime > $fifteenMinutesAgo): 
                                    ?>
                                        <span class="badge-new">NOU</span>
                                    <?php endif; ?>
                                    <a href="/article/<?php echo $article['id']; ?>">
                                        <?php echo e($article['title']); ?>
                                    </a>
                                </h2>
                                
                                <?php if (!empty($article['content'])): ?>
                                    <p class="article-excerpt"><?php echo e(truncate($article['content'], 100)); ?></p>
                                <?php endif; ?>
                                
                                <div class="article-footer">
                                    <a href="/article/<?php echo $article['id']; ?>" class="btn btn-sm btn-primary">Citește mai mult</a>
                                    <a href="<?php echo e($article['source_url']); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">Sursa originală</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&site=<?php echo $siteFilter; ?>&search=<?php echo urlencode($search); ?>" class="pagination-btn">« Anterior</a>
                        <?php endif; ?>
                        
                        <div class="pagination-info">
                            Pagina <?php echo $page; ?> din <?php echo $totalPages; ?>
                        </div>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&site=<?php echo $siteFilter; ?>&search=<?php echo urlencode($search); ?>" class="pagination-btn">Următor »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Popular Tags Section -->
            <div class="popular-tags-section">
                <h3>Taguri Populare</h3>
                <div class="tags-cloud">
                    <?php 
                    $popularTags = getPopularTags(25);
                    foreach ($popularTags as $tag => $count): 
                    ?>
                        <a href="?tag=<?php echo urlencode($tag); ?>" class="tag-item" data-count="<?php echo $count; ?>">
                            <?php echo e($tag); ?> <span class="tag-count">(<?php echo $count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Compact View Hover Preview -->
    <div id="compactPreview" class="compact-preview-tooltip">
        <div class="preview-image"></div>
        <div class="preview-content">
            <h4 class="preview-title"></h4>
            <p class="preview-text"></p>
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
    
    <script src="/assets/js/main.js"></script>
</body>
</html>

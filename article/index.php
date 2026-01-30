<?php
/**
 * Article Detail Page
 * Supports both /article/123 and /article?id=123 formats
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/tracker.php';

// Extract article ID from URL
$articleId = null;

// Try to get ID from clean URL format: /article/123
if (preg_match('#/article/(\d+)#', $_SERVER['REQUEST_URI'], $matches)) {
    $articleId = (int)$matches[1];
}

// Fallback to query string format: /article?id=123
if (!$articleId && isset($_GET['id'])) {
    $articleId = (int)$_GET['id'];
}

// Redirect if no ID found
if (!$articleId) {
    header('Location: /');
    exit;
}

// Get article from database
$db = getDB();
$stmt = $db->prepare("
    SELECT a.*, s.name as site_name, s.url as site_url
    FROM articles a
    LEFT JOIN news_sites s ON a.site_id = s.id
    WHERE a.id = ?
");
$stmt->execute([$articleId]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// 404 if article not found
if (!$article) {
    header("HTTP/1.0 404 Not Found");
    echo "Articol negăsit";
    exit;
}

// Increment view count
$updateViews = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
$updateViews->execute([$articleId]);

// Get related articles (same site, recent)
$relatedStmt = $db->prepare("
    SELECT id, title, image_url, published_at
    FROM articles
    WHERE site_id = ? AND id != ?
    ORDER BY published_at DESC
    LIMIT 5
");
$relatedStmt->execute([$article['site_id'], $articleId]);
$relatedArticles = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Get article tags
$tags = !empty($article['tags']) ? explode(',', $article['tags']) : [];

// Set page title
$pageTitle = e($article['title']) . ' - ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/themes.css">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo e($article['title']); ?>">
    <meta property="og:type" content="article">
    <?php if ($article['image_url']): ?>
    <meta property="og:image" content="<?php echo e($article['image_url']); ?>">
    <?php endif; ?>
    <meta property="og:description" content="<?php echo e(truncate($article['content'] ?? $article['body'] ?? '', 160)); ?>">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo-link">
                    <h1 class="site-logo"><?php echo SITE_NAME; ?></h1>
                </a>
                <nav class="main-nav">
                    <a href="/" class="nav-link">Acasă</a>
                    <a href="/tags" class="nav-link">Taguri</a>
                    <a href="/about" class="nav-link">Despre</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container article-page-wrapper">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <a href="/" class="breadcrumb-item">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>
                </svg>
                Acasă
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?php echo e(truncate($article['title'], 50)); ?></span>
        </nav>

        <div class="article-layout-modern">
            <!-- Main Article Content -->
            <article class="article-main-content">
                <!-- Article Header with Category Badge -->
                <div class="article-category-badge">
                    <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M11 1a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h6zM5 0a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V3a3 3 0 0 0-3-3H5z"/>
                        <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    <?php echo e($article['site_name']); ?>
                </div>

                <h1 class="article-title-modern"><?php echo e($article['title']); ?></h1>
                
                <div class="article-meta-modern">
                    <div class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                        </svg>
                        <a href="<?php echo e($article['site_url']); ?>" target="_blank" rel="noopener" class="meta-link">
                            <?php echo e($article['site_name']); ?>
                        </a>
                    </div>
                    <div class="meta-separator">•</div>
                    <div class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        <time datetime="<?php echo $article['published_at']; ?>"><?php echo formatDate($article['published_at']); ?></time>
                    </div>
                    <?php 
                    $fifteenMinutesAgo = time() - (15 * 60);
                    $publishedTime = strtotime($article['published_at']);
                    if ($publishedTime > $fifteenMinutesAgo): 
                    ?>
                    <div class="meta-separator">•</div>
                    <div class="meta-item meta-new-badge">
                        NOU
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($tags)): ?>
                <div class="article-tags-modern">
                    <?php foreach ($tags as $tag): ?>
                        <a href="/?tag=<?php echo urlencode(trim($tag)); ?>" class="tag-modern">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M2 2a2 2 0 0 1 2-2h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 3 5.586V4a2 2 0 0 1-2-2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                            </svg>
                            <?php echo e(trim($tag)); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($article['image_url']): ?>
                <div class="article-image-container">
                    <img src="<?php echo e($article['image_url']); ?>" 
                         alt="<?php echo e($article['title']); ?>"
                         class="article-image-modern"
                         loading="lazy"
                         onerror="this.parentElement.style.display='none';">
                </div>
                <?php endif; ?>

                <div class="article-body-modern">
                    <?php 
                    $articleText = $article['content'] ?? $article['body'] ?? '';
                    if ($articleText): 
                    ?>
                        <?php echo nl2br(e($articleText)); ?>
                    <?php else: ?>
                        <div class="article-empty-state">
                            <svg width="64" height="64" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            </svg>
                            <p>Conținutul complet este disponibil pe site-ul sursă.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="article-actions">
                    <a href="<?php echo e($article['source_url'] ?? $article['url'] ?? '#'); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn-modern btn-primary-modern">
                        <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                            <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                        </svg>
                        Citește articolul complet
                    </a>
                    <a href="/" class="btn-modern btn-outline-modern">
                        <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                            <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
                        </svg>
                        Înapoi
                    </a>
                </div>
            </article>

            <!-- Sidebar -->
            <aside class="article-sidebar-modern">
                <?php if (!empty($relatedArticles)): ?>
                    <div class="sidebar-card">
                        <h3 class="sidebar-title">
                            <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                            </svg>
                            Articole similare
                        </h3>
                        <div class="related-articles-list">
                            <?php foreach ($relatedArticles as $related): ?>
                                <a href="/article/<?php echo $related['id']; ?>" class="related-article-card">
                                    <?php if ($related['image_url']): ?>
                                        <div class="related-article-thumb">
                                            <img src="<?php echo e($related['image_url']); ?>" 
                                                 alt="<?php echo e($related['title']); ?>"
                                                 loading="lazy"
                                                 onerror="this.parentElement.style.display='none';">
                                        </div>
                                    <?php endif; ?>
                                    <div class="related-article-info">
                                        <h4><?php echo e(truncate($related['title'], 80)); ?></h4>
                                        <time>
                                            <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                            </svg>
                                            <?php echo formatDate($related['published_at']); ?>
                                        </time>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="sidebar-card">
                    <h3 class="sidebar-title">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.93 4.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                        Navigare rapidă
                    </h3>
                    <nav class="sidebar-nav-modern">
                        <a href="/" class="sidebar-nav-item">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>
                            </svg>
                            Pagina principală
                        </a>
                        <a href="/tags" class="sidebar-nav-item">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M2 2a2 2 0 0 1 2-2h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 3 5.586V4a2 2 0 0 1-2-2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                            </svg>
                            Toate tagurile
                        </a>
                        <a href="/about" class="sidebar-nav-item">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            Despre site
                        </a>
                    </nav>
                </div>
            </aside>
        </div>
    </div>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Toate drepturile rezervate.</p>
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


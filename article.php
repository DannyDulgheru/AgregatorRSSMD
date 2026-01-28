<?php
/**
 * Individual Article Page
 */

require_once __DIR__ . '/includes/functions.php';

$articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$articleId) {
    header('Location: /');
    exit;
}

$article = getArticle($articleId);

if (!$article) {
    header('Location: /');
    exit;
}

// Get other recent articles for sidebar
$db = getDB();
$recentArticles = $db->prepare("SELECT a.*, s.name as site_name 
                               FROM articles a 
                               LEFT JOIN news_sites s ON a.site_id = s.id 
                               WHERE a.id != ? 
                               ORDER BY a.published_at DESC 
                               LIMIT 12");
$recentArticles->execute([$articleId]);
$recentArticles = $recentArticles->fetchAll();

$siteTitle = getSetting('site_title', SITE_NAME);

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($article['title']); ?> - <?php echo e($siteTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1><a href="/"><?php echo e($siteTitle); ?></a></h1>
                    <p class="tagline">Agregator Știri Republica Moldova</p>
                </div>
                <nav class="main-nav">
                    <a href="/" class="nav-link">Acasă</a>
                    <a href="/admin" class="nav-link">Admin</a>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="article-page-container">
            <div class="article-main-content">
                <article class="article-full">
                    <div class="article-header">
                        <div class="article-meta">
                            <span class="article-source"><?php echo e($article['site_name'] ?? 'N/A'); ?></span>
                            <span class="article-date"><?php echo formatDate($article['published_at']); ?></span>
                        </div>
                        <h1 class="article-title-full"><?php echo e($article['title']); ?></h1>
                    </div>
                    
                    <?php if ($article['image_url']): ?>
                        <div class="article-image-full">
                            <img src="<?php echo e($article['image_url']); ?>" 
                                 alt="<?php echo e($article['title']); ?>"
                                 loading="lazy"
                                 onerror="this.style.display='none';">
                        </div>
                    <?php endif; ?>
                    
                    <div class="article-body">
                        <?php if (!empty($article['content'])): ?>
                            <div class="article-content-full">
                                <?php 
                                $content = $article['content'];
                                // Split into paragraphs if it's long
                                $paragraphs = explode("\n", $content);
                                if (count($paragraphs) == 1) {
                                    // Try to split by sentences if no line breaks
                                    $sentences = preg_split('/(?<=[.!?])\s+/', $content);
                                    $paragraphs = array_chunk($sentences, 3);
                                    $paragraphs = array_map(function($chunk) {
                                        return implode(' ', $chunk);
                                    }, $paragraphs);
                                }
                                foreach ($paragraphs as $para) {
                                    if (trim($para)) {
                                        echo '<p>' . nl2br(e(trim($para))) . '</p>';
                                    }
                                }
                                ?>
                            </div>
                        <?php else: ?>
                            <p class="article-content-full">Conținutul complet este disponibil pe site-ul sursă.</p>
                        <?php endif; ?>
                        
                        <div class="article-actions">
                            <a href="<?php echo e($article['source_url']); ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                Citește articolul complet pe <?php echo e($article['site_name']); ?>
                            </a>
                            <a href="/" class="btn btn-outline">Înapoi la știri</a>
                        </div>
                    </div>
                </article>
            </div>
            
            <!-- Sidebar with recent articles -->
            <aside class="article-sidebar">
                <div class="sidebar-section">
                    <h3>Alte Știri</h3>
                    <div class="sidebar-articles">
                        <?php foreach ($recentArticles as $recent): ?>
                            <div class="sidebar-article-item">
                                <?php if ($recent['image_url']): ?>
                                    <div class="sidebar-article-image">
                                        <a href="/article.php?id=<?php echo $recent['id']; ?>">
                                            <img src="<?php echo e($recent['image_url']); ?>" 
                                                 alt="<?php echo e($recent['title']); ?>"
                                                 loading="lazy"
                                                 onerror="this.style.display='none';">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="sidebar-article-content">
                                    <h4>
                                        <a href="/article.php?id=<?php echo $recent['id']; ?>">
                                            <?php echo e(truncate($recent['title'], 80)); ?>
                                        </a>
                                    </h4>
                                    <div class="sidebar-article-meta">
                                        <span><?php echo e($recent['site_name']); ?></span>
                                        <span><?php echo formatDate($recent['published_at'], 'd.m.Y'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </main>
    
    <footer class="main-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e($siteTitle); ?>. Toate drepturile rezervate.</p>
            <p>Agregator de știri din Republica Moldova</p>
        </div>
    </footer>
</body>
</html>

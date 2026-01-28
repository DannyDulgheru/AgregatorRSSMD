<?php
/**
 * Manage Articles
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/scraper.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle delete and scrape all
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'delete') {
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = 'Articolul a fost șters cu succes.';
                    $messageType = 'success';
                }
            }
        } elseif ($action === 'scrape_all') {
            $results = scrapeAllSites();
            $successCount = 0;
            $totalCount = 0;
            foreach ($results as $result) {
                $totalCount++;
                if ($result['result']['success']) {
                    $successCount++;
                }
            }
            $message = "Scraping completat: {$successCount}/{$totalCount} site-uri cu succes.";
            $messageType = $successCount > 0 ? 'success' : 'error';
        }
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$siteFilter = isset($_GET['site']) ? intval($_GET['site']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$articles = getArticles($page, $perPage, $siteFilter, $search);
$totalArticles = getArticlesCount($siteFilter, $search);
$totalPages = ceil($totalArticles / $perPage);

$sites = getNewsSites();

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionare Articole - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-sidebar">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>
        
        <div class="admin-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 style="margin: 0;">Gestionare Articole</h1>
                <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur doriți să rulați scraping pentru toate site-urile active? Acest proces poate dura câteva minute.');">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="scrape_all">
                    <button type="submit" class="btn btn-primary">
                        🔄 Scrape Toate Site-urile
                    </button>
                </form>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo e($message); ?></div>
            <?php endif; ?>
            
            <div class="admin-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="site">Filtrează după sursă:</label>
                        <select id="site" name="site">
                            <option value="">Toate sursele</option>
                            <?php foreach ($sites as $site): ?>
                                <option value="<?php echo $site['id']; ?>" <?php echo $siteFilter == $site['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($site['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="search">Căutare:</label>
                        <input type="text" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Caută în titluri...">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                    <a href="articles.php" class="btn btn-outline">Resetează</a>
                </form>
            </div>
            
            <div class="admin-section">
                <p class="info-text">Total: <?php echo number_format($totalArticles); ?> articole</p>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titlu</th>
                                <th>Sursă</th>
                                <th>Data Publicării</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($articles)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Nu există articole.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e(truncate($article['title'], 80)); ?></strong>
                                            <?php if ($article['image_url']): ?>
                                                <span class="badge badge-info">📷</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($article['site_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($article['published_at']); ?></td>
                                        <td>
                                            <a href="/article.php?id=<?php echo $article['id']; ?>" target="_blank" class="btn btn-sm">Vezi</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur doriți să ștergeți acest articol?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Șterge</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&site=<?php echo $siteFilter; ?>&search=<?php echo urlencode($search); ?>" class="btn">« Anterior</a>
                        <?php endif; ?>
                        
                        <span>Pagina <?php echo $page; ?> din <?php echo $totalPages; ?></span>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&site=<?php echo $siteFilter; ?>&search=<?php echo urlencode($search); ?>" class="btn">Următor »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/scraper.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle scrape all action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'scrape_all') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
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

// Get statistics
$totalArticles = $db->query("SELECT COUNT(*) as count FROM articles")->fetch()['count'];
$totalSites = $db->query("SELECT COUNT(*) as count FROM news_sites")->fetch()['count'];
$activeSites = $db->query("SELECT COUNT(*) as count FROM news_sites WHERE active = 1")->fetch()['count'];
$todayArticles = $db->query("SELECT COUNT(*) as count FROM articles WHERE DATE(scraped_at) = DATE('now')")->fetch()['count'];

// Get recent articles
$recentArticles = $db->query("SELECT a.*, s.name as site_name 
                              FROM articles a 
                              LEFT JOIN news_sites s ON a.site_id = s.id 
                              ORDER BY a.scraped_at DESC 
                              LIMIT 10")->fetchAll();

// Get sites with last scraped info
$sites = $db->query("SELECT * FROM news_sites ORDER BY name ASC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
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
                <h1 style="margin: 0;">Dashboard</h1>
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
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📰</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo number_format($totalArticles); ?></div>
                        <div class="stat-label">Total Articole</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🌐</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo $activeSites; ?>/<?php echo $totalSites; ?></div>
                        <div class="stat-label">Site-uri Active</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <div class="stat-value"><?php echo number_format($todayArticles); ?></div>
                        <div class="stat-label">Articole Astăzi</div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-section">
                <h2>Ultimele Știri</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Titlu</th>
                                <th>Sursă</th>
                                <th>Data</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentArticles)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Nu există articole.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentArticles as $article): ?>
                                    <tr>
                                        <td><?php echo e(truncate($article['title'], 60)); ?></td>
                                        <td><?php echo e($article['site_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($article['scraped_at']); ?></td>
                                        <td>
                                            <a href="/article.php?id=<?php echo $article['id']; ?>" target="_blank" class="btn btn-sm">Vezi</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="dashboard-section">
                <h2>Status Site-uri</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nume</th>
                                <th>Tip</th>
                                <th>Status</th>
                                <th>Ultima Scrapare</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sites as $site): ?>
                                <tr>
                                    <td><?php echo e($site['name']); ?></td>
                                    <td><?php echo strtoupper($site['scraping_type']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $site['active'] ? 'badge-success' : 'badge-inactive'; ?>">
                                            <?php echo $site['active'] ? 'Activ' : 'Inactiv'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $site['last_scraped'] ? formatDate($site['last_scraped']) : 'Niciodată'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Manage News Sites
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/scraper.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = 'Eroare de securitate. Vă rugăm încercați din nou.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $rssUrl = trim($_POST['rss_url'] ?? '');
            $scrapingType = $_POST['scraping_type'] ?? 'rss';
            $active = isset($_POST['active']) ? 1 : 0;
            
            if ($name && $url) {
                $stmt = $db->prepare("INSERT INTO news_sites (name, url, rss_url, scraping_type, active) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $url, $rssUrl ?: null, $scrapingType, $active])) {
                    $message = 'Site-ul a fost adăugat cu succes.';
                    $messageType = 'success';
                } else {
                    $message = 'Eroare la adăugarea site-ului.';
                    $messageType = 'error';
                }
            }
        } elseif ($action === 'edit') {
            $id = $_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $url = trim($_POST['url'] ?? '');
            $rssUrl = trim($_POST['rss_url'] ?? '');
            $scrapingType = $_POST['scraping_type'] ?? 'rss';
            $active = isset($_POST['active']) ? 1 : 0;
            
            if ($id && $name && $url) {
                $stmt = $db->prepare("UPDATE news_sites SET name = ?, url = ?, rss_url = ?, scraping_type = ?, active = ? WHERE id = ?");
                if ($stmt->execute([$name, $url, $rssUrl ?: null, $scrapingType, $active, $id])) {
                    $message = 'Site-ul a fost actualizat cu succes.';
                    $messageType = 'success';
                }
            }
        } elseif ($action === 'delete') {
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $stmt = $db->prepare("DELETE FROM news_sites WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $message = 'Site-ul a fost șters cu succes.';
                    $messageType = 'success';
                }
            }
        } elseif ($action === 'scrape') {
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $result = scrapeSite($id);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
            }
        } elseif ($action === 'scrape_all') {
            $results = scrapeAllSites();
            $successCount = 0;
            $totalCount = 0;
            $messages = [];
            foreach ($results as $result) {
                $totalCount++;
                if ($result['result']['success']) {
                    $successCount++;
                }
                $messages[] = $result['site'] . ': ' . $result['result']['message'];
            }
            $message = "Scraping completat: {$successCount}/{$totalCount} site-uri cu succes. " . implode(' | ', $messages);
            $messageType = $successCount > 0 ? 'success' : 'error';
        }
    }
}

// Get all sites
$sites = getNewsSites();

// Get site for editing
$editSite = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM news_sites WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editSite = $stmt->fetch();
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionare Site-uri - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-sidebar">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>
        
        <div class="admin-content">
            <h1>Gestionare Site-uri Știri</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo e($message); ?></div>
            <?php endif; ?>
            
            <div class="admin-section">
                <h2><?php echo $editSite ? 'Editează Site' : 'Adaugă Site Nou'; ?></h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="<?php echo $editSite ? 'edit' : 'add'; ?>">
                    <?php if ($editSite): ?>
                        <input type="hidden" name="id" value="<?php echo $editSite['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nume Site:</label>
                        <input type="text" id="name" name="name" value="<?php echo e($editSite['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="url">URL Principal:</label>
                        <input type="url" id="url" name="url" value="<?php echo e($editSite['url'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="rss_url">URL RSS Feed (opțional):</label>
                        <input type="url" id="rss_url" name="rss_url" value="<?php echo e($editSite['rss_url'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="scraping_type">Tip Scraping:</label>
                        <select id="scraping_type" name="scraping_type">
                            <option value="rss" <?php echo ($editSite['scraping_type'] ?? 'rss') === 'rss' ? 'selected' : ''; ?>>RSS Feed</option>
                            <option value="html" <?php echo ($editSite['scraping_type'] ?? '') === 'html' ? 'selected' : ''; ?>>HTML Scraping</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="active" <?php echo ($editSite['active'] ?? 1) ? 'checked' : ''; ?>>
                            Activ
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editSite ? 'Actualizează' : 'Adaugă'; ?>
                    </button>
                    <?php if ($editSite): ?>
                        <a href="sites.php" class="btn btn-outline">Anulează</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="admin-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">Lista Site-uri</h2>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur doriți să rulați scraping pentru toate site-urile active? Acest proces poate dura câteva minute.');">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="scrape_all">
                        <button type="submit" class="btn btn-primary" style="font-size: 14px;">
                            🔄 Scrape Toate Site-urile
                        </button>
                    </form>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nume</th>
                                <th>URL</th>
                                <th>Tip</th>
                                <th>Status</th>
                                <th>Ultima Scrapare</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sites)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nu există site-uri.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sites as $site): ?>
                                    <tr>
                                        <td><?php echo e($site['name']); ?></td>
                                        <td><a href="<?php echo e($site['url']); ?>" target="_blank"><?php echo e(truncate($site['url'], 40)); ?></a></td>
                                        <td><?php echo strtoupper($site['scraping_type']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $site['active'] ? 'badge-success' : 'badge-inactive'; ?>">
                                                <?php echo $site['active'] ? 'Activ' : 'Inactiv'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $site['last_scraped'] ? formatDate($site['last_scraped']) : 'Niciodată'; ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur doriți să ștergeți acest site?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="scrape">
                                                <input type="hidden" name="id" value="<?php echo $site['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-info">Scrape</button>
                                            </form>
                                            <a href="?edit=<?php echo $site['id']; ?>" class="btn btn-sm">Editează</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Sigur doriți să ștergeți acest site?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $site['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Șterge</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

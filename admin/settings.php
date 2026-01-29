<?php
/**
 * Platform Settings
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/scraper.php';

requireLogin();

$db = getDB();
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'change_password') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $message = 'Toate câmpurile sunt obligatorii.';
                $messageType = 'error';
            } elseif ($newPassword !== $confirmPassword) {
                $message = 'Parolele noi nu se potrivesc.';
                $messageType = 'error';
            } elseif (strlen($newPassword) < 6) {
                $message = 'Parola nouă trebuie să aibă minim 6 caractere.';
                $messageType = 'error';
            } else {
                $result = changeAdminPassword($currentPassword, $newPassword);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
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
        } else {
            $siteTitle = trim($_POST['site_title'] ?? '');
            $itemsPerPage = intval($_POST['items_per_page'] ?? ARTICLES_PER_PAGE);
            
            setSetting('site_title', $siteTitle);
            setSetting('items_per_page', $itemsPerPage);
            
            $message = 'Setările au fost salvate cu succes.';
            $messageType = 'success';
        }
    }
}

$siteTitle = getSetting('site_title', SITE_NAME);
$itemsPerPage = getSetting('items_per_page', ARTICLES_PER_PAGE);

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setări - Admin</title>
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
                <h1 style="margin: 0;">Setări Platformă</h1>
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
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="save_settings">
                    
                    <div class="form-group">
                        <label for="site_title">Titlu Site:</label>
                        <input type="text" id="site_title" name="site_title" value="<?php echo e($siteTitle); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="items_per_page">Articole pe Pagină:</label>
                        <input type="number" id="items_per_page" name="items_per_page" value="<?php echo $itemsPerPage; ?>" min="5" max="100" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Salvează Setările</button>
                </form>
            </div>
            
            <div class="admin-section">
                <h2>Schimbă Parola</h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label for="current_password">Parola Curentă:</label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Parola Nouă:</label>
                        <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
                        <small style="color: #666;">Minim 6 caractere</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmă Parola Nouă:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" autocomplete="new-password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">🔒 Schimbă Parola</button>
                </form>
            </div>
            
            <div class="admin-section">
                <h2>Informații Sistem</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Versiune PHP:</strong> <?php echo PHP_VERSION; ?>
                    </div>
                    <div class="info-item">
                        <strong>Baza de Date:</strong> SQLite
                    </div>
                    <div class="info-item">
                        <strong>Locație DB:</strong> <?php echo DB_PATH; ?>
                    </div>
                    <div class="info-item">
                        <strong>Interval Scraping:</strong> <?php echo SCRAPE_INTERVAL; ?> secunde (<?php echo SCRAPE_INTERVAL / 60; ?> minute)
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

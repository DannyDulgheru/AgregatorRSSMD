<?php
/**
 * Admin Header
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
?>
<header class="admin-header">
    <div class="admin-header-content">
        <div class="admin-logo">
            <h2><?php echo SITE_NAME; ?></h2>
            <span class="admin-badge">Admin Panel</span>
        </div>
        <div class="admin-user">
            <span>Bună, <strong><?php echo e($_SESSION['admin_username']); ?></strong></span>
            <a href="/admin/logout" class="btn btn-sm btn-outline">Deconectare</a>
        </div>
    </div>
</header>

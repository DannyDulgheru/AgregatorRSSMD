<?php
/**
 * Admin Sidebar Navigation
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="admin-nav">
    <ul>
        <li>
            <a href="/admin" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                <span class="nav-icon">📊</span>
                Dashboard
            </a>
        </li>
        <li>
            <a href="/admin/statistics.php" class="<?php echo $currentPage === 'statistics.php' ? 'active' : ''; ?>">
                <span class="nav-icon">📈</span>
                Statistici
            </a>
        </li>
        <li>
            <a href="/admin/sites.php" class="<?php echo $currentPage === 'sites.php' ? 'active' : ''; ?>">
                <span class="nav-icon">🌐</span>
                Site-uri Știri
            </a>
        </li>
        <li>
            <a href="/admin/articles.php" class="<?php echo $currentPage === 'articles.php' ? 'active' : ''; ?>">
                <span class="nav-icon">📰</span>
                Articole
            </a>
        </li>
        <li>
            <a href="/admin/settings.php" class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
                <span class="nav-icon">⚙️</span>
                Setări
            </a>
        </li>
        <li>
            <a href="/" target="_blank">
                <span class="nav-icon">👁️</span>
                Vezi Site-ul
            </a>
        </li>
    </ul>
</nav>

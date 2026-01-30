<?php
/**
 * Router for PHP Built-in Server
 * This file handles clean URLs when using php -S
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Remove query string
$path = strtok($uri, '?');

// Admin routes
if (preg_match('#^/admin/statistics$#', $path)) {
    require __DIR__ . '/admin/statistics.php';
    exit;
}
if (preg_match('#^/admin/sites$#', $path)) {
    require __DIR__ . '/admin/sites.php';
    exit;
}
if (preg_match('#^/admin/articles$#', $path)) {
    require __DIR__ . '/admin/articles.php';
    exit;
}
if (preg_match('#^/admin/settings$#', $path)) {
    require __DIR__ . '/admin/settings.php';
    exit;
}
if (preg_match('#^/admin/login$#', $path)) {
    require __DIR__ . '/admin/login.php';
    exit;
}
if (preg_match('#^/admin/logout$#', $path)) {
    require __DIR__ . '/admin/logout.php';
    exit;
}
if (preg_match('#^/admin/?$#', $path)) {
    require __DIR__ . '/admin/index.php';
    exit;
}

// Article routes
if (preg_match('#^/article/(\d+)$#', $path, $matches)) {
    $_GET['id'] = $matches[1];
    require __DIR__ . '/article/index.php';
    exit;
}
if (preg_match('#^/article$#', $path)) {
    require __DIR__ . '/article/index.php';
    exit;
}

// Tags page
if (preg_match('#^/tags$#', $path)) {
    require __DIR__ . '/tags/index.php';
    exit;
}

// About page
if (preg_match('#^/about$#', $path)) {
    require __DIR__ . '/about/index.php';
    exit;
}

// Privacy page
if (preg_match('#^/privacy$#', $path)) {
    require __DIR__ . '/privacy/index.php';
    exit;
}

// Terms page
if (preg_match('#^/terms$#', $path)) {
    require __DIR__ . '/terms/index.php';
    exit;
}

// ZEN mode
if (preg_match('#^/zen$#', $path)) {
    require __DIR__ . '/zen/index.php';
    exit;
}

// Sitemap
if (preg_match('#^/sitemap\.xml$#', $path)) {
    require __DIR__ . '/sitemap.xml.php';
    exit;
}

// Homepage
if ($path === '/' || $path === '') {
    require __DIR__ . '/index.php';
    exit;
}

// 404 for everything else
http_response_code(404);
echo "404 Not Found";
exit;

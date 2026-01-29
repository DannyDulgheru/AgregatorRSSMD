<?php
/**
 * Dynamic Sitemap Generator
 * Generates XML sitemap with all clean URLs
 */

require_once __DIR__ . '/includes/functions.php';

// Set XML headers
header('Content-Type: application/xml; charset=utf-8');

// Get database connection
$db = getDB();

// Get all published articles
$stmt = $db->query("
    SELECT id, published_at, updated_at
    FROM articles
    ORDER BY published_at DESC
");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get site domain
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$domain = $protocol . '://' . $_SERVER['HTTP_HOST'];

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc><?php echo $domain; ?>/</loc>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
        <lastmod><?php echo date('c'); ?></lastmod>
    </url>
    
    <!-- Tags Page -->
    <url>
        <loc><?php echo $domain; ?>/tags</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- About Page -->
    <url>
        <loc><?php echo $domain; ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <!-- Articles -->
    <?php foreach ($articles as $article): ?>
    <url>
        <loc><?php echo $domain; ?>/article/<?php echo $article['id']; ?></loc>
        <lastmod><?php echo date('c', strtotime($article['updated_at'] ?? $article['published_at'])); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>

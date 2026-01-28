<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date
 */
function formatDate($date, $format = 'd.m.Y H:i') {
    if (empty($date)) return '';
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Truncate text
 */
function truncate($text, $length = 120) {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Generate unique hash for article
 */
function generateArticleHash($title, $sourceUrl) {
    return md5($title . $sourceUrl);
}

/**
 * Get setting value
 */
function getSetting($key, $default = '') {
    $db = getDB();
    $stmt = $db->prepare("SELECT value FROM settings WHERE key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['value'] : $default;
}

/**
 * Set setting value
 */
function setSetting($key, $value) {
    $db = getDB();
    $stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
    return $stmt->execute([$key, $value]);
}

/**
 * Get all news sites
 */
function getNewsSites($activeOnly = false) {
    $db = getDB();
    $sql = "SELECT * FROM news_sites";
    if ($activeOnly) {
        $sql .= " WHERE active = 1";
    }
    $sql .= " ORDER BY name ASC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get articles with pagination
 */
function getArticles($page = 1, $perPage = ARTICLES_PER_PAGE, $siteId = null, $search = '', $sortBy = 'date') {
    $db = getDB();
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT a.*, s.name as site_name, s.url as site_url 
            FROM articles a 
            LEFT JOIN news_sites s ON a.site_id = s.id 
            WHERE 1=1";
    $params = [];
    
    if ($siteId) {
        $sql .= " AND a.site_id = ?";
        $params[] = $siteId;
    }
    
    if ($search) {
        $sql .= " AND (a.title LIKE ? OR a.content LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    // Sorting
    switch ($sortBy) {
        case 'site':
            $sql .= " ORDER BY s.name ASC, a.published_at DESC";
            break;
        case 'title':
            $sql .= " ORDER BY a.title ASC";
            break;
        case 'date':
        default:
            $sql .= " ORDER BY a.published_at DESC, a.scraped_at DESC";
            break;
    }
    
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get top articles (most recent, with images)
 */
function getTopArticles($limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("SELECT a.*, s.name as site_name, s.url as site_url 
                         FROM articles a 
                         LEFT JOIN news_sites s ON a.site_id = s.id 
                         WHERE a.image_url != '' AND a.image_url IS NOT NULL
                         ORDER BY a.published_at DESC, a.scraped_at DESC 
                         LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get total articles count
 */
function getArticlesCount($siteId = null, $search = '') {
    $db = getDB();
    $sql = "SELECT COUNT(*) as total FROM articles WHERE 1=1";
    $params = [];
    
    if ($siteId) {
        $sql .= " AND site_id = ?";
        $params[] = $siteId;
    }
    
    if ($search) {
        $sql .= " AND (title LIKE ? OR content LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'];
}

/**
 * Get article by ID
 */
function getArticle($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT a.*, s.name as site_name, s.url as site_url 
                         FROM articles a 
                         LEFT JOIN news_sites s ON a.site_id = s.id 
                         WHERE a.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get similar articles
 */
function getSimilarArticles($articleId, $siteId, $limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM articles 
                         WHERE site_id = ? AND id != ? 
                         ORDER BY published_at DESC LIMIT ?");
    $stmt->execute([$siteId, $articleId, $limit]);
    return $stmt->fetchAll();
}

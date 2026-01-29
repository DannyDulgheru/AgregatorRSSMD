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
 * Get top articles (most viewed articles with images)
 */
function getTopArticles($limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("SELECT a.*, s.name as site_name, s.url as site_url 
                         FROM articles a 
                         LEFT JOIN news_sites s ON a.site_id = s.id 
                         WHERE a.image_url != '' AND a.image_url IS NOT NULL
                         ORDER BY a.views DESC, a.published_at DESC 
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

/**
 * Generate tags from article title and content
 */
function generateTags($title, $content = '') {
    // Common Romanian words to exclude
    $stopWords = ['și', 'de', 'la', 'în', 'cu', 'pe', 'din', 'pentru', 'sau', 'a', 'al', 'ale', 'să', 'este', 'sunt', 'cea', 'cel', 'cei', 'cele', 'ca', 'dar', 'mai', 'că', 'an', 'ani', 'după', 'fost', 'fără', 'către', 'dacă', 'între', 'toate', 'tot', 'într', 'cea', 'lui', 'unei', 'unui', 'unei', 'acesta', 'această', 'acestea', 'acest', 'acești'];
    
    // Important keywords/topics that should be preserved
    $importantTopics = [
        'război', 'conflict', 'criză', 'scandal', 'proteste', 'alegeri', 'referendum',
        'guvern', 'parlament', 'președinte', 'premier', 'ministru', 'partid', 'opoziție',
        'economie', 'buget', 'energie', 'gaze', 'electricitate', 'salariu', 'pensie',
        'educație', 'sănătate', 'cultură', 'sport', 'justiție', 'corupție',
        'uniune europeană', 'nato', 'rusia', 'ucraina', 'românia', 'moldova',
        'chișinău', 'bucurești', 'moscova', 'kiev', 'bruxelles', 'washington'
    ];
    
    $tags = [];
    $text = $title . ' ' . mb_substr($content, 0, 300);
    
    // Extract proper nouns (capitalized words/phrases)
    // Look for sequences of 1-3 capitalized words
    preg_match_all('/\b([A-ZĂÂÎȘȚ][a-zăâîșț]+(?:\s+[A-ZĂÂÎȘȚ][a-zăâîșț]+){0,2})\b/u', $text, $matches);
    if (!empty($matches[1])) {
        foreach ($matches[1] as $properNoun) {
            $words = explode(' ', $properNoun);
            // Skip if it's just a stop word capitalized
            $isStopWord = true;
            foreach ($words as $word) {
                if (!in_array(mb_strtolower($word), $stopWords)) {
                    $isStopWord = false;
                    break;
                }
            }
            if (!$isStopWord && mb_strlen($properNoun) >= 4) {
                $tags[$properNoun] = ($tags[$properNoun] ?? 0) + 2; // Higher weight for proper nouns
            }
        }
    }
    
    // Extract important topics
    $textLower = mb_strtolower($text);
    foreach ($importantTopics as $topic) {
        if (mb_stripos($textLower, $topic) !== false) {
            $topicCapitalized = mb_convert_case($topic, MB_CASE_TITLE, 'UTF-8');
            $tags[$topicCapitalized] = ($tags[$topicCapitalized] ?? 0) + 3; // Highest weight
        }
    }
    
    // Extract significant single words (as fallback)
    $words = preg_split('/\s+/', preg_replace('/[^\p{L}\s]/u', ' ', mb_strtolower($text)), -1, PREG_SPLIT_NO_EMPTY);
    foreach ($words as $word) {
        if (mb_strlen($word) >= 5 && !in_array($word, $stopWords)) {
            $wordCapitalized = mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
            $tags[$wordCapitalized] = ($tags[$wordCapitalized] ?? 0) + 1;
        }
    }
    
    // Sort by weight/frequency
    arsort($tags);
    
    // Get top 3-5 tags, prioritizing multi-word tags
    $finalTags = [];
    foreach (array_keys($tags) as $tag) {
        if (count($finalTags) >= 5) break;
        // Avoid single-word duplicates if we have multi-word version
        $isDuplicate = false;
        foreach ($finalTags as $existingTag) {
            if (mb_stripos($existingTag, $tag) !== false || mb_stripos($tag, $existingTag) !== false) {
                if (str_word_count($existingTag) > str_word_count($tag)) {
                    $isDuplicate = true;
                    break;
                }
            }
        }
        if (!$isDuplicate) {
            $finalTags[] = $tag;
        }
    }
    
    return implode(',', array_slice($finalTags, 0, 4));
}

/**
 * Get popular tags
 */
function getPopularTags($limit = 20) {
    $db = getDB();
    $stmt = $db->query("SELECT tags FROM articles WHERE tags IS NOT NULL AND tags != ''");
    $allTags = [];
    
    while ($row = $stmt->fetch()) {
        $tags = explode(',', $row['tags']);
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if ($tag) {
                $allTags[$tag] = ($allTags[$tag] ?? 0) + 1;
            }
        }
    }
    
    arsort($allTags);
    return array_slice($allTags, 0, $limit, true);
}

/**
 * Get articles by tag
 */
function getArticlesByTag($tag, $page = 1, $perPage = ARTICLES_PER_PAGE) {
    $db = getDB();
    $offset = ($page - 1) * $perPage;
    
    $stmt = $db->prepare("SELECT a.*, s.name as site_name, s.url as site_url 
                         FROM articles a 
                         LEFT JOIN news_sites s ON a.site_id = s.id 
                         WHERE a.tags LIKE ?
                         ORDER BY a.published_at DESC 
                         LIMIT ? OFFSET ?");
    $stmt->execute(['%' . $tag . '%', $perPage, $offset]);
    return $stmt->fetchAll();
}


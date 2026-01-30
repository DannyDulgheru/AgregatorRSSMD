<?php
/**
 * Analytics and Statistics Functions
 */

// Ensure database connection is available
if (!function_exists('getDB')) {
    require_once __DIR__ . '/../config/database.php';
}

/**
 * Get or create visitor ID
 */
function getVisitorId() {
    if (isset($_COOKIE['visitor_id'])) {
        return $_COOKIE['visitor_id'];
    }
    
    $visitorId = uniqid('visitor_', true);
    setcookie('visitor_id', $visitorId, time() + (365 * 24 * 60 * 60), '/'); // 1 year
    return $visitorId;
}

/**
 * Get or create session ID
 */
function getSessionId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return session_id();
}

/**
 * Detect device type
 */
function detectDevice($userAgent) {
    if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
        return 'Mobile';
    } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
        return 'Tablet';
    }
    return 'Desktop';
}

/**
 * Detect browser
 */
function detectBrowser($userAgent) {
    if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
    if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
    if (strpos($userAgent, 'Safari') !== false) return 'Safari';
    if (strpos($userAgent, 'Edge') !== false) return 'Edge';
    if (strpos($userAgent, 'Opera') !== false) return 'Opera';
    if (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) return 'Internet Explorer';
    return 'Other';
}

/**
 * Detect OS
 */
function detectOS($userAgent) {
    if (preg_match('/windows/i', $userAgent)) return 'Windows';
    if (preg_match('/macintosh|mac os x/i', $userAgent)) return 'macOS';
    if (preg_match('/linux/i', $userAgent)) return 'Linux';
    if (preg_match('/android/i', $userAgent)) return 'Android';
    if (preg_match('/iphone|ipad|ipod/i', $userAgent)) return 'iOS';
    return 'Other';
}

/**
 * Get country from IP (simplified version)
 */
function getCountryFromIP($ip) {
    // For local IPs
    if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
        return 'Local';
    }
    
    // You can integrate with a GeoIP service here
    // For now, return Moldova as default
    return 'Moldova';
}

/**
 * Track page visit
 */
function trackVisit($pageUrl = null) {
    try {
        $db = getDB();
        
        $visitorId = getVisitorId();
        $sessionId = getSessionId();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $pageUrl = $pageUrl ?? $_SERVER['REQUEST_URI'] ?? '/';
        
        $deviceType = detectDevice($userAgent);
        $browser = detectBrowser($userAgent);
        $os = detectOS($userAgent);
        $country = getCountryFromIP($ip);
        
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        
        // Insert visit
        $stmt = $db->prepare("
            INSERT INTO visits (
                visitor_id, ip_address, user_agent, device_type, browser, os, 
                country, page_url, referrer, visit_date, visit_time, session_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $visitorId, $ip, $userAgent, $deviceType, $browser, $os,
            $country, $pageUrl, $referrer, $today, $now, $sessionId
        ]);
        
        // Update daily stats
        updateDailyStats($today);
        
    } catch (Exception $e) {
        // Silently fail to not break the page
        error_log("Analytics error: " . $e->getMessage());
    }
}

/**
 * Update daily statistics summary
 */
function updateDailyStats($date) {
    try {
        $db = getDB();
        
        // Get stats for the day
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_visits,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                COUNT(*) as total_pageviews
            FROM visits 
            WHERE visit_date = ?
        ");
        $stmt->execute([$date]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Insert or update daily stats
        $stmt = $db->prepare("
            INSERT INTO daily_stats (stat_date, total_visits, unique_visitors, total_pageviews, updated_at)
            VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(stat_date) DO UPDATE SET
                total_visits = excluded.total_visits,
                unique_visitors = excluded.unique_visitors,
                total_pageviews = excluded.total_pageviews,
                updated_at = CURRENT_TIMESTAMP
        ");
        
        $stmt->execute([
            $date,
            $stats['total_visits'],
            $stats['unique_visitors'],
            $stats['total_pageviews']
        ]);
        
    } catch (Exception $e) {
        error_log("Daily stats update error: " . $e->getMessage());
    }
}

/**
 * Get online users count (last 5 minutes)
 */
function getOnlineUsersCount() {
    try {
        $db = getDB();
        $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime('-5 minutes'));
        
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT session_id) as online_users
            FROM visits
            WHERE visit_time >= ?
        ");
        $stmt->execute([$fiveMinutesAgo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['online_users'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Get statistics for a date range
 */
function getStatsByDateRange($startDate, $endDate) {
    try {
        $db = getDB();
        
        $stmt = $db->prepare("
            SELECT 
                visit_date,
                COUNT(*) as total_visits,
                COUNT(DISTINCT visitor_id) as unique_visitors
            FROM visits
            WHERE visit_date BETWEEN ? AND ?
            GROUP BY visit_date
            ORDER BY visit_date ASC
        ");
        $stmt->execute([$startDate, $endDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get device statistics
 */
function getDeviceStats($days = 30) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                device_type,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visits WHERE visit_date >= ?), 2) as percentage
            FROM visits
            WHERE visit_date >= ?
            GROUP BY device_type
            ORDER BY count DESC
        ");
        $stmt->execute([$startDate, $startDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get browser statistics
 */
function getBrowserStats($days = 30) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                browser,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visits WHERE visit_date >= ?), 2) as percentage
            FROM visits
            WHERE visit_date >= ?
            GROUP BY browser
            ORDER BY count DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $startDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get OS statistics
 */
function getOSStats($days = 30) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                os,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visits WHERE visit_date >= ?), 2) as percentage
            FROM visits
            WHERE visit_date >= ?
            GROUP BY os
            ORDER BY count DESC
        ");
        $stmt->execute([$startDate, $startDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get country statistics
 */
function getCountryStats($days = 30) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                country,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visits WHERE visit_date >= ?), 2) as percentage
            FROM visits
            WHERE visit_date >= ?
            GROUP BY country
            ORDER BY count DESC
            LIMIT 10
        ");
        $stmt->execute([$startDate, $startDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get top pages statistics
 */
function getTopPages($days = 30, $limit = 10) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                page_url,
                COUNT(*) as visits,
                COUNT(DISTINCT visitor_id) as unique_visitors
            FROM visits
            WHERE visit_date >= ?
            GROUP BY page_url
            ORDER BY visits DESC
            LIMIT ?
        ");
        $stmt->execute([$startDate, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Get today's statistics
 */
function getTodayStats() {
    try {
        $db = getDB();
        $today = date('Y-m-d');
        
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_visits,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                COUNT(DISTINCT session_id) as sessions
            FROM visits
            WHERE visit_date = ?
        ");
        $stmt->execute([$today]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ['total_visits' => 0, 'unique_visitors' => 0, 'sessions' => 0];
    }
}

/**
 * Get monthly statistics
 */
function getMonthlyStats() {
    try {
        $db = getDB();
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');
        
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_visits,
                COUNT(DISTINCT visitor_id) as unique_visitors,
                COUNT(DISTINCT session_id) as sessions
            FROM visits
            WHERE visit_date BETWEEN ? AND ?
        ");
        $stmt->execute([$startOfMonth, $endOfMonth]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ['total_visits' => 0, 'unique_visitors' => 0, 'sessions' => 0];
    }
}

/**
 * Get referrer statistics
 */
function getReferrerStats($days = 30, $limit = 10) {
    try {
        $db = getDB();
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $stmt = $db->prepare("
            SELECT 
                CASE 
                    WHEN referrer = '' THEN 'Direct'
                    ELSE referrer
                END as referrer_source,
                COUNT(*) as count
            FROM visits
            WHERE visit_date >= ?
            GROUP BY referrer_source
            ORDER BY count DESC
            LIMIT ?
        ");
        $stmt->execute([$startDate, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

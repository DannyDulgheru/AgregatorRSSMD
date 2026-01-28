<?php
/**
 * General Configuration
 */

// Site settings
define('SITE_NAME', 'Agregator Știri Moldova');
define('SITE_URL', 'http://localhost');
define('TIMEZONE', 'Europe/Chisinau');

// Scraping settings
define('SCRAPE_INTERVAL', 300); // 5 minutes in seconds
define('USER_AGENT', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
define('REQUEST_TIMEOUT', 30);

// Pagination
define('ARTICLES_PER_PAGE', 20);

// Admin settings
define('ADMIN_SESSION_NAME', 'news_admin_session');

// Set timezone
date_default_timezone_set(TIMEZONE);

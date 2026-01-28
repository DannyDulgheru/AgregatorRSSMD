<?php
/**
 * Cron Job Script for Scraping News
 * Run this script every 5 minutes via cron or scheduled task
 * 
 * Cron example (Linux):
 * */5 * * * * /usr/bin/php /path/to/cron/scrape.php
 * 
 * Windows Task Scheduler:
 * Create a task that runs every 5 minutes
 */

// Prevent web access
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line.\n");
}

require_once __DIR__ . '/../includes/scraper.php';
require_once __DIR__ . '/../config/config.php';

echo "[" . date('Y-m-d H:i:s') . "] Starting news scraping...\n";

$results = scrapeAllSites();

foreach ($results as $result) {
    $status = $result['result']['success'] ? 'SUCCESS' : 'ERROR';
    echo "[" . date('Y-m-d H:i:s') . "] {$result['site']}: {$status} - {$result['result']['message']}\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Scraping completed.\n";

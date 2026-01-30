<?php
/**
 * Analytics Tracker - Include this in public pages
 */

// Only track non-admin pages
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
if (strpos($currentPath, '/admin') === false) {
    require_once __DIR__ . '/analytics.php';
    trackVisit();
}

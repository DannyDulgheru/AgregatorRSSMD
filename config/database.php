<?php
/**
 * Database Configuration and Connection
 */

define('DB_PATH', __DIR__ . '/../data/newsdb.sqlite');
define('DB_DIR', __DIR__ . '/../data');

// Create data directory if it doesn't exist
if (!file_exists(DB_DIR)) {
    mkdir(DB_DIR, 0755, true);
}

/**
 * Get database connection
 */
function getDB() {
    try {
        $db = new PDO('sqlite:' . DB_PATH);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

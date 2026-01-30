<?php
/**
 * Create statistics tables
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    // Create visits table
    $db->exec("CREATE TABLE IF NOT EXISTS visits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        visitor_id TEXT NOT NULL,
        ip_address TEXT,
        user_agent TEXT,
        device_type TEXT,
        browser TEXT,
        os TEXT,
        country TEXT,
        city TEXT,
        page_url TEXT,
        referrer TEXT,
        visit_date DATE NOT NULL,
        visit_time DATETIME NOT NULL,
        session_id TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create indexes for better performance
    $db->exec("CREATE INDEX IF NOT EXISTS idx_visits_date ON visits(visit_date)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_visits_visitor ON visits(visitor_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_visits_session ON visits(session_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_visits_page ON visits(page_url)");
    
    // Create daily stats summary table for faster queries
    $db->exec("CREATE TABLE IF NOT EXISTS daily_stats (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        stat_date DATE NOT NULL UNIQUE,
        total_visits INTEGER DEFAULT 0,
        unique_visitors INTEGER DEFAULT 0,
        total_pageviews INTEGER DEFAULT 0,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "✓ Statistics tables created successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error creating tables: " . $e->getMessage() . "\n";
}

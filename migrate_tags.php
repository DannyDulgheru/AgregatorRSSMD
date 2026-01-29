<?php
/**
 * Migration Script - Add Tags Column
 */

require_once __DIR__ . '/config/database.php';

echo "Adding tags column to articles table...\n";

$db = getDB();

try {
    // Check if column exists
    $columns = $db->query("PRAGMA table_info(articles)")->fetchAll();
    $hasTagsColumn = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'tags') {
            $hasTagsColumn = true;
            break;
        }
    }
    
    if (!$hasTagsColumn) {
        $db->exec("ALTER TABLE articles ADD COLUMN tags TEXT");
        echo "✓ Tags column added successfully!\n";
    } else {
        echo "✓ Tags column already exists.\n";
    }
    
    echo "\nMigration complete!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

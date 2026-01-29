<?php
/**
 * Generate tags for existing articles
 */

require_once __DIR__ . '/includes/functions.php';

echo "Generating tags for existing articles...\n";

$db = getDB();

// Get all articles without tags
$stmt = $db->query("SELECT id, title, content FROM articles WHERE tags IS NULL OR tags = ''");
$articles = $stmt->fetchAll();

$count = 0;
foreach ($articles as $article) {
    $tags = generateTags($article['title'], $article['content']);
    
    $updateStmt = $db->prepare("UPDATE articles SET tags = ? WHERE id = ?");
    $updateStmt->execute([$tags, $article['id']]);
    
    $count++;
    if ($count % 10 == 0) {
        echo "Processed $count articles...\n";
    }
}

echo "\n✓ Generated tags for $count articles!\n";

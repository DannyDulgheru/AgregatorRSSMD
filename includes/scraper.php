<?php
/**
 * News Scraping Engine
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';

/**
 * Fetch URL content with cURL
 */
function fetchUrl($url, $timeout = REQUEST_TIMEOUT) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, USER_AGENT);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: ro-RO,ro;q=0.9,en;q=0.8'
    ]);
    
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode !== 200 || $error) {
        return false;
    }
    
    return $content;
}

/**
 * Parse RSS feed
 */
function parseRSS($rssUrl) {
    $content = fetchUrl($rssUrl);
    if (!$content) {
        return [];
    }
    
    $articles = [];
    try {
        $xml = @simplexml_load_string($content);
        if ($xml === false) {
            return [];
        }
        
        $items = $xml->channel->item ?? [];
        
        foreach ($items as $item) {
            $title = (string)($item->title ?? '');
            $link = (string)($item->link ?? '');
            $description = (string)($item->description ?? '');
            $pubDate = (string)($item->pubDate ?? '');
            $image = '';
            
            // Try to get image from enclosure or media:content
            if (isset($item->enclosure) && isset($item->enclosure['type']) && 
                strpos($item->enclosure['type'], 'image') !== false) {
                $image = (string)$item->enclosure['url'];
            } elseif (isset($item->children('media', true)->content)) {
                $media = $item->children('media', true);
                if (isset($media->content)) {
                    $image = (string)$media->content['url'];
                }
            }
            
            // Try to extract image from description HTML
            if (empty($image) && !empty($description)) {
                preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $description, $matches);
                if (!empty($matches[1])) {
                    $image = $matches[1];
                }
            }
            
            // Clean description HTML
            $description = strip_tags($description);
            $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
            
            // If no image or no content, try to scrape from article page
            if ((empty($image) || empty($description)) && !empty($link)) {
                $articleData = extractArticleData(fetchUrl($link), $link, $title);
                if ($articleData) {
                    if (empty($image) && !empty($articleData['image_url'])) {
                        $image = $articleData['image_url'];
                    }
                    if (empty($description) && !empty($articleData['content'])) {
                        $description = $articleData['content'];
                    }
                }
            }
            
            if (!empty($title) && !empty($link)) {
                // If no image or short content, try to scrape from article page
                $articleContent = '';
                $articleImage = $image;
                if (empty($image) || strlen($description) < 200) {
                    $articleData = extractArticleData(fetchUrl($link), $link, $title);
                    if ($articleData) {
                        if (empty($articleImage) && !empty($articleData['image_url'])) {
                            $articleImage = $articleData['image_url'];
                        }
                        if (strlen($description) < 200 && !empty($articleData['content'])) {
                            $articleContent = $articleData['content'];
                        }
                    }
                }
                
                $finalContent = !empty($articleContent) ? $articleContent : $description;
                
                $articles[] = [
                    'title' => trim($title),
                    'content' => truncate(trim($finalContent), 1500), // Increased to 1500 characters
                    'image_url' => $articleImage,
                    'source_url' => $link,
                    'published_at' => $pubDate ? date('Y-m-d H:i:s', strtotime($pubDate)) : date('Y-m-d H:i:s')
                ];
            }
        }
    } catch (Exception $e) {
        error_log("RSS parsing error: " . $e->getMessage());
        return [];
    }
    
    return $articles;
}

/**
 * Parse HTML page for articles
 */
function parseHTML($url) {
    $content = fetchUrl($url);
    if (!$content) {
        return [];
    }
    
    $articles = [];
    
    try {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        
        // Try to find article links - common patterns
        $articleLinks = [];
        
        // Look for common article link patterns
        $patterns = [
            "//a[contains(@class, 'article')]",
            "//a[contains(@class, 'news')]",
            "//a[contains(@class, 'post')]",
            "//article//a",
            "//h2//a",
            "//h3//a"
        ];
        
        foreach ($patterns as $pattern) {
            $nodes = $xpath->query($pattern);
            if ($nodes->length > 0) {
                foreach ($nodes as $node) {
                    $href = $node->getAttribute('href');
                    if (!empty($href)) {
                        // Make absolute URL
                        if (strpos($href, 'http') !== 0) {
                            $parsedUrl = parse_url($url);
                            $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                            if (strpos($href, '/') === 0) {
                                $href = $baseUrl . $href;
                            } else {
                                $href = $baseUrl . '/' . $href;
                            }
                        }
                        
                        $title = trim($node->textContent);
                        if (!empty($title) && strlen($title) > 10) {
                            $articleLinks[] = [
                                'title' => $title,
                                'url' => $href
                            ];
                        }
                    }
                }
                if (count($articleLinks) > 0) break;
            }
        }
        
        // Limit to first 10 articles to avoid too many requests
        $articleLinks = array_slice($articleLinks, 0, 10);
        
        // Fetch each article page
        foreach ($articleLinks as $link) {
            $articleContent = fetchUrl($link['url']);
            if ($articleContent) {
                $article = extractArticleData($articleContent, $link['url'], $link['title']);
                if ($article) {
                    $articles[] = $article;
                }
            }
            // Small delay to be respectful
            usleep(500000); // 0.5 seconds
        }
        
    } catch (Exception $e) {
        error_log("HTML parsing error: " . $e->getMessage());
    }
    
    return $articles;
}

/**
 * Extract article data from HTML
 */
function extractArticleData($html, $url, $fallbackTitle = '') {
    try {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new DOMXPath($dom);
        
        // Get title - try og:title, then h1, then title tag
        $title = $fallbackTitle;
        $ogTitle = $xpath->query("//meta[@property='og:title']/@content");
        if ($ogTitle->length > 0) {
            $title = $ogTitle->item(0)->nodeValue;
        } else {
            $h1 = $xpath->query("//h1");
            if ($h1->length > 0) {
                $title = trim($h1->item(0)->textContent);
            } else {
                $titleTag = $xpath->query("//title");
                if ($titleTag->length > 0) {
                    $title = trim($titleTag->item(0)->textContent);
                }
            }
        }
        
        // Get image - try og:image, then first img in article
        $image = '';
        $ogImage = $xpath->query("//meta[@property='og:image']/@content");
        if ($ogImage->length > 0) {
            $image = $ogImage->item(0)->nodeValue;
        } else {
            $images = $xpath->query("//article//img | //div[contains(@class, 'content')]//img | //div[contains(@class, 'article')]//img");
            if ($images->length > 0) {
                $image = $images->item(0)->getAttribute('src');
                // Make absolute URL if needed
                if ($image && strpos($image, 'http') !== 0) {
                    $parsedUrl = parse_url($url);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (strpos($image, '/') === 0) {
                        $image = $baseUrl . $image;
                    } else {
                        $image = $baseUrl . '/' . $image;
                    }
                }
            }
        }
        
        // Get content - try meta description, then first paragraph
        $content = '';
        $metaDesc = $xpath->query("//meta[@property='og:description']/@content | //meta[@name='description']/@content");
        if ($metaDesc->length > 0) {
            $content = $metaDesc->item(0)->nodeValue;
        } else {
            $paragraphs = $xpath->query("//article//p | //div[contains(@class, 'content')]//p | //div[contains(@class, 'article')]//p");
            if ($paragraphs->length > 0) {
                $content = trim($paragraphs->item(0)->textContent);
                $content = preg_replace('/\s+/', ' ', $content);
            }
        }
        
        // Get published date
        $publishedAt = date('Y-m-d H:i:s');
        $pubDate = $xpath->query("//meta[@property='article:published_time']/@content | //time/@datetime");
        if ($pubDate->length > 0) {
            $dateStr = $pubDate->item(0)->nodeValue;
            $timestamp = strtotime($dateStr);
            if ($timestamp) {
                $publishedAt = date('Y-m-d H:i:s', $timestamp);
            }
        }
        
        if (empty($title)) {
            return null;
        }
        
        // Get more content - try multiple paragraphs
        if (empty($content) || strlen($content) < 200) {
            $paragraphs = $xpath->query("//article//p | //div[contains(@class, 'content')]//p | //div[contains(@class, 'article')]//p");
            $fullContent = '';
            $paraCount = 0;
            foreach ($paragraphs as $para) {
                $text = trim($para->textContent);
                if (!empty($text) && strlen($text) > 50) {
                    $fullContent .= $text . ' ';
                    $paraCount++;
                    if ($paraCount >= 5) break; // Get up to 5 paragraphs
                }
            }
            if (!empty($fullContent) && strlen($fullContent) > strlen($content)) {
                $content = $fullContent;
            }
        }
        
        return [
            'title' => trim($title),
            'content' => truncate(trim($content), 1500), // Increased to 1500 characters
            'image_url' => $image,
            'source_url' => $url,
            'published_at' => $publishedAt
        ];
        
    } catch (Exception $e) {
        error_log("Article extraction error: " . $e->getMessage());
        return null;
    }
}

/**
 * Scrape news from a site
 */
function scrapeSite($siteId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM news_sites WHERE id = ? AND active = 1");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch();
    
    if (!$site) {
        return ['success' => false, 'message' => 'Site not found or inactive'];
    }
    
    $articles = [];
    $scrapedCount = 0;
    
    try {
        if ($site['scraping_type'] === 'rss' && !empty($site['rss_url'])) {
            $articles = parseRSS($site['rss_url']);
        } else {
            $articles = parseHTML($site['url']);
        }
        
        foreach ($articles as $article) {
            $hash = generateArticleHash($article['title'], $article['source_url']);
            
            // Check if article already exists
            $checkStmt = $db->prepare("SELECT id FROM articles WHERE unique_hash = ?");
            $checkStmt->execute([$hash]);
            if ($checkStmt->fetch()) {
                continue; // Skip duplicates
            }
            
            // Insert article
            $insertStmt = $db->prepare("INSERT INTO articles 
                (site_id, title, image_url, content, source_url, published_at, scraped_at, unique_hash) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $insertStmt->execute([
                $site['id'],
                $article['title'],
                $article['image_url'] ?? '',
                $article['content'] ?? '',
                $article['source_url'],
                $article['published_at'],
                date('Y-m-d H:i:s'),
                $hash
            ]);
            
            $scrapedCount++;
        }
        
        // Update last_scraped
        $updateStmt = $db->prepare("UPDATE news_sites SET last_scraped = ? WHERE id = ?");
        $updateStmt->execute([date('Y-m-d H:i:s'), $site['id']]);
        
        return [
            'success' => true,
            'message' => "Scraped {$scrapedCount} new articles from {$site['name']}",
            'count' => $scrapedCount
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

/**
 * Scrape all active sites
 */
function scrapeAllSites() {
    $sites = getNewsSites(true);
    $results = [];
    
    foreach ($sites as $site) {
        $result = scrapeSite($site['id']);
        $results[] = [
            'site' => $site['name'],
            'result' => $result
        ];
        // Small delay between sites
        sleep(1);
    }
    
    return $results;
}

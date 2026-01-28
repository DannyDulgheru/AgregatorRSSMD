<?php
/**
 * Database Initialization Script
 * Run this once to set up the database and demo data
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';

echo "Initializing database...\n";

$db = getDB();

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS news_sites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    url TEXT NOT NULL,
    rss_url TEXT,
    scraping_type TEXT DEFAULT 'rss',
    active INTEGER DEFAULT 1,
    last_scraped DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    site_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    image_url TEXT,
    content TEXT,
    source_url TEXT NOT NULL,
    published_at DATETIME,
    scraped_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    unique_hash TEXT UNIQUE,
    FOREIGN KEY (site_id) REFERENCES news_sites(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT UNIQUE NOT NULL,
    value TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Create indexes
$db->exec("CREATE INDEX IF NOT EXISTS idx_articles_site_id ON articles(site_id)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_articles_published_at ON articles(published_at)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_articles_unique_hash ON articles(unique_hash)");
$db->exec("CREATE INDEX IF NOT EXISTS idx_news_sites_active ON news_sites(active)");

echo "Tables created.\n";

// Insert demo news sites (Moldova)
$demoSites = [
    // RSS Sites
    [
        'name' => 'Unimedia',
        'url' => 'https://unimedia.info',
        'rss_url' => 'https://unimedia.info/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Jurnal.md',
        'url' => 'https://jurnal.md',
        'rss_url' => 'https://jurnal.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Publika.md',
        'url' => 'https://publika.md',
        'rss_url' => 'https://publika.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Ziarul de Gardă',
        'url' => 'https://www.zdg.md',
        'rss_url' => 'https://www.zdg.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Deschide.md',
        'url' => 'https://deschide.md',
        'rss_url' => 'https://deschide.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'TV8',
        'url' => 'https://tv8.md',
        'rss_url' => 'https://tv8.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'ProTV Chișinău',
        'url' => 'https://protv.md',
        'rss_url' => 'https://protv.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Moldova.org',
        'url' => 'https://moldova.org',
        'rss_url' => 'https://moldova.org/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Agora.md',
        'url' => 'https://agora.md',
        'rss_url' => 'https://agora.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Timpul.md',
        'url' => 'https://timpul.md',
        'rss_url' => 'https://timpul.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'SPUTNIK Moldova',
        'url' => 'https://ru.sputnik.md',
        'rss_url' => 'https://ru.sputnik.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'NOI.md',
        'url' => 'https://noi.md',
        'rss_url' => 'https://noi.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    [
        'name' => 'Moldova1',
        'url' => 'https://moldova1.md',
        'rss_url' => 'https://moldova1.md/rss',
        'scraping_type' => 'rss',
        'active' => 1
    ],
    // HTML Scraping Sites
    [
        'name' => 'Realitatea.md',
        'url' => 'https://realitatea.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldova Suverană',
        'url' => 'https://moldova-suverana.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldpres',
        'url' => 'https://www.moldpres.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'IPN',
        'url' => 'https://www.ipn.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldova Azi',
        'url' => 'https://moldova-azi.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldova Curier',
        'url' => 'https://moldovacurier.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldova Business',
        'url' => 'https://moldovabusiness.md',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ],
    [
        'name' => 'Moldova.org News',
        'url' => 'https://moldova.org/news',
        'rss_url' => '',
        'scraping_type' => 'html',
        'active' => 1
    ]
];

$stmt = $db->prepare("INSERT OR IGNORE INTO news_sites (name, url, rss_url, scraping_type, active) VALUES (?, ?, ?, ?, ?)");

foreach ($demoSites as $site) {
    $stmt->execute([
        $site['name'],
        $site['url'],
        $site['rss_url'],
        $site['scraping_type'],
        $site['active']
    ]);
}

echo "Demo news sites inserted.\n";

// Create admin user (username: admin, password: pass)
$adminUsername = 'admin';
$adminPassword = 'pass';
$passwordHash = password_hash($adminPassword, PASSWORD_BCRYPT);

$stmt = $db->prepare("INSERT OR REPLACE INTO admin_users (username, password_hash) VALUES (?, ?)");
$stmt->execute([$adminUsername, $passwordHash]);

echo "Admin user created:\n";
echo "  Username: admin\n";
echo "  Password: pass\n";

// Insert default settings
$defaultSettings = [
    ['site_title', SITE_NAME],
    ['items_per_page', ARTICLES_PER_PAGE],
    ['scrape_interval', SCRAPE_INTERVAL]
];

$stmt = $db->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");

foreach ($defaultSettings as $setting) {
    $stmt->execute($setting);
}

echo "Default settings inserted.\n";
echo "\nDatabase initialization complete!\n";
echo "You can now access the site and admin panel.\n";

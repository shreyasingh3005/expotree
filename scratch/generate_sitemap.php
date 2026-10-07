<?php
/**
 * XML Sitemap Generator
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$baseUrl = BASE_URL;

// Static pages
$staticPages = [
    '' => ['priority' => '1.0', 'changefreq' => 'daily'],
    '/upcoming-exhibitions.php' => ['priority' => '0.9', 'changefreq' => 'daily'],
    '/book-a-stall.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
    '/list-your-event.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    '/free-shopper-pass.php' => ['priority' => '0.8', 'changefreq' => 'weekly'],
    '/categories.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    '/about-us.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
    '/gallery.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/faq.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/contact.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
    '/privacy-policy.php' => ['priority' => '0.3', 'changefreq' => 'yearly'],
    '/terms.php' => ['priority' => '0.3', 'changefreq' => 'yearly'],
];

$events = $pdo->query("SELECT id, slug, updated_at, start_date FROM events WHERE status IN ('published', 'active') ORDER BY start_date ASC")->fetchAll();

$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

$urlset = $xml->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

foreach ($staticPages as $path => $meta) {
    $url = $xml->createElement('url');
    $loc = $xml->createElement('loc', htmlspecialchars($baseUrl . $path));
    $lastmod = $xml->createElement('lastmod', date('Y-m-d'));
    $changefreq = $xml->createElement('changefreq', $meta['changefreq']);
    $priority = $xml->createElement('priority', $meta['priority']);

    $url->appendChild($loc);
    $url->appendChild($lastmod);
    $url->appendChild($changefreq);
    $url->appendChild($priority);
    $urlset->appendChild($url);
}

foreach ($events as $ev) {
    $url = $xml->createElement('url');
    $evUrl = $baseUrl . '/event.php?id=' . $ev['id'] . '&slug=' . urlencode($ev['slug']);
    $loc = $xml->createElement('loc', htmlspecialchars($evUrl));
    $modDate = !empty($ev['updated_at']) ? date('Y-m-d', strtotime($ev['updated_at'])) : date('Y-m-d');
    $lastmod = $xml->createElement('lastmod', $modDate);
    $changefreq = $xml->createElement('changefreq', 'weekly');
    $priority = $xml->createElement('priority', '0.8');

    $url->appendChild($loc);
    $url->appendChild($lastmod);
    $url->appendChild($changefreq);
    $url->appendChild($priority);
    $urlset->appendChild($url);
}

$xml->appendChild($urlset);
$targetFile = dirname(__DIR__) . '/sitemap.xml';
$xml->save($targetFile);

echo "Sitemap generated successfully at $targetFile with " . (count($staticPages) + count($events)) . " URLs.\n";

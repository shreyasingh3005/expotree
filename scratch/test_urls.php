<?php
$pages = [
    'http://localhost/expotree/',
    'http://localhost/expotree/upcoming-exhibitions.php',
    'http://localhost/expotree/categories.php',
    'http://localhost/expotree/book-a-stall.php',
    'http://localhost/expotree/gallery.php',
    'http://localhost/expotree/list-your-event.php',
    'http://localhost/expotree/free-shopper-pass.php',
    'http://localhost/expotree/about-us.php',
    'http://localhost/expotree/faq.php',
    'http://localhost/expotree/contact.php',
    'http://localhost/expotree/event.php?slug=lucerna-towers-corporate-lifestyle-carnival-2026-10-13',
    'http://localhost/expotree/admin/login.php',
    'http://localhost/expotree/admin/index.php'
];

foreach ($pages as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "$url => $code\n";
}

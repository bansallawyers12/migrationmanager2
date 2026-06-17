<?php

$dir = dirname(__DIR__) . '/storage/app/templates';
$files = glob($dir . '/Service_Agreement_*.docx');

foreach ($files as $path) {
    $file = basename($path);
    $zip = new ZipArchive();
    $zip->open($path);
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();

    preg_match_all('/<w:p\b[^>]*>.*?<\/w:p>/s', $xml, $paras);
    foreach ($paras[0] as $p) {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $p))));
        if (stripos($text, 'Category:') !== false && stripos($text, 'Subclass:') !== false) {
            $hasTab = str_contains($p, '<w:tab');
            echo "$file => tabs=" . ($hasTab ? 'yes' : 'no') . " | $text\n";
        }
    }
}

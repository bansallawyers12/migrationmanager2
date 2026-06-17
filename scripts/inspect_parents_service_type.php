<?php

$path = dirname(__DIR__) . '/storage/app/templates/Service_Agreement_parents.docx';
$zip = new ZipArchive();
$zip->open($path);
$xml = $zip->getFromName('word/document.xml');
$zip->close();

preg_match_all('/<w:p\b[^>]*>.*?<\/w:p>/s', $xml, $paras);
foreach ($paras[0] as $p) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $p))));
    if ($text === '') {
        continue;
    }
    if (stripos($text, 'Category') !== false || stripos($text, 'Subclass') !== false || stripos($text, 'Service Type') !== false) {
        echo "TEXT: {$text}\n";
        if (preg_match('/<w:jc w:val="([^"]+)"/', $p, $m)) {
            echo "  jc={$m[1]}\n";
        }
        if (preg_match('/<w:tab/', $p)) {
            echo "  has tab\n";
        }
        echo substr($p, 0, 1200) . "\n---\n";
    }
}

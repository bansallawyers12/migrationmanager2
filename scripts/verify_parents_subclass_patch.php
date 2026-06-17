<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Services\VisaAgreementServiceTypeRowPatcher;

$path = dirname(__DIR__) . '/storage/app/templates/Service_Agreement_parents.docx';
$zip = new ZipArchive();
$zip->open($path);
$xml = $zip->getFromName('word/document.xml');
$zip->close();

$result = (new VisaAgreementServiceTypeRowPatcher())->patchDocumentXml($xml);
preg_match_all('/<w:p\b.*?<\/w:p>/s', $result['xml'], $paras);
foreach ($paras[0] as $p) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $p))));
    if (str_contains($text, 'Category:') && str_contains($text, 'Subclass:')) {
        echo $text . PHP_EOL;
        echo (preg_match('/w:val="right"/', $p) ? "right tab: yes\n" : "right tab: no\n");
        echo (preg_match('/<w:tab\/>/', $p) ? "tab char: yes\n" : "tab char: no\n");
        echo substr($p, 0, 1800) . PHP_EOL;
        break;
    }
}

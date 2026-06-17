<?php

$path = dirname(__DIR__) . '/storage/app/templates/Service_Agreement_parents.docx';
$zip = new ZipArchive();
$zip->open($path);
$xml = $zip->getFromName('word/document.xml');
$zip->close();

if (preg_match('/<w:sectPr.*?<\/w:sectPr>/s', $xml, $m)) {
    echo "sectPr:\n" . $m[0] . "\n\n";
}

preg_match_all('/<w:tab[^>]*\/>|<w:tab\b[^>]*>/', $xml, $tabs);
echo 'tab count in doc: ' . count($tabs[0]) . "\n";
if (! empty($tabs[0])) {
    print_r(array_slice($tabs[0], 0, 5));
}

preg_match_all('/<w:tabs>.*?<\/w:tabs>/s', $xml, $tabDefs);
echo 'tabs defs: ' . count($tabDefs[0]) . "\n";
foreach (array_slice($tabDefs[0], 0, 3) as $t) {
    echo $t . "\n";
}

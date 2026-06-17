<?php

$dir = __DIR__ . '/../storage/app/templates';
$files = [
    'Service_Agreement_408.docx',
    'Service_Agreement_ART.docx',
    'Service_Agreement_citizenship.docx',
    'Service_Agreement_company_nomination.docx',
    'Service_Agreement_company_sponsorship.docx',
    'Service_Agreement_EOI_ROI.docx',
    'Service_Agreement_general.docx',
    'Service_Agreement_Job_Ready.docx',
    'Service_Agreement_parents.docx',
    'Service_Agreement_PSA.docx',
    'Service_Agreement_Skill_Assessment.docx',
];

foreach ($files as $file) {
    $path = $dir . '/' . $file;
    if (! is_file($path)) {
        echo "MISSING: $file\n\n";
        continue;
    }

    $zip = new ZipArchive();
    $zip->open($path);
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();

    echo "=== $file ===\n";
    $markers = [
        'Block1feesincltax', 'Block2feesincltax', 'Block3feesincltax', 'Blocktotalfeesincltax',
        'GrandTotalFeesAndCosts', 'TotalDoHASurcharges', 'TotalDoHAChargesInclSurcharge',
        'TotalEstimatedOthCosts', 'Relevant Authority Charges', 'Total Professional Fee',
    ];
    foreach ($markers as $m) {
        if (str_contains($xml, $m)) {
            echo "  has $m\n";
        }
    }

    preg_match_all('/<w:tr\b.*?<\/w:tr>/s', $xml, $rows);
    $amountCells = 0;
    $needsPatch = 0;
    foreach ($rows[0] as $row) {
        if (! preg_match_all('/<w:tc>.*?<\/w:tc>/s', $row, $cells) || count($cells[0]) < 2) {
            continue;
        }
        $cell = $cells[0][count($cells[0]) - 1];
        $text = trim(preg_replace('/\s+/', ' ', strip_tags(preg_replace('/<w:t[^>]*>/', '', $cell))));
        if ($text === '' || ! preg_match('/(\$\$\{|^\$[\d,.]+|\$\{)/', $text)) {
            continue;
        }
        $jc = preg_match('/<w:jc w:val="([^"]+)"/', $cell, $m) ? $m[1] : 'none';
        $hasSpaces = (bool) preg_match('/xml:space="preserve">\s+/', $cell);
        $amountCells++;
        if ($jc !== 'right' || $hasSpaces || substr_count($cell, '<w:r>') > 2) {
            $needsPatch++;
            if ($needsPatch <= 5) {
                echo "  patch? jc=$jc spaces=" . ($hasSpaces ? 'yes' : 'no') . " text=" . substr($text, 0, 60) . "\n";
            }
        }
    }
    echo "  amount-like cells: $amountCells, likely need patch: $needsPatch\n\n";
}

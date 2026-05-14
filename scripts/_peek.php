<?php
$z = new ZipArchive();
$z->open(__DIR__ . '/../storage/app/templates/Service_Agreement_general.docx');
$x = $z->getFromName('word/document.xml');
$z->close();
$p = strpos($x, 'Business');
$x = substr($x, $p, 1) . ''; 
$c = substr_count($x, 'Business');
// reopen
$z->open(__DIR__ . '/../storage/app/templates/Service_Agreement_general.docx');
$x = $z->getFromName('word/document.xml');
echo substr_count($x, 'Address');

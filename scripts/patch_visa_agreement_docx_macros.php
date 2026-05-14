<?php

/**
 * Patches visa service agreement .docx files with ${Macro} placeholders for PhpWord TemplateProcessor.
 *
 *   php scripts/patch_visa_agreement_docx_macros.php
 *
 * Close Word / file locks on Windows before running. Uses a temp copy + rename for safer writes.
 */

declare(strict_types=1);

$base = dirname(__DIR__) . '/storage/app/templates';
$targets = array_map(
    static fn (string $f): string => $base . '/' . $f,
    [
        'Service_Agreement_general.docx',
        'Service_Agreement_Skill_Assessment.docx',
        'Service_Agreement_Job_Ready.docx',
        'Service_Agreement_408.docx',
        'Service_Agreement_ART.docx',
        'Service_Agreement_citizenship.docx',
        'Service_Agreement_EOI_ROI.docx',
        'Service_Agreement_parents.docx',
        'Service_Agreement_company_sponsorship.docx',
        'Service_Agreement_company_nomination.docx',
        'Service_Agreement_template_Skill_Assessment.docx',
        'agreement_template-skillassment.docx',
        'agreement_template-JRP.docx',
        'agreement_template-ART.docx',
    ]
);

function tRun(string $text): string
{
    return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
        . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>' . $text . '</w:t></w:r>';
}

function spaceRun(): string
{
    return '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book"/>'
        . '<w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r>';
}

function paragraphIsEmptyPOnly(string $paragraphXml): bool
{
    if (! preg_match('#</w:pPr>(.*)</w:p>\s*$#s', $paragraphXml, $m)) {
        return false;
    }
    $body = $m[1];
    $body = (string) preg_replace('#<w:bookmarkStart[^>]*/?>#', '', $body);
    $body = (string) preg_replace('#<w:bookmarkEnd[^>]*/?>#', '', $body);
    $body = (string) preg_replace('#<w:proofErr[^>]*/>#', '', $body);

    return ! str_contains($body, '<w:r');
}

function paragraphIsInjectTarget(string $paragraphXml): bool
{
    if (paragraphIsEmptyPOnly($paragraphXml)) {
        return true;
    }
    if (preg_match_all('#<w:t[^>]*>([^<]*)</w:t>#', $paragraphXml, $m)) {
        $all = implode('', $m[1]);

        return trim(str_replace("\xc2\xa0", '', $all)) === '';
    }

    return false;
}

/** @return array{0: int, 1: int} */
function primaryClientSectionBounds(string $xml): array
{
    $s = strpos($xml, 'PRIMARY CLIENT DETAILS');
    if ($s === false) {
        throw new RuntimeException('PRIMARY CLIENT DETAILS not found');
    }
    $e = strpos($xml, 'Details of any additional', $s);
    if ($e === false) {
        $e = strpos($xml, 'Registered Migration Agent', $s);
    }
    if ($e === false) {
        throw new RuntimeException('Could not bound primary client section');
    }

    return [$s, $e];
}

function injectFirstEmptyFromOffset(string $xml, int $from, int $maxPos, string $runsXml): string
{
    $slice = substr($xml, $from, max(0, $maxPos - $from));
    if (! preg_match_all('/<w:p\\b[^>]*>.*?<\\/w:p>/s', $slice, $matches, PREG_OFFSET_CAPTURE)) {
        throw new RuntimeException('No paragraphs in inject window');
    }
    foreach ($matches[0] as $full) {
        [$block, $offset] = $full;
        if (paragraphIsInjectTarget((string) $block)) {
            $absolute = $from + (int) $offset;
            $len = strlen((string) $block);
            $innerClose = (int) strrpos((string) $block, '</w:p>');
            $replacement = substr((string) $block, 0, $innerClose) . $runsXml . '</w:p>';

            return substr($xml, 0, $absolute) . $replacement . substr($xml, $absolute + $len);
        }
    }

    throw new RuntimeException('No empty paragraph found in inject window');
}

/**
 * @param  list<string>  $anchors
 */
function injectAfterFirstAnchorInSection(
    string $xml,
    array $anchors,
    int $s,
    int $e,
    string $runsXml,
    ?string $skipIfContains = null
): string {
    if ($skipIfContains !== null && str_contains(substr($xml, $s, $e - $s), $skipIfContains)) {
        return $xml;
    }
    $pos = false;
    $alen = 0;
    foreach ($anchors as $a) {
        $p = strpos($xml, $a, $s);
        if ($p !== false && $p < $e) {
            $pos = $p;
            $alen = strlen($a);
            break;
        }
    }
    if ($pos === false) {
        throw new RuntimeException('No section anchor matched: ' . implode(' | ', $anchors));
    }

    return injectFirstEmptyFromOffset($xml, $pos + $alen, $e, $runsXml);
}

function safePatchDocx(string $path, callable $patchXml): void
{
    $dir = dirname($path);
    $work = $dir . '/~patch_' . basename($path) . '.' . uniqid('', true) . '.docx';
    if (! copy($path, $work)) {
        throw new RuntimeException('Cannot copy to work file: ' . $work);
    }
    $zip = new ZipArchive();
    if ($zip->open($work) !== true) {
        @unlink($work);
        throw new RuntimeException('Cannot open work zip: ' . $work);
    }
    $old = $zip->getFromName('word/document.xml');
    if ($old === false) {
        $zip->close();
        @unlink($work);
        throw new RuntimeException('Missing word/document.xml');
    }
    $new = $patchXml($old);
    $zip->deleteName('word/document.xml');
    $zip->addFromString('word/document.xml', $new);
    $zip->close();
    if (@unlink($path)) {
        if (! @rename($work, $path)) {
            throw new RuntimeException('Cannot rename patched file into place');
        }

        return;
    }
    if (! @copy($work, $path)) {
        $fallback = $path . '.macro-patched.' . uniqid('', true) . '.docx';
        @rename($work, $fallback) || @copy($work, $fallback);
        @unlink($work);

        echo 'LOCKED (replaced by sidecar): ' . basename($path) . ' -> ' . basename($fallback) . PHP_EOL;

        return;
    }
    @unlink($work);
}

function patchDocumentXml(string $xml, string $basename): string
{
    $xml = str_replace('«ClientID»', '${ClientID}', $xml);

    if ($basename === 'Service_Agreement_template_Skill_Assessment.docx') {
        $xml = str_replace(
            '<w:t xml:space="preserve">Client ID: </w:t>',
            '<w:t xml:space="preserve">Client ID: </w:t></w:r>' . tRun('${ClientID}'),
            $xml
        );
    }

    [$s, $e] = primaryClientSectionBounds($xml);

    if (preg_match('/<w:p w14:paraId="68EC93AB"[^>]*><w:pPr>.*?<\\/w:pPr><\\/w:p>/s', $xml)) {
        $xml = preg_replace(
            '/(<w:p w14:paraId="68EC93AB"[^>]*><w:pPr>.*?<\\/w:pPr>)<\\/w:p>/s',
            '$1' . tRun('${ApplicantGivenNames}') . spaceRun() . tRun('${ApplicantSurname}') . '</w:p>',
            $xml,
            1
        );
    }
    [$s, $e] = primaryClientSectionBounds($xml);

    $xml = injectAfterFirstAnchorInSection(
        $xml,
        [
            'Date of Birth</w:t></w:r></w:p></w:tc><w:tc>',
            'Birth:</w:t></w:r></w:p></w:tc><w:tc>',
        ],
        $s,
        $e,
        tRun('${ApplicantDOB}'),
        '${ApplicantDOB}'
    );
    [$s, $e] = primaryClientSectionBounds($xml);

    try {
        $xml = injectAfterFirstAnchorInSection(
            $xml,
            [
                'Residential Address</w:t></w:r></w:p></w:tc><w:tc>',
                'Address:</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="7763"',
                'Address:</w:t></w:r></w:p></w:tc><w:tc><w:tcPr><w:tcW w:w="7506"',
            ],
            $s,
            $e,
            tRun('${ApplicantResidentialAddressStreet1and2}') . spaceRun() . tRun('${ApplicantResidentialAddressPostcode}'),
            '${ApplicantResidentialAddressStreet1and2}'
        );
    } catch (RuntimeException) {
        // Layout differs in some templates; add fields manually in Word if needed.
    }
    [$s, $e] = primaryClientSectionBounds($xml);

    $xml = injectAfterFirstAnchorInSection($xml, ['Mobile:</w:t>'], $s, $e, tRun('${Contact_ContactMobile}'), '${Contact_ContactMobile}');
    [$s, $e] = primaryClientSectionBounds($xml);

    $xml = injectAfterFirstAnchorInSection($xml, ['Email:</w:t>'], $s, $e, tRun('${Contact_ContactEmail}'), '${Contact_ContactEmail}');

    $xml = str_replace('<w:t>ARUN BANSAL</w:t>', '<w:t>${ApplicantGivenNames}</w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book" w:cs="Arial"/><w:b w:val="0"/><w:bCs w:val="0"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t xml:space="preserve"> </w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book" w:cs="Arial"/><w:b w:val="0"/><w:bCs w:val="0"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>${AgentSurName}</w:t>', $xml);

    $xml = str_replace(
        '<w:t xml:space="preserve"> BANSAL Immigration Consultants,</w:t>',
        '<w:t xml:space="preserve"> </w:t></w:r>' . tRun('${AgentTitle},'),
        $xml
    );

    $xml = str_replace('<w:t>MARN: 2418466</w:t>', '<w:t>MARN: </w:t></w:r>' . tRun('${MARN}'), $xml);

    if ($basename === 'Service_Agreement_template_Skill_Assessment.docx') {
        $xml = str_replace(
            '<w:t>Agent:      MARN:</w:t>',
            '<w:t>Agent: </w:t></w:r>'
            . tRun('${AgentName}')
            . spaceRun()
            . tRun('${AgentSurName}')
            . '<w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book" w:cs="Arial"/><w:b w:val="0"/><w:bCs w:val="0"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>MARN: </w:t></w:r>'
            . tRun('${MARN}'),
            $xml
        );
    }

    $xml = str_replace(
        '<w:proofErr w:type="gramStart"/><w:r><w:rPr><w:rFonts w:ascii="Franklin Gothic Book" w:hAnsi="Franklin Gothic Book" w:cs="Arial"/><w:b w:val="0"/><w:bCs w:val="0"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr><w:t>: ,</w:t></w:r><w:proofErr w:type="gramEnd"/>',
        '<w:proofErr w:type="gramStart"/>'
        . tRun('${AgentName}')
        . spaceRun()
        . tRun('${AgentSurName}')
        . '<w:proofErr w:type="gramEnd"/>',
        $xml
    );

    if (substr_count($xml, '${MARN}') < 1) {
        $xml = str_replace(
            '<w:t xml:space="preserve">MARN: </w:t></w:r></w:p>',
            '<w:t xml:space="preserve">MARN: </w:t></w:r>' . tRun('${MARN}') . '</w:p>',
            $xml
        );
        $xml = str_replace('<w:t>MARN: </w:t></w:r></w:p>', '<w:t>MARN: </w:t></w:r>' . tRun('${MARN}') . '</w:p>', $xml);
    }

    return $xml;
}

foreach ($targets as $path) {
    if (! is_file($path)) {
        fwrite(STDERR, "Skip (missing): $path\n");
        continue;
    }
    safePatchDocx($path, function (string $old) use ($path): string {
        return patchDocumentXml($old, basename($path));
    });
    echo 'Patched: ' . basename($path) . "\n";
}

echo "Done.\n";

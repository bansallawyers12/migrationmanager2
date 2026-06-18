<?php

namespace App\Services;

/**
 * Resolves ApplicantResidentialAddress* merge values for visa service agreements.
 *
 * PSA template renders street and postcode as separate placeholders; the legacy
 * {@see client_addresses.address} column often already includes postcode.
 */
class VisaAgreementApplicantAddressResolver
{
    public function supportsSplitStreetPostcode(string $templateFileName): bool
    {
        return $templateFileName === config('visa_agreement_templates.psa', 'Service_Agreement_PSA.docx');
    }

    /**
     * @return array{street: ?string, postcode: ?string}
     */
    public function resolveForTemplate(?object $row, string $templateFileName): array
    {
        if ($row === null) {
            return ['street' => null, 'postcode' => null];
        }

        $postcode = $this->normalizePostcode($row->zip ?? null);

        if (! $this->supportsSplitStreetPostcode($templateFileName)) {
            $street = trim((string) ($row->address ?? ''));

            return [
                'street' => $street !== '' ? $street : null,
                'postcode' => $postcode,
            ];
        }

        $street = $this->buildStreetWithoutPostcode($row);

        return [
            'street' => $street !== '' ? $street : null,
            'postcode' => $postcode,
        ];
    }

    private function buildStreetWithoutPostcode(object $row): string
    {
        $line1 = trim((string) ($row->address_line_1 ?? ''));
        $line2 = trim((string) ($row->address_line_2 ?? ''));
        $postcode = $this->normalizePostcode($row->zip ?? null);

        if ($line1 !== '' || $line2 !== '') {
            $parts = array_filter([
                $line1,
                $line2,
                trim((string) ($row->suburb ?? '')),
                trim((string) ($row->state ?? '')),
            ], static fn (string $part): bool => $part !== '');

            return implode(', ', $parts);
        }

        $legacy = trim((string) ($row->address ?? ''));
        if ($legacy === '') {
            return '';
        }

        if ($postcode === null) {
            return $legacy;
        }

        $suffix = ', '.$postcode;
        if (str_ends_with($legacy, $suffix)) {
            return substr($legacy, 0, -strlen($suffix));
        }

        return $legacy;
    }

    private function normalizePostcode(mixed $zip): ?string
    {
        $postcode = trim((string) ($zip ?? ''));

        return $postcode !== '' ? $postcode : null;
    }
}

<?php

namespace Tests\Unit;

use App\Models\Admin;
use App\Models\Company;
use App\Support\DocumentStoredFilename;
use Tests\TestCase;

class DocumentStoredFilenameTest extends TestCase
{
    public function test_individual_uses_sanitized_first_name(): void
    {
        $admin = new Admin([
            'first_name' => 'Jane',
            'is_company' => false,
        ]);

        $this->assertSame('Jane', DocumentStoredFilename::storedNamePrefix($admin, 'Jane'));
    }

    public function test_individual_empty_fallback_segment_uses_client(): void
    {
        $admin = new Admin([
            'first_name' => 'x',
            'is_company' => false,
        ]);

        $this->assertSame('client', DocumentStoredFilename::storedNamePrefix($admin, ''));
    }

    public function test_company_uses_company_name_when_present(): void
    {
        $admin = new Admin([
            'first_name' => 'ContactFirst',
            'is_company' => true,
        ]);
        $admin->setRelation('company', new Company([
            'company_name' => 'Acme & Co Pty Ltd',
        ]));

        $this->assertSame(
            'Acme___Co_Pty_Ltd',
            DocumentStoredFilename::storedNamePrefix($admin, 'ContactFirst')
        );
    }

    public function test_company_without_company_name_returns_company_literal(): void
    {
        $admin = new Admin([
            'first_name' => 'ContactFirst',
            'is_company' => true,
        ]);
        $admin->setRelation('company', new Company(['company_name' => '']));

        $this->assertSame('company', DocumentStoredFilename::storedNamePrefix($admin, 'ignored'));
    }

    public function test_null_admin_uses_sanitized_first_argument(): void
    {
        $this->assertSame('Pat', DocumentStoredFilename::storedNamePrefix(null, 'Pat'));
    }
}

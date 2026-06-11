<?php

namespace Tests\Unit\Services;

use App\Models\Admin;
use App\Models\Company;
use App\Models\CompanyDirector;
use App\Services\CompanyVisaAgreementMacroBuilder;
use Tests\TestCase;

class CompanyVisaAgreementMacroBuilderTest extends TestCase
{
    private CompanyVisaAgreementMacroBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new CompanyVisaAgreementMacroBuilder();
    }

    public function test_non_company_client_returns_empty_array(): void
    {
        $client = new Admin(['is_company' => false]);

        $this->assertSame([], $this->builder->build($client, null));
    }

    public function test_company_client_populates_profile_contact_and_cost_macros(): void
    {
        $contact = new Admin([
            'first_name' => 'David',
            'last_name' => 'Bongiorno',
            'phone' => '0400111222',
            'email' => 'david@example.com',
        ]);

        $director = new CompanyDirector([
            'director_name' => 'Jane Director',
            'director_dob' => '1980-05-21',
            'is_primary' => true,
        ]);

        $company = new Company([
            'company_name' => 'Alba Cheese Manufacturing Pty Ltd',
            'ABN_number' => '12 345 678 901',
        ]);
        $company->setRelation('directors', collect([$director]));
        $company->setRelation('contactPerson', $contact);

        $client = new Admin(['is_company' => true]);
        $client->setRelation('company', $company);

        $costRow = (object) [
            'surcharge' => 'Yes',
            'saf_levy' => '1000',
            'Dept_Nomination_Application_Charge' => '540',
            'Dept_Sponsorship_Application_Charge' => '420',
        ];

        $macros = $this->builder->build($client, $costRow);

        $this->assertSame('Alba Cheese Manufacturing Pty Ltd', $macros['CompanyName']);
        $this->assertSame('12 345 678 901', $macros['CompanyABN']);
        $this->assertSame('Jane', $macros['DirectorGivenname']);
        $this->assertSame('Director', $macros['DirectorSurname']);
        $this->assertSame('21/05/1980', $macros['DirectorDOB']);
        $this->assertSame('0400111222', $macros['PrimaryContact_ContactMobile']);
        $this->assertSame('david@example.com', $macros['PrimaryContact_ContactEmail']);
        $this->assertSame('1000.00', $macros['SAFLevycharge']);
        $this->assertSame('1014.00', $macros['SAFLevysurcharge']);
        $this->assertSame('540.00', $macros['DoHANominationApplicantCharge']);
        $this->assertSame('547.56', $macros['DoHANominationApplicantSurcharge']);
        $this->assertSame('420.00', $macros['DoHASponsorshipApplicantCharge']);
        $this->assertSame('425.88', $macros['DoHASponsorshipApplicantSurcharge']);
    }

    public function test_linked_director_client_names_and_dob_are_used(): void
    {
        $directorClient = new Admin([
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);
        $directorClient->setAttribute('dob', '1975-12-01');

        $director = new CompanyDirector([
            'director_name' => null,
            'director_dob' => null,
            'is_primary' => true,
        ]);
        $director->setRelation('directorClient', $directorClient);

        $company = new Company(['company_name' => 'Test Co']);
        $company->setRelation('directors', collect([$director]));
        $company->setRelation('contactPerson', null);

        $client = new Admin(['is_company' => true]);
        $client->setRelation('company', $company);

        $macros = $this->builder->build($client, null);

        $this->assertSame('John', $macros['DirectorGivenname']);
        $this->assertSame('Smith', $macros['DirectorSurname']);
        $this->assertSame('01/12/1975', $macros['DirectorDOB']);
        $this->assertSame('0.00', $macros['SAFLevycharge']);
    }

    public function test_surcharge_no_uses_base_amounts_only(): void
    {
        $company = new Company(['company_name' => 'Test Co']);
        $company->setRelation('directors', collect());
        $company->setRelation('contactPerson', null);

        $client = new Admin(['is_company' => true]);
        $client->setRelation('company', $company);

        $costRow = (object) [
            'surcharge' => 'No',
            'saf_levy' => '500',
            'Dept_Nomination_Application_Charge' => '100',
            'Dept_Sponsorship_Application_Charge' => '200',
        ];

        $macros = $this->builder->build($client, $costRow);

        $this->assertSame('500.00', $macros['SAFLevycharge']);
        $this->assertSame('500.00', $macros['SAFLevysurcharge']);
        $this->assertSame('100.00', $macros['DoHANominationApplicantSurcharge']);
        $this->assertSame('200.00', $macros['DoHASponsorshipApplicantSurcharge']);
    }
}

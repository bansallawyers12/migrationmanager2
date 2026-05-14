<?php

namespace Tests\Unit\Services;

use App\Services\VisaAgreementTemplateResolver;
use Tests\TestCase;

class VisaAgreementTemplateResolverTest extends TestCase
{
    private VisaAgreementTemplateResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new VisaAgreementTemplateResolver();
    }

    public function test_company_client_without_nomination_or_sponsorship_hints_uses_sponsorship_first_chain(): void
    {
        $r = $this->resolver->determineCandidates(true, 'gn', 'General', false);
        $this->assertSame('company_default_sponsorship_first', $r['rule']);
        $this->assertSame(
            [
                'Service_Agreement_company_sponsorship.docx',
                'Service_Agreement_company_nomination.docx',
                'Service_Agreement_general.docx',
            ],
            $r['candidates']
        );
    }

    public function test_company_client_nomination_hint_orders_nomination_first(): void
    {
        $r = $this->resolver->determineCandidates(true, 'gn', 'Employer nomination pathway', false);
        $this->assertSame('company_nomination', $r['rule']);
        $this->assertSame('Service_Agreement_company_nomination.docx', $r['candidates'][0]);
        $this->assertSame('Service_Agreement_company_sponsorship.docx', $r['candidates'][1]);
    }

    public function test_company_client_with_nomination_and_employer_subclass_prefers_nomination_template_first(): void
    {
        $r = $this->resolver->determineCandidates(true, 'gn', 'Employer nomination subclass 186', false);
        $this->assertSame('company_nomination_and_sponsorship_hint', $r['rule']);
        $this->assertSame('Service_Agreement_company_nomination.docx', $r['candidates'][0]);
    }

    public function test_company_client_sponsorship_hint_orders_sponsorship_first(): void
    {
        $r = $this->resolver->determineCandidates(true, 'es', 'TSS sponsorship 482', false);
        $this->assertSame('company_sponsorship', $r['rule']);
        $this->assertSame('Service_Agreement_company_sponsorship.docx', $r['candidates'][0]);
    }

    public function test_art_matter_uses_art_template_and_legacy_tail(): void
    {
        $r = $this->resolver->determineCandidates(false, 'art', 'ART', false);
        $this->assertSame('art', $r['rule']);
        $this->assertSame(
            [
                'Service_Agreement_ART.docx',
                'agreement_template-ART.docx',
                'Service_Agreement_general.docx',
            ],
            $r['candidates']
        );
    }

    public function test_skill_nick_maps_to_skill_template_chain(): void
    {
        $r = $this->resolver->determineCandidates(false, 'skillassessment', 'X', false);
        $this->assertSame('skill_assessment', $r['rule']);
        $this->assertSame('Service_Agreement_Skill_Assessment.docx', $r['candidates'][0]);
        $this->assertSame('Service_Agreement_general.docx', end($r['candidates']));
    }

    public function test_vetassess_signal_triggers_skill_path(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'General', true);
        $this->assertSame('skill_assessment', $r['rule']);
        $this->assertSame('Service_Agreement_Skill_Assessment.docx', $r['candidates'][0]);
    }

    public function test_job_ready_nick_maps_to_job_ready_chain(): void
    {
        $r = $this->resolver->determineCandidates(false, 'jrp', 'Job Ready Program', false);
        $this->assertSame('job_ready', $r['rule']);
        $this->assertSame('Service_Agreement_Job_Ready.docx', $r['candidates'][0]);
    }

    public function test_partner_subclass_triggers_conflict_chain_starting_with_general(): void
    {
        $r = $this->resolver->determineCandidates(false, 'pv', 'Partner visa subclass 820', false);
        $this->assertSame('conflict_of_interests_visa', $r['rule']);
        $this->assertSame(['Service_Agreement_general.docx'], $r['candidates']);
    }

    public function test_employer_subclass_triggers_conflict(): void
    {
        $r = $this->resolver->determineCandidates(false, 'es', 'Subclass 482 TSS', false);
        $this->assertSame('conflict_of_interests_visa', $r['rule']);
        $this->assertSame('Service_Agreement_general.docx', $r['candidates'][0]);
    }

    public function test_family_sponsored_visitor_triggers_conflict(): void
    {
        $r = $this->resolver->determineCandidates(
            false,
            'vbv',
            '600 - Visitor — Sponsored Family stream',
            false
        );
        $this->assertSame('conflict_of_interests_visa', $r['rule']);
    }

    public function test_generic_non_company_uses_general_then_legacy_fallbacks(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'General skilled', false);
        $this->assertSame('general', $r['rule']);
        $this->assertSame(['Service_Agreement_general.docx'], $r['candidates']);
    }

    public function test_visitor_600_without_family_sponsor_is_not_conflict(): void
    {
        $r = $this->resolver->determineCandidates(false, 'vbv', '600 - Tourist stream', false);
        $this->assertSame('general', $r['rule']);
    }

    public function test_company_overrides_personal_art_branch(): void
    {
        $r = $this->resolver->determineCandidates(true, 'art', 'ART', false);
        $this->assertSame('company_default_sponsorship_first', $r['rule']);
        $this->assertStringContainsString('Service_Agreement_company_sponsorship.docx', $r['candidates'][0]);
    }

    public function test_job_ready_path_takes_priority_over_conflict_subclass_in_title(): void
    {
        $r = $this->resolver->determineCandidates(false, 'jrp', 'JRP / 482 overlap', false);
        $this->assertSame('job_ready', $r['rule']);
    }

    public function test_parents_subclass_in_title_selects_parents_template(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'Contributory parent 143', false);
        $this->assertSame('parents', $r['rule']);
        $this->assertSame('Service_Agreement_parents.docx', $r['candidates'][0]);
    }

    public function test_eoi_and_roi_in_title_selects_eoi_roi_template(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'EOI and ROI for state nomination', false);
        $this->assertSame('eoi_roi', $r['rule']);
        $this->assertSame('Service_Agreement_EOI_ROI.docx', $r['candidates'][0]);
    }

    public function test_citizenship_in_title_selects_citizenship_template(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'Australian citizenship application', false);
        $this->assertSame('citizenship', $r['rule']);
        $this->assertSame('Service_Agreement_citizenship.docx', $r['candidates'][0]);
    }

    public function test_subclass_408_in_title_selects_408_template(): void
    {
        $r = $this->resolver->determineCandidates(false, 'gn', 'Temporary activity subclass 408', false);
        $this->assertSame('subclass_408', $r['rule']);
        $this->assertSame('Service_Agreement_408.docx', $r['candidates'][0]);
    }
}

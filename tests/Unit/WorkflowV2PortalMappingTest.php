<?php

namespace Tests\Unit;

use App\Support\WorkflowV2Display;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkflowV2PortalMappingTest extends TestCase
{
    #[Test]
    public function skill_assessment_stages_map_to_client_portal_copy(): void
    {
        $awaiting = WorkflowV2Display::portalMappingMeta('Awaiting Client Action');
        $this->assertNotNull($awaiting);
        $this->assertSame('Sign your agreement', $awaiting['client_label']);
        $this->assertSame('action', $awaiting['tag']);
        $this->assertSame(12, $awaiting['pct']);
        $this->assertFalse($awaiting['silent']);

        $silent = WorkflowV2Display::portalMappingMeta('Internal Review');
        $this->assertNotNull($silent);
        $this->assertTrue($silent['silent']);
        $this->assertNull($silent['notif_title']);

        $this->assertNull(WorkflowV2Display::portalMappingMeta('Some custom stage'));
        $this->assertNull(WorkflowV2Display::portalMappingForStage('Some custom stage', [
            ['name' => 'Ignored', 'task_type' => 'upload'],
        ]));
    }

    #[Test]
    public function portal_mapping_uses_portal_tasklists_not_staff_checklists(): void
    {
        $mapping = WorkflowV2Display::portalMappingForStage('Documents Review & Completion', [
            ['name' => 'Bank statement', 'task_type' => 'upload', 'description' => 'Last 3 months'],
            ['name' => '  ', 'task_type' => 'sign'],
        ]);

        $this->assertNotNull($mapping);
        $this->assertSame('Documents needed', $mapping['client_label']);
        $this->assertSame('Action required', $mapping['tag_label']);
        $this->assertCount(1, $mapping['tasks']);
        $this->assertSame('Bank statement', $mapping['tasks'][0]['name']);
        $this->assertSame('upload', $mapping['tasks'][0]['task_type']);
        $this->assertSame('Last 3 months', $mapping['tasks'][0]['description']);
    }

    #[Test]
    public function activities_payload_includes_mapping_workflow_payload_does_not(): void
    {
        $stages = collect([
            (object) [
                'id' => 58,
                'name' => 'Awaiting Client Action',
                'sort_order' => 2,
                'is_protected' => false,
            ],
        ]);

        $workflowPayload = WorkflowV2Display::buildStagesPayload(null, $stages, null, false);
        $this->assertArrayNotHasKey('portalMapping', $workflowPayload[0]);

        $activitiesPayload = WorkflowV2Display::buildStagesPayload(null, $stages, null, true, [], [
            58 => [
                ['name' => 'Service agreement', 'task_type' => 'sign', 'description' => null],
            ],
        ]);
        $this->assertArrayHasKey('portalMapping', $activitiesPayload[0]);
        $this->assertSame('Sign your agreement', $activitiesPayload[0]['portalMapping']['client_label']);
        $this->assertSame('Service agreement', $activitiesPayload[0]['portalMapping']['tasks'][0]['name']);
        $this->assertSame('sign', $activitiesPayload[0]['portalMapping']['tasks'][0]['task_type']);
    }
}

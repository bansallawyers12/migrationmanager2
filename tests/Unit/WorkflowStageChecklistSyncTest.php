<?php

namespace Tests\Unit;

use App\Support\WorkflowStageChecklistSync;
use Tests\TestCase;

class WorkflowStageChecklistSyncTest extends TestCase
{
    public function test_portal_documents_hides_auto_seeded_template_rows(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Initial assessment recorded'],
            (object) ['id' => 2, 'user_id' => null, 'cp_checklist_name' => 'Specific checklist sent'],
            (object) ['id' => 3, 'user_id' => 9, 'cp_checklist_name' => 'Staff uploaded pack'],
            (object) ['id' => 4, 'user_id' => null, 'cp_checklist_name' => 'Legacy custom item'],
        ]);

        $visible = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'Initial assessment recorded',
            'Specific checklist sent',
        ]);

        $this->assertSame([3, 4], $visible->pluck('id')->all());
    }

    public function test_portal_documents_keeps_staff_added_row_even_if_name_matches_template(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Cost / service agreement sent'],
            (object) ['id' => 2, 'user_id' => 4, 'cp_checklist_name' => 'Cost / service agreement sent'],
        ]);

        $visible = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'Cost / service agreement sent',
        ]);

        $this->assertSame([2], $visible->pluck('id')->all());
    }

    public function test_portal_documents_is_case_insensitive_for_template_names(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Follow-up date set'],
        ]);

        $visible = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'FOLLOW-UP DATE SET',
        ]);

        $this->assertSame([], $visible->pluck('id')->all());
    }
}

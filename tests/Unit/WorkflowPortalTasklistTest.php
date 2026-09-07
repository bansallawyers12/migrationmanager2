<?php

namespace Tests\Unit;

use App\Enums\ChecklistSource;
use App\Enums\PortalTaskType;
use App\Support\WorkflowPortalTasklistDefaults;
use App\Support\WorkflowStageChecklistSync;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkflowPortalTasklistTest extends TestCase
{
    #[Test]
    public function workflow_11_defaults_use_documents_review_stage_and_task_types(): void
    {
        $defaults = WorkflowPortalTasklistDefaults::byStageName(11);

        Assert::assertArrayHasKey('Documents Review & Completion', $defaults);
        Assert::assertArrayNotHasKey('Documents Review', $defaults);
        Assert::assertSame([], WorkflowPortalTasklistDefaults::byStageName(1));

        $awaiting = collect($defaults['Awaiting Client Action'])->keyBy('name');
        Assert::assertSame(PortalTaskType::Sign->value, $awaiting['Service agreement']['task_type']);
        Assert::assertSame(PortalTaskType::Sign->value, $awaiting['Form 956 — appointment of agent']['task_type']);
        Assert::assertSame(PortalTaskType::Pay->value, $awaiting['Initial payment']['task_type']);

        $docs = collect($defaults['Documents Review & Completion'])->pluck('task_type', 'name')->all();
        Assert::assertSame(PortalTaskType::Upload->value, $docs['Bank statement']);
        Assert::assertSame(PortalTaskType::Upload->value, $docs['Passport bio page']);
        Assert::assertSame(PortalTaskType::Upload->value, $docs['English test result']);

        Assert::assertSame(
            PortalTaskType::Sign->value,
            $defaults['Client Draft Approval'][0]['task_type']
        );
        Assert::assertSame('Draft application', $defaults['Client Draft Approval'][0]['name']);
        Assert::assertArrayNotHasKey('Additional Request, if Any', $defaults);
    }

    #[Test]
    public function portal_task_type_labels_match_client_actions(): void
    {
        Assert::assertSame('Upload', PortalTaskType::Upload->label());
        Assert::assertSame('Review and sign', PortalTaskType::Sign->label());
        Assert::assertSame('Pay', PortalTaskType::Pay->label());
        Assert::assertSame(['upload', 'sign', 'pay'], PortalTaskType::values());
    }

    #[Test]
    public function checklist_source_defaults_to_workflow_and_portal_is_explicit(): void
    {
        Assert::assertSame('workflow', ChecklistSource::Workflow->value);
        Assert::assertSame('portal', ChecklistSource::Portal->value);
        Assert::assertSame(['workflow', 'portal'], ChecklistSource::values());
    }

    #[Test]
    public function backfill_portal_source_is_noop_when_tables_are_missing(): void
    {
        Assert::assertSame(0, WorkflowStageChecklistSync::backfillPortalSource());
        Assert::assertSame(0, WorkflowStageChecklistSync::backfillPortalSource(9671));
    }

    #[Test]
    public function constrain_to_portal_source_is_noop_when_column_is_missing(): void
    {
        $query = DB::table('cp_doc_checklists');
        $constrained = WorkflowStageChecklistSync::constrainToPortalSource($query);

        Assert::assertSame($query, $constrained);
        Assert::assertStringNotContainsString('source', $constrained->toSql());
    }

    #[Test]
    public function documents_tab_shows_portal_defaults_but_hides_staff_templates(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Service agreement signed'],
            (object) ['id' => 2, 'user_id' => null, 'cp_checklist_name' => 'Service agreement'],
            (object) ['id' => 3, 'user_id' => 9, 'cp_checklist_name' => 'Extra passport copy'],
        ]);

        $onDocuments = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'Service agreement signed',
        ]);
        Assert::assertSame([2, 3], $onDocuments->pluck('id')->all());

        $onActivities = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'Service agreement signed',
            'Service agreement',
        ]);
        Assert::assertSame([3], $onActivities->pluck('id')->all());

        $onWorkflow = WorkflowStageChecklistSync::forPortalDocumentsTab($rows, [
            'Service agreement',
        ]);
        Assert::assertSame([1, 3], $onWorkflow->pluck('id')->all());
    }

    #[Test]
    public function client_portal_documents_uses_source_portal_and_keeps_staff_added_rows(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'source' => 'workflow', 'cp_checklist_name' => 'Initial assessment recorded'],
            (object) ['id' => 2, 'user_id' => null, 'source' => 'portal', 'cp_checklist_name' => 'Service agreement'],
            (object) ['id' => 3, 'user_id' => 9, 'source' => 'workflow', 'cp_checklist_name' => 'test55'],
        ]);

        $visible = WorkflowStageChecklistSync::forClientPortalDocuments($rows, [], true);

        Assert::assertSame([2, 3], $visible->pluck('id')->all());
    }

    #[Test]
    public function client_portal_documents_falls_back_to_name_filter_without_source_column(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Initial assessment recorded'],
            (object) ['id' => 2, 'user_id' => null, 'cp_checklist_name' => 'Service agreement'],
        ]);

        $visible = WorkflowStageChecklistSync::forClientPortalDocuments($rows, [
            'Initial assessment recorded',
        ], false);

        Assert::assertSame([2], $visible->pluck('id')->all());
    }

    #[Test]
    public function workflow_tab_keeps_workflow_source_and_hides_portal_source(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'source' => 'workflow', 'cp_checklist_name' => 'Initial assessment recorded'],
            (object) ['id' => 2, 'user_id' => null, 'source' => 'portal', 'cp_checklist_name' => 'Service agreement'],
            (object) ['id' => 3, 'user_id' => 9, 'source' => 'workflow', 'cp_checklist_name' => 'test55'],
            (object) ['id' => 4, 'user_id' => 4, 'source' => 'portal', 'cp_checklist_name' => 'Extra passport copy'],
        ]);

        $visible = WorkflowStageChecklistSync::forWorkflowTabChecklists($rows, ['Service agreement'], true);

        Assert::assertSame([1, 3], $visible->pluck('id')->all());
    }

    #[Test]
    public function workflow_tab_falls_back_to_hiding_portal_template_names(): void
    {
        $rows = collect([
            (object) ['id' => 1, 'user_id' => null, 'cp_checklist_name' => 'Initial assessment recorded'],
            (object) ['id' => 2, 'user_id' => null, 'cp_checklist_name' => 'Service agreement'],
            (object) ['id' => 3, 'user_id' => 9, 'cp_checklist_name' => 'Service agreement'],
        ]);

        $visible = WorkflowStageChecklistSync::forWorkflowTabChecklists($rows, [
            'Service agreement',
        ], false);

        Assert::assertSame([1, 3], $visible->pluck('id')->all());
    }

    #[Test]
    public function admin_stages_page_exposes_client_portal_tasklists_button(): void
    {
        $stages = file_get_contents($this->projectPath('resources/views/AdminConsole/features/workflow/stages-index.blade.php'));
        Assert::assertNotFalse($stages);
        Assert::assertStringContainsString('data-bs-toggle="tooltip"', $stages);
        Assert::assertStringContainsString('title="Portal Tasklists"', $stages);
        Assert::assertStringContainsString('title="Workflow Checklists"', $stages);
        Assert::assertStringContainsString('$portalTasklistCount', $stages);
        Assert::assertStringContainsString('workflow.stagePortalTasklists', $stages);

        $routes = file_get_contents($this->projectPath('routes/adminconsole.php'));
        Assert::assertNotFalse($routes);
        Assert::assertStringContainsString("name('workflow.stagePortalTasklists')", $routes);
    }

    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }
}

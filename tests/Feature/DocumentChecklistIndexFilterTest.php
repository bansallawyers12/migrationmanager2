<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminConsole\DocumentChecklistController;
use App\Models\DocumentChecklist;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentChecklistIndexFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
    }

    #[Test]
    public function index_view_exposes_checklist_name_and_document_type_filters(): void
    {
        $blade = file_get_contents(resource_path('views/AdminConsole/features/documentchecklist/index.blade.php'));

        $this->assertNotFalse($blade);
        $this->assertStringContainsString('Checklist Name', $blade);
        $this->assertStringContainsString('Document Type', $blade);
        $this->assertStringContainsString('name="name"', $blade);
        $this->assertStringContainsString('name="doc_type"', $blade);
        $this->assertStringContainsString('id="checklist_name_filter"', $blade);
        $this->assertStringContainsString('id="doc_type_filter"', $blade);
    }

    #[Test]
    public function index_filters_by_checklist_name(): void
    {
        $this->seedChecklists();

        $view = $this->indexView(['name' => 'Invoice']);
        $lists = $view->getData()['lists'];

        $this->assertSame(1, $view->getData()['totalData']);
        $this->assertSame(['Invoice'], $lists->pluck('name')->all());
    }

    #[Test]
    public function index_filters_by_partial_checklist_name(): void
    {
        $this->seedChecklists();

        $view = $this->indexView(['name' => 'Receipt']);
        $lists = $view->getData()['lists'];

        $this->assertSame(2, $view->getData()['totalData']);
        $this->assertEqualsCanonicalizing(['DIBP Receipt', 'Refund Receipt'], $lists->pluck('name')->all());
    }

    #[Test]
    public function index_filters_by_document_type(): void
    {
        $this->seedChecklists();

        $view = $this->indexView(['doc_type' => '2']);
        $lists = $view->getData()['lists'];

        $this->assertSame(2, $view->getData()['totalData']);
        $this->assertEqualsCanonicalizing(['Statement of Service', 'Refund Receipt'], $lists->pluck('name')->all());
        $this->assertTrue($lists->every(fn (DocumentChecklist $checklist): bool => (int) $checklist->doc_type === 2));
    }

    #[Test]
    public function index_applies_name_and_document_type_filters_together(): void
    {
        $this->seedChecklists();

        $view = $this->indexView([
            'name' => 'Receipt',
            'doc_type' => '2',
        ]);
        $lists = $view->getData()['lists'];

        $this->assertSame(1, $view->getData()['totalData']);
        $this->assertSame(['Refund Receipt'], $lists->pluck('name')->all());
    }

    #[Test]
    public function index_ignores_invalid_document_type_and_blank_name(): void
    {
        $this->seedChecklists();

        $view = $this->indexView([
            'name' => '   ',
            'doc_type' => '99',
        ]);

        $this->assertSame(4, $view->getData()['totalData']);
        $this->assertCount(4, $view->getData()['lists']);
    }

    #[Test]
    public function index_excludes_inactive_checklists_from_filtered_results(): void
    {
        $this->seedChecklists();
        DocumentChecklist::query()->create([
            'name' => 'Hidden Invoice',
            'doc_type' => 1,
            'status' => 0,
        ]);

        $view = $this->indexView(['name' => 'Invoice']);

        $this->assertSame(1, $view->getData()['totalData']);
        $this->assertSame(['Invoice'], $view->getData()['lists']->pluck('name')->all());
    }

    /**
     * @param  array<string, string>  $query
     */
    private function indexView(array $query = []): View
    {
        $request = Request::create('/adminconsole/features/document-checklist', 'GET', $query);
        $this->app->instance('request', $request);

        $view = app(DocumentChecklistController::class)->index($request);

        $this->assertInstanceOf(View::class, $view);
        $this->assertSame('AdminConsole.features.documentchecklist.index', $view->name());

        return $view;
    }

    private function seedChecklists(): void
    {
        DocumentChecklist::query()->create(['name' => 'Invoice', 'doc_type' => 1, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Statement of Service', 'doc_type' => 2, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'DIBP Receipt', 'doc_type' => 3, 'status' => 1]);
        DocumentChecklist::query()->create(['name' => 'Refund Receipt', 'doc_type' => 2, 'status' => 1]);
    }

    private function createSchema(): void
    {
        if (! Schema::hasTable('portal_document_checklists')) {
            Schema::create('portal_document_checklists', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedTinyInteger('doc_type')->nullable();
                $table->unsignedTinyInteger('status')->nullable();
                $table->timestamps();
            });
        }
    }
}

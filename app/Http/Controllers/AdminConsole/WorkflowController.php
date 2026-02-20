<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use App\Models\ClientMatter;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * List workflows (Workflow model).
     */
    public function index(Request $request)
    {
        $query = Workflow::with(['matter', 'stages']);
        $lists = $query->orderBy('name')->paginate(config('constants.limit', 20));
        return view('AdminConsole.features.workflow.workflows-index', compact('lists'));
    }

    /**
     * Create new workflow form.
     */
    public function create(Request $request)
    {
        return view('AdminConsole.features.workflow.workflow-create');
    }

    /**
     * Store new workflow.
     */
    public function storeWorkflow(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'matter_id' => 'nullable|exists:matters,id',
        ]);
        $wf = new Workflow();
        $wf->name = $request->name;
        $wf->matter_id = $request->matter_id ?: null;
        $wf->save();

        // Every workflow must have at least 3 default stages: first, checklist, and last two
        $defaultStages = ['Application Received', 'Checklist', 'Ready to Close', 'File Closed'];
        foreach ($defaultStages as $i => $stageName) {
            $stage = new WorkflowStage();
            $stage->name = $stageName;
            $stage->workflow_id = $wf->id;
            $stage->sort_order = $i + 1;
            $stage->save();
        }

        return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Created Successfully with default stages.');
    }

    /**
     * Edit workflow form.
     */
    public function editWorkflow($id)
    {
        $id = $this->decodeString($id);
        $workflow = Workflow::find($id);
        if (!$workflow) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow not found');
        }
        return view('AdminConsole.features.workflow.workflow-edit', compact('workflow'));
    }

    /**
     * Update workflow.
     */
    public function updateWorkflow(Request $request, $id)
    {
        $id = $this->decodeString($id);
        $workflow = Workflow::find($id);
        if (!$workflow) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow not found');
        }
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'matter_id' => 'nullable|exists:matters,id',
        ]);
        $workflow->name = $request->name;
        $workflow->matter_id = $request->matter_id ?: null;
        $workflow->save();
        return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Updated Successfully');
    }

    /**
     * List stages for a workflow.
     */
    public function stages($id)
    {
        $id = $this->decodeString($id);
        $workflow = Workflow::find($id);
        if (!$workflow) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow not found');
        }
        $lists = WorkflowStage::where('workflow_id', $workflow->id)
            ->orderByRaw('COALESCE(sort_order, id) ASC')
            ->paginate(config('constants.limit', 50));
        return view('AdminConsole.features.workflow.stages-index', compact('workflow', 'lists'));
    }

    /**
     * Create stage form (for a specific workflow).
     */
    public function createStage($workflowId)
    {
        $workflowId = $this->decodeString($workflowId);
        $workflow = Workflow::find($workflowId);
        if (!$workflow) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow not found');
        }
        return view('AdminConsole.features.workflow.create', compact('workflow'));
    }

    /**
     * Store new stage(s). Supports workflow_id for per-workflow stages.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'stage_name' => 'required|array',
            'stage_name.*' => 'required|string|max:255',
        ]);
        $workflowId = $request->workflow_id;
        if (!$workflowId) {
            $general = Workflow::where('name', 'General')->first();
            $workflowId = $general ? $general->id : null;
        }
        $stages = $request->stage_name;
        $baseQuery = WorkflowStage::query();
        if ($workflowId) {
            $baseQuery->where('workflow_id', $workflowId);
        }
        $maxSortOrder = (int) ($baseQuery->max('sort_order') ?? $baseQuery->max('id') ?? 0);
        foreach ($stages as $stageName) {
            $o = new WorkflowStage();
            $o->name = $stageName;
            $o->workflow_id = $workflowId;
            $o->sort_order = ++$maxSortOrder;
            $o->save();
        }
        if ($workflowId) {
            return redirect()->route('adminconsole.features.workflow.stages', base64_encode(convert_uuencode($workflowId)))
                ->with('success', 'Workflow Stages Added Successfully');
        }
        return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Stages Added Successfully');
    }

    /**
     * Edit stage form.
     */
    public function edit($id)
    {
        $id = $this->decodeString($id);
        $fetchedData = WorkflowStage::find($id);
        if (!$fetchedData) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow Stage Not Found');
        }
        $workflow = $fetchedData->workflow;
        return view('AdminConsole.features.workflow.edit', compact('fetchedData', 'workflow'));
    }

    /**
     * Update stage.
     */
    public function update(Request $request, $id)
    {
        $id = $this->decodeString($id);
        $stage = WorkflowStage::find($id);
        if (!$stage) {
            return redirect()->route('adminconsole.features.workflow.index')->with('error', 'Workflow Stage Not Found');
        }
        $this->validate($request, [
            'stage_name' => 'required|array',
            'stage_name.*' => 'required|string|max:255',
        ]);
        $stage->name = $request->stage_name[0];
        $stage->save();
        $workflow = $stage->workflow;
        if ($workflow) {
            return redirect()->route('adminconsole.features.workflow.stages', base64_encode(convert_uuencode($workflow->id)))
                ->with('success', 'Workflow Stage Updated Successfully');
        }
        return redirect()->route('adminconsole.features.workflow.index')->with('success', 'Workflow Stage Updated Successfully');
    }
}

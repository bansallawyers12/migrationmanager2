<?php

namespace App\Models;

use App\Enums\PortalTaskType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStagePortalTasklist extends Model
{
    protected $table = 'workflow_stage_portal_tasklists';

    protected $fillable = [
        'workflow_id',
        'workflow_stage_id',
        'name',
        'description',
        'task_type',
        'allow_client',
        'is_required',
        'sort_order',
    ];

    protected $attributes = [
        'task_type' => 'upload',
        'allow_client' => true,
        'is_required' => true,
        'sort_order' => 0,
    ];

    protected $casts = [
        'allow_client' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
        'task_type' => PortalTaskType::class,
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'workflow_stage_id');
    }
}

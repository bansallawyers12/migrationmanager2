<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class ClientMatter extends Model
{
    use Notifiable;
    use Sortable;

    /**
     * The table associated with the model.
     */
    protected $table = 'client_matters';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'client_id',
        'sel_migration_agent',
        'sel_person_responsible',
        'sel_person_assisting',
        'workflow_stage_id',
        'matter_status',
        'client_unique_matter_no',
        'sel_matter_id',
        'updated_at_type'
    ];

    /**
     * Get the client that owns the matter.
     */
    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get the migration agent assigned to the matter.
     */
    public function migrationAgent()
    {
        return $this->belongsTo(Admin::class, 'sel_migration_agent');
    }

    /**
     * Get the person responsible for the matter.
     */
    public function personResponsible()
    {
        return $this->belongsTo(Admin::class, 'sel_person_responsible');
    }

    /**
     * Get the person assisting with the matter.
     */
    public function personAssisting()
    {
        return $this->belongsTo(Admin::class, 'sel_person_assisting');
    }

    /**
     * Get the workflow stage for the matter.
     */
    public function workflowStage()
    {
        return $this->belongsTo(WorkflowStage::class, 'workflow_stage_id');
    }

    /**
     * Get the matter type.
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class, 'sel_matter_id');
    }

    /**
     * Get the notes for the matter.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'client_id', 'client_id');
    }

    /**
     * Get the mail reports for the matter.
     */
    public function mailReports()
    {
        return $this->hasMany(MailReport::class, 'client_matter_id');
    }
}

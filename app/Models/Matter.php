<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Matter extends Model
{
	use Sortable;
	
	protected $table = 'matters';
	
	protected $fillable = [
		'id', 'title', 'nick_name', 'workflow_id', 'is_for_company', 'created_at', 'updated_at'
	];

	/**
	 * Get the default workflow for this matter type.
	 */
	public function workflow()
	{
		return $this->belongsTo(Workflow::class, 'workflow_id');
	}
	
	public $sortable = ['id', 'title', 'nick_name', 'created_at', 'updated_at'];
	
	// Relationship with EmailTemplate (matter_other type)
	public function otherEmailTemplates()
	{
		return $this->hasMany(EmailTemplate::class, 'matter_id')->where('type', EmailTemplate::TYPE_MATTER_OTHER);
	}

	// Relationship with EmailTemplate (matter_first type)
	public function firstEmailTemplate()
	{
		return $this->hasOne(EmailTemplate::class, 'matter_id')->where('type', EmailTemplate::TYPE_MATTER_FIRST);
	}

	/**
	 * Check if this matter is for companies only
	 */
	public function isForCompany(): bool
	{
		return (bool) $this->is_for_company;
	}

	/**
	 * Scope to filter matters by client type
	 */
	public function scopeForClientType($query, bool $isCompany)
	{
		if ($isCompany) {
			// For companies: show only matters where is_for_company = true
			return $query->where('is_for_company', true);
		} else {
			// For personal clients: show only matters where is_for_company = false or null
			return $query->where(function($q) {
				$q->where('is_for_company', false)
				  ->orWhereNull('is_for_company');
			});
		}
	}
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectArea extends Model
{
    protected $table = 'subject_areas';

    protected $fillable = [
        'name',
        'description',
        'admin_id',
        'created_at',
        'updated_at'
    ];

    // Relationship with Subject model
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'subject_area_id');
    }
}

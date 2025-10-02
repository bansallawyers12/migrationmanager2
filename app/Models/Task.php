<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
        'created_at',
        'updated_at'
    ];

    // Relationship with Admin (user)
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }
}

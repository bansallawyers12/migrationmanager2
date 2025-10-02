<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'description',
        'admin_id',
        'parent_id',
        'sort_order',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Self-referencing relationship for parent categories
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}

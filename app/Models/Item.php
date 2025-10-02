<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'admin_id',
        'category_id',
        'price',
        'unit',
        'is_active',
        'created_at',
        'updated_at'
    ];

    // Relationship with Admin (user)
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    // Relationship with Admin (admin)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

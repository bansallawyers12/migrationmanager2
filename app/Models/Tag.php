<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tag extends Authenticatable
{
    use Notifiable;
	use Sortable;
	

	public function createddetail()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','created_by', 'id');
    }	
	
	public function updateddetail()
    {
        return $this->belongsTo('App\\Models\\\Models\\Admin','updated_by', 'id');
    }
}

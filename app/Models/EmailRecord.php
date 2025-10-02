<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class EmailRecord extends Authenticatable
{
    use Notifiable;
	use Sortable;

    protected $connection = 'second_db';

	// The authentication guard for admin
    protected $guard = 'email_users';

	/**
      * The attributes that are mass assignable.
      *
      * @var array
	*/
	//protected $fillable = ['id', 'email', 'password', 'decrypt_password', 'status', 'created_at', 'updated_at' ];

	/**
      * The attributes that should be hidden for arrays.
      *
      * @var array
	*/
    //protected $hidden = [ 'password', 'remember_token'];

}

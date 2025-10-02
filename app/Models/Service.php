<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Service extends Authenticatable
{
    use Notifiable;
	use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
	/*protected $fillable = [
        'id', 'first_name', 'last_name', 'email', 'email_verified_at', 'password', 'phone', 'course_level', 'country', 'state', 'city', 'address', 'zip', 'profile_img', 'provider', 'provider_id', 'first_time_msg', 'status', 'created_at', 'updated_at'
    ];*/
	 
	protected $fillable = [
        'id', 'title', 'parent', 'description', 'services_icon', 'services_image', 'status', 'created_at', 'updated_at'
    ];

    /** 
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */ 
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	/* public function countryData()
    {
        return $this->belongsTo('App\\Models\\Country','country');
    }
	
	public function stateData()
    {
        return $this->belongsTo('App\\Models\\State','state');
    } */
	
	
	public function buildTree(Array $data, $parent = 0) {
		$tree = array();
		foreach ($data as $d) {
			if ($d['parent'] == $parent) {
				$children = Self::buildTree($data, $d['id']);
				// set a trivial key
				if (!empty($children)) {
					$d['_children'] = $children;
				}
				$tree[] = $d;
			}
		}
		return $tree;
	} 

	public static function printTree($tree, $r = 0, $p = null, $select = null) {
		foreach ($tree as $i => $t) {
			$dash = ($t['parent'] == 0) ? '' : str_repeat('-', $r) .' ';
			$selected = ($t['id'] == $select) ? 'selected' : ' ';
			printf("\t<option value='%d' %s>%s%s</option>\n", $t['id'],$selected, $dash, $t['name']);
			if ($t['parent'] == $p) {
				// reset $r
				$r = 0;
			}
			if (isset($t['_children'])) {
				Self::printTree($t['_children'], ++$r, $t['parent'], $select);
			}
		}
	}
	
}

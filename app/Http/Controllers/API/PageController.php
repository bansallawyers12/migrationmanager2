<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
//use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Response;

use App\Models\Admin;

use Config;
class PageController extends BaseController
{
	public function __construct(Request $request)
    {	
		//$siteData = WebsiteSetting::where('id', '!=', '')->first();
		//\View::share('siteData', $siteData);
	}
	
	public function Index(Request $request){
		return $this->sendError('Error', array('page'=>array('CMS Pages functionality has been removed'))); 
	}
}
?>
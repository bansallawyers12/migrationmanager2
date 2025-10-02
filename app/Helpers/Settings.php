<?php namespace App\Helpers;
use Auth;
class Settings
{
    static function sitedata($fieldname)
    {
       
         $siteData = \App\Models\Setting::where('office_id', '=', @Auth::user()->office_id)->first();
         if($siteData){
              return $siteData->$fieldname;
         }else{
             return 'none';
            
         }
         
      
	    
    }
    
}
?>
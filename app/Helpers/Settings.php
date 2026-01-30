<?php namespace App\Helpers;
use Auth;
class Settings
{
    static function sitedata($fieldname)
    {
        // Set permanent date and time formats
        if($fieldname == 'date_format') {
            return 'd/m/Y'; // Permanent date format
        }
        
        if($fieldname == 'time_format') {
            return 'g:i A'; // Permanent time format (12-hour with AM/PM)
        }
        
        // For other fields, still check database if needed
        $siteData = \App\Models\Setting::where('office_id', '=', @Auth::user()->office_id)->first();
        if($siteData){
             return $siteData->$fieldname;
        }else{
            return 'none';
        }
    }
    
}
?>
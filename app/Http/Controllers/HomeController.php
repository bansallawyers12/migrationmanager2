<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

// use App\Models\WebsiteSetting; // removed website settings dependency
use App\Models\Slider;
use App\Models\OurService;
use App\Models\Testimonial;
use App\Models\HomeContent;
use App\Mail\CommonMail;

use Illuminate\Support\Facades\Session;
use Cookie;

use Mail;
use Swift_SmtpTransport;
use Swift_Mailer;
use Helper;

use Stripe;


class HomeController extends Controller
{
	public function __construct(Request $request)
    {
        // Share safe defaults instead of WebsiteSetting
        $siteData = (object) [
            'phone' => env('APP_PHONE', ''),
            'ofc_timing' => env('APP_OFFICE_TIMING', ''),
            'email' => env('APP_EMAIL', ''),
            'logo' => env('APP_LOGO', 'logo.png'),
        ];
        \View::share('siteData', $siteData);
	}


	public function sicaptcha(Request $request)
    {
		 $code=$request->code;

		$im = imagecreatetruecolor(50, 24);
		$bg = imagecolorallocate($im, 37, 37, 37); //background color blue
		$fg = imagecolorallocate($im, 255, 241, 70);//text color white
		imagefill($im, 0, 0, $bg);
		imagestring($im, 5, 5, 5,  $code, $fg);
		header("Cache-Control: no-cache, must-revalidate");
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);

    }

	public static function hextorgb ($hexstring){
		$integar = hexdec($hexstring);
					return array( "red" => 0xFF & ($integar >> 0x10),
		"green" => 0xFF & ($integar >> 0x8),
		"blue" => 0xFF & $integar
		);
	}




	public function refresh_captcha() {
		$vals = array(
			'img_path' => public_path().'/captcha/',
			'img_url' => asset('public/captcha'),
			'expiration' => 7200,
			'word_lenght' => 6,
			'font_size' => 15,
			'img_width'	=> '110',
			'img_height' => '40',
			'colors'	=> array('background' => array(255,175,2),'border' => array(255,175,2),	'text' => array(255,255,255),	'grid' => array(255,255,255))
		);

		$cap = $this->create_captcha($vals);
		$captcha = $cap['image'];
		session()->put('captchaWord', $cap['word']);
		echo $cap['image'];
	}

	


     public function getdatetime(Request $request)
    {   //dd($request->all());
        $enquiry_item = $request->enquiry_item;
        $req_service_id = $request->id;
        //echo $enquiry_item."===".$req_service_id; die;
        if(isset($request->inperson_address) && $request->inperson_address == 1 ) { //Adelaide
            if( $enquiry_item != "" && $req_service_id != "") {
                if( $req_service_id == 1 ) { //Paid service
                    $person_id = 5; //Adelaide
                    $service_type = $req_service_id; //Paid service
                }
                else if( $req_service_id == 2 ) { //Free service
                    $person_id = 5; //Adelaide
                    $service_type = $req_service_id; //Free service
                }
            }
        }
        else { //Melbourne

            if( $enquiry_item != "" && $req_service_id != "") {
                if( $req_service_id == 1 ) { //Paid service
                    $person_id = 1; //Ajay
                    $service_type = $req_service_id; //Paid service
                }
                else if( $req_service_id == 2 ) { //Free service
                    if( $enquiry_item == 1 || $enquiry_item == 6 || $enquiry_item == 7 ){
                        //1 => Permanent Residency Appointment
                        //6 => Complex matters: AAT, Protection visa, Federal Cas
                        //7 => Visa Cancellation/ NOICC/ Visa refusals
                        $person_id = 1; //Ajay
                        $service_type = $req_service_id; //Free service
                    }
                    else if( $enquiry_item == 2 || $enquiry_item == 3 ){
                        //2 => Temporary Residency Appointment
                        //3 => JRP/Skill Assessment
                        $person_id = 2; //Shubam
                        $service_type = $req_service_id; //Free service
                    }
                    else if( $enquiry_item == 4 ){ //Tourist Visa
                        $person_id = 3; //Tourist
                        $service_type = $req_service_id; //Free service
                    }
                    else if( $enquiry_item == 5 ){ //Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)
                        $person_id = 4; //Education
                        $service_type = $req_service_id; //Free service
                    }
                }
            }
        }
        //echo $person_id."===".$service_type; die;
        $bookservice = \App\Models\BookService::where('id', $req_service_id)->first();//dd($bookservice);
        $service = \App\Models\BookServiceSlotPerPerson::where('person_id', $person_id)->where('service_type', $service_type)->first();//dd($service);
	    if( $service ){
		   $weekendd  =array();
		    if($service->weekend != ''){
				$weekend = explode(',',$service->weekend);
				foreach($weekend as $e){
					if($e == 'Sun'){
						$weekendd[] = 0;
					}else if($e == 'Mon'){
						$weekendd[] = 1;
					}else if($e == 'Tue'){
						$weekendd[] = 2;
					}else if($e == 'Wed'){
						$weekendd[] = 3;
					}else if($e == 'Thu'){
						$weekendd[] = 4;
					}else if($e == 'Fri'){
						$weekendd[] = 5;
					}else if($e == 'Sat'){
						$weekendd[] = 6;
					}
				}
			}
			$start_time = date('H:i',strtotime($service->start_time));
			$end_time = date('H:i',strtotime($service->end_time));

            if($service->disabledates != ''){
                $disabledatesarray =  array();
                if( strpos($service->disabledates, ',') !== false ) {
                    $disabledatesArr = explode(',',$service->disabledates);
                    $disabledatesarray = $disabledatesArr;
                } else {
                    $disabledatesarray = array($service->disabledates);
                }
            } else {
                $disabledatesarray =  array();
            }

            // Add the current date to the array
            $disabledatesarray[] = date('d/m/Y'); //dd($disabledatesarray);
            if(isset($request->inperson_address) && $request->inperson_address == 1 ) { //Adelaide
                $duration = $bookservice->duration;
            } else { //Melbourne
                if( isset($req_service_id) && $req_service_id == 1){ //Paid
                    $duration = 15; //In melbourne case paid service = 15
                } else if( isset($req_service_id) && $req_service_id == 2){ //Free
                    $duration = $bookservice->duration; //In melbourne case free service = 15
                }
            }
            return json_encode(array('success'=>true, 'duration' =>$duration,'weeks' => $weekendd,'start_time' =>$start_time,'end_time'=>$end_time,'disabledatesarray'=>$disabledatesarray));
	    } else {
		 return json_encode(array('success'=>false, 'duration' =>0));
	    }
    }

    /**
     * Get disabled date/time - COMMENTED OUT FOR FUTURE USE
     * This function has been replaced by AppointmentsController::getDisabledDateTime()
     * which uses the Bansal API REST endpoint.
     * 
     * @deprecated Use AppointmentsController::getDisabledDateTime() instead
     */
    /*
    public function getdisableddatetime(Request $request)
    {
		$requestData = $request->all(); //dd($requestData);
		$slot_overwrite = $request->slot_overwrite ?? 0; // Default to 0 if not provided
		$date = explode('/', $requestData['sel_date']);
		$datey = $date[2].'-'.$date[1].'-'.$date[0];

        //Adelaide
        if( isset($request->inperson_address) && $request->inperson_address == 1 )
        {
            if( isset($request->service_id) && $request->service_id == 1  ){ //Adelaide Paid Service
                $book_service_slot_per_person_tbl_unique_id = 6;
            } else if( isset($request->service_id) && $request->service_id == 2  ){ //Adelaide Free Service
                $book_service_slot_per_person_tbl_unique_id = 8;
            }

            $service = \App\Models\Appointment::select('id','date','time')
            ->where('inperson_address', '=', 1)
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->exists();

            $servicelist = \App\Models\Appointment::select('id','date','time')
            ->where('inperson_address', '=', 1)
            ->where('status', '!=', 7)
            ->whereDate('date', $datey)
            ->get();
        }

        //Melbourne
        else
        {
            if
            (
                ( isset($request->service_id) && $request->service_id == 1  )
                ||
                (
                    ( isset($request->service_id) && $request->service_id == 2 )
                    &&
                    ( isset($request->enquiry_item) && ( $request->enquiry_item == 1 || $request->enquiry_item == 6 || $request->enquiry_item == 7) )
                )
            ) { //Paid
                if( isset($request->service_id) && $request->service_id == 1  ){ //Ajay Paid Service
                    $book_service_slot_per_person_tbl_unique_id = 1;
                } else if( isset($request->service_id) && $request->service_id == 2  ){ //Ajay Free Service
                    $book_service_slot_per_person_tbl_unique_id = 2;
                }

                $service = \App\Models\Appointment::select('id', 'date', 'time')
                ->where(function ($query) {
                    $query->whereNull('inperson_address')
                        ->orWhere('inperson_address', '')
                        ->orWhere('inperson_address', 2); //For Melbourne
                })
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                        ->where('service_id', 1);
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('noe_id', [1, 6, 7])
                        ->where('service_id', 2);
                    });
                })->exists();

                $servicelist = \App\Models\Appointment::select('id', 'date', 'time')
                ->where(function ($query) {
                    $query->whereNull('inperson_address')
                        ->orWhere('inperson_address', '')
                        ->orWhere('inperson_address', 2); //For Melbourne
                })
                ->where('status', '!=', 7)
                ->whereDate('date', $datey)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereIn('noe_id', [1, 2, 3, 4, 5, 6, 7, 8])
                        ->where('service_id', 1);
                    })
                    ->orWhere(function ($q) {
                        $q->whereIn('noe_id', [1, 6, 7])
                        ->where('service_id', 2);
                    });
                })->get();
            }
            else if( isset($request->service_id) && $request->service_id == 2) { //Free
                if( isset($request->enquiry_item) && ( $request->enquiry_item == 2 || $request->enquiry_item == 3 ) ) { //Temporary and JRP

                    if( isset($request->service_id) && $request->service_id == 2  ){ //Shubam Free Service
                        $book_service_slot_per_person_tbl_unique_id = 3;
                    }

                    $service = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->exists();

                    $servicelist = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [2,3])
                        ->Where('service_id', 2);
                    })->get();
                }
                else if( isset($request->enquiry_item) && ( $request->enquiry_item == 4 ) ) { //Tourist Visa

                    if( isset($request->service_id) && $request->service_id == 2  ){ //Tourist Free Service
                        $book_service_slot_per_person_tbl_unique_id = 4;
                    }

                    $service = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->exists();

                    $servicelist = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [4])
                        ->Where('service_id', 2);
                    })->get();
                }
                else if( isset($request->enquiry_item) && ( $request->enquiry_item == 5 ) ) { //Education/Course Change
                    if( isset($request->service_id) && $request->service_id == 2  ){ //Education Free Service
                        $book_service_slot_per_person_tbl_unique_id = 5;
                    }
                    $service = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [5])
                        ->Where('service_id', 2);
                    })->exists();

                    $servicelist = \App\Models\Appointment::select('id','date','time')
                    ->where(function ($query) {
                        $query->whereNull('inperson_address')
                            ->orWhere('inperson_address', '')
                            ->orWhere('inperson_address', 2); //For Melbourne
                    })
                    ->where('status', '!=', 7)
                    ->whereDate('date', $datey)
                    ->where(function ($query) {
                        $query->whereIn('noe_id', [5])
                        ->Where('service_id', 2);
                    })->get();
                }
            }
        }
        //dd($servicelist);
        $disabledtimeslotes = array();
	    if($service){
            foreach($servicelist as $list){
                $disabledtimeslotes[] = date('g:i A', strtotime($list->time)); //'H:i A'
			}
            // Query book_service_disable_slots table only if slot_overwrite is not enabled
            if($slot_overwrite != 1){
                $disabled_slot_arr = \App\Models\BookServiceDisableSlot::select('id','slots')->where('book_service_slot_per_person_id', $book_service_slot_per_person_tbl_unique_id)->whereDate('disabledates', $datey)->get();
                //dd($disabled_slot_arr);
                if(!empty($disabled_slot_arr) && count($disabled_slot_arr) >0 ){
                    $newArray = explode(",",$disabled_slot_arr[0]->slots); //dd($newArray);
                } else {
                    $newArray = array();
                }
                $disabledtimeslotes = array_merge($disabledtimeslotes, $newArray); //dd($disabledtimeslotes);
            }
		    return json_encode(array('success'=>true, 'disabledtimeslotes' =>$disabledtimeslotes));
	    } else {
            // Query book_service_disable_slots table only if slot_overwrite is not enabled
            if($slot_overwrite != 1){
                $disabled_slot_arr = \App\Models\BookServiceDisableSlot::select('id','slots')->where('book_service_slot_per_person_id', $book_service_slot_per_person_tbl_unique_id)->whereDate('disabledates', $datey)->get();
                //dd($disabled_slot_arr);
                if(!empty($disabled_slot_arr) && count($disabled_slot_arr) >0 ){
                    $newArray = explode(",",$disabled_slot_arr[0]->slots); //dd($newArray);
                } else {
                    $newArray = array();
                }
                $disabledtimeslotes = array_merge($disabledtimeslotes, $newArray); //dd($disabledtimeslotes);
            }
		    return json_encode(array('success'=>true, 'disabledtimeslotes' =>$disabledtimeslotes));
	    }
    }
    */


    /**
     * Get date/time backend - COMMENTED OUT FOR FUTURE USE
     * This function has been replaced by AppointmentsController::getDateTimeBackend()
     * which uses the Bansal API REST endpoint.
     * 
     * @deprecated Use AppointmentsController::getDateTimeBackend() instead
     */
    /*
    public function getdatetimebackend(Request $request)
    {   //dd($request->all());
        $enquiry_item = $request->enquiry_item;
        $req_service_id = $request->id;
        $slot_overwrite = $request->slot_overwrite ?? 0; // Default to 0 if not provided
        \Log::info('getdatetimebackend called with slot_overwrite:', ['slot_overwrite' => $slot_overwrite, 'request' => $request->all()]);
        //echo $enquiry_item."===".$req_service_id; die;
        
        $book_service_slot_per_person_tbl_unique_id = null; // Initialize
        
        if(isset($request->inperson_address) && $request->inperson_address == 1 ) { //Adelaide
            if( $enquiry_item != "" && $req_service_id != "") {
                if( $req_service_id == 1 ) { //Paid service
                    $person_id = 5; //Adelaide
                    $service_type = $req_service_id; //Paid service
                    $book_service_slot_per_person_tbl_unique_id = 6;
                }
                else if( $req_service_id == 2 ) { //Free service
                    $person_id = 5; //Adelaide
                    $service_type = $req_service_id; //Free service
                    $book_service_slot_per_person_tbl_unique_id = 8;
                }
            }
        }
        else { //Melbourne

            if( $enquiry_item != "" && $req_service_id != "")
            {
                if( $req_service_id == 1 ) { //Paid service
                    $person_id = 1; //Ajay
                    $service_type = $req_service_id; //Paid service
                    $book_service_slot_per_person_tbl_unique_id = 1;
                }
                else if( $req_service_id == 2 ) { //Free service
                    if( $enquiry_item == 1 || $enquiry_item == 6 || $enquiry_item == 7 ){
                        //1 => Permanent Residency Appointment
                        //6 => Complex matters: AAT, Protection visa, Federal Cas
                        //7 => Visa Cancellation/ NOICC/ Visa refusals
                        $person_id = 1; //Ajay
                        $service_type = $req_service_id; //Free service
                        $book_service_slot_per_person_tbl_unique_id = 2;
                    }
                    else if( $enquiry_item == 2 || $enquiry_item == 3 ){
                        //2 => Temporary Residency Appointment
                        //3 => JRP/Skill Assessment
                        $person_id = 2; //Shubam
                        $service_type = $req_service_id; //Free service
                        $book_service_slot_per_person_tbl_unique_id = 3;
                    }
                    else if( $enquiry_item == 4 ){ //Tourist Visa
                        $person_id = 3; //Tourist
                        $service_type = $req_service_id; //Free service
                        $book_service_slot_per_person_tbl_unique_id = 4;
                    }
                    else if( $enquiry_item == 5 ){ //Education/Course Change/Student Visa/Student Dependent Visa (for education selection only)
                        $person_id = 4; //Education
                        $service_type = $req_service_id; //Free service
                        $book_service_slot_per_person_tbl_unique_id = 5;
                    }
                }
            }
        }
        //echo $person_id."===".$service_type; die;
        $bookservice = \App\Models\BookService::where('id', $req_service_id)->first();//dd($bookservice);
        $service = \App\Models\BookServiceSlotPerPerson::where('person_id', $person_id)->where('service_type', $service_type)->first();//dd($service);
	    if( $service ){
		   $weekendd  =array();
		   // Skip weekend blocking if slot_overwrite is enabled
		    if($service->weekend != '' && $slot_overwrite != 1){
				$weekend = explode(',',$service->weekend);
				foreach($weekend as $e){
					$e = trim($e); // Remove whitespace
					if($e == 'Sun'){
						$weekendd[] = 0;
					}else if($e == 'Mon'){
						$weekendd[] = 1;
					}else if($e == 'Tue'){
						$weekendd[] = 2;
					}else if($e == 'Wed'){
						$weekendd[] = 3;
					}else if($e == 'Thu'){
						$weekendd[] = 4;
					}else if($e == 'Fri'){
						$weekendd[] = 5;
					}else if($e == 'Sat'){
						$weekendd[] = 6;
					}
				}
			}
			$start_time = date('H:i',strtotime($service->start_time));
			$end_time = date('H:i',strtotime($service->end_time));

            if($service->disabledates != ''){
                $disabledatesarray =  array();
                if( strpos($service->disabledates, ',') !== false ) {
                    $disabledatesArr = explode(',',$service->disabledates);
                    $disabledatesarray = $disabledatesArr;
                } else {
                    $disabledatesarray = array($service->disabledates);
                }
            } else {
                $disabledatesarray =  array();
            }
            
            // Query book_service_disable_slots table to get additional disabled dates
            // Skip this if slot_overwrite is enabled (allows booking on blocked dates)
            if(isset($book_service_slot_per_person_tbl_unique_id) && $slot_overwrite != 1){
                $disabled_dates_from_table = \App\Models\BookServiceDisableSlot::select('disabledates')
                    ->where('book_service_slot_per_person_id', $book_service_slot_per_person_tbl_unique_id)
                    ->get();
                
                foreach($disabled_dates_from_table as $disabled_date_row){
                    $formatted_date = date('d/m/Y', strtotime($disabled_date_row->disabledates));
                    if(!in_array($formatted_date, $disabledatesarray)){
                        $disabledatesarray[] = $formatted_date;
                    }
                }
            }
            
            // Add the current date to the array
            //$disabledatesarray[] = date('d/m/Y'); //dd($disabledatesarray);
            if(isset($request->inperson_address) && $request->inperson_address == 1 ) { //Adelaide
                $duration = $bookservice->duration;
            } else { //Melbourne
                if( isset($req_service_id) && $req_service_id == 1){ //Paid
                    $duration = 15; //In melbourne case paid service = 15
                } else if( isset($req_service_id) && $req_service_id == 2){ //Free
                    $duration = $bookservice->duration; //In melbourne case free service = 15
                }
            }
            \Log::info('getdatetimebackend response:', ['slot_overwrite' => $slot_overwrite, 'weekendd' => $weekendd, 'disabledatesarray' => $disabledatesarray]);
            return json_encode(array('success'=>true, 'duration' =>$duration,'weeks' => $weekendd,'start_time' =>$start_time,'end_time'=>$end_time,'disabledatesarray'=>$disabledatesarray));
	    }else{
		 return json_encode(array('success'=>false, 'duration' =>0));
	   }
    }
    */


}


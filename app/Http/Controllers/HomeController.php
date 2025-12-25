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

	


    /**
     * Get date/time backend settings (office hours, duration, disabled days)
     * Returns appointment configuration for calendar initialization
     */
    public function getdatetimebackend(Request $request)
    {
        $enquiry_item = $request->enquiry_item;
        $req_service_id = $request->id;
        $slot_overwrite = $request->slot_overwrite ?? 0;
        $inperson_address = $request->inperson_address;
        
        \Log::info('getdatetimebackend called', [
            'service_id' => $req_service_id,
            'enquiry_item' => $enquiry_item,
            'inperson_address' => $inperson_address,
            'slot_overwrite' => $slot_overwrite
        ]);
        
        // Define default settings based on service and location
        // Service 1 = Free Consultation (15 min)
        // Service 2 = Comprehensive Migration Advice (30 min)
        // Service 3 = Overseas Applicant Enquiry (30 min)
        
        $duration = 15; // Default
        $start_time = '09:00';
        $end_time = '17:00';
        $weekendd = [];
        $disabledatesarray = [];
        
        // Determine duration based on service
        if ($req_service_id == 1) {
            $duration = 15; // Free Consultation
        } else if ($req_service_id == 2 || $req_service_id == 3) {
            $duration = 30; // Paid services
        }
        
        // Set office hours based on location
        if ($inperson_address == 1) {
            // Adelaide office hours
            $start_time = '09:00';
            $end_time = '17:00';
        } else {
            // Melbourne office hours
            $start_time = '09:00';
            $end_time = '17:00';
        }
        
        // Block weekends unless slot_overwrite is enabled
        if ($slot_overwrite != 1) {
            $weekendd = [0, 6]; // Sunday and Saturday
        }
        
        // Return success with appointment settings
        return json_encode([
            'success' => true,
            'duration' => $duration,
            'weeks' => $weekendd,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'disabledatesarray' => $disabledatesarray
        ]);
    }

    /**
     * Get disabled date/time slots for selected date
     * Queries BookingAppointment table to find already booked time slots
     */
    public function getdisableddatetime(Request $request)
    {
        $requestData = $request->all();
        $slot_overwrite = $request->slot_overwrite ?? 0;
        $date = explode('/', $requestData['sel_date']);
        $datey = $date[2].'-'.$date[1].'-'.$date[0];
        
        \Log::info('getdisableddatetime called', [
            'date' => $datey,
            'service_id' => $request->service_id,
            'inperson_address' => $request->inperson_address,
            'slot_overwrite' => $slot_overwrite
        ]);

        // Query BookingAppointment table to find already booked slots
        $query = \App\Models\BookingAppointment::select('id', 'appointment_datetime', 'timeslot_full')
            ->whereDate('appointment_datetime', $datey)
            ->whereNotIn('status', ['cancelled', 'no_show']);

        // Filter by location
        if (isset($request->inperson_address)) {
            $query->where('inperson_address', '=', $request->inperson_address);
        }

        $servicelist = $query->get();
        
        // Extract booked time slots
        $disabledtimeslotes = array();
        
        foreach ($servicelist as $appointment) {
            // Try to extract start time from timeslot_full (e.g., "10:00 AM - 10:15 AM")
            if ($appointment->timeslot_full) {
                // Extract the start time
                if (preg_match('/^([0-9]{1,2}:[0-9]{2}\s*(?:AM|PM)?)/i', $appointment->timeslot_full, $matches)) {
                    $disabledtimeslotes[] = trim($matches[1]);
                }
            } else {
                // Fallback to appointment_datetime
                $time = date('g:i A', strtotime($appointment->appointment_datetime));
                $disabledtimeslotes[] = $time;
            }
        }
        
        // Remove duplicates
        $disabledtimeslotes = array_unique($disabledtimeslotes);
        
        \Log::info('Disabled timeslots found', ['count' => count($disabledtimeslotes), 'slots' => $disabledtimeslotes]);
        
        return json_encode(array('success' => true, 'disabledtimeslotes' => array_values($disabledtimeslotes)));
    }



}


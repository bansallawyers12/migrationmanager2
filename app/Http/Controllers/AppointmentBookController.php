<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use App\Models\Appointment;
use App\Models\Admin;
use Helper;
use Auth;
use Config;
use Stripe;
use DB;


class AppointmentBookController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

	}
	/**
     * All Cms Page.
     *
     * @return \Illuminate\Http\Response
     */

	public function store(Request $request) {

		$requestData = $request->all(); //dd($requestData);
		$service_id = $requestData['service_id'];
		$noe_id = $requestData['noe_id'];
		$fullname = $requestData['fullname'];
		$title = $requestData['title'];
		$description = $requestData['description'];
		$email = $requestData['email'];
		$phone = $requestData['phone'];
		$date = explode('/', $requestData['date']);
		$datey = $date[2].'-'.$date[1].'-'.$date[0];
		$service = \App\Models\BookService::find($requestData['service_id']);

		$user=\App\Models\Admin::where(function ($query) use($requestData){
			$query->where('email',$requestData['email'])
				  ->orWhere('phone',$requestData['phone']);
		})->first();
			$parts = explode(" ", $fullname);
			$last_name = array_pop($parts);
			$first_name = implode(" ", $parts);
		if(empty($user)){
			$objs							= 	new Admin;
			$objs->client_id	=	strtoupper($first_name).date('his');
			$objs->role	=	7;
			$objs->last_name	=	$last_name;
			$objs->first_name	=	$first_name;
			$objs->email	=	$email;
			$objs->phone	=	$phone;
			$objs->save();
			$client_id =$objs->id;
		}else{
			if(empty($user->client_id)){
				Admin::where('id', $user->id)->update(['client_id' => strtoupper($first_name).date('his')]);
			}
			$client_id=$user->id;
		}

		$obj = new Appointment;
		$obj->client_id = $client_id;
		$obj->service_id = $service_id;
		$obj->noe_id = $noe_id;
		$obj->full_name = $fullname;
		$obj->title = $title;
		$obj->description = $description;
		$obj->email =	$email;
		$obj->phone = $phone;
		$obj->date = $datey;
		if($requestData['time'] != ""){
			$time = explode('-', $requestData['time']);
			//echo "@@".date("H:i", strtotime($time[0])); die;
			$obj->time = date("H:i", strtotime($time[0]));
		}
		//$obj->time = $requestData['time'];
		$obj->timeslot_full = $requestData['time'];
		$obj->invites=0;
		$obj->appointment_details=$requestData['appointment_details'];
		$saved = $obj->save();

		if($saved){

		$note = new \App\Models\Note;
		$note->client_id =  $client_id;
		$note->user_id = 1;
		$note->title = $requestData['appointment_details'];
		$note->description = $description;
		$note->mail_id = 0;
		$note->type = 'client';
		$saved = $note->save();

		$subject = 'added a note';
		$objs = new \App\Models\ActivitiesLog;
		$objs->client_id = $client_id;
		$objs->created_by = 1;
		$objs->description = '<span class="text-semi-bold">You have an new appointment on '.$requestData['date'].' at '.$requestData['time'].' for '.$title.'</span>';
		$objs->subject = $subject;
		$objs->save();

		// 	$message = '<html><body>';
		// 		$message .= '<p>Dear Admin,</p>';
		// 		$message .= '<p>You have an Appointment on '.$requestData['date'].' '.$requestData['time'].'</p>';
		// 		$message .= '<table><tr>
		// 			<td><b>Service: </b>'.$service->title.'</td></tr>
		// 			<tr><td><b>Name: </b>'.$fullname.'</td></tr>
		// 			<tr><td><b>Description: </b>'.$description.'</td></tr>
		// 			<tr><td><b>Email: </b>'.$email.'</td></tr>
		// 			<tr><td><b>Phone: </b>'.$phone.'</td></tr>
		// 			<tr><td><b>Apoimtment Date/Time: </b>'.$requestData['date'].'/'.$requestData['time'].'</td></tr>
		// 		</table>';
		// 	$message .= '</body></html>';
		// 	$subject = 'Appointment on '.$requestData['date'].' '.$requestData['time'];
		// 	$this->send_compose_template('test@gmail.com', $subject, 'test@gmail.com', $message,'Test');

		// 	$message1 = '<html><body>';
		// 	$message1 .= '<p>Dear '.$fullname.',</p>';
		// 	$message1 .= '<p>You have an Appointment on '.$requestData['date'].' '.$requestData['time'].'</p>';
		// 	$message1 .= '<table><tr>
		// 		<td><b>Service: </b>'.$service->title.'</td></tr>
		// 		<tr><td><b>Name: </b>'.$fullname.'</td></tr>
		// 		<tr><td><b>Description: </b>'.$description.'</td></tr>
		// 		<tr><td><b>Email: </b>'.$email.'</td></tr>
		// 		<tr><td><b>Phone: </b>'.$phone.'</td></tr>
		// 		<tr><td><b>Apoimtment Date/Time: </b>'.$requestData['date'].'/'.$requestData['time'].'</td></tr>
		// 	</table>';
		// $message1 .= '</body></html>';
		// 	$this->send_compose_template($email, $subject, 'test@gmail.com', $message1,'Test');

		$details1 = [
			'title' => 'You have received a new appointment from '.$fullname.' for '.$service->title,
			'body' => 'This is for testing email using smtp',
			'fullname' => 'Admin',
			'date' => $requestData['date'],
			'time' => $requestData['time'],
			'email'=> $email,
			'phone' => $phone,
			'description' => $description,
			'service'=> $service->title,
		];
		\Mail::to('test@gmail.com')->send(new \App\Mail\AppointmentMail($details1));

		$details = [
			'title' => 'You have booked an Appointment on '.$requestData['date'].'  at '.$requestData['time'],
			'body' => 'This is for testing email using smtp',
			'fullname' => $fullname,
			'date' => $requestData['date'],
			'time' => $requestData['time'],
			'email'=> $email,
			'phone' => $phone,
			'description' => $description,
			'service'=> $service->title,
		];

		\Mail::to($email)->send(new \App\Mail\AppointmentMail($details));

		//SMS to admin
		$receiver_number="+610422905860";
		// $receiver_number="+61476857122"; testing number
		$smsMessage="An appointment has been booked for $fullname on ".$requestData['date'].' at '.$requestData['time'];
		Helper::sendSms($receiver_number,$smsMessage);

			$message = 'Your appointment booked successfully on '.$requestData['date'].' '.$requestData['time'];
			return json_encode(array('success'=>true,'message'=>$message));
		}else{
			return json_encode(array('success'=>false));
		}
	}



    public function storepaid(Request $request)
    {
        $requestData = $request->all(); //dd($requestData);

        $service_id = $requestData['service_id'];
		$noe_id = $requestData['noe_id'];
		$fullname = $requestData['fullname'];
		$title = $requestData['title'];
		$description = $requestData['description'];
		$email = $requestData['email'];
		$phone = $requestData['phone'];
		$date = explode('/', $requestData['date']);
		$datey = $date[2].'-'.$date[1].'-'.$date[0];
		$service = \App\Models\BookService::find($requestData['service_id']); //dd($service);
        if(!empty($service)){
            $amount =  str_replace("$", "", $service['price']);
        } else {
            $amount = 1;
        }  //dd($amount);

        //Order insertion
        $cardName = $requestData['cardName'];
        $stripeToken = $requestData['stripeToken'];
        $currency = "usd";
        $payment_type = "stripe";
        $order_date = date("Y-m-d H:i:s");
        $order_status = "Pending";
        $ins = DB::table('tbl_paid_appointment_payment')->insert(
            [
                'order_hash' => $stripeToken,
                'payer_email' => $email,
                'amount' => $amount,
                'currency' => $currency,
                'payment_type' => $payment_type,
                'order_date' => $order_date,
                'order_status' => $order_status,
                'name' => $cardName
            ]
        );

        //stripe payment
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Stripe\Customer::create(array("email" => $email,"name" => $cardName,"source" => $stripeToken));

        $payment_result = Stripe\Charge::create ([
            "amount" => $amount * 100,
            "currency" => $currency,
            "customer" => $customer->id,
            "description" => "Paid To Test For  By $cardName",
        ]);
        //dd($payment_result);
        //update Order status
        if ( ! empty($payment_result) && $payment_result["status"] == "succeeded") { //success
            $stripe_payment_intent_id = $payment_result['id'];
            $payment_status = "Paid";
            $order_status = "Completed";

            DB::table('tbl_paid_appointment_payment')
            ->where('order_hash',$stripeToken)
            ->update(
                array(
                    'stripe_payment_intent_id'=>$stripe_payment_intent_id,
                    'payment_status'=>$payment_status,
                    'order_status'=>$order_status
                )
            );
        } else { //failed
            $stripe_payment_intent_id = $payment_result['id'];
            $payment_status = "Unpaid";
            $order_status = "Payement Failure";

            DB::table('tbl_paid_appointment_payment')
            ->where('order_hash',$stripeToken)
            ->update(
                array(
                    'stripe_payment_intent_id'=>$stripe_payment_intent_id,
                    'payment_status'=>$payment_status,
                    'order_status'=>$order_status
                )
            );
            return json_encode(array('success'=>false));
        }


        $user=\App\Models\Admin::where(function ($query) use($requestData){
			$query->where('email',$requestData['email'])
			->orWhere('phone',$requestData['phone']);
		})->first();
        $parts = explode(" ", $fullname);
        $last_name = array_pop($parts);
        $first_name = implode(" ", $parts);
        if(empty($user)){
			$objs				= 	new Admin;
			$objs->client_id	=	strtoupper($first_name).date('his');
			$objs->role	=	7;
			$objs->last_name	=	$last_name;
			$objs->first_name	=	$first_name;
			$objs->email	=	$email;
			$objs->phone	=	$phone;
			$objs->save();
			$client_id =$objs->id;
		}else{
			if(empty($user->client_id)){
				Admin::where('id', $user->id)->update(['client_id' => strtoupper($first_name).date('his')]);
			}
			$client_id = $user->id;
		}

		$obj = new Appointment;
		$obj->client_id = $client_id;
		$obj->service_id = $service_id;
		$obj->noe_id = $noe_id;
		$obj->full_name = $fullname;
		$obj->title = $title;
		$obj->description = $description;
		$obj->email =	$email;
		$obj->phone = $phone;
		$obj->date = $datey;
		if($requestData['time'] != ""){
			$time = explode('-', $requestData['time']);
			//echo "@@".date("H:i", strtotime($time[0])); die;
			$obj->time = date("H:i", strtotime($time[0]));
		}
		//$obj->time = $requestData['time'];
		$obj->timeslot_full = $requestData['time'];
		$obj->invites=0;
		$obj->appointment_details=$requestData['appointment_details'];
        $obj->order_hash = $stripeToken;
		$saved = $obj->save();

		if($saved)
        {
            $note = new \App\Models\Note;
            $note->client_id =  $client_id;
            $note->user_id = 1;
            $note->title = $requestData['appointment_details'];
            $note->description = $description;
            $note->mail_id = 0;
            $note->type = 'client';
            $saved = $note->save();

            $subject = 'added a note';
            $objs = new \App\Models\ActivitiesLog;
            $objs->client_id = $client_id;
            $objs->created_by = 1;
            $objs->description = '<span class="text-semi-bold">You have an new appointment on '.$requestData['date'].' at '.$requestData['time'].' for '.$title.'</span>';
            $objs->subject = $subject;
            $objs->save();

            $details1 = [
                'title' => 'You have received a new appointment from '.$fullname.' for '.$service->title,
                'body' => 'This is for testing email using smtp',
                'fullname' => 'Admin',
                'date' => $requestData['date'],
                'time' => $requestData['time'],
                'email'=> $email,
                'phone' => $phone,
                'description' => $description,
                'service'=> $service->title,
            ];
		    \Mail::to('test@gmail.com')->send(new \App\Mail\AppointmentMail($details1));

            $details = [
                'title' => 'You have booked an Appointment on '.$requestData['date'].'  at '.$requestData['time'],
                'body' => 'This is for testing email using smtp',
                'fullname' => $fullname,
                'date' => $requestData['date'],
                'time' => $requestData['time'],
                'email'=> $email,
                'phone' => $phone,
                'description' => $description,
                'service'=> $service->title,
            ];

            \Mail::to($email)->send(new \App\Mail\AppointmentMail($details));

            //SMS to admin
            $receiver_number="1234567";
            $smsMessage="An appointment has been booked for $fullname on ".$requestData['date'].' at '.$requestData['time'];
            Helper::sendSms($receiver_number,$smsMessage);

			$message = 'Your appointment booked successfully on '.$requestData['date'].' '.$requestData['time'];
			return json_encode(array('success'=>true,'message'=>$message));

		} else {
			return json_encode(array('success'=>false));
		}
	}

}

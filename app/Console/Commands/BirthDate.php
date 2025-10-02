<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\EmailTemplate;

 use PDF;
 use DateTime;
 use App\Mail\CommonMail;
use App\Mail\InvoiceEmailManager;
use Mail;
use Config;
class BirthDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BirthDate:birthdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Name Change Successfully';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$query 		= Contact::where('id', '!=', '')->with(['company']);
		$totalco = $query->count();
		
		$lists = $query->get();
		if($totalco !== 0){
			foreach($lists as $contact){
				$today = date('Y-m-d');
				$count = 2;
				$datetime1 = new DateTime($today);
				$datetime2 = new DateTime($contact->birth_date);
				$interval = $datetime1->diff($datetime2);
				$diff = $interval->format('%a');
				 if(strtotime($today) == strtotime($contact->birth_date)){
				 $replace = array('{sur_name}', '{customer_name}', '{company_name}', '{company_logo}','{company_email}');		//echo \URL::to('/').'/public/img/profile_imgs/'.@$contact->company->profile_img; die;			
					$replace_with = array(@$contact->srname, @$contact->first_name.' '.@$contact->last_name, @$contact->company->company_name, \URL::to('/').'/public/img/profile_imgs/'.@$contact->company->profile_img, @$contact->company->email);
				
				 $replacesub = array('{sur_name}', '{customer_name}');					
				$replace_with_sub = array(@$contact->srname, @$contact->first_name.' '.@$contact->last_name);
				
				$emailtemplate	= 	DB::table('email_templates')->where('alias', 'birthday-wish')->first();
				$subContent 	= 	$emailtemplate->subject;
				$subContent	=	str_replace($replacesub,$replace_with_sub,$subContent);
				
					$issuccess = $this->send_email_template($replace, $replace_with, 'birthday-wish', $contact->contact_email,$subContent,'info@crm.travelsdata.com'); 
				}else{}
				
				
				/*Aversery*/
				$todayav = date('Y-m-d');
				$count = 2;
				$datetime11 = new DateTime($todayav);
				$datetime21 = new DateTime($contact->anniversary_date);
				$interval1 = $datetime11->diff($datetime21);
				$diff2 = $interval1->format('%a');
				 if(strtotime($todayav) == strtotime($contact->anniversary_date)){
				 $replaceav = array('{sur_name}', '{customer_name}', '{company_name}', '{company_logo}','{company_email}');		//echo \URL::to('/').'/public/img/profile_imgs/'.@$contact->company->profile_img; die;			
					$replace_withav = array(@$contact->srname, @$contact->first_name.' '.@$contact->last_name, @$contact->company->company_name, \URL::to('/').'/public/img/profile_imgs/'.@$contact->company->profile_img, @$contact->company->email);
				
				 $replacesubav = array('{surname}', '{customer_name}');					
				$replace_with_subav = array(@$contact->srname, @$contact->first_name.' '.@$contact->last_name);
				
				$emailtemplate	= 	DB::table('email_templates')->where('alias', 'anniversary-wish')->first();
				$subContentav 	= 	$emailtemplate->subject;
				$subContent	=	str_replace($replacesubav,$replace_with_subav,$subContentav);
				
					$issuccess = $this->send_email_template($replaceav, $replace_withav, 'anniversary-wish', $contact->contact_email,$subContent,'info@crm.travelsdata.com'); 
				}else{}
				/*Aversery*/
			}
			
		}
		
        $this->info('Test has fired.');
    }
	
	protected function send_email_template($replace = array(), $replace_with = array(), $alias = null, $to = null, $subject = null, $sender = null) 
	{
		$email_template	= 	DB::table('email_templates')->where('alias', $alias)->first();
		$emailContent 	= 	$email_template->description;
		$emailContent	=	str_replace($replace,$replace_with,$emailContent);
		if($subject == NULL)
		{
			$subject		=	$subject;	
		}	
		$explodeTo = explode(';', $to);//for multiple and single to
		Mail::to($explodeTo)->send(new CommonMail($emailContent, $subject, $sender));
	
		// check for failures
		if (Mail::failures()) {
			return false;
		}

		// otherwise everything is okay ...
		return true;
		
	}
}
?>
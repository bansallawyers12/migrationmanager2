<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
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
		// Contact functionality has been removed
		$this->info('Contact functionality has been removed from the system.');
		return;
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
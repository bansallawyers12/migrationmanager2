<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Admin;
// use App\Models\EmailTemplate; // REMOVED: email_templates table has been deleted
// use App\Models\InvoiceFollowup; // REMOVED: InvoiceFollowup model has been deleted
// use App\Models\ShareInvoice; // REMOVED: ShareInvoice model has been deleted
 use PDF;
 use DateTime;
 use App\Mail\CommonMail;
use App\Mail\InvoiceEmailManager;
use Mail;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJob:cronjob';

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
		// Invoice-related functionality disabled - invoice_payments, invoice_details, and invoices tables have been deleted
		// This cron job previously handled invoice reminder emails for invoices due in 2 days or on due date
		// All invoice model references (Invoice, InvoicePayment, InvoiceDetail) have been removed
		
		/* DISABLED - Invoice reminder functionality
		$query 		= Invoice::where('status', '!=', 1)->with(['customer','company']);
		$totalco = $query->count();
		
		$lists = $query->get();
		if($totalco !== 0){
			foreach($lists as $invoice){
				$today = date('Y-m-d');
				$count = 2;
				$datetime1 = new DateTime($today);
				$datetime2 = new DateTime($invoice->due_date);
				$interval = $datetime1->diff($datetime2);
				$diff = $interval->format('%a');
				if($diff == '2'){
					$amount_rec = InvoicePayment::where('invoice_id',$invoice->id)->get()->sum("amount_rec");
				$baldue = $invoice->amount - $amount_rec;
				$currency_sign = '$'; // Default currency symbol
				 $replace = array('{customer_name}', '{invoice_no}', '{invoice_date}', '{due_date}','{amount}','{company_name}');					
					$replace_with = array(@$invoice->customer->first_name.' '.@$invoice->customer->last_name, @$invoice->invoice,@$invoice->invoice_date, @$invoice->due_date, $currency_sign.$baldue, @$invoice->company->company_name);
				
				 $replacesub = array('{due_amount}', '{invoice_no}');					
				$replace_with_sub = array($currency_sign.$baldue, @$invoice->invoice);
				
				$emailtemplate	= 	DB::table('email_templates')->where('alias', 'invoice-reminder')->first();
				$subContent 	= 	$emailtemplate->subject;
				$subContent	=	str_replace($replacesub,$replace_with_sub,$subContent);
				
					//Attachment start
					$invoicedetail = Invoice::find($invoice->id);
					$invoicefilename = $invoicedetail->invoice.'-'.$invoicedetail->id.'.pdf';

					$pdf = PDF::setOptions([
					'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
					'logOutputFile' => storage_path('logs/log.htm'),
					'tempDir' => storage_path('logs/')
					])->loadView('invoices.invoice', compact('invoicedetail'));
					$output = $pdf->output();

					file_put_contents('public/invoices/'.$invoicefilename, $output);

					$array['view'] = 'emails.invoice';
					$array['file'] = 'public/invoices/'.$invoicefilename;
					$array['file_name'] = $invoicefilename;

					//sends email to customer with the invoice pdf attached
					$issuccess = self::send_attachment_email_template($array, $replace, $replace_with, 'invoice-reminder', $invoice->customer->contact_email, $subContent, config('mail.from.address'));
					unlink($array['file']);
					$objf				= 	new InvoiceFollowup;
				$objf->invoice_id	=	$invoice->id;
				$objf->user_id	=	$invoice->user_id;
				$objf->followup_type	=	'invoice_email';
				$objf->comment	=	"Payment reminder sent to ".$invoice->customer->contact_email;
				$followupsaved				=	$objf->save(); 
				}else if(strtotime($today) == strtotime($invoice->due_date)){
					$amount_rec = InvoicePayment::where('invoice_id',$invoice->id)->get()->sum("amount_rec");
				$baldue = $invoice->amount - $amount_rec;
				$currency_sign = '$'; // Default currency symbol
				 $replace = array('{customer_name}', '{invoice_no}', '{invoice_date}', '{due_date}','{amount}','{company_name}');					
					$replace_with = array(@$invoice->customer->first_name.' '.@$invoice->customer->last_name, @$invoice->invoice,@$invoice->invoice_date, @$invoice->due_date, $currency_sign.$baldue, @$invoice->company->company_name);
				
				 $replacesub = array('{due_amount}', '{invoice_no}');					
				$replace_with_sub = array($currency_sign.$baldue, @$invoice->invoice);
				
				$emailtemplate	= 	DB::table('email_templates')->where('alias', 'invoice-reminder')->first();
				$subContent 	= 	$emailtemplate->subject;
				$subContent	=	str_replace($replacesub,$replace_with_sub,$subContent);
				
					//Attachment start
					$invoicedetail = Invoice::find($invoice->id);
					$invoicefilename = $invoicedetail->invoice.'-'.$invoicedetail->id.'.pdf';

					$pdf = PDF::setOptions([
					'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
					'logOutputFile' => storage_path('logs/log.htm'),
					'tempDir' => storage_path('logs/')
					])->loadView('invoices.invoice', compact('invoicedetail'));
					$output = $pdf->output();

					file_put_contents('public/invoices/'.$invoicefilename, $output);

					$array['view'] = 'emails.invoice';
					$array['file'] = 'public/invoices/'.$invoicefilename;
					$array['file_name'] = $invoicefilename;

					//sends email to customer with the invoice pdf attached
					$issuccess = self::send_attachment_email_template($array, $replace, $replace_with, 'invoice-reminder', $invoice->customer->contact_email, $subContent, config('mail.from.address'));
					unlink($array['file']);
					$objf				= 	new InvoiceFollowup;
				$objf->invoice_id	=	$invoice->id;
				$objf->user_id	=	$invoice->user_id;
				$objf->followup_type	=	'invoice_email';
				$objf->comment	=	"Payment reminder sent to ".$invoice->customer->contact_email;
				$followupsaved				=	$objf->save(); 
				}else{}
			}
			
		}
		*/
		/* \DB::table('users')
            ->where('id', 1)
            ->update(['course_level' => str_random(10)]);
         Mail::send('emails.test', [], function($message)
        {
            $message->to('pankaj95.mca10.lgc@gmail.com', 'John Doe')->subject('Test');
        }); */
 
        $this->info('Test has fired.');
    }
	
	public static function send_attachment_email_template($invoicearray, $replace = array(), $replace_with = array(), $alias = null, $to = null, $subject = null, $sender = null) 
	{
		// email_templates table has been deleted - using fallback content
		$email_template	= 	DB::table('email_templates')->where('alias', $alias)->first();
		if(!$email_template) {
			\Log::warning('Email template not found for alias: ' . $alias . ' - email_templates table has been deleted');
			// Use a simple fallback email content
			$emailContent = 'Email template content is no longer available. Please contact support.';
		} else {
			$emailContent 	= 	$email_template->description;
			$emailContent	=	str_replace($replace,$replace_with,$emailContent);
		}
		if($subject == NULL)
		{
			$subject		=	$subject;	
		}	
		try {
			$explodeTo = explode(';', $to);//for multiple and single to
			$invoicearray['subject'] = $subject;
			$invoicearray['from'] = $sender;
			$invoicearray['content'] = $emailContent;
			Mail::to($explodeTo)->queue(new InvoiceEmailManager($invoicearray));
			
			return true;
		} catch (\Exception $e) {
			\Log::error('Email sending failed in CronJob: ' . $e->getMessage());
			return false;
		}
		
	}
}
?>
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

use App\Admin;
use App\Invoice;
use App\InvoiceDetail;
use App\InvoicePayment;
use App\ScheduleItem;
use App\InvoiceFollowup;
use App\EmailTemplate;
use App\ShareInvoice;
use App\InvoiceSchedule;
use App\TaxRate;
use App\Currency;
use App\Contact;
use App\AttachFile;
 use PDF;
use Auth;
use Config;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function createInvoice(Request $request){

		$d = '';
		if(isset($request->schedule_id)){
			$d = '?sch_id='.$request->schedule_id;
		}
		return Redirect::to('/admin/application/invoice/'.$request->client_id.'/'.$request->application.'/'.$request->invoice_type.$d);
	}
	public function getInvoice(Request $request, $clientid, $applicationid, $type){
		// dd($type);
		$clientdata = \App\Admin::where('role', 7)->where('id', $clientid)->first();
		if($type == 3){
            $workflowdaa = \App\Workflow::where('id', $applicationid)->first();
			return view('Admin.invoice.general-invoice',compact(['clientid','applicationid','type','clientdata','workflowdaa']));
		} else {
            $applicationdata = \App\Application::where('id', $applicationid)->first();
            $partnerdata = \App\Partner::where('id', @$applicationdata->partner_id)->first();
            $productdata = \App\Product::where('id', @$applicationdata->product_id)->first();
            $branchdata = \App\PartnerBranch::where('id', @$applicationdata->branch)->first();
            $workflowdaa = \App\Workflow::where('id', @$applicationdata->workflow)->first();
			return view('Admin.invoice.commission-invoice',compact(['clientid','applicationid','type','applicationdata','partnerdata','workflowdaa','clientdata','productdata','branchdata']));
		}
    }

	public function unpaid(Request $request)
	{

		$query 		= Invoice::where('status', '=', 0);
		$lists		= $query->orderby('id','desc')->paginate(20);
		return view('Admin.invoice.unpaid',compact(['lists']));

	}

	public function paid(Request $request)
	{

		$query 		= Invoice::where('status', '=', 1);
		$lists		= $query->orderby('id','desc')->paginate(20);
		return view('Admin.invoice.paid',compact(['lists']));

	}



	public function show(Request $request, $id){
		if(Invoice::where('id', '=', $id)->exists())
		{
			$invoicedetail = Invoice::where('id', '=', $id)->first();
			if($invoicedetail->type == 3){
				$workflowdaa = \App\Workflow::where('id', $invoicedetail->application_id)->first();
				$applicationdata = array();
				$partnerdata = array();
				$productdata = array();
				$branchdata = array();
			}else{
				$applicationdata = \App\Application::where('id', $invoicedetail->application_id)->first();
				$partnerdata = \App\Partner::where('id', @$applicationdata->partner_id)->first();
				$productdata = \App\Product::where('id', @$applicationdata->product_id)->first();
				$branchdata = \App\PartnerBranch::where('id', @$applicationdata->branch)->first();
				$workflowdaa = \App\Workflow::where('id', @$applicationdata->workflow)->first();
			}

			$clientdata = \App\Admin::where('role', 7)->where('id', $invoicedetail->client_id)->first();
			$admindata = \App\Admin::where('role', 1)->where('id', $invoicedetail->user_id)->first();


			return view('Admin.invoice.show',compact(['applicationdata','partnerdata','workflowdaa','clientdata','productdata','branchdata','invoicedetail','admindata']));
		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}
	}

	public function invoicepaymentstore(Request $request){
		$requestData 		= 	$request->all();
		$invoicedetail = \App\Invoice::where('id', $requestData['invoice_id'])->first();
		$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $requestData['invoice_id'])->orderby('id','ASC')->get();
		$coom_amt = 0;
		$total_fee = 0;
		$netamount = 0;
		foreach($invoiceitemdetails as $invoiceitemdetail){
			$coom_amt += $invoiceitemdetail->comm_amt;
			$total_fee += $invoiceitemdetail->total_fee;
			$netamount += $invoiceitemdetail->netamount;
		}
		$paymentdetails = \App\InvoicePayment::where('invoice_id', $requestData['invoice_id'])->orderby('created_at', 'DESC')->get();
		$amount_rec = 0;
		foreach($paymentdetails as $paymentdetail){
			$amount_rec += $paymentdetail->amount_rec;
		}
		if($invoicedetail->type == 2){
			$totaldue = $coom_amt - $amount_rec;
		}else if($invoicedetail->type == 3){
			$totaldue = $netamount - $amount_rec;
		}else{
			$feepaid = $total_fee - $coom_amt;
			$totaldue = $feepaid - $amount_rec;
		}
		$payment_amount = 0;
		for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
			$payment_amount += @$requestData['payment_amount'][$ia];
		}
	//echo $payment_amount; die;
		if($payment_amount > $totaldue){
			if($request->is_ajax == 'true'){
				$response['status'] 	= 	false;
				$response['message']	=	'Amount should be less than total due';
				echo json_encode($response);
			}else{
				return redirect()->back()->with('error', 'Amount should be less than total due');
			}
		}

		for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
				$objf				= 	new InvoicePayment;
				$objf->invoice_id	=	$requestData['invoice_id'];
				$objf->amount_rec	=	@$requestData['payment_amount'][$ia];
				$objf->payment_date	=	@$requestData['payment_date'][$ia];
				$objf->payment_mode	=	@$requestData['payment_mode'][$ia];

				$followupsaved				=	$objf->save();
			}

		$invoicedetail = \App\Invoice::where('id', $requestData['invoice_id'])->first();
		$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $requestData['invoice_id'])->orderby('id','ASC')->get();
		$coom_amt = 0;
		$total_fee = 0;
		$netamount = 0;
		foreach($invoiceitemdetails as $invoiceitemdetail){
			$coom_amt += $invoiceitemdetail->comm_amt;
			$total_fee += $invoiceitemdetail->total_fee;
			$netamount += $invoiceitemdetail->netamount;
		}
		$paymentdetails = \App\InvoicePayment::where('invoice_id', $requestData['invoice_id'])->orderby('created_at', 'DESC')->get();
		$amount_rec = 0;
		foreach($paymentdetails as $paymentdetail){
			$amount_rec += $paymentdetail->amount_rec;
		}
		if($invoicedetail->type == 2){
			$totaldue = $coom_amt - $amount_rec;
		}else if($invoicedetail->type == 3){
			$totaldue = $netamount - $amount_rec;
		}else{
			$feepaid = $total_fee - $coom_amt;
			$totaldue = $feepaid - $amount_rec;
		}
		if($totaldue == 0){
			$obj = \App\Invoice::find($requestData['invoice_id']);
			$obj->status = 1;
			$obj->save();
		}
		if($followupsaved){
			if($request->is_ajax == 'true'){
				$response['status'] 	= 	true;
				$response['message']	=	'Payment Saved successfully';
				echo json_encode($response);
			}else{
				return redirect()->back()->with('success', 'Payment Saved successfully');
			}
		}else{
			if($request->is_ajax == 'true'){
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
				echo json_encode($response);
			}else{
				return redirect()->back()->with('error', 'Please try again');
			}
		}
	}

	public function getinvoices(Request $request){
		$client_id = $request->clientid;

		$invoicelists = \App\Invoice::where('client_id',$client_id)->orderby('created_at','DESC')->get();
		ob_start();
		foreach($invoicelists as $invoicelist){
			if($invoicelist->type == 3){
				$workflowdaa = \App\Workflow::where('id', $invoicelist->application_id)->first();
			}else{
				$applicationdata = \App\Application::where('id', $invoicelist->application_id)->first();
				$workflowdaa = \App\Workflow::where('id', $invoicelist->application_id)->first();
				$partnerdata = \App\Partner::where('id', $applicationdata->partner_id)->first();
			}
			$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $invoicelist->id)->orderby('id','ASC')->get();
			$netamount = 0;
			$coom_amt = 0;
			$total_fee = 0;
			foreach($invoiceitemdetails as $invoiceitemdetail){
				$netamount += $invoiceitemdetail->netamount;
				$coom_amt += $invoiceitemdetail->comm_amt;
				$total_fee += $invoiceitemdetail->total_fee;
			}

			$paymentdetails = \App\InvoicePayment::where('invoice_id', $invoicelist->id)->orderby('created_at', 'DESC')->get();
			$amount_rec = 0;
			foreach($paymentdetails as $paymentdetail){
				$amount_rec += $paymentdetail->amount_rec;
			}
			if($invoicelist->type == 1){
				$totaldue = $total_fee - $coom_amt;
			} if($invoicelist->type == 2){
				$totaldue = $netamount - $amount_rec;
			}else{
				$totaldue = $netamount - $amount_rec;
			}
			?>
			<tr id="iid_<?php echo $invoicelist->id; ?>">
				<td><?php echo $invoicelist->id; ?></td>
				<td><?php echo $invoicelist->invoice_date; ?>
				<?php if($invoicelist->type == 1){
					$rtype = 'Net Claim';
				}else if($invoicelist->type == 2){
					$rtype = 'Gross Claim';
				}else{
					$rtype = 'General';
				} ?>
				<span title="<?php echo $rtype; ?>" class="ui label zippyLabel"><?php echo $rtype; ?></span></td>
				<td><?php echo $workflowdaa->name; ?><br><?php echo @$partnerdata->partner_name; ?></td>
				<td>AUD <?php echo $invoicelist->net_fee_rec; ?></td>
				<td><?php echo $invoicelist->discount; ?></td>
				<td>-</td>
				<td>
				<?php if($invoicelist->status == 1){ ?>
					<span class="ag-label--circular" style="color: #6777ef" >Paid</span></td>
				<?php }else{  ?>
					<span class="ag-label--circular" style="color: #ed5a5a" >UnPaid</span></td>
				<?php }  ?>
				<td>
					<div class="dropdown d-inline">
						<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
						<div class="dropdown-menu">
							<a class="dropdown-item has-icon" href="#">Send Email</a>
							<a target="_blank" class="dropdown-item has-icon" href="<?php echo \URL::to('admin/invoice/view/'); ?>/<?php echo $invoicelist->id; ?>">View</a>
							<?php if($invoicelist->status == 0){ ?>
							<a target="_blank" class="dropdown-item has-icon" href="<?php echo \URL::to('admin/invoice/edit/'); ?>/<?php echo $invoicelist->id; ?>">Edit</a>
							<a data-netamount="<?php echo $netamount; ?>" data-dueamount="<?php echo $totaldue; ?>" data-invoiceid="<?php echo $invoicelist->id; ?>" class="dropdown-item has-icon addpaymentmodal" href="javascript:;"> Make Payment</a>
							<?php } ?>
						</div>
					</div>
				</td>
			</tr>
			<?php
		}
		return ob_get_clean();
	}
	public function store(Request $request)
	{
        $userid = Auth::user()->id;
        $requestData 		= 	$request->all(); //dd($requestData);

		$subtotal = 0;
		$total_value = 0;
		/* Profile Image Upload Function Start */
		$files = $request->file('attachfile');
		if($request->hasfile('attachfile'))
		{
            $attachfile = array();
			foreach ($files as $file) {
				$attachfile[] = $this->uploadFile($file, Config::get('constants.invoice'));
			}
		}
		else
		{
			//$attachfile = NULL;
            $attachfile = array();
		}
		/* Profile Image Upload Function End */
		$profiledetail = \App\Profile::where('id', @$requestData['profile'])->first();
		$pdetail = '';
		if($profiledetail){
			$ps = array(
				'name'=> $profiledetail->company_name,
				'address'=> $profiledetail->address,
				'phone'=> $profiledetail->phone,
				'other_phone'=> $profiledetail->other_phone,
				'email'=> $profiledetail->email,
				'website'=> $profiledetail->website,
				'logo'=> $profiledetail->logo,
				'id'=> $profiledetail->id,
				'abn'=> $profiledetail->abn,
			);
			$pdetail = json_encode($ps);
		}
		$obj						= 	new Invoice;
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['applicationid'];
		$obj->type					=	@$requestData['type'];
		$obj->invoice_date			=	@$requestData['invoice_date'];
		$obj->due_date				=	@$requestData['invoice_due_date'];
		$obj->discount				=	@$requestData['discount'];
		$obj->discount_date			=	@$requestData['discount_date'];
		if($requestData['type'] == 2){
			$obj->net_fee_rec			=	@$requestData['invoice_net_revenue'];
		}else{
			$obj->net_fee_rec			=	@$requestData['invoice_net_amount'];
			$obj->net_incone			=	@$requestData['invoice_net_income'];
		}


		$obj->notes					=	@$requestData['notes'];
		$obj->profile				=	@$pdetail;
		$obj->payment_option		=	@$requestData['paymentoption'];

		$obj->attachments			=	implode(",",$attachfile);

		if( isset( $requestData['payment_done'] ) && $requestData['payment_done'] == 'on'){
		    $obj->status	=  1;
		} else {
		    $obj->status	=  0;
		}

		$saved				=	$obj->save();



        /* Update Client detail start*/
        $obj_client				=   \App\Admin::find(@$requestData['client_id']);
        $obj_client->first_name	=	@$requestData['first_name'];
        $obj_client->last_name	=	@$requestData['last_name'];

        $dob = '';
        if($requestData['dob'] != ''){
            $dobs = explode('/', $requestData['dob']);
            $dob = $dobs[2].'-'.$dobs[1].'-'. $dobs[0];
        }
        $obj_client->dob =	@$dob;
        $saved_client	 =	$obj_client->save();
        /* Update Client detail end*/


        for($i =0; $i<count(@$requestData['total_fee']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new InvoiceDetail;
			$invoicedetail->invoice_id 		= $obj->id;
			$invoicedetail->description		= @$requestData['description'][$i];
			$invoicedetail->total_fee 		= @$requestData['total_fee'][$i];
			$invoicedetail->comm_per 		= @$requestData['comm_per'][$i];
			$invoicedetail->comm_amt 		= @$requestData['comm_amt'][$i];
			$invoicedetail->tax 			= @$requestData['tax'][$i];
			$invoicedetail->tax_amount 		= @$requestData['tax_amount'][$i];
			$invoicedetail->bonus_amount 		= @$requestData['bonus_amount'][$i];
			$invoicedetail->netamount 		= @$requestData['netamount'][$i];
			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{

			if(isset($request->share_user) && $request->share_user != 'no' && $request->incomeshare_amount != ''){
				if(\App\IncomeSharing::where('invoice_id',$obj->id)->exists()){
					$IncomeSharing = \App\IncomeSharing::where('invoice_id',$obj->id)->first();
					$oshare = \App\IncomeSharing::find($IncomeSharing->id);
				}else{
					$oshare = new \App\IncomeSharing;
					$oshare->invoice_id = $obj->id;
				}
					$oshare->rec_id = $request->share_user;
					$oshare->amount = $request->incomeshare_amount;
					if(isset($request->taxval)){
						$oshare->is_tax = 1;
					}else{
						$oshare->is_tax = '';
					}
					$oshare->tax = '';
					$oshare->save();
			}else{

			}

			for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
				$objf				= 	new InvoicePayment;
				$objf->invoice_id	=	$obj->id;
				$objf->amount_rec	=	@$requestData['payment_amount'][$ia];
				$objf->payment_date	=	@$requestData['payment_date'][$ia];
				$objf->payment_mode	=	@$requestData['payment_mode'][$ia];

				$followupsaved				=	$objf->save();
			}



			$invoicedetail = \App\Invoice::where('id',$obj->id)->first();
		$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $obj->id)->orderby('id','ASC')->get();
		$coom_amt = 0;
		$total_fee = 0;
		$netamount = 0;
		foreach($invoiceitemdetails as $invoiceitemdetail){
			$coom_amt += $invoiceitemdetail->comm_amt;
			$total_fee += $invoiceitemdetail->total_fee;
			$netamount += $invoiceitemdetail->netamount;
		}
		$paymentdetails = \App\InvoicePayment::where('invoice_id',$obj->id)->orderby('created_at', 'DESC')->get();
		$amount_rec = 0;
		foreach($paymentdetails as $paymentdetail){
			$amount_rec += $paymentdetail->amount_rec;
		}
		if($invoicedetail->type == 2){
			$totaldue = $coom_amt - $amount_rec;
		}else if($invoicedetail->type == 3){
			$totaldue = $netamount - $amount_rec;
		}else{
			$feepaid = $total_fee - $coom_amt;
			$totaldue = $feepaid - $amount_rec;
		}

			$objsss = \App\Invoice::find($obj->id);
			$objsss->invoice_no = date('Y').'/'.date('m').'/'.$obj->id;
			$objsss->save();
		if($totaldue == 0){
			$objss = \App\Invoice::find($obj->id);
			$objss->status = 1;
			$objss->save();
		}

			if(@$requestData['btn'] == 'savepreview'){
				return Redirect::to('/admin/invoice/view/'.@$obj->id)->with('success', 'Invoice saved Successfully');
			}
			else{
				return Redirect::to('/admin/invoice/unpaid/')->with('success', 'Invoice saved Successfully');

			}
		}
	}

	public function updatecominvoices(Request $request)
	{
		$userid = Auth::user()->id;

		$requestData 		= 	$request->all();
		//echo '<pre>'; print_r($requestData); die;
		$subtotal = 0;
		$total_value = 0;
		//echo Config::get('constants.invoice'); die;
		/* Profile Image Upload Function Start */
			$files = $request->file('attachfile');
			if($request->hasfile('attachfile'))
			{
				foreach ($files as $file) {
					$attachfile[] = $this->uploadFile($file, Config::get('constants.invoice'));
				}

				$old_images=explode(",",$requestData['old_attachments']);

				foreach($old_images as $image){
					$image_path = public_path("/img/invoice/{$image}");
					if (File::exists($image_path)) {
						File::delete($image_path);
						// unlink($image_path);
					}
				}

			}
			else
			{
				$attachfile[] = @$requestData['old_attachments'];
			}

		$profiledetail = \App\Profile::where('id', @$requestData['profile'])->first();
		$pdetail = '';
		if($profiledetail){
			$ps = array(
				'name'=> $profiledetail->company_name,
				'address'=> $profiledetail->address,
				'phone'=> $profiledetail->phone,
				'other_phone'=> $profiledetail->other_phone,
				'email'=> $profiledetail->email,
				'website'=> $profiledetail->website,
				'logo'=> $profiledetail->logo,
				'id'=> $profiledetail->id,
				'abn'=> $profiledetail->abn,
			);
			$pdetail = json_encode($ps);
		}
		/* Profile Image Upload Function End */
		$obj						= 	Invoice::find($requestData['id']);
		$obj->user_id				=	Auth::user()->id;
		//$obj->invoice_no = date('Y').'/'.date('m').'/'.$requestData['id'];
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['applicationid'];
		$obj->type					=	@$requestData['type'];
		$obj->invoice_date			=	@$requestData['invoice_date'];
		$obj->due_date				=	@$requestData['invoice_due_date'];
		$obj->discount				=	@$requestData['discount'];
		$obj->discount_date			=	@$requestData['discount_date'];
		if($requestData['type'] == 2){
			$obj->net_fee_rec			=	@$requestData['invoice_net_revenue'];
		}else{
			$obj->net_fee_rec			=	@$requestData['invoice_net_amount'];
			$obj->net_incone			=	@$requestData['invoice_net_income'];
		}

		$obj->profile				=	@$pdetail;
		$obj->notes					=	@$requestData['notes'];
		$obj->payment_option		=	@$requestData['paymentoption'];

		$obj->attachments			=	implode(",",$attachfile);
		$obj->status				=	0;

		$saved				=	$obj->save();
		$res= InvoiceDetail::where('invoice_id',$obj->id)->delete();
		for($i =0; $i<count(@$requestData['total_fee']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new InvoiceDetail;
			$invoicedetail->invoice_id 		= $obj->id;
			$invoicedetail->description		= @$requestData['description'][$i];
			$invoicedetail->total_fee 		= @$requestData['total_fee'][$i];
			$invoicedetail->comm_per 		= @$requestData['comm_per'][$i];
			$invoicedetail->comm_amt 		= @$requestData['comm_amt'][$i];
			$invoicedetail->tax 			= @$requestData['tax'][$i];
			$invoicedetail->bonus_amount 		= @$requestData['bonus_amount'][$i];
			$invoicedetail->tax_amount 		= @$requestData['tax_amount'][$i];
			$invoicedetail->netamount 		= @$requestData['netamount'][$i];
			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{
			if($request->share_user != 'no' && $request->incomeshare_amount != ''){
				if(\App\IncomeSharing::where('invoice_id',$obj->id)->exists()){
					$IncomeSharing = \App\IncomeSharing::where('invoice_id',$obj->id)->first();
					$oshare = \App\IncomeSharing::find($IncomeSharing->id);
				}else{
					$oshare = new \App\IncomeSharing;
					$oshare->invoice_id = $obj->id;
				}
					$oshare->rec_id = $request->share_user;
					$oshare->amount = $request->incomeshare_amount;
					if(isset($request->taxval)){
						$oshare->is_tax = 1;
					}else{
						$oshare->is_tax = '';
					}
					$oshare->tax = '';
					$oshare->save();
			}

			if($request->share_user == 'no'){
				if(\App\IncomeSharing::where('invoice_id',$obj->id)->exists()){
					\App\IncomeSharing::where('invoice_id',$obj->id)->delete();
				}
			}

	$res= InvoicePayment::where('invoice_id',$obj->id)->delete();
			for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
				$objf				= 	new InvoicePayment;
				$objf->invoice_id	=	$obj->id;

				$objf->amount_rec	=	@$requestData['payment_amount'][$ia];
				$objf->payment_date	=	@$requestData['payment_date'][$ia];
				$objf->payment_mode	=	@$requestData['payment_mode'][$ia];

				$followupsaved				=	$objf->save();
			}

			$invoicedetail = \App\Invoice::where('id',$obj->id)->first();
		$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $obj->id)->orderby('id','ASC')->get();
		$coom_amt = 0;
		$total_fee = 0;
		$netamount = 0;
		foreach($invoiceitemdetails as $invoiceitemdetail){
			$coom_amt += $invoiceitemdetail->comm_amt;
			$total_fee += $invoiceitemdetail->total_fee;
			$netamount += $invoiceitemdetail->netamount;
		}
		$paymentdetails = \App\InvoicePayment::where('invoice_id',$obj->id)->orderby('created_at', 'DESC')->get();
		$amount_rec = 0;
		foreach($paymentdetails as $paymentdetail){
			$amount_rec += $paymentdetail->amount_rec;
		}
		if($invoicedetail->type == 2){
			$totaldue = $coom_amt - $amount_rec;
		}else if($invoicedetail->type == 3){
			$totaldue = $netamount - $amount_rec;
		}else{
			$feepaid = $total_fee - $coom_amt;
			$totaldue = $feepaid - $amount_rec;
		}

		if($totaldue == 0){
			$objss = \App\Invoice::find($obj->id);
			$objss->status = 1;
			$objss->save();
		}

			if(@$requestData['btn'] == 'savepreview'){
				return Redirect::to('/admin/invoice/view/'.@$obj->id)->with('success', 'Invoice saved Successfully');
			}
			else{
				return Redirect::to('/admin/invoice/unpaid/')->with('success', 'Invoice updated Successfully');
			}
		}
	}

	public function generalStore(Request $request)
	{

		$userid = Auth::user()->id;

		$requestData 		= 	$request->all();
		//echo '<pre>'; print_r($requestData); die;
		$subtotal = 0;
		$total_value = 0;
		/* Profile Image Upload Function Start */
		$files = $request->file('attachfile');
		if($request->hasfile('attachfile'))
		{
			foreach ($files as $file) {
				$attachfile[] = $this->uploadFile($file, Config::get('constants.invoice'));
			}
		}
		else
		{
			$attachfile = NULL;
		}
		/* Profile Image Upload Function End */
		$profiledetail = \App\Profile::where('id', @$requestData['profile'])->first();
		$pdetail = '';
		if($profiledetail){
			$ps = array(
				'name'=> $profiledetail->company_name,
				'address'=> $profiledetail->address,
				'phone'=> $profiledetail->phone,
				'other_phone'=> $profiledetail->other_phone,
				'email'=> $profiledetail->email,
				'website'=> $profiledetail->website,
				'logo'=> $profiledetail->logo,
				'id'=> $profiledetail->id,
				'abn'=> $profiledetail->abn,
			);
			$pdetail = json_encode($ps);
		}
		$obj						= 	new Invoice;
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['applicationid'];
		$obj->type					=	@$requestData['type'];
		$obj->invoice_date			=	@$requestData['invoice_date'];
		$obj->due_date				=	@$requestData['invoice_due_date'];
		$obj->currency			=	@$requestData['currency'];
		$obj->net_fee_rec			=	@$requestData['invoice_net_amount'];
		$obj->net_incone			=	@$requestData['invoice_net_income'];
		$obj->notes					=	@$requestData['notes'];
		$obj->payment_option		=	@$requestData['paymentoption'];
		$obj->attachments			=	implode(",",$attachfile);
		$obj->status				=	0;
		$obj->profile				=	@$pdetail;
		$saved				=	$obj->save();
		for($i =0; $i<count(@$requestData['amount']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new InvoiceDetail;
			$invoicedetail->invoice_id 		= $obj->id;
			$invoicedetail->description		= @$requestData['description'][$i];
			$invoicedetail->income_type		= @$requestData['income_type'][$i];
			$invoicedetail->total_fee 		= @$requestData['amount'][$i];

			$invoicedetail->tax 			= @$requestData['tax_code'][$i];
			$invoicedetail->tax_amount 		= @$requestData['tax_amt'][$i];
			$invoicedetail->netamount 		= @$requestData['total_amt'][$i];
			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{
			for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
				$objf				= 	new InvoicePayment;
				$objf->invoice_id	=	$obj->id;
				$objf->amount_rec	=	@$requestData['payment_amount'][$ia];
				$objf->payment_date	=	@$requestData['payment_date'][$ia];
				$objf->payment_mode	=	@$requestData['payment_mode'][$ia];

				$followupsaved				=	$objf->save();
			}

			if(@$requestData['btn'] == 'savepreview'){
				return Redirect::to('/admin/invoice/view/'.@$obj->id)->with('success', 'Invoice saved Successfully');
			}
			else{
				return Redirect::to('/admin/invoice/unpaid/')->with('success', 'Invoice updated Successfully');
			}
		}
	}

	public function updategeninvoices(Request $request)
	{

		$userid = Auth::user()->id;

		$requestData 		= 	$request->all();
		//echo '<pre>'; print_r($requestData); die;
		$subtotal = 0;
		$total_value = 0;
		/* Profile Image Upload Function Start */
        if($request->hasfile('attachfile'))
        {
            $attachfile = $this->uploadFile($request->file('attachfile'), Config::get('constants.invoice'));
        }
        else
        {
            $attachfile = @$requestData['old_attachments'];
        }
		/* Profile Image Upload Function End */
		$profiledetail = \App\Profile::where('id', @$requestData['profile'])->first();
		$pdetail = '';
		if($profiledetail){
			$ps = array(
				'name'=> $profiledetail->company_name,
				'address'=> $profiledetail->address,
				'phone'=> $profiledetail->phone,
				'other_phone'=> $profiledetail->other_phone,
				'email'=> $profiledetail->email,
				'website'=> $profiledetail->website,
				'logo'=> $profiledetail->logo,
				'id'=> $profiledetail->id,
				'abn'=> $profiledetail->abn,
			);
			$pdetail = json_encode($ps);
		}
		$obj						= 	Invoice::find($requestData['id']);
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['applicationid'];
		$obj->type					=	@$requestData['type'];
		$obj->invoice_date			=	@$requestData['invoice_date'];
		$obj->due_date				=	@$requestData['invoice_due_date'];
		$obj->currency			=	@$requestData['currency'];
		$obj->net_fee_rec			=	@$requestData['invoice_net_amount'];
		$obj->net_incone			=	@$requestData['invoice_net_income'];
		$obj->notes					=	@$requestData['notes'];
		$obj->payment_option		=	@$requestData['paymentoption'];
		$obj->attachments			=	$attachfile;
		$obj->status				=	0;
		$obj->profile				=	@$pdetail;
		$saved				=	$obj->save();
		$res= InvoiceDetail::where('invoice_id',$obj->id)->delete();
		for($i =0; $i<count(@$requestData['amount']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new InvoiceDetail;
			$invoicedetail->invoice_id 		= $obj->id;
			$invoicedetail->description		= @$requestData['description'][$i];
			$invoicedetail->income_type		= @$requestData['income_type'][$i];
			$invoicedetail->total_fee 		= @$requestData['amount'][$i];

			$invoicedetail->tax 			= @$requestData['tax_code'][$i];
			$invoicedetail->tax_amount 		= @$requestData['tax_amt'][$i];
			$invoicedetail->netamount 		= @$requestData['total_amt'][$i];
			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{
	$res= InvoicePayment::where('invoice_id',$obj->id)->delete();
			for($ia =0; $ia<count(@$requestData['payment_amount']); $ia++){
				$objf				= 	new InvoicePayment;
				$objf->invoice_id	=	$obj->id;
				$objf->amount_rec	=	@$requestData['payment_amount'][$ia];
				$objf->payment_date	=	@$requestData['payment_date'][$ia];
				$objf->payment_mode	=	@$requestData['payment_mode'][$ia];

				$followupsaved				=	$objf->save();
			}

			if(@$requestData['btn'] == 'savepreview'){
				return Redirect::to('/admin/invoice/view/'.@$obj->id)->with('success', 'Invoice saved Successfully');
			}
			else{
				//return Redirect::to('/admin/invoice/lists/'.base64_encode(convert_uuencode(@$obj->id)))->with('success', 'Invoice saved Successfully');
				echo 'success';
			}
		}
	}


	public function getinvoicespdf(Request $request, $id = NULL){
		if(Invoice::where('id', '=', $id)->exists())
		{
			$invoicedetail = Invoice::where('id', '=', $id)->first();
			if($invoicedetail->type == 3){
				$workflowdaa = \App\Workflow::where('id', $invoicedetail->application_id)->first();
				$applicationdata = array();
				$partnerdata = array();
				$productdata = array();
				$branchdata = array();
			}else{
				$applicationdata = \App\Application::where('id', $invoicedetail->application_id)->first();
				$partnerdata = \App\Partner::where('id', @$applicationdata->partner_id)->first();
				$productdata = \App\Product::where('id', @$applicationdata->product_id)->first();
				$branchdata = \App\PartnerBranch::where('id', @$applicationdata->branch)->first();
				$workflowdaa = \App\Workflow::where('id', @$applicationdata->workflow)->first();
			}

			$clientdata = \App\Admin::where('role', 7)->where('id', $invoicedetail->client_id)->first();
			$admindata = \App\Admin::where('role', 1)->where('id', $invoicedetail->user_id)->first();

			$pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
			])->loadView('emails.invoice',compact(['applicationdata','partnerdata','workflowdaa','clientdata','productdata','branchdata','invoicedetail','admindata']));
			//
			return $pdf->stream('Inv '.$invoicedetail->id.'.pdf');

		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}

	}
	public function edit(Request $request, $id = NULL)
	{
		if(Invoice::where('id', '=', $id)->exists())
		{
			$invoicedetail = Invoice::where('id', '=', $id)->first();
			$clientdata = \App\Admin::where('role', 7)->where('id', $invoicedetail->client_id)->first();
			$admindata = \App\Admin::where('role', 1)->where('id', $invoicedetail->user_id)->first();
			if($invoicedetail->type == 3){
				$workflowdaa = \App\Workflow::where('id', $invoicedetail->application_id)->first();

				return view('Admin.invoice.edit-gen',compact(['workflowdaa','clientdata','invoicedetail','admindata']));
			}else{
				$applicationdata = \App\Application::where('id', $invoicedetail->application_id)->first();
				$partnerdata = \App\Partner::where('id', @$applicationdata->partner_id)->first();
				$productdata = \App\Product::where('id', @$applicationdata->product_id)->first();
				$branchdata = \App\PartnerBranch::where('id', @$applicationdata->branch)->first();
				$workflowdaa = \App\Workflow::where('id', @$applicationdata->workflow)->first();
				return view('Admin.invoice.edit',compact(['applicationdata','partnerdata','workflowdaa','clientdata','productdata','branchdata','invoicedetail','admindata']));
			}

		}else{
			return redirect()->back()->with('error', 'Record Not Found');
		}

	}

	public function deletepayment(Request $request){
		if(InvoicePayment::where('id', '=', $request->pay_id)->exists()){
			$invocie = InvoicePayment::where('id', '=', $request->pay_id)->first();
			$res = DB::table('invoice_payments')->where('id', $request->pay_id)->delete();
			if($res){

				$invoicedetail = \App\Invoice::where('id',$invocie->invoice_id)->first();
		$invoiceitemdetails = \App\InvoiceDetail::where('invoice_id', $invocie->invoice_id)->orderby('id','ASC')->get();
		$coom_amt = 0;
		$total_fee = 0;
		$netamount = 0;
		foreach($invoiceitemdetails as $invoiceitemdetail){
			$coom_amt += $invoiceitemdetail->comm_amt;
			$total_fee += $invoiceitemdetail->total_fee;
			$netamount += $invoiceitemdetail->netamount;
		}
		$paymentdetails = \App\InvoicePayment::where('invoice_id',$invocie->invoice_id)->orderby('created_at', 'DESC')->get();
		$amount_rec = 0;
		foreach($paymentdetails as $paymentdetail){
			$amount_rec += $paymentdetail->amount_rec;
		}
		if($invoicedetail->type == 2){
			$totaldue = $coom_amt - $amount_rec;
		}else if($invoicedetail->type == 3){
			$totaldue = $netamount - $amount_rec;
		}else{
			$feepaid = $total_fee - $coom_amt;
			$totaldue = $feepaid - $amount_rec;
		}

		if($totaldue != 0){
			$objss = \App\Invoice::find($invocie->invoice_id);
			$objss->status = 0;
			$objss->save();
		}
				$response['status'] 	= 	true;
			$response['message']	=	'Deleted successfully';
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function deleteinvoice(Request $request){
		if(Invoice::where('id', '=', $request->id)->exists()){
			$invocie = Invoice::where('id', '=', $request->id)->delete();

			if($invocie){
				$res = DB::table('invoice_payments')->where('invoice_id', $request->id)->delete();
				$res = DB::table('invoice_details')->where('invoice_id', $request->id)->delete();
				$response['status'] 	= 	true;
			$response['message']	=	'Deleted successfully';
			}else{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function unpaidgroupinvoice(){
		return view('Admin.invoice.unpaidgroupinvoice');
	}
	public function paidgroupinvoice(){
		return view('Admin.invoice.paidgroupinvoice');
	}
	public function creategroupinvoice(Request $request){ //dd('@@@');
		return view('Admin.invoice.creategroupinvoice');
	}

    public function savegroupinvoice(Request $request){
        $requestData 		= 	$request->all(); dd($requestData);
		//return view('Admin.invoice.creategroupinvoice');
	}

	public function invoiceschedules(){
		$query 		= InvoiceSchedule::where('id', '!=', '');
		$lists		= $query->orderby('id','desc')->paginate(20);
		return view('Admin.invoice.invoiceschedules',compact(['lists']));
	}

	public function deletepaymentschedule(Request $request){
		$requestData 		= 	$request->all();

		if(InvoiceSchedule::where('id', $request->note_id)->exists()){
			$d = InvoiceSchedule::where('id', $request->note_id)->first();
			InvoiceSchedule::where('id', $request->note_id)->delete();
			ScheduleItem::where('schedule_id', $request->note_id)->delete();
			$response['status'] 	= 	true;
				$response['message']	=	'Payment Schedule deleted successfully';
				$response['application_id'] 	= 	$d->application_id;
					$response['client_id'] 	= 	$d->client_id;

		}else{
			$response['status'] 	= 	false;
				$response['message']	=	'Please try again';

		}
		echo json_encode($response);
	}
	public function paymentschedule(Request $request){
		$requestData 		= 	$request->all();
		$obj						= 	new InvoiceSchedule;
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['application'];
		$obj->installment_name		=	@$requestData['installment_name'];
		$obj->installment_date		=	@$requestData['installment_date'];
		$obj->invoice_sc_date		=	@$requestData['invoice_sc_date'];
		$obj->discount				=	@$requestData['discount_amount'];

		$saved				=	$obj->save();
		for($i =0; $i<count(@$requestData['fee_type']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new ScheduleItem;
			$invoicedetail->schedule_id 		= $obj->id;
			$invoicedetail->fee_amount		= @$requestData['fee_amount'][$i];
			$invoicedetail->fee_type		= @$requestData['fee_type'][$i];
			$invoicedetail->commission 		= @$requestData['comm_amount'][$i];

			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{
			if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
				echo json_encode($response);
			}else{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
		}
		else
		{
			if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
				$response['status'] 	= 	true;
				$response['message']	=	'Payment Schedule Added successfully';
				$response['application_id'] 	= 	$requestData['application'];
					$response['client_id'] 	= 	$requestData['client_id'];
				echo json_encode($response);
			}else{
				return Redirect::to('/admin/invoice-schedules')->with('success', 'Invoice schedules added Successfully');
			}

		}
	}


	public function setuppaymentschedule(Request $request){
		$requestData 		= 	$request->all();
		if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
			if(\App\Application::where('id', $request->application_id)->exists()){
				$appid = \App\Application::where('id', $request->application_id)->first();
				if($appid){
					$appfeeid = \App\ApplicationFeeOption::where('app_id', $request->application_id)->first();
					$getfeetypequery = \App\ApplicationFeeOptionType::where('fee_id', $appfeeid->id);
					$totcount = $getfeetypequery->count();
					$getfeetype = $getfeetypequery->get();

					$requestData['client_id'] = $appid->client_id;
					$requestData['application'] = $appid->id;

					if($appfeeid){
					$requestData['installment_name'] = $appfeeid->installment_type.' 1';
						}else{
							$requestData['installment_name'] = 'Semester 1';
						}

						$requestData['invoice_sc_date'] = $requestData['invoice_date'];
						$requestData['discount_amount'] = @$appfeeid->total_discount;
						if($totcount !== 0){
							$i = 0;
							foreach($getfeetype as $getfeetyp){
								$requestData['fee_type'][$i] = $getfeetyp->fee_type;
								$requestData['fee_amount'][$i] = $getfeetyp->inst_amt;
								$requestData['comm_amount'][$i] = $getfeetyp->commission;
								$i++;
							}
						}else{
							$requestData['fee_type'][0] = 'Tuition Fee';
								$requestData['fee_amount'][0] = 0.00;
								$requestData['comm_amount'][0] = 0.00;
						}
				}

			}
		}


		$obj						= 	new InvoiceSchedule;
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['application'];
		$obj->installment_name		=	@$requestData['installment_name'];
		$obj->installment_date		=	@$requestData['installment_date'];
		$obj->invoice_sc_date		=	@$requestData['invoice_sc_date'];
		$obj->discount				=	@$requestData['discount_amount'];
		$obj->installment_no				=	@$requestData['installment_no'];
		$obj->installment_intervel				=	@$requestData['installment_intervel'];

		$saved				=	$obj->save();
		for($i =0; $i<count(@$requestData['fee_type']); $i++){
			//echo $requestData['item_detail'][$i].'---'.$requestData['quantity'][$i].'---'.$requestData['rate'][$i].'<br>';
			$invoicedetail = new ScheduleItem;
			$invoicedetail->schedule_id 		= $obj->id;
			$invoicedetail->fee_amount		= @$requestData['fee_amount'][$i];
			$invoicedetail->fee_type		= @$requestData['fee_type'][$i];
			$invoicedetail->commission 		= @$requestData['comm_amount'][$i];

			$saved							=	@$invoicedetail->save();
		}
		if(!$saved)
		{

			if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
				echo json_encode($response);
			}else{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
		}
		else
		{
			if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
				$response['status'] 	= 	true;
				$response['message']	=	'Payment Schedule Added successfully';
				echo json_encode($response);
			}else{
				return Redirect::to('/admin/invoice-schedules')->with('success', 'Invoice schedules added Successfully');
			}


		}
	}

	public function editpaymentschedule(Request $request){
		$requestData 				= 	$request->all();
		$obj						= 	InvoiceSchedule::find(@$requestData['id']);
		$obj->user_id				=	Auth::user()->id;
		$obj->client_id				=	@$requestData['client_id'];
		$obj->application_id		=	@$requestData['application'];
		$obj->installment_name		=	@$requestData['installment_name'];
		$obj->installment_date		=	@$requestData['installment_date'];
		$obj->invoice_sc_date		=	@$requestData['invoice_sc_date'];
		$obj->discount				=	@$requestData['discount_amount'];
		$saved						=	$obj->save();

		if(!$saved)
		{
			if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
				echo json_encode($response);
			}else{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
		}
		else
		{
			$res= ScheduleItem::where('schedule_id',@$requestData['id'])->delete();
				for($i =0; $i<count(@$requestData['fee_type']); $i++){
					$invoicedetail 					= new ScheduleItem;
					$invoicedetail->schedule_id 	= @$requestData['id'];
					$invoicedetail->fee_amount		= @$requestData['fee_amount'][$i];
					$invoicedetail->fee_type		= @$requestData['fee_type'][$i];
					$invoicedetail->commission 		= @$requestData['comm_amount'][$i];

					$saved							=	@$invoicedetail->save();
				}
				if(isset($requestData['is_ajax']) && $requestData['is_ajax']){
					$response['status'] 	= 	true;
					$response['application_id'] 	= 	$obj->application_id;
					$response['client_id'] 	= 	$obj->client_id;
					$response['message']	=	'Payment Schedule Added successfully';
					echo json_encode($response);
				}else{
						return Redirect::to('/admin/invoice-schedules')->with('success', 'Invoice schedules added Successfully');
				}

		}
	}

	public function getallpaymentschedules(Request $request){
		$InvoiceSchedules = InvoiceSchedule::where('client_id', $request->client_id)->where('application_id', $request->appid)->get();
		ob_start();
		foreach($InvoiceSchedules as $invoiceschedule){
			$scheduleitem = \App\ScheduleItem::where('schedule_id', $invoiceschedule->id)->get();
			?>
			<tr id="<?php echo @$invoiceschedule->id; ?>">
				<td><?php echo @$invoiceschedule->id; ?></td>
				<td >
					<div style="flex-direction: column;display: flex;">
					<span style="line-height: 23px;" class="text-info"><?php echo @$invoiceschedule->installment_name; ?></span>
					<span style="line-height: 16px;" class=""><?php echo @$invoiceschedule->installment_date; ?></span>
					<span style="line-height: 14px;" title="Non-Claimable" style="background-color: #2185d0!important;border-color: #2185d0!important;color: #fff!important; cursor: auto;font-size: 10px;padding: 3px;border-radius: 5px;">Non-Claimable</span>
					</div>
				</td>
				<td >
					<div style="flex-direction: column;display: flex;">
					<?php
					foreach($scheduleitem as $scheduleite){
						?>
						<span style="line-height: 23px;" class=""><?php echo @$scheduleite->fee_type; ?></span>
						<?php
					}
					?>
					</div>
				</td>
				<td >
					<div style="flex-direction: column;display: flex;">
					<?php
					$totlfee = 0;
					foreach($scheduleitem as $scheduleite){
						$totlfee += $scheduleite->fee_amount;
						?>
						<span style="line-height: 23px;" class=""><?php echo @$scheduleite->fee_amount; ?></span>
						<?php
					}
					?>
					</div>
				</td>
				<td><?php echo $totlfee; ?></td>
				<td><?php echo @$invoiceschedule->discount; ?></td>
				<td>-</td>
				<td><span title="Non Claimable" class="ui alignMiddle ag-label--circular truncate text-info">Non Claimable</span></td>
				<td>
					<div class="dropdown d-inline">
						<button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
						<div class="dropdown-menu">
							<a class="dropdown-item editpaymentschedule" data-id="<?php echo $invoiceschedule->id; ?>" href="javascript:;">Edit</a>
							<a class="dropdown-item deletenote" data-href="deletepaymentschedule" data-id="<?php echo $invoiceschedule->id; ?>" href="javascript:;" >Delete</a>
							<a data-cid="<?php echo $invoiceschedule->client_id; ?>" data-app-id="<?php echo @$invoiceschedule->application_id; ?>" data-id="<?php echo @$invoiceschedule->id; ?>" class="dropdown-item createapplicationnewinvoice" href="javascript:;" >Create Invoice</a>
						</div>
					</div>
				</td>
		</tr>
			<?php
		}
		return ob_get_clean();
	}
	public function addscheduleinvoicedetail(Request $request){
		ob_start();
		$application = \App\Application::where('id',$request->id)->first();
		$cleintname = \App\Admin::where('role',7)->where('id',$application->client_id)->first();
		?>
		<form method="post" action="<?php echo \URL::to('/admin/paymentschedule'); ?>" name="addinvpaymentschedule"  id="addinvpaymentschedule" autocomplete="off" enctype="multipart/form-data">
			<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
			<input type="hidden" name="is_ajax" value="true">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="">Client Name</label>
						<input type="text" disabled class="form-control" value="<?php echo $cleintname->first_name.' '.$cleintname->last_name; ?>">
						<input type="hidden" name="client_id" value="<?php echo $application->client_id; ?>">
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="">Application</label>
						<?php
						$productdetail = \App\Product::where('id', $application->product_id)->first();
						?>
						<input type="text" disabled class="form-control" value="<?php echo $productdetail->name; ?>">
						<input type="hidden" name="application" value="<?php echo $application->id; ?>">
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="installment_name">Installment Name <span class="span_req">*</span></label>
						<input data-valid="required" type="text" class="form-control" name="installment_name" placeholder="Installment Name" />
						<span class="custom-error installment_name_error" role="alert">
							<strong></strong>
						</span>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="installment_date">Installment Date <span class="span_req">*</span></label>
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">
									<i class="fas fa-calendar-alt"></i>
								</div>
							</div>
							<input data-valid="required" type="text" class="form-control datepicker" placeholder="Select Date" name="installment_date" />
						</div>
						<span class="custom-error installment_date_error" role="alert">
							<strong></strong>
						</span>
						<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
					</div>
				</div>
			</div>
			<div class="fee_type_sec">
						<div class="fee_label">
							<div class="label_col wd40">
								<label>Fee Type <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Fee Amount <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Commission %</label>
							</div>
							<div class="label_col wd10"></div>
						</div>
						<div class="clearfix"></div>
						<div class="fee_fields">
							<div class="fee_fields_row">
								<div class="field_col wd40">
									<select data-valid="required" class="form-control fee_type " id="fee_type" name="fee_type[]">
										<option value="">Select Fee Type</option>
										<option value="Accommodation Fee">Accommodation Fee</option>
										<option value="Administration Fee">Administration Fee</option>
										<option value="Airline Ticket">Airline Ticket</option>
										<option value="Airport Transfer Fee">Airport Transfer Fee</option>
										<option value="Application Fee">Application Fee</option>
										<option  value="Tution Fee">Tution Fee</option>
									</select>
								</div>
								<div class="field_col wd25">
									<input data-valid="required" type="number" class="form-control payfee_amount" name="fee_amount[]" placeholder="0.00" />
								</div>
								<div class="field_col wd25">
									<input type="number" class="form-control" name="comm_amount[]" placeholder="0.00" />
								</div>

								<div class="clearfix"></div>
							</div>
						</div>
						<div class="discount_fields">
							<div class="field_col wd40">
								<input type="text" class="form-control " name="" placeholder="Discount" readonly />
							</div>
							<div class="field_col wd25">
								<input type="number" class="form-control paydiscount" name="discount_amount" placeholder="0.00" />
							</div>
							<div class="field_col wd25">
								<input type="text" class="form-control" name="discount_payable" placeholder="0.00" readonly />
							</div>
							<div class="field_col wd10"></div>
						</div>
						<div class="clearfix"></div>

						<div class="totalfee_addbtn">
							<div class="feetype_btn">
								<a href="javascript:;" class="btn btn-outline-primary addfee"><i class="fa fa-plus"></i> Add Fee</a>
							</div>
							<div class="total_fee">
								<div class="total">
									<h4>Total Fee<span class="paytotlfee">0.00</span></h4>
								</div>
								<div class="netfee">
									<h6>Net Fee<span class="paynetfeeamt">0.00</span></h6>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<hr/>
					<h4>Setup Invoice Scheduling</h4>
					<span class="note">Schedule your Invoices by selecting an Invoice date for this installment.</span>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" class="form-control datepicker" placeholder="Select Date" name="invoice_sc_date" />
								</div>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('addinvpaymentschedule')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
		</form>
		<?php
		return ob_get_clean();
	}
	public function scheduleinvoicedetail(Request $request){
		$fetchedData = InvoiceSchedule::find($request->id);
		ob_start();
		?>
		<form method="post" action="<?php echo \URL::to('/admin/editpaymentschedule'); ?>" name="editinvpaymentschedule"  id="editinvpaymentschedule" autocomplete="off" enctype="multipart/form-data">

			<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
			<input type="hidden" name="id" value="<?php echo $fetchedData->id; ?>">
			<?php if(isset($request->t) && $request->t == 'application'){ ?><input type="hidden" name="is_ajax" value="true"><?php }else{ ?><input type="hidden" name="is_ajax" value="false"><?php } ?>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="editclientname">Client Name <span class="span_req">*</span></label>
								<?php if(isset($request->t) && $request->t == 'application'){
									$cleintname = \App\Admin::where('role',7)->where('id',$fetchedData->client_id)->first();
									?>
									<input type="text" disabled class="form-control" value="<?php echo $cleintname->first_name.' '.$cleintname->last_name; ?>">
									<input type="hidden" name="client_id" value="<?php echo $fetchedData->client_id; ?>">
									<?php
								}else{
									?>
									<select  data-valid="required" class="form-control editclientname" id="editclientname" name="client_id">
									<option value="">Select Client Name</option>
									<?php foreach(\App\Admin::where('role',7)->get() as $wlist){ ?>
										<option <?php if($fetchedData->client_id == $wlist->id){ echo 'selected'; } ?> value="<?php echo $wlist->id; ?>"><?php echo $wlist->first_name.' '.$wlist->last_name; ?></option>
									<?php } ?>
								</select>
									<?php
								}

								?>

								<span class="custom-error clientname_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="application">Application <span class="span_req">*</span></label>
								<?php if(isset($request->t) && $request->t == 'application'){
									$applications = \App\Application::where('client_id', $fetchedData->client_id)->where('id', $fetchedData->id)->first();
									$productdetail = \App\Product::where('id', $applications->product_id)->first();
									?>
									<input type="text" disabled class="form-control" value="<?php echo $productdetail->name; ?>">
									<input type="hidden" name="application" value="<?php echo $fetchedData->application_id; ?>">
									<?php
								}else{
									?>
									<select  data-valid="required" class="form-control application select2" id="editapplication" name="application">
									<option value="">Select Application</option>
									<?php
									$applications = \App\Application::where('client_id', $fetchedData->client_id)->orderby('created_at', 'DESC')->get();
									foreach($applications as $application){
									$productdetail = \App\Product::where('id', $application->product_id)->first();
                                    $partnerdetail = \App\Partner::where('id', $application->partner_id)->first();
                                    $clientdetail = \App\Admin::where('id', $application->client_id)->first();
                                    $PartnerBranch = \App\PartnerBranch::where('id', $application->branch)->first();
                                    ?>
                                    <option <?php if($fetchedData->application_id == $application->id){ echo 'selected'; } ?> value="<?php echo $application->id; ?>"><?php echo $productdetail->name.'('.$partnerdetail->partner_name; ?> <?php echo $PartnerBranch->name; ?>)</option>
									<?php } ?>
								</select>
									<?php
								}

								?>

								<span class="custom-error application_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_name">Installment Name <span class="span_req">*</span></label>
								<input data-valid="required" type="text" class="form-control" name="installment_name" placeholder="Installment Name" value="<?php echo @$fetchedData->installment_name; ?>" />
								<span class="custom-error installment_name_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="installment_date">Installment Date <span class="span_req">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input data-valid="required" type="text" class="form-control datepicker" placeholder="Select Date" name="installment_date" value="<?php echo @$fetchedData->installment_date; ?>" />
								</div>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
						</div>+
					</div>
					<div class="fee_type_sec">
						<div class="fee_label">
							<div class="label_col wd40">
								<label>Fee Type <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Fee Amount <span class="span_req">*</span></label>
							</div>
							<div class="label_col wd25">
								<label>Commission %</label>
							</div>
							<div class="label_col wd10"></div>
						</div>
						<div class="clearfix"></div>
						<div class="fee_fields editfields">
						<?php
						$amt = 0;
						$i =0;
						$ScheduleItems = \App\ScheduleItem::where('schedule_id',$fetchedData->id)->get();
						foreach($ScheduleItems as $ScheduleItem){
							$amt += $ScheduleItem->fee_amount;
						?>

							<div class="fee_fields_row editfee_fields_row">
								<div class="field_col wd40">
									<select data-valid="required" class="form-control fee_type selectfee_type" id="fee_type" name="fee_type[]">
										<option value="">Select Fee Type</option>
										<option <?php if($ScheduleItem->fee_type == "Accommodation Fee"){ echo 'selected'; } ?> value="Accommodation Fee">Accommodation Fee</option>
										<option <?php if($ScheduleItem->fee_type == "Administration Fee"){ echo 'selected'; } ?> value="Administration Fee">Administration Fee</option>
										<option <?php if($ScheduleItem->fee_type == "Airline Ticket"){ echo 'selected'; } ?> value="Airline Ticket">Airline Ticket</option>
										<option <?php if($ScheduleItem->fee_type == "Airport Transfer Fee"){ echo 'selected'; } ?> value="Airport Transfer Fee">Airport Transfer Fee</option>
										<option <?php if($ScheduleItem->fee_type == "Application Fee"){ echo 'selected'; } ?> value="Application Fee">Application Fee</option>
										<option  <?php if($ScheduleItem->fee_type == "Tution Fee"){ echo 'selected'; } ?> value="Tution Fee">Tution Fee</option>
									</select>
								</div>
								<div class="field_col wd25">
									<input data-valid="required" type="number" class="form-control edt_fee_amount" name="fee_amount[]" value="<?php echo $ScheduleItem->fee_amount; ?>" placeholder="0.00" />
								</div>
								<div class="field_col wd25">
									<input type="number" class="form-control edit_comm_amount" name="comm_amount[]" value="<?php echo $ScheduleItem->commission; ?>" placeholder="0.00" />
								</div>
								<?php if($i > 0 ){ ?>
								<div class="field_col wd10">
									<a href="javascript:;" class="editremoveitems"><i class="fa fa-trash"></i></a>
								</div>
								<?php } ?>
								<div class="clearfix"></div>
							</div>

						<?php $i++; } ?>
						</div>
						<div class="discount_fields">
							<div class="field_col wd40">
								<input type="text" class="form-control" name="discount" placeholder="Discount" readonly />
							</div>
							<div class="field_col wd25">
								<input type="number" class="form-control edit_discount" name="discount_amount" value="<?php echo $fetchedData->discount; ?>" placeholder="0.00" />
							</div>
							<div class="field_col wd25">
								<input type="text" class="form-control" name="discount_payable" placeholder="0.00" readonly />
							</div>
							<div class="field_col wd10"></div>
						</div>
						<div class="clearfix"></div>

						<div class="totalfee_addbtn">
							<div class="feetype_btn">
								<a href="javascript:;" class="btn btn-outline-primary cloneaddfee"><i class="fa fa-plus"></i> Add Fee</a>
							</div>
							<div class="total_fee">
								<div class="total">
									<h4>Total Fee<span class="edittotlfee"><?php echo number_format($amt,2,'.',''); ?></span></h4>
								</div>
								<div class="netfee">
								<?php
								$dis = 0;
								if($fetchedData->discount != ''){
									$dis = $fetchedData->discount;
								}
								?>
									<h6>Net Fee<span class="editnetfeeamt"><?php echo number_format($amt - $dis,2,'.',''); ?></span></h6>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<hr/>
					<h4>Setup Invoice Scheduling</h4>
					<span class="note">Schedule your Invoices by selecting an Invoice date for this installment.</span>
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invoice_date">Invoice Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input value="<?php echo @$fetchedData->invoice_sc_date; ?>" type="text" class="form-control datepicker" placeholder="Select Date" name="invoice_sc_date" />
								</div>
								<span class="custom-error installment_date_error" role="alert">
									<strong></strong>
								</span>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editinvpaymentschedule')" type="button" class="btn btn-primary">Update</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
		<?php
		return ob_get_clean();
	}

	public function apppreviewschedules(Request $request, $id){
		$applications = \App\Application::where('id', $id)->first();
		$invoiceschedules = \App\InvoiceSchedule::where('application_id', @$id)->get();

		$productdetail = \App\Product::where('id', @$applications->product_id)->first();
		$cleintname = \App\Admin::where('role',7)->where('id',@$applications->client_id)->first();
		$PartnerBranch = \App\PartnerBranch::where('id', @$applications->branch)->first();

		$pdf = PDF::setOptions([
			'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
			'logOutputFile' => storage_path('logs/log.htm'),
			'tempDir' => storage_path('logs/')
			])->loadView('emails.paymentschedules',compact(['invoiceschedules','cleintname','applications','productdetail','PartnerBranch']));
			//
			return $pdf->stream('PaymentSchedule.pdf');
	}

}

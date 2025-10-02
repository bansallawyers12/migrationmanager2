<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\InvoicePayment;
use App\Models\IncomeSharing;
use PDF;
use Auth;
use Config;

class AccountController extends Controller
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
	public function payment(Request $request)
	{
		$query 		= InvoicePayment::where('id', '!=', '')->with(['invoice']);

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);

		return view('Admin.account.payment', compact(['lists', 'totalData']));

	}
	public function preview(Request $request, $id = Null)
	{
		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(InvoicePayment::where('id', '=', $id)->exists())
				{
					$fetchedData = InvoicePayment::find($id);
					//return view('emails.reciept', compact(['fetchedData']));
					$pdf = PDF::setOptions([
					'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
					'logOutputFile' => storage_path('logs/log.htm'),
					'tempDir' => storage_path('logs/')
					])->loadView('emails.reciept', compact(['fetchedData']));
					//
					return $pdf->stream('REC '.$fetchedData->id.'.pdf');
				}
				else
				{
					return Redirect::to('/admin/payment')->with('error', 'Not Exist');
				}
		}else{
			return Redirect::to('/admin/payment')->with('error', 'Not Exist');
		}

	}

	public function payableunpaid(Request $request)
	{
		$query 		= IncomeSharing::where('status', '=', 0)->with(['branch']);

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(20); //dd($lists);
		return view('Admin.account.payableunpaid', compact(['lists', 'totalData']));
	}

	public function incomepaymentstore(Request $request)
	{
		$obj = IncomeSharing::find($request->invoice_id);
		$obj->payment_date 		=  $request->payment_date;
		$obj->payment_method 	=  $request->payment_mode;
		$obj->status 			=  1;
		$obj->user_id 			= @Auth::user()->id;
		$saved					=	@$obj->save();
		if(!$saved)
		{
			return redirect()->back()->with('error', Config::get('constants.server_error'));
		}
		else
		{
			return Redirect::to('/admin/income-sharing/payables/unpaid')->with('success', 'Payment for this payable has been added successfully');
		}
	}

	public function revertpayment(Request $request)
	{
		$obj = IncomeSharing::find($request->id);
		$obj->payment_date 		=  '';
		$obj->payment_method 	=  '';
		$obj->status 			=  0;
		$saved					=	@$obj->save();
		if(!$saved)
		{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		else
		{
			$response['status'] 	= 	true;
			$response['message']	=	'Pament Reverted successfully';
		}
		echo json_encode($response);
	}
	public function payablepaid(Request $request)
	{
		$query 		= IncomeSharing::where('status', '=', 1)->with(['branch']);

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		return view('Admin.account.payablepaid', compact(['lists', 'totalData']));
	}
	public function receivableunpaid(Request $request)
	{
		return view('Admin.account.receivableunpaid');
	}
	public function receivablepaid(Request $request)
	{
		return view('Admin.account.receivablepaid');
	}

}

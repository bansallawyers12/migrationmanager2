<?php
namespace App\Http\Controllers\AdminConsole;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Email;

use Auth;

use Hfig\MAPI;
use Hfig\MAPI\Message\Msg as Msg;



class EmailController extends Controller
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

    public function showForm()
    {
        return view('crm.clients.upload_email');
    }

    public function handleForm(Request $request)
    {
        $request->validate([
            'email_file' => 'required|mimes:msg|max:2048',
        ]);

        // Upload Email File
        $emailPath = $request->file('email_file')->store('emails', 'public');

        // Parse Email
        $emailData = $this->parseEmail(storage_path('app/public/' . $emailPath));

        return view('crm.clients.email_details', compact('emailData'));
    }

    private function parseEmail($filePath)
    {
        try {

            $msg = new Msg($filePath); dd('###'.$msg);

            $from = $msg->getHeaders()['from'];
            $to = $msg->getHeaders()['to'];
            $subject = $msg->getSubject();
            $body = $msg->getBodyText();

            $emailData = [
                'from'    => $from,
                'to'      => $to,
                'subject' => $subject,
                'body'    => $body
            ];

            return $emailData;
        } catch (\Exception $e) {
            return ['error' => 'Failed to parse email: ' . $e->getMessage()];
        }
    }

	/**
     * All Vendors.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Request $request)
	{
		//check authorization start

			/* if($check)
			{
				return Redirect::to('/dashboard')->with('error',config('constants.unauthorized'));
			} */
		//check authorization end
	
		$query 		= Email::query();

		$totalData 	= $query->count();	//for all data

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('AdminConsole.features.emails.index',compact(['lists', 'totalData']));

		//return view('AdminConsole\.features\.producttype.index');
	}

	public function create(Request $request)
	{
		//check authorization end
		//return view('AdminConsole\.system\.users\.create',compact(['usertype']));

		return view('AdminConsole.features.emails.create');
	}

	public function store(Request $request)
	{
		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, ['email' => 'required|max:255|unique:emails']);

			$requestData = 	$request->all();
            $obj		 = 	new Email;
			$obj->email	 =	@$requestData['email'];
			$obj->email_signature	=	@$requestData['email_signature'];
			$obj->display_name	=	@$requestData['display_name'];
            $obj->password	=	@$requestData['password'];
			$obj->status	=	@$requestData['status'];
			$obj->user_id	=	json_encode(@$requestData['users']);
            $saved			=	$obj->save();

			if(!$saved)
			{
				return redirect()->back()->with('error', config('constants.server_error'));
			}
			else
			{
				return redirect()->route('adminconsole.features.emails.index')->with('success', 'Email Added Successfully');
			}
		}

		return view('AdminConsole.features.emails.create');
	}

	/**
	 * Show the form for editing the specified email.
	 */
	public function edit($id)
	{
		//check authorization end

		if(isset($id) && !empty($id))
		{
			$id = $this->decodeString($id);
			if(Email::where('id', '=', $id)->exists())
			{
				$fetchedData = Email::find($id);
				return view('AdminConsole.features.emails.edit', compact(['fetchedData']));
			}
			else
			{
				return redirect()->route('adminconsole.features.emails.index')->with('error', 'Email Not Exist');
			}
		}
		else
		{
			return redirect()->route('adminconsole.features.emails.index')->with('error', config('constants.unauthorized'));
		}
	}

	/**
	 * Update the specified email in storage.
	 */
	public function update(Request $request, $id)
	{
		//check authorization end

		$requestData = $request->all();
		$this->validate($request, ['email' => 'required|max:255|unique:emails,email,'.$id]);
		
		$obj = Email::find($id);
		if (!$obj) {
			return redirect()->route('adminconsole.features.emails.index')->with('error', 'Email Not Found');
		}
		
		$obj->email = @$requestData['email'];
		$obj->email_signature = @$requestData['email_signature'];
		$obj->display_name = @$requestData['display_name'];
		$obj->password = @$requestData['password'];
		$obj->status = @$requestData['status'];
		$obj->user_id = json_encode(@$requestData['users']);
		$saved = $obj->save();

		if(!$saved)
		{
			return redirect()->back()->with('error', config('constants.server_error'));
		}
		else
		{
			return redirect()->route('adminconsole.features.emails.index')->with('success', 'Email Updated Successfully');
		}
	}
}



<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Admin;
use App\Models\Partner;
use App\Models\Contact;
use App\Models\PartnerBranch;
//use App\Models\ActivitiesLog;

use Auth;
use Config;

class PartnersController extends Controller
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
	public function index(Request $request)
	{
		//check authorization start

			/* if($check)
			{
				return Redirect::to('/admin/dashboard')->with('error',config('constants.unauthorized'));
			} */
		//check authorization end

		 $query 		= Partner::where('id', '!=', '');

		$totalData 	= $query->count();	//for all data
		if ($request->has('name'))
		{
			$name 		= 	$request->input('name');
			if(trim($name) != '')
			{
				$query->where('partner_name', 'LIKE', '%'.$name.'%');
			}
		}

		 if ($request->has('email'))
		{
			$email 		= 	$request->input('email');
			if(trim($email) != '')
			{
				$query->where('email', '=', $email);
			}
		}
		 if ($request->has('reginal_code'))
		{
			$reginal_code 		= 	$request->input('reginal_code');
			if(trim($reginal_code) != '')
			{
				$query->where('reginal_code', '=', $reginal_code);
			}
		}

		if ($request->has('level'))
		{
			$level 		= 	$request->input('level');
			if(trim($level) != '')
			{
				$query->where('level', 'LIKE', '%'.$level.'%');
			}
		}

		$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));

		return view('Admin.partners.index',compact(['lists', 'totalData']));


		//return view('Admin.partners.index');
	}

	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));

		return view('Admin.partners.create');
	}

	public function store(Request $request)
	{

		//check authorization end
		if ($request->isMethod('post'))
		{
			$this->validate($request, [
										//'master_category' => 'required|max:255',
										//'partner_type' => 'required|max:255',
										'partner_name' => 'required|max:255',
										//'service_workflow' => 'required|max:255',
										//'currency' => 'required|max:255',
										'email' => 'required|max:255'
									  ]);

			$requestData 		= 	$request->all();
			//echo '<pre>'; print_r($requestData); die;
			$obj				= 	new Partner;
			$obj->master_category	=	@$requestData['master_category'];
			$obj->partner_type	=	@$requestData['partner_type'];
			$obj->partner_name	=	@$requestData['partner_name'];
			$obj->business_reg_no	=	@$requestData['business_reg_no'];
			$obj->service_workflow	=	@$requestData['service_workflow'];
			$obj->currency	=	@$requestData['currency'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->country_code	=	@$requestData['country_code'];
			$obj->is_regional	=	@$requestData['is_regional'];
			$obj->phone	=	@$requestData['phone'];
			$obj->email	=	@$requestData['email'];
			$obj->fax	=	@$requestData['fax'];
			$obj->level = @$requestData['level'];
			$obj->website	=	@$requestData['website'];

			/* Profile Image Upload Function Start */
				if($request->hasfile('profile_img'))
				{
					$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
				}
				else
				{
					$profile_img = NULL;
				}
			/* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved				=	$obj->save();
			if(isset($requestData['branchname']) && $requestData['branchname'] != ''){
			$branchname =  $requestData['branchname'];
			$branchemail =  $requestData['branchemail'];
			$branchcountry =  $requestData['branchcountry'];
			$branchcity =  $requestData['branchcity'];
			$branchstate =  $requestData['branchstate'];
			$branchaddress =  $requestData['branchaddress'];
			$branchzip =  $requestData['branchzip'];
			$branchreg =  $requestData['branchreg'];
			$branchcountry_code =  $requestData['branchcountry_code'];
			$branchphone =  $requestData['branchphone'];
			for($i=0; $i< count($branchname); $i++){
				$is_headoffice = 0;
				if($i==0){
					$is_headoffice = 1;
				}
				$o = new \App\Models\PartnerBranch;
				$o->user_id = @Auth::user()->id;
				$o->partner_id = @$obj->id;
				$o->name = @$branchname[$i];
				$o->email = @$branchemail[$i];
				$o->country = @$branchcountry[$i];
				$o->city = @$branchcity[$i];
				$o->state = @$branchstate[$i];
				$o->street = @$branchaddress[$i];
				$o->zip = @$branchzip[$i];
				$o->country_code = @$branchcountry_code[$i];
				$o->phone = @$branchphone[$i];
				$o->is_regional = @$branchreg[$i];
				$o->is_headoffice = $is_headoffice;
				$o->save();
			}
		}else{
		    $is_headoffice = 1;
		    $o = new \App\Models\PartnerBranch;
				$o->user_id = @Auth::user()->id;
				$o->partner_id = @$obj->id;
				$o->name = 'Head Office';
				$o->email = @$requestData['email'];
				$o->country = @$requestData['country'];
				$o->city = @$requestData['city'];
				$o->state = @$requestData['state'];
				$o->street = @$requestData['address'];
				$o->zip = @$requestData['zip'];
				$o->country_code = @$requestData['country_code'];
				$o->phone =@$requestData['phone'];
				$o->is_regional =@$requestData['is_regional'];
				$o->is_headoffice = $is_headoffice;
				$o->save();
		}
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{

				return Redirect::to('/admin/partners')->with('success', 'Partners Added Successfully');
			}
		}

		return view('Admin.partners.create');
	}

	public function edit(Request $request, $id = NULL)
	{
        //check authorization end
        if ($request->isMethod('post'))
		{ //dd('ifff');
			$requestData 		= 	$request->all();
		    //echo '<pre>'; print_r($requestData); die;
			$this->validate($request, [
                //'master_category' => 'required|max:255',
                //'partner_type' => 'required|max:255',
                'partner_name' => 'required|max:255',
                //'service_workflow' => 'required|max:255',
                //'currency' => 'required|max:255',
                'email' => 'required|max:255'
            ]);

			$obj			= 	Partner::find(@$requestData['id']);

			//$obj->master_category	=	@$requestData['master_category'];
			$obj->partner_type	=	@$requestData['partner_type'];
			$obj->partner_name	=	@$requestData['partner_name'];
			$obj->business_reg_no	=	@$requestData['business_reg_no'];
			$obj->service_workflow	=	@$requestData['service_workflow'];
			$obj->currency	=	@$requestData['currency'];
			$obj->address	=	@$requestData['address'];
			$obj->city	=	@$requestData['city'];
			$obj->state	=	@$requestData['state'];
			$obj->is_regional	=	@$requestData['is_regional'];
			$obj->zip	=	@$requestData['zip'];
			$obj->country	=	@$requestData['country'];
			$obj->country_code	=	@$requestData['country_code'];
			$obj->phone	=	@$requestData['phone'];
			$obj->email	=	@$requestData['email'];
			$obj->fax	=	@$requestData['fax'];
			$obj->level = @$requestData['level'];
			$obj->website	=	@$requestData['website'];

			/* Profile Image Upload Function Start */
			if($request->hasfile('profile_img'))
			{
				/* Unlink File Function Start */
                if($requestData['profile_img'] != '')
                {
                    $this->unlinkFile($requestData['old_profile_img'], Config::get('constants.profile_imgs'));
                }
				/* Unlink File Function End */

				$profile_img = $this->uploadFile($request->file('profile_img'), Config::get('constants.profile_imgs'));
			}
			else
			{
				$profile_img = @$requestData['old_profile_img'];
			}
		    /* Profile Image Upload Function End */
			$obj->profile_img			=	@$profile_img;
			$saved							=	$obj->save();
            if(isset($requestData['rem'])){
                $rem =  @$requestData['rem'];
                for($irem=0; $irem< count($rem); $irem++){
                    if(\App\Models\PartnerBranch::where('id', $rem[$irem])->exists()){
                        \App\Models\PartnerBranch::where('id', $rem[$irem])->delete();
                    }
                }
            }
            if(isset($requestData['branchname'])){
                $branchname =  $requestData['branchname'];
            } else {
                $branchname = array();
            }

            if(isset($requestData['branchemail'])){
                $branchemail =  $requestData['branchemail'];
            }

            if(isset($requestData['branchcountry'])){
                $branchcountry =  $requestData['branchcountry'];
            }

            if(isset($requestData['branchcity'])){
                $branchcity =  $requestData['branchcity'];
            }

			if(isset($requestData['branchstate'])){
                $branchstate =  $requestData['branchstate'];
            }

            if(isset($requestData['branchaddress'])){
                $branchaddress =  $requestData['branchaddress'];
            }

            if(isset($requestData['branchzip'])){
                $branchzip =  $requestData['branchzip'];
            }

            if(isset($requestData['branchreg'])){
                $branchreg =  $requestData['branchreg'];
            }

            if(isset($requestData['branchcountry_code'])){
                $branchcountry_code =  $requestData['branchcountry_code'];
            }

            if(isset($requestData['branchphone'])){
                $branchphone =  $requestData['branchphone'];
            }

            if(count($branchname) >0){
                for($i=0; $i< count($branchname); $i++){
                    $is_headoffice = 0;
                    if($i==0){
                        $is_headoffice = 1;
                    }
                    if(\App\Models\PartnerBranch::where('id', $requestData['branchid'][$i])->exists()){
                        $os = \App\Models\PartnerBranch::find($requestData['branchid'][$i]);

                        $os->name = @$branchname[$i];
                        $os->email = @$branchemail[$i];
                        $os->country = @$branchcountry[$i];
                        $os->city = @$branchcity[$i];
                        $os->state = @$branchstate[$i];
                        $os->street = @$branchaddress[$i];
                        $os->zip = @$branchzip[$i];
                        $os->country_code = @$branchcountry_code[$i];
                        $os->phone = @$branchphone[$i];
                        $os->is_regional = @$branchreg[$i];

                        $os->save();
                    }else{
                        $o = new \App\Models\PartnerBranch;
                        $o->user_id = @Auth::user()->id;
                        $o->partner_id = @$obj->id;
                        $o->name = @$branchname[$i];
                        $o->email = @$branchemail[$i];
                        $o->country = @$branchcountry[$i];
                        $o->city = @$branchcity[$i];
                        $o->state = @$branchstate[$i];
                        $o->street = @$branchaddress[$i];
                        $o->zip = @$branchzip[$i];
                        $o->country_code = @$branchcountry_code[$i];
                        $o->phone = @$branchphone[$i];
                        $o->is_regional = @$branchreg[$i];
                        $o->is_headoffice = $is_headoffice;
                        $o->save();
                    }
                }
            }

			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}

			else
			{
				return Redirect::to('/admin/partners')->with('success', 'Partners Edited Successfully');
			}
		}

		else
		{ //dd('elseee');
			if(isset($id) && !empty($id))
			{
                $id = $this->decodeString($id);
				if(Partner::where('id', '=', $id)->exists())
				{
					$fetchedData = Partner::find($id); //dd($fetchedData);
					return view('Admin.partners.edit', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/partners')->with('error', 'Partners Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/partners')->with('error', Config::get('constants.unauthorized'));
			}
		}

	}

	public function getpaymenttype(Request $request){
		$catid = $request->cat_id;
		$lists = \App\Models\PartnerType::where('category_id', $catid)->orderby('name','ASC')->get();
		ob_start();
		?>
		<option value="">Select a Partner Type</option>
		<?php
		foreach($lists as $list){
			?>
			<option value="<?php echo $list->id; ?>"><?php echo $list->name; ?></option>
			<?php
		}
		echo ob_get_clean();
	}

	public function detail(Request $request, $id = NULL){
		if(isset($id) && !empty($id))
			{
				$id = $this->decodeString($id);
				if(Partner::where('id', '=', $id)->exists())
				{
					$fetchedData = Partner::find($id);
					return view('Admin.partners.detail', compact(['fetchedData']));
				}
				else
				{
					return Redirect::to('/admin/partners')->with('error', 'Partners Not Exist');
				}
			}
			else
			{
				return Redirect::to('/admin/partners')->with('error', Config::get('constants.unauthorized'));
			}
	}

	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){

			 $partners = \App\Models\Partner::where('id', '!=', '')

       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();

			$items = array();
			foreach($partners as $partner){
				$items[] = array('name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}

			echo json_encode(array('items'=>$items));
		}
	}


	public function getallpartners(Request $request){
		$squery = $request->q;
		if($squery != ''){

			 $partners = \App\Models\Partner::where('id', '!=', '')

       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();

			$items = array();
			foreach($partners as $partner){
				$items[] = array('name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}

			echo json_encode(array('items'=>$items));
		}
	}

	public function saveagreement(Request $request){  //dd($request->all());
		if(Partner::where('id', '=', $request->partner_id)->exists())
		{
			$obj = Partner::find($request->partner_id);
			$obj->contract_expiry = $request->contract_expiry;
			if(isset($request->represent_region) && !empty($request->represent_region)){
				$obj->represent_region = implode(',',$request->represent_region);
			}
			if(isset($request->gst)){
				$obj->gst = 1;
			}else{
				$obj->gst = 0;
			}
			$obj->commission_percentage = $request->commission_percentage;
			$obj->default_super_agent = $request->default_super_agent;


            if ($request->hasfile('file_upload')) {
                if(!is_array($request->file('file_upload'))){
                    $files[] = $request->file('file_upload');
                } else {
                    $files = $request->file('file_upload');
                }
                foreach ($files as $file) {
                    $size = $file->getSize();
                    $fileName = $file->getClientOriginalName();
                    $explodeFileName = explode('.', $fileName);
                    $document_upload = $this->uploadrenameFile($file, Config::get('constants.documents'));
                    $exploadename = explode('.', $document_upload);
                    $obj->file_upload = $document_upload; //$explodeFileName[0];
                }
            }

			$saved = $obj->save();
			if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your partner agreement’s information.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record Not Found';
		}

		 echo json_encode($response);
	}

	public function createcontact(Request $request){
		if(isset($request->contact_id) && $request->contact_id != ''){
			$obj = Contact::find($request->contact_id);
		}else{
			$obj = new Contact;
		}
		$obj->name 				= $request->name;
		$obj->contact_email 	= $request->email;
		$obj->contact_phone 	= $request->phone;
		$obj->department 		= $request->department;
		$obj->branch 			= $request->branch;
		$obj->fax 				= $request->fax;
		$obj->position 			= $request->position;
		$obj->primary_contact 	= $request->primary_contact;
		$obj->user_id 			= $request->client_id;
		$obj->countrycode 		= $request->country_code;
		$saved = $obj->save();

		if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your contact.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

			echo json_encode($response);
	}

	public function getcontacts(Request $request){
		$querycontactlist = \App\Models\Contact::where('user_id', $request->clientid)->orderby('created_at', 'DESC');
		$contactlistcount = $querycontactlist->count();
		$contactlist = $querycontactlist->get();
		if($contactlistcount !== 0){
			foreach($contactlist as $clist){
				$branch = \App\Models\PartnerBranch::where('id', $clist->branch)->first();
				?>
				<div class="note_col" id="contact_<?php echo $clist->id; ?>" style="width:33.33333333%">
					<div class="note_content">
						<h4><?php echo $clist->name; ?></h4>
						<p><span class="text-semi-bold"><?php if($clist->position != ''){ echo $clist->position; }else{ echo '-'; } ?></span> In <span class="text-semi-bold"><?php if($clist->department != ''){ echo $clist->department; }else{ echo '-'; } ?></span></p>
						<div class="" style="margin-top: 15px!important;">
							<p><i class="fa fa-phone"></i> <?php if($clist->contact_phone != ''){ echo $clist->contact_phone; }else{ echo '-'; } ?></p>
							<p style="margin-top: 5px!important;"><i class="fa fa-fax"></i> <?php if($clist->fax != ''){ echo $clist->fax; }else{ echo '-'; } ?></p>
							<p style="margin-top: 5px!important;"><i class="fa fa-mail"></i> <?php if($clist->contact_email != ''){ echo $clist->contact_email; }else{ echo '-'; } ?></p>
						</div>
					</div>
					<div class="extra_content">
						<div class="left">
							<i class="fa fa-map-marker" style="margin-right: 20px!important;"></i>
							<?php echo $branch->city; ?>, <?php echo $branch->country; ?>
						</div>
						<div class="right">
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item opencontactform" data-id="<?php echo $clist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $clist->id; ?>" data-href="deletecontact" class="dropdown-item deletenote" href="javascript:;" >Delete</a>

								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}else{

		}
	}


	public function deletecontact(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Contact::where('id',$note_id)->exists()){
			$res = DB::table('contacts')->where('id', @$note_id)->delete();
			if($res){

			$response['status'] 	= 	true;
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

	public function getcontactdetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\Contact::where('id',$note_id)->exists()){
			$data = \App\Models\Contact::select('name','contact_email','contact_phone','department','branch','fax','position','primary_contact','countrycode')->where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}


	public function createbranch(Request $request){
		if(isset($request->branch_id) && $request->branch_id != ''){
			$obj = PartnerBranch::find($request->branch_id);
		}else{
			$obj = new PartnerBranch;
		}
		$obj->user_id 			= Auth::user()->id;
		$obj->partner_id 				= $request->client_id;
		$obj->name 	= $request->name;
		$obj->email 	= $request->email;
		$obj->country 		= $request->country;
		$obj->city 			= $request->city;
		$obj->state 				= $request->state;
		$obj->street 			= $request->street;
		$obj->country_code 	= $request->country_code;
		$obj->phone 			= $request->phone;
		$obj->is_headoffice 		= $request->head_office;
		$obj->zip 		= $request->zip_code;
		$saved = $obj->save();

		if($saved){
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully saved your contact.';
			}else{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}

			echo json_encode($response);
	}

	public function getbranch(Request $request){
		$branchesquery = PartnerBranch::where('partner_id', $request->clientid)->orderby('created_at', 'DESC');
		$branchescount = $branchesquery->count();
		$branches = $branchesquery->get();
		if($branchescount !== 0){

			foreach($branches as $branch){

				?>
				<div class="branch_col" id="contact_">
					<div class="branch_content">
						<h4><?php echo $branch->name; ?></h4>
						<div class="" style="margin-top: 15px!important;">
							<p><i class="fa fa-map-marker-alt" style="margin-right: 10px!important;"></i> <?php echo $branch->city; ?>, <?php echo $branch->a; ?></p>
						</div>
					</div>
					<div class="extra_content">
						<div class="left">
							<p><i class="fa fa-phone" style="margin-right: 20px!important;"></i> <?php if($branch->phone != ''){ echo $branch->phone; }else{ echo '-'; } ?></p>
							<p><i class="fa fa-envelope-o" style="margin-right: 20px!important;"></i> <?php if($branch->email != ''){ echo $branch->email; }else{ echo '-'; } ?></p>
						</div>
						<div class="right">
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item openbranchform" data-id="{{$branch->id}}" href="javascript:;">Edit</a>
									<a data-id="{{$branch->id}}" data-href="deletebranch" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
		}else{

		}
	}

	public function getbranchdetail(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\PartnerBranch::where('id',$note_id)->exists()){
			$data = \App\Models\PartnerBranch::where('id',$note_id)->first();
			$response['status'] 	= 	true;
			$response['data']	=	$data;
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}

	public function deletebranch(Request $request){
		$note_id = $request->note_id;
		if(\App\Models\PartnerBranch::where('id',$note_id)->exists()){
			$res = DB::table('partner_branches')->where('id', @$note_id)->delete();
			if($res){

			$response['status'] 	= 	true;
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

	public function addappointment(Request $request){
		$requestData = $request->all();

		$obj = new \App\Models\Appointment;
		$obj->user_id = @Auth::user()->id;
		$obj->client_id = @$request->client_id;
		$obj->timezone = @$request->timezone;
		$obj->date = @$request->appoint_date;
		$obj->time = @$request->appoint_time;
		$obj->title = @$request->title;
		$obj->description = @$request->description;
		$obj->status = 0;
		$obj->related_to = 'partner';
		$saved = $obj->save();
		if($saved){

			$response['status'] 	= 	true;
			$response['data']	=	'Appointment saved successfully';
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		}
		echo json_encode($response);
	}



	public function getappointment(Request $request){
		ob_start();
		$appointmentlistsquery = \App\Models\Appointment::where('client_id', $request->clientid)->where('related_to', 'partner')->orderby('created_at', 'DESC');
		$appointmentlistscount = $appointmentlistsquery->count();
		$appointmentlists = $appointmentlistsquery->get();
		if($appointmentlistscount !== 0){
		?>
		<div class="row">
			<div class="col-md-5 appointment_grid_list">
				<?php
				$rr=0;
				$appointmentdata = array();

				$appointmentlistslast = \App\Models\Appointment::where('client_id', $request->clientid)->where('related_to', 'partner')->orderby('created_at', 'DESC')->first();
				foreach($appointmentlists as $appointmentlist){
					$admin = \App\Models\Admin::where('id', $appointmentlist->user_id)->first();
					$datetime = $appointmentlist->created_at;
					$timeago = Controller::time_elapsed_string($datetime);

					$appointmentdata[$appointmentlist->id] = array(
						'title' => $appointmentlist->title,
						'time' => date('H:i A', strtotime($appointmentlist->time)),
						'date' => date('d D, M Y', strtotime($appointmentlist->date)),
						'description' => $appointmentlist->description,
						'createdby' => substr($admin->first_name, 0, 1),
						'createdname' => $admin->first_name,
						'createdemail' => $admin->email,
					);
				?>
				<div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
					<div class="appointment_col">
						<div class="appointdate">
							<h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
							<p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
							<i><small><?php echo $timeago ?></small></i></p>
						</div>
						<div class="title_desc">
							<h5><?php echo $appointmentlist->title; ?></h5>
							<p><?php echo $appointmentlist->description; ?></p>
						</div>
						<div class="appoint_created">
							<span class="span_label">Created By:
							<span><?php echo substr($admin->first_name, 0, 1); ?></span></span>
							<div class="dropdown d-inline dropdown_ellipsis_icon">
								<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu">
									<a class="dropdown-item edit_appointment" data-id="<?php echo $appointmentlist->id; ?>" href="javascript:;">Edit</a>
									<a data-id="<?php echo $appointmentlist->id; ?>" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php $rr++; } ?>
			</div>
			<div class="col-md-7">
				<div class="editappointment">
					<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php echo $appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>
					<?php
					$adminfirst = \App\Models\Admin::where('id', $appointmentlistslast->user_id)->first();
					?>
					<div class="content">
						<h4 class="appointmentname"><?php echo $appointmentlistslast->title; ?></h4>
						<div class="appitem">
							<i class="fa fa-clock"></i>
							<span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime($appointmentlistslast->time)); ?></span>
						</div>
						<div class="appitem">
							<i class="fa fa-calendar"></i>
							<span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime($appointmentlistslast->date)); ?></span>
						</div>
						<div class="description appointmentdescription">
							<p><?php echo $appointmentlistslast->description; ?></p>
						</div>
						<div class="created_by">
							<span class="label">Created By:</span>
							<div class="createdby">
								<span class="appointmentcreatedby"><?php echo substr($adminfirst->first_name, 0, 1); ?></span>
							</div>
							<div class="createdinfo">
								<a href="" class="appointmentcreatedname"><?php echo $adminfirst->first_name ?></a>
								<p class="appointmentcreatedemail"><?php echo $adminfirst->primary_email; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		}
		echo ob_get_clean();
		die;
	}

	public function getAppointmentdetail(Request $request){
		$obj = \App\Models\Appointment::find($request->id);
		if($obj){
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editappointment'); ?>" name="editpartnerappointment" id="editpartnerappointment" autocomplete="off" enctype="multipart/form-data">

				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->client_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Related to:</label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="client" value="Client" name="related_to" checked>
									<label class="form-check-label" for="client">Client</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" id="partner" value="Partner" name="related_to">
									<label class="form-check-label" for="partner">Partner</label>
								</div>
								<span class="custom-error related_to_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label style="display:block;" for="related_to">Added by:</label>
								<span><?php echo @Auth::user()->first_name; ?></span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="client_name">Client Name <span class="span_req">*</span></label>
								<input type="text" name="client_name" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Client Name" readonly value="<?php echo $obj->partners->partner_name; ?>">

							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="timezone">Timezone <span class="span_req">*</span></label>
								<select class="form-control select2" name="timezone" data-valid="required">
									<option value="">Select Timezone</option>
									<?php
									$timelist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
									foreach($timelist as $tlist){
										?>
										<option value="<?php echo $tlist; ?>" <?php if($obj->timezone == $tlist){ echo 'selected'; } ?>><?php echo $tlist; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-7 col-lg-7">
							<div class="form-group">
								<label for="appoint_date">Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="appoint_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->date; ?>">

								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error appoint_date_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-5 col-lg-5">
							<div class="form-group">
								<label for="appoint_time">Time</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-clock"></i>
										</div>
									</div>
									<input type="text" name="appoint_time" class="form-control timepicker" data-valid="required" autocomplete="off" placeholder="Select Date" readonly value="<?php echo $obj->time; ?>">

								</div>
								<span class="custom-error appoint_time_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="title">Title <span class="span_req">*</span></label>
								<input type="text" name="title" class="form-control " data-valid="required" autocomplete="off" placeholder="Enter Title"  value="<?php echo $obj->title; ?>">

								<span class="custom-error title_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="description">Description</label>
								<textarea class="form-control" name="description" placeholder="Description"><?php echo $obj->description; ?></textarea>
								<span class="custom-error description_error" role="alert">
									<strong></strong>
								</span>
							</div>
						</div>
						<div class="col-12 col-md-6 col-lg-6">
							<div class="form-group">
								<label for="invitees">Invitees</label>
								<select class="form-control select2" name="invitees">
									<option>Select Invitees</option>
									<option>Option 1</option>
									<option>Option 2</option>
									<option>Option 3</option>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editpartnerappointment')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
		}else{
			?>
			Record Not Found
			<?php
		}
	}

	public function addtask(Request $request){
		$requestData 		= 	$request->all();
			 if($request->hasfile('attachments'))
			{
				$attachfile = $this->uploadFile($request->file('attachments'), Config::get('constants.invoice'));
			}
			else
			{
				$attachfile = Null;
			}
			$obj				= 	new Task;
			$obj->title			=	@$requestData['title'];
			$obj->category		=	@$requestData['category'];
			$obj->assignee		=	@$requestData['assignee'];
			$obj->priority		=	@$requestData['priority'];
			$obj->due_date		=	@$requestData['due_date'];
			$obj->due_time		=	@$requestData['due_time'];
			$obj->description	=	@$requestData['description'];
			$obj->related_to	=	@$requestData['related_to'];
			if(isset($requestData['contact_name']) && !empty($requestData['contact_name'])){
			$obj->contact_name	=	implode(',',@$requestData['contact_name']);
			}
			$obj->partner_name	=	@$requestData['partner_name'];
			$obj->client_name	=	@$requestData['client_name'];
			$obj->application	=	@$requestData['application'];
			$obj->stage			=	@$requestData['stage'];
			$obj->followers		=	@$requestData['followers'];
			$obj->attachments	=	@$requestData['attachfile'];
			$obj->user_id		=	Auth::user()->id;
			$obj->type			=	'partner';
			$obj->client_id	=	@$requestData['partnerid'];
			$saved				=	$obj->save();


			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$objtask = new TaskLog;
				$objtask->task_id 		= $obj->id;
				$objtask->created_by 	= Auth::user()->id;
				$objtask->title = 'created a task';
				$saved				=	$objtask->save();

				$response['status'] 	= 	true;
				$response['message']	=	'Task Created Successfully';
			}
			echo json_encode($response);
	}

	public function gettasks(Request $request){
		$client_id = $request->clientid;

		$notelist = \App\Models\Task::where('client_id',$client_id)->where('type','partner')->orderby('created_at', 'DESC')->get();
		ob_start();
		foreach($notelist as $alist){
			$admin = \App\Models\Admin::where('id', $alist->user_id)->first();
			?>
			<tr class="opentaskview" style="cursor:pointer;" id="<?php echo $alist->id; ?>">
				<td></td>
				<td><b><?php echo $alist->category; ?></b>: <?php echo $alist->title; ?></td>
				<td><span class="author-avtar" style="font-size: .8rem;height: 24px;line-height: 24px;width: 24px;min-width: 24px;background: rgb(3, 169, 244);"><?php echo substr($admin->first_name, 0, 1); ?></span></td>
				<td><?php echo $alist->priority; ?></td>
				<td><i class="fa fa-clock"></i> <?php echo $alist->due_date; ?> <?php echo $alist->due_time; ?></td>
				<td><?php
				if($alist->status == 3){
					echo '<span style="color: rgb(113, 204, 83); width: 84px;">Completed</span>';
				}else if($alist->status == 1){
					echo '<span style="color: rgb(3, 169, 244); width: 84px;">In Progress</span>';
				}else if($alist->status == 2){
					echo '<span style="color: rgb(156, 156, 156); width: 84px;">On Review</span>';
				}else{
					echo '<span style="color: rgb(255, 173, 0); width: 84px;">Todo</span>';
				}
				?></td>

			</tr>
			<?php
		}
		return ob_get_clean();
	}

	public function taskdetail(Request $request){
		$notedetail = \App\Models\Task::where('id',$request->task_id)->where('type','partner')->first();
		?>
		<div class="modal-header">
				<h5 class="modal-title" id="taskModalLabel"><i class="fa fa-bag"></i> <?php echo $notedetail->title; ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Status:</label>
						<?php
						if($notedetail->status == 0){
					$comment = 'Todo';
				}else if($notedetail->status == 1){
					$comment = 'In Progress';
				}else if($notedetail->status == 2){
					$comment = 'On Review';
				}else if($notedetail->status == 3){
					$comment = 'Completed';
				}
						?>
						<ul class="navbar-nav navbar-right">
							<li class="dropdown dropdown-list-toggle">
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle taskstatus"><?php echo $comment; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a href="javascript:;" data-statusname="To Do" data-status="0" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										To Do
									</a>
									<a href="javascript:;" data-statusname="In Progress" data-status="1" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										In Progress
									</a>
									<a href="javascript:;" data-statusname="On Review" data-status="2"  data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										On Review
									</a>
									<a href="javascript:;" data-statusname="Completed" data-status="3"  data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changetaskstatus">
										Completed
									</a>
								 </div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Priority:</label>

						<ul class="navbar-nav navbar-right">
							<li class="dropdown dropdown-list-toggle">
								<a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle prioritystatus"><?php echo $notedetail->priority; ?> <i class="fa fa-angle-down"></i></a>
								 <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
									<a href="javascript:;" data-statusname="Low" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Low
									</a>
									<a href="javascript:;" data-statusname="Normal" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Normal
									</a>
									<a href="javascript:;" data-statusname="High" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										High
									</a>
									<a href="javascript:;" data-statusname="Urgent" data-id="<?php echo $notedetail->id; ?>" class="dropdown-item changeprioritystatus">
										Urgent
									</a>
								 </div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Category:</label>
						<br>
						<span><?php echo $notedetail->category; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Due Date:</label>
						<br>
						<span><?php echo $notedetail->due_date; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Due Time:</label>
						<br>
						<span><?php echo date('h:i A', strtotime($notedetail->due_time)); ?></span>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">

				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
					<?php
					$admindetail = \App\Models\Admin::where('id',$notedetail->assignee)->first();
					?>
						<label for="title">Assignee:</label>
						<br>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetail->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetail->first_name; ?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Followers:</label>
						<br>
						<?php
					$admindetailfollowers = \App\Models\Admin::where('id',$notedetail->followers)->first();
					if($admindetailfollowers){
					?>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetailfollowers->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetailfollowers->first_name; ?></span>
						</div>
<?php } ?>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Added By:</label>
						<br>
						<?php
					$admindetailadded = \App\Models\Admin::where('id',$notedetail->user_id)->first();
					?>
						<div style="display: flex;">
						<span  title="Arun " class="col-hr-1 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);color: #fff;display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;
    font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;
}"><b > <?php echo substr($admindetailadded->first_name, 0, 1); ?></b></span>
						<span><?php echo $admindetailadded->first_name; ?></span>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-4 col-lg-4">
					<div class="form-group">
						<label for="title">Description:</label>
						<br>
						<span><?php echo $notedetail->description; ?></span>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Related to:</label>
						<br>
						<span><?php echo $notedetail->related_to; ?></span>
					</div>
				</div>

				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
						<label for="title">Comments:</label>
						<textarea id="comment" class="form-control" name="comment" placeholder="Enter comment here"></textarea>
						<span class="comment-error" style="color:#9f3a38"></span>
					</div>
				</div>
				<div class="col-12 col-md-12 col-lg-12">
					<div class="form-group">
					<input type="hidden" id="taskid" value="<?php echo $notedetail->id; ?>">
						<button class="btn btn-primary savecomment" >Save</button>
					</div>
				</div>

				<div class="col-12 col-md-12 col-lg-12" style="background-color: #f2f2f2;padding: 1rem 2rem 2rem;">
					<h4>Logs</h4>
					<div class="ag-flex ag-flex-column tasklogs">
						<?php
						$datas = TaskLog::where('task_id', $notedetail->id)->orderby('created_at','DESC')->get();
							foreach($datas as $data){
								$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
								?>

								<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;
    border-radius: 4px;
    box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);
    padding: 7px 15px;">
									<div class="ag-flex">
										<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;
    height: 24px;
    line-height: 24px;
    min-width: 24px;
    width: 24px;    color: #fff;
    display: block;
    font-weight: 600;
    letter-spacing: 1px;
    text-align: center;
    border-radius: 50%;
    overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span>
										<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
											<div  class="ag-flex ag-space-between ag-align-start">
												<div class="ag-flex ag-align-start">
													<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
												</div>
												<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
											</div>
											<?php if($data->message != ''){ ?>
											<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
											<?php } ?>
										</div>
									</div>
								</div>

								<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function savecomment(Request $request){
		$obj = new TaskLog;
		$obj->title = 'commented';
		$obj->message = $request->comment;
		$obj->task_id = $request->taskid;
		$obj->created_by = Auth::user()->id;
		$saved = $obj->save();
		if(!$saved)
			{
				$response['status'] 	= 	false;
				$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;

			}
			$datas = TaskLog::where('task_id', $request->taskid)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span>
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div>
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div>
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
			echo json_encode($response);
	}

	public function changetaskstatus(Request $request){
		if(Task::where('id', $request->id)->exists()){

			$obj = Task::find($request->id);
			if($obj->status == 0){
					$precomment = 'Todo';
				}else if($obj->status == 1){
					$precomment = 'In Progress';
				}else if($obj->status == 2){
					$precomment = 'On Review';
				}else if($obj->status == 3){
					$precomment = 'Completed';
				}
			$obj->status = $request->status;
			$saved = $obj->save();
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				if($request->status == 0){
					$comment = 'Todo';
				}else if($request->status == 1){
					$comment = 'In Progress';
				}else if($request->status == 2){
					$comment = 'On Review';
				}else if($request->status == 3){
					$comment = 'Completed';
				}
				if($comment != $precomment){
				$obj = new TaskLog;
				$obj->title = 'changed status from '.$precomment.' to '.$comment;
				$obj->message = '';
				$obj->task_id = $request->id;
				$obj->created_by = Auth::user()->id;
				$saved = $obj->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Task.';
				}
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		$datas = TaskLog::where('task_id', $request->id)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span>
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div>
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div>
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
		echo json_encode($response);
	}

	public function changetaskpriority(Request $request){
		if(Task::where('id', $request->id)->exists()){

			$obj = Task::find($request->id);
			$precomment = $obj->priority;
			$obj->priority = $request->status;
			$saved = $obj->save();
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$comment = $request->status;
				if($comment != $precomment){
				$obj = new TaskLog;
				$obj->title = 'changed priority from '.$precomment.' to '.$comment;
				$obj->message = '';
				$obj->task_id = $request->id;
				$obj->created_by = Auth::user()->id;
				$saved = $obj->save();
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Task.';
				}
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		$datas = TaskLog::where('task_id', $request->id)->orderby('created_at','DESC')->get();
			ob_start();
			foreach($datas as $data){
				$admindetailcreated_by = \App\Models\Admin::where('id',$data->created_by)->first();
				?>
				<div  class="task-log-item text-semi-light-grey col-v-1" style="margin-top: 5px!important;background-color: #fff;border-radius: 4px;box-shadow: 0 1px 1px 0 rgb(0 0 0 / 10%);padding: 7px 15px;">
			<div class="ag-flex">
								<span  title="Arun " class="col-hr-2 ag-avatar ag-avatar--xs" style="position: relative; background: rgb(3, 169, 244);font-size: .8rem;height: 24px;line-height: 24px;min-width: 24px;width: 24px;    color: #fff;display: block;font-weight: 600;letter-spacing: 1px;text-align: center;border-radius: 50%;overflow: hidden;    margin-right: 10px!important;"><b ><?php echo substr($admindetailcreated_by->first_name, 0, 1); ?></b></span>
			<div style="flex: 1;" class="ag-flex ag-flex-column ag-flex-1 task-activity-content">
				<div  class="ag-flex ag-space-between ag-align-start">
					<div class="ag-flex ag-align-start">
						<strong class="text-info col-hr-1 ag-flex-shrink-0"> <?php echo $admindetailcreated_by->first_name; ?>  </strong> <?php echo $data->title; ?>
					</div>
					<span class="text-semi-bold ag-flex-shrink-0"> <?php echo date('Y-m-d h:i A', strtotime($data->created_at)); ?></span>
				</div>
				<?php if($data->message != ''){ ?>
				<p class="col-v-2 text-semi-light-grey task-activity-block"><?php echo $data->message; ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
				<?php
			}
			$dat = ob_get_clean();
			$response['data']	=	$dat;
		echo json_encode($response);
	}

	public function import(Request $request){
		$the_file = $request->file('uploaded_file');
		try{
			$spreadsheet = IOFactory::load($the_file->getRealPath());
			$sheet        = $spreadsheet->getActiveSheet();
			$row_limit    = $sheet->getHighestDataRow();
			$column_limit = $sheet->getHighestDataColumn();
			$row_range    = range( 2, $row_limit );
			$column_range = range( 'S', $column_limit ); //AE
			$startcount = 2;
			$data = array();

			foreach ( $row_range as $row ) {

                /*$data[] = [
                    'partner_name'=>$sheet->getCell( 'B' . $row )->getValue(),
                    'master_category'=>$master_category_id,
                    'partner_type'=>$partner_type_id,
                    'business_reg_no'=>$sheet->getCell( 'E' . $row )->getValue(),
                    'service_workflow'=>$workflow_data_id,
                    'currency'=>$sheet->getCell( 'G' . $row )->getValue(),
                    'email'=>$sheet->getCell( 'H' . $row )->getValue(),
                    'gender'=>$sheet->getCell( 'I' . $row )->getValue(),
                    'country_code'=>$sheet->getCell( 'J' . $row )->getValue(),
                    'phone'=>$sheet->getCell( 'K' . $row )->getValue(),
                    'state'=>$sheet->getCell( 'L' . $row )->getValue(),
                    'country'=>$sheet->getCell( 'M' . $row )->getValue(),
                    'zip'=>$sheet->getCell( 'N' . $row )->getValue(),
                    'password'=>$sheet->getCell( 'O' . $row )->getValue(),
                    'profile_img'=>$sheet->getCell( 'P' . $row )->getValue(),
                    'created_at'=>$sheet->getCell( 'Q' . $row )->getValue(),
                    'updated_at'=>$sheet->getCell( 'R' . $row )->getValue(),
                    'city'=>$sheet->getCell( 'S' . $row )->getValue(),
                    'address'=>$sheet->getCell( 'T' . $row )->getValue(),
                    'fax'=>$sheet->getCell( 'U' . $row )->getValue(),
                    'website'=>$sheet->getCell( 'V' . $row )->getValue(),
                    'status'=>$sheet->getCell( 'W' . $row )->getValue(),
                    'contract_expiry'=>$sheet->getCell( 'X' . $row )->getValue(),
                    'represent_region'=>$sheet->getCell( 'Y' . $row )->getValue(),
                    'commission_percentage'=>$sheet->getCell( 'Z' . $row )->getValue(),
                    'default_super_agent'=>$sheet->getCell( 'AA' . $row )->getValue(),
                    'gst'=>$sheet->getCell( 'AB' . $row )->getValue(),
                    'is_regional'=>$sheet->getCell( 'AC' . $row )->getValue(),
                    'is_archived'=>$sheet->getCell( 'AD' . $row )->getValue(),
                    'level'=>$sheet->getCell( 'AE' . $row )->getValue(),
				];*/

                //Get master_category id
                $master_category_val = trim($sheet->getCell( 'C' . $row )->getValue());
                if($master_category_val){
                    $cat_data = DB::table('categories')->select('categories.id')->where('category_name', 'like', '%'.$master_category_val.'%')->get();
                    if(!empty($cat_data)){
                        $master_category_id = $cat_data[0]->id;
                    } else {
                        $master_category_id = "";
                    }
                } else {
                    $master_category_id = "";
                }
                //dd($master_category_id);

                //Get partner type id
                $partner_type_val = trim($sheet->getCell( 'D' . $row )->getValue()); //dd($partner_type_val);
                if($partner_type_val){
                    $partner_type_data = DB::table('partner_types')->select('partner_types.id')->where('category_id', $master_category_id)->where('name', 'like', '%'.$partner_type_val.'%')->get();
                    if(!empty($partner_type_data)){
                        $partner_type_id = $partner_type_data[0]->id;
                    } else {
                        $partner_type_id = "";
                    }
                } else {
                    $partner_type_id = "";
                }
                //dd($partner_type_id);

                //Get service_workflow id
                $service_workflow_val = trim($sheet->getCell( 'F' . $row )->getValue()); //dd($service_workflow_val);
                if($service_workflow_val){
                    $workflow_data = DB::table('workflows')->select('workflows.id')->where('name', 'like', '%'.$service_workflow_val.'%')->get();
                    //dd($workflow_data);
                    if(!empty($workflow_data)){
                        $workflow_data_id = $workflow_data[0]->id;
                    } else {
                        $workflow_data_id = "";
                    }
                } else {
                    $workflow_data_id = "";
                }
                //dd($workflow_data_id);

                //Get is_regional value
                $is_regional_val = trim($sheet->getCell( 'R' . $row )->getValue());
                if($is_regional_val == 'Regional'){
                    $is_regional_val_bit = 1;
                } else {
                    $is_regional_val_bit = 0;
                }

                $data[] = [
                    'partner_name'=>$sheet->getCell( 'B' . $row )->getValue(),
                    'master_category'=>$master_category_id,
                    'partner_type'=>$partner_type_id,
                    'business_reg_no'=>$sheet->getCell( 'E' . $row )->getValue(),
                    'service_workflow'=>$workflow_data_id,
                    'currency'=>$sheet->getCell( 'G' . $row )->getValue(),
                    'email'=>$sheet->getCell( 'H' . $row )->getValue(),
                    'country_code'=>$sheet->getCell( 'I' . $row )->getValue(),
                    'phone'=>$sheet->getCell( 'J' . $row )->getValue(),
                    'state'=>$sheet->getCell( 'K' . $row )->getValue(),
                    'country'=>$sheet->getCell( 'L' . $row )->getValue(),
                    'zip'=>$sheet->getCell( 'M' . $row )->getValue(),
                    'city'=>$sheet->getCell( 'N' . $row )->getValue(),
                    'address'=>$sheet->getCell( 'O' . $row )->getValue(),
                    'fax'=>$sheet->getCell( 'P' . $row )->getValue(),
                    'website'=>$sheet->getCell( 'Q' . $row )->getValue(),
                    'is_regional'=>$is_regional_val_bit,
                    'level'=>$sheet->getCell( 'S' . $row )->getValue(),
				];
                $startcount++;
			}
            //dd($data);
			DB::table('partners')->insert($data);
            DB::table('check_partners')->insert($data);
		} catch (Exception $e) {
			$error_code = $e->errorInfo[1];
			return back()->withErrors('There was a problem uploading the data!');
		}
		return back()->withSuccess('Great! Data has been successfully uploaded.');
	}
}

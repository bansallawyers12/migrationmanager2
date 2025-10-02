<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Product;
use App\Models\Partner;
 
use Auth;
use Config;

class ServicesController extends Controller
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
		
		$lists = array();
		if($request->has('sf')){
			$search_term_first 		= 	$request->input('sf');
			$cat 		= 	$request->input('cat');
			if(trim($search_term_first) == '1')	
			{
				$query 		= Partner::where('master_category', '=', $cat); 
				if($request->has('s')){
					$s 		= 	$request->input('s');
					$query->where('partner_name','LIKE','%'.$s.'%'); 
				}
			}else{
				$partnerids 		= Partner::where('master_category', '=', $cat)->get(); 
				$ids = array();
				foreach($partnerids as $id){
					$ids[] = $id->id;
				}
				$query 		= Product::whereIn('partner', $ids); 
				if($request->has('s')){
					$s 		= 	$request->input('s');
					$query->where('name','LIKE','%'.$s.'%'); 
				}
			}
			

			$lists		= $query->sortable(['id' => 'desc'])->paginate(config('constants.limit'));
		}
			//for all data

		
		
		return view('Admin.services.index',compact(['lists'])); 	
		
		
		//return view('Admin.services.index');	 
	}
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		$services = Service::where('id','!=','')->orderby('title','ASC')->get();
		$service = array();
		foreach($services as $ser){
			$service[] = array(
				'id' => $ser['id'],
				'name' => $ser['title'],
				'parent' => $ser['parent'],
			);
			
		}
		$ob = new Service;
		$tree = $ob->buildTree($service);
		return view('Admin.services.create',compact(['tree']));	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'title' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Service;
			$obj->title	=	@$requestData['title'];
			$obj->parent		=	@$requestData['parent'];
			$obj->description			=	@$requestData['description'];
			$obj->services_icon	=	@$requestData['services_icon'];
			
			/* Profile Image Upload Function Start */						  
					if($request->hasfile('services_image')) 
					{	
						$service_img = $this->uploadFile($request->file('services_image'), Config::get('constants.service_imgs'));
					}
					else
					{
						$service_img = NULL;
					}		
				/* Profile Image Upload Function End */	
			$obj->services_image			=	@$service_img;
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/services')->with('success', 'Services Added Successfully');
			}				
		}	

		return view('Admin.services.create');	
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'title' => 'required|max:255'
									  ]);
								  					  
			$obj							= 	Service::find(@$requestData['id']);
						
			$obj->title	=	@$requestData['title'];
			$obj->parent		=	@$requestData['parent'];
			$obj->description			=	@$requestData['description'];
			$obj->services_icon	=	@$requestData['services_icon'];
			
			/* Profile Image Upload Function Start */						  
			 if($request->hasfile('services_image')) 
			{ 	
				/* Unlink File Function Start */ 
					 if($requestData['services_image'] != '')
						{
							$this->unlinkFile($requestData['old_service_img'], Config::get('constants.service_imgs'));
						} 
				/* Unlink File Function End */
				
				 $service_img = $this->uploadFile($request->file('services_image'), Config::get('constants.service_imgs'));
			}
			else
			{
				$service_img = @$requestData['old_service_img'];
			}		 
		/* Profile Image Upload Function End */
			 $obj->services_image			=	@$service_img;
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/services')->with('success', 'Services Edited Successfully');
			}				
		}

		else
		{		
			$services = Service::where('id','!=','')->orderby('title','ASC')->get();
			$service = array();
			foreach($services as $ser){
				$service[] = array(
					'id' => $ser['id'],
					'name' => $ser['title'],
					'parent' => $ser['parent'],
				);
				
			}
			$ob = new Service;
			$tree = $ob->buildTree($service);
			
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Service::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Service::find($id);
					return view('Admin.services.edit', compact(['fetchedData', 'tree']));
				}
				else
				{
					return Redirect::to('/admin/services')->with('error', 'Service Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/services')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function servicemodal(Request $request){
		ob_start();
		?>
		<form method="post" action="<?php echo \URL::to('/admin/interested-service'); ?>" name="inter_servform_serv" autocomplete="off" id="inter_servform_serv" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="contact">Select Contact <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control contact select2" id="contact" name="client_id">
									<option value="">Please Select Contact</option>
									<?php 
										foreach(\App\Models\Admin::where('role',7)->get() as $wlist){
									?>
										<option value="<?php echo $wlist->id; ?>"><?php echo $wlist->first_name.' '.$wlist->last_name; ?>
											(<?php echo $wlist->email.' '.$wlist->phone; ?>)</option>
										<?php } ?>
								</select>
								<span class="custom-error contact_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="intrested_workflow">Select Workflow <span class="span_req">*</span></label>
								<select data-valid="required" class="form-control select2" id="intrested_workflow" name="workflow">
									<option value="">Please Select a Workflow</option>
									
								</select>
								<span class="custom-error workflow_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="partner">Select Partner <span class="span_req">*</span></label> 
								
								<input readonly type="text" name="partnername" class="servpartnername form-control" >
								<input type="hidden" name="partner" class="servpartner_id" >
								
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="product">Select Product</label> 
								<?php if($request->servicetype == 'product'){ ?>
								<input readonly type="text" name="servproduct" class="servproductname form-control">
								<input type="hidden" name="product" class="servproduct_id" >
								<?php }else{
									?>
									<select class="form-control productselect2" data-valid="required" name="product">
									<option value="">Select Product</option>
									<?php
									$partnerproduct = \App\Models\Product::where('partner', $request->partnerid)->get();
									foreach($partnerproduct as $partnerp){
									?>
										<option value="<?php echo $partnerp->id; ?>"><?php echo $partnerp->name; ?></option>
									<?php
									}
									?>
									</select> 
									<?php
								} ?>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="branch">Select Branch</label> 
								<select data-valid="required" class="form-control select2" id="intrested_branch" name="branch">
									<option value="">Please Select a Branch</option>
									
								</select> 
								<span class="custom-error branch_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group"> 
								<label for="expect_start_date">Expected Start Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
						<input  type="text" name="expect_start_date" class="datepicker form-control" >
									
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_start_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group"> 
								<label for="expect_win_date">Expected Win Date</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input  type="text" name="expect_win_date" class="datepicker form-control" >
									
								</div> 
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error expect_win_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('inter_servform_serv')" type="button" class="btn btn-primary">Submit</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form> 
		<?php
		return ob_get_clean();
	}
}

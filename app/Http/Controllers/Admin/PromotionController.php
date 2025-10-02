<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\Promotion; 
  
use Auth; 
use Config;

class PromotionController extends Controller
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
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			
			
			$requestData 		= 	$request->all();
			
			$obj				= 	new Promotion; 
			$obj->promotion_title	=	@$requestData['promotion_title'];
			$obj->promotion_desc	=	@$requestData['promotion_desc'];
			$obj->promotion_start_date	=	@$requestData['promotion_start_date'];
			$obj->promotion_end_date	=	@$requestData['promotion_end_date'];
			$obj->apply_to	=	@$requestData['apply_to'];
			$obj->selectproduct	=	implode(',',@$requestData['selectproduct']);
			$obj->user_id	=	Auth::user()->id;
			$obj->partner_id	=	@$requestData['client_id'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully added a Promotion.';
			}	

			echo json_encode($response);				
		}	

		
	}
	
	public function getpromotions(Request $request){
	
		$promotionslist = Promotion::where('partner_id',$request->clientid)->orderby('created_at','DESC')->get();
		foreach($promotionslist as $promotion){
			$countproducts = 0;
			$countbranches = 0;
			if($promotion->apply_to == 'All Products'){
				$countproducts = \App\Models\Product::where('partner', $promotion->partner_id)->count();
				$countbranches = \App\Models\PartnerBranch::where('partner_id', $promotion->partner_id)->count();
			}else{
				
			}
			ob_start();
									?>
			<div class="promotion_col" id="contact_<?php echo $promotion->id; ?>"> 
				<div class="promotion_content">
				<?php if($promotion->status == 1){ ?>
					<span class="text-success"><b>Active</b></span>
				<?php }else{ ?>
					<span class="text-danger"><b>Inactive</b></span>
				<?php }?>
					<div class="" style="margin-top: 15px!important;">
						<h4><?php echo $promotion->promotion_title; ?></h4>
						<p><?php echo @$promotion->promotion_desc == "" ? config('constants.empty') : str_limit(@$promotion->promotion_desc, '50', '...'); ?></p>
					</div>
					<div class="" style="margin-top: 15px!important;">
						<div class="row">
							<div class="col-md-6">
							<span class="text-semi-bold text-underline">For Branches</span>
							</div>
							<div class="col-md-6">
							<span  class=""><?php echo $countbranches; ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
							<span class="text-semi-bold text-underline">For Products</span>
							</div>
							<div class="col-md-6">
							<span  class=""><?php echo $countproducts; ?></span>
							</div>
						</div>
					</div>
					<div class="" style="margin-top: 15px!important;">
						<div class="row">
							<div class="col-md-6">
							<span ><b>Start Date</b></span>
							<p><?php echo $promotion->promotion_start_date; ?></p>
							</div>
							<div class="col-md-6">
							<span ><b>Expiry Date</b></span>
							<p><?php echo $promotion->promotion_end_date; ?></p>
							</div>
						</div>
						
					</div>
				</div>
				<div class="extra_content">
					<div class="left">
						<div class="dropdown d-inline dropdown_ellipsis_icon">
							<a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
							<div class="dropdown-menu">
								<a class="dropdown-item openpromotonform" data-id="<?php echo $promotion->id; ?>" href="javascript:;">Edit</a>
							</div>
						</div>
					</div>   
					<div class="right">
						<div class="custom-switches">
							<label class="custom-switch">
								<input type="checkbox" data-status="<?php echo $promotion->status; ?>" data-id="<?php echo $promotion->id; ?>" name="custom-switch-checkbox" class="custom-switch-input changepromotonstatus" <?php if($promotion->status == 1){ ?> checked <?php } ?>>
								<span class="custom-switch-indicator"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
		<?php }
				echo ob_get_clean();
				die;				
	}
	public function getpromotioneditform(Request $request){
		ob_start();
		$obj = \App\Models\Promotion::find($request->id);
		if($obj){
		?>
		<form method="post" action="<?php echo \URL::to('/admin/promotion/edit'); ?>" id="editpromotionform" name="editpromotionform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="client_id" value="<?php echo $obj->partner_id; ?>">
				<input type="hidden" name="id" value="<?php echo $obj->id; ?>">
					<div class="row">
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="promotion_title">Promotion Title <span class="span_req">*</span></label>
								<input type="text" name="promotion_title" class="form-control" data-valid="required" autocomplete="off" placeholder="Enter Promotion Title"  value="<?php echo $obj->promotion_title; ?>">
								
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group">
								<label for="promotion_desc">Description</label>
								<textarea class="form-control" name="promotion_desc" placeholder="Description"><?php echo $obj->promotion_desc; ?></textarea>
								<span class="custom-error promotion_desc_error" role="alert">
									<strong></strong>
								</span> 
							</div>	
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group"> 
								<label for="promotion_start_date">Start Date</label>
								<div class="input-group">
									<div class="input-group-prepend"> 
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="promotion_start_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date"  value="<?php echo $obj->promotion_start_date; ?>">
									
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error promotion_start_date_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="form-group"> 
								<label for="promotion_end_date">End Date</label>
								<div class="input-group">
									<div class="input-group-prepend"> 
										<div class="input-group-text">
											<i class="fas fa-calendar-alt"></i>
										</div>
									</div>
									<input type="text" name="promotion_end_date" class="form-control datepicker" data-valid="required" autocomplete="off" placeholder="Select Date"  value="<?php echo $obj->promotion_end_date; ?>">
									
								</div>
								<span class="span_note">Date must be in YYYY-MM-DD (2012-12-22) format.</span>
								<span class="custom-error promotion_end_date_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
						
							<div class="form-group">
								<label style="display:block;" for="apply_to">Apply To:</label>
								<div class="form-check form-check-inline">
									<input <?php if($obj->apply_to == 'All Products'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="all_product" value="All Products" name="apply_to" >
									<label class="form-check-label" for="all_product">All Products</label>
								</div>
								<div class="form-check form-check-inline">
									<input <?php if($obj->apply_to == 'Select Products'){ echo 'checked'; } ?> class="form-check-input" type="radio" id="select_products" value="Select Products" name="apply_to">
									<label class="form-check-label" for="select_products">Select Products</label>
								</div>
								<span class="custom-error apply_to_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<?php
						$exploder = explode(',', $obj->selectproduct);
						?>
						<div class="col-12 col-md-12 col-lg-12 ifselectproducts" multiple <?php if($obj->apply_to == 'Select Products'){}else{ ?>style="display:none;<?php } ?>">
							<div class="form-group">
								<select  class="form-control productselect2"  name="selectproduct[]">
										 <option></option>
									<?php foreach(\App\Models\Product::where('partner', $obj->partner_id)->orderby('created_at','DESC')->get() as $plist){ ?>
										<option <?php if(in_array($plist->id, $exploder)){ echo 'selected'; } ?> value="<?php echo $plist->id; ?>"><?php echo $plist->name; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editpromotionform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>  
				
		<?php
		}
		return ob_get_clean();
	}
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$obj				= 	Promotion::find(@$requestData['id']); 
			$obj->promotion_title	=	@$requestData['promotion_title'];
			$obj->promotion_desc	=	@$requestData['promotion_desc'];
			$obj->promotion_start_date	=	@$requestData['promotion_start_date'];
			$obj->promotion_end_date	=	@$requestData['promotion_end_date'];
			$obj->apply_to	=	@$requestData['apply_to'];
			$obj->selectproduct	=	implode(',',@$requestData['selectproduct']);
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Promotion.';
			}	

			echo json_encode($response);				
		}
	}
	
	public function changepromotionstatus(Request $request){
		if(Promotion::where('id', $request->id)->exists()){
			if($request->status == 1){
				$status = 0;
			}else{
				$status = 1;
			}
			
			$obj = Promotion::find($request->id);
			$obj->status = $status;
			$saved = $obj->save(); 
			if(!$saved)
			{
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
			}
			else
			{
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully updated a Promotion.';
			}				
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		
		echo json_encode($response);	
	}
}

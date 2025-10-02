<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Models\Admin;
use App\Models\Product;
use App\Models\AcademicRequirement;
use App\Models\ProductAreaLevel;
use App\Models\FeeOption;
use App\Models\FeeOptionType;
 
use Auth;
use Config;

class ProductsController extends Controller
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
	
		$query 		= Product::where('id', '!=', ''); 
		  
		$totalData 	= $query->count();	//for all data
		if ($request->has('name')) 
		{
			$name 		= 	$request->input('name'); 
			if(trim($name) != '')
			{
				$query->where('name', 'LIKE', '%'.$name.'%');
					
			}
		}
		if ($request->has('branch')) 
		{
			$branch 		= 	$request->input('branch'); 
			if(trim($branch) != '')
			{
				$query->whereHas('branchdetail', function ($q) use($branch){
					$q->where('name','Like', '%'.$branch.'%');
				});
					
			}
		}
		if ($request->has('branch')) 
		{
			$branch 		= 	$request->input('branch'); 
			if(trim($branch) != '')
			{
				$query->whereHas('branchdetail', function ($q) use($branch){
					$q->where('name','Like', '%'.$branch.'%');
				});
					
			}
		}
		if ($request->has('partner')) 
		{
			$branch 		= 	$request->input('partner'); 
			if(trim($branch) != '')
			{
				$query->whereHas('partnerdetail', function ($q) use($branch){
					$q->where('partner_name','Like', '%'.$branch.'%');
				});
					
			}
		}
		
		$lists		= $query->sortable(['id' => 'desc'])->paginate(20);
		
		
		return view('Admin.products.index', compact(['lists', 'totalData'])); 	
				
		//return view('Admin.products.index'); 	 
	}
	
	
	public function create(Request $request)
	{
		//check authorization end
		//return view('Admin.users.create',compact(['usertype']));	
		
		return view('Admin.products.create');	
	}
	
	public function store(Request $request)
	{		
		//check authorization end
		if ($request->isMethod('post')) 
		{
			$this->validate($request, [
										'name' => 'required|max:255'
									  ]);
			
			$requestData 		= 	$request->all();
			 
			$obj				= 	new Product; 
			$obj->name	=	@$requestData['name'];
			$obj->partner	=	@$requestData['partner'];
			$obj->branches	=	@$requestData['branches'];
			$obj->product_type	=	@$requestData['product_type'];
			$obj->revenue_type	=	@$requestData['revenue_type']; 
			$obj->duration	=	@$requestData['duration'];
			$obj->intake_month	=	@$requestData['intake_month'];
			$obj->description	=	@$requestData['description'];
			$obj->note	=	@$requestData['note'];
			
			$saved				=	$obj->save();  
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			else
			{
				return Redirect::to('/admin/products')->with('success', 'Products Added Successfully');
			}				
		}	

		return view('Admin.products.create');	 
	}
	
	public function edit(Request $request, $id = NULL)
	{
	
		//check authorization end
		
		if ($request->isMethod('post')) 
		{
			$requestData 		= 	$request->all();
			
			$this->validate($request, [										
										'name' => 'required|max:255'
									  ]);
								  					  
			$obj							= 	Product::find(@$requestData['id']);
						
			$obj->name	=	@$requestData['name'];
			$obj->partner	=	@$requestData['partner'];
			$obj->branches	=	@$requestData['branches'];
			$obj->product_type	=	@$requestData['product_type'];
			$obj->revenue_type	=	@$requestData['revenue_type']; 
			$obj->duration	=	@$requestData['duration'];
			$obj->intake_month	=	@$requestData['intake_month'];
			$obj->description	=	@$requestData['description'];
			$obj->note	=	@$requestData['note'];
			
			$saved							=	$obj->save();
			
			if(!$saved)
			{
				return redirect()->back()->with('error', Config::get('constants.server_error'));
			}
			
			else
			{
				return Redirect::to('/admin/products')->with('success', 'Products Edited Successfully');
			}				
		}

		else
		{		
			if(isset($id) && !empty($id))
			{
				
				$id = $this->decodeString($id);	
				if(Product::where('id', '=', $id)->exists()) 
				{
					$fetchedData = Product::find($id);
					return view('Admin.products.edit', compact(['fetchedData']));
				}
				else 
				{
					return Redirect::to('/admin/products')->with('error', 'Products Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/products')->with('error', Config::get('constants.unauthorized'));
			}		
		} 	
		
	}
	
	public function detail(Request $request, $id = NULL){
		if(isset($id) && !empty($id))  
			{				
				$id = $this->decodeString($id);	
				if(Product::where('id', '=', $id)->exists()) 
				{ 
					$fetchedData = Product::find($id);
					return view('Admin.products.detail', compact(['fetchedData']));
				}
				else 
				{  
					return Redirect::to('/admin/products')->with('error', 'Products Not Exist');
				}	
			}
			else
			{
				return Redirect::to('/admin/products')->with('error', Config::get('constants.unauthorized'));
			}
	}
	
	public function getrecipients(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where(
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){
				$items[] = array('partner_name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	
	public function getallproducts(Request $request){
		$squery = $request->q;
		if($squery != ''){
			
			 $partners = \App\Models\Admin::where('is_archived', '=', 0)
       ->where('role', '=', 7)
       ->where( 
           function($query) use ($squery) {
             return $query
                    ->where('email', 'LIKE', '%'.$squery.'%')
                    ->orwhere('partner_name', 'LIKE','%'.$squery.'%');
            })
            ->get();
			
			$items = array();
			foreach($partners as $partner){ 
				$items[] = array('partner_name' => $partner->partner_name,'email'=>$partner->email,'status'=>'Partner','id'=>$partner->id,'cid'=>base64_encode(convert_uuencode(@$partner->id)));
			}
			
			echo json_encode(array('items'=>$items));
		}
	}
	
	public function saveacademic(Request $request){
		$requestData 		= 	$request->all();
			 if(AcademicRequirement::where('product_id', @$requestData['client_id'])->exists()){
				 $ac = AcademicRequirement::where('product_id', @$requestData['client_id'])->first();
				 $obj				= 	 AcademicRequirement::find($ac->id); 
			 }else{
				$obj				= 	new AcademicRequirement;  
			 }
			
			$obj->degree			=	@$requestData['degree_level'];
			$obj->academic_score_type	=	@$requestData['academic_score_type'];
			$obj->academic_score_per	=	@$requestData['academic_score'];
			$obj->user_id	=	Auth::user()->id;
			$obj->product_id	=	@$requestData['client_id'];
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		
			}
			else
			{
				$data = '<div class="row"><div class="col-md-6"><strong>Degree Level</strong><p>'.$obj->degree.'</p></div><div class="col-md-6"><strong>Academic Score</strong><p>'.$obj->academic_score_per.' '.$obj->academic_score_type.'</p></div></div>';
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully added a Academic Requirements.';
					$response['data']	=	$data;
					$response['requirment']	=	$obj;
			}	
			echo json_encode($response);	
	}
	
	public function saveotherinfo(Request $request){
		$requestData 		= 	$request->all();
			 if(ProductAreaLevel::where('product_id', @$requestData['client_id'])->exists()){
				 $ac = ProductAreaLevel::where('product_id', @$requestData['client_id'])->first();
				 $obj				= 	 ProductAreaLevel::find($ac->id); 
			 }else{
				$obj				= 	new ProductAreaLevel;  
			 }
			
			$obj->subject_area			=	@$requestData['subject_area'];
			$obj->subject	=	@$requestData['subject'];
			$obj->degree	=	@$requestData['degree_level'];
		
			$obj->product_id	=	@$requestData['client_id'];
			$saved				=	$obj->save();  
			
			
			if(!$saved)
			{
				
				$response['status'] 	= 	false;
			$response['message']	=	'Please try again';
		
			}
			else
			{
				$subjectarea = \App\Models\SubjectArea::where('id', $obj->subject_area)->first();
				$subject = \App\Models\Subject::where('id', $obj->subject)->first();
				$data = '<div class="row"><div class="col-md-4"><strong>Subject Area</strong><p>'.$subjectarea->name.'</p></div><div class="col-md-4"><strong>Subject</strong><p>'.$subject->name.'</p></div><div class="col-md-4"><strong>Degree Level</strong><p>'.$obj->degree.'</p></div></div>';
				$response['status'] 	= 	true;
				$response['message']	=	'You’ve successfully added a Subject Area.';
					$response['data']	=	$data;
			}	
			echo json_encode($response);	
	}
	
	
	public function getotherinfo(Request $request){
		$ac = ProductAreaLevel::where('product_id', @$request->id)->first();
		ob_start();
		?>
		<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Subject Area <span class="span_req">*</span></label> 	
							<select data-valid="" class="form-control subject_area select2" id="subjectlist" name="subject_area">
									<option value="">Please Select Subject Area</option>
									<?php
									foreach(\App\Models\SubjectArea::all() as $sublist){
										?>
										<option <?php if($ac->subject_area == $sublist->id){ echo 'selected'; } ?> value="<?php echo $sublist->id; ?>"><?php echo $sublist->name; ?></option>
										<?php
									}
									?>
								</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Subject<span class="span_req">*</span></label> 	
							<select data-valid="" class="form-control subject select2" id="subject" name="subject">
									<option value="">Please Select Subject</option>
									<?php
									foreach(\App\Models\Subject::where('subject_area',$ac->subject_area) ->orderby('name','ASC')->get() as $sublist){
										?>
										<option <?php if($ac->subject == $sublist->id){ echo 'selected'; } ?> value="<?php echo $sublist->id; ?>"><?php echo $sublist->name; ?></option>
										<?php
									}
									?>
								</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-6 col-lg-6">
						<div class="form-group">
							<label for="degree_level">Degree Level</label> 	
							<select data-valid="required" class="form-control degree_level select2" name="degree_level">
								<option value=""></option>
								<option <?php if($ac->degree == 'Bachelor'){ echo 'selected'; } ?> value="Bachelor">Bachelor</option>
									<option value="Certificate" <?php if($ac->degree == 'Certificate'){ echo 'selected'; } ?>>Certificate</option>
									<option value="Diploma" <?php if($ac->degree == 'Diploma'){ echo 'selected'; } ?>>Diploma</option>
									<option value="High School" <?php if($ac->degree == 'High School'){ echo 'selected'; } ?>>High School</option>
									<option value="Master" <?php if($ac->degree == 'Master'){ echo 'selected'; } ?>>Master</option>
							</select>
							<span class="custom-error degree_level_error" role="alert">
								<strong></strong>
							</span> 
						</div>
					</div>
					<div class="col-12 col-md-12 col-lg-12">
						<button onclick="customValidate('editsubjectarea')" type="button" class="btn btn-primary">Save</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
		<?php
		return ob_get_clean();
		
	}
	
	
	public function savefee(Request $request){
		$requestData = $request->all();
		$obj = new FeeOption;
		$obj->user_id = Auth::user()->id;
		$obj->product_id = $requestData['product_id'];
		$obj->name = $requestData['fee_option_name'];
		$obj->country = $requestData['country_residency'];
		$obj->installment_type = $requestData['degree_level'];
		$saved = $obj->save();
		if($saved){
			$course_fee_type = $requestData['course_fee_type'];
			for($i = 0; $i< count($course_fee_type); $i++){
				$objs = new FeeOptionType;
				$objs->fee_id = $obj->id;
				$objs->fee_type = $requestData['course_fee_type'][$i];
				$objs->inst_amt = $requestData['installment_amount'][$i];
				$objs->installment = $requestData['installment'][$i];
				$objs->total_fee = $requestData['total_fee'][$i];
				$objs->claim_term = $requestData['claimable_terms'][$i];
				$objs->commission = $requestData['commission'][$i];
				$objs->quotation = @$requestData['add_quotation'][$i];
				$saved = $objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'Fee Option added successfully';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		
		echo json_encode($response);	
	}
	
	public function getallfees(Request $request){
		$feeoptions = FeeOption::where('product_id', $request->clientid)->orderby('created_at', 'DESC')->get();
		ob_start();
		foreach($feeoptions as $feeoption){
			
	?>
		<div class="feeitem">
			<div class="row">
				<div class="col-md-10">
					<h4 class="text-info"><?php echo $feeoption->name; ?></h4>
				</div>
				<div class="col-md-2">
					<a href="javascript:;" class="editfeeoption" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-edit"></i></a>
					<a href="javascript:;" class="deletenote" data-href="deletefee" data-id="<?php echo $feeoption->id; ?>"><i class="fa fa-trash"></i></a>
				</div>
				<div class="col-md-2">
					<div class="validfor">
						<span>Valid For</span><br>
						<div class=""><b><?php echo $feeoption->country; ?></b></div>
					</div>
					<div class="installmenttype">
						<span>Installment Type</span><br>
						<div class=""><b><?php echo $feeoption->installment_type; ?></b></div>
					</div>
				</div>
				<?php
				$feeoptiontype = \App\Models\FeeOptionType::where('fee_id', $feeoption->id)->get();
				
				?>
				<div class="col-md-8">
					<div class="validfor">
						<span>Fee Breakdown</span><br>
					<?php $totlfee = 0; foreach($feeoptiontype as $feeoptiontyp){
						$totlfee += $feeoptiontyp->total_fee;
						?>
						<div class="">
							<span><b><?php echo $feeoptiontyp->fee_type; ?></b></span><span> <?php echo $feeoptiontyp->installment; ?> Per Month @ AUD <?php echo $feeoptiontyp->inst_amt; ?></span><span style="margin-left: 24px;"><b>AUD <?php echo $feeoptiontyp->total_fee; ?></b></span>
							
						</div>
					<?php } ?>
					</div>
					
				</div>
				
				<div class="col-md-2">
					<div class="validfor">
						<span>Total Fees</span><br>
						<div class="text-info"><h4>AUD</h4></div>
					
						<div class="text-info"><h4><?php echo number_format($totlfee,2,'.',''); ?></h4></div>
					</div>
					
				</div>
			</div>
			<hr>
		</div>
	<?php }
return ob_get_clean();	
	}
	
	public function editfee(Request $request){
		$fetchedData = FeeOption::where('id', $request->id)->first();
		if($fetchedData){
			ob_start();
			?>
			<form method="post" action="<?php echo \URL::to('/admin/editfee'); ?>" name="editfeeform" id="editfeeform" autocomplete="off" enctype="multipart/form-data">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="id" value="<?php echo $fetchedData->id; ?>">
				<input type="hidden" name="product_id" value="<?php echo $fetchedData->product_id; ?>">
					<div class="row">
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="fee_option_name">Fee Option Name <span class="span_req">*</span></label> 	
								<input type="text" value="<?php echo $fetchedData->name; ?>" class="form-control selectedappsubject" data-valid="required" placeholder="Enter Fee Option Name" name="fee_option_name">
								
								<span class="custom-error feeoption_name_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group">
								<label for="country_residency">Country of Residency <span class="span_req">*</span></label> 
								<select class="form-control residencyelect2" name="country_residency" data-valid="required">
								<option value="">Select Country</option>
								<?php
									foreach(\App\Models\Country::all() as $list){
										?>
										<option <?php if($fetchedData->country == $list->name){ echo 'selected'; } ?> value="<?php echo @$list->name; ?>"><?php echo @$list->name; ?></option>
										<?php
									}
									?>
								</select>
								<span class="custom-error country_residency_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="form-group"> 
								<label for="degree_level">Installment Type <span class="span_req">*</span></label> 
								<select data-valid="required" class="form-control degree_level edit_installment_type select2" name="degree_level">
									<option value="">Select Type</option>
									<option value="Full Fee" <?php if($fetchedData->installment_type == "Full Fee"){ echo 'selected'; } ?>>Full Fee</option>
									<option value="Per Year" <?php if($fetchedData->installment_type == "Per Year"){ echo 'selected'; } ?>>Per Year</option>
									<option value="Per Month" <?php if($fetchedData->installment_type == "Per Month"){ echo 'selected'; } ?>>Per Month</option>
									<option value="Per Term" <?php if($fetchedData->installment_type == "Per Term"){ echo 'selected'; } ?>>Per Term</option>
									<option value="Per Trimester" <?php if($fetchedData->installment_type == "Per Trimester"){ echo 'selected'; } ?>>Per Trimester</option>
									<option value="Per Semester" <?php if($fetchedData->installment_type == "Per Semester"){ echo 'selected'; } ?>>Per Semester</option>
									<option value="Per Week" <?php if($fetchedData->installment_type == "Per Week"){ echo 'selected'; } ?>>Per Week</option>
									<option value="Installment" <?php if($fetchedData->installment_type == "Installment"){ echo 'selected'; } ?>>Installment</option>
								</select>
								<span class="custom-error degree_level_error" role="alert">
									<strong></strong>
								</span> 
							</div>
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<div class="table-responsive"> 
								<table class="table text_wrap" id="productitemview">
									<thead>
										<tr> 
											<th>Fee Type <span class="span_req">*</span></th>
											<th>Installment Amount <span class="span_req">*</span></th>
											<th>Installments <span class="span_req">*</span></th>
											<th>Total Fee</th>
											<th>Claimable Terms</th>
											<th>Commission %</th>
											<th>Add in quotation</th>
										</tr> 
									</thead>
									<tbody class="tdata">
									<?php
									$total_fee = 0;
									$i = 0;
											$feeoptiontypes = \App\Models\FeeOptionType::where('fee_id', $fetchedData->id)->get();
												foreach($feeoptiontypes as $feeoptiontype){
													$total_fee += $feeoptiontype->total_fee;
									?>
										<tr class="add_fee_option cus_fee_option">
											<td>
												<select data-valid="required" class="form-control course_fee_type " name="course_fee_type[]">
													<option value="">Select Type</option>
													<?php foreach(\App\Models\FeeType::all() as $feetypes){ ?>
													<option <?php if($feeoptiontype->fee_type == $feetypes->name){ echo 'selected'; } ?> value="<?php echo $feetypes->name; ?>"><?php echo $feetypes->name; ?></option>
													<?php } ?>
												
												</select>
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->inst_amt; ?>" class="form-control installment_amount" name="installment_amount[]">
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->installment; ?>" class="form-control installment" name="installment[]">
											</td>
											<td class="total_fee"><span><?php echo $feeoptiontype->total_fee; ?></span><input type="hidden"  class="form-control total_fee_am" value="<?php echo $feeoptiontype->total_fee; ?>" name="total_fee[]"></td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->claim_term; ?>" class="form-control claimable_terms" name="claimable_terms[]">
											</td>
											<td>
												<input type="number" value="<?php echo $feeoptiontype->commission; ?>" class="form-control commission" name="commission[]">
											</td>
											<td>
												<input value="1" <?php if($feeoptiontype->quotation == 1){ echo 'checked'; } ?> class="add_quotation" type="checkbox" name="add_quotation[]">
												<?php if($i != 0){ ?>
												<a href="javascript:;" class="removefeetype"><i class="fa fa-trash"></i></a>
												<?php } ?>
											</td>
									
										</tr>
										<?php $i++; } ?>
									</tbody>
									<tfoot>
										<tr>
											
											<td colspan="3" style="text-align: right;"><b>Net Total</b></td>
											<td class="net_totl text-info"><?php echo number_format($total_fee,2,'.',''); ?></td>
											<td colspan="3"></td>
										</tr>
									</tfoot>
								</table>	
							</div>
							<div class="fee_option_addbtn">
								<a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> Add Fee</a>
							</div>
							
						</div>
						<div class="col-12 col-md-12 col-lg-12">
							<button onclick="customValidate('editfeeform')" type="button" class="btn btn-primary">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			<?php
			return ob_get_clean();
		}else{
			echo '<h4>Record Not FOund</h4>';
		}
		die;
	}
	
	
	public function editfeeform(Request $request){
		$requestData = $request->all();
		$obj = FeeOption::find($request->id);
		$obj->name = $requestData['fee_option_name'];
		$obj->country = $requestData['country_residency'];
		$obj->installment_type = $requestData['degree_level'];
		$saved = $obj->save();
		if($saved){
			FeeOptionType::where('fee_id',$request->id)->delete();
			$course_fee_type = $requestData['course_fee_type'];
			for($i = 0; $i< count($course_fee_type); $i++){
				$objs = new FeeOptionType;
				$objs->fee_id = $obj->id;
				$objs->fee_type = $requestData['course_fee_type'][$i];
				$objs->inst_amt = $requestData['installment_amount'][$i];
				$objs->installment = $requestData['installment'][$i];
				$objs->total_fee = $requestData['total_fee'][$i];
				$objs->claim_term = $requestData['claimable_terms'][$i];
				$objs->commission = $requestData['commission'][$i];
				$objs->quotation = @$requestData['add_quotation'][$i];
				$saved = $objs->save();
				$response['status'] 	= 	true;
				$response['message']	=	'Fee Option updated successfully';
			}
		}else{
			$response['status'] 	= 	false;
			$response['message']	=	'Record not found';
		}
		
		echo json_encode($response);	
	}
	
	public function deletefee(Request $request){
		$note_id = $request->note_id;
		if(FeeOption::where('id',$note_id)->exists()){

			$res = DB::table('fee_options')->where('id', @$note_id)->delete();
			
			if($res){
				DB::table('fee_option_types')->where('fee_id', @$note_id)->delete();
				$response['status'] 	= 	true;
				$response['data']	=	'Fee removed successfully';
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

	public function import(Request $request){
		$the_file = $request->file('uploaded_file');
		try{
			$spreadsheet = IOFactory::load($the_file->getRealPath());
			$sheet        = $spreadsheet->getActiveSheet();
			$row_limit    = $sheet->getHighestDataRow();
			$column_limit = $sheet->getHighestDataColumn();
			$row_range    = range( 2, $row_limit );
			$column_range = range( 'L', $column_limit );
			$startcount = 2;
			$data = array();
			
			foreach ( $row_range as $row ) {
				$data[] = [
											   'name'=>$sheet->getCell( 'B' . $row )->getValue(),
											   'partner'=>$sheet->getCell( 'C' . $row )->getValue(),
											   'branches'=>$sheet->getCell( 'D' . $row )->getValue(),
											   'product_type'=>$sheet->getCell( 'E' . $row )->getValue(),
											   'revenue_type'=>$sheet->getCell( 'F' . $row )->getValue(),
											   'duration'=>$sheet->getCell( 'G' . $row )->getValue(),
											   'intake_month'=>$sheet->getCell( 'H' . $row )->getValue(),
											   'description'=>$sheet->getCell( 'I' . $row )->getValue(),
											   'note'=>$sheet->getCell( 'J' . $row )->getValue(),
											   'created_at'=>$sheet->getCell( 'K' . $row )->getValue(),
											   'updated_at'=>$sheet->getCell( 'L' . $row )->getValue(),
				];
				$startcount++;
			}
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
			DB::table('check_products')->insert($data);
			DB::connection()->getPdo()->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch (Exception $e) {
			$error_code = $e->errorInfo[1];
			return back()->withErrors('There was a problem uploading the data!');
		} 
		return back()->withSuccess('Great! Data has been successfully uploaded.');
	}
}

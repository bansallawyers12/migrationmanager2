<?php
namespace App\Imports;
use App\Models\PartnerType;
use App\Models\PartnerBranch;
use App\Models\ProductType;
use App\Models\Product;
use App\Models\Partner;
use App\Models\Workflow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class ImportPartner implements ToModel, WithHeadingRow
{
   public function model(array $row)
   {
       if($row['partner_name'] != ''){
           $Category = Category::where('category_name', $row['category'])->first();
           
           if($Category){
                $categoryid = $Category->id;
           }else{
               $cobj = new Category;
               $cobj->category_name = $row['category'];
               $cobj->status = 1;
               $cobj->save();
               
               $categoryid = $cobj->id;
           }
    	  $partnertype = PartnerType::where('name', $row['partner_type'])->first();
    	  
    	  if($partnertype){
                $ptypeid = $partnertype->id;
           }else{
               $ptobj = new PartnerType;
               $ptobj->category_id =$categoryid;
               $ptobj->name = $row['partner_type'];
               $ptobj->save();
               
               $ptypeid = $ptobj->id;
           }
           $workflow = workflow::where('name', $row['service_workflow'])->first();
    	  
    	  if($workflow){
                $wflowid = $workflow->id;
           }else{
               $wtobj = new Workflow;
               $wtobj->name =$row['service_workflow'];
               $wtobj->status =1;
               $wtobj->save();
               
               $wflowid = $wtobj->id;
           }
           
            $Partner = Partner::where('email', $row['test_at_gmailcom'])->first();
    	  
    	  if($Partner){
                $partnerid = $Partner->id;
                $pttobj =  Partner::find($partnerid);
               $pttobj->partner_name =  $row['partner_name'];
               $pttobj->master_category =   $categoryid;
               $pttobj->partner_type =  $ptypeid;
               $pttobj->business_reg_no =   $row['business_registration_number'];
               $pttobj->service_workflow =  $wflowid;
               $pttobj->currency =$row['currency'];
               $pttobj->email =$row['test_at_gmailcom'];
               $pttobj->gender ='';
               $pttobj->phone =$row['phone_number'];
                $pttobj->state =$row['state'];
                $pttobj->country =$row['country'];
                $pttobj->zip =$row['zip_post_code'];
                $pttobj->city =$row['city'];
                $pttobj->address =$row['address'];
                $pttobj->fax =$row['fax'];
                $pttobj->website =$row['website'];
                
               $pttobj->status =1;
               $pttobj->save();
                $partnerbranch = PartnerBranch::where('partner_id', $partnerid)->first();
                $branchid = $partnerbranch->id;
           }else{
               $pttobj = new Partner;
               $pttobj->partner_name =  $row['partner_name'];
               $pttobj->master_category =   $categoryid;
               $pttobj->partner_type =  $ptypeid;
               $pttobj->business_reg_no =   $row['business_registration_number'];
               $pttobj->service_workflow =  $wflowid;
               $pttobj->currency =$row['currency'];
               $pttobj->email =$row['test_at_gmailcom'];
               $pttobj->gender ='';
               $pttobj->phone =$row['phone_number'];
                $pttobj->state =$row['state'];
                $pttobj->country =$row['country'];
                $pttobj->zip =$row['zip_post_code'];
                $pttobj->city =$row['city'];
                $pttobj->address =$row['address'];
                $pttobj->fax =$row['fax'];
                $pttobj->website =$row['website'];
                
               $pttobj->status =1;
               $pttobj->save();
               
               $partnerid = $pttobj->id;
               $partnerbranch = new PartnerBranch;
               $partnerbranch->user_id = 1;
               $partnerbranch->partner_id = $partnerid;
               $partnerbranch->name = 'Head Office';
                $partnerbranch->email = $row['test_at_gmailcom'];
                 $partnerbranch->country = $row['country'];
                  $partnerbranch->city =$row['city'];
                   $partnerbranch->state = $row['state'];
                    $partnerbranch->street = $row['address'];
                     $partnerbranch->country_code = '+61';
                     $partnerbranch->phone = $row['phone_number'];
                     $partnerbranch->is_headoffice = 1;
                     $partnerbranch->zip = $row['zip_post_code'];
                     $partnerbranch->is_regional = 0;
                     $partnerbranch->save();
                     
                     $branchid = $partnerbranch->id;
           }
           
            $ProductType = ProductType::where('name', $row['product_type'])->first();
    	  
    	  if($ProductType){
                $ptypeid = $ProductType->name;
           }else{
               if($row['product_type'] != 'courses'){
                 $wtobj = new ProductType;
                 $wtobj->name =$row['product_type'];
                $wtobj->save();
                 $ptypeid = $row['product_type'];
               }else{
                   $ptypeid = 'Course';
               }
               
               
           }
           
              $productty = new Product;
                $productty->name = $row['product_name'];
                $productty->partner = $partnerid;
                $productty->branches = $branchid;
                $productty->product_type = $ptypeid;
                $productty->revenue_type = $row['revenue_type'];
                $productty->duration = $row['duration'];
                 $productty->intake_month = $row['intake_month'];
                  $productty->description = $row['description'];
                   $productty->note = $row['note'];
             $productty->save();
       }
   }
}
<?php
namespace App\Http\Controllers\EmailUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

use App\Models\Admin;
use App\Models\EmailUser;
use App\Models\EmailRecord;
use App\Models\MailReport;

use Auth;
use Config;
use Illuminate\Support\Facades\Storage;

use App\Models\Matter;
use App\Models\ClientMatter;


class EmailListController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:email_users');
    }

    function uploadToS3($foldername,$mailtype,$filename) {
        $filePath = $foldername.'/'.$mailtype .'/'.basename($filename);
        Storage::disk('s3')->put($filePath, file_get_contents($filename));
    }

    public function loadinbox($email_user_id) {
        $email_user_info = DB::connection('second_db')->table('email_users')->where('id', $email_user_id)->first();
        set_time_limit(0);
        if (! function_exists('imap_open')) {
            echo "IMAP is not configured.";
            exit();
        } else {
            /* Connecting Gmail server with IMAP */
            $connection = imap_open('{imap.zoho.com:993/imap/ssl}INBOX', $email_user_info->email, $email_user_info->decrypt_password) or die('Cannot connect to server: ' .imap_last_error());
            /* Search Emails having the specified keyword in the email subject */
            //$emailData = imap_search($connection, 'ALL');

            // Specify the start and end dates of the period
            $start_date = '22-Aug-2024';
            $end_date = '23-Aug-2024';

            //Search for emails received within the specified period
            $emailData = imap_search($connection, 'SINCE "' . $start_date . '" BEFORE "' . $end_date . '"');

            if (! empty($emailData)) {
                rsort($emailData); // Sort emails by newest first
                foreach ($emailData as $emailIdent) {
                    $overview = imap_fetch_overview($connection, $emailIdent, 0);
                    #echo "<pre>";print_r($overview);die;
                    if( isset($overview[0]->subject) && $overview[0]->subject != ""){
                        $subject = $overview[0]->subject;
                    } else {
                        $subject = "";
                    }

                    if( isset($overview[0]->date) && $overview[0]->date != ""){
                        $date_email = date("Y-m-d H:i:s", strtotime($overview[0]->date));
                    } else {
                        $date_email = "";
                    }

                    if( isset($overview[0]->msgno) && $overview[0]->msgno != ""){
                        $msgno = $overview[0]->msgno;
                    } else {
                        $msgno = "";
                    }

                    $count_record = DB::connection('second_db')->table('email_records')->where('msgno', $msgno)->count();
                    if($count_record < 1) {
                        //Fetch the full email and headers
                        $emailData = imap_fetchbody($connection, $emailIdent, "");
                        $header = imap_fetchheader($connection, $emailIdent);

                        $fullEmail = $header . $emailData; //Combine header and body
                        $filename = $emailIdent.".pdf"; //Save the email in .pdf format
                        file_put_contents($filename, $fullEmail);
                        $foldername = $email_user_info->email;
                        $mailtype = "inbox";
                        $this->uploadToS3($foldername,$mailtype,$filename);
                        if(file_exists($filename)){
                            unlink($filename);
                        }
                        DB::connection('second_db')->table('email_records')->insert([
                            'user_id' => Auth::user()->id,
                            'to_email_id' => $email_user_info->id,
                            'to_email' => $email_user_info->email,
                            'from_email' =>  $overview[0]->from,
                            'subject' => $subject,
                            'date_email' => $date_email,
                            'msgno' => $msgno
                        ]);
                    }
                } // end foreach
            } // end if
            imap_close($connection);
        }
        return view('EmailUser.loadinbox');
    }

    public function loadsent($email_user_id) {
        $email_user_info = DB::connection('second_db')->table('email_users')->where('id', $email_user_id)->first();
        set_time_limit(0);
        if (! function_exists('imap_open')) {
            echo "IMAP is not configured.";
            exit();
        } else {
            /* Connecting Gmail server with IMAP */
            $connection = imap_open('{imap.zoho.com:993/imap/ssl}Sent', $email_user_info->email, $email_user_info->decrypt_password) or die('Cannot connect to server: ' .imap_last_error());
            /* Search Emails having the specified keyword in the email subject */
            //$emailData = imap_search($connection, 'ALL');

            // Specify the start and end dates of the period
            $start_date = '22-Aug-2024';
            $end_date = '23-Aug-2024';

            // Search for emails received within the specified period
            $emailData = imap_search($connection, 'SINCE "' . $start_date . '" BEFORE "' . $end_date . '"');

            if (! empty($emailData)) {
                rsort($emailData); //Sort emails by newest first
                foreach ($emailData as $emailIdent) {
                    $overview = imap_fetch_overview($connection, $emailIdent, 0);
                    if( isset($overview[0]->subject) && $overview[0]->subject != ""){
                        $subject = $overview[0]->subject;
                    } else {
                        $subject = "";
                    }

                    if( isset($overview[0]->date) && $overview[0]->date != ""){
                        $date_email = date("Y-m-d H:i:s", strtotime($overview[0]->date));
                    } else {
                        $date_email = "";
                    }

                    if( isset($overview[0]->msgno) && $overview[0]->msgno != ""){
                        $msgno = $overview[0]->msgno;
                    } else {
                        $msgno = "";
                    }

                    $count_record = DB::connection('second_db')->table('email_records')->where('msgno', $msgno)->count();
                    if($count_record < 1) {
                        //Fetch the full email and headers
                        $emailData = imap_fetchbody($connection, $emailIdent, "");
                        $header = imap_fetchheader($connection, $emailIdent);
                        $fullEmail = $header . $emailData; //Combine header and body
                        $filename = $emailIdent.".pdf"; //Save the email in .pdf format
                        file_put_contents($filename, $fullEmail);
                        $foldername = $email_user_info->email;
                        $mailtype = "sent";
                        $this->uploadToS3($foldername,$mailtype,$filename);
                        if(file_exists($filename)){
                            unlink($filename);
                        }

                        DB::connection('second_db')->table('email_records')->insert([
                            'user_id' => Auth::user()->id,
                            'to_email' =>  $overview[0]->to,
                            'from_email_id' => $email_user_info->id,
                            'from_email' =>$email_user_info->email,
                            'subject' => $subject,
                            'date_email' => $date_email,
                            'msgno' => $msgno
                        ]);
                    }
                } // end foreach
            } // end if
            imap_close($connection);
        }
        return view('EmailUser.loadsent');
    }

    //Listing Of Inbox record
    public function inbox($email_user_id) {
        $email_user_info = DB::connection('second_db')->table('email_users')->where('id', $email_user_id)->first();
        $totalData = DB::connection('second_db')->table('email_records')->where('to_email', $email_user_info->email)->count();
        if($totalData >0 ) {
            $query 	= EmailRecord::on('second_db')->where('to_email', $email_user_info->email);
		    $lists	= $query->sortable(['date_email' => 'desc'])->paginate(20); //dd($lists);
		    $totalData = $totalData;
        } else {
            $query 	= EmailRecord::on('second_db')->where('to_email', $email_user_info->email);
		    $lists	= $query->sortable(['date_email' => 'desc'])->paginate(20);
		    $totalData = 0;
        }
        return view('EmailUser.inbox',compact(['lists', 'totalData']));
    }

    //Listing Of Sent record
    public function sent($email_user_id) {
        $email_user_info = DB::connection('second_db')->table('email_users')->where('id', $email_user_id)->first();
        $totalData = DB::connection('second_db')->table('email_records')->where('from_email',$email_user_info->email)->count();
        if($totalData >0 ) {
            $query 	= EmailRecord::on('second_db')->where('from_email',$email_user_info->email);
		    $lists	= $query->sortable(['date_email' => 'desc'])->paginate(20);
		    $totalData = $totalData;
        } else {
            $query 	= EmailRecord::on('second_db')->where('from_email',$email_user_info->email);
		    $lists	= $query->sortable(['date_email' => 'desc'])->paginate(20);
		    $totalData = 0;
        }
        return view('EmailUser.sent',compact(['lists', 'totalData']));
    }

    //Assign inbox email
    public function assiginboxemail(Request $request) {
		$requestData = $request->all(); //dd($requestData);
		$id = $requestData['memail_id'];
		if( EmailRecord::on('second_db')->where('id', '=', $id)->exists() )
		{
			$record_info = EmailRecord::on('second_db')->where('id', '=', $id)->first();
			if( $record_info->assign_client_id != '' || $record_info->assign_client_matter_id != '')
            {
				if( $record_info->assign_client_matter_id == $requestData['assign_client_matter_id'] ){
					return redirect()->back()->with('error', 'Already assigned to same matter of client');
				} else {
                    //Update email record table with client id and matter id
                    $ld = EmailRecord::find($id);
                    $ld->assign_client_id = $requestData['assign_client_id'];
                    $ld->assign_client_matter_id = $requestData['assign_client_matter_id'];
                    $saved = $ld->save();

                    //Add entry in document table
                    $myfile = $ld['msgno'].'.pdf';
                    $obj = new \App\Models\Document;
                    $obj->file_name = $ld['msgno'];
                    $obj->filetype = "pdf";
                    $obj->user_id = Auth::user()->id;
                    $obj->myfile = $myfile;
                    $obj->client_id = $requestData['assign_client_id'];
                    $obj->type = 'client';
                    $obj->doc_type = 'conversion_email_fetch';
                    $obj->mail_type = $requestData['mail_type'];
                    $obj->client_matter_id = $requestData['assign_client_matter_id'];
                    $saved_doc = $obj->save();

                    if($saved_doc){
                        $lastInsertedDocId = $obj->id;

                        $admin_info = \App\Models\Admin::where('id', '=', $requestData['assign_client_id'])->first();
                        // Define the source and destination paths
                        $sourcePath = $requestData['user_mail'].'/'.$requestData['mail_type'].'/'.$myfile; // Replace with your source file path
                        $destinationPath = $admin_info['client_id'].'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$myfile; // Replace with your destination file path

                        try {
                            // Check if the file exists before copying
                            if (Storage::disk('s3')->exists($sourcePath)) {
                                // Use the copy method to copy the file within S3
                                Storage::disk('s3')->copy($sourcePath, $destinationPath);
                                //echo "File copied successfully.";
                            } else {
                                //echo "Source file does not exist.";
                            }
                        } catch (\Exception $e) {
                            // Handle errors here
                            echo "Error: " . $e->getMessage();
                        }
                        //Save in mail_reports table
                        $obj1 = new \App\Models\MailReport;
                        $obj1->user_id = Auth::user()->id;
                        $obj1->from_mail = $ld['from_email'];
                        $obj1->to_mail = $ld['to_email'];
                        $obj1->subject = $ld['subject'];
                        $obj1->type = 'client';
                        $obj1->mail_type = 1;
                        $obj1->client_id = $requestData['assign_client_id'];
                        $obj1->conversion_type = 'conversion_email_fetch';
                        $obj1->fetch_mail_sent_time = $ld['date_email'];
                        $obj1->uploaded_doc_id = $lastInsertedDocId;
                        $obj1->mail_body_type = $requestData['mail_type'];
                        $obj1->client_matter_id = $requestData['assign_client_matter_id'];
                        $saved_email = $obj1->save();
                        if($saved_email){
                            $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['assign_client_matter_id'])->first();
                            $subject = 'Inbox Email Assign';
                            $objs = new \App\Models\ActivitiesLog;
                            $objs->client_id = $requestData['assign_client_id'];
                            $objs->created_by = Auth::user()->id;
                            $objs->description = $admin_info['client_id'].'-'.$client_matter_info['client_unique_matter_no'];
                            $objs->subject = $subject;
                            $objs->save();

                            //Update date in client matter table
                            if( isset($requestData['assign_client_matter_id']) && $requestData['assign_client_matter_id'] != ""){
                                $obj2 = \App\Models\ClientMatter::find($requestData['assign_client_matter_id']);
                                $obj2->updated_at = date('Y-m-d H:i:s');
                                $obj2->save();
                            }
                        }
                    }
				}
            }
            else
            {
                //Update email record table with client id and matter id
                $ld = EmailRecord::find($id);
				$ld->assign_client_id = $requestData['assign_client_id'];
                $ld->assign_client_matter_id = $requestData['assign_client_matter_id'];
				$saved	= $ld->save();

                //Add entry in document table
                $myfile = $ld['msgno'].'.pdf';
                $obj = new \App\Models\Document;
                $obj->file_name = $ld['msgno'];
                $obj->filetype = "pdf";
                $obj->user_id = Auth::user()->id;
                $obj->myfile = $myfile;
                $obj->client_id = $requestData['assign_client_id'];
                $obj->type = 'client';
                $obj->doc_type = 'conversion_email_fetch';
                $obj->mail_type = $requestData['mail_type'];
                $obj->client_matter_id = $requestData['assign_client_matter_id'];
                $saved_doc = $obj->save();
                if($saved_doc){
                    $lastInsertedDocId = $obj->id;

                    $admin_info = \App\Models\Admin::where('id', '=', $requestData['assign_client_id'])->first();
                    // Define the source and destination paths
                    $sourcePath = $requestData['user_mail'].'/'.$requestData['mail_type'].'/'.$myfile; // Replace with your source file path
	                $destinationPath = $admin_info['client_id'].'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$myfile; // Replace with your destination file path
                    try {
                        // Check if the file exists before copying
                        if (Storage::disk('s3')->exists($sourcePath)) {
                            // Use the copy method to copy the file within S3
                            Storage::disk('s3')->copy($sourcePath, $destinationPath);
                            //echo "File copied successfully.";
                        } else {
                            //echo "Source file does not exist.";
                        }
                    } catch (\Exception $e) {
                        // Handle errors here
                        echo "Error: " . $e->getMessage();
                    }

                    //Save in mail_reports table
                    $obj1 = new \App\Models\MailReport;
                    $obj1->user_id = Auth::user()->id;
                    $obj1->from_mail = $ld['from_email'];
                    $obj1->to_mail = $ld['to_email'];
                    $obj1->subject = $ld['subject'];
                    $obj1->type = 'client';
                    $obj1->mail_type = 1;
                    $obj1->client_id = $requestData['assign_client_id'];
                    $obj1->conversion_type = 'conversion_email_fetch';
                    $obj1->fetch_mail_sent_time = $ld['date_email'];
                    $obj1->uploaded_doc_id = $lastInsertedDocId;
                    $obj1->mail_body_type = $requestData['mail_type'];
                    $obj1->client_matter_id = $requestData['assign_client_matter_id'];
                    $saved_email = $obj1->save();
                    if($saved_email){
                        $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['assign_client_matter_id'])->first();
                        $subject = 'Inbox Email Assign';
                        $objs = new \App\Models\ActivitiesLog;
                        $objs->client_id = $requestData['assign_client_id'];
                        $objs->created_by = Auth::user()->id;
                        $objs->description = $admin_info['client_id'].'-'.$client_matter_info['client_unique_matter_no'];
                        $objs->subject = $subject;
                        $objs->save();

                        //Update date in client matter table
                        if( isset($requestData['assign_client_matter_id']) && $requestData['assign_client_matter_id'] != ""){
                            $obj2 = \App\Models\ClientMatter::find($requestData['assign_client_matter_id']);
                            $obj2->updated_at = date('Y-m-d H:i:s');
                            $obj2->save();
                        }
                    }
                }

                if(!$saved_email) {
                    return redirect()->back()->with('error', 'Please try again');
                } else {
                    return redirect()->back()->with('success', 'Inbox email assigned successfully');
                }
            }
		} else {
			return redirect()->back()->with('error', 'Not Found');
		}
	}
    //Assign sent email
    public function assigsentemail(Request $request) {
		$requestData = $request->all(); //dd($requestData);
		$id = $requestData['memail_id'];
		if( EmailRecord::on('second_db')->where('id', '=', $id)->exists() )
		{
			$record_info = EmailRecord::on('second_db')->where('id', '=', $id)->first();
			if( $record_info->assign_client_id != '' || $record_info->assign_client_matter_id != '')
            {
				if( $record_info->assign_client_matter_id == $requestData['assign_client_matter_id'] ){
					return redirect()->back()->with('error', 'Already assigned to same matter of client');
				} else {
                    //Update email record table with client id and matter id
                    $ld = EmailRecord::find($id); //dd($ld);
                    $ld->assign_client_id = $requestData['assign_client_id'];
                    $ld->assign_client_matter_id = $requestData['assign_client_matter_id'];
                    $saved = $ld->save();

                    //Add entry in document table
                    $myfile = $ld['msgno'].'.pdf';
                    $obj = new \App\Models\Document;
                    $obj->file_name = $ld['msgno'];
                    $obj->filetype = "pdf";
                    $obj->user_id = Auth::user()->id;
                    $obj->myfile = $myfile;
                    $obj->client_id = $requestData['assign_client_id'];
                    $obj->type = 'client';
                    $obj->doc_type = 'conversion_email_fetch';
                    $obj->mail_type = $requestData['mail_type'];
                    $obj->client_matter_id = $requestData['assign_client_matter_id'];
                    $saved_doc = $obj->save();

                    if($saved_doc){
                        $lastInsertedDocId = $obj->id;

                        $admin_info = \App\Models\Admin::where('id', '=', $requestData['assign_client_id'])->first();
                        // Define the source and destination paths
                        $sourcePath = $requestData['user_mail'].'/'.$requestData['mail_type'].'/'.$myfile; // Replace with your source file path
                        $destinationPath = $admin_info['client_id'].'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$myfile; // Replace with your destination file path

                        try {
                            // Check if the file exists before copying
                            if (Storage::disk('s3')->exists($sourcePath)) {
                                // Use the copy method to copy the file within S3
                                Storage::disk('s3')->copy($sourcePath, $destinationPath);
                                //echo "File copied successfully.";
                            } else {
                                //echo "Source file does not exist.";
                            }
                        } catch (\Exception $e) {
                            // Handle errors here
                            echo "Error: " . $e->getMessage();
                        }
                        //Save in mail_reports table
                        $obj1 = new \App\Models\MailReport;
                        $obj1->user_id = Auth::user()->id;
                        $obj1->from_mail = $ld['from_email'];
                        $obj1->to_mail = $ld['to_email'];
                        $obj1->subject = $ld['subject'];
                        $obj1->type = 'client';
                        $obj1->mail_type = 1;
                        $obj1->client_id = $requestData['assign_client_id'];
                        $obj1->conversion_type = 'conversion_email_fetch';
                        $obj1->fetch_mail_sent_time = $ld['date_email'];
                        $obj1->uploaded_doc_id = $lastInsertedDocId;
                        $obj1->mail_body_type = $requestData['mail_type'];
                        $obj1->client_matter_id = $requestData['assign_client_matter_id'];
                        $saved_email = $obj1->save();
                        if($saved_email){
                            $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['assign_client_matter_id'])->first();
                            $subject = 'Sent Email Assign';
                            $objs = new \App\Models\ActivitiesLog;
                            $objs->client_id = $requestData['assign_client_id'];
                            $objs->created_by = Auth::user()->id;
                            $objs->description = $admin_info['client_id'].'-'.$client_matter_info['client_unique_matter_no'];
                            $objs->subject = $subject;
                            $objs->save();

                            //Update date in client matter table
                            if( isset($requestData['assign_client_matter_id']) && $requestData['assign_client_matter_id'] != ""){
                                $obj2 = \App\Models\ClientMatter::find($requestData['assign_client_matter_id']);
                                $obj2->updated_at = date('Y-m-d H:i:s');
                                $obj2->save();
                            }
                        }
                    }
				}
            }
            else
            {
                //Update email record table with client id and matter id
                $ld = EmailRecord::find($id); //dd('@@@'.$ld);
				$ld->assign_client_id = $requestData['assign_client_id'];
                $ld->assign_client_matter_id = $requestData['assign_client_matter_id'];
				$saved	= $ld->save();

                //Add entry in document table
                $myfile = $ld['msgno'].'.pdf';
                $obj = new \App\Models\Document;
                $obj->file_name = $ld['msgno'];
                $obj->filetype = "pdf";
                $obj->user_id = Auth::user()->id;
                $obj->myfile = $myfile;
                $obj->client_id = $requestData['assign_client_id'];
                $obj->type = 'client';
                $obj->doc_type = 'conversion_email_fetch';
                $obj->mail_type = $requestData['mail_type'];
                $obj->client_matter_id = $requestData['assign_client_matter_id'];
                $saved_doc = $obj->save();
                if($saved_doc){
                    $lastInsertedDocId = $obj->id;

                    $admin_info = \App\Models\Admin::where('id', '=', $requestData['assign_client_id'])->first();
                    // Define the source and destination paths
                    $sourcePath = $requestData['user_mail'].'/'.$requestData['mail_type'].'/'.$myfile; // Replace with your source file path
	                $destinationPath = $admin_info['client_id'].'/conversion_email_fetch/'.$requestData['mail_type'].'/'.$myfile; // Replace with your destination file path
                    try {
                        // Check if the file exists before copying
                        if (Storage::disk('s3')->exists($sourcePath)) {
                            // Use the copy method to copy the file within S3
                            Storage::disk('s3')->copy($sourcePath, $destinationPath);
                            //echo "File copied successfully.";
                        } else {
                            //echo "Source file does not exist.";
                        }
                    } catch (\Exception $e) {
                        // Handle errors here
                        echo "Error: " . $e->getMessage();
                    }

                    //Save in mail_reports table
                    $obj1 = new \App\Models\MailReport;
                    $obj1->user_id = Auth::user()->id;
                    $obj1->from_mail = $ld['from_email'];
                    $obj1->to_mail = $ld['to_email'];
                    $obj1->subject = $ld['subject'];
                    $obj1->type = 'client';
                    $obj1->mail_type = 1;
                    $obj1->client_id = $requestData['assign_client_id'];
                    $obj1->conversion_type = 'conversion_email_fetch';
                    $obj1->fetch_mail_sent_time = $ld['date_email'];
                    $obj1->uploaded_doc_id = $lastInsertedDocId;
                    $obj1->mail_body_type = $requestData['mail_type'];
                    $obj1->client_matter_id = $requestData['assign_client_matter_id'];
                    $saved_email = $obj1->save();
                    if($saved_email){
                        $client_matter_info = \App\Models\ClientMatter::select('client_unique_matter_no')->where('id', '=', $requestData['assign_client_matter_id'])->first();
                        $subject = 'Sent Email Assign';
                        $objs = new \App\Models\ActivitiesLog;
                        $objs->client_id = $requestData['assign_client_id'];
                        $objs->created_by = Auth::user()->id;
                        $objs->description = $admin_info['client_id'].'-'.$client_matter_info['client_unique_matter_no'];
                        $objs->subject = $subject;
                        $objs->save();

                        //Update date in client matter table
                        if( isset($requestData['assign_client_matter_id']) && $requestData['assign_client_matter_id'] != ""){
                            $obj2 = \App\Models\ClientMatter::find($requestData['assign_client_matter_id']);
                            $obj2->updated_at = date('Y-m-d H:i:s');
                            $obj2->save();
                        }
                    }
                }

                if(!$saved_email) {
                    return redirect()->back()->with('error', 'Please try again');
                } else {
                    return redirect()->back()->with('success', 'Sent email assigned successfully');
                }
            }
		} else {
			return redirect()->back()->with('error', 'Not Found');
		}
	}


    //Fetch selected client all matters at assign email to user popup
    public function listAllMattersWRTSelClient(Request $request){ //dd($request->all());
        if( ClientMatter::where('client_id', $request->client_id)->exists()){
            //Fetch All client matters
            $clientMatetrs = ClientMatter::join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
            ->select('client_matters.id', 'matters.title','client_matters.client_unique_matter_no')
            ->where('client_id', $request->client_id)
            ->get(); //dd($clientMatetrs);
            if( !empty($clientMatetrs) && count($clientMatetrs)>0 ){
                $response['status'] 	= 	true;
                $response['message']	=	'Client matter is successfully fetched.';
                $response['clientMatetrs']	=	$clientMatetrs;
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['clientMatetrs']	=	array();
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['clientMatetrs']	=	array();
        }
        echo json_encode($response);
	}
}
?>

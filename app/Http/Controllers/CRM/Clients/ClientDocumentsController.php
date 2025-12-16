<?php

namespace App\Http\Controllers\CRM\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Document;
use App\Models\ClientMatter;
use App\Models\VisaDocChecklist;
use App\Models\PersonalDocumentType;
use App\Models\VisaDocumentType;

use App\Traits\ClientAuthorization;
use App\Traits\ClientHelpers;

class ClientDocumentsController extends Controller
{
    use ClientAuthorization, ClientHelpers;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Add Personal/Education Document Checklist
     */
    public function addedudocchecklist(Request $request){
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $clientid = $request->clientid;
            $admin_info1 = Admin::select('client_id')->where('id', $clientid)->first();
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }
            $doctype = isset($request->doctype)? $request->doctype : '';

            if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist');
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->folder_name = $request->folder_name;
                    $obj->checklist = $item;
                    $saved = $obj->save();
                } //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $checklistCount = count($checklistArray);
                        $subject = 'added personal checklist';
                        $description = "Added {$checklistCount} personal document checklist items in '{$request->folder_name}' category: " . implode(', ', array_slice($checklistArray, 0, 3)) . ($checklistCount > 3 ? '...' : '');
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = $description;
                        $objs->subject = $subject;
                        $objs->activity_type = 'document';
                        $objs->save();
                    }

                    $response['status'] = true;
                    $response['message'] = 'You\'ve successfully added your personal checklist';

                    $fetchd = Document::with('user')->where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->where('folder_name',$request->folder_name)->orderby('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = $fetch->user;
                        $fileUrl = $fetch->myfile_key ? $fetch->myfile : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $clientid . '/personal/' . $fetch->myfile;
                        ?>
                        <tr class="drow" id="id_<?php echo $fetch->id; ?>">
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo htmlspecialchars($fetch->checklist); ?>" class="personalchecklist-row" title="Uploaded by: <?php echo htmlspecialchars($admin->first_name ?? 'NA'); ?> on <?php echo date('d/m/Y H:i', strtotime($fetch->created_at)); ?>" style="display: flex; align-items: center; gap: 8px;">
                                    <span style="flex: 1;"><?php echo htmlspecialchars($fetch->checklist); ?></span>
                                    <div class="checklist-actions" style="display: flex; gap: 5px;">
                                        <a href="javascript:;" class="edit-checklist-btn" data-id="<?php echo $fetch->id; ?>" data-checklist="<?php echo htmlspecialchars($fetch->checklist); ?>" title="Edit Checklist Name" style="color: #007bff; cursor: pointer;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (!$fetch->file_name): ?>
                                        <a href="javascript:;" class="delete-checklist-btn" data-id="<?php echo $fetch->id; ?>" data-checklist="<?php echo htmlspecialchars($fetch->checklist); ?>" title="Delete Checklist" style="color: #dc3545; cursor: pointer;">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo htmlspecialchars($fetch->file_name); ?>" class="doc-row" title="Uploaded by: <?php echo htmlspecialchars($admin->first_name ?? 'NA'); ?> on <?php echo date('d/m/Y H:i', strtotime($fetch->created_at)); ?>" oncontextmenu="showFileContextMenu(event, <?php echo $fetch->id; ?>, '<?php echo htmlspecialchars($fetch->filetype); ?>', '<?php echo $fileUrl; ?>', '<?php echo $request->folder_name; ?>', '<?php echo $fetch->status ?? 'draft'; ?>'); return false;">
                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo $fileUrl; ?>','preview-container-<?php echo $request->folder_name;?>')">
                                            <i class="fas fa-file-image"></i> <span><?php echo htmlspecialchars($fetch->file_name . '.' . $fetch->filetype); ?></span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="upload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $clientid;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="personal">
                                            <input type="hidden" name="doccategory" value="<?php echo $request->doccategory;?>">
                                            
                                            <!-- Drag and Drop Zone -->
                                            <div class="document-drag-drop-zone personal-doc-drag-zone" 
                                                 data-fileid="<?php echo $fetch->id; ?>" 
                                                 data-doccategory="<?php echo $request->folder_name; ?>"
                                                 data-formid="upload_form_<?php echo $fetch->id; ?>">
                                                <div class="drag-zone-inner">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="drag-zone-text">Drag file here or <strong>click to browse</strong></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Keep existing file input (hidden, used as fallback) -->
                                            <input class="docupload d-none" data-fileid="<?php echo $fetch->id;?>" data-doccategory="<?php echo $request->folder_name;?>" type="file" name="document_upload" style="display: none;"/>
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <td>
                                <!-- Hidden elements for context menu actions -->
                                <?php if ($fetch->myfile): ?>
                                    <a class="renamechecklist" data-id="<?php echo $fetch->id; ?>" href="javascript:;" style="display: none;"></a>
                                    <a class="renamedoc" data-id="<?php echo $fetch->id; ?>" href="javascript:;" style="display: none;"></a>
                                    <a class="download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>" href="#" style="display: none;"></a>
                                    <a class="notuseddoc" data-id="<?php echo $fetch->id; ?>" data-doctype="personal" data-doccategory="<?php echo $request->doccategory;?>" data-href="documents/not-used" href="javascript:;" style="display: none;"></a>
                                <?php endif; ?>
                            </td>
                        </tr>
			        <?php
			        } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = $fetch->user;
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?php echo $request->folder_name;?>" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data'] = $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        } catch (\Exception $e) {
            Log::error('Error adding personal document checklist', [
                'client_id' => $request->clientid ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
	}
    
    /**
     * Upload Personal/Education Document
     */
    public function uploadedudocument(Request $request) {
        ob_start();
    
        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = $request->doctype ?? '';
    
        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
    
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
    
                    // Get checklist (doc category)
                    $req_file_id = $request->fileid;
                    $obj = Document::find($req_file_id);
                    $checklistName = $obj->checklist;
                    $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;
    
                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
    
                    $req_file_id = $request->fileid;
                    $obj = Document::find($req_file_id);
                    if ($obj) {
                        $obj->file_name = $client_first_name . "_" . $checklistName . "_" . time();
                        $obj->filetype = $extension;
                        $obj->user_id = Auth::user()->id;
                        $fileUrl = Storage::disk('s3')->url($filePath);
                        $obj->myfile = $fileUrl;
                        $obj->myfile_key = $name;
                        $obj->type = $request->type;
                        $obj->file_size = $size;
                        $obj->doc_type = $doctype;
                        $saved = $obj->save();
    
                        if ($saved && $request->type == 'client') {
                            $subject = 'updated personal document';
                            $description = "Uploaded '{$checklistName}' in '{$request->doccategory}' category";
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = $description;
                            $objs->subject = $subject;
                            $objs->activity_type = 'document';
                            $objs->save();
                        }
    
                        if ($saved) {
                            $response['status'] = true;
                            $response['message'] = 'File uploaded successfully';
                            $response['filename'] = $name;
                            $response['filetype'] = $extension;
                            $response['fileurl'] = $fileUrl;
                            $response['filekey'] = $name;
                            $response['doccategory'] = $checklistName;
                        }
                    } else {
                        $response['message'] = 'Document record not found.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Add Visa Document Checklist
     */
    public function addvisadocchecklist(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $clientid = $request->clientid;
            $admin_info1 = Admin::select('client_id')->where('id', $clientid)->first();
            if(!empty($admin_info1)){
                $client_unique_id = $admin_info1->client_id;
            } else {
                $client_unique_id = "";
            }

            $doctype = isset($request->doctype)? $request->doctype : '';
            if ($request->has('visa_checklist'))
        {
            $checklistArray = $request->input('visa_checklist');
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->client_matter_id = $request->client_matter_id;
                    $obj->checklist = $item;
                    $obj->folder_name = $request->folder_name;
                    $saved = $obj->save();
                }  //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $checklistCount = count($checklistArray);
                        $subject = 'added visa checklist';
                        $description = "Added {$checklistCount} visa document checklist items: " . implode(', ', array_slice($checklistArray, 0, 3)) . ($checklistCount > 3 ? '...' : '');
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = $description;
                        $objs->subject = $subject;
                        $objs->activity_type = 'document';
                        $objs->save();
                    }

                    //Update date in client matter table
                    if( isset($request->client_matter_id) && $request->client_matter_id != ""){
                        $obj1 = ClientMatter::find($request->client_matter_id);
                        $obj1->updated_at = date('Y-m-d H:i:s');
                        $obj1->save();
                    }
                    $response['status'] 	= 	true;
                    $response['message']	=	'You have added uploaded your visa checklist';

                    // Get all documents for this client (original behavior - no strict filtering)
                    $fetchd = Document::with('user')->where('client_id',$clientid)
                        ->whereNull('not_used_doc')
                        ->where('doc_type',$doctype)
                        ->where('type',$request->type)
                        ->orderby('updated_at', 'DESC')
                        ->get();
                    
                    ob_start();
                    foreach($fetchd as $visaKey=>$fetch)
                    {
                        $admin = $fetch->user;
                        $VisaDocumentType = VisaDocumentType::where('id', $fetch->folder_name)->first();
                        $fileUrl = $fetch->myfile_key ? $fetch->myfile : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $fetch->client_id . '/visa/' . $fetch->myfile;
                        
                        // Hide non-matching documents with CSS (original behavior)
                        if (
                            $request->client_matter_id != $fetch->client_matter_id ||
                            $request->folder_name != $fetch->folder_name
                        ) {
                            $showCls = "style='display: none;'";
                        } else {
                            $showCls = "";
                        }
                        ?>
                        <tr class="drow" data-matterid="<?php echo $fetch->client_matter_id;?>" data-catid="<?php echo $fetch->folder_name;?>" id="id_<?php echo $fetch->id; ?>" <?php echo $showCls;?>>
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-visachecklistname="<?php echo htmlspecialchars($fetch->checklist); ?>" class="visachecklist-row" title="Uploaded by: <?php echo htmlspecialchars($admin->first_name ?? 'NA'); ?> on <?php echo date('d/m/Y H:i', strtotime($fetch->created_at)); ?>">
                                    <span><?php echo htmlspecialchars($fetch->checklist); ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo htmlspecialchars($fetch->file_name); ?>" class="doc-row" title="Uploaded by: <?php echo htmlspecialchars($admin->first_name ?? 'NA'); ?> on <?php echo date('d/m/Y H:i', strtotime($fetch->created_at)); ?>" oncontextmenu="showVisaFileContextMenu(event, <?php echo $fetch->id; ?>, '<?php echo htmlspecialchars($fetch->filetype); ?>', '<?php echo $fileUrl; ?>', '<?php echo $fetch->folder_name; ?>', '<?php echo $fetch->status ?? 'draft'; ?>'); return false;">
                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo $fetch->myfile; ?>','preview-container-migdocumnetlist')">
                                            <i class="fas fa-file-image"></i> <span><?php echo htmlspecialchars($fetch->file_name . '.' . $fetch->filetype); ?></span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="migration_upload_document" style="display: inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $fetch->client_id;?>">
                                            <input type="hidden" name="client_matter_id" value="<?php echo $fetch->client_matter_id;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="visa">
                                            <input type="hidden" name="doccategory" value="<?php echo $VisaDocumentType->title; ?>">
                                            
                                            <!-- Drag and Drop Zone -->
                                            <div class="document-drag-drop-zone visa-doc-drag-zone" 
                                                 data-fileid="<?php echo $fetch->id;?>" 
                                                 data-doccategory="<?php echo $fetch->folder_name;?>"
                                                 data-formid="mig_upload_form_<?php echo $fetch->id;?>">
                                                <div class="drag-zone-inner">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="drag-zone-text">Drag file here or <strong>click to browse</strong></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Keep existing file input (hidden) -->
                                            <input class="migdocupload d-none" 
                                                   data-fileid="<?php echo $fetch->id;?>" 
                                                   data-doccategory="<?php echo $fetch->folder_name;?>" 
                                                   type="file" 
                                                   name="document_upload" 
                                                   style="display: none;"/>
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <td>
                                <!-- Hidden elements for context menu actions -->
                                <?php if ($fetch->myfile): ?>
                                    <a class="renamechecklist" data-id="<?php echo $fetch->id; ?>" href="javascript:;" style="display: none;"></a>
                                    <a class="renamedoc" data-id="<?php echo $fetch->id; ?>" href="javascript:;" style="display: none;"></a>
                                    <a class="download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>" href="#" style="display: none;"></a>
                                    <a class="notuseddoc" data-id="<?php echo $fetch->id; ?>" data-doctype="visa" data-href="documents/not-used" href="javascript:;" style="display: none;"></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = $fetch->user;
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                           

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data']	= $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        } catch (\Exception $e) {
            Log::error('Error adding visa document checklist', [
                'client_id' => $request->clientid ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
    }

    /**
     * Upload Visa Document
     */
    public function uploadvisadocument(Request $request) {
        ob_start();
        
        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = isset($request->doctype)? $request->doctype : '';
        
        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
                
                // Allow only letters, numbers, underscores, dashes, spaces, dots, and dollar signs
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();

                    // Get checklist (doc category)
                    $req_file_id = $request->fileid;
                    $obj = Document::find($req_file_id);
                    $checklistName = $obj->checklist;
                    // Build new file name: firstname_checklist_timestamp.ext
                    $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;

                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));

                    $obj->file_name = $client_first_name . "_" . $checklistName . "_" . time();
                    $obj->filetype = $extension;
                    $obj->user_id = Auth::user()->id;
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    $obj->myfile = $fileUrl;
                    $obj->myfile_key = $name;
                    $obj->type = $request->type;
                    $obj->file_size = $size;
                    $obj->doc_type = $doctype;
                    $saved = $obj->save();
                    
                    if($saved){
                        if($request->type == 'client'){
                            $subject = 'updated visa document';
                            $description = "Uploaded '{$checklistName}' visa document";
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = $description;
                            $objs->subject = $subject;
                            $objs->activity_type = 'document';
                            $objs->save();
                        }

                        //Update date in client matter table
                        if( isset($request->client_matter_id) && $request->client_matter_id != ""){
                            $obj1 = ClientMatter::find($request->client_matter_id);
                            $obj1->updated_at = date('Y-m-d H:i:s');
                            $obj1->save();
                        }
                        
                        $response['status'] = true;
                        $response['message'] = 'You have successfully uploaded your visa document';
                        $response['filename'] = $name;
                        $response['filetype'] = $extension;
                        $response['fileurl'] = $fileUrl;
                        $response['filekey'] = $name;
                        $response['doccategory'] = $checklistName;
                        $response['doctype'] = $doctype;
                        $response['visa_doc_cat'] = $request->visa_doc_cat;
                        $response['status_value'] = $obj->status ?? 'draft'; // Add status for JavaScript
                    } else {
                        $response['message'] = 'Failed to save document record.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
        
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Rename Document
     */
    public function renamedoc(Request $request) {
        $id = $request->id;
        $filename = $request->filename; // new file name without extension

        if(\App\Models\Document::where('id',$id)->exists()){
            $doc = \App\Models\Document::where('id',$id)->first();

            // Get old S3 key and extension
            $oldKey = $doc->myfile_key;
            $extension = $doc->filetype;
            $client_id = $doc->client_id;
            $doc_type = $doc->doc_type;

            // Get client unique id for S3 path
            $admin = \App\Models\Admin::select('client_id')->where('id', $client_id)->first();
            $client_unique_id = $admin ? $admin->client_id : "";

            // Build new key and S3 path
            $newKey = time() . $filename . '.' . $extension;
            $newS3Path = $client_unique_id . '/' . $doc_type . '/' . $newKey;
            $oldS3Path = $client_unique_id . '/' . $doc_type . '/' . $oldKey;

            // Copy and delete in S3
            if (\Storage::disk('s3')->exists($oldS3Path)) {
                try {
                    // Attempt to copy first
                    $copySuccess = \Storage::disk('s3')->copy($oldS3Path, $newS3Path);
                    
                    if ($copySuccess) {
                        // Only delete original if copy was successful
                        \Storage::disk('s3')->delete($oldS3Path);
                    } else {
                        // Copy failed, don't proceed with database update
                        $response['status'] = false;
                        $response['message'] = 'Failed to copy file. Please try again.';
                        echo json_encode($response);
                        return;
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging
                    \Log::error('S3 copy failed: ' . $e->getMessage(), [
                        'oldPath' => $oldS3Path,
                        'newPath' => $newS3Path,
                        'document_id' => $id
                    ]);
                    
                    $response['status'] = false;
                    $response['message'] = 'File operation failed. Please try again.';
                    echo json_encode($response);
                    return;
                }
            } else {
                // Original file not found - handle gracefully
                \Log::warning('Document rename failed: Original file not found', [
                    'document_id' => $id,
                    'old_s3_path' => $oldS3Path,
                    'new_filename' => $filename,
                    'user_id' => Auth::user()->id ?? 'unknown'
                ]);
                
                // Return error response
                $response['status'] = false;
                $response['message'] = 'Original document not found. Please re-upload the document.';
                $response['error_type'] = 'file_not_found';
                echo json_encode($response);
                return;
            }

            // Build new S3 URL
            $newS3Url = \Storage::disk('s3')->url($newS3Path);

            // Update DB
            $res = \DB::table('documents')->where('id', $id)->update([
                'file_name' => $filename,
                'myfile' => $newS3Url,
                'myfile_key' => $newKey
            ]);

            if($res){
                // Log activity for document rename
                $oldName = $doc->file_name;
                $subject = 'renamed a document';
                $description = "Renamed {$doc_type} document from '{$oldName}' to '{$filename}'";
                
                $objs = new ActivitiesLog;
                $objs->client_id = $client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = $description;
                $objs->subject = $subject;
                $objs->activity_type = 'document';
                $objs->save();
                
                $response['status'] = true;
                $response['data'] = 'Document saved successfully';
                $response['Id'] = $id;
                $response['filename'] = $filename;
                $response['filetype'] = $doc->filetype;
                $response['fileurl'] = $newS3Url;

                if($doc->doc_type == 'personal') {
                    $response['folder_name'] = 'preview-container-'.$doc->folder_name;
                } else if($doc->doc_type == 'visa') {
                    $response['folder_name'] = 'preview-container-migdocumnetlist';
                }
            } else {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        }
        echo json_encode($response);
    }

    /**
     * Delete Document
     */
    public function deletedocs(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        $data = null;
        
        try {
            $note_id = $request->note_id;
            if(\App\Models\Document::where('id',$note_id)->exists()){
            $data = DB::table('documents')->where('id', @$note_id)->first();
            $admin = DB::table('admins')->select('client_id')->where('id', @$data->client_id)->first();
            $res = DB::table('documents')->where('id', @$note_id)->delete();
            //Storage::disk('s3')->delete('documents/' . $data->myfile);
            if($data->doc_type == 'migration') {
                Storage::disk('s3')->delete($admin->client_id.'/'.$data->doc_type.'/'.$data->myfile_key);
            } else {
                Storage::disk('s3')->delete($admin->client_id.'/'.$data->doc_type.'/'.$data->myfile_key);
            }
            if($res){
                $documentName = $data->file_name ?? 'unknown';
                $documentType = $data->doc_type ?? 'document';
                $subject = 'deleted a document';
                $description = "Deleted {$documentType} document: {$documentName}";

                $objs = new ActivitiesLog;
                $objs->client_id = $data->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = $description;
                $objs->subject = $subject;
                $objs->activity_type = 'document';
                $objs->save();
                $response['status'] 	= 	true;
                $response['data']	=	'Document removed successfully';
                if(isset($data->doc_type) && $data->doc_type == 'personal'){
                    $response['doc_categry']	= $data->folder_name;
                } else {
                    $response['doc_categry']	= "";
                }
            }else{
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                if(isset($data->doc_type) && $data->doc_type == 'personal'){
                    $response['doc_categry']	= $data->folder_name;
                } else {
                    $response['doc_categry']	= "";
                }
            }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            if(isset($data->doc_type) && $data->doc_type == 'personal'){
                $response['doc_categry']	= $data->folder_name;
            } else {
                $response['doc_categry']	= "";
            }
        }
        } catch (\Exception $e) {
            Log::error('Error deleting document', [
                'document_id' => $request->note_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
    }


    /**
     * Get Visa Checklist
     */
    public function getvisachecklist(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again', 'visaCheckListInfo' => []];
        
        try {
            if( ClientMatter::where('id', $request->client_matter_id)->exists()){
                $clientMatterInfo = ClientMatter::select('sel_matter_id')->where('id',$request->client_matter_id)->first();
                //dd($clientMatterInfo->sel_matter_id);
                if( isset($clientMatterInfo) ){
                    $visaCheckListInfo = VisaDocChecklist::select('id','name')->whereRaw("FIND_IN_SET(?, matter_id)", [$clientMatterInfo->sel_matter_id])->get();
                    //dd($visaCheckListInfo);
                    if( !empty($visaCheckListInfo) && count($visaCheckListInfo)>0 ){
                        $response['status'] 	= 	true;
                        $response['message']	=	'Visa checklist is successfully fetched.';
                        $response['visaCheckListInfo']	=	$visaCheckListInfo;
                    } else {
                        $response['status'] 	= 	false;
                        $response['message']	=	'Please try again';
                        $response['visaCheckListInfo'] = array();
                    }
                } else {
                    $response['status'] 	= 	false;
                    $response['message']	=	'Please try again';
                    $response['visaCheckListInfo']	=	array();
                }
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['visaCheckListInfo']	=	array();
            }
        } catch (\Exception $e) {
            Log::error('Error getting visa checklist', [
                'client_matter_id' => $request->client_matter_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
            $response['visaCheckListInfo'] = [];
        }
        echo json_encode($response);
    }

    /**
     * Mark Document as Not Used
     */
    public function notuseddoc(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $doc_id = $request->doc_id;
            $doc_type = $request->doc_type;
            if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Models\Document::with(['user', 'verifiedBy'])->where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
                $objs->client_id = $docInfo->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = $subject;
                $objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" && $docInfo->user ){
                        $response['Added_By'] = $docInfo->user->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }

                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" && $docInfo->verifiedBy ){
                        $response['Verified_By'] = $docInfo->verifiedBy->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }
                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;

                if(isset($docInfo->doc_type) && $docInfo->doc_type == 'personal'){
                    $response['doc_category'] = $docInfo->folder_name;
                } else {
                    $response['doc_category'] = "";
                }
                $response['status'] = true;
                $response['data'] = $doc_type.' document moved to Not Used Tab';
            } else {
                $response['status'] = false;
                $response['message'] = 'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";
                $response['doc_category'] = "";
                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";
            $response['doc_category'] = "";
            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
        }
        } catch (\Exception $e) {
            Log::error('Error marking document as not used', [
                'doc_id' => $request->doc_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
    }

    /**
     * Rename Checklist in Document
     */
    public function renamechecklistdoc(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $id = $request->id;
            $checklist = $request->checklist;
            if(\App\Models\Document::where('id',$id)->exists()){
            $doc = \App\Models\Document::where('id',$id)->first();
            $res = DB::table('documents')->where('id', @$id)->update(['checklist' => $checklist]);
            if($res){
                $response['status'] = true;
                $response['data'] = 'Checklist saved successfully';
                $response['Id'] = $id;
                $response['checklist'] = $checklist;
            }else{
                $response['status'] = false;
                $response['message'] = 'Please try again';
            }
        }else{
            $response['status'] = false;
            $response['message'] = 'Please try again';
        }
        } catch (\Exception $e) {
            Log::error('Error renaming checklist', [
                'document_id' => $request->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
    }

    /**
     * Move Document Back from Not Used
     */
    public function backtodoc(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $doc_id = $request->doc_id;
            $doc_type = $request->doc_type;
            if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => null));
            if($upd){
                $docInfo = \App\Models\Document::with(['user', 'verifiedBy'])->where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to '.$doc_type.' document tab';
                $objs = new ActivitiesLog;
                $objs->client_id = $docInfo->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = $subject;
                $objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" && $docInfo->user ){
                        $response['Added_By'] = $docInfo->user->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }

                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" && $docInfo->verifiedBy ){
                        $response['Verified_By'] = $docInfo->verifiedBy->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }
                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;
                $response['status'] = 	true;
                $response['data']	=	$doc_type.' document moved to '.$doc_type.' document tab';
            } else {
                $response['status'] 	= 	false;
                $response['message']	=	'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";

                $response['Added_By'] = "";
                $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
        }
        } else {
            $response['status'] 	= 	false;
            $response['message']	=	'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";

            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
        }
        } catch (\Exception $e) {
            Log::error('Error moving document back from not used', [
                'doc_id' => $request->doc_id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        echo json_encode($response);
    }

    /**
     * Delete Checklist Item (only if no file uploaded)
     */
    public function deleteChecklist(Request $request) {
        $response = ['status' => false, 'message' => 'Please try again'];
        
        try {
            $checklist_id = $request->id;
            
            if (!$checklist_id) {
                $response['message'] = 'Checklist ID is required';
                return response()->json($response);
            }
            
            $document = Document::find($checklist_id);
            
            if (!$document) {
                $response['message'] = 'Checklist not found';
                return response()->json($response);
            }
            
            // Only allow deletion if no file has been uploaded
            if ($document->file_name || $document->myfile) {
                $response['message'] = 'Cannot delete checklist with uploaded file. Please remove the file first.';
                return response()->json($response);
            }
            
            $checklistName = $document->checklist;
            $clientId = $document->client_id;
            $folderName = $document->folder_name;
            
            // Delete the document record
            $deleted = $document->delete();
            
            if ($deleted) {
                // Log activity
                $subject = 'deleted personal checklist';
                $description = "Deleted personal document checklist: '{$checklistName}'";
                
                $objs = new ActivitiesLog;
                $objs->client_id = $clientId;
                $objs->created_by = Auth::user()->id;
                $objs->description = $description;
                $objs->subject = $subject;
                $objs->activity_type = 'document';
                $objs->save();
                
                $response['status'] = true;
                $response['message'] = 'Checklist deleted successfully';
                $response['folder_name'] = $folderName;
            } else {
                $response['message'] = 'Failed to delete checklist';
            }
        } catch (\Exception $e) {
            Log::error('Error deleting checklist', [
                'checklist_id' => $request->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response['status'] = false;
            $response['message'] = 'An error occurred. Please try again.';
        }
        
        return response()->json($response);
    }

    /**
     * Download Document (S3 Temporary URL)
     */
    public function download_document(Request $request) {
        $fileUrl = $request->input('filelink');
        $filename = $request->input('filename', 'downloaded.pdf');

        if (!$fileUrl) {
            return abort(400, 'Missing file URL');
        }

        try {
            // Extract S3 key from the URL
            $parsed = parse_url($fileUrl);
            if (!isset($parsed['path'])) {
                return abort(400, 'Invalid S3 URL format');
            }
            
            $s3Key = ltrim(urldecode($parsed['path']), '/');
            
            // Check if file exists in S3
            if (!Storage::disk('s3')->exists($s3Key)) {
                return abort(404, 'File not found in S3');
            }
            
            // Generate temporary URL with proper headers
            $tempUrl = Storage::disk('s3')->temporaryUrl(
                $s3Key,
                now()->addMinutes(5), // 5 minutes expiration
                [
                    'ResponseContentDisposition' => 'attachment; filename="' . $filename . '"',
                    'ResponseContentType' => 'application/pdf'
                ]
            );
            
            // Redirect to S3 temporary URL
            return redirect($tempUrl);
            
        } catch (\Exception $e) {
            \Log::error('S3 download error: ' . $e->getMessage());
            return abort(500, 'Error generating download link');
        }
    }

    /**
     * Add Personal Document Category
     */
    public function addPersonalDocCategory(Request $request) {
        $categoryTitle = trim($request->input('personal_doc_category'));
        $clientId = $request->input('clientid');

        $request->merge(['personal_doc_category' => $categoryTitle]);

        // Basic validation
        $validator = \Validator::make($request->all(), [
            'personal_doc_category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('personal_doc_category')
            ]);
        }

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally (for NULL client).'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed
        $existsForSameClient = PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_id', $clientId)
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client.'
            ]);
        }

        try {
            $category = new PersonalDocumentType();
            $category->title = $categoryTitle;
            $category->status = 1;
            $category->client_id = $clientId ?? null;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Personal Document Category added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding category: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update Personal Document Category
     */
    public function updatePersonalDocCategory(Request $request) {
        $request->validate([
            'id' => 'required|exists:personal_document_types,id',
            'title' => 'required|string|max:255',
        ]);

        $category = PersonalDocumentType::findOrFail($request->id);
        $clientId = $category->client_id; // Get the client_id of the category being updated

        // Check if the category is client-generated
        if ($category->client_id === null) {
            return response()->json(['success' => false, 'message' => 'Only client-generated categories can be updated.']);
        }

        $categoryTitle = trim($request->input('title'));

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally for all client.Pls try other.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed (excluding the current category)
        $existsForSameClient = PersonalDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_id', $clientId)
            ->where('id', '!=', $category->id) // Exclude the current category
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client.Pls try other.'
            ]);
        }

        try {
            $category->title = $categoryTitle;
            $category->save();

            return response()->json(['status' => true,'message' => 'This category is updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating category: ' . $e->getMessage()]);
        }
    }

    /**
     * Add Visa Document Category
     */
    public function addVisaDocCategory(Request $request) {
        $categoryTitle = trim($request->input('visa_doc_category'));
        $clientId = $request->input('clientid');
        $clientMatterId = $request->input('clientmatterid');

        $request->merge(['visa_doc_category' => $categoryTitle]);

        // Basic validation
        $validator = \Validator::make($request->all(), [
            'visa_doc_category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first('visa_doc_category')
            ]);
        }

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_matter_id')
            ->whereNull('client_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed
        $existsForSameClient = VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_matter_id', $clientMatterId)
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client matter.'
            ]);
        }

        try {
            $category = new VisaDocumentType();
            $category->title = $categoryTitle;
            $category->status = 1;
            $category->client_id = $clientId ?? null;
            $category->client_matter_id = $clientMatterId ?? null;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Visa Document Category added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding category: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update Visa Document Category  
     */
    public function updateVisaDocCategory(Request $request) {
        $request->validate([
            'id' => 'required|exists:visa_document_types,id',
            'title' => 'required|string|max:255',
        ]);

        $category = VisaDocumentType::findOrFail($request->id);
        $clientMatterId = $category->client_matter_id; // Get the client_id of the category being updated

        // Check if the category is client-generated
        if ($category->client_matter_id === null) {
            return response()->json(['success' => false, 'message' => 'Only client-matter-generated categories can be updated.']);
        }

        $categoryTitle = trim($request->input('title'));

        // RULE 1: If status=1 and client_id is NULL, title must be unique globally (only one)
        $existsForNullClient = VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->whereNull('client_matter_id')
            ->exists();

        if ($existsForNullClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists globally for all client matters.Pls try other.'
            ]);
        }

        // RULE 2: Same title with status=1 for same client_id is not allowed (excluding the current category)
        $existsForSameClient = VisaDocumentType::where('title', $categoryTitle)
            ->where('status', 1)
            ->where('client_matter_id', $clientMatterId)
            ->where('id', '!=', $request->id)
            ->exists();

        if ($existsForSameClient) {
            return response()->json([
                'status' => false,
                'message' => 'This category already exists for this client matter.'
            ]);
        }

        try {
            $category->title = $categoryTitle;
            $category->save();

            return response()->json([
                'status' => true,
                'message' => 'Visa Document Category updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ]);
        }
    }
}

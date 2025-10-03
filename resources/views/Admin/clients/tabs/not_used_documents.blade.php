           <!-- Not Used Documents Tab (Shared - Client Level) -->
           <div class="tab-pane" id="notuseddocuments-tab">
                <div class="card full-width documentalls-container">
                    <div style="display: flex; gap: 20px; padding: 15px;">
                        <!-- Table Container -->
                        <div style="flex: 1; min-width: 0;">
                            <div class="subtab-header" style="margin-bottom: 15px;">
                                <h3><i class="fas fa-folder"></i> Not Used Documents</h3>
                                <p class="text-muted">Documents marked as "Not Used" from both Personal and Visa document tabs are shown here.</p>
                            </div>
                            <div style="overflow: auto; max-height: calc(100vh - 250px);">
                                <table class="checklist-table" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>SNo.</th>
                                            <th>Checklist</th>
                                            <th>Document Type</th>
                                            <th>Added By</th>
                                            <th>File Name</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="tdata notuseddocumnetlist">
                                        <?php
                                        $fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
                                        ->where('not_used_doc', 1)
                                        ->where('type','client')
                                        ->where(function($query) {
                                            $query->orWhere('doc_type','personal')
                                            ->orWhere('doc_type','visa');
                                        })->orderBy('type', 'DESC')->get();
                                        foreach($fetchd as $notuseKey=>$fetch)
                                        {
                                            $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                            ?>
                                            <tr class="drow" id="id_{{$fetch->id}}">
                                                <td><?php echo $notuseKey+1;?></td>
                                                <td style="white-space: initial;"><?php echo $fetch->checklist; ?></td>
                                                <td style="white-space: initial;">
                                                    <span class="badge badge-<?php echo $fetch->doc_type === 'personal' ? 'primary' : 'success'; ?>">
                                                        <?php echo ucfirst($fetch->doc_type); ?>
                                                    </span>
                                                </td>
                                                <td style="white-space: initial;">
                                                    <?php
                                                    echo ($admin->first_name ?? 'NA') . "<br>";
                                                    echo date('d/m/Y', strtotime($fetch->created_at));
                                                    ?>
                                                </td>
                                                <td style="white-space: initial;">
                                                    <?php
                                                    if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                        <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                            <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumnetlist')">
                                                                    <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                </a>
                                                            <?php } else {  //For old file upload
                                                                $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                ?>
                                                                <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumnetlist')">
                                                                    <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        echo "N/A";
                                                    }?>
                                                </td>
                                                <td>
                                                    <div class="dropdown d-inline">
                                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                                        <div class="dropdown-menu">
                                                            <?php
                                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                            ?>
                                                            <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                            <?php } else {  //For old file upload ?>
                                                                <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                            <?php } ?>

                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item deletenote" data-doccategory="<?= $fetch->doc_type ?>" data-href="deletedocs" href="javascript:;">Delete</a>
                                                            <a data-id="{{$fetch->id}}" class="dropdown-item backtodoc" data-doctype="{{$fetch->doc_type}}" data-href="backtodoc" href="javascript:;">Back To Document</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php
                                        } //end foreach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Preview Container -->
                        <div class="preview-pane file-preview-container preview-container-notuseddocumnetlist" style="display: inline;margin-top: 15px !important;width: 499px;">
                            <h3>File Preview</h3>
                            <p>Click on a file to preview it here.</p>
                        </div>
                    </div>
                </div>
            </div>


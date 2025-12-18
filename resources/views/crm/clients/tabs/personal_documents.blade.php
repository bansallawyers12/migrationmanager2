           <!-- Personal Documents Tab (Client-Level) -->
           <div class="tab-pane" id="personaldocuments-tab">
                <div class="card full-width documentalls-container">
                    <?php
                    $clientId = $fetchedData->id ?? null;
                    $persDocCatList = \App\Models\PersonalDocumentType::select('id', 'title','client_id')
                        ->where('status', 1)
                        ->where(function($query) use ($clientId) {
                            $query->whereNull('client_id')
                                ->orWhere('client_id', $clientId);
                        })
                        ->orderBy('id', 'ASC')
                        ->get();
                    ?>

                    <!-- Personal Documents Content -->
                    <div class="personal-documents-content" id="personal-documents-content">
                        <!-- Document Type Subtabs Container -->
                        <div class="subtab-header-container" style="background-color: #4a90e2; padding: 10px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <nav class="subtabs2" style="display: flex; gap: 5px; flex-wrap: wrap; flex: 1;">
                                <?php foreach ($persDocCatList as $catVal): ?>
                                    <?php
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $isClientGenerated = $catVal->client_id !== null;
                                    ?>
                                    <div style="display: inline-block; position: relative;" class="button-container">
                                        <button class="subtab2-button <?= $isActive ?>" data-subtab2="<?= $id ?>">
                                            <?= htmlspecialchars($catVal->title) ?>
                                        </button>
                                        <?php if ($isClientGenerated): ?>
                                            <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                <button class="btn btn-sm btn-warning update-personal-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </nav>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button type="button" class="btn add_personal_doc_cat-btn add_personal_doc_cat" data-type="personal" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Category
                                </button>
                                <!-- Add link to Not Used Documents -->
                                <button class="btn btn-secondary client-nav-button" data-tab="notuseddocuments">
                                    <i class="fas fa-folder-minus"></i> Not Used Documents
                                </button>
                            </div>
                        </div>

                        <!-- Subtab2 Contents -->
                        <div class="subtab2-content">
                            <?php foreach ($persDocCatList as $catVal): ?>
                                <?php
                                $id = $catVal->id;
                                $isActive = $id == 1 ? 'active' : '';
                                $folderName = $id;
                                ?>

                                <div class="subtab2-pane <?= $isActive ?>" id="<?= $id ?>-subtab2">
                                    <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                        <div class="subtab2-header" style="margin-left: 10px;">
                                            <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                            <div style="display: flex; gap: 10px;">
                                                <button type="button" class="btn add-checklist-btn add_education_doc" data-type="personal" data-categoryid="<?= $id ?>">
                                                    <i class="fas fa-plus"></i> Add Checklist
                                                </button>
                                                <button type="button" class="btn btn-info bulk-upload-toggle-btn" data-categoryid="<?= $id ?>" data-categoryname="<?= htmlspecialchars($catVal->title) ?>">
                                                    <i class="fas fa-upload"></i> Bulk Upload
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Bulk Upload Dropzone (Hidden by default) -->
                                        <div class="bulk-upload-dropzone-container" id="bulk-upload-<?= $id ?>" style="display: none; margin: 15px 0; padding: 20px; border: 2px dashed #4a90e2; border-radius: 8px; background-color: #f8f9fa;">
                                            <div class="bulk-upload-dropzone" data-categoryid="<?= $id ?>" style="text-align: center; padding: 30px; cursor: pointer;">
                                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #4a90e2; margin-bottom: 15px;"></i>
                                                <p style="font-size: 16px; color: #666; margin-bottom: 10px;">
                                                    <strong>Drag and drop files here</strong> or <strong>click to browse</strong>
                                                </p>
                                                <p style="font-size: 14px; color: #999;">You can select multiple files at once</p>
                                                <input type="file" class="bulk-upload-file-input" data-categoryid="<?= $id ?>" multiple style="display: none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                            </div>
                                            <div class="bulk-upload-file-list" style="display: none; margin-top: 20px;">
                                                <h5 style="margin-bottom: 15px;">Files Selected: <span class="file-count">0</span></h5>
                                                <div class="bulk-upload-files-container"></div>
                                            </div>
                                        </div>
                                        <table class="checklist-table">
                                            <thead>
                                                <tr>
                                                    <th>Checklist</th>
                                                    <th>File Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tdata persdocumnetlist documnetlist_<?= $id ?>">
                                                <?php
                                                $documents = \App\Models\Document::with('user')->where('client_id', $clientId)
                                                    ->whereNull('not_used_doc')
                                                    ->where('doc_type', 'personal')
                                                    ->where('folder_name', $folderName)
                                                    ->where('type', 'client')
                                                    ->orderBy('created_at', 'DESC')
                                                    ->get();
                                                ?>
                                                <?php foreach ($documents as $docKey => $fetch): ?>
                                                    <?php
                                                    $admin = $fetch->user;
                                                    
                                                    // Ensure $fileUrl is always a valid full URL
                                                    if (!empty($fetch->myfile) && strpos($fetch->myfile, 'http') === 0) {
                                                        // Already a full URL
                                                        $fileUrl = $fetch->myfile;
                                                    } else {
                                                        // Legacy format or relative path - construct full URL
                                                        $fileUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $clientId . '/personal/' . $fetch->myfile;
                                                    }
                                                    ?>
                                                    <tr class="drow" id="id_<?= $fetch->id ?>">
                                                        <td style="white-space: initial;">
                                                            <div data-id="<?= $fetch->id ?>" data-personalchecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="personalchecklist-row" title="Uploaded by: <?= htmlspecialchars($admin->first_name ?? 'NA') ?> on <?= date('d/m/Y H:i', strtotime($fetch->created_at)) ?>" style="display: flex; align-items: center; gap: 8px;">
                                                                <span style="flex: 1;"><?= htmlspecialchars($fetch->checklist) ?></span>
                                                                <div class="checklist-actions" style="display: flex; gap: 5px;">
                                                                    <?php if (!$fetch->file_name): ?>
                                                                    <a href="javascript:;" class="edit-checklist-btn" data-id="<?= $fetch->id ?>" data-checklist="<?= htmlspecialchars($fetch->checklist) ?>" title="Edit Checklist Name" style="color: #007bff; cursor: pointer;">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="javascript:;" class="delete-checklist-btn" data-id="<?= $fetch->id ?>" data-checklist="<?= htmlspecialchars($fetch->checklist) ?>" title="Delete Checklist" style="color: #dc3545; cursor: pointer;">
                                                                        <i class="fas fa-trash"></i>
                                                                    </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?php if ($fetch->file_name): ?>
                                                                <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row" title="Uploaded by: <?= htmlspecialchars($admin->first_name ?? 'NA') ?> on <?= date('d/m/Y H:i', strtotime($fetch->created_at)) ?>" oncontextmenu="showFileContextMenu(event, <?= $fetch->id ?>, '<?= htmlspecialchars($fetch->filetype) ?>', '<?= $fileUrl ?>', '<?= $id ?>', '<?= $fetch->status ?? 'draft' ?>'); return false;">
                                                                    <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= $fileUrl ?>','preview-container-<?= $id ?>')">
                                                                        <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                    </a>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="upload_document" style="display: inline-block;">
                                                                    <form method="POST" enctype="multipart/form-data" id="upload_form_<?= $fetch->id ?>">
                                                                        @csrf
                                                                        <input type="hidden" name="clientid" value="<?= $clientId ?>">
                                                                        <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                        <input type="hidden" name="type" value="client">
                                                                        <input type="hidden" name="doctype" value="personal">
                                                                        <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">

                                                                        <!-- Drag and Drop Zone -->
                                                                        <div class="document-drag-drop-zone personal-doc-drag-zone" 
                                                                             data-fileid="<?= $fetch->id ?>" 
                                                                             data-doccategory="<?= $id ?>"
                                                                             data-formid="upload_form_<?= $fetch->id ?>">
                                                                            <div class="drag-zone-inner">
                                                                                <i class="fas fa-cloud-upload-alt"></i>
                                                                                <span class="drag-zone-text">Drag file here or <strong>click to browse</strong></span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- Keep existing file input (hidden, used as fallback) -->
                                                                        <input class="docupload d-none" 
                                                                               data-fileid="<?= $fetch->id ?>" 
                                                                               data-doccategory="<?= $id ?>" 
                                                                               type="file" 
                                                                               name="document_upload" 
                                                                               style="display: none;"/>
                                                                    </form>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <!-- Hidden elements for context menu actions -->
                                                            <?php if ($fetch->myfile): ?>
                                                                <a class="renamechecklist" data-id="<?= $fetch->id ?>" href="javascript:;" style="display: none;"></a>
                                                                <a class="renamedoc" data-id="<?= $fetch->id ?>" href="javascript:;" style="display: none;"></a>
                                                                <a class="download-file" data-filelink="<?= $fileUrl ?>" data-filename="<?= $fetch->myfile_key ?: basename($fetch->myfile) ?>" data-id="<?= $fetch->id ?>" href="#" style="display: none;"></a>
                                                                <a class="notuseddoc" data-id="<?= $fetch->id ?>" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="documents/not-used" href="javascript:;" style="display: none;"></a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="grid_data griddata_<?= $id ?>">
                                        <?php foreach ($documents as $fetch): ?>
                                            <?php if ($fetch->myfile): ?>
                                                <div class="grid_list" id="gid_<?= $fetch->id ?>">
                                                    <div class="grid_col">
                                                        <div class="grid_icon">
                                                            <i class="fas fa-file-image"></i>
                                                        </div>
                                                        <div class="grid_content">
                                                            <span id="grid_<?= $fetch->id ?>" class="gridfilename"><?= htmlspecialchars($fetch->file_name) ?></span>
                                                            <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                <div class="dropdown-menu">
                                                                    <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                    <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                    <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="preview-pane file-preview-container preview-container-<?= $id ?>" style="display: inline; margin-top: 15px !important; width: 499px;">
                                        <p>Click on a file to preview it here.</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Context Menu -->
            <div id="fileContextMenu" class="context-menu" style="display: none; position: absolute; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000; min-width: 180px;">
                <div class="context-menu-item" onclick="handleContextAction('rename-checklist')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-edit" style="margin-right: 8px;"></i> Rename Checklist
                </div>
                <div class="context-menu-item" onclick="handleContextAction('rename-doc')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-file-text" style="margin-right: 8px;"></i> Rename File Name
                </div>
                <div class="context-menu-item" onclick="handleContextAction('preview')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-eye" style="margin-right: 8px;"></i> Preview
                </div>
                <div id="context-pdf-option" class="context-menu-item" onclick="handleContextAction('pdf')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-file-pdf" style="margin-right: 8px;"></i> PDF
                </div>
                <div class="context-menu-item" onclick="handleContextAction('download')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-download" style="margin-right: 8px;"></i> Download
                </div>
                <div class="context-menu-item" onclick="handleContextAction('not-used')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-trash" style="margin-right: 8px;"></i> Not Used
                </div>
            </div>

            <script>
                // ============================================================================
                // PERSONAL DOCUMENTS - DRAG AND DROP INITIALIZATION
                // ============================================================================
                console.log('🚀 Personal Documents Tab Script Loading...');
                
                function initPersonalDocDragDrop() {
                    console.log('🔄 Initializing Personal Doc Drag & Drop...');
                    console.log('📊 Drop zones found:', $('.personal-doc-drag-zone').length);
                    console.log('📊 Visible drop zones:', $('.personal-doc-drag-zone:visible').length);
                    
                    // Check each drop zone
                    $('.personal-doc-drag-zone').each(function(index) {
                        var $zone = $(this);
                        var fileid = $zone.data('fileid');
                        var formid = $zone.data('formid');
                        var isVisible = $zone.is(':visible');
                        console.log('🔍 Drop zone #' + index + ':', {
                            fileid: fileid,
                            formid: formid,
                            visible: isVisible,
                            hasFileInput: $('#' + formid).find('.docupload').length > 0
                        });
                    });
                    
                    // IMPORTANT: Remove ALL handlers (including those from detail-main.js)
                    // This ensures our local handlers take priority
                    $('.personal-doc-drag-zone').off('click');
                    $('.personal-doc-drag-zone').off('dragenter');
                    $('.personal-doc-drag-zone').off('dragover');
                    $('.personal-doc-drag-zone').off('dragleave');
                    $('.personal-doc-drag-zone').off('drop');
                    
                    // Also remove delegated event handlers
                    $(document).off('click', '.personal-doc-drag-zone');
                    $(document).off('dragenter', '.personal-doc-drag-zone');
                    $(document).off('dragover', '.personal-doc-drag-zone');
                    $(document).off('dragleave', '.personal-doc-drag-zone');
                    $(document).off('drop', '.personal-doc-drag-zone');
                    
                    // Add local click handler as backup (in case detail-main.js handler doesn't fire)
                    $(document).off('click', '.personal-doc-drag-zone').on('click', '.personal-doc-drag-zone', function(e) {
                        console.log('🎯 LOCAL CLICK HANDLER - personal-doc-drag-zone clicked');
                        e.preventDefault();
                        e.stopPropagation();
                        
                        var fileid = $(this).data('fileid');
                        var formid = $(this).data('formid');
                        console.log('📂 File ID:', fileid, 'Form ID:', formid);
                        
                        var fileInput = $('#' + formid).find('.docupload');
                        console.log('📁 File input found:', fileInput.length > 0);
                        
                        if (fileInput.length > 0) {
                            console.log('✅ Triggering file input click...');
                            fileInput[0].click(); // Use native click
                        } else {
                            console.error('❌ File input not found for fileid:', fileid);
                        }
                        
                        return false;
                    });
                    
                    // Add drag and drop handlers with better event handling
                    // IMPORTANT: Remove all existing handlers first to prevent conflicts
                    $(document).off('dragenter', '.personal-doc-drag-zone');
                    $(document).off('dragover', '.personal-doc-drag-zone');
                    $(document).off('dragleave', '.personal-doc-drag-zone');
                    $(document).off('drop', '.personal-doc-drag-zone');
                    
                    // Prevent default drag behavior on document
                    $(document).off('dragover.personaldoc').on('dragover.personaldoc', function(e) {
                        if ($(e.target).closest('.personal-doc-drag-zone').length > 0) {
                            return; // Let drop zone handle it
                        }
                        e.preventDefault(); // Prevent browser from opening file
                    });
                    
                    $(document).off('drop.personaldoc').on('drop.personaldoc', function(e) {
                        if ($(e.target).closest('.personal-doc-drag-zone').length > 0) {
                            return; // Let drop zone handle it
                        }
                        e.preventDefault(); // Prevent browser from opening file
                    });
                    
                    // Attach handlers DIRECTLY to each drop zone element (not delegated)
                    // This ensures they fire BEFORE detail-main.js handlers
                    $('.personal-doc-drag-zone').each(function() {
                        var $zone = $(this);
                        
                        // Click handler
                        $zone.on('click', function(e) {
                            console.log('🎯 DIRECT CLICK HANDLER - personal-doc-drag-zone clicked');
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation(); // Stop other handlers
                            
                            var fileid = $(this).data('fileid');
                            var formid = $(this).data('formid');
                            console.log('📂 File ID:', fileid, 'Form ID:', formid);
                            
                            var fileInput = $('#' + formid).find('.docupload');
                            console.log('📁 File input found:', fileInput.length > 0);
                            
                            if (fileInput.length > 0) {
                                console.log('✅ Triggering file input click...');
                                fileInput[0].click();
                            } else {
                                console.error('❌ File input not found for fileid:', fileid);
                            }
                            
                            return false;
                        });
                        
                        // Dragenter handler
                        $zone.on('dragenter', function(e) {
                            console.log('🔥 DIRECT DRAGENTER HANDLER');
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            $(this).addClass('drag_over');
                            return false;
                        });
                        
                        // Dragover handler
                        $zone.on('dragover', function(e) {
                            console.log('🔥 DIRECT DRAGOVER HANDLER');
                            var event = e.originalEvent || e;
                            event.preventDefault();
                            event.stopPropagation();
                            
                            if (event.dataTransfer) {
                                event.dataTransfer.dropEffect = 'copy';
                            }
                            
                            $(this).addClass('drag_over');
                            return false;
                        });
                        
                        // Dragleave handler
                        $zone.on('dragleave', function(e) {
                            console.log('⚠️ DIRECT DRAGLEAVE HANDLER');
                            e.preventDefault();
                            e.stopPropagation();
                            
                            var rect = this.getBoundingClientRect();
                            var x = e.originalEvent.clientX;
                            var y = e.originalEvent.clientY;
                            
                            if (x <= rect.left || x >= rect.right || y <= rect.top || y >= rect.bottom) {
                                $(this).removeClass('drag_over');
                            }
                            return false;
                        });
                        
                        // Drop handler
                        $zone.on('drop', function(e) {
                            console.log('🎯 DIRECT DROP HANDLER');
                            var event = e.originalEvent || e;
                            event.preventDefault();
                            event.stopPropagation();
                            event.stopImmediatePropagation();
                            
                            $(this).removeClass('drag_over');
                            
                            var files = event.dataTransfer ? event.dataTransfer.files : null;
                            if (files && files.length > 0) {
                                console.log('📄 File dropped:', files[0].name);
                                
                                var fileid = $(this).data('fileid');
                                var formid = $(this).data('formid');
                                var fileInput = $('#' + formid).find('.docupload')[0];
                                
                                if (fileInput) {
                                    try {
                                        var dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(files[0]);
                                        fileInput.files = dataTransfer.files;
                                        console.log('✅ File assigned using DataTransfer');
                                    } catch(err) {
                                        console.warn('⚠️ Fallback to direct assignment');
                                        try {
                                            fileInput.files = files;
                                        } catch(err2) {
                                            console.error('❌ Could not assign file:', err2);
                                        }
                                    }
                                    
                                    $(fileInput).trigger('change');
                                    console.log('✅ Change event triggered');
                                } else {
                                    console.error('❌ File input not found');
                                }
                            }
                            return false;
                        });
                    });
                    
                    // BACKUP: Also keep delegated handlers as fallback
                    // Drag enter - initial entry into zone
                    $(document).on('dragenter.pdbackup', '.personal-doc-drag-zone', function(e) {
                        console.log('🔥 LOCAL DRAGENTER HANDLER');
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).addClass('drag_over');
                        return false;
                    });
                    
                    // Drag over - continuous while dragging over zone
                    $(document).on('dragover.pdbackup', '.personal-doc-drag-zone', function(e) {
                        console.log('🔥 LOCAL DRAGOVER HANDLER');
                        var event = e.originalEvent || e;
                        event.preventDefault();
                        event.stopPropagation();
                        
                        // Set dropEffect to indicate this is a valid drop zone
                        if (event.dataTransfer) {
                            event.dataTransfer.dropEffect = 'copy';
                        }
                        
                        $(this).addClass('drag_over');
                        return false;
                    });
                    
                    // Drag leave - when dragging out of zone
                    $(document).on('dragleave.pdbackup', '.personal-doc-drag-zone', function(e) {
                        console.log('⚠️ LOCAL DRAGLEAVE HANDLER');
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Only remove highlight if actually leaving the zone (not just moving between child elements)
                        var $zone = $(this);
                        var rect = this.getBoundingClientRect();
                        var x = e.originalEvent.clientX;
                        var y = e.originalEvent.clientY;
                        
                        if (x <= rect.left || x >= rect.right || y <= rect.top || y >= rect.bottom) {
                            $zone.removeClass('drag_over');
                        }
                        return false;
                    });
                    
                    // Drop - when file is dropped
                    $(document).on('drop.pdbackup', '.personal-doc-drag-zone', function(e) {
                        console.log('🎯 LOCAL DROP HANDLER');
                        var event = e.originalEvent || e;
                        event.preventDefault();
                        event.stopPropagation();
                        
                        $(this).removeClass('drag_over');
                        
                        var files = event.dataTransfer ? event.dataTransfer.files : null;
                        if (files && files.length > 0) {
                            console.log('📄 File dropped:', files[0].name);
                            console.log('📄 File type:', files[0].type);
                            console.log('📄 File size:', files[0].size);
                            
                            var fileid = $(this).data('fileid');
                            var formid = $(this).data('formid');
                            console.log('📂 File ID:', fileid, 'Form ID:', formid);
                            
                            var fileInput = $('#' + formid).find('.docupload')[0];
                            console.log('📁 File input found:', !!fileInput);
                            
                            if (fileInput) {
                                try {
                                    // Try modern approach first
                                    var dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(files[0]);
                                    fileInput.files = dataTransfer.files;
                                    console.log('✅ File assigned to input using DataTransfer');
                                } catch(err) {
                                    console.warn('⚠️ DataTransfer not supported, trying fallback...');
                                    // Fallback: directly assign files (works in most browsers)
                                    try {
                                        fileInput.files = files;
                                        console.log('✅ File assigned directly');
                                    } catch(err2) {
                                        console.error('❌ Could not assign file:', err2);
                                    }
                                }
                                
                                // Trigger change event
                                console.log('🚀 Triggering change event on file input...');
                                $(fileInput).trigger('change');
                                console.log('✅ Change event triggered');
                            } else {
                                console.error('❌ File input not found for formid:', formid);
                            }
                        } else {
                            console.error('❌ No files in drop event');
                        }
                        return false;
                    });
                    
                    console.log('✅ Local drag-drop handlers attached');
                }
                
                // Initialize on DOM ready
                $(document).ready(function() {
                    console.log('✅ Personal Documents DOM Ready');
                    initPersonalDocDragDrop();
                });
                
                // Re-initialize when Personal Documents tab is shown
                // Listen for tab clicks using the sidebar-tabs.js system
                $(document).on('click', '.client-nav-button[data-tab="personaldocuments"]', function() {
                    console.log('📂 Personal Documents tab clicked, reinitializing...');
                    setTimeout(function() {
                        initPersonalDocDragDrop();
                    }, 200); // Increased delay to ensure tab is visible
                });
                
                // Also check if tab is already active (e.g., direct URL navigation)
                if ($('#personaldocuments-tab').hasClass('active')) {
                    console.log('📂 Personal Documents tab already active on load');
                    setTimeout(function() {
                        initPersonalDocDragDrop();
                    }, 500);
                }
                
                let currentContextFile = null;
                let currentContextData = {};

                function showFileContextMenu(event, fileId, fileType, fileUrl, categoryId, fileStatus) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    currentContextFile = fileId;
                    currentContextData = {
                        fileId: fileId,
                        fileType: fileType,
                        fileUrl: fileUrl,
                        categoryId: categoryId,
                        fileStatus: fileStatus
                    };

                    const menu = document.getElementById('fileContextMenu');
                    
                    // Show/hide PDF option based on file type
                    const pdfOption = document.getElementById('context-pdf-option');
                    const fileExt = fileType.toLowerCase();
                    if (['jpg', 'png', 'jpeg'].includes(fileExt)) {
                        pdfOption.style.display = 'block';
                    } else {
                        pdfOption.style.display = 'none';
                    }

                    // Measure actual menu dimensions dynamically
                    // Temporarily show menu to measure it (off-screen)
                    menu.style.visibility = 'hidden';
                    menu.style.display = 'block';
                    const menuWidth = menu.offsetWidth;
                    const menuHeight = menu.offsetHeight;
                    menu.style.display = 'none';
                    menu.style.visibility = 'visible';

                    // Configuration
                    const MIN_PADDING = 5; // Minimum distance from viewport edges (reduced for closer positioning)
                    const CURSOR_OFFSET = 2; // Small offset from cursor position (reduced for closer feel)
                    
                    // Get scroll position
                    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    // Get viewport dimensions
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    // Get cursor position relative to viewport
                    const cursorX = event.clientX;
                    const cursorY = event.clientY;
                    
                    // Start with cursor position (preferred: right and below)
                    let menuLeft = cursorX + scrollLeft + CURSOR_OFFSET;
                    let menuTop = cursorY + scrollTop + CURSOR_OFFSET;
                    
                    // Check if menu would overflow right edge - adjust to left of cursor if needed
                    if (menuLeft + menuWidth > scrollLeft + viewportWidth - MIN_PADDING) {
                        // Try positioning to the left of cursor
                        const leftPosition = cursorX + scrollLeft - menuWidth - CURSOR_OFFSET;
                        // Only use left position if it keeps menu visible
                        if (leftPosition >= scrollLeft + MIN_PADDING) {
                            menuLeft = leftPosition;
                        } else {
                            // Not enough space on left either - push to right edge with padding
                            menuLeft = scrollLeft + viewportWidth - menuWidth - MIN_PADDING;
                        }
                    }
                    
                    // Ensure menu doesn't go off left edge (minimal adjustment)
                    if (menuLeft < scrollLeft + MIN_PADDING) {
                        menuLeft = scrollLeft + MIN_PADDING;
                    }
                    
                    // Check if menu would overflow bottom edge - adjust above cursor if needed
                    if (menuTop + menuHeight > scrollTop + viewportHeight - MIN_PADDING) {
                        // Try positioning above cursor
                        const topPosition = cursorY + scrollTop - menuHeight - CURSOR_OFFSET;
                        // Only use top position if it keeps menu visible
                        if (topPosition >= scrollTop + MIN_PADDING) {
                            menuTop = topPosition;
                        } else {
                            // Not enough space above either - push to bottom edge with padding
                            menuTop = scrollTop + viewportHeight - menuHeight - MIN_PADDING;
                        }
                    }
                    
                    // Ensure menu doesn't go off top edge (minimal adjustment)
                    if (menuTop < scrollTop + MIN_PADDING) {
                        menuTop = scrollTop + MIN_PADDING;
                    }
                    
                    // Final safety check: ensure menu stays fully within viewport bounds
                    // These checks ensure the menu is always fully visible
                    if (menuLeft + menuWidth > scrollLeft + viewportWidth - MIN_PADDING) {
                        menuLeft = scrollLeft + viewportWidth - menuWidth - MIN_PADDING;
                    }
                    if (menuTop + menuHeight > scrollTop + viewportHeight - MIN_PADDING) {
                        menuTop = scrollTop + viewportHeight - menuHeight - MIN_PADDING;
                    }
                    
                    // Apply position
                    menu.style.left = menuLeft + 'px';
                    menu.style.top = menuTop + 'px';
                    menu.style.display = 'block';

                    // Hide menu when clicking elsewhere
                    setTimeout(() => {
                        document.addEventListener('click', hideContextMenu);
                    }, 100);
                }

                function hideContextMenu() {
                    const menu = document.getElementById('fileContextMenu');
                    menu.style.display = 'none';
                    document.removeEventListener('click', hideContextMenu);
                }

                function handleContextAction(action) {
                    if (!currentContextFile) return;

                    hideContextMenu();

                    switch(action) {
                        case 'rename-checklist':
                            $('.renamechecklist[data-id="' + currentContextFile + '"]').click();
                            break;
                        case 'rename-doc':
                            $('.renamedoc[data-id="' + currentContextFile + '"]').click();
                            break;
                        case 'preview':
                            window.open(currentContextData.fileUrl, '_blank');
                            break;
                        case 'pdf':
                            const pdfUrl = '{{ URL::to('/document/download/pdf') }}/' + currentContextFile;
                            window.open(pdfUrl, '_blank');
                            break;
                        case 'download':
                            // Try to find download button by filelink
                            let $downloadBtn = $('.download-file[data-filelink="' + currentContextData.fileUrl + '"]');
                            if ($downloadBtn.length === 0) {
                                // Fallback: try finding by document ID
                                $downloadBtn = $('.download-file[data-id="' + currentContextFile + '"]');
                            }
                            if ($downloadBtn.length > 0) {
                                $downloadBtn.click();
                            } else {
                                console.error('Download button not found for file ID:', currentContextFile);
                                alert('Download link not found. Please refresh the page and try again.');
                            }
                            break;
                        case 'not-used':
                            $('.notuseddoc[data-id="' + currentContextFile + '"]').click();
                            break;
                    }
                }

                // Hide context menu on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        hideContextMenu();
                    }
                });
            </script>

            <style>
                .context-menu-item:hover {
                    background-color: #f8f9fa;
                }

                /* Drag and Drop Zone Styles */
                .document-drag-drop-zone {
                    border: 2px dashed #ccc;
                    border-radius: 4px;
                    padding: 15px 20px;
                    text-align: center;
                    background-color: #f9f9f9;
                    cursor: pointer !important;
                    transition: all 0.3s ease;
                    min-height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 5px 0;
                    position: relative;
                    z-index: 1;
                }

                .document-drag-drop-zone:hover {
                    border-color: #007bff;
                    background-color: #f0f8ff;
                }

                .document-drag-drop-zone.drag_over {
                    border-color: #28a745;
                    background-color: #e8f5e9;
                    border-width: 3px;
                }

                .drag-zone-inner {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #666;
                }

                .drag-zone-inner i {
                    font-size: 20px;
                    color: #007bff;
                }

                .drag-zone-text {
                    font-size: 14px;
                }

                .document-drag-drop-zone.uploading {
                    pointer-events: none;
                    opacity: 0.6;
                }

                .document-drag-drop-zone.uploading .drag-zone-text::after {
                    content: ' Uploading...';
                    font-weight: bold;
                    color: #007bff;
                }

                /* Bulk Upload Styles */
                .bulk-upload-dropzone.drag_over {
                    border-color: #28a745;
                    background-color: #e8f5e9;
                }

                .bulk-upload-file-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 10px;
                    margin-bottom: 10px;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }

                .bulk-upload-file-item .file-info {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .bulk-upload-file-item .file-name {
                    font-weight: 500;
                    color: #333;
                }

                .bulk-upload-file-item .file-size {
                    font-size: 12px;
                    color: #999;
                }

                .bulk-upload-file-item .checklist-select {
                    min-width: 200px;
                }

                .bulk-upload-file-item .match-status {
                    font-size: 12px;
                    padding: 2px 8px;
                    border-radius: 3px;
                }

                .match-status.auto-matched {
                    background-color: #d4edda;
                    color: #155724;
                }

                .match-status.manual {
                    background-color: #fff3cd;
                    color: #856404;
                }

                .match-status.new-checklist {
                    background-color: #cce5ff;
                    color: #004085;
                }

                .bulk-upload-mapping-modal {
                    display: none;
                    position: fixed;
                    z-index: 10000;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                }

                .bulk-upload-mapping-content {
                    background-color: #fefefe;
                    margin: 5% auto;
                    padding: 20px;
                    border: 1px solid #888;
                    border-radius: 8px;
                    width: 90%;
                    max-width: 900px;
                    max-height: 80vh;
                    overflow-y: auto;
                }

                .bulk-upload-mapping-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 2px solid #eee;
                }

                .bulk-upload-mapping-header h3 {
                    margin: 0;
                    color: #333;
                }

                .close-mapping-modal {
                    color: #aaa;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }

                .close-mapping-modal:hover {
                    color: #000;
                }

                .bulk-upload-actions {
                    margin-top: 20px;
                    padding-top: 15px;
                    border-top: 2px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .bulk-upload-progress {
                    display: none;
                    margin-top: 15px;
                }

                .progress-bar-container {
                    width: 100%;
                    height: 25px;
                    background-color: #f0f0f0;
                    border-radius: 4px;
                    overflow: hidden;
                }

                .progress-bar {
                    height: 100%;
                    background-color: #4a90e2;
                    width: 0%;
                    transition: width 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 12px;
                }
            </style>

            <!-- Bulk Upload Mapping Modal -->
            <div id="bulk-upload-mapping-modal" class="bulk-upload-mapping-modal">
                <div class="bulk-upload-mapping-content">
                    <div class="bulk-upload-mapping-header">
                        <h3><i class="fas fa-link"></i> Map Files to Checklists</h3>
                        <span class="close-mapping-modal">&times;</span>
                    </div>
                    <div id="bulk-upload-mapping-table"></div>
                    <div class="bulk-upload-actions">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" id="auto-create-unmatched" checked>
                            <span>Auto-create checklist for unmatched files</span>
                        </label>
                        <div>
                            <button type="button" class="btn btn-secondary" id="cancel-bulk-upload">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirm-bulk-upload">Upload All</button>
                        </div>
                    </div>
                    <div class="bulk-upload-progress" id="bulk-upload-progress">
                        <p>Uploading files...</p>
                        <div class="progress-bar-container">
                            <div class="progress-bar" id="bulk-upload-progress-bar">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // ============================================================================
                // BULK UPLOAD FUNCTIONALITY
                // ============================================================================
                
                let bulkUploadFiles = {};
                let currentCategoryId = null;
                let currentClientId = <?= $clientId ?>;
                
                // Toggle bulk upload dropzone
                $(document).on('click', '.bulk-upload-toggle-btn', function() {
                    const categoryId = $(this).data('categoryid');
                    const dropzoneContainer = $('#bulk-upload-' + categoryId);
                    
                    // Hide all other dropzones first
                    $('.bulk-upload-dropzone-container').not('#bulk-upload-' + categoryId).slideUp();
                    $('.bulk-upload-toggle-btn').not(this).html('<i class="fas fa-upload"></i> Bulk Upload');
                    
                    if (dropzoneContainer.is(':visible')) {
                        dropzoneContainer.slideUp();
                        $(this).html('<i class="fas fa-upload"></i> Bulk Upload');
                        // Clear files if closing
                        bulkUploadFiles[categoryId] = [];
                        dropzoneContainer.find('.bulk-upload-file-list').hide();
                        dropzoneContainer.find('.bulk-upload-files-container').empty();
                        dropzoneContainer.find('.file-count').text('0');
                    } else {
                        dropzoneContainer.slideDown();
                        $(this).html('<i class="fas fa-times"></i> Close');
                        currentCategoryId = categoryId;
                    }
                });
                
                // Initialize bulk upload files array for each category
                $('.bulk-upload-dropzone').each(function() {
                    const categoryId = $(this).data('categoryid');
                    if (!bulkUploadFiles[categoryId]) {
                        bulkUploadFiles[categoryId] = [];
                    }
                });
                
                // Click to browse files
                $(document).on('click', '.bulk-upload-dropzone', function(e) {
                    if (!$(e.target).is('input')) {
                        const categoryId = $(this).data('categoryid');
                        $(this).closest('.bulk-upload-dropzone-container').find('.bulk-upload-file-input[data-categoryid="' + categoryId + '"]').click();
                    }
                });
                
                // File input change
                $(document).on('change', '.bulk-upload-file-input', function() {
                    const categoryId = $(this).data('categoryid');
                    const files = this.files;
                    
                    if (files.length > 0) {
                        handleBulkFilesSelected(categoryId, files);
                    }
                });
                
                // Drag and drop handlers
                $(document).on('dragover', '.bulk-upload-dropzone', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('drag_over');
                });
                
                $(document).on('dragleave', '.bulk-upload-dropzone', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('drag_over');
                });
                
                $(document).on('drop', '.bulk-upload-dropzone', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('drag_over');
                    
                    const categoryId = $(this).data('categoryid');
                    const files = e.originalEvent.dataTransfer.files;
                    
                    if (files.length > 0) {
                        handleBulkFilesSelected(categoryId, files);
                    }
                });
                
                // Handle files selected
                function handleBulkFilesSelected(categoryId, files) {
                    if (!bulkUploadFiles[categoryId]) {
                        bulkUploadFiles[categoryId] = [];
                    }
                    
                    // Validate and add files to array
                    const invalidFiles = [];
                    const maxSize = 50 * 1024 * 1024; // 50MB
                    const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                    
                    Array.from(files).forEach(file => {
                        // Check file size
                        if (file.size > maxSize) {
                            invalidFiles.push(file.name + ' (exceeds 50MB)');
                            return;
                        }
                        
                        // Check file extension
                        const ext = file.name.split('.').pop().toLowerCase();
                        if (!allowedExtensions.includes(ext)) {
                            invalidFiles.push(file.name + ' (invalid file type)');
                            return;
                        }
                        
                        // Check if file already exists
                        const exists = bulkUploadFiles[categoryId].some(f => f.name === file.name && f.size === file.size);
                        if (!exists) {
                            bulkUploadFiles[categoryId].push(file);
                        }
                    });
                    
                    if (invalidFiles.length > 0) {
                        alert('The following files were skipped:\n' + invalidFiles.join('\n'));
                    }
                    
                    if (bulkUploadFiles[categoryId].length === 0) {
                        alert('No valid files selected. Please select PDF, JPG, PNG, DOC, or DOCX files under 50MB.');
                        return;
                    }
                    
                    // Show file list
                    const container = $('#bulk-upload-' + categoryId);
                    container.find('.bulk-upload-file-list').show();
                    container.find('.file-count').text(bulkUploadFiles[categoryId].length);
                    
                    // Show mapping interface
                    showBulkUploadMapping(categoryId);
                }
                
                // Show mapping interface
                function showBulkUploadMapping(categoryId) {
                    currentCategoryId = categoryId;
                    const files = bulkUploadFiles[categoryId];
                    
                    if (files.length === 0) {
                        return;
                    }
                    
                    // Get existing checklists for this category
                    getExistingChecklists(categoryId, function(checklists) {
                        // Call backend to get auto-matches
                        getAutoChecklistMatches(categoryId, files, checklists, function(matches) {
                            displayMappingInterface(files, checklists, matches);
                        });
                    });
                }
                
                // Get existing checklists
                function getExistingChecklists(categoryId, callback) {
                    const checklists = [];
                    const checklistNames = new Set();
                    
                    $('.documnetlist_' + categoryId + ' .personalchecklist-row').each(function() {
                        const checklistName = $(this).data('personalchecklistname');
                        const checklistId = $(this).closest('tr').attr('id').replace('id_', '');
                        
                        if (checklistName && !checklistNames.has(checklistName)) {
                            checklistNames.add(checklistName);
                            checklists.push({
                                id: checklistId,
                                name: checklistName
                            });
                        }
                    });
                    
                    callback(checklists);
                }
                
                // Get auto-checklist matches from backend
                function getAutoChecklistMatches(categoryId, files, checklists, callback) {
                    const fileData = Array.from(files).map(file => ({
                        name: file.name,
                        size: file.size,
                        type: file.type
                    }));
                    
                    const checklistNames = checklists.map(c => c.name);
                    
                    $.ajax({
                        url: '{{ route("clients.documents.getAutoChecklistMatches") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            clientid: currentClientId,
                            categoryid: categoryId,
                            files: fileData,
                            checklists: checklistNames
                        },
                        success: function(response) {
                            if (response.status) {
                                callback(response.matches || {});
                            } else {
                                callback({});
                            }
                        },
                        error: function() {
                            callback({});
                        }
                    });
                }
                
                // Display mapping interface
                function displayMappingInterface(files, checklists, matches) {
                    const modal = $('#bulk-upload-mapping-modal');
                    const tableContainer = $('#bulk-upload-mapping-table');
                    
                    let html = '<table class="table table-bordered" style="width: 100%;">';
                    html += '<thead><tr><th style="width: 30%;">File Name</th><th style="width: 50%;">Checklist Assignment</th><th style="width: 20%;">Status</th></tr></thead>';
                    html += '<tbody>';
                    
                    Array.from(files).forEach((file, index) => {
                        const fileName = file.name;
                        const fileSize = formatFileSize(file.size);
                        const match = matches[fileName] || null;
                        
                        let selectedChecklist = '';
                        let statusClass = 'manual';
                        let statusText = 'Manual selection';
                        
                        if (match && match.checklist) {
                            selectedChecklist = match.checklist;
                            statusClass = match.confidence === 'high' ? 'auto-matched' : 'manual';
                            statusText = match.confidence === 'high' ? 'Auto-matched' : 'Suggested';
                        }
                        
                        html += '<tr class="bulk-upload-file-item">';
                        html += '<td>';
                        html += '<div class="file-info">';
                        html += '<i class="fas fa-file" style="color: #4a90e2;"></i>';
                        html += '<div>';
                        html += '<div class="file-name">' + escapeHtml(fileName) + '</div>';
                        html += '<div class="file-size">' + fileSize + '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</td>';
                        html += '<td>';
                        html += '<select class="form-control checklist-select" data-file-index="' + index + '" data-file-name="' + escapeHtml(fileName) + '">';
                        html += '<option value="">-- Select Checklist --</option>';
                        html += '<option value="__NEW__">+ Create New Checklist</option>';
                        checklists.forEach(checklist => {
                            const selected = selectedChecklist === checklist.name ? 'selected' : '';
                            html += '<option value="' + escapeHtml(checklist.name) + '" ' + selected + '>' + escapeHtml(checklist.name) + '</option>';
                        });
                        html += '</select>';
                        html += '<input type="text" class="form-control mt-2 new-checklist-input" data-file-index="' + index + '" placeholder="Enter new checklist name" style="display: none;">';
                        html += '</td>';
                        html += '<td>';
                        html += '<span class="match-status ' + statusClass + '">' + statusText + '</span>';
                        html += '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table>';
                    tableContainer.html(html);
                    
                    // Handle new checklist option
                    $(document).off('change', '.checklist-select').on('change', '.checklist-select', function() {
                        const fileIndex = $(this).data('file-index');
                        const value = $(this).val();
                        const newInput = $('.new-checklist-input[data-file-index="' + fileIndex + '"]');
                        
                        if (value === '__NEW__') {
                            newInput.show();
                            newInput.attr('required', true);
                            $(this).closest('tr').find('.match-status').removeClass('auto-matched manual').addClass('new-checklist').text('New checklist');
                        } else {
                            newInput.hide();
                            newInput.removeAttr('required');
                            if (value) {
                                $(this).closest('tr').find('.match-status').removeClass('new-checklist').addClass('manual').text('Manual selection');
                            }
                        }
                    });
                    
                    modal.show();
                }
                
                // Close mapping modal
                $(document).off('click', '.close-mapping-modal, #cancel-bulk-upload').on('click', '.close-mapping-modal, #cancel-bulk-upload', function() {
                    $('#bulk-upload-mapping-modal').hide();
                    $('#bulk-upload-progress').hide();
                    $('#confirm-bulk-upload').prop('disabled', false);
                });
                
                // Confirm bulk upload
                $('#confirm-bulk-upload').on('click', function() {
                    const categoryId = currentCategoryId;
                    const files = bulkUploadFiles[categoryId];
                    const mappings = [];
                    const autoCreate = $('#auto-create-unmatched').is(':checked');
                    
                    // Collect mappings in order of files
                    Array.from(files).forEach((file, fileIndex) => {
                        const fileName = file.name;
                        const selectElement = $('.checklist-select[data-file-index="' + fileIndex + '"]');
                        
                        if (selectElement.length === 0) {
                            mappings.push(null);
                            return;
                        }
                        
                        const checklist = selectElement.val();
                        
                        let mapping = null;
                        
                        if (checklist === '__NEW__') {
                            const newChecklistName = selectElement.closest('tr').find('.new-checklist-input').val();
                            if (newChecklistName) {
                                mapping = {
                                    type: 'new',
                                    name: newChecklistName.trim()
                                };
                            } else if (autoCreate) {
                                // Auto-create from filename
                                mapping = {
                                    type: 'new',
                                    name: extractChecklistNameFromFile(fileName)
                                };
                            }
                        } else if (checklist) {
                            mapping = {
                                type: 'existing',
                                name: checklist
                            };
                        } else if (autoCreate) {
                            // Auto-create for unmatched
                            mapping = {
                                type: 'new',
                                name: extractChecklistNameFromFile(fileName)
                            };
                        }
                        
                        if (!mapping) {
                            // Try to get from auto-match if available
                            const matchStatus = selectElement.closest('tr').find('.match-status');
                            if (matchStatus.hasClass('auto-matched') || matchStatus.hasClass('manual')) {
                                const selectedOption = selectElement.find('option:selected');
                                if (selectedOption.val() && selectedOption.val() !== '__NEW__') {
                                    mapping = {
                                        type: 'existing',
                                        name: selectedOption.val()
                                    };
                                }
                            }
                        }
                        
                        mappings.push(mapping);
                    });
                    
                    // Validate all files have mappings
                    const unmappedFiles = [];
                    mappings.forEach((mapping, index) => {
                        if (!mapping || !mapping.name) {
                            unmappedFiles.push(files[index].name);
                        }
                    });
                    
                    if (unmappedFiles.length > 0 && !autoCreate) {
                        alert('Please map all files to checklists or enable "Auto-create checklist for unmatched files"');
                        return;
                    }
                    
                    // Fill in any missing mappings with auto-create
                    mappings.forEach((mapping, index) => {
                        if (!mapping || !mapping.name) {
                            mappings[index] = {
                                type: 'new',
                                name: extractChecklistNameFromFile(files[index].name)
                            };
                        }
                    });
                    
                    // Upload files
                    uploadBulkFiles(categoryId, files, mappings);
                });
                
                // Extract checklist name from filename
                function extractChecklistNameFromFile(fileName) {
                    // Remove extension
                    let name = fileName.replace(/\.[^/.]+$/, '');
                    // Remove client name prefix (if exists)
                    name = name.replace(/^[^_]+_/, '');
                    // Remove timestamps
                    name = name.replace(/_\d{10,}$/, '');
                    // Replace underscores with spaces
                    name = name.replace(/_/g, ' ');
                    // Capitalize first letter of each word
                    name = name.replace(/\b\w/g, l => l.toUpperCase());
                    return name || 'Document';
                }
                
                // Upload bulk files
                function uploadBulkFiles(categoryId, files, mappings) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('clientid', currentClientId);
                    formData.append('categoryid', categoryId);
                    formData.append('doctype', 'personal');
                    formData.append('type', 'client');
                    
                    // Add files
                    Array.from(files).forEach((file, index) => {
                        formData.append('files[]', file);
                        const mapping = mappings[index] || { type: 'new', name: extractChecklistNameFromFile(file.name) };
                        formData.append('mappings[]', JSON.stringify(mapping));
                    });
                    
                    // Show progress
                    $('#bulk-upload-progress').show();
                    $('#bulk-upload-progress-bar').css('width', '0%').text('0%');
                    $('#confirm-bulk-upload').prop('disabled', true);
                    
                    $.ajax({
                        url: '{{ route("clients.documents.bulkUploadPersonalDocuments") }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function() {
                            const xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener('progress', function(e) {
                                if (e.lengthComputable) {
                                    const percentComplete = (e.loaded / e.total) * 100;
                                    $('#bulk-upload-progress-bar').css('width', percentComplete + '%').text(Math.round(percentComplete) + '%');
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            if (response.status) {
                                let message = response.message || 'Files uploaded successfully!';
                                if (response.errors && response.errors.length > 0) {
                                    message += '\n\nWarnings:\n' + response.errors.join('\n');
                                }
                                alert(message);
                                // Reload the page or refresh the document list
                                location.reload();
                            } else {
                                let errorMsg = 'Error: ' + (response.message || 'Upload failed');
                                if (response.errors && response.errors.length > 0) {
                                    errorMsg += '\n\nDetails:\n' + response.errors.join('\n');
                                }
                                alert(errorMsg);
                                $('#bulk-upload-progress').hide();
                                $('#confirm-bulk-upload').prop('disabled', false);
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = 'Upload failed';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            alert('Error: ' + errorMsg);
                            $('#bulk-upload-progress').hide();
                            $('#confirm-bulk-upload').prop('disabled', false);
                        }
                    });
                }
                
                // Format file size
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                }
                
                // Escape HTML
                function escapeHtml(text) {
                    const map = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return text.replace(/[&<>"']/g, m => map[m]);
                }
            </script>


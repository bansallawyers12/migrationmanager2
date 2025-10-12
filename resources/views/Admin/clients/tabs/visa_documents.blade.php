           <!-- Visa Documents Tab (Matter-Specific) -->
           <div class="tab-pane" id="visadocuments-tab">
                <div class="card full-width documentalls-container">
                    <?php
                    $client_selected_matter_id1 = null;
                    $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                    if( $matter_cnt >0 ) {
                        //if client unique reference id is present in url
                        if( isset($id1) && $id1 != "") {
                            $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                        } else {
                            $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                        }
                        if($matter_get_id ){
                            $client_selected_matter_id1 = $matter_get_id->id;
                        }
                    }

                    $visaDocCatList = \App\Models\VisaDocumentType::select('id', 'title','client_id','client_matter_id')
                    ->where('status', 1)
                    ->where(function($query) use ($client_selected_matter_id1) {
                        $query->whereNull('client_matter_id')
                            ->orWhere('client_matter_id', (int) $client_selected_matter_id1);
                    })
                    ->orderBy('id', 'ASC')
                    ->get();
                    ?>

                    <!-- Visa Documents Content -->
                    <div class="visa-documents-content" id="visa-documents-content">
                        <!-- Visa Document Type Subtabs Container -->
                        <div class="subtab-header-container" style="background-color: #4a90e2; padding: 10px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <nav class="subtabs6" style="display: flex; gap: 5px; flex-wrap: wrap; flex: 1;">
                                <?php foreach ($visaDocCatList as $catVal): ?>
                                    <?php
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $folderName = $id;
                                    $isClientGenerated = $catVal->client_matter_id !== null;
                                    ?>
                                    <div style="display: inline-block; position: relative;" class="button-container">
                                        <button class="subtab6-button <?= $isActive ?>" data-subtab6="<?= $id ?>">
                                            <?= htmlspecialchars($catVal->title) ?>
                                        </button>
                                        <?php if ($isClientGenerated): ?>
                                            <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                <button class="btn btn-sm btn-warning update-visa-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </nav>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button type="button" class="btn add-visa-doc-category-btn add-visa-doc-category" data-type="visa" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Category
                                </button>
                                <!-- Add link to Not Used Documents -->
                                <button class="btn btn-secondary client-nav-button" data-tab="notuseddocuments">
                                    <i class="fas fa-folder-minus"></i> Not Used Documents
                                </button>
                            </div>
                        </div>

                        <!-- Subtab6 Contents -->
                        <div class="subtab6-content">
                            <?php foreach ($visaDocCatList as $catVal):
                                $id = $catVal->id;
                                $isActive = $id == 1 ? 'active' : '';
                                $folderName = $id;
                                ?>
                                <div class="subtab6-pane <?= $isActive ?>" id="<?= $id ?>-subtab6">
                                    <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                        <div class="subtab6-header" style="margin-left: 10px;">
                                            <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                            <button type="button" class="btn add-checklist-btn add_migration_doc" data-type="visa" data-categoryid="<?= $id ?>">
                                                <i class="fas fa-plus"></i> Add Checklist
                                            </button>
                                        </div>
                                        <table class="checklist-table">
                                            <thead>
                                                <tr>
                                                    <th>Checklist</th>
                                                    <th>File Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tdata migdocumnetlist1 migdocumnetlist_<?= $id ?>">
                                                <?php
                                                 $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                    ->whereNull('not_used_doc')
                                                    ->where('doc_type', 'visa')
                                                    ->where('folder_name', $folderName)
                                                    ->where('type', 'client')
                                                    ->orderBy('created_at', 'DESC')
                                                    ->get();
                                                ?>
                                                <?php foreach ($documents as $visaKey => $fetch): ?>
                                                    <?php
                                                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                    
                                                    // Ensure $fileUrl is always a valid full URL
                                                    if (!empty($fetch->myfile) && strpos($fetch->myfile, 'http') === 0) {
                                                        // Already a full URL
                                                        $fileUrl = $fetch->myfile;
                                                    } else {
                                                        // Legacy format or relative path - construct full URL
                                                        $fileUrl = 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $fetchedData->id . '/visa/' . $fetch->myfile;
                                                    }
                                                    ?>
                                                    <tr class="drow" data-matterid="<?= $fetch->client_matter_id ?>" data-catid="<?= $fetch->folder_name ?>" id="id_<?= $fetch->id ?>">
                                                        <td style="white-space: initial;">
                                                            <div data-id="<?= $fetch->id ?>" data-visachecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="visachecklist-row" title="Uploaded by: <?= htmlspecialchars($admin->first_name ?? 'NA') ?> on <?= date('d/m/Y H:i', strtotime($fetch->created_at)) ?>">
                                                                <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                            </div>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?php if ($fetch->file_name): ?>
                                                                <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row" title="Uploaded by: <?= htmlspecialchars($admin->first_name ?? 'NA') ?> on <?= date('d/m/Y H:i', strtotime($fetch->created_at)) ?>" oncontextmenu="showVisaFileContextMenu(event, <?= $fetch->id ?>, '<?= htmlspecialchars($fetch->filetype) ?>', '<?= $fileUrl ?>', '<?= $id ?>', '<?= $fetch->status ?? 'draft' ?>'); return false;">
                                                                    <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= $fileUrl ?>','preview-container-migdocumnetlist')">
                                                                        <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                    </a>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="migration_upload_document" style="display: inline-block;">
                                                                    <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?= $fetch->id ?>">
                                                                        @csrf
                                                                        <input type="hidden" name="clientid" value="<?= $fetchedData->id ?>">
                                                                        <input type="hidden" name="client_matter_id" value="<?= $fetch->client_matter_id ?? '' ?>">
                                                                        <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                        <input type="hidden" name="type" value="client">
                                                                        <input type="hidden" name="doctype" value="visa">
                                                                        <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">
                                                                        <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                                                        <input class="migdocupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"/>
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
                                                                <a class="notuseddoc" data-id="<?= $fetch->id ?>" data-doctype="visa" data-href="documents/not-used" href="javascript:;" style="display: none;"></a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="grid_data miggriddata" style="display:none;">
                                        <?php foreach ($visaDocCatList as $catVal):
                                            $id = $catVal->id;
                                            $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                ->whereNull('not_used_doc')
                                                ->where('doc_type', 'visa')
                                                ->where('folder_name', $id)
                                                ->where('type', 'client')
                                                ->orderBy('updated_at', 'DESC')
                                                ->get();
                                            foreach ($documents as $fetch):
                                                if ($fetch->myfile):
                                                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                    ?>
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
                                                                        <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                        @if (strtolower($fetch->filetype) === 'pdf')

                                                                            @if ($fetch->status === 'draft')
                                                                                <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                    <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                        Send To Signature
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            @if($fetch->status === 'sent')
                                                                                <a target="_blank" href="{{ route('documents.index', $fetch->id) }}" class="dropdown-item">Check To Signature</a>
                                                                            @endif

                                                                            @if($fetch->status === 'signed')
                                                                                <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                            @endif

                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <div class="clearfix"></div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="preview-pane file-preview-container preview-container-migdocumnetlist" style="display: inline;margin-top: 15px !important; width: 499px;">
                                        <p>Click on a file to preview it here.</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Context Menu for Visa Documents -->
            <div id="visaFileContextMenu" class="context-menu" style="display: none; position: absolute; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 1000; min-width: 180px;">
                <div class="context-menu-item" onclick="handleVisaContextAction('rename-checklist')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-edit" style="margin-right: 8px;"></i> Rename Checklist
                </div>
                <div class="context-menu-item" onclick="handleVisaContextAction('rename-doc')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-file-text" style="margin-right: 8px;"></i> Rename File Name
                </div>
                <div class="context-menu-item" onclick="handleVisaContextAction('preview')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-eye" style="margin-right: 8px;"></i> Preview
                </div>
                <div id="visa-context-pdf-option" class="context-menu-item" onclick="handleVisaContextAction('pdf')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-file-pdf" style="margin-right: 8px;"></i> PDF
                </div>
                <div class="context-menu-item" onclick="handleVisaContextAction('download')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-download" style="margin-right: 8px;"></i> Download
                </div>
                <div class="context-menu-item" onclick="handleVisaContextAction('not-used')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="fa fa-trash" style="margin-right: 8px;"></i> Not Used
                </div>
                <div id="visa-context-send-signature" class="context-menu-item" onclick="handleVisaContextAction('send-signature')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-signature" style="margin-right: 8px;"></i> Send To Signature
                </div>
                <div id="visa-context-check-signature" class="context-menu-item" onclick="handleVisaContextAction('check-signature')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-check-circle" style="margin-right: 8px;"></i> Check To Signature
                </div>
                <div id="visa-context-download-signed" class="context-menu-item" onclick="handleVisaContextAction('download-signed')" style="padding: 8px 12px; cursor: pointer; display: none;">
                    <i class="fa fa-file-signature" style="margin-right: 8px;"></i> Download Signed
                </div>
            </div>

            <script>
                let currentVisaContextFile = null;
                let currentVisaContextData = {};

                function showVisaFileContextMenu(event, fileId, fileType, fileUrl, categoryId, fileStatus) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    currentVisaContextFile = fileId;
                    currentVisaContextData = {
                        fileId: fileId,
                        fileType: fileType,
                        fileUrl: fileUrl,
                        categoryId: categoryId,
                        fileStatus: fileStatus
                    };

                    const menu = document.getElementById('visaFileContextMenu');
                    
                    // Show/hide PDF option based on file type
                    const pdfOption = document.getElementById('visa-context-pdf-option');
                    const fileExt = fileType.toLowerCase();
                    if (['jpg', 'png', 'jpeg'].includes(fileExt)) {
                        pdfOption.style.display = 'block';
                    } else {
                        pdfOption.style.display = 'none';
                    }

                    // Show/hide signature options based on file type and status
                    const sendSignature = document.getElementById('visa-context-send-signature');
                    const checkSignature = document.getElementById('visa-context-check-signature');
                    const downloadSigned = document.getElementById('visa-context-download-signed');
                    
                    if (fileType.toLowerCase() === 'pdf') {
                        if (fileStatus === 'draft') {
                            sendSignature.style.display = 'block';
                            checkSignature.style.display = 'none';
                            downloadSigned.style.display = 'none';
                        } else if (fileStatus === 'sent') {
                            sendSignature.style.display = 'none';
                            checkSignature.style.display = 'block';
                            downloadSigned.style.display = 'none';
                        } else if (fileStatus === 'signed') {
                            sendSignature.style.display = 'none';
                            checkSignature.style.display = 'none';
                            downloadSigned.style.display = 'block';
                        } else {
                            sendSignature.style.display = 'none';
                            checkSignature.style.display = 'none';
                            downloadSigned.style.display = 'none';
                        }
                    } else {
                        sendSignature.style.display = 'none';
                        checkSignature.style.display = 'none';
                        downloadSigned.style.display = 'none';
                    }

                    // Position menu at cursor with edge detection
                    const MENU_WIDTH = 180;
                    const MENU_HEIGHT = 350;
                    
                    // Get scroll position
                    const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    // Get viewport dimensions
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    // Use clientX/Y for viewport-relative position, then add scroll for document position
                    let menuLeft = event.clientX + scrollLeft + 5;
                    let menuTop = event.clientY + scrollTop + 5;
                    
                    // Check right edge - if menu would go beyond viewport, show on left
                    if (event.clientX + MENU_WIDTH + 5 > viewportWidth) {
                        menuLeft = event.clientX + scrollLeft - MENU_WIDTH - 5;
                    }
                    
                    // Check bottom edge - if menu would go beyond viewport, show above
                    if (event.clientY + MENU_HEIGHT + 5 > viewportHeight) {
                        menuTop = event.clientY + scrollTop - MENU_HEIGHT - 5;
                    }
                    
                    // Apply position
                    menu.style.left = menuLeft + 'px';
                    menu.style.top = menuTop + 'px';

                    menu.style.display = 'block';

                    // Hide menu when clicking elsewhere
                    setTimeout(() => {
                        document.addEventListener('click', hideVisaContextMenu);
                    }, 100);
                }

                function hideVisaContextMenu() {
                    const menu = document.getElementById('visaFileContextMenu');
                    menu.style.display = 'none';
                    document.removeEventListener('click', hideVisaContextMenu);
                }

                function handleVisaContextAction(action) {
                    if (!currentVisaContextFile) return;

                    hideVisaContextMenu();

                    switch(action) {
                        case 'rename-checklist':
                            $('.renamechecklist[data-id="' + currentVisaContextFile + '"]').click();
                            break;
                        case 'rename-doc':
                            $('.renamedoc[data-id="' + currentVisaContextFile + '"]').click();
                            break;
                        case 'preview':
                            window.open(currentVisaContextData.fileUrl, '_blank');
                            break;
                        case 'pdf':
                            const pdfUrl = '{{ URL::to('/admin/document/download/pdf') }}/' + currentVisaContextFile;
                            window.open(pdfUrl, '_blank');
                            break;
                        case 'download':
                            // Try to find download button by filelink
                            let $downloadBtn = $('.download-file[data-filelink="' + currentVisaContextData.fileUrl + '"]');
                            if ($downloadBtn.length === 0) {
                                // Fallback: try finding by document ID
                                $downloadBtn = $('.download-file[data-id="' + currentVisaContextFile + '"]');
                            }
                            if ($downloadBtn.length > 0) {
                                $downloadBtn.click();
                            } else {
                                console.error('Download button not found for file ID:', currentVisaContextFile);
                                alert('Download link not found. Please refresh the page and try again.');
                            }
                            break;
                        case 'not-used':
                            $('.notuseddoc[data-id="' + currentVisaContextFile + '"]').click();
                            break;
                        case 'send-signature':
                            const sendSignatureUrl = '{{ route('documents.edit', ':id') }}'.replace(':id', currentVisaContextFile);
                            window.open(sendSignatureUrl, '_blank');
                            break;
                        case 'check-signature':
                            const checkSignatureUrl = '{{ route('documents.index', ':id') }}'.replace(':id', currentVisaContextFile);
                            window.open(checkSignatureUrl, '_blank');
                            break;
                        case 'download-signed':
                            const downloadSignedUrl = '{{ route('download.signed', ':id') }}'.replace(':id', currentVisaContextFile);
                            window.open(downloadSignedUrl, '_blank');
                            break;
                    }
                }

                // Hide context menu on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        hideVisaContextMenu();
                    }
                });
            </script>

            <style>
                .context-menu-item:hover {
                    background-color: #f8f9fa;
                }
            </style>


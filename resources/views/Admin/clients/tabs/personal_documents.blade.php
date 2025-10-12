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
                                            <button type="button" class="btn add-checklist-btn add_education_doc" data-type="personal" data-categoryid="<?= $id ?>">
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
                                            <tbody class="tdata persdocumnetlist documnetlist_<?= $id ?>">
                                                <?php
                                                $documents = \App\Models\Document::where('client_id', $clientId)
                                                    ->whereNull('not_used_doc')
                                                    ->where('doc_type', 'personal')
                                                    ->where('folder_name', $folderName)
                                                    ->where('type', 'client')
                                                    ->orderBy('created_at', 'DESC')
                                                    ->get();
                                                ?>
                                                <?php foreach ($documents as $docKey => $fetch): ?>
                                                    <?php
                                                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                    
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
                                                            <div data-id="<?= $fetch->id ?>" data-personalchecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="personalchecklist-row" title="Uploaded by: <?= htmlspecialchars($admin->first_name ?? 'NA') ?> on <?= date('d/m/Y H:i', strtotime($fetch->created_at)) ?>">
                                                                <span><?= htmlspecialchars($fetch->checklist) ?></span>
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

                                                                        <a href="javascript:;" class="btn btn-primary add-document" data-fileid="<?= $fetch->id ?>"><i class="fa fa-plus"></i> Add Document</a>
				                                                        <input class="docupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"/>
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

                                                                     @if (strtolower($fetch->filetype) === 'pdf')

                                                                            @if ($fetch->status === 'draft')
                                                                                <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                    <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                        Send To Signature
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            @if($fetch->status === 'sent')
                                                                                <form method="GET" action="{{ route('documents.index', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                    <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                        Check To Signature
                                                                                    </button>
                                                                                </form>
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
                <div id="context-send-signature" class="context-menu-item" onclick="handleContextAction('send-signature')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-signature" style="margin-right: 8px;"></i> Send To Signature
                </div>
                <div id="context-check-signature" class="context-menu-item" onclick="handleContextAction('check-signature')" style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; display: none;">
                    <i class="fa fa-check-circle" style="margin-right: 8px;"></i> Check To Signature
                </div>
                <div id="context-download-signed" class="context-menu-item" onclick="handleContextAction('download-signed')" style="padding: 8px 12px; cursor: pointer; display: none;">
                    <i class="fa fa-file-signature" style="margin-right: 8px;"></i> Download Signed
                </div>
            </div>

            <script>
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

                    // Show/hide signature options based on file type and status
                    const sendSignature = document.getElementById('context-send-signature');
                    const checkSignature = document.getElementById('context-check-signature');
                    const downloadSigned = document.getElementById('context-download-signed');
                    
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
                            const pdfUrl = '{{ URL::to('/admin/document/download/pdf') }}/' + currentContextFile;
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
                        case 'send-signature':
                            const sendSignatureUrl = '{{ route('documents.edit', ':id') }}'.replace(':id', currentContextFile);
                            window.open(sendSignatureUrl, '_blank');
                            break;
                        case 'check-signature':
                            const checkSignatureUrl = '{{ route('documents.index', ':id') }}'.replace(':id', currentContextFile);
                            window.open(checkSignatureUrl, '_blank');
                            break;
                        case 'download-signed':
                            const downloadSignedUrl = '{{ route('download.signed', ':id') }}'.replace(':id', currentContextFile);
                            window.open(downloadSignedUrl, '_blank');
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
            </style>


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
                                    <i class="fas fa-plus"></i> Add Personal Document Category
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
                                                <i class="fas fa-plus"></i> Add Personal Checklist
                                            </button>
                                        </div>
                                        <table class="checklist-table">
                                            <thead>
                                                <tr>
                                                    <th>SNo.</th>
                                                    <th>Checklist</th>
                                                    <th>Added By</th>
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
                                                    $fileUrl = $fetch->myfile_key
                                                        ? asset($fetch->myfile)
                                                        : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $clientId . '/personal/' . $fetch->myfile;
                                                    ?>
                                                    <tr class="drow" id="id_<?= $fetch->id ?>">
                                                        <td><?= $docKey + 1 ?></td>
                                                        <td style="white-space: initial;">
                                                            <div data-id="<?= $fetch->id ?>" data-personalchecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="personalchecklist-row">
                                                                <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                            </div>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                            <?= date('d/m/Y', strtotime($fetch->created_at)) ?>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?php if ($fetch->file_name): ?>
                                                                <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row">
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
				                                                        <input class="docupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"  style="display: none;">
                                                                    </form>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($fetch->myfile): ?>
                                                                <div class="dropdown d-inline">
                                                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>
                                                                    <div class="dropdown-menu">
                                                                        <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                        <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                                                        <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                        <?php
                                                                        $fileExt = pathinfo($fetch->myfile, PATHINFO_EXTENSION);
                                                                        if (in_array($fileExt, ['jpg', 'png', 'jpeg'])): ?>
                                                                            <a target="_blank" class="dropdown-item" href="<?= URL::to('/admin/document/download/pdf') ?>/<?= $fetch->id ?>">PDF</a>
                                                                        <?php endif; ?>
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
                                                                                <a target="_blank" href="{{ route('admin.download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
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
                                        <h3>File Preview</h3>
                                        <p>Click on a file to preview it here.</p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>


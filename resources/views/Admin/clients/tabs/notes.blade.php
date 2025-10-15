            <!-- Notes Tab -->
            <div class="tab-pane" id="noteterm-tab">
                <div class="card full-width notes-container">
                    <div class="notes-header">
                        <h3><i class="fas fa-file-alt"></i> Notes</h3>
                        <button class="btn btn-primary btn-sm create_note_d" datatype="note">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </div>

                    <!-- Redesigned Tabs -->
                    <div class="subtab-header-container">
                        <nav class="subtabs8 note-pills" style="margin: 10px 0 0 10px; display: flex; gap: 10px;">
                            <button class="subtab8-button pill-tab active" data-subtab8="All">All</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Call">Call</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Email">Email</button>
                            <button class="subtab8-button pill-tab" data-subtab8="In-Person">In-Person</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Others">Others</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Attention">Attention</button>
                            <button class="subtab8-button pill-tab" data-subtab8="Uncategorized">Uncategorized</button>
                        </nav>
                    </div>

                    <style>
                        .note-pills .pill-tab {
                            border-radius: 999px;
                            padding: 8px 22px;
                            border: none;
                            background: #f1f5f9;
                            color: #333;
                            font-weight: 500;
                            font-size: 1rem;
                            transition: background 0.2s, color 0.2s;
                        }
                        .note-pills .pill-tab.active {
                            background: #2563eb;
                            color: #fff;
                        }
                        .note-pills .pill-tab:not(.active):hover {
                            background: #e0e7ef;
                        }
                        .note-card-redesign {
                            background: #f9f9f9;
                            border-radius: 16px;
                            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                            padding: 24px 28px 20px 28px;
                            margin-bottom: 18px;
                            border: none;
                            position: relative;
                        }
                        .note-type-label {
                            display: inline-block;
                            font-size: 0.95rem;
                            font-weight: 600;
                            border-radius: 16px;
                            padding: 6px 18px;
                            margin-bottom: 5px;
                        }
                        .note-type-inperson { background: #e6f4ea; color: #219653; }
                        .note-type-call { background: #e3f0fd; color: #2563eb; }
                        .note-type-email { background: #fdeaea; color: #e74c3c; }
                        .note-type-attention { background: #f3e8ff; color: #8e44ad; }
                        .note-type-others { background: #f5f5f5; color: #888; }
                        .note-type-uncategorized { background: #fff3cd; color: #856404; }
                        .note-title {
                            font-size: 1.18rem;
                            font-weight: 700;
                            color: #22223b;
                            margin-bottom: 2px;
                        }
                        .note-meta-redesign {
                            font-size: 0.97rem;
                            color: #6c757d;
                            margin-bottom: 8px;
                        }
                        .note-content-redesign {
                            color: #444;
                            font-size: 1.01rem;
                            margin-bottom: 8px;
                        }
                        .viewnote {
                            color: #2563eb;
                            font-size: 0.97rem;
                            text-decoration: underline;
                            cursor: pointer;
                        }
                        .author-name-created {
                            font-size:0.67rem;
                            color:#6c757d;
                        }
                        .author-updated-date-time {
                            font-size:0.67rem;
                            color:#6c757d;
                        }
                        .note-card-info {
                            display: flex;
                            align-items: center;
                            gap: 12px;
                            margin-bottom:rgba(41, 31, 31, 0.07)
                        }
                        .note-toggle-btn-div {
                            position:absolute;
                            top:18px;
                            right:18px;
                        }
                        .note-toggle-btn-div-type {
                            display:inline-grid;
                            width: 133px;
                        }
                        .pined_note {
                            position: absolute;
                            top: 1px;
                            right: 65px;
                        }
                    </style>

                    <!-- Notes List -->
                    <div class="note_term_list subtab8-content">
                        <?php
                        $notelist = \App\Models\Note::where('client_id', $fetchedData->id)
                            ->whereNull('assigned_to')
                            ->where('type', 'client')
                            ->orderby('pin', 'DESC')
                            ->orderBy('updated_at', 'DESC')
                            ->get(); 
                        foreach($notelist as $list) {
                            $admin = \App\Models\Admin::where('id', $list->user_id)->first();
                            // Determine type label and color
                            if($list->task_group === null || $list->task_group === '') {
                                // Handle NULL or empty task_group - assign to "Uncategorized"
                                $typeLabel = 'Uncategorized';
                                $typeClass = 'note-type-uncategorized';
                            } else {
                                $type = strtolower($list->task_group);
                                $typeLabel = 'Others';
                                $typeClass = 'note-type-others';

                                if(strpos($type, 'call') !== false) { $typeLabel = 'Call'; $typeClass = 'note-type-call'; }
                                else if(strpos($type, 'email') !== false) { $typeLabel = 'Email'; $typeClass = 'note-type-email'; }
                                else if(strpos($type, 'in-person') !== false) { $typeLabel = 'In-Person'; $typeClass = 'note-type-inperson'; }
                                else if(strpos($type, 'others') !== false) { $typeLabel = 'Others'; $typeClass = 'note-type-others'; }
                                else if(strpos($type, 'attention') !== false) { $typeLabel = 'Attention'; $typeClass = 'note-type-attention'; }
                            }

                            //$desc = strip_tags($list->description);
                        ?>
                        <div class="note-card-redesign <?php if($list->pin == 1) echo 'pinned'; ?>" data-matterid="{{ $list->matter_id }}" id="note_id_{{$list->id}}" data-id="{{$list->id}}" data-type="{{ $typeLabel }}">
                            <?php if($list->pin == 1) { ?>
                                <div class="pined_note">
                                    <i class="fa fa-thumb-tack" aria-hidden="true"></i>
                                </div>
                            <?php } ?>

                            <div class="note-card-info">
                                <span class="note-type-label {{ $typeClass }}">{{ $typeLabel }}</span>
                                <span class="author-name-created">{{ $admin->first_name ?? 'NA' }} {{ $admin->last_name ?? 'NA' }}</span>
                                <span class="author-updated-date-time">{{date('d/m/Y h:i A', strtotime($list->updated_at))}}</span>
                            </div>
                            <!--<div class="note-content-redesign">{--!! nl2br(e($desc)) !!--}</div>-->
                            <div class="note-content-redesign">
                                @if(!empty($list->description))
                                    @php
                                        $description = $list->description;
                                    @endphp

                                    @if(strpos($description, '<xml>') !== false || strpos($description, '<o:OfficeDocumentSettings>') !== false)
                                        <p>{!! htmlentities($description) !!}</p>
                                    @else
                                        <p>{!! $description !!}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="note-toggle-btn-div">
                                <div class="dropdown">
                                    <button class="btn btn-link dropdown-toggle note-toggle-btn-div-type" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item opennoteform" data-id="{{$list->id}}" href="javascript:;">Edit</a>
                                        @if(Auth::user()->role == 1 || Auth::user()->role == 16)
                                            <a class="dropdown-item editdatetime" data-id="{{$list->id}}" href="javascript:;">Edit Date Time</a>
                                        @endif
                                        <a data-id="{{$list->id}}" data-href="deletenote" class="dropdown-item deletenote" href="javascript:;">Delete</a>
                                        <?php if($list->pin == 1) { ?>
                                            <a data-id="{{$list->id}}" class="dropdown-item pinnote" href="javascript:;">Unpin</a>
                                        <?php } else { ?>
                                            <a data-id="{{$list->id}}" class="dropdown-item pinnote" href="javascript:;">Pin</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.subtab8-button.pill-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        // Get selected matter
                        let selectedMatter;
                        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                            selectedMatter = $('.general_matter_checkbox_client_detail').val();
                        } else {
                            selectedMatter = $('#sel_matter_id_client_detail').val();
                        }
                        // Remove active from all tabs
                        document.querySelectorAll('.subtab8-button.pill-tab').forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        const type = this.getAttribute('data-subtab8');
                        console.log('Selected Matter:', selectedMatter, 'Type:', type);
                        // Show/hide notes based on type and matter
                        document.querySelectorAll('.note-card-redesign').forEach(card => {
                            const cardType = card.getAttribute('data-type');
                            const cardMatter = card.getAttribute('data-matterid');
                            const typeMatch = (type === 'All' || cardType === type);
                            
                            let matterMatch = false;
                            // Matter filtering logic
                            if (selectedMatter && selectedMatter !== "" && selectedMatter !== null && selectedMatter !== undefined) {
                                // Show notes that match the selected matter OR notes with no matter_id
                                matterMatch = (cardMatter == selectedMatter || cardMatter == '' || cardMatter == null);
                            } else {
                                // Show all notes when no matter is selected
                                matterMatch = true;
                            }
                            
                            console.log('Note:', cardType, 'Matter:', cardMatter, 'TypeMatch:', typeMatch, 'MatterMatch:', matterMatch);
                            
                            if (typeMatch && matterMatch) {
                                card.style.display = '';
                                console.log('SHOWING note:', cardType);
                            } else {
                                card.style.display = 'none';
                                console.log('HIDING note:', cardType);
                            }
                        });
                    });
                });
                // On page load, ensure All tab is active and shows all notes
                setTimeout(function() {
                    const allTab = document.querySelector('.subtab8-button.pill-tab[data-subtab8="All"]');
                    if (allTab) {
                        // Remove active from all tabs first
                        document.querySelectorAll('.subtab8-button.pill-tab').forEach(t => t.classList.remove('active'));
                        
                        // Make All tab active
                        allTab.classList.add('active');
                        
                        // Get selected matter
                        let selectedMatter;
                        if ($('.general_matter_checkbox_client_detail').is(':checked')) {
                            selectedMatter = $('.general_matter_checkbox_client_detail').val();
                        } else {
                            selectedMatter = $('#sel_matter_id_client_detail').val();
                        }
                        
                        console.log('Page load - Selected Matter:', selectedMatter, 'Type: All');
                        
                        // Show/hide notes based on All type and matter
                        document.querySelectorAll('.note-card-redesign').forEach(card => {
                            const cardType = card.getAttribute('data-type');
                            const cardMatter = card.getAttribute('data-matterid');
                            const typeMatch = true; // All tab shows all types
                            
                            let matterMatch = false;
                            // Matter filtering logic
                            if (selectedMatter && selectedMatter !== "" && selectedMatter !== null && selectedMatter !== undefined) {
                                // Show notes that match the selected matter OR notes with no matter_id
                                matterMatch = (cardMatter == selectedMatter || cardMatter == '' || cardMatter == null);
                            } else {
                                // Show all notes when no matter is selected
                                matterMatch = true;
                            }
                            
                            console.log('Page load - Note:', cardType, 'Matter:', cardMatter, 'TypeMatch:', typeMatch, 'MatterMatch:', matterMatch);
                            
                            if (typeMatch && matterMatch) {
                                card.style.display = '';
                                console.log('Page load - SHOWING note:', cardType);
                            } else {
                                card.style.display = 'none';
                                console.log('Page load - HIDING note:', cardType);
                            }
                        });
                        
                        console.log('Page load - All tab activated and notes filtered');
                    }
                }, 200);
                

            });
            </script>

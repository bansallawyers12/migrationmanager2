{{-- Create Task Modal --}}
<div class="modal fade custom_modal" id="create_task_modal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Create New Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="#" name="newtaskform" autocomplete="off" id="tasktermform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="is_ajax" value="0">
                    <input type="hidden" name="is_dashboard" value="true">
                    
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="title">Title <span class="span_req">*</span></label>
                                <input type="text" name="title" class="form-control" required autocomplete="off" placeholder="Enter Title">
                                <span class="custom-error title_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="category">Category <span class="span_req">*</span></label>
                                <select data-valid="required" class="form-control cleintselect2 select2" name="category">
                                    <option value="">Choose Category</option>
                                    <option value="Reminder">Reminder</option>
                                    <option value="Call">Call</option>
                                    <option value="Follow Up">Follow Up</option>
                                    <option value="Email">Email</option>
                                    <option value="Meeting">Meeting</option>
                                    <option value="Support">Support</option>
                                    <option value="Others">Others</option>
                                </select>
                                <span class="custom-error category_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="assignee">Assignee</label>
                                <select data-valid="" class="form-control cleintselect2 select2" name="assignee">
                                    <option value="">Select</option>
                                    @foreach(\App\Models\Admin::select('id','first_name','email')->where('role','!=',1)->get() as $assigne)
                                        <option value="{{$assigne->id}}">{{$assigne->first_name}} ({{$assigne->email}})</option>
                                    @endforeach
                                </select>
                                <span class="custom-error assignee_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select data-valid="" class="form-control cleintselect2 select2" name="priority">
                                    <option value="">Choose Priority</option>
                                    <option value="Low">Low</option>
                                    <option value="Normal">Normal</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                                <span class="custom-error priority_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description"></textarea>
                                <span class="custom-error description_error" role="alert">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>

                        <div class="col-12 col-md-12 col-lg-12">
                            <button onclick="customValidate('newtaskform')" type="button" class="btn btn-primary">Create</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Extend Note Deadline Modal --}}
<div class="modal fade custom_modal" id="extend_note_popup" tabindex="-1" role="dialog" aria-labelledby="create_action_popupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="padding: 20px;">
            <div class="modal-header" style="padding-bottom: 11px;">
                <h5 class="modal-title assignnn" id="create_action_popupLabel" style="margin: 0 -24px;">Extend Notes Deadline</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input id="note_id" type="hidden" value="">
            <input id="unique_group_id" type="hidden" value="">
            <div id="popover-content">
                <div class="box-header with-border">
                    <div class="form-group row" style="margin-bottom:12px;">
                        <label for="inputEmail3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">Note</label>
                        <div class="col-sm-9">
                            <textarea id="assignnote" class="form-control" placeholder="Enter a note..."></textarea>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="form-group row note_deadline">
                    <label for="inputSub3" class="col-sm-3 control-label c6 f13" style="margin-top:8px;">
                        Note Deadline
                    </label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control f13" placeholder="yyyy-mm-dd" id="note_deadline" value="{{ date('Y-m-d') }}" name="note_deadline">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="box-footer" style="padding:10px 0;">
                    <div class="row text-center">
                        <div class="col-md-12 text-center">
                            <button class="btn btn-danger" id="extend_deadline">Extend Deadline</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

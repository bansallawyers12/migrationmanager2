           <!-- Appointment Tab -->
           <div class="tab-pane" id="appointments-tab">
                <div class="card-header-action text-right" style="padding-bottom:15px;">
                    <a href="javascript:;" data-toggle="modal" data-target="#create_appoint" class="btn btn-primary createaddapointment"><i class="fa fa-plus"></i> Add Appointment</a>
                </div>
                <div class="appointmentlist">
                    <div class="row">
                        <div class="col-md-5 appointment_grid_list">
                            <?php
                            $rr=0;
                            $appointmentdata = array();
                            $appointmentlists = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->get();

                            $appointmentlistslast = \App\Models\Appointment::where('client_id', $fetchedData->id)->where('related_to', 'client')->orderby('created_at', 'DESC')->first();
                            foreach($appointmentlists as $appointmentlist){
                                $admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $appointmentlist->user_id)->first();
                                $first_name= $admin->first_name ?? 'N/A';
                                $datetime = $appointmentlist->created_at;
                                $timeago = \App\Http\Controllers\Controller::time_elapsed_string($datetime);

                                $appointmentdata[$appointmentlist->id] = array(
                                    'title' => $appointmentlist->title,
                                    'time' => date('H:i A', strtotime($appointmentlist->time)),
                                    'date' => date('d D, M Y', strtotime($appointmentlist->date)),
                                    //'description' => $appointmentlist->description,
                                    'description' => htmlspecialchars($appointmentlist->description, ENT_QUOTES, 'UTF-8'),
                                    'createdby' => substr($first_name, 0, 1),
                                    'createdname' => $first_name,
                                    'createdemail' => $admin->email ?? 'N/A',
                                );
                            ?>

                            <div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
                                <div class="appointment_col">
                                    <div class="appointdate">
                                        <h5><?php echo date('d D', strtotime($appointmentlist->date)); ?></h5>
                                        <p><?php echo date('H:i A', strtotime($appointmentlist->time)); ?><br>
                                        <i><small><?php echo $timeago ?></small></i></p>
                                    </div>
                                    <div class="title_desc">
                                        <h5><?php echo $appointmentlist->title; ?></h5>
                                        <p><?php echo $appointmentlist->description; ?></p>
                                    </div>
                                    <div class="appoint_created">
                                        <span class="span_label">Created By:
                                        <span><?php echo substr($first_name, 0, 1); ?></span></span>
                                        <div class="dropdown d-inline dropdown_ellipsis_icon">
                                            <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu">
                                                <!--<a class="dropdown-item edit_appointment" data-id="{{--$appointmentlist->id--}}" href="javascript:;">Edit</a>-->
                                                <a data-id="{{$appointmentlist->id}}" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $rr++; } ?>
                        </div>
                        <div class="col-md-7">
                            <div class="editappointment">
                            @if($appointmentlistslast)
                                <!--<a class="edit_link edit_appointment" href="javascript:;" data-id="<?php //echo @$appointmentlistslast->id; ?>"><i class="fa fa-edit"></i></a>-->
                                <?php
                                $adminfirst = \App\Models\Admin::select('id', 'first_name','email')->where('id', @$appointmentlistslast->user_id)->first();
                                ?>
                                <div class="content">
                                    <h4 class="appointmentname"><?php echo @$appointmentlistslast->title; ?></h4>
                                    <div class="appitem">
                                        <i class="fa fa-clock"></i>
                                        <span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime(@$appointmentlistslast->time)); ?></span>
                                    </div>
                                    <div class="appitem">
                                        <i class="fa fa-calendar"></i>
                                        <span class="appcontent appointmentdate"><?php echo date('d D, M Y', strtotime(@$appointmentlistslast->date)); ?></span>
                                    </div>
                                    <div class="description appointmentdescription">
                                        <p><?php echo @$appointmentlistslast->description; ?></p>
                                    </div>
                                    <div class="created_by">
                                        <span class="label">Created By:</span>
                                        <div class="createdby">
                                            <span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
                                        </div>
                                        <div class="createdinfo">
                                            <a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
                                            <p class="appointmentcreatedemail"><?php echo @$adminfirst->primary_email; ?></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


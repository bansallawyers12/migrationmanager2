           <!-- Appointment Tab -->
           <div class="tab-pane" id="appointments-tab">
                <!-- Add Appointment Button -->
                <div class="mb-3 text-right" style="padding: 0 15px;">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create_appoint" style="background-color: #0056b3; border-color: #0056b3;">
                        <i class="fas fa-calendar-plus mr-2"></i>Add Appointment
                    </button>
                </div>
                <div class="appointmentlist">
                    <div class="row">
                        <div class="col-md-5 appointment_grid_list">
                            <?php
                            $rr=0;
                            $appointmentdata = [];
                            $appointmentlists = \App\Models\BookingAppointment::where('client_id', $fetchedData->id)->orderby('created_at', 'DESC')->get();

                            $appointmentlistslast = \App\Models\BookingAppointment::where('client_id', $fetchedData->id)->orderby('created_at', 'DESC')->first();
                            foreach($appointmentlists as $appointmentlist){
                                $admin = \App\Models\Staff::select('id', 'first_name','email')->where('id', $appointmentlist->user_id)->first();
                                $first_name= $admin->first_name ?? 'N/A';
                                $datetime = $appointmentlist->created_at;
                                $timeago = \App\Http\Controllers\Controller::time_elapsed_string($datetime);

                                // Extract start time from timeslot_full (format: "10:00 AM - 10:15 AM" or just "10:00 AM")
                                $appointmentTime = '';
                                if($appointmentlist->timeslot_full) {
                                    $timeslotParts = explode(' - ', $appointmentlist->timeslot_full);
                                    $appointmentTime = trim($timeslotParts[0] ?? '');
                                }

                                $appointmentdata[$appointmentlist->id] = [
                                    'title' => $appointmentlist->service_type ?? 'N/A',
                                    'time' => $appointmentTime,
                                    'date' => $appointmentlist->appointment_datetime ? date('d D, M Y', strtotime($appointmentlist->appointment_datetime)) : '',
                                    'description' => htmlspecialchars($appointmentlist->enquiry_details ?? '', ENT_QUOTES, 'UTF-8'),
                                    'createdby' => substr($first_name, 0, 1),
                                    'createdname' => $first_name,
                                    'createdemail' => $admin->email ?? 'N/A',
                                ];
                            ?>

                            <div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
                                <div class="appointment_col">
                                    <div class="appointdate">
                                        <h5><?php echo $appointmentlist->appointment_datetime ? date('d/m/Y', strtotime($appointmentlist->appointment_datetime)) : ''; ?></h5>
                                        <p><?php 
                                            // Extract start time from timeslot_full
                                            $displayTime = '';
                                            if($appointmentlist->timeslot_full) {
                                                $timeslotParts = explode(' - ', $appointmentlist->timeslot_full);
                                                $displayTime = trim($timeslotParts[0] ?? '');
                                            }
                                            echo $displayTime;
                                        ?><br>
                                        <i><small><?php echo $timeago ?></small></i></p>
                                    </div>
                                    <div class="title_desc">
                                        <h5><?php echo $appointmentlist->service_type; ?></h5>
                                        <p><?php echo $appointmentlist->enquiry_details; ?></p>
                                    </div>
                                    <div class="appoint_created">
                                        <span class="span_label">Created By:
                                        <span><?php echo substr($first_name, 0, 1); ?></span></span>
                                    </div>
                                </div>
                            </div>
                            <?php $rr++; } ?>
                        </div>
                        <div class="col-md-7">
                            <div class="editappointment">
                            @if($appointmentlistslast)
                                <?php
                                $adminfirst = \App\Models\Staff::select('id', 'first_name','email')->where('id', @$appointmentlistslast->user_id)->first();
                                ?>
                                <div class="content">
                                    <h4 class="appointmentname"><?php echo @$appointmentlistslast->service_type; ?></h4>
                                    <div class="appitem">
                                        <i class="fa fa-clock"></i>
                                        <span class="appcontent appointmenttime"><?php 
                                            // Extract start time from timeslot_full
                                            $displayTimeLast = '';
                                            if(@$appointmentlistslast->timeslot_full) {
                                                $timeslotPartsLast = explode(' - ', $appointmentlistslast->timeslot_full);
                                                $displayTimeLast = trim($timeslotPartsLast[0] ?? '');
                                            }
                                            echo $displayTimeLast;
                                        ?></span>
                                    </div>
                                    <div class="appitem">
                                        <i class="fa fa-calendar"></i>
                                        <span class="appcontent appointmentdate"><?php echo @$appointmentlistslast->appointment_datetime ? date('d D, M Y', strtotime($appointmentlistslast->appointment_datetime)) : ''; ?></span>
                                    </div>
                                    <div class="description appointmentdescription">
                                        <p><?php echo @$appointmentlistslast->enquiry_details; ?></p>
                                    </div>
                                    <div class="created_by">
                                        <span class="label">Created By:</span>
                                        <div class="createdby">
                                            <span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
                                        </div>
                                        <div class="createdinfo">
                                            <a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
                                            <p class="appointmentcreatedemail"><?php echo @$adminfirst->email; ?></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Ensure appointmentData is available for click handler
                @if(isset($appointmentdata) && !empty($appointmentdata))
                    window.appointmentData = {!! json_encode($appointmentdata, JSON_FORCE_OBJECT) !!};
                @endif
            </script>


@extends('layouts.admin_client_detail')
@section('title', 'Appointments')

@section('content')
<style>
.fc-event-container .fc-h-event{cursor:pointer;}
</style>

<!-- Main Content -->
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<div class="server-error">
				@include('../Elements/flash-message')
			</div>
			<div class="custom-error-msg">
			</div>
			<div class="row">
				<div class="col-12 col-md-12 col-lg-12">
					<div class="card">
						<div class="card-header">
						@if($type=="Education")
							<h4>Education Appointments Calendar</h4>
						@elseif($type=="Jrp")
							<h4>Tr Appointments Calendar</h4>
						@elseif($type=="Tourist")
							<h4>Tourist Appointments Calendar</h4>

                        @elseif($type == "Adelaide")
							<h4>Adelaide Appointments Calendar</h4>

						@else
							<h4>Arun Appointments Calendar</h4>
						@endif
						</div>
						<div class="card-body">
							 <div class="fc-overflow">
								<div id="myEvent"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<?php
$sched_res = [];
// Handle API response structure - check if data exists in the response
$appointmentsData = [];
if (isset($appointments['data']) && is_array($appointments['data'])) {
    $appointmentsData = $appointments['data'];
} elseif (isset($appointments['data']['data']) && is_array($appointments['data']['data'])) {
    $appointmentsData = $appointments['data']['data'];
} else {
    $appointmentsData = $appointments;
}

// Debug: Log the appointments data structure (remove in production)
// error_log('Appointments Data Structure: ' . print_r($appointments, true));
// error_log('Appointments Data Count: ' . count($appointmentsData));

foreach($appointmentsData as $appointment){ //dd($appointment);
    $datetimes = $appointment['date'];
    $datetime = $appointment['date'].' '.$appointment['time'];
    $curtime = date('Y-m-d');
    if(strtotime($datetimes) >= strtotime($curtime)){
		$row['id'] = $appointment['id'];
        $row['stitle'] = $appointment['client_unique_id'] ? addslashes($appointment['client_unique_id']) : 'NA';
        // Handle different client data structures from API
        $clientName = 'NA';
        if (isset($appointment['client']['name'])) {
            $clientName = $appointment['client']['name'];
        } elseif (isset($appointment['full_name'])) {
            $clientName = $appointment['full_name'];
        }
        $row['name'] = base64_encode($clientName);

		$row['startdate'] = date("F d, Y h:i A",strtotime($appointment['date']));
        $row['start'] = date("d F, Y h:i A",strtotime($datetime));

		if( isset($appointment['timeslot_full']) && $appointment['timeslot_full'] != "" ) {
            $timeslot_full_arr = explode("-", $appointment['timeslot_full']);
            if(!empty($timeslot_full_arr)){
                $appointment_end_date_time = $appointment['date'].' '.$timeslot_full_arr[1];
                $row['end'] = date("d F, Y h:i A",strtotime($appointment_end_date_time));
            }
        }
        $row['appointdate'] = date("d/m/Y",strtotime($appointment['date']));
        $row['appointtime'] = $appointment['time'];
		$row['status'] = $appointment['status'];
		$row['url'] = URL::to('/admin/clients/detail/'.base64_encode(convert_uuencode(@$appointment['client_id'])));

        /*
        0=>Pending/Not confirmed,
        2=>Completed,
        4=>N/P,
        6=>Did Not Come,
        7=>Cancelled,
        8=>Missed,
        9=>Payment Pending,
        10=>Payment Success,
        11=>Payment Fail
        */

        if( $appointment['status'] == 0){ //0=>Pending/Not confirmed
			$row['backgroundColor'] = "#ff5722"; //Saffron
		} else if( $appointment['status'] == 2 ){ //2=>Completed
			$row['backgroundColor'] = "#4682B4"; //SteelBlue
		} else if( $appointment['status'] == 4 ){ //4=>N/P
			$row['backgroundColor'] = "#FFD700"; //Gold
		} else if( $appointment['status'] == 6 ){ //6=>Did Not Come
			$row['backgroundColor'] = "#8B0000"; //DarkRed
		} else if( $appointment['status'] == 7 ){ //7=>Cancelled
			$row['backgroundColor'] = "#B2BEB5"; //grey
		} else if( $appointment['status'] == 8 ){ //8=>Missed
			$row['backgroundColor'] = "#DC143C"; //Crimson
		} else if( $appointment['status'] == 9 ){ //9=>Payment Pending
			$row['backgroundColor'] = "#FF8C00"; //DarkOrange
		} else if( $appointment['status'] == 10 ){ //10=>Payment Success
			$row['backgroundColor'] = "#228B22"; //ForestGreen
		} else if( $appointment['status'] == 11 ){ //11=>Payment Fail
			$row['backgroundColor'] = "#B22222"; //FireBrick
		}
        // Use the same client name logic as above
        $row['full_name'] = trim($clientName);
        $row['timeslot_full'] = $appointment['timeslot_full'];

        if( isset($appointment['noe_id']) && $appointment['noe_id'] != "" ) {
            $row['noe_id'] = $appointment['noe_id'];
            if( isset($appointment['nature_of_enquiry']['title']) && $appointment['nature_of_enquiry']['title'] != '' ){
                $row['nature_of_enquiry'] = $appointment['nature_of_enquiry']['title'];
            } elseif (isset($appointment['nature_of_enquiry']) && is_string($appointment['nature_of_enquiry'])) {
                $row['nature_of_enquiry'] = $appointment['nature_of_enquiry'];
            } else {
                $row['nature_of_enquiry'] = "";
            }
        }

        if( isset($appointment['service_id']) && $appointment['service_id'] != "" ) {
            $row['service_id'] = $appointment['service_id'];
            if( isset($appointment['service']['title']) && $appointment['service']['title'] != ''  ){
                if( isset($appointment['service']['price']) && $appointment['service']['price'] == 'Free') {
                    $row['service'] = $appointment['service']['title'].' - '."Free";
                } else {
                    $row['service'] = $appointment['service']['title'].' - '."Paid";
                }
            } elseif (isset($appointment['service']) && is_string($appointment['service'])) {
                $row['service'] = $appointment['service'];
            } else {
                $row['service'] = "";
            }
        }

        if( isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'in_person' ) { //in_person
            $appointment_details = "In person";
        } else if( isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'phone' ) { //phone
            $appointment_details = "Phone";
        } else if( isset($appointment['appointment_details']) && $appointment['appointment_details'] == 'zoom_google_meeting' ) { //zoom_google_meeting
            $appointment_details = "Zoom";
        }
        $row['appointment_details'] = $appointment_details;

        if( isset($appointment['inperson_address']) && $appointment['inperson_address'] != '' ) {
            if( $appointment['inperson_address'] == 1 ) {
                $inperson_address = "ADELAIDE (Unit 5 5/55 Gawler Pl, Adelaide SA 5000)";
            }
            else if( $appointment['inperson_address'] == 2 ) {
                $inperson_address = "MELBOURNE (Next to flight Center, Level 8/278 Collins St, Melbourne VIC 3000, Australia)";
            }
        } else {
            $inperson_address = "";
        }
        $row['inperson_address'] = $inperson_address;


        if( isset($appointment['service_id']) && $appointment['service_id'] == 1 ) { //Paid
            $service_type = "Paid";
            if( isset($appointment['preferred_language']) && $appointment['preferred_language'] != '' ) {
                $row['appointment_other'] = $appointment_details.' - '.$service_type.' - '.$appointment['preferred_language'];
            } else {
                $row['appointment_other'] = $appointment_details.' - '.$service_type;
            }
        } else if( isset($appointment['service_id']) && $appointment['service_id'] == 2 ) { //Free
            if( isset($appointment['preferred_language']) && $appointment['preferred_language'] != '' ) {
                $row['appointment_other'] = $appointment_details.' - '.$appointment['preferred_language'];
            } else {
                $row['appointment_other'] = $appointment_details;
            }
        }
        $row['preferred_language'] = $appointment['preferred_language'];
        $sched_res[$appointment['id']] = $row;
    }
}
//dd($sched_res);
?>
@endsection
@push('scripts')
<script src="{{URL::asset('public/js/bootstrap-datepicker.js')}}"></script>
<script>
jQuery(document).ready(function($){
    $('.followup_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    $(document).delegate('.editfollowupdate', 'click', function(){
        $('.if_edit_followup').show();
        $('.editfollowupdate').hide();
    });
    $(document).delegate('.cancelfollowupdate', 'click', function(){
        $('.if_edit_followup').hide();
        $('.editfollowupdate').show();
    });

	$(document).delegate('#updateappointmentstatus', 'change', function(){
        var v = $('#updateappointmentstatus option:selected').val();
		var aid = $('#appid').val();
		window.location.href = '{{URL::to('/admin/updateappointmentstatus')}}/'+v+'/'+aid;
    });
});

function decodeHTMLEntities(str) {
    // Create a temporary textarea element to decode entities
    var textarea = document.createElement('textarea');

    // Decode once
    textarea.innerHTML = str;
    var decodedStr = textarea.value;

    // Decode again if the decoded string still contains entities (for double encoding)
    textarea.innerHTML = decodedStr;
    return textarea.value;
}

var events = [];
//var scheds = $.parseJSON('<?= json_encode($sched_res) ?>');
var scheds = $.parseJSON('<?= json_encode($sched_res, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>');
if (!!scheds) {
    Object.keys(scheds).map(k => {
        var row = scheds[k]
        events.push({ id: row.id, title: row.full_name+ '\n' + row.appointment_other, start: row.start, end: row.end, backgroundColor: row.backgroundColor });
    });
}
var today = new Date();
year = today.getFullYear();
month = today.getMonth();
day = today.getDate();
var calendar = $("#myEvent").fullCalendar({
  height: "auto",
  defaultView: "month",
  editable: false,
  selectable: true,
  displayEventTime: true,
  header: {
    left: "prev,next today",
    center: "title",
    right: "month,agendaWeek,agendaDay,listMonth",
  },
  events: events,
  timeFormat: 'h:mm A',
  eventClick: function(info) {
		//console.log(info);
        var details = $('#event-details-modal');
        var id = info.id;
        if (!!scheds[id]) {
            details.find('#service').text(scheds[id].service);

            details.find('#appointment_details').text(scheds[id].appointment_details);
            details.find('#inperson_address').text(scheds[id].inperson_address);
            details.find('#preferred_language').text(scheds[id].preferred_language);

            details.find('#nature_of_enquiry').text(scheds[id].nature_of_enquiry);
            details.find('#timeslot_full').text(scheds[id].timeslot_full);
            details.find('#appointment_id').val(id);

            details.find('#followup_date').val(scheds[id].appointdate);
            details.find('#followup_time').val(scheds[id].appointtime);

            details.find('#start').html(scheds[id].start+' <a href="javascript:;" class="editfollowupdate"><i class="fa fa-edit"></i> Edit</a>');
            if (scheds[id].url) {
                //window.open(scheds[id].url, "_blank");
                //return false;
            }
            var csel = '';
            if(scheds[id].status == '2'){
                var csel2 = 'selected';
            }else if(scheds[id].status == '4'){
                var csel4 = 'selected';
            } else if(scheds[id].status == '6'){
                var csel6 = 'selected';
            } else if(scheds[id].status == '7'){
                var csel7 = 'selected';
            } else if(scheds[id].status == '8'){
                var csel8 = 'selected';
            } else if(scheds[id].status == '9'){
                var csel9 = 'selected';
            } else if(scheds[id].status == '10'){
                var csel10 = 'selected';
            } else if(scheds[id].status == '11'){
                var csel11 = 'selected';
            }
            details.find('.clienturl').html('<div class="row"><div class="col-md-6"><a target="_blank" class="btn btn-outline-primary btn-sm" style="" href="'+scheds[id].url+'">'+atob(scheds[id].name)+' '+scheds[id].stitle+'</a> </div><div class="col-md-6" style="text-align: right;"><select class="" id="updateappointmentstatus"><option value="0">Pending/Not confirmed</option><option value="2" '+csel2+'>Completed</option><option value="4" '+csel4+'>N/P</option><option value="6" '+csel6+'>Did Not Come</option><option value="7" '+csel7+'>Cancelled</option><option value="8" '+csel8+'>Missed</option><option value="9" '+csel9+'>Payment Pending</option><option value="10" '+csel10+'>Payment Success</option><option value="11" '+csel11+'>Payment Failed</option></select><input type="hidden" id="appid" value="'+id+'"></div>');
            details.modal('show');
        } else {
            alert("Event is undefined");
        }
    }
});
</script>
<style>
/* Align event text to the left in day and week views */
.fc-time-grid-event .fc-content {
    text-align: left;
    padding-left: 5px; /* Optional padding */
}
</style>
    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header rounded-0">
                    <h5 class="modal-title">Schedule Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body rounded-0">

                    <div class="container-fluid">
                        <div class="clienturl"></div>
                        <dl>
                            <dt class="text-muted" style="color: #061d28 !important;">Nature of Enquiry</dt>
                            <dd id="nature_of_enquiry" class="fw-bold fs-4"></dd>

                            <dt class="text-muted" style="color: #061d28 !important;">Services</dt>
                            <dd id="service" class="fw-bold fs-4"></dd>

                            <dt class="text-muted" style="color: #061d28 !important;">Appointment Details</dt>
                            <dd id="appointment_details" class="fw-bold fs-4"></dd>

                            <dt class="text-muted" style="color: #061d28 !important;">Location</dt>
                            <dd id="inperson_address" class="fw-bold fs-4"></dd>

                            <dt class="text-muted" style="color: #061d28 !important;">Preferred Language</dt>
                            <dd id="preferred_language" class="fw-bold fs-4"></dd>

                            <dt class="text-muted" style="color: #061d28 !important;">Date</dt>
                            <dd id="start" class=""></dd>

                            <dt class="text-muted" style="color: #061d28 !important;"> Booked Slot</dt>
                            <dd id="timeslot_full" class=""></dd>
                        </dl>
                        <div class="if_edit_followup" style="display:none">
                            <form method="post" action="{{URL::to('/admin/updatefollowupschedule')}}" name="updatefollowupschedule" id="updatefollowupschedule" autocomplete="off" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="appointment_id" id="appointment_id">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" name="followup_date" id="followup_date" class="form-control  followup_date">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Time</label>
                                            <input type="time" name="followup_time" id="followup_time" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea class="form-control" name="edit_description" id="edit_description"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-primary" onclick="customValidate('updatefollowupschedule')">Save</button>
                                        <a href="javascript:;" class="btn btn-info cancelfollowupdate" >Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

	<script>
        document.getElementById('followup_time').addEventListener('input', function () {
            let time = this.value.split(':');
            let hours = parseInt(time[0]);
            let minutes = parseInt(time[1]);

            // Round the minutes to the nearest 15-minute increment
            let roundedMinutes = Math.round(minutes / 15) * 15;

            // Handle the edge case where rounding exceeds 60 minutes
            if (roundedMinutes === 60) {
                roundedMinutes = 0;
                hours = (hours + 1) % 24;  // Wrap around the hours if necessary
            }

            // Format the hours and minutes with leading zeros if needed
            this.value = String(hours).padStart(2, '0') + ':' + String(roundedMinutes).padStart(2, '0');
        });
    </script>
@endpush

@extends('layouts.admin')
@section('title', 'Edit Not Available Appointment Dates')

@section('content')
<!-- Main Content -->
<style>
.date {
    max-width: 330px;
    font-size: 14px;
    line-height: 21px;
    margin: 0px auto;
    background: #d3d4ec;
    padding: 8px;
    border-radius: 5px;
}
</style>
<div class="main-content">
	<section class="section">
		<div class="section-body">
			<form action="{{ url('admin/appointment-dates-disable/edit') }}" name="edit-partnertype" autocomplete="off" enctype="multipart/form-data" method="POST">
				@csrf
				<input type="hidden" name="id" value="{{ @$fetchedData->id }}">
				<div class="row">
					<div class="col-12 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header">
								<h4>Edit Not Available Appointment Dates</h4>
								<div class="card-header-action">
									<a href="{{route('admin.feature.appointmentdisabledate.index')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Back</a>
								</div>
							</div>
						</div>
					</div>
					 <div class="col-3 col-md-3 col-lg-3">
			        	@include('../Elements/Admin/setting')
    		        </div>
    				<div class="col-9 col-md-9 col-lg-9">
						<div class="card">
							<div class="card-body">
								<div id="accordion">
									<div class="accordion">
										<h4 style="background-color: #6777ef;color: #fff;font-size: 14px;font-weight: 700;padding: 10px 10px 10px 10px;">
                                                <?php
                                                if(isset($fetchedData->title)){
                                                    $title = "<b>Service Title - ".$fetchedData->title."</b>";
                                                    if($fetchedData->id == 1){
                                                        $title .= " (".'User1'.")";
                                                    } else if($fetchedData->id == 2 || $fetchedData->id == 6){
                                                        $title .= " (".'User2'.")";
                                                    }
                                                    $title .=  "<br/>Daily Timings - ".date('H:i',strtotime($fetchedData->start_time))."-".date('H:i',strtotime($fetchedData->end_time));
                                                    $title .=  "<br/>Weekend - ".$fetchedData->weekend;
                                                    echo  $title;
                                                }?>
                                            </h4>

										<div class="accordion-body collapse show" id="primary_info" data-parent="#accordion">
											<div class="row">
												<div class="col-12 col-md-4 col-lg-4">
													<div class="form-group">
														<label for="disabledates">Not Available Dates </label>
														<input type="text" class="form-control date" name="disabledates"/>
                                                        @if ($errors->has('disabledates'))
															<span class="custom-error" role="alert">
																<strong>{{ @$errors->first('disabledates') }}</strong>
															</span>
														@endif
													</div>
												</div>
                                            </div>
										</div>
									</div>
								</div>
								<div class="form-group float-right">
									<button type="submit" class="btn btn-primary">Update</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script>
jQuery(document).ready(function($){
    var daysOfWeek = <?php echo json_encode($weekendd);?>;
    var disabledatesF = <?php echo json_encode($disabledatesF); ?>;
    $('.date').datepicker({
        inline: true,
        startDate: new Date(),
        daysOfWeekDisabled: daysOfWeek,
        format: 'dd/mm/yyyy',
        multidate: true
    })

    $('.date').datepicker('setDate', disabledatesF)
    //$('.date').datepicker('setDates', [new Date(2024, 2, 22), new Date(2024, 2, 25)])
});
</script>
@endsection


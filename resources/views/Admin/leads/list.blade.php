<?php use App\Http\Controllers\Admin\FollowupController; ?>
@if(@$totalData !== 0)
	@foreach (@$lists as $list)
		<?php $strtime = strtotime($list->created_at); ?>
		@if($list->followup_type == 'mail_compose')
			<div> 
				<i class="fas fa-envelope bg-success"></i>
				<div class="timeline-item" style="padding: 10px;">
					<span class="time"><i class="far fa-clock"></i><?php FollowupController::time_Ago($strtime); ?> @if($list->pin == 1)<a href="{{URL::to('/admin/leads/pin/'.$list->id)}}"><i style="color:red;" class="fa fa-thumbtack"></i></a>@else<a href="{{URL::to('/admin/leads/pin/'.$list->id)}}"><i style="color:#808080;" class="fa fa-thumbtack"></i></a> @endif</span>
					<span class="name">{{@$list->user->first_name}} {{@$list->user->last_name}}</span> <a  onclick="return confirm('Are you sure?')" style="color:#808080;" href="{{URL::to('/admin/leads/notes/delete/'.$list->id)}}"><i style="color:#6777ef;"  class="fa fa-trash"></i></a>
					<h3 class="timeline-header"><a href="#">Mail Activity</a> Mail Sent <i class="fa fa-share"></i></h3>
					<div class="timeline-body">
						Subject: {{$list->subject}} 
						<br>
						<p><?php echo htmlspecialchars_decode(stripslashes(@$list->note)); ?></p>
					</div>
					<div class="modal fade" id="mymodel{{$list->id}}">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Message</h4>
									<button type="button" class="close" data-dismiss="modal">&times;</button>
								</div>
								<div class="modal-body">
									<div class="form-group row">
										<div class="col-sm-12">
											<label for="email_to" class="col-form-label col-sm-2">Subject</label>
											<div class="col-sm-10">
												<p>{{$list->subject}}</p>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-sm-12">
											<label for="email_to" class="col-form-label col-sm-2">Message</label>
											<div class="col-sm-10">
												<p>{!!$list->note!!}</p>
											</div>
										</div>
									</div>
									<?php $attmet = \App\Models\Attachment::where('leade_id',$list->id)->get(); ?>
									@foreach(@$attmet as $al)
									<div class="form-group row">
									<div class="col-sm-12">
									<label for="email_to" class="col-form-label col-sm-2">Attachments</label>
										<div class="col-sm-10">
											<a download href="{{URL::to('public/img/attacment_file/')}}/{{$al->file}}" target="_blank">{{@$al->file}}</a>
										</div>
										</div>
									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@else	
		<div>
			<i class="fas fa-{{FollowupController::followuptype($list->followup_type,'icon')}} {{FollowupController::followuptype($list->followup_type,'color')}}"></i>
			<div class="timeline-item" style="padding: 10px;">
			    
				<span class="time"><i style="color:#808080;" class="far fa-clock"></i> <?php FollowupController::time_Ago($strtime); ?> @if($list->pin == 1)<a href="{{URL::to('/admin/leads/pin/'.$list->id)}}"><i style="color:red;" class="fa fa-thumbtack"></i></a>@else<a href="{{URL::to('/admin/leads/pin/'.$list->id)}}"><i style="color:#808080;" class="fa fa-thumbtack"></i></a> @endif</span>
				<span class="name">{{@$list->user->first_name}} {{@$list->user->last_name}}</span> @if($list->followup_type != 'mail_compose' && $list->followup_type != 'follow_up')<a href="javascript:;" class="editnote" data-id={{$list->id}}><i style="color:#6777ef;"  class="fa fa-edit" ></i></a> @endif | <a  onclick="return confirm('Are you sure?')" style="color:#808080;" href="{{URL::to('/admin/leads/notes/delete/'.$list->id)}}"><i style="color:#6777ef;"  class="fa fa-trash"></i></a>
				@if($list->followup_type == "follow_up")
					<h3 class="timeline-header"><a href="#">{{FollowupController::followuptype($list->followup_type,'name')}}</a> set for {{date('d-m-Y h:i A', strtotime($list->followup_date))}}  @if($list->rem_cat == 1) regardless @else no reply @endif     </h3>
				@else
					<h3 class="timeline-header"><a href="#">{{FollowupController::followuptype($list->followup_type,'name')}}</a></h3>
			    @endif
			    @if($list->subject != "")
				<h3 class="timeline-header">Subject {{$list->subject}}</h3>
				@endif
				@if($list->note != "")
				<p class="timeline-header">{!!$list->note!!}</p>
				@endif
			</div>
		</div>
		@endif
	@endforeach

@else
	<div> 
		<div class="timeline-item">
			<h3 class="timeline-header">History not found</h3>
		</div>
	</div>
@endif 

<!-- 
<div>
	<i class="fas fa-comments bg-info"></i>
	<div class="timeline-item">
		<span class="time"><i class="far fa-clock"></i> 5 mins ago</span>
		<span class="name">Suran Singh</span>
		<h3 class="timeline-header"><a href="#">Wrong Number</a></h3>
		<h3 class="timeline-header border-0"></h3>
	</div>
</div>

<div>
	<i class="fas fa-phone bg-warning"></i>
	<div class="timeline-item">
		<span class="time"><i class="far fa-clock"></i> 27 mins ago</span>
		<span class="name">Ritesh Kumar - 8444710000</span>
		<h3 class="timeline-header"><a href="#">Call Received</a></h3>
		<div class="timeline-body">
			Received call from Punitraina38. <a href="#">Listen to call here <i class="fa fa-download"></i></a>
		</div>
	</div>
</div>

<div>
	<i class="fas fa-tag bg-info"></i>
	<div class="timeline-item">
		<span class="time"><i class="far fa-clock"></i> 27 Feb, 2020</span>
		<span class="name">Suran Singh</span>
		<div class="timeline-body">
			lead for 0 credits via Hellotravel
		</div>
	</div>
</div>bg-danger

<div> 
	<i class="fas fa-envelope bg-success"></i>
	<div class="timeline-item">
		<span class="time"><i class="far fa-clock"></i> 27 Feb, 2020</span>
		<span class="name">Ritesh Kumar - 8444710000</span>
		<h3 class="timeline-header"><a href="#">Mail Activity</a> Mail Received <i class="fa fa-share"></i></h3>
		<div class="timeline-body">
			Subject: Message from Punitraina38<br/>Hello, Please send me details<br>From: Punitraina38
		</div>
	</div>
</div>

<div>
	<i class="fas fa-tag bg-info"></i>
	<div class="timeline-item">
		<span class="time"><i class="far fa-clock"></i> 27 Feb, 2020</span>
		<span class="name">Ritesh Kumar - 8444710000</span>
		<h3 class="timeline-header"><a href="#">Assigned To</a> changed from Ritesh Kumar - 8444710000 to Suran Singh</h3>
	</div>
</div>-->
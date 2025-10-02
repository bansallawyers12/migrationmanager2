@extends('layouts.emailmanager')
@section('title', 'Sent')

@section('content')

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
							<h4>Sent</h4>
							<div class="card-header-action">
							</div>
						</div>
						<div class="card-body">
                            <div class="tab-content" id="clientContent">
						        <div class="tab-pane fade show active" id="clients" role="tabpanel" aria-labelledby="clients-tab">
									<div class="table-responsive common_table client_table_data">
										<table class="table text_wrap">
											<thead>
												<tr>
												    <th>Date</th>
                                                    <th>To</th>
                                                    <th>Subject</th>
													<th>Assign Client Id</th>
                                                    <th>Assign Client Matter</th>
                                                    <th>Action</th>
												</tr>
											</thead>

											<tbody class="tdata">
											    <?php
                                                $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                ?>
												@if(@$totalData !== 0)
													<?php $i=0; ?>
												@foreach (@$lists as $list)
                                                <?php
												$assign_user_info = \App\Models\Admin::select('first_name','last_name','client_id')->where('id', $list->assign_client_id)->first();
												if($assign_user_info){
                                                    $assign_full_name = '<b style="color: limegreen;">'.$assign_user_info->first_name.' '.$assign_user_info->last_name.'('.$assign_user_info->client_id.')</b>';
                                                } else {
                                                    $assign_full_name = 'NA';
                                                }

                                                $client_matter_info = \App\Models\ClientMatter::join('matters', 'client_matters.sel_matter_id', '=', 'matters.id')
                                                ->select('client_matters.id', 'matters.title','client_matters.client_unique_matter_no')
                                                ->where('client_matters.id', $list->assign_client_matter_id)
                                                ->first(); //dd($client_matter_info);
                                                if($client_matter_info){
                                                    $client_matter_name = '<b style="color: limegreen;">'.$client_matter_info->title.' ('.$client_matter_info->client_unique_matter_no.')</b>';
                                                } else {
                                                    $client_matter_name = 'NA';
                                                }
                                                ?>
												<tr id="id_{{@$list->id}}">
                                                    <td style="white-space: initial;">
                                                        {{ date('d/m/Y h:i:s a', strtotime($list->date_email)) }}
                                                    </td>
                                                    <td style="white-space: initial;">
                                                        {{ @$list->to_email == "" ? config('constants.empty') : @$list->to_email }}
                                                    </td>

													<td style="white-space: initial;">
                                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$list->from_email.'/sent/'.$list->msgno.'.pdf'; ?>">
                                                            {{ @$list->subject == "" ? config('constants.empty') : @$list->subject }}
                                                        </a>
                                                    </td>
													<td style="white-space: initial;"><?php echo $assign_full_name;?></td>
                                                    <td style="white-space: initial;"><?php echo $client_matter_name;?></td>
                                                    <td><a class="dropdown-item has-icon btn btn-primary sent_assignemail_modal" href="javascript:;" memail_id="{{@$list->id}}" user_mail="{{@$list->from_email}}">Assign</a></td>
												</tr>
												<?php $i++; ?>
												@endforeach
											</tbody>
											@else
											<tbody>
												<tr>
													<td style="text-align:center;" colspan="17">
														No Record found
													</td>
												</tr>
											</tbody>
											@endif
										</table>
									</div>
								</div>

							</div>
						</div>
						<div class="card-footer">
							{!! $lists->appends(\Request::except('page'))->render() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade" id="sent_assignemail_modal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				  <h4 class="modal-title">Assign Sent Email</h4>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
			</div>
			{!! html()->form('POST', url('email_users/assigsentemail'))->attribute('name', 'sent-email-assign-to-client-matter')->attribute('autocomplete', 'off')->attribute('enctype', 'multipart/form-data')->attribute('id', 'sent-email-assign-to-client-matter')->open() !!}
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-sm-12">
                        <input id="memail_id" name="memail_id" type="hidden" value="">
                        <input id="mail_type" name="mail_type" type="hidden" value="sent">
                        <input id="user_mail" name="user_mail" type="hidden" value="">

						<select id="assign_client_id" name="assign_client_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" data-valid="required">
							<option value="">Select Client</option>
							@foreach(\App\Models\Admin::Where('role','7')->Where('type','client')->get() as $ulist)
							<option value="{{@$ulist->id}}">{{@$ulist->first_name}} {{@$ulist->last_name}}({{@$ulist->client_id}})</option>
							@endforeach
						</select>
					</div>
				</div>

                <div class="form-group row">
					<div class="col-sm-12">
						<select id="assign_client_matter_id" name="assign_client_matter_id" class="form-control select2 " style="width: 100%;" data-select2-id="1" tabindex="-1" aria-hidden="true" disabled>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! html()->button('<i class="fa fa-save"></i> Assign User')->class('btn btn-primary')->attribute('onClick', 'customValidate("sent-email-assign-to-client-matter")') !!}
			</div>
			{!! html()->closeModel('form') !!}
		</div>
	</div>
</div>

@endsection
@section('scripts')

<script>

$(document).ready(function() {

    $('.sent_assignemail_modal').on('click', function(){
        var val = $(this).attr('memail_id');
        $('#sent_assignemail_modal #memail_id').val(val);

        var user_mail = $(this).attr('user_mail');
        $('#sent_assignemail_modal #user_mail').val(user_mail);

        $('#sent_assignemail_modal').modal('show');
    });

    //Initialize both Select2 dropdowns
    $('#assign_client_id').select2();
    $('#assign_client_matter_id').select2();

    $(document).delegate('#assign_client_id', 'change', function(){
        let selected_client_id = $(this).val();
        //console.log('selected_client_id='+selected_client_id);

        if (selected_client_id != "") {
            $('.popuploader').show();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('email_users.listAllMattersWRTSelClient')}}",
                method: "POST",
                data: {client_id:selected_client_id},
                datatype: 'json',
                success: function(response) {
                    $('.popuploader').hide();
                    var obj = $.parseJSON(response); //console.log(obj.clientMatetrs);
                    var matterlist = '<option value="">Select Client Matter</option>';
                    $.each(obj.clientMatetrs, function(index, subArray) {
                        matterlist += '<option value="'+subArray.id+'">'+subArray.title+'('+subArray.client_unique_matter_no+')</option>';
                    });
                    $('#assign_client_matter_id').html(matterlist);
                }
            });
            $('#assign_client_matter_id').prop('disabled', false).select2();
        } else {
            $('#assign_client_matter_id').prop('disabled', true).select2();
        }
    });
});
</script>
@endsection

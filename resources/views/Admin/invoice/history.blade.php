<div class="form-group row">
<textarea class="form-control" id="comment" name="comment" style="width: 100%; height:80px;padding: 10px;margin-bottom:10px;"></textarea>
</div>

<div class="form-group row">
 <button type="button" id="add_note" invoiceid="{{$invoiceid}}" class="btn btn-primary">Add Comment</button>
</div>
<?php use App\Http\Controllers\Admin\FollowupController; ?>
<div class="timeline timeline-inverse mydivlist">
@if(@$totalcount !== 0)
	@foreach (@$fetchedData as $list)
		<?php $strtime = date('d/m/Y h:i a', strtotime($list->created_at)); ?>
		
		<div> 
	<i class="fas fa-{{FollowupController::followuptype($list->followup_type,'icon')}} {{FollowupController::followuptype($list->followup_type,'color')}}"></i>
	<div class="timeline-item">
		<span class="name">{{@$list->user->first_name}} {{@$list->user->last_name}}</span>
		<h3 class="timeline-header"><i class="far fa-clock"></i> {{$strtime}} </h3>
		<div class="timeline-body">
		<?php
		$Invoicedata = \App\Models\Invoice::where('id',$list->invoice_id)->first();
		$currencydata = \App\Models\Currency::where('id',$Invoicedata->currency_id)->first();
		 $replacesub = array('{currency}');					
		$replace_with_sub = array($currencydata->currency_symbol);
		$subContent= $list->comment;
		$subContent	=	str_replace($replacesub,$replace_with_sub,$subContent);
		echo htmlspecialchars_decode(stripslashes($subContent));
		?>
			
		</div>
	</div>
</div>

	@endforeach
@else
	<div> 
		<div class="timeline-item">
			<h3 class="timeline-header">History not found</h3>
		</div>
	</div>
@endif 

</div>
function print() {
        var objFra = document.getElementById('myFrame');
        objFra.contentWindow.focus();
        objFra.contentWindow.print();
    }
	
	function myfollowuplist(lead_id) {
	$.ajax({
		type:'get',
		url: followuplist + '/admin/followup/list',
		data: {leadid:lead_id},
		success: function(response){
			$('.followuphistory').html(response);
		}
	});
}

function deleteAllAction(table){
	var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
	if(conf){
		var checkboxValues = [];
		$('input[name=allcheckbox]:checked').map(function() {
					checkboxValues.push($(this).val());
		});
		
		if(checkboxValues.length === 0) {
			alert('Please select ID to delete the record.');
			return false;
		}else{
			$(".server-error").html(''); //remove server error.
			$(".custom-error-msg").html(''); //remove custom error.
			$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_all_action',
					data:{'id[]': checkboxValues, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
								setTimeout(function(){
								window.location.reload();
								}, 1000)  
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
		}	
	}else{
			$("#loader").hide();
		}	
}
//Delete Function Start
	function deleteAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	
	function deleteDesAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_dest_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	
	function deleteHotelAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_hotel_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	
	function deleteInclusionAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_inclusion_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	
	function deleteEnclusionAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_exclusions_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	function deleteTopAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_top_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	
	function deleteTypeAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_type_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
	 
	function deletePackageAction( id, table ) {
		var conf = confirm('Are you sure, you would like to delete this record. Remember all Related data would be deleted.');
		if(conf){	 
			if(id == '') {
				alert('Please select ID to delete the record.');
				return false;	
			} else {
				$(".server-error").html(''); //remove server error.
				$(".custom-error-msg").html(''); //remove custom error.
				$.ajax({
					type:'post',
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					url:site_url+'/admin/delete_package_action',
					data:{'id': id, 'table' : table},
					success:function(resp) {
						var obj = $.parseJSON(resp);
						if(obj.status == 1) {
							$("#id_"+id).remove();
							//show success msg 
								var html = successMessage(obj.message);
								$(".custom-error-msg").html(html);
							
							//show count
								var old_count = $(".count").text();
								var new_count = old_count - 1;
								$(".count").text(new_count);
							
							//when all data has been deleted
								if(new_count == 0){
									$(".tdata").html('<tr><td colspan="6">There are no data in this table.</td></tr>');
								}
							
						} else{
							var html = errorMessage(obj.message);
							$(".custom-error-msg").html(html);
						}
						$("#loader").hide();
					},
					beforeSend: function() {
						$("#loader").show();
					}
				});
				$('html, body').animate({scrollTop:0}, 'slow');
			}
		} else{
			$("#loader").hide();
		}
	}
//Delete Function End


function successMessage(msg){
	var html = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
	html +=	'<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>';
	html += '<strong>'+msg+'</strong>';
	html += '</div>';
	
	return html;
}
function errorMessage(msg){
	var html = '<div class="alert alert-danger alert-dismissible fade show">';
	html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>'+msg;	
	html += '</div>';
	
	return html;	
}

jQuery(document).ready(function($){
	
	$('#customer_name').on("select2:select", function(e) {
	var customer_id = $('#customer_name option:selected').val();
	if(customer_id != ''){
		$.ajax({
			type: 'get',
			url: site_url+'/admin/get_customer_detail',
			data: {'customer_id':customer_id},
			success: function(resp){
				var obj = $.parseJSON(resp);
				if(obj.success){
					$('.addresslist').show();
					if(obj.contactdetail.country  != null){
						$('.addresslist').show();
						$('.addresslist').css('margin-bottom','20px');
						$('.addresslist').html('<span style="">Billing Address <a href="javascript:;" id="'+customer_id+'" class="change_address">Change</a></span><address style="" id="">'+obj.contactdetail.address+'<br>'+obj.contactdetail.city+'<br>'+obj.contactdetail.zipcode+'<br>'+obj.contactdetail.country+'<br>'+obj.contactdetail.phone+'</address>'); 
						
						}else{
							
							$('.addresslist').show();
							$('.addresslist').css('margin-bottom','20px');
							$('.addresslist').html('<span style="">Billing Address <a href="javascript:;" id="'+obj.contactdetail.id +'" class="change_address">Add Address</a></span>');  
						}
						$('.currencydata').html('<span class="badge badge-success text-semibold currencycode">'+obj.contactdetail.currencydata.currency_code+'</span><input type="hidden" name="currency_id" value="'+obj.contactdetail.currencydata.id+'"><input type="hidden" id="dec_type" value="'+obj.contactdetail.currencydata.decimal+'">');
						$('.currecysign').html(obj.contactdetail.currencydata.currency_symbol);
						/* if(obj.contactdetail.currencydata.decimal == 2){
							$('.amount').val('0.00');
							$('.rate').val('0.00');
						$('.subtotal').html('0.00');
						$('.subtotal').html('0.00');
						$('.finaltotal').html('0.00');
						}else if(obj.contactdetail.currencydata.decimal == 3){
							$('.amount').val('0.000');
							$('.rate').val('0.000');
						$('.subtotal').html('0.000');
						$('.finaltotal').html('0.000');
						}else{
							$('.amount').val('0');
							$('.rate').val('0');
						$('.subtotal').html('0');
						$('.finaltotal').html('0');
						} */
						
					 
					billingdata[0] = {
					"customer_id":customer_id,
					"address":obj.contactdetail.address,
					"city":obj.contactdetail.city,
					"zipcode":obj.contactdetail.zipcode,
					"phone":obj.contactdetail.phone,
					
					"country":obj.contactdetail.country,
					"currencycode":obj.contactdetail.currencydata.currency_code,
					
				}
				}else{
					$('.currencydata').html('');
					$('.currecysign').html('₹');
					$('.addresslist').show();
					$('.addresslist').html(obj.message);
				}
			}
		}); 
	}	
	});    

	$(document).delegate('.rate', 'blur', function(){
		  var item_quantity = parseFloat($(this).parents("tr").find(".qty").val());
        var unit_price = parseFloat($(this).parents("tr").find(".rate").val());
        var total_value = unit_price * item_quantity;
       
        if(!isNaN(total_value)){
            $(this).parents("tr").find(".amount").val(total_value.toFixed($('#dec_type').val()));
		}
		calculatefinaltotal();
	});
	
	$(document).delegate('.selectdiscount', 'click', function(){
		$('.disc_type').val($(this).attr('dataid'));
		$('.showdiscounttype').html($(this).text()+' <span class="caret"></span>');
		calculatefinaltotal();
	});
	$(document).delegate('.discount', 'blur', function(){
		calculatefinaltotal();
		
	});
	$(document).delegate('.qty', 'blur', function(){
		  var item_quantity = parseFloat($(this).parents("tr").find(".qty").val());
        var unit_price = parseFloat($(this).parents("tr").find(".rate").val());
        var total_value = unit_price * item_quantity;
       
        if(!isNaN(total_value))
            $(this).parents("tr").find(".amount").val(total_value.toFixed($('#dec_type').val()));
		
		calculatefinaltotal();
	});
	
	$(document).delegate('input[name=tax]', 'change', function(){
		calculatefinaltotal();
	});
function calculatefinaltotal(){
	 var item_quantity = 0;
	 var unit_price = 0;
	 var total_value = 0;
	 var subtotal = 0;
	 var finaltotal = 0.00;
	
	$('.tr_clone').each(function(){
		  item_quantity = parseFloat($(this).find(".qty").val());
         unit_price = parseFloat($(this).find(".rate").val());
		 total_value = unit_price * item_quantity;
         subtotal += total_value;
		 console.log(unit_price);
	});
	var tax = $("input[name='tax']:checked"). val();
	
	
	var discount = $('.discount').val();
	var discounttype = $('.disc_type').val();
	if(discounttype == 'fixed'){
		
		var per = discount;
		finaltotal = subtotal - parseFloat(discount);
		 
	}else {
		console.log('dsdds'); 
		var per = (subtotal * discount) / 100;
		finaltotal = subtotal - per;
	}
	console.log(finaltotal);
	if(tax != 0){
		console.log(tax);
		var taxname = $("input[name='tax']:checked"). attr('ratename');
		var taxrate = $("input[name='tax']:checked"). attr('ratetax');
		var taxcal = (finaltotal * taxrate) / 100;
		$('.taxdetail').show();
		$('.taxdetail .taxname').html(taxname+' ['+taxrate+']%');
		$('.taxdetail .taxprice').html(taxcal.toFixed($('#dec_type').val()));
	}else{
		$('.taxdetail').hide();
		$('.taxdetail .taxname').html('');
		var taxcal = 0;
	}
	
	finaltotal = finaltotal + parseFloat(taxcal);
	
	$('.subtotal').html(subtotal.toFixed($('#dec_type').val()));
	 
	$('.discountsho').html(per);
	console.log(finaltotal+'sddd');
	$('.finaltotal').html(finaltotal.toFixed($('#dec_type').val()));
}
	
		$(document).delegate('#add_note', "click", function () {
			if($('#comment').val() != ''){
				$("#loader").show();
				$.ajax({
					type: 'post',
					url:site_url+'/admin/invoice/addcomment',
					data:{ivoiceid:$(this).attr('invoiceid'),comment:$('#comment').val()},
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					success: function(resp){
						$("#loader").hide();
						var obj = $.parseJSON(resp);
						if(obj.success){
							var html = obj.followup;
							$('.mydivlist').prepend(html);
						}else{
							
						}
					}
				});
			}
		});
		 $('input[type=file]#attach_file').on("change", function(){
	  
		ajaxCoverFileUpload();
	});
	function ajaxCoverFileUpload(){
		$('#loader').show();
		var fd = new FormData();
        var files = $('#attach_file')[0].files[0];
		let size = $('#attach_file')[0].files[0].size; // this is in bytes
    if (size > 5000000) {
        alert("You can upload a maximum of 5MB files");
    }else{
        fd.append('image',files);
        fd.append('invoice_id',$('#attach_file_id').val());
		$.ajax({
			url: site_url+'/admin/invoice/attachfile',
			type: 'POST',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			processData: false,
			contentType: false,
			data: fd,
			success: function(response){
				$('#loader').hide();
				var obj = $.parseJSON(response);
				if(obj.success){
					var html = '';
					$(obj.files).each(function(key, val){
						  html += '<tr><td><img style= "width:64px;" src="'+site_url+'/public/img/img_148071.png"><i</td><td>'+val.name+'</td><td><a class="coll removeattac" href="javascript:;" data-ember-action="" data-at-id="'+val.id+'">Remove</a></td></tr>';
					});
					
					 $("#allfiles").html(html); 
                   
				}else{
                    alert(obj.message);
                }
			}
	}); 
	}
	}
		$(document).delegate('.removeattac', "click", function () {
			$("#loader").show();
			$.ajax({
				type:'get',
				url:site_url+'/admin/invoice/removeattachfile',
				data:{invoice_id:$(this).attr('data-at-id')},
				success:function(res){
					$('#loader').hide();
					var obj = $.parseJSON(res);
					if(obj.success){
						var html = '';
					$(obj.files).each(function(key, val){
						console.log(val);
						 html += '<tr><td><img style= "width:64px;" src="'+site_url+'/public/img/img_148071.png"><i</td><td>'+val.name+'</td><td><a class="coll removeattac" href="javascript:;" data-ember-action="" data-at-id="'+val.id+'">Remove</a></td></tr>';
					});
					
					 $("#allfiles").html(html); 
					}else{
						alert('not found');
					}
				}
			});
		});
		
		$('.display_attach').on('switchChange.bootstrapSwitch', function (event, state) {
			$("#loader").show();
			$.ajax({
				type:'get',
				url:site_url+'/admin/invoice/attachfileemail',
				data:{invoice_id:$(this).attr('data-id')},
				success:function(res){
					$('#loader').hide();
					var obj = $.parseJSON(res);
					if(obj.success){
						
					}else{
						alert('not found');
					}
				}
			});
		});
		$(document).delegate('.removeattacmet', "click", function () {
			var val = $(this).attr('id');
			$('#rem_'+val).remove();
			
			if ( $('.attacfilel ul li').length == 0 ) {
				$('.attacfilel').remove();
			}
		});
		$(document).delegate('.attach_filemodel', "click", function () {
			var val = $(this).attr('dataid');
			$('#attach_filemodel').modal('show');
			$('.display_attach').attr('data-id',val);
			$('#attach_file_id').val(val);
			$("#loader").show();
			$.ajax({
				type:'get',
				url:site_url+'/admin/invoice/getattachfile',
				data:{invoice_id:val},
				success:function(res){
					$('#loader').hide();
				var obj = $.parseJSON(res);
				if(obj.success){
					var html = '';
					$(obj.files).each(function(key, val){
						console.log(val);
						 html += '<tr><td><img style= "width:64px;" src="'+site_url+'/public/img/img_148071.png"><i</td><td>'+val.name+'</td><td><a class="coll removeattac" href="javascript:;" data-ember-action="" data-at-id="'+val.id+'">Remove</a></td></tr>';
					});
					
					if(obj.state == 1){
						
					$('.display_attach').bootstrapSwitch('state', true);
					}else{
						$('.display_attach').bootstrapSwitch('state', false);
					}
					 $("#allfiles").html(html); 
                   
				}else{
                    alert(obj.message);
                }
					
				}
			});
         
		});
		$(document).delegate('.print_invoice', "click", function () {
			var val = $(this).attr('dataid');
			$('#pdfmodel').modal('show');
		
					 $("#pdfmodel .modal-body iframe").attr('src', site_url+'/admin/invoice/print/'+val) // create an iframe
         
		});
		$(document).delegate('.closepri', "click", function () {
			$('#pdfmodel').modal('hide');
			var uri = window.location.toString();
if (uri.indexOf("?") > 0) {
    var clean_uri = uri.substring(0, uri.indexOf("?"));
    window.history.replaceState({}, document.title, clean_uri);
}
         
		});
		$(document).delegate('.commenthistory', "click", function () {
			var val = $(this).attr('dataid');
			$('#commenthistorymodel').modal('show');
			$("#loader").show();
			$.ajax({
				type:'get',
				url:site_url+'/admin/invoice/history',
				data:{ivoiceid:val},
				success:function(res){
					$("#loader").hide();
					$('#commenthistorymodel .modal-body').html(res);
					
				}
			});
		});
		$(document).delegate('#edit_payment_save', "click", function () {
		$('.custom-error').remove();
		
		var fd = new FormData(document.getElementById('editpaymentsave'));
		$.ajax({
				type:'post',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url:site_url+'/admin/invoice/editpaymentsave',
				contentType: false,
				processData: false,
				data:fd,
				success:function(resp) {
					var obj = $.parseJSON(resp);
					if(obj.success){
						 location.reload();
							
					}else{
						$('.customerror').html("<span class='custom-error' role='alert'>"+obj.message+"</span>");
						$('#editpaymentmodel').animate({scrollTop:0}, 'slow');
					}
					$("#loader").hide();
					
				},
				error:function(jqXhr,status) { 
				
				$("#loader").hide();
					if(jqXhr.status === 422) {
						var errors = jqXhr.responseJSON;

                        if(typeof  errors.errors['payment_id']  != "undefined"){
                            $('input[name="payment_id"]').after("<span class='custom-error' role='alert'>"+errors.errors['payment_id']+"</span>");
                        }
						if(typeof  errors.errors['amount_rec']  != "undefined"){
                            $('input[name="amount_rec"]').after("<span class='custom-error' role='alert'>"+errors.errors['amount_rec']+"</span>");
                        }
						if(typeof  errors.errors['payment_date']  != "undefined"){
                            $('input[name="payment_date"]').after("<span class='custom-error' role='alert'>"+errors.errors['payment_date']+"</span>");
                        }
						$('#editpaymentmodel').animate({scrollTop:0}, 'slow');
					}
				},
				beforeSend: function() {
					$("#loader").show();
				}
			});
	});
		$(document).delegate('#payment_save', "click", function () {
		$('.custom-error').remove();
		
		var fd = new FormData(document.getElementById('updatepaymentsave'));
		$.ajax({
				type:'post',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url:site_url+'/admin/invoice/paymentsave',
				contentType: false,
				processData: false,
				data:fd,
				success:function(resp) {
					var obj = $.parseJSON(resp);
					if(obj.success){
						 location.reload();
							
					}else{
						$('.customerror').html("<span class='custom-error' role='alert'>"+obj.message+"</span>");
						$('#payment_modal').animate({scrollTop:0}, 'slow');
					}
					$("#loader").hide();
					
				},
				error:function(jqXhr,status) { 
				
				$("#loader").hide();
					if(jqXhr.status === 422) {
						var errors = jqXhr.responseJSON;

                        if(typeof  errors.errors['invoice_id']  != "undefined"){
                            $('input[name="invoice_id"]').after("<span class='custom-error' role='alert'>"+errors.errors['invoice_id']+"</span>");
                        }
						if(typeof  errors.errors['amount_rec']  != "undefined"){
                            $('input[name="amount_rec"]').after("<span class='custom-error' role='alert'>"+errors.errors['amount_rec']+"</span>");
                        }
						if(typeof  errors.errors['payment_date']  != "undefined"){
                            $('input[name="payment_date"]').after("<span class='custom-error' role='alert'>"+errors.errors['payment_date']+"</span>");
                        }
						$('#payment_modal').animate({scrollTop:0}, 'slow');
					}
				},
				beforeSend: function() {
					$("#loader").show();
				}
			});
	});
	$('.list_td').on("click", function () {
		var val = $(this).attr('invoiceid');
		$("#loader").show();
		$.ajax({
			url:site_url+'/admin/invoice/invoicebyid',
			type: 'get',
			data: {invoiceid:val},
			success: function(response){
				$("#loader").hide();
				$('.showinvoicedata').html(response);
			
				if (typeof (history.pushState) != "undefined") {
					var obj = { Page: val, Url: val };
					history.pushState(obj, obj.Page, obj.Url);
				} else {
					alert("Browser does not support HTML5.");
				}
			}
		});
	});
	$(document).delegate('.payment_modal', "click", function () {
		var val = $(this).attr('dataid');
		$('#payment_modal').modal('show'); 
		$("#loader").show();
		$.ajax({
			url:site_url+'/admin/invoice/detail',
			type: 'get',
			data: {invoiceid:val},
			success: function(response){
				$("#loader").hide();
				var obj = $.parseJSON(response);
				if(obj.success){
					$('#payment_modal .modal-title').html('Payment for '+obj.invoicedetail.invoice);
					$('#payment_modal #invoice_id').val(obj.invoicedetail.id);
					$('#payment_modal #customer_name').val(obj.invoicedetail.customer.first_name+' '+ obj.invoicedetail.customer.last_name);
					$('#payment_modal #amount_rec').val(obj.invoicedetail.amount);
				}else{
					$('#payment_modal .modal-body').html(obj.message);
				}
			}
		});
	});
	
		$(document).delegate('.editpaymentmodel', "click", function () {
		var val = $(this).attr('dataid');
		$('#editpaymentmodel').modal('show'); 
		$("#loader").show();
		$.ajax({
			url:site_url+'/admin/invoice/editpayment',
			type: 'get',
			data: {invoiceid:val},
			success: function(response){
				$("#loader").hide();
				var obj = $.parseJSON(response);
				if(obj.success){
					$('#editpaymentmodel #payment_id').val(obj.invoicedetail.id);
					$('#editpaymentmodel #customer_name').val(obj.invoicedetail.invoice.customer.first_name+' '+ obj.invoicedetail.invoice.customer.last_name);
					$('#editpaymentmodel #amount_rec').val(obj.invoicedetail.amount_rec);
					$('#editpaymentmodel #bank_charges').val(obj.invoicedetail.bank_charges);
					$('#editpaymentmodel #payment_date').val(obj.invoicedetail.payment_date);
					$('#editpaymentmodel #payment_mode').val(obj.invoicedetail.payment_mode);
					$('#editpaymentmodel #reference').val(obj.invoicedetail.reference);
					$('#editpaymentmodel #notes').val(obj.invoicedetail.notes);
				}else{
					$('#editpaymentmodel .modal-body').html(obj.message);
				}
			}
		});
	});
	
	
	var flg = 0;

    $('#customer_name').on("select2:open", function () {
      flg++;
      if (flg == 1) {
        var this_html = $('#wrpdata').html();
		console.log(this_html);
        $(".select2-results").append("<div class='select2-results__option'>" + 
      this_html + "</div>");
      }
    });
	
	$(document).delegate('.copyboard', 'click', function(e) {
  e.preventDefault();

  var copyText = $(this).attr('data-text');

  var textarea = document.createElement("textarea");
  textarea.textContent = copyText;
  textarea.style.position = "fixed"; // Prevent scrolling to bottom of page in MS Edge.
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand("copy"); 

  document.body.removeChild(textarea);
  
  $('#sharelinkmodel').modal('hide');
  var html = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
	html +=	'<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>';
	html += '<strong>Successfully Copied!</strong>';
	html += '</div>';
  $('.custom-error-msg').html(html);
   setTimeout(function(){
          $('.custom-error-msg').html('');
        }, 5000);
});
	
	
	$(document).delegate('#share_linkdisable','click', function(){
		$("#loader").show();
		$.ajax({
			type:'post',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url:site_url+'/admin/invoice/disablelink',
			success: function(resp){
				$("#loader").hide();
				var obj = $.parseJSON(resp);
					if(obj.success){
						 $('#sharelinkmodel').modal('hide');
						var html = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
						html +=	'<button type="button" class="close" data-dismiss="alert" aria-label="Close">☓</button>';
						html += '<strong>All Invoice Links are disabled!</strong>';
						html += '</div>';
					  $('.custom-error-msg').html(html);
					   setTimeout(function(){
							  $('.custom-error-msg').html('');
							}, 5000);
					}else{}
			}
		});
	});
	$(document).delegate('#share_linksave','click', function(){
		$('.custom-error').remove();
		$('#sharelink').val('');
		$('.showifgenrate').hide();
		$('.hideifshare').show();
		$('.disableifgenreate').prop('disabled', false);
		if($('#invoiceid').val() != ''){
			$("#loader").show();
			$.ajax({
				type:'post',
				headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				url:site_url+'/admin/invoice/sharelink',
				data:{invoiceid:$('#invoiceid').val(),expire_date:$('#expire_date').val()},
				success: function(resp){
					$("#loader").hide();
					var obj = $.parseJSON(resp);
					if(obj.success){
						$('#sharelink').val(obj.sharelink);
						$('#copydata').attr('data-text', obj.sharelink);
						$('.showifgenrate').show();
						$('.hideifshare').hide();
						$('.disableifgenreate').prop('disabled', true);
					}else{
						//$('.custom').html(obj.sharelink);
					}
				}
			});
		}
	});
	$(document).delegate('#setreminder','click', function(){
		$("#loader").show(); 
		var flag = true;
		$(".custom-error").remove();
		if($('#popoverdate').val() == ''){
			$('#popoverdate').parent().after("<span class='custom-error' role='alert'>"+error+"</span>");
			flag = false;
		}
		if(flag){
			$.ajax({
				type:'post',
					url:followupstore,
					headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					
					data: {note_type:'follow_up',description:$('#remindernote').val(),lead_id:$('#leadid').val(),followup_date:$('#popoverdate').val(),followup_time:$('#popovertime').val(),rem_cat:$('#rem_cat option:selected').val()},
					success: function(response){
						$('#loader').hide(); 
						var obj = $.parseJSON(response);
						if(obj.success){
							$("[data-role=popover]").each(function(){
           
                (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
            
        });
							myfollowuplist(obj.leadid);
						}else{
							
							
						}
					}
			});
		}else{
			$("#loader").hide();
		}
	});
	$(document).delegate('.openpopmodel','click', function(){
		var obj = $(this).attr('datatypemodel');
		var datatypeid = $(this).attr('datatypeid');
		$('#'+obj).modal('show');
		 $('#savetype').val(datatypeid);
		 if(datatypeid == 'topinclusion'){
			  $('.displayiftopinclusion').show();
		}else{
			$('.displayiftopinclusion').hide(); 
		 }				
		 $('#inclusion_Name').val("");
		 $('.inclusion-error').html("");
		 $('#fileimage').val("");
	}); 
	$(document).delegate('#save_Inclusion','click', function(){
		$(".custom-error").remove();
		var flag = true;
		if($('#inclusion_Name').val() == ''){
			$('#inclusion_Name').after("<span class='custom-error' role='alert'>Field is required</span>");
			flag= false;
		}
		if(flag){
			 var fd = new FormData(document.getElementById('addnewdatapackage'));
			var files = $('#fileimage')[0].files[0];
			fd.append('fileimage',files);
		$.ajax({
			type:'post',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url:site_url+'/admin/add_inclusion',
			contentType: false,
			processData: false,
			data:fd,
			success:function(resp) {
				var obj = $.parseJSON(resp);
				if(obj.status == 1) {
					var data = {
					id: obj.data.id,
					text: obj.data.name
					};
					if($('#savetype').val() == 'exclusions'){
						var newOption = new Option(data.text, data.id, true, true);
							// Append it to the select
							$('#packageexclusions').append(newOption).trigger('change');
					}else if($('#savetype').val() == 'topinclusion'){
						var newOption = new Option(data.text, data.id, true, true);
							// Append it to the select
							$('#packagetopinclusions').append(newOption).trigger('change');
					}else if($('#savetype').val() == 'inclusion'){
						var newOption = new Option(data.text, data.id, true, true);
							// Append it to the select
							$('#packageinclusions').append(newOption).trigger('change');
					}else if($('#savetype').val() == 'departure_x_city'){
						var newOption = new Option(data.text, data.id, true, true);
							// Append it to the select
							$('#city').append(newOption).trigger('change');
					}else if($('#savetype').val() == 'holidaytype'){
						var newOption = new Option(data.text, data.id, true, true);
							// Append it to the select
							$('#holiday_type').append(newOption).trigger('change');
					}
							
						 
					$('#Inclusionsopen_modal').modal('hide');
				} else{
					$('.inclusion-error').html(obj.message);
					
				}
				$("#loader").hide();
				
			},
			beforeSend: function() {
				$("#loader").show();
			}
		});
	}
	});
	 
	 
	$(document).delegate('.opennotepopup', 'click', function(){
		var notename = $.trim($(this).attr('data-notename'));
			var notetype = $.trim($(this).attr('data-notetype'));
		$('#myAddnotes .modal-title').html(notename);
		$('#myAddnotes #note_type').val(notetype);
		$('#myAddnotes').modal('show');
	});
	//$(document).delegate('.change-status','click', function(){
		$('.change-status').on('switchChange.bootstrapSwitch', function (event, state) {
			var id = $.trim($(this).attr('data-id'));
			var current_status = $.trim($(this).attr('data-status'));
			var table = $.trim($(this).attr('data-table'));
			var col = $.trim($(this).attr('data-col'));
			
			if(id != "" && current_status != "" && table != ""){
				updateStatus(id, current_status, table, col);
			}
		});
		
		$('.change-status-type').on('switchChange.bootstrapSwitch', function (event, state) {
			var id = $.trim($(this).attr('data-id'));
			var current_status = $.trim($(this).attr('data-status'));
			var table = $.trim($(this).attr('data-table'));
			var col = $.trim($(this).attr('data-col'));
			
			if(id != "" && current_status != "" && table != ""){
				updateTypeStatus(id, current_status, table, col);
			}
		});
	/*Package Theme Start*/
	
	//Update Status Start
	function updateStatus( id, current_status, table,col ) {
		$(".server-error").html(''); //remove server error.	
		$(".custom-error-msg").html(''); //remove custom error.
		$.ajax({
			type:'post',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url:site_url+'/admin/update_action',
			data:{'id': id, 'current_status' : current_status, 'table': table, 'colname':col},
			success:function(resp) {
				var obj = $.parseJSON(resp);
				if(obj.status == 1) {
					//show success msg 
						var html = successMessage(obj.message);
						$(".custom-error-msg").html(html);
					
					//change status
						if(current_status == 1){
							var updated_status = 0;
						} else {
							var updated_status = 1;
						}
					
						$(".change-status[data-id="+id+"]").attr('data-status', updated_status);
					
				} else{
					var html = errorMessage(obj.message);
					$(".custom-error-msg").html(html);
					
					//not change status
						if(current_status == 1){
							$(".change-status[data-id="+id+"]").prop('checked', true);
						} else {
							$(".change-status[data-id="+id+"]").prop('checked', false);
						}
				}
				$("#loader").hide();
			},
			beforeSend: function() {
				$("#loader").show();
			}
		});
		$('html, body').animate({scrollTop:0}, 'slow');
	}
	
	function updateTypeStatus( id, current_status, table,col ) {
		$(".server-error").html(''); //remove server error.	
		$(".custom-error-msg").html(''); //remove custom error.
		$.ajax({
			type:'post',
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			url:site_url+'/admin/update_type_action',
			data:{'id': id, 'current_status' : current_status, 'table': table, 'colname':col},
			success:function(resp) {
				var obj = $.parseJSON(resp);
				if(obj.status == 1) {
					//show success msg 
						var html = successMessage(obj.message);
						$(".custom-error-msg").html(html);
					
					//change status
						if(current_status == 1){
							var updated_status = 0;
						} else {
							var updated_status = 1;
						}
					
						$(".change-status-type[data-id="+id+"]").attr('data-status', updated_status);
					
				} else{
					var html = errorMessage(obj.message);
					$(".custom-error-msg").html(html);
					
					//not change status
						if(current_status == 1){
							$(".change-status-type[data-id="+id+"]").prop('checked', true);
						} else {
							$(".change-status-type[data-id="+id+"]").prop('checked', false);
						}
				}
				$("#loader").hide();
			},
			beforeSend: function() {
				$("#loader").show();
			}
		});
		$('html, body').animate({scrollTop:0}, 'slow');
	}
//Update Status End
	$('#save_theme').on('click', function(){
		var val = $('#holiday_type option:selected').val(); 
		if ($('table#theme_table').find('#theme_'+val).length > 0) {
		}else{
		if(val != ''){
			$('#hide_theme').hide();
			var html = '<tr id="theme_'+val+'"><td>'+val+'</td><td>'+$('#holiday_type option:selected').text()+'</td><td><a class="remove_theme" id="'+val+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_theme" id="'+val+'"><i class="fa fa-edit"></i></a><input type="hidden" name="package_theme[]" value="'+val+'"></td></tr>';
			$('.theme_data').append(html);
			$('#holiday_type').val('');
			$('#packagetype_modal').modal('hide');
		}
		}
	});
	
	$(document).delegate('.remove_theme','click', function(){
		var val = $(this).attr('id'); 
		$('#theme_'+val).remove();
	});
	
	$(document).delegate('.package_themeopen','click', function(){
		$('#holiday_type').val('');
		$('#save_theme').show();
		$('#update_theme').hide();
		$('#packagetype_modal').modal('show');
	});
	var cval = '';
	$(document).delegate('.edit_theme','click', function(){
		var val = $(this).attr('id'); 
		cval = val;
		$('#holiday_type').val(val);
		$('#save_theme').hide();
		$('#update_theme').show();
		$('#packagetype_modal').modal('show');
	});
	
	$(document).delegate('#update_theme','click', function(){
		var val = $('#holiday_type option:selected').val(); 
		var html = '<td>'+val+'</td><td>'+$('#holiday_type option:selected').text()+'</td><td><a class="remove_theme" id="'+val+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_theme" id="'+val+'"><i class="fa fa-edit"></i></a><input type="hidden" name="package_theme[]" value="'+val+'"></td>';
		if ($('table#theme_table').find('#theme_'+val).length > 0) {
		}else{
			if(val != ''){
			$('#theme_'+cval).html(html);
			$('#theme_'+cval).attr('id','theme_'+val);
			} 
		}
			$('#holiday_type').val('');
			
			$('#packagetype_modal').modal('hide');
	});
	/*Package Theme End*/
	/*Hotel Start*/
	$('.hotelopen').on('click', function(){
		$('#dest_type_pack').val('');
		$('#destination_pack').html('<option value="">Choose One...</option>');
				$('#hotel_name').html('<option value="">Choose One...</option>');
		$('#save_hotel').show();
		$('#update_hotel').hide();
		$('#hotel_modal').modal('show');
	});
	
	var ih = $('.hoteldata tr.counthtlen').length;
	console.log(ih+'dd');
	$('#save_hotel').on('click', function(){
		var dest_typ = $('#dest_type_pack option:selected').val(); 
		var dest_pack = $('#destination_pack option:selected').val(); 
		var hotel_name = $('#hotel_name option:selected').val(); 
		var despack = '';
		var hotpack = '';
		$('#destination_pack option').map(function() { 
		if($(this).val() != ''){
		despack += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
		}
		});
		
		$('#hotel_name option').map(function() { 
		if($(this).val() != ''){
		hotpack += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
		}
		});
		if ($('table#hotel_table').find('#hotel_'+hotel_name).length > 0) {
		}else{
			if(dest_typ != '' && dest_pack != '' && hotel_name != ''){
				hoteldata[ih] = {
					"destination_type":dest_typ,
					"destination":dest_pack,
					"hotel_name":hotel_name,
					"alldest":despack.replace(/,\s*$/, ""),
					
					"allhotel":hotpack.replace(/,\s*$/, ""),
					
				}
				console.log(hoteldata);
				$('#hide_hotelrow').hide();
				var html = '<tr id="hotel_'+ih+'"><td>'+hotel_name+'</td><td>'+$('#hotel_name option:selected').text()+'</td><td><a class="remove_hotel" id="'+ih+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_hotel" id="'+ih+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_hotel_name[]" value="'+hotel_name+'"><input type="hidden" name="all_dest_type[]" value="'+dest_typ+'"><input type="hidden" name="all_hotel_destination[]" value="'+dest_pack+'"></td></tr>';
				$('.hoteldata').append(html);
				$('#dest_type_pack').val('');
				$('#destination_pack').html('<option value="">Choose One...</option>');
				$('#hotel_name').html('<option value="">Choose One...</option>');
				$('#hotel_modal').modal('hide');
				ih++;
			}
		}
	});
	
	var htval = 0;
	$(document).delegate('.edit_hotel','click', function(){
		var val = $(this).attr('id'); 
		htval = val;
		$('#dest_type_pack').val(hoteldata[val]['destination_type']);
		var dtp = hoteldata[val]['alldest'];
		var htp = hoteldata[val]['allhotel'];

		var html = '<option value="">Choose One...</option>';
		var htmlh = '<option value="">Choose One...</option>';

		html += dtp;
		htmlh += htp;
		$('#destination_pack').html(html);
		$('#destination_pack').val(hoteldata[val]['destination']);
		
		$('#hotel_name').html(htmlh);
		$('#hotel_name').val(hoteldata[val]['hotel_name']);
		$('#save_hotel').hide();
		$('#update_hotel').show();
		$('#hotel_modal').modal('show');
	});
	
	$(document).delegate('#update_hotel','click', function(){
		var dest_typ = $('#dest_type_pack option:selected').val(); 
		var dest_pack = $('#destination_pack option:selected').val(); 
		var hotel_name = $('#hotel_name option:selected').val(); 
		var despack = '';
		var hotpack = '';
		$('#destination_pack option').map(function() { 
		if($(this).val() != ''){
		despack += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
		}
		});
		
		$('#hotel_name option').map(function() { 
		if($(this).val() != ''){
		hotpack += '<option value="'+$(this).val()+'">'+$(this).text()+'</option>';
		}
		});
		
		if(dest_typ != '' && dest_pack != '' && hotel_name != ''){
				hoteldata[htval] = {
					"destination_type":dest_typ,
					"destination":dest_pack,
					"hotel_name":hotel_name,
					"alldest":despack.replace(/,\s*$/, ""),
					
					"allhotel":hotpack.replace(/,\s*$/, ""),
					
				}
				
				$('#hide_hotelrow').hide();
				var html = '<td>'+hotel_name+'</td><td>'+$('#hotel_name option:selected').text()+'</td><td><a class="remove_hotel" id="'+hotel_name+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_hotel" id="'+htval+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_hotel_name[]" value="'+hotel_name+'"><input type="hidden" name="all_dest_type[]" value="'+dest_typ+'"><input type="hidden" name="all_hotel_destination[]" value="'+dest_pack+'"></td>';
			$('#hotel_'+htval).html(html);
			$('#hotel_'+htval).attr('id','hotel_'+htval);
			} 
			
				$('#hotel_modal').modal('hide');

	});
	
	$(document).delegate('.remove_hotel','click', function(){
		var val = $(this).attr('id'); 
		
		$('#hotel_'+val).remove();
	});
	/*Hotel End*/
	var isitenery = false;
	/*Itinerary Start*/
	$('.itineraryshow').on('click', function(){
		$('#itinerary_title').val('');
		$('#itinerary_detail').val('');
		$('#itinerary_modal .note-editable').html(' ');
		$('.itineraryimage_m').html('');
		$('#itineraryimage_m').val('');
		$('#file_itineraryimage_m_id').val('');
		$('#itineraryimage_m_id .dungdt-upload-box-normal').removeClass('active');
		$('#itineraryimage_m_id .upload-box').show();
		$('#itineraryimage_m_id .attach-demo').html(''); 
		$('#foodtype').val([]).trigger('change');
		$('#save_itinerary').show();
		$('#update_itinerary').hide();
		$('#itinerary_modal').modal('show');
		var isitenery = true;
		if(isitenery){
			$(document).on("hidden.bs.modal", "#cdn-browser-modal", function (e) {

			console.log('2nd level modal closed');
			$("body").addClass("modal-open");
			});
		}
	});
	
	
		var is = $('.itinerarydata tr.countitlen').length;
		
	console.log(is);

		$('#save_itinerary').on('click', function(){
			console.log(is);
		var itinerary_title = $('#itinerary_title').val(); 
		var itinerary_detail = $('#itinerary_detail').val(); 
		var itineraryimage_m = $('#file_itineraryimage_m_id').val(); 
		var itinerary_img = $('#file_itineraryimage_m_id').val(); 
		var itineraryimg = $('#itineraryimage_m_id .attach-demo img').attr('src'); 
		var foodtype = ''; 
		$("#foodtype option:selected").each(function(){
			foodtype += $(this).val()+',';
		});
		if ($('table#itinerary_table').find('#itinerary_'+is).length > 0) {
		}else{
			if(itinerary_title != ''){
				itinerarydata[is] = {
					"title":itinerary_title,
					"detail":btoa(itinerary_detail),
					"imageid":itinerary_img,
					"image":itineraryimg,
					"foodtype":foodtype.replace(/,\s*$/, ""),
				}
				console.log(itinerarydata);
				$('#hide_itinerary').hide();
				
				var html = '<tr id="itinerary_'+is+'"><td class="itenery_day"></td><td>'+itinerary_title+'</td><td><a class="remove_itinerary" id="'+is+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_itinerary" id="'+is+'"><i class="fa fa-edit"></i></a><input type="hidden" name="itinerary_title[]" value="'+itinerary_title+'"><textarea style="display:none;" type="hidden" name="all_itinerary_detail[]" value="">'+itinerary_detail+'</textarea><input type="hidden" name="all_itinerary_img[]" value="'+itinerary_img+'"><input type="hidden" name="all_itinerary_food[]" value="'+foodtype+'"></td></tr>';
				$('.itinerarydata').append(html);
				$('#itinerary_title').val('');
				$('#itinerary_detail').val(''); 
				$('#itinerary_modal').modal('hide');
				is++;
				serlizeno();
			}
		}
	});
	
	function serlizeno(){
		var i=1;
		$('.itinerarydata tr').each(function() {
			$(this).find(".itenery_day").html(i);
			i++;
		});
	}
	var isiteneryd = false;
	var itval = 0;
	$(document).delegate('.edit_itinerary','click', function(){
		
		var val = $(this).attr('id'); 
		itval = val;
		console.log(val);
		$('#itinerary_title').val(itinerarydata[val]['title']);
		$('#itinerary_modal .note-editable').html(' ');
		$('#foodtype').val([]).trigger('change');
		$('#itinerary_detail').val(atob(itinerarydata[val]['detail']));
		$('#itinerary_modal .note-editable').html(atob(itinerarydata[val]['detail']));
	if (typeof itinerarydata[val]['image'] !== "undefined") {
	
			console.log(itinerarydata[val]['image']);
			$('#itineraryimage_m_id .dungdt-upload-box-normal').addClass('active');
		$('#itineraryimage_m_id .upload-box').hide();
		$('#itineraryimage_m_id .attach-demo').show();
		$('#itineraryimage_m_id .upload-actions').show();
			$('#file_itineraryimage_m_id').val(itinerarydata[val]['imageid']);
		$('#itineraryimage_m_id .attach-demo').html('<img width="100" height="100" src="'+itinerarydata[val]['image']+'">');
		}else{
			$('#itineraryimage_m_id .attach-demo').html(''); 
			$('.itineraryimage_m').html('');
		$('#file_itineraryimage_m_id').val('');
		$('#itineraryimage_m_id .dungdt-upload-box-normal').removeClass('active');
		$('#itineraryimage_m_id .upload-box').show();
		}
		var ff = itinerarydata[val]['foodtype'];
		var splitval = ff.split(',');
		var Values = new Array();
		for(var i =0; i<splitval.length;i++){
		Values.push(splitval[i]);
		}

		$("#foodtype").val(Values).trigger('change');
		
			
		$('#save_itinerary').hide();
		$('#update_itinerary').show();
		$('#itinerary_modal').modal('show');
		var isiteneryd = true;
		if(isiteneryd){
		$(document).on("hidden.bs.modal", "#cdn-browser-modal", function (e) {

			console.log('2nd level modal closed');
			$("body").addClass("modal-open");
			});
		}
	});
	
	
	$(document).delegate('#update_itinerary','click', function(){
		console.log(itval);
		var itinerary_title = $('#itinerary_title').val(); 
		var itinerary_detail = $('#itinerary_detail').val(); 
		var itineraryimage_m = $('#file_itineraryimage_m_id').val(); 
		var itinerary_img = $('#file_itineraryimage_m_id').val(); 
		var itineraryimg = $('#itineraryimage_m_id .attach-demo img').attr('src'); 
		var foodtype = ''; 
		$("#foodtype option:selected").each(function(){
			foodtype += $(this).val()+',';
		});
			
		
			if(itinerary_title != ''){
				itinerarydata[itval] = {
					"title":itinerary_title,
					"detail":btoa(itinerary_detail),
					"imageid":itineraryimage_m,
					"image":itineraryimg,
					"foodtype":foodtype.replace(/,\s*$/, ""),
				}	
				
				var html = '<td class="itenery_day"></td><td>'+itinerary_title+'</td><td><a class="remove_itinerary" id="'+itval+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_itinerary" id="'+itval+'"><i class="fa fa-edit"></i></a><input type="hidden" name="itinerary_title[]" value="'+itinerary_title+'"><textarea style="display:none;" type="hidden" name="all_itinerary_detail[]" value="">'+itinerary_detail+'</textarea><input type="hidden" name="all_itinerary_img[]" value="'+itinerary_img+'"><input type="hidden" name="all_itinerary_food[]" value="'+foodtype+'"></td>';
			$('#itinerary_'+itval).html(html);
			$('#itinerary_'+itval).attr('id','itinerary_'+itval);
			} 
			
				$('#itinerary_modal').modal('hide');
				serlizeno();

	});
	 $('tbody.itinerarydata').sortable({
		  stop: function (event, ui) {
			serlizeno(); // re-number rows after sorting
		}
	 });
	$(document).delegate('.remove_itinerary','click', function(){
		var val = $(this).attr('id'); 
	
		$('#itinerary_'+val).remove();
		serlizeno();
	});
	/*Itinerary End*/
	var gglary = false;
	/*Gallery Start*/
	$('.galleryopen').on('click', function(){
		$('#gallery_imagealt').val('');

		$('.galleryimage_m').html('');
		$('#file_galleryimage_m_id').val('');
		$('#galleryimage_m_id .dungdt-upload-box-normal').removeClass('active');
		$('#galleryimage_m_id .upload-box').show();
		$('#save_gallery').show();
		$('#update_gallery').hide();
		$('#gallery_modal').modal('show');
		var gglary = true;
		if(gglary){
			$(document).on("hidden.bs.modal", "#cdn-browser-modal", function (e) {

			console.log('2nd level modal closed');
			$("body").addClass("modal-open");
			});
		}
	});
	var i =	$('.gallerydata tr.countgallen').length;
	
		$('#save_gallery').on('click', function(){
		var gallery_imagealt = $('#gallery_imagealt').val();  
		var galleryimage_m = $('#file_galleryimage_m_id').val(); 
		var galleryimage_src = $('#galleryimage_m_id .attach-demo img').attr('src'); 
		if ($('table#gallery_table').find('#gallery_'+i).length > 0) {
		}else{
			if(gallery_imagealt != ''){
				gallerydata[i] = {
					"image_alt":gallery_imagealt,
					"imageid":galleryimage_m,
					"image":galleryimage_src,
				}
				$('#hide_gallery').hide();
				var html = '<tr id="gallery_'+i+'"><td>'+galleryimage_m+'</td><td>'+gallery_imagealt+'</td><td><img width="50" height="50" src="'+galleryimage_src+'"></td><td><a class="remove_gallery" id="'+i+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_gallery" id="'+i+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_gallery_imagealt[]" value="'+gallery_imagealt+'"><input type="hidden" name="all_gallery_imageid[]" value="'+galleryimage_m+'"></td></tr>';
				$('.gallerydata').append(html);
				$('#galleryimage_m').val(''); 
				$('#gallery_imagealt').val(''); 
				$('#gallery_modal').modal('hide');
				i++;
			}
		}
	});
	
	var glval = 0;
	var galleryds = false;
	$(document).delegate('.edit_gallery','click', function(){
		
		var val = $(this).attr('id'); 
		glval = val;
		console.log(gallerydata[val]);
		$('#gallery_imagealt').val(gallerydata[val]['image_alt']);
		$('#file_galleryimage_m_id').val(gallerydata[val]['imageid']);
		$('#galleryimage_m_id .attach-demo').html('<img width="100" height="100" src="'+gallerydata[val]['image']+'">');
		
		$('#galleryimage_m_id .dungdt-upload-box-normal').addClass('active');
		$('#galleryimage_m_id .upload-box').hide();
		$('#galleryimage_m_id .attach-demo').show();
		$('#galleryimage_m_id .upload-actions').show();
		//$('.galleryimage_m').html('<input type="hidden" id="galleryimage_m" name="galleryimage_m" value="'+gallerydata[val]['imageid']+'"><img width="100" height="100" src="'+gallerydata[val]['image']+'"><a href="javascript:;" class="removepackimage" rdatatype="galleryimage_m" id="'+gallerydata[val]['imageid']+'"><i class="fa fa-times"></i></a>');
		$('#save_gallery').hide();
		$('#update_gallery').show();
		$('#gallery_modal').modal('show');
		
		var galleryds = true;
		if(galleryds){
		$(document).on("hidden.bs.modal", "#cdn-browser-modal", function (e) {

			console.log('2nd level modal closed');
			$("body").addClass("modal-open");
			});
		}
	});
	
	$(document).delegate('#update_gallery','click', function(){
		console.log(itval);
		var gallery_imagealt = $('#gallery_imagealt').val();  
		var galleryimage_m = $('#file_galleryimage_m_id').val(); 
		var galleryimage_src = $('#galleryimage_m_id .attach-demo img').attr('src'); 
	
		
				if(gallery_imagealt != ''){
				gallerydata[glval] = {
					"image_alt":gallery_imagealt,
					"imageid":galleryimage_m,
					"image":galleryimage_src,
				}	
				
			var html = '<td>'+galleryimage_m+'</td><td>'+gallery_imagealt+'</td><td><img width="50" height="50" src="'+galleryimage_src+'"></td><td><a class="remove_gallery" id="'+glval+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_gallery" id="'+glval+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_gallery_imagealt[]" value="'+gallery_imagealt+'"><input type="hidden" name="all_gallery_imageid[]" value="'+galleryimage_m+'"></td>';
			$('#gallery_'+glval).html(html);
			$('#gallery_'+glval).attr('id','gallery_'+glval);
			} 
			
				$('#gallery_modal').modal('hide');

	});
	
	$(document).delegate('.remove_gallery','click', function(){
		var val = $(this).attr('id'); 
		gallerydata = $.grep(gallerydata, function(value) {
		  return value != val;
		});
		$('#gallery_'+val).remove();
	});
	/*Gallery End*/ 
	
	/*Meta Tags Start*/
	$('.metatagopen').on('click', function(){
		$('#metatitle').val('');
		$('#metakeyword').val('');
		$('#metadescription').val('');
		$('#metatag_modal .note-editable').html(' ');
		
		$('#save_metatag').show();
		$('#update_metatag').hide();
		$('#metatag_modal').modal('show');
	});
	
	var itag = $('.metatag_data tr.countmtlist').length;
	if(itag >0){
			$('.metatagopen').hide();
		}else{
			$('.metatagopen').show();
		}
	$('#save_metatag').on('click', function(){		
		var metatitle = $('#metatitle').val(); 
		var metakeyword = $('#metakeyword').val(); 
		var metadescription = $('#metadescription').val(); 		
		if ($('table#metatag_table').find('#metatag_'+itag).length > 0) {
		}else{ 
		if(metatitle != ''){
			metatagdata[itag] = {
					"title":metatitle,
					"keyword":metakeyword,
					"description":btoa(metadescription),
				}	
			$('#hide_metatag').hide();
			var html = '<tr id="metatag_'+itag+'"><td>'+metatitle+'</td><td>'+metakeyword+'</td><td>'+metadescription+'</td><td><a class="remove_metatag" id="'+i+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_metatag" id="'+itag+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_meta_title[]" value="'+metatitle+'"><input type="hidden" name="all_meta_keyword[]" value="'+metakeyword+'"><textarea style="display:none;" type="hidden" name="all_meta_desc[]" value="">'+metadescription+'</textarea></td></tr>';
			$('.metatag_data').append(html);
			$('#metatitle').val('');
			$('#metakeyword').val('');
			$('#metadescription').val('');
			$('#metatag_modal').modal('hide');
			itag++;
		}
		}
		if(itag >0){
			$('.metatagopen').hide();
		}else{
			$('.metatagopen').show();
		}
	});
	
	
	var mtval = 0;
	$(document).delegate('.edit_metatag','click', function(){
		
		var val = $(this).attr('id'); 
		mtval = val;
		console.log(metatagdata[val]);
		$('#metatitle').val(metatagdata[val]['title']);
		$('#metatag_modal .note-editable').html(' ');
		$('#metakeyword').val(metatagdata[val]['keyword']);
		$('#metadescription').val(atob(metatagdata[val]['description']));
		$('#metatag_modal .note-editable').html(metatagdata[val]['description']);
		
		
		$('#save_metatag').hide();
		$('#update_metatag').show();
		$('#metatag_modal').modal('show');
	});
	
	$(document).delegate('#update_metatag','click', function(){
		var metatitle = $('#metatitle').val(); 
		var metakeyword = $('#metakeyword').val(); 
		var metadescription = $('#metadescription').val(); 
	
			if(metatitle != ''){
				metatagdata[mtval] = {
					"title":metatitle,
					"keyword":metakeyword,
					"description":btoa(metadescription),
				}	
				
				$('#hide_metatag').hide();
			var html = '<td>'+metatitle+'</td><td>'+metakeyword+'</td><td>'+metadescription+'</td><td><a class="remove_metatag" id="'+i+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_metatag" id="'+mtval+'"><i class="fa fa-edit"></i></a><input type="hidden" name="all_meta_title[]" value="'+metatitle+'"><input type="hidden" name="all_meta_keyword[]" value="'+metakeyword+'"><textarea style="display:none;" type="hidden" name="all_meta_desc[]" value="">'+metadescription+'</textarea></td>';
			$('#metatag_'+mtval).html(html);
			$('#metatag_'+mtval).attr('id','metatag_'+mtval);
			} 
			
				$('#metatag_modal').modal('hide');

	});
	
	$(document).delegate('.remove_metatag','click', function(){
		var val = $(this).attr('id'); 
		
		$('#metatag_'+val).remove();
		var itag = $('.metatag_data tr.countmtlist').length;
		if(itag >0){
			$('.metatagopen').hide();
		}else{
			$('.metatagopen').show();
		}
	});
	/*Meta Tags End*/ 
	
	/*Meta Search Start*/
	$('#save_metasearch').on('click', function(){
		var val = $('#metasearch_destination option:selected').val(); 		
		if ($('table#metasearch_table').find('#metasearch_'+val).length > 0) {
		}else{
		if(val != ''){
			$('#hide_metasearch').hide();
			var html = '<tr id="metasearch_'+val+'"><td>'+val+'</td><td>'+$('#metasearch_destination option:selected').text()+'</td><td><a class="remove_metasearch" id="'+val+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_metasearch" id="'+val+'"><i class="fa fa-edit"></i></a><input type="hidden" name="metasearch[]" value="'+val+'"></td></tr>';
			$('.metasearch_data').append(html);
			$('#metasearch_destination').val('');
			$('#metasearch_modal').modal('hide');
		}
		}
	});
	
	$(document).delegate('.remove_metasearch','click', function(){
		var val = $(this).attr('id'); 
		$('#metasearch_'+val).remove();
	});
	
	$(document).delegate('.metasearchopen','click', function(){
		$('#metasearch_destination').val('');
		$('#save_metasearch').show();
		$('#update_metasearch').hide();
		$('#metasearch_modal').modal('show');
	});
	var cval = '';
	$(document).delegate('.edit_metasearch','click', function(){
		var val = $(this).attr('id'); 
		cval = val;
		$('#metasearch_destination').val(val);
		$('#save_metasearch').hide();
		$('#update_metasearch').show();
		$('#metasearch_modal').modal('show');
	});
	
	$(document).delegate('.sharelinkbtn','click', function(){
		$('.custom-error').remove();
		$('#sharelink').val('');
		$('.showifgenrate').hide();
		$('.hideifshare').show();
		$('.disableifgenreate').prop('disabled', false);
		var val = $(this).attr('dataid');
		$('#invoiceid').val(val);
		$('#sharelinkmodel').modal('show');
	});
	$(document).delegate('#update_metasearch','click', function(){
		var val = $('#metasearch_destination option:selected').val(); 
		var html = '<td>'+val+'</td><td>'+$('#metasearch_destination option:selected').text()+'</td><td><a class="remove_metasearch" id="'+val+'" href="javascript:;"><i class="fa fa-trash"></i></a> / <a href="javascript:;" class="edit_metasearch" id="'+val+'"><i class="fa fa-edit"></i></a><input type="hidden" name="metasearch[]" value="'+val+'"></td>';
		if ($('table#metasearch_table').find('#metasearch_'+val).length > 0) {
		}else{
			if(val != ''){
			$('#metasearch_'+cval).html(html);
			$('#metasearch_'+cval).attr('id','metasearch_'+val);
			} 
		}
			$('#metasearch_destination').val('');
			
			$('#metasearch_modal').modal('hide');
	});
	/*Meta Search End*/ 
});		 
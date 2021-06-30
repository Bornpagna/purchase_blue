@extends('layouts.app')

@section('content')
<style>
	.btnAdd{
		cursor: pointer;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="{{$icon}} font-dark"></i>
					<span class="caption-subject bold font-dark uppercase"> {{$title}}</span>
					<span class="caption-helper">{{$small_title}}</span>
				</div>
				<div class="actions">
					<a rounte="{{$rounteBack}}" title="{{trans('lang.back')}}" class="btn btn-circle btn-icon-only btn-default" id="btnBack">
						<i class="fa fa-reply"></i>
					</a>
				</div>
			</div>
			<div class="portlet-body form">
				<?php if(Session::has('success')):?>
					<div class="alert alert-success display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.success')}}!</strong> {{Session::get('success')}} 
					</div>
				<?php elseif(Session::has('error')):?>
					<div class="alert alert-danger display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.error')}}!</strong> {{Session::get('error')}} 
					</div>
				<?php endif; ?>
				<div class="form-group">
					<div class="col-md-12 ">
						<span class="show-message-error center font-red bold"></span>
					</div>
				</div>
				<form action="{{$rounteSave}}" method="POST" id="form-stock-entry" class="form-horizontal" enctype="multipart/form-data">
					{{csrf_field()}}
					<input type="hidden" name="btnSubmit" id="btnSubmit"/>
					<div class="portlet-body" >
						<div class="row">
							<div class="col-md-6">
								<div class="portlet-body" style="background: #f8f9fb;padding: 12px 12px 0 12px	;border: 1px solid #72aee2;">
									<h4>*** {{trans('lang.general_part')}}</h4>
									<hr style="margin-top:10px !important;">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="reference_no" class="col-md-4 control-label"><strong>{{ trans('lang.reference_no') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-8">
													<input class="form-control reference_no" length="20" type="text" id="reference_no" name="reference_no" placeholder="{{ trans('lang.enter_text') }}">
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="trans_date" class="col-md-4 control-label"><strong>{{ trans('lang.trans_date') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-8">
													<input class="form-control trans_date" length="10" type="text" id="trans_date" name="trans_date" placeholder="{{ trans('lang.enter_text') }}">
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="supplier" class="col-md-4 control-label"><strong>{{ trans('lang.supplier') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-8">
													@if(hasRole('supplier_add'))
														<div class="input-group">
															<select class="form-control supplier" name="supplier" id="supplier">
																<option value=""></option>
																{{getSuppliers()}}
															</select>
															<span class="input-group-addon btn blue" id="btnAddSupplier">
																<i class="fa fa-plus"></i>
															</span>
														</div>
													@else
														<select class="form-control supplier" name="supplier" id="supplier">
															<option value=""></option>
															{{getSuppliers()}}
														</select>
													@endif
													<span class="help-block font-red bold"></span>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6" >
								<div class="portlet-body" style="background: #f8f9fb;padding: 12px;border: 1px solid #72aee2;">
									<h4>*** {{trans('lang.desc')}}</h4>
									<hr style="margin-top:10px !important;">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="photo" class="col-md-2 control-label" style="text-align: left;"><strong>{{trans('lang.photo')}}</strong></label>
												<div class="col-md-10">
													<div class="fileinput fileinput-new input-group picture" data-provides="fileinput">
														<div class="form-control" data-trigger="fileinput">
															<i class="glyphicon glyphicon-file fileinput-exists"></i> 
															<span class="fileinput-filename"></span>
														</div>
														<span class="input-group-addon btn blue btn-file">
															<span class="fileinput-new bold">{{trans('lang.select_image')}}</span>
															<span class="fileinput-exists bold">{{trans('lang.change')}}</span>
															<input type="file" id="photo" name="photo" accept="image/*">
														</span>
															<a href="#" class="input-group-addon btn red fileinput-exists bold" data-dismiss="fileinput">{{trans('lang.remove')}}</a>
													</div>
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											<textarea class="form-control trans_desc" id="desc" name="desc" length="100" rows="3" placeholder="{{ trans('lang.enter_text') }}"> </textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="po_no" class="col-md-12 control-label" style="text-align: left;"><strong>{{ trans('lang.reference') }}</strong>
							<span class="required"> * </span>
						</label>
						<div class="col-md-12">
							<div class="input-group input-icon right">
								<span class="input-group-addon">
									<i class="fa fa-shopping-cart font-red"></i>
								</span>
								<select class="form-control po_no" name="po_no" id="po_no">
									<option value=""></option>
									{{getReferencePO()}}
								</select>
							</div>
							<span class="help-block font-red bold"></span>
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover" width="100%" id="table-income">
							<thead>
								<tr style="font-size:12px;">
									<th width="4%" class="text-center all">{{ trans('lang.line_no') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.items') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.units') }}</th>
									<th width="8%" class="text-center all">{{ trans('lang.qty') }}</th>
									<th width="8%" class="text-center all">{{ trans('lang.price') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.amount') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.disc(%)') }}</th>
									<th width="8%" class="text-center all">{{ trans('lang.disc($)') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.total') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.to_warehouse') }}</th>
									<th width="8%" class="text-center all">{{ trans('lang.remark') }}</th>
									<th width="5%" class="text-center all">{{trans('lang.action')}}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th class="text-center all" colspan="12">{{ trans('lang.no_data_available_in_table') }}</th>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="clearfix"></div><br/>
					<div class="form-actions text-right">
						<button type="submit" id="save_close" name="save_close" value="1" class="btn green bold">{{trans('lang.save')}}</button>
						<button type="submit" id="save_new" name="save_new" value="2"  class="btn blue bold">{{trans('lang.save_new')}}</button>
						<a class="btn red bold" rounte="{{$rounteBack}}" id="btnCancel">{{trans('lang.cancel')}}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('modal.supplier')
@endsection()

@section('javascript')
<script type="text/javascript">	
	var jsonUnits = JSON.parse(convertQuot("{{\App\Model\Unit::get(['id','from_code','from_desc','to_code','to_desc','factor','status'])}}"));
	var jsonItems = JSON.parse(convertQuot("{{\App\Model\Item::where('status',1)->get(['id','code','name','unit_purch','unit_stock'])}}"));
	
	function onRemove(field){
		$(field).parents('tr').remove();
		calculateTotal();
	}
	
	function isSave(){
		var isValid = true;
		$(".line_qty").each(function(){
			var qty_ = $(this).next().val();
			var qty = $(this).val();
			if(parseFloat(qty) > parseFloat(qty_)){
				isValid = false;
				$(this).css('border','1px solid #e43a45');
				$(".show-message-error").html('{{trans("lang.delivery_qty_can_not_greater_then_ordered_qty")}}!');
			}else{
				$(this).css('border','1px solid #c2cad8');
				$(".show-message-error").empty();
			}
		});
		return isValid;
	}
	
	jQuery.fn.ForceNumericOnly = function(){
        return this.each(function(){
            $(this).keydown(function(e){
                var key = e.charCode || e.keyCode || 0;
                return (
                    key == 8 || 
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };

	$('#save_close,#save_new').on('click',function(){
		$(this).prop('disabled', true);
		if(chkValid([".reference_no",".trans_date",".supplier",".trans_desc",".po_no",".disc_perc",".disc_usd",".line_item",".line_unit",".line_qty",".line_price",".line_disc_perc",".line_disc_usd",".line_warehouse"])){
			if(isSave()){
				$("#btnSubmit").val($(this).val());
				$('#form-stock-entry').submit();
			}else{
				$(this).prop('disabled', false);
				return false;
			}
		}else{
			$(this).prop('disabled', false);
			return false;
		}
	});
	
	function onSetItem(val, row){
		if(val!=null && val!='' && jsonUnits && jsonItems){
			$.each(jsonItems.filter(c=>c.id==val), function(key, val){
				$('.line_unit_'+row).empty();
				$('.line_unit_'+row).append($('<option></option>').val('').text(''));
				$.each(jsonUnits.filter(d=>d.to_code==val.unit_stock), function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
			});
		}
	}
	
	function onChangeUnit(field, row, po_unit, po_qty){
		var item_id = $(".line_item_"+row).val();
		var deliv_unit = $(field).val();
		var new_qty = 0;
		if(item_id!=null && item_id!='' && po_unit!=null && po_unit!='' && jsonItems && jsonUnits){
			var stock_unit = "";
			var stock_qty = 0;
			$.each(jsonItems.filter(c=>c.id==item_id), function(key, value){
				stock_unit = value.unit_stock;
			});
			$.each(jsonUnits.filter(c=>c.from_code==po_unit).filter(d=>d.to_code==stock_unit), function(key, value){
				stock_qty = parseFloat(value.factor) * parseFloat(po_qty);
			});
			$.each(jsonUnits.filter(c=>c.from_code==deliv_unit).filter(d=>d.to_code==stock_unit), function(key, value){
				new_qty = parseFloat(stock_qty) / parseFloat(value.factor);
			});
		}
		$(".line_qty_"+row).val(new_qty);
		$(".line_qty_orig_"+row).val(new_qty);
		
		calculateRecord(row);
	}	
	
	function enterQty(field, row){
		var qty_ = $(".line_qty_orig_"+row).val();
		var qty = $(field).val();
		if(qty!=null && qty!=''){
			if(parseFloat(qty) > parseFloat(qty_)){
				$(field).css('border','1px solid #e43a45');
				$(".show-message-error").html('{{trans("lang.delivery_qty_can_not_greater_then_ordered_qty")}}!');
			}else{
				$(field).css('border','1px solid #c2cad8');
				$(".show-message-error").empty();
			}
		}else{
			$(field).css('border','1px solid #c2cad8');
			$(".show-message-error").empty();
		}
		
		calculateRecord(row);
	}
	
	function onDiscountDollar(field, row){
		var disc_usd = $(field).val();
		var amount = $(".line_amount_"+row).val();
		
		if(disc_usd==null || disc_usd==''){
			$(".line_disc_perc_"+row).val(0);
			$(".line_disc_perc_"+row).attr('readonly', false);
		}else{
			var disc_perc = (parseFloat(disc_usd) * 100) / parseFloat(amount);
			$(".line_disc_perc_"+row).val(disc_perc.toFixed(2));
			$(".line_disc_perc_"+row).attr('readonly', true);
		}
		
		calculateRecord(row);
	}
	
	function onDiscountPercentage(field, row){
		var disc_perc = $(field).val();
		var amount = $(".line_amount_"+row).val();
		
		if(disc_perc==null || disc_perc==''){
			$(".line_disc_usd_"+row).val(0);
			$(".line_disc_usd_"+row).attr('readonly', false);
		}else{
			var disc_usd = (parseFloat(amount) * parseFloat(disc_perc)) / 100;
			$(".line_disc_usd_"+row).val(disc_usd.toFixed(2));
			$(".line_disc_usd_"+row).attr('readonly', true);
		}
		
		calculateRecord(row);
	}
	
	function calculateRecord(row){
		var qty = 0;
		var price = 0;
		var disc = 0;
		
		var qty_ = $(".line_qty_"+row).val();
		var price_ = $(".line_price_"+row).val();
		var disc_ = $(".line_disc_usd_"+row).val();
		if(price_ !='' && price_!=null){
			price = parseFloat(price_);
		}
		if(qty_ !='' && qty_!=null){
			qty = parseFloat(qty_);
		}
		if(disc_ !='' && disc_!=null){
			disc = parseFloat(disc_);
		}
		
		$(".line_amount_"+row).val(parseFloat(qty) * parseFloat(price));
		$(".line_grand_total_"+row).val((parseFloat(qty) * parseFloat(price)) - parseFloat(disc));
		
		calculateTotal();
	}
	
	function calculateTotal(){
		var sub_total = 0;
		var disc_usd = 0;
		
		var disc_usd_ = $(".disc_usd").val();
		if(disc_usd_ !='' && disc_usd_!=null){
			disc_usd = parseFloat(disc_usd_);
		}
		$(".line_grend_total").each(function(){
			var sub_total_ = $(this).val();
			if(sub_total_!="" && sub_total_!=null){
				sub_total = sub_total + parseFloat(sub_total_);
			}
		});
		$(".sub_total").val(parseFloat(sub_total));
		$(".grand_total").val(parseFloat(sub_total) - parseFloat(disc_usd));
	}
	
	function initTable(data, warehouse_id){
		$("#table-income tbody").append('<tr>'+
				'<td class="text-center all" style="vertical-align: middle !important;">'+
				'	<input type="hidden" class="line_index line_index_'+data.id+'" name="line_index[]" value="'+data.line_no+'" />'+
				'	<strong>'+data.line_no+'</strong>'+
				'</td>'+
				'<td class="text-left all" style="vertical-align: middle !important;">'+
				'	<input type="hidden" class="line_item line_item_'+data.id+'" value="'+data.item_id+'" name="line_item[]"/>'+
				'	<strong class="label_item label_item_'+data.id+'"></strong>'+
				'</td>'+
				'<td>'+
				'	<select class="form-control line_unit line_unit_'+data.id+'" name="line_unit[]">'+
				'		<option value=""></option>'+
				'	</select>'+
				'</td>'+
				'<td>'+
				'	<input type="number" value="'+data.qty+'" length="11" step="any" class="form-control noscroll line_qty line_qty_'+data.id+'" onkeyup="enterQty(this, '+data.id+')"  name="line_qty[]"/>'+
				'	<input type="hidden" value="'+data.qty+'" class="line_qty_orig line_qty_orig_'+data.id+'"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" value="'+data.price+'" length="11" step="any" class="form-control noscroll line_price line_price_'+data.id+'" onkeyup="calculateRecord('+data.id+')"  name="line_price[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="text" readonly value="'+data.amount+'" class="form-control noscroll line_amount line_amount_'+data.id+'" name="line_amount[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" value="'+data.disc_perc+'" length="11" step="any" class="form-control noscroll line_disc_perc line_disc_perc_'+data.id+'" onkeyup="onDiscountPercentage(this, '+data.id+')" name="line_disc_perc[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" value="'+data.disc_usd+'" length="11" step="any" class="form-control noscroll line_disc_usd line_disc_usd_'+data.id+'" onkeyup="onDiscountDollar(this, '+data.id+')" name="line_disc_usd[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="text" readonly value="'+data.total+'" class="form-control noscroll line_grend_total line_grand_total_'+data.id+'" name="line_grend_total[]"/>'+
				'</td>'+
				'<td>'+
				'	<select class="form-control line_warehouse line_warehouse_'+data.id+'" name="line_warehouse[]">'+
				'		<option value=""></option>'+
				'		{{getWarehouse()}}'+
				'	</select>'+
				'</td>'+
				'<td>'+
				'	<input type="text" length="100" class="form-control line_reference line_reference_'+data.id+'" name="line_reference[]"/>'+
				'</td>'+
				'<td class="text-center all">'+
				'	<button type="button" class="btn btn-danger" onclick="onRemove(this)" title="{{trans("lang.delete")}}"><i class="fa fa-remove"></i></button>'+
				'</td>'+
			'</tr>');
		$.fn.select2.defaults.set("theme", "classic");
		$(".line_unit_"+data.id+",.line_warehouse_"+data.id).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		$(".line_warehouse_"+data.id).select2('val', warehouse_id);
		$(".line_qty_"+data.id).ForceNumericOnly();
		if(jsonItems){
			$.each(jsonItems.filter(c=>c.id==data.item_id), function(key ,val){
				$(".label_item_"+data.id).html(val.code+' ('+val.name+')');
			});
		}
		onSetItem(data.item_id, data.id);
		$(".line_unit_"+data.id).select2('val', data.unit);
		$('.line_unit_'+data.id).attr("onchange", "onChangeUnit(this, "+data.id+", '"+data.unit+"',"+data.qty+")");
	}

	@if(hasRole('supplier_add'))
		var objSupplier = JSON.parse(convertQuot('{{\App\Model\Supplier::get(["name","desc"])}}'));
		$("#btnAddSupplier").on('click', function(event){
			event.preventDefault();
			$('.supplier-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#old_name').val('');
			$('#sup-name').val('');
			$('.tel').val('N/A');
			$('.status').select2('val',1);
			$('#sup-desc').val(' ');
			$('.address').val('N/A');
			$('.button-submit-supplier').attr('id','btnSave').attr('name','btnSave').attr('onclick','onSubmitSupplier(this)');
			$('.button-submit-supplier').html('{{trans("lang.save")}}');
			$('.supplier-modal').modal('show');
		});

		function onSubmitSupplier(field){
			$('.button-submit-supplier').prop('disabled', true);
			if(chkValid([".sup-name",".sup-desc"])){
				if(chkDublicateName(objSupplier, '#sup-name')){
					$.ajax({
						url :'{{url("supplier/save")}}',
						type:'POST',
						data:$('.supplier-form').serialize(),
						success:function(data){
							if(data){
								$("#supplier").append($('<option></option>').val(data.id).text(data.name+' ('+data.desc+')'));
								$("#supplier").select2('val', data.id);
							}
							$('.supplier-modal').modal('hide');
							$('.button-submit-supplier').prop('disabled', false);
						},error:function(){
							$('.supplier-modal').modal('hide');
							$('.button-submit-supplier').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-supplier').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit-supplier').prop('disabled', false);
				return false;
			}
		}
	@endif
	
	document.addEventListener("mousewheel", function(event){
		if(document.activeElement.type === "number" &&
		   document.activeElement.classList.contains("noscroll"))
		{
			document.activeElement.blur();
		}
	});
	
	function totalDiscountDollar(field){
		try {
			var sub_total = $("#sub_total").val();
			var disc = 0;
			var val = $(field).val();
			if(val=='' || val==null){
				$("#disc_perc").val(0);
				$("#disc_perc").attr('readonly', false);
			}else{
				disc = (parseFloat(val) * 100) / parseFloat(sub_total);
				$("#disc_perc").val(disc);
				$("#disc_perc").attr('readonly', true);
			}
			$("#grand_total").val(parseFloat(sub_total) - parseFloat(disc));
		}catch(err) {
			$("#grand_total").val(0);
		}
	}
	
	function totalDiscountPercentage(field){
		try {
			var sub_total = $("#sub_total").val();
			var disc = 0;
			var val = $(field).val();
			if(val=='' || val==null){
				$("#disc_usd").val(0);
				$("#disc_usd").attr('readonly', false);
			}else{
				disc = (parseFloat(val) * parseFloat(sub_total)) / 100;
				$("#disc_usd").val(disc);
				$("#disc_usd").attr('readonly', true);
			}
			$("#grand_total").val(parseFloat(sub_total) - parseFloat(disc));
		}catch(err) {
			$("#grand_total").val(0);
		}
		
	}
	
	$(document).ready(function(){
		$.fn.select2.defaults.set("theme", "classic");
		$(".supplier,.po_no").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		
		$('#trans_date').val(formatDate('{{date('Y-m-d')}}'));
		$("#trans_date").datepicker({
			format: "{{getSetting()->format_date}}",
            autoclose: true,
            pickerPosition: "bottom-right"
		});
		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
		
		/* setInterval(function() {
			$.ajax({
				url :'{{url("prefix/gen/DEL/Delivery/ref_no")}}',
				type:'get',
				success:function(data){
					if(data!=null && data!=''){
						$('#reference_no').attr("readonly", true);
						$('#reference_no').val(data);
					}else{
						$('#reference_no').attr("readonly", false);
						$('#reference_no').val('');
					}
				},error:function(){}
			});
		}, 3000); */
		
		$("#po_no").on('change', function(){
			var val = $(this).val();
			if(val!=null && val!=''){
				var _token = $("input[name=_token]").val();
				$.ajax({
					url :'{{url("purch/order/remotePO")}}',
					type:'POST',
					data:{
						'_token': _token,
						'po_id': val,
					},
					success:function(data){
						if(data){
							var grand_total = 0;
							var disc_perc = data.head.disc_perc;
							var disc_usd = 0;
							var sub_total = 0;
							
							$(".supplier").select2('val', data.head.sup_id);
							$("#table-income tbody").empty();
							$.each(data.body,function(key, row){
								initTable(row, data.head.delivery_address);
								grand_total = grand_total + parseFloat(row.total);
							});
							
							disc_usd = grand_total * (parseFloat(disc_perc) / 100);
							sub_total = grand_total - disc_usd;
							
							var str = '';
								str+= '<tr>';
								str+='<th colspan="10" class="text-right" style="vertical-align: middle !important;">{{trans("lang.sub_total")}}</th>';
								str+='<th colspan="2" class="text-right"><div class="input-group input-icon right"><span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span><input type="text" readonly class="form-control sub_total" name="sub_total" id="sub_total" value="'+sub_total+'"/></div></th>';
								str+='</tr>';
								str+='<tr>';
								str+='<th colspan="10" class="text-right" style="vertical-align: middle !important;">{{trans("lang.discount(%)")}}</th>';
								str+='<th colspan="2" class="text-right"><div class="input-group input-icon right"><span class="input-group-addon"><i class="font-red">%</i></span><input type="number" value="'+disc_perc+'" class="noscroll disc_perc form-control " onkeyup="totalDiscountPercentage(this)" id="disc_perc" name="disc_perc"/></div></th>';
								str+='</tr>';
								str+='<tr>';
								str+='<th colspan="10" class="text-right" style="vertical-align: middle !important;">{{trans("lang.discount($)")}}</th>';
								str+='<th colspan="2" class="text-right"><div class="input-group input-icon right"><span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span><input type="number" value="'+disc_usd+'" class="noscroll disc_usd form-control " onkeyup="totalDiscountDollar(this)" id="disc_usd" name="disc_usd"/></div></th>';
								str+='</tr>';
								str+='<tr>';
								str+='<th colspan="10" class="text-right" style="vertical-align: middle !important;">{{trans("lang.grand_total")}}</th>';
								str+='<th colspan="2" class="text-right"><div class="input-group input-icon right"><span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span><input type="text" readonly class="form-control grand_total" id="grand_total" name="grand_total" value="'+grand_total+'"/></div></th>';
								str+='</tr>';
							$("#table-income tbody").append(str);
						}
					},error:function(){
						console.log('error get PR reference.');
					}
				});
			}
		});
	});
</script>
@endsection()
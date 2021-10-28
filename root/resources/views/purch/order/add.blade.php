@extends('layouts.app')

@section('content')
<style>
	td.details-control {
        background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
        cursor: pointer !important;
    }
    tr.shown td.details-control {
        background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
    }
	.btnAdd{
		cursor: pointer;
	}
	.form-horizontal .form-group {
		margin-left: 0px !important;
		margin-right: 0px !important;
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

					<!-- New Form -->
					<div class="row">

						<!-- Reference No -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="reference_no" class="control-label"><strong>{{ trans('lang.reference_no') }}</strong>
									<span class="required"> * </span>
								</label>
								<input class="form-control reference_no" length="20" type="text" id="reference_no" name="reference_no" placeholder="{{ trans('lang.enter_text') }}">
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Trans Data -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="trans_date" class="control-label"><strong>{{ trans('lang.trans_date') }}</strong>
									<span class="required"> * </span>
								</label>
								<input class="form-control trans_date" length="10" type="text" id="trans_date" name="trans_date" placeholder="{{ trans('lang.enter_text') }}">
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Delivry Date -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="delivery_date" class="control-label"><strong>{{ trans('lang.delivery_date') }}</strong>
									<span class="required"> * </span>
								</label>
								<input class="form-control delivery_date" length="10" type="text" id="delivery_date" name="delivery_date" placeholder="{{ trans('lang.enter_text') }}">
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Delivery Address -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="delivery_address" class="control-label"><strong>{{ trans('lang.delivery_address') }}</strong>
									<span class="required"> * </span>
								</label>
								@if(hasRole('warehouse_add'))
									<div class="input-group">
										<select class="form-control select2 delivery_address" name="delivery_address" id="delivery_address">
											<option value=""></option>
											{{getWarehouse('all')}}
										</select>
										<span class="input-group-addon btn blue" id="btnAddWarehouse">
											<i class="fa fa-plus"></i>
										</span>
									</div>
								@else
									<select class="form-control select2 delivery_address" name="delivery_address" id="delivery_address">
										<option value=""></option>
										{{getWarehouse('all')}}
									</select>
								@endif
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Supplier -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="supplier" class="control-label" style="text-align: left;"><strong>{{ trans('lang.supplier') }}</strong>
									<span class="required"> * </span>
								</label>
								@if(hasRole('supplier_add'))
									<div class="input-group">
										<select class="form-control select2 supplier" name="supplier" id="supplier">
											<option value=""></option>
											{{getSuppliers()}}
										</select>
										<span class="input-group-addon btn blue" id="btnAddSupplier">
											<i class="fa fa-plus"></i>
										</span>
									</div>
								@else
									<select class="form-control select2 supplier" name="supplier" id="supplier">
										<option value=""></option>
										{{getSuppliers()}}
									</select>
								@endif
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Order By -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="ordered_by" class="control-label" style="text-align: left;"><strong>{{ trans('lang.ordered_by') }}</strong>
									<span class="required"> * </span>
								</label>
								<select class="form-control select2 ordered_by" name="ordered_by" id="ordered_by">
									<option value=""></option>
									{{getUsers()}}
								</select>
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Purchase Request -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="pr_no" class="control-label" style="text-align: left;"><strong>{{ trans('lang.pr_number') }}</strong>
									<span class="required"> * </span>
								</label>
								<select class="form-control select2 pr_no" name="pr_no" id="pr_no">
									<option value=""></option>
								</select>
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Term of Payment -->
						<div class="col-md-4">
							<div class="form-group">
								<label for="term_pay" class="control-label" style="text-align: left;"><strong>{{ trans('lang.term_of_payment') }}</strong></label>
								<input type="text" class="form-control term_pay" id="term_pay" name="term_pay" length="100" placeholder="{{trans('lang.enter_text')}}">
								<span class="help-block font-red bold"></span>
							</div>
						</div>

						<!-- Status -->
						<div class="col-md-4">
							<div class="form-group">
								<!-- Label -->
								<label for="status" class="control-label" style="text-align: left;"><strong>{{ trans('lang.status') }}</strong></label>
								<!-- Select -->
								<select class="form-control select2 status" name="status" id="status">
									<option value="1" selected>{{trans('lang.pending')}}</option>
									<option value="5">{{trans('lang.draft')}}</option>
								</select>
							</div>
						</div>

						<!-- Note -->
						<div class="col-md-12">
							<div class="form-group">
								<label for="trans_desc" class="control-label" style="text-align: left;"><strong>{{ trans('lang.note') }}</strong></label>
								<textarea class="form-control trans_desc" id="trans_desc" name="desc" length="100" rows="8" placeholder="{{ trans('lang.enter_text') }}"></textarea>
							</div>
						</div>
					</div>
					
					<div class="portlet-body">
						<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover" width="100%" id="table-income">
								<thead>
									<tr style="font-size:12px;">
										<th width="5%" class="text-center all">{{ trans('lang.line_no') }}</th>
										<th width="15%" class="text-center all">{{ trans('lang.items') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.units') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.qty') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.price') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.amount') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.disc(%)') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.disc($)') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.total') }}</th>
										<th width="15%" class="text-center all">{{ trans('lang.remark') }}</th>
										<th width="5%" class="text-center all">{{trans('lang.action')}}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th class="text-center all" colspan="11">{{ trans('lang.no_data_available_in_table') }}</th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-7" ></div>
						<div class="col-md-5" >
								<div class="portlet-body" style="background: #f8f9fb;padding: 12px 12px 0 12px	;border: 1px solid #72aee2;">
									<div class="form form-group">
										<div class="col-md-6">
											<label class="control-label"><strong>{{ trans('lang.sub_total') }}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span>
												<input type="text" readonly class="form-control sub_total" name="sub_total" id="sub_total" value="0" />
											</div>
										</div>
										<div class="col-md-6">
											<label class="control-label"><strong>{{ trans('lang.fee_charge') }}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span>
												<input type="number" onkeyup="calculateGrandTotal()" step="any" class="form-control noscroll fee_charge" id="fee_charge" name="fee_charge" length="50" placeholder="{{trans('lang.enter_text')}}" value="0">
											</div>
											<span class="help-block font-red bold"></span>
										</div>
										<div class="col-md-6">
											<label class="control-label"><strong>{{trans("lang.discount(%)")}}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="font-red">%</i></span>
												<input type="number" class="noscroll disc_perc form-control " onkeyup="totalDiscountPercentage(this)" id="disc_perc" name="disc_perc" length="50" placeholder="{{trans('lang.enter_text')}}" value="0"/>
											</div>
											<span class="help-block font-red bold"></span>
										</div>
										<div class="col-md-6">
											<label class="control-label"><strong>{{trans("lang.discount($)")}}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span>
												<input type="number" step="any"  class="noscroll disc_usd form-control " onkeyup="totalDiscountDollar(this)" id="disc_usd" name="disc_usd" length="50" placeholder="{{trans('lang.enter_text')}}" value="0"/>
											</div>
											<span class="help-block font-red bold"></span>
										</div>
										<div class="col-md-6">
											<label class="control-label"><strong>{{ trans('lang.deposit') }}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span>
												<input type="number" step="any" onkeyup="calculateGrandTotal()" class="form-control noscroll deposit" id="deposit" name="deposit" length="50" placeholder="{{trans('lang.enter_text')}}" value="0"/>
											</div>
											<span class="help-block font-red bold"></span>
										</div>
										<div class="col-md-6">
											<label class="control-label"><strong>{{trans("lang.total_after_deposit_and_discount")}}</strong></label>
											<div class="input-group input-icon right">
												<span class="input-group-addon"><i class="fa fa-dollar font-red"></i></span>
												<input type="hidden" value="0" class="grand_total" id="grand_total" name="grand_total">
												<input type="text" readonly class="form-control last_total" id="last_total" name="last_total" value="0"/>
											</div>
											<span class="help-block font-red bold"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
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
@include('modal.warehouse')
@endsection()

@section('javascript')
<script type="text/javascript">	
	var jsonUnits = [];
	var jsonItems = [];
	var jsonRef   = JSON.parse(convertQuot("{{\App\Model\Order::where('pro_id','=',Session::get('project'))->where('trans_status','!=',0)->get(['ref_no'])}}"));
	
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
				$(".show-message-error").html('{{trans("lang.order_qty_can_not_greater_then_request_qty")}}!');
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
		if(chkValid([".pr_no",".reference_no",".trans_date",".delivery_date",".delivery_address",".ordered_by",".supplier",".line_item",".line_unit",".line_qty",".line_price",".line_disc_perc",".line_disc_usd",".disc_perc",".disc_usd",".status"])){
			if(isSave()){
				if (chkDublicateRef(jsonRef, '#reference_no')) {
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
		}else{
			$(this).prop('disabled', false);
			return false;
		}
	});
	
	function onSetItem(val, row){
		if(val!=null && val!='' && jsonItems){

			$.each(jsonItems.filter(c=>c.item_id==val), function(key, val){
				$('.line_unit_'+row).empty();
				$('.line_unit_'+row).append($('<option></option>').val('').text(''));
				jsonUnits[row] = GetUnit(val.unit_stock);
				$.each(jsonUnits[row], function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				// $('.line_unit_'+row).select2('val', val.unit_purch);
			});
		}
	}

	function onChangeUnit(field, row, pr_unit, pr_qty){
		var item_id = $(".line_item_"+row).val();
		var po_unit = $(field).val();
		var new_qty = 0;

		if(item_id!=null && item_id!='' && po_unit!=null && po_unit!='' && jsonItems && jsonUnits[row]){
			var stock_unit = "";
			var stock_qty = 0;
			$.each(jsonItems.filter(c=>c.item_id==item_id), function(key, value){
				stock_unit = value.unit_stock;
			});
			$.each(jsonUnits[row].filter(c=>c.from_code==pr_unit && c.to_code==stock_unit), function(key, value){
				stock_qty = parseFloat(value.factor) * parseFloat(pr_qty);
			});
			$.each(jsonUnits[row].filter(c=>c.from_code==po_unit && c.to_code==stock_unit), function(key, value){
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
				$(".show-message-error").html('{{trans("lang.order_qty_can_not_greater_then_request_qty")}}!');
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
		var total_amout = 0;
		var total_grand_total = 0;
		
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
		total_amout = parseFloat(qty) * parseFloat(price);
		total_grand_total = (parseFloat(qty) * parseFloat(price)) - parseFloat(disc);
		$(".line_amount_"+row).val(total_amout.toFixed(2));
		$(".line_grand_total_"+row).val(total_grand_total.toFixed(2));
		
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
		$(".sub_total").val(parseFloat(sub_total.toFixed(2)));
		
		calculateGrandTotal();
	}
	
	function initTable(data){
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
				'	<input type="number" value="'+data.qty+'" length="50" step="any" class="form-control noscroll line_qty line_qty_'+data.id+'" onkeyup="enterQty(this, '+data.id+')"  name="line_qty[]" placeholder="{{trans('lang.enter_text')}}"/>'+
				'	<input type="hidden" value="'+data.qty+'" class="line_qty_orig line_qty_orig_'+data.id+'"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" value="'+(parseFloat(data.price)).toFixed(3)+'" length="50" step="any" class="form-control noscroll line_price line_price_'+data.id+'" onkeyup="calculateRecord('+data.id+')"  name="line_price[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="text" readonly value="'+(parseFloat(data.price) * parseFloat(data.qty)).toFixed(2)+'" class="form-control noscroll line_amount line_amount_'+data.id+'" name="line_amount[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" length="50" step="any" class="form-control noscroll line_disc_perc line_disc_perc_'+data.id+'" onkeyup="onDiscountPercentage(this, '+data.id+')" value="0" name="line_disc_perc[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="number" length="50" step="any" class="form-control noscroll line_disc_usd line_disc_usd_'+data.id+'" onkeyup="onDiscountDollar(this, '+data.id+')" value="0" name="line_disc_usd[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="text" readonly value="'+(parseFloat(data.price) * parseFloat(data.qty)).toFixed(2)+'" class="form-control noscroll line_grend_total line_grand_total_'+data.id+'" name="line_grend_total[]"/>'+
				'</td>'+
				'<td>'+
				'	<input type="text" length="100" class="form-control line_reference line_reference_'+data.id+'" name="line_reference[]" placeholder="{{trans('lang.enter_text')}}"/>'+
				'</td>'+
				'<td class="text-center all">'+
				'	<button type="button" class="btn btn-danger" onclick="onRemove(this)" title="{{trans("lang.delete")}}"><i class="fa fa-remove"></i></button>'+
				'</td>'+
			'</tr>');
		$(".line_unit_"+data.id).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		$(".line_qty_"+data.id).ForceNumericOnly();
		$(".label_item_"+data.id).html(data.code+' ('+data.name+')');
		onSetItem(data.item_id, data.id);
		$(".line_unit_"+data.id).select2('val', data.unit);
		$('.line_unit_'+data.id).attr("onchange", "onChangeUnit(this, "+data.id+", '"+data.unit+"',"+data.qty+")");
	}

	function GetItem(po_id) {
		return $.ajax({url:'{{url("/stock/use/GetItem")}}',type:'GET',dataType:'json',data:{po_id:po_id},async:false}).responseJSON;
	}

	function GetUnit(unit_stock) {
		return $.ajax({url:'{{url("/stock/deliv/GetUnit")}}',type:'GET',dataType:'json',data:{unit_stock:unit_stock},async:false}).responseJSON;
	}
	
	document.addEventListener("mousewheel", function(event){
		if(document.activeElement.type === "number" &&
		   document.activeElement.classList.contains("noscroll"))
		{
			document.activeElement.blur();
		}
	});
	
	@if(hasRole('warehouse_add'))
		var objWarehouse = JSON.parse(convertQuot('{{\App\Model\Warehouse::where(["pro_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddWarehouse").on('click', function(event){
			event.preventDefault();
			$('.warehouse-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#old_name').val('');
			$('#name').val('');
			$('.tel').val('N/A');
			$('#user_control').select2('val',null);
			$('.status').select2('val',1);
			$('.address').val('N/A');
			$('.button-submit-warehouse').attr('id','btnSaveWarehouse').attr('name','btnSaveWarehouse').attr('onclick','onSubmitWarehouse(this)');
			$('.button-submit-warehouse').html('{{trans("lang.save")}}');
			$('.warehouse-modal').modal('show');
		});

		function onSubmitWarehouse(field){
			$('.button-submit-warehouse').prop('disabled', true);
			if(chkValid([".name",".user_control"])){
				if(chkDublicateName(objWarehouse, '#name')){
					$.ajax({
						url :'{{url("warehouse/save")}}',
						type:'POST',
						data:$('.warehouse-form').serialize(),
						success:function(data){
							if(data){
								$("#delivery_address").append($('<option></option>').val(data.id).text(data.name));
								$("#delivery_address").select2('val', data.id);
							}
							$('.warehouse-modal').modal('hide');
							$('.button-submit-warehouse').prop('disabled', false);
						},error:function(){
							$('.warehouse-modal').modal('hide');
							$('.button-submit-warehouse').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-warehouse').prop('disabled', false);
					return false;					
				}
			}else{
				$('.button-submit-warehouse').prop('disabled', false);
				return false;
			}
		}
	@endif

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

	function calculateGrandTotal(){
		var sub_total = $("#sub_total").val();
		var fee_charge = $("#fee_charge").val();
		var discount = $("#disc_usd").val();
		var deposit = $("#deposit").val();
		if (sub_total!='' && sub_total!=null && discount!='' && discount!=null && deposit!='' && deposit!=null && parseFloat(sub_total)!=0) {
			$("#last_total").val((((parseFloat(sub_total) + parseFloat(fee_charge))  - parseFloat(discount))-parseFloat(deposit)).toFixed(2));
			$("#grand_total").val(((parseFloat(sub_total) + parseFloat(fee_charge))  - parseFloat(discount)).toFixed(2));
		}
	}

	function totalDiscountDollar(field){
		var sub_total = $("#sub_total").val();
		var disc = 0;
		var val = $(field).val();
		if (sub_total!='' && sub_total!=null && parseFloat(sub_total)!=0) {
			if(val=='' || val==null){
				$("#disc_perc").val(0);
				$("#disc_perc").attr('readonly', false);
			}else{
				disc = (parseFloat(val) * 100) / parseFloat(sub_total);
				$("#disc_perc").val(disc.toFixed(3));
				$("#disc_perc").attr('readonly', true);
			}
			calculateGrandTotal();
		}
	}
	
	function totalDiscountPercentage(field){
		var sub_total = $("#sub_total").val();
		var disc = 0;
		var val = $(field).val();
		if (sub_total!='' && sub_total!=null && parseFloat(sub_total)!=0) {
			if(val=='' || val==null){
				$("#disc_usd").val(0);
				$("#disc_usd").attr('readonly', false);
			}else{
				disc = (parseFloat(val) * parseFloat(sub_total)) / 100;
				$("#disc_usd").val(disc.toFixed(3));
				$("#disc_usd").attr('readonly', true);
			}
			calculateGrandTotal();
		}		
	}
	
	$(document).ready(function(){
		$('#trans_date,.delivery_date').val(formatDate('{{date('Y-m-d')}}'));
		$("#trans_date,.delivery_date").datepicker({
			format: "{{getSetting()->format_date}}",
            autoclose: true,
            pickerPosition: "bottom-right"
		});
		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
		$("#reference_no").on('change', function(){
	    	chkDublicateRef(jsonRef, '#reference_no');
	    });
		$('.select2').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		
		$('.pr_no').select2({
		  width:'100%',
		  allowClear:'true',
		  placeholder:'{{trans("lang.please_choose")}}',
		  ajax: {
		    url: '{{url("/purch/request/GetRef")}}',
		    dataType:"json",
		    data: function (params) {
		      var query = {
		        q: params.term
		      }
		      return query;
		    },
		    async:true,
		    success:function(data){
		    	// jsonItems = data.data;
		    },
		    processResults: function (data) {
		      return {
		        results: data.data
		      };
		    }
		  }
		});
		
		$("#pr_no").on('change', function(){
			var val = $(this).val();
			if(val!=null && val!=''){
				var _token = $("input[name=_token]").val();
				$.ajax({
					url :'{{url("purch/request/remotePR")}}',
					type:'POST',
					data:{
						'_token': _token,
						'pr_id': val,
					},
					success:function(data){
						if(data){
							var grand_total = 0;
							$("#table-income tbody").empty();
							jsonItems = data;
							$.each(data,function(key, row){
								initTable(row);
								grand_total = grand_total + (parseFloat(row.price) * parseFloat(row.qty));
							});
							$(".sub_total").val(grand_total.toFixed(2));
							$(".fee_charge").val(0);
							$(".disc_perc").val(0);
							$(".disc_usd").val(0);
							$(".deposit").val(0);
							$(".grand_total").val(grand_total.toFixed(2));
							$(".last_total").val(grand_total.toFixed(2));
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
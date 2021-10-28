@extends('layouts.app')

@section('content')
<style>
	.btnAdd{
		cursor: pointer;
	}
	#table-income tbody tr td{
		vertical-align: middle;
		
	}
	#table-income tbody tr td.center{
		text-align: center;
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
									<div class="row" >
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
												<label for="reference" class="col-md-4 control-label">
													<strong>{{ trans('lang.reference') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-8">
													<input class="form-control reference"​​ length="100" type="text" id="reference" name="reference" placeholder="{{ trans('lang.enter_text') }}">
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											<div class="form-group">
												<label for="warehouse_id" class="col-md-4 control-label"><strong>{{ trans('lang.warehouse') }}</strong>
													<span class="required">  </span>
												</label>
												<div class="col-md-8">
													@if(hasRole('warehouse_add'))
														<div class="input-group">
															<select class="form-control warehouse_id" id="warehouse_id">
																<option value=""></option>
																{{getWarehouse()}}
															</select>
															<span class="input-group-addon btn blue" id="btnAddWarehouse">
																<i class="fa fa-plus"></i>
															</span>
														</div>
													@else
														<select class="form-control warehouse_id" id="warehouse_id">
															<option value=""></option>
															{{getWarehouse()}}
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
												<label for="engineer" class="col-md-3 control-label" style="text-align: left;"><strong>{{ trans('lang.engineer') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-9">
													@if(hasRole('constructor_add'))
														<div class="input-group">
															<select class="form-control engineer" name="engineer" id="engineer">
																<option value=""></option>
																{{getConstructor([1])}}
															</select>
															<span class="input-group-addon btn blue" id="btnAddEngineer">
																<i class="fa fa-plus"></i>
															</span>
														</div>
													@else
														<select class="form-control engineer" name="engineer" id="engineer">
															<option value=""></option>
															{{getConstructor([1])}}
														</select>
													@endif
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											@if(getSetting()->usage_constructor==1)
											<div class="form-group">
												<label for="sub_const" class="col-md-3 control-label" style="text-align: left;"><strong>{{ trans('lang.sub_const') }}</strong>
													<span class="required"> * </span>
												</label>
												<div class="col-md-9">
													@if(hasRole('constructor_add'))
														<div class="input-group">
															<select class="form-control sub_const" name="sub_const" id="sub_const">
																<option value=""></option>
																{{getConstructor([2,3,4,5])}}
															</select>
															<span class="input-group-addon btn blue" id="btnAddSubConstructor">
																<i class="fa fa-plus"></i>
															</span>
														</div>
													@else
														<select class="form-control sub_const" name="sub_const" id="sub_const">
														<option value=""></option>
														{{getConstructor([2,3,4,5])}}
													</select>
													@endif
													<span class="help-block font-red bold"></span>
												</div>
											</div>
											@endif
											<textarea class="form-control trans_desc" id="trans_desc" name="desc" rows="<?php if(getSetting()->usage_constructor==1){echo '3';}else{echo '5';} ?>" length="100" placeholder="{{ trans('lang.enter_text') }}"> </textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-scrollable">
							<table class="table table-striped table-responsive table-bordered table-hover" width="100%" id="table-income">
								<thead>
									<tr style="font-size:12px;">
										<th width="5%" class="text-center all">{{ trans('lang.line_no') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.from_warehouse') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.building') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.on_house') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.items') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.units') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.qty_in_stock') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.remain_qty') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.qty') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.note') }}</th>
										<th width="5%" class="text-center all"><i class='fa fa-plus btnAdd' id="btnAdd"></i></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
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
@include('modal.warehouse')
@include('modal.constructor')
@endsection()

@section('javascript')
<script type="text/javascript">	
	var jsonHouse = [];
	var jsonUnits = [];
	var jsonItems = [];
	var objRef = [];
	
	var i = 1;
	function onRemove(field){
		$(field).parents('tr').remove();
		$("#table-income tbody tr").each(function(k){
			$(this).children("td").children(".line_index").val(lineNo((k+1),3));
			$(this).children("td").children("strong").text(lineNo((k+1),3));
		});
	}

	function GetUnit(unit_stock) {
		return $.ajax({url:'{{url("/stock/deliv/GetUnit")}}',type:'GET',dataType:'json',data:{unit_stock:unit_stock},async:false}).responseJSON;
	}

	function GetItem(query) {
		return $.ajax({url:'{{url("/stock/use/GetItem")}}',type:'GET',dataType:'json',data:{q:query},async:false}).responseJSON;
	}

	// function GetHouse(street_id) {
	// 	return $.ajax({url:'{{url("/stock/use/GetHouse")}}',type:'GET',dataType:'json',data:{id:street_id},async:false}).responseJSON;
	// }

	function GetHouse(building_id){
		return $.ajax({url:'{{url("/repository/getHousesByAllTrigger")}}',type:'GET',dataType:'json',data:{building_id:building_id},async:false}).responseJSON;
	}

	function GetStreet(query) {
		return $.ajax({url:'{{url("/stock/use/GetStreet")}}',type:'GET',dataType:'json',data:{q:query},async:false}).responseJSON;
	}

	function GetRef(){
		return $.ajax({url:'{{url("/stock/use/GetRef")}}',type:'GET',dataType:'json',async:false}).responseJSON;
	}

	function isDuplicateArray(err) {
        var duplicate = false;
        err.sort();
        var current = null;
        var cnt = 1;
        for (var i = 0; i < err.length; i++) {
            if (err[i] != current) {
                current = err[i];
            } else {
                cnt++;
            }
        }
        if (cnt > 1) {
            duplicate = true;
        }else{
            duplicate = false;
        }
        return duplicate;
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
	
	function isSave(){
		var isValid = true;
		var dupArray = [];
		$(".check_row").each(function(i){
			if($(this).val()!=null && $(this).val()!='' && typeof $(this).val()!=undefined){
				dupArray[i] = $(this).val();
			}
		});
		console.log(dupArray);
		if (isDuplicateArray(dupArray)==true) {
			isValid = false;
			$(".show-message-error").html('{{trans("lang.some_record_is_dublicate")}}');
		}else{
			$(".line_use_qty").each(function(){
				var boq_set = $(this).next().next().val();
				var stock_qty = $(this).next().val();
				var use_qty = $(this).val();
				
				if(parseFloat(boq_set) == -1){
					if(parseFloat(use_qty) > parseFloat(stock_qty)){
						isValid = false;
						$(this).css('border','1px solid #e43a45');
						$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
					}else{
						$(this).css('border','1px solid #c2cad8');
						$(".show-message-error").empty();
					}
				}else{
					if(parseFloat(use_qty) > parseFloat(boq_set)){
						isValid = false;
						$(this).css('border','1px solid #e43a45');
						$(".show-message-error").html('{{trans("lang.greater_than_boq_qty")}}!');
					}else if(parseFloat(use_qty) > parseFloat(stock_qty)){
						isValid = false;
						$(field).css('border','1px solid #e43a45');
						$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
					}else{
						$(this).css('border','1px solid #c2cad8');
						$(".show-message-error").empty();
					}
				}
			});
		}
		return isValid;
	}

	$('#save_close,#save_new').on('click',function(){
		$(this).prop('disabled', true);
		var errorClass =$("input[class*='error-qty']");
		if(errorClass.length == 0){
			if(chkValid([".reference_no",".trans_date",".reference",".trans_desc",".line_from_warehouse",".line_on_house",".line_street",".line_item",".line_unit",".line_use_qty",<?php if(getSetting()->usage_constructor==1){echo '".sub_const",';} ?>".engineer"])){
				if(isSave()){
					objRef = GetRef();
					if (chkReference(objRef, '#reference')) {
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
		}else{
				$(this).prop('disabled', false);
				return false;
			}
	});
	
	function onChangeWarehouse(field, row){
		var val = $(field).val();
		if(val!=null && val!=''){
			// $('.line_item_'+row).empty();
			// $('.line_item_'+row).append($('<option></option>').val('').text(''));
			// $('.line_item_'+row).select2('val', null);
			// $('.line_item_'+row).append(option);

			// $('.line_item_'+row).empty();
			$('.line_unit_'+row).empty();
			$('.line_unit_'+row).append($('<option></option>').val('').text(''));
			$('.line_unit_'+row).select2('val', null);
		}
	}
	function onChangeBuilding(field,row){
		var val = $(field).val();
		jsonHouse = GetHouse(val);
		if(val!=null && val!='' && jsonHouse){
			$('.line_on_house_'+row).empty();
			$('.line_on_house_'+row).append($('<option></option>').val('').text(''));
			$('.line_on_house_'+row).select2('val', null);
			$.each(jsonHouse, function(key ,value){
				$('.line_on_house_'+row).append($('<option></option>').val(value.id).text(value.house_no));
			});
		}
	}
	function onChangeStreet(field, row){
		var val = $(field).val();
		jsonHouse = GetHouse(val);
		if(val!=null && val!='' && jsonHouse){
			$('.line_on_house_'+row).empty();
			$('.line_on_house_'+row).append($('<option></option>').val('').text(''));
			$('.line_on_house_'+row).select2('val', null);
			$.each(jsonHouse, function(key ,value){
				$('.line_on_house_'+row).append($('<option></option>').val(value.id).text(value.house_no));
			});
		}
	}

	function onChangeBuilding(field, row){
		var val = $(field).val();
		jsonHouse = GetHouse(val);
		if(val!=null && val!='' && jsonHouse){
			$('.line_on_house_'+row).empty();
			$('.line_on_house_'+row).append($('<option></option>').val('').text(''));
			$('.line_on_house_'+row).select2('val', null);
			$.each(jsonHouse, function(key ,value){
				$('.line_on_house_'+row).append($('<option></option>').val(value.id).text(value.house_no));
			});
		}
	}
	
	function onChangeHouse(field, row){
		var val = $(field).val();
		if(val!=null && val!='' && jsonHouse){
			$('.line_item_'+row).select2('val', null);
			$('.line_unit_'+row).empty();
			$('.line_unit_'+row).append($('<option></option>').val('').text(''));
			$('.line_unit_'+row).select2('val', null);
		}
	}
	
	function onChangeItem(field, row){
		var val = $(field).val();
		if(val!=null && val!='' && jsonItems){
			$.each(jsonItems.filter(c=>c.id==val), function(key, val){
				$('.line_unit_'+row).empty();
				$('.line_unit_'+row).append($('<option></option>').val('').text(''));
				jsonUnits = GetUnit(val.unit_stock);
				$.each(jsonUnits, function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				$('.line_unit_'+row).select2('val', val.unit_usage);
			});
		}
	}
	
	function onChangeUnit(field, row){
		var from_warehouse_id = $(".line_from_warehouse_"+row).val();
		var trans_date = $(".trans_date").val();
		var on_house_id = $(".line_on_house_"+row).val();
		var item_id = $(".line_item_"+row).val();
		var unit = $(field).val();
		if(from_warehouse_id!=null && from_warehouse_id!='' && on_house_id!=null && on_house_id!='' && item_id!=null && item_id!='' && unit!=null && unit!='' && trans_date!=null && trans_date!=''){
			var _token = $("input[name=_token]").val();
			$.ajax({
				url :'{{url("stock/use/remoteItem")}}',
				type:'POST',
				data:{
					'_token': _token,
					'warehouse_id':from_warehouse_id,
					'house_id':on_house_id,
					'item_id':item_id,
					'unit':unit,
					'trans_date':trans_date,
				},
				success:function(data){
					var stock_qty = parseFloat(data.stock_qty);
					var boq_qty = parseFloat(data.boq_set);
					var remain_qty = parseFloat(data.remain_boq);
					var stock_sty_with_unit = stock_qty.toFixed(4) + unit;
					// var boq_qty_with_unit = boq_qty.toFixed(4) + unit;
					var remain_boq_qty_with_unit = remain_qty.toFixed(4) + " | " + unit;
					if(data.boq_set == -1){
                        var boq_qty_with_unit = "Boq not set";
					}else{
						var boq_qty_with_unit = boq_qty.toFixed(4) + unit;
					}
					$('.line_stock_qty_'+row).val(data.stock_qty);
					$('.line_boq_set_'+row).val(data.boq_set);
					$(".stock_qty_"+row).text(stock_sty_with_unit);
					$(".remain_qty_"+row).text(remain_boq_qty_with_unit);
				},error:function(){
					$('.line_stock_qty_'+row).val(0);
					$('.line_boq_set_'+row).val(0);
					$(".stock_qty_"+row).text(0);
					$(".remain_qty_"+row).text(0);
					console.log('error get qty stock.');
				}
			});
		}
		$(".line_use_qty_"+row).val('');
		$(".check_row_"+row).val(from_warehouse_id+"_"+on_house_id+"_"+item_id);
	}
	
	function enterQtyUsage(field, row){
		var stock_qty = $(".line_stock_qty_"+row).val();
		var boq_set = $(".line_boq_set_"+row).val();
		var use_qty = $(field).val();
		if(boq_set!=null && boq_set!='' && boq_set!=null && stock_qty!=null && stock_qty!='' && use_qty!=null && use_qty!=''){
			if(parseFloat(boq_set) == -1){
				if(parseFloat(use_qty) > parseFloat(stock_qty)){
					$(field).css('border','1px solid #e43a45');
					$(field).addClass('error-qty');
					$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
				}else{
					$(field).css('border','1px solid #c2cad8');
					$(field).removeClass('error-qty');
					$(".show-message-error").empty();
				}
			}else{
				if(parseFloat(use_qty) > parseFloat(boq_set)){
					$(field).css('border','1px solid #e43a45');
					$(field).addClass('error-qty');
					$(".show-message-error").html('{{trans("lang.greater_than_boq_qty")}}!');
				}else if(parseFloat(use_qty) > parseFloat(stock_qty)){
					$(field).css('border','1px solid #e43a45');
					$(field).addClass('error-qty');
					$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
				}else{
					$(field).css('border','1px solid #c2cad8');
					$(field).removeClass('error-qty');
					$(".show-message-error").empty();
				}
			}
		}else{
			$(field).css('border','1px solid #c2cad8');
			$(field).removeClass('error-qty');
			$(".show-message-error").empty();
		}
	}

	$("#btnAdd").on('click',function(){
		var line_row = $("#table-income tbody tr").length;
		if(line_row<=99){
			$(".show-message-error").empty();
			$("#table-income tbody").append('<tr>'+
					'<td class="text-center all" style="vertical-align: middle !important;">'+
					'	<input type="hidden" class="line_index line_index_'+i+'" name="line_index[]" value="'+lineNo((line_row+1),3)+'" />'+
					'	<input type="hidden" class="check_row check_row_'+i+'" value="" />'+
					'	<strong>'+lineNo((line_row+1),3)+'</strong>'+
					'</td>'+
					'<td>'+
					'	<select class="form-control line_from_warehouse line_from_warehouse_'+i+'" onchange="onChangeWarehouse(this, '+i+')" name="line_from_warehouse[]">'+
					'		<option value=""></option>'+
					'		{{getWarehouse()}}'+
					'	</select>'+
					'</td>'+
					// '<td>'+
					// '	<select class="form-control line_zone line_zone_'+i+'" onchange="onChangeStreet(this, '+i+')" name="line_street[]">'+
					// '		<option value=""></option>'+
					// '		{{getSystemData("ST")}}'+
					// '	</select>'+
					// '</td>'+
					// '<td>'+
					// '	<select class="form-control line_block line_block_'+i+'" onchange="onChangeStreet(this, '+i+')" name="line_street[]">'+
					// '		<option value=""></option>'+
					// '		{{getSystemData("ST")}}'+
					// '	</select>'+
					// '</td>'+
					'<td>'+
					'	<select class="form-control line_building line_building_'+i+'" onchange="onChangeBuilding(this, '+i+')" name="line_building[]">'+
					'		<option value=""></option>'+
					'		{{getSystemData("BD")}}'+
					'	</select>'+
					'</td>'+
					// '<td>'+
					// '	<select class="form-control line_street line_street_'+i+'" onchange="onChangeStreet(this, '+i+')" name="line_street[]">'+
					// '		<option value=""></option>'+
					// '		{{getSystemData("ST")}}'+
					// '	</select>'+
					// '</td>'+
					'<td>'+
					'	<select class="form-control line_on_house line_on_house_'+i+'" onchange="onChangeHouse(this, '+i+')" name="line_on_house[]">'+
					'		<option value=""></option>'+
					'	</select>'+
					'</td>'+
					'<td>'+
					'	<select class="form-control line_item line_item_'+i+'" name="line_item[]" onchange="onChangeItem(this, '+i+')">'+
					'		<option value=""></option>'+
					'	</select>'+
					'</td>'+
					'<td>'+
					'	<select class="form-control line_unit line_unit_'+i+'" name="line_unit[]" onchange="onChangeUnit(this, '+i+')">'+
					'		<option value=""></option>'+
					'	</select>'+
					'</td>'+
					'<td class="center">'+
					'	<span class="center stock_qty stock_qty_'+i+'"><span/>'+
					'</td>'+
					'<td class="center">'+
					'	<span class="center remain_qty remain_qty_'+i+'"><span/>'+
					'</td>'+
					'<td>'+
					'	<input type="number" length="50" step="any" class="noscroll form-control line_use_qty line_use_qty_'+i+'" name="line_use_qty[]" onkeyup="enterQtyUsage(this, '+i+')" />'+
					'	<input type="hidden" class="form-control line_stock_qty line_stock_qty_'+i+'" name="line_stock_qty[]"/>'+
					'	<input type="hidden" class="form-control line_boq_set line_boq_set_'+i+'" name="line_boq_set[]"/>'+
					'</td>'+
					'<td>'+
					'	<input type="text" length="100" class="form-control line_note line_note_'+i+'" name="line_note[]"/>'+
					'</td>'+
					'<td class="text-center all">'+
					'	<button type="button" class="btn btn-danger" onclick="onRemove(this)" title="{{trans("lang.delete")}}"><i class="fa fa-remove"></i></button>'+
					'</td>'+
				'</tr>');
			$.fn.select2.defaults.set("theme", "classic");
			$(".line_on_house_"+i+",.line_from_warehouse_"+i+",.line_building_"+i+",.line_unit_"+i).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
			var warehouse_id = $("#warehouse_id").val();

			var itemSelect = $('.line_item_'+i);
			itemSelect.select2({
			  width:'100%',
			  allowClear:'true',
			  placeholder:'{{trans("lang.please_choose")}}',
			  ajax: {
			    url: '{{url("/stock/use/GetItem")}}',
			    dataType:"json",
			    data: function (params) {
			      var query = {
			        q: params.term || '',
			        page:params.page || 1,
			        limit:params.limit
			      }
			      return query;
			    },
			    async:true,
			    success:function(data){
			    	jsonItems = data.data;
			    },
			    processResults: function (data) {
			      return {
			        results: data.data,
			        more: (data.to < data.total),
			        page: (data.current_page + 1),
			        limit: data.per_page
			      };
			    }
			  }
			});

			if(warehouse_id!='' && warehouse_id!=null){
				$(".line_from_warehouse_"+i).select2('val', warehouse_id);
			}
			$(".line_use_qty_"+i).ForceNumericOnly();
			i++;
		}else{
			$(".show-message-error").html('{{trans("lang.not_more_than_100")}}!');
		}
	});
	
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
								$("#warehouse_id").append($('<option></option>').val(data.id).text(data.name));
								$("#warehouse_id").select2('val', data.id);
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

	@if(hasRole('constructor_add'))
		var objConstructor = JSON.parse(convertQuot('{{\App\Model\Constructor::get(["name","id_card"])}}'));
		$("#btnAddEngineer").on('click', function(event){
			$('.constructor-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#id_card').val('');
			$('#old_id_card').val('');
			$('#con-name').val('');
			$('#con-tel').val('');
			$('#con-type option[value=1]').prop('disabled', false);
			$('#con-type option[value!=1]').prop('disabled', true);
			$.fn.select2.defaults.set("theme", "classic");
			$('#con-type').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
			$('#con-type').select2('val', null);
			$('#con-status').select2('val',1);
			$('#con-address').val('');
			$('.button-submit-constructor').attr('id','btnSaveEngineer').attr('name','btnSaveEngineer').attr('onclick','onSubmitEngineer(this)');
			$('.button-submit-constructor').html('{{trans("lang.save")}}');
			$('.constructor-modal').modal('show');
		});

		function onSubmitEngineer(field){
			$('.button-submit-constructor').prop('disabled', true);
			if(chkValid([".id_card",".con-name",".con-tel",".con-type",".con-status",".con-address"])){
				if(chkDublicateName(objConstructor, '#id_card')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("constr/save")}}',
						type:'POST',
						data:$('.constructor-form').serialize(),
						success:function(data){
							if(data){
								$("#engineer").append($('<option></option>').val(data.id).text(data.id_card+' ('+data.name+')'+' - '+data.tel));
								$("#engineer").select2('val', data.id);
							}
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						},error:function(){
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-constructor').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit-constructor').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddSubConstructor").on('click', function(event){
			$('.constructor-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#id_card').val('');
			$('#old_id_card').val('');
			$('#con-name').val('');
			$('#con-tel').val('');
			$('#con-type option[value!=1]').prop('disabled', false);
			$('#con-type option[value=1]').prop('disabled', true);
			$.fn.select2.defaults.set("theme", "classic");
			$('#con-type').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
			$('#con-type').select2('val', null);
			$('#con-status').select2('val',1);
			$('#con-address').val('');
			$('.button-submit-constructor').attr('id','btnSaveSubConstructor').attr('name','btnSaveSubConstructor').attr('onclick','onSubmitSubConstructor(this)');
			$('.button-submit-constructor').html('{{trans("lang.save")}}');
			$('.constructor-modal').modal('show');
		});

		function onSubmitSubConstructor(field){
			$('.button-submit-constructor').prop('disabled', true);
			if(chkValid([".id_card",".con-name",".con-tel",".con-type",".con-status",".con-address"])){
				if(chkDublicateName(objConstructor, '#id_card')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("constr/save")}}',
						type:'POST',
						data:$('.constructor-form').serialize(),
						success:function(data){
							if(data){
								$("#sub_const").append($('<option></option>').val(data.id).text(data.id_card+' ('+data.name+')'+' - '+data.tel));
								$("#sub_const").select2('val', data.id);
							}
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						},error:function(){
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-constructor').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit-constructor').prop('disabled', false);
				return false;
			}
		}
	@endif
	
	$(document).ready(function(){
		$.fn.select2.defaults.set("theme", "classic");
		$(".engineer, .sub_const, .warehouse_id").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		
		$('#trans_date').val(formatDate('{{date('Y-m-d')}}'));
		$("#trans_date").datepicker({
			format: "{{getSetting()->format_date}}",
            autoclose: true,
            pickerPosition: "bottom-right"
		});

		$('#trans_date').on('change', function(){
			$('.line_boq_set, .line_stock_qty, .line_use_qty, .line_note').val('');

			$('.line_unit').each(function(){
				$(this).empty();
				$(this).append($('<option></option>').val('').text(''));
				$(this).select2('val', null);
			});
			$('.line_item').each(function(){
				$(this).empty();
				$(this).append($('<option></option>').val('').text(''));
				$(this).select2('val', null);
			});
			$('.line_on_house').each(function(){
				$(this).empty();
				$(this).append($('<option></option>').val('').text(''));
				$(this).select2('val', null);
			});
			$('.line_street').each(function(){
				$(this).select2('val', null);
			});
			$('.line_from_warehouse').each(function(){
				$(this).select2('val', null);
			});
		});

		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
		
		$('#btnAdd').trigger('click');
		
		setInterval(function() {
			$.ajax({
				url :'{{url("prefix/gen/USE/Usage/ref_no")}}',
				type:'get',
				success:function(data){
					if(data!=null && data!=''){
						$('#reference_no').attr("readonly", true);
						$('#reference_no').val(data);
					}else{
						$('#reference_no').attr("readonly", false);
						// $('#reference_no').val('');
					}
				},error:function(){}
			});
		}, 3000);
	});
</script>
@endsection()
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
								</select>
							</div>
							<span class="help-block font-red bold"></span>
						</div>
					</div>
					<div class="portlet-body">
						<div class="table-scrollable">
							<table class="table table-striped table-bordered table-hover" width="100%" id="table-income">
								<thead>
									<tr style="font-size:12px;">
										<th width="5%" class="text-center all">{{ trans('lang.line_no') }}</th>
										<th width="25%" class="text-center all">{{ trans('lang.items') }}</th>
										<th width="15%" class="text-center all">{{ trans('lang.units') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.ordered_qty') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.recieve_qty') }}</th>
										{{-- @if(getSetting()->is_costing==1)
										<th width="10%" class="text-center all">{{ trans('lang.cost') }}</th>
										@endif --}}
										<th width="10%" class="text-center all">{{ trans('lang.to_warehouse') }}</th>
										<th width="10%" class="text-center all">{{ trans('lang.remark') }}</th>
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
	var jsonUnits = [];
	var jsonItems = [];

	function GetItem(po_id) {
		return $.ajax({url:'{{url("/stock/deliv/GetItem")}}',type:'GET',dataType:'json',data:{po_id:po_id},async:false}).responseJSON;
	}

	function GetUnit(unit_stock) {
		return $.ajax({url:'{{url("/stock/deliv/GetUnit")}}',type:'GET',dataType:'json',data:{unit_stock:unit_stock},async:false}).responseJSON;
	}
	
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
		if(chkValid([".reference_no",".trans_date",".supplier",".trans_desc",".po_no",".line_item",".line_unit",".line_qty",".line_warehouse",".line_cost"])){
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
		if(val!=null && val!='' && jsonItems){
			$.each(jsonItems.filter(a=>a.item_id==val),function(key,val){
				$('.line_unit_'+row).empty();
				$('.line_unit_'+row).append($('<option></option>').val('').text(''));
				jsonUnits[row] = GetUnit(val.unit_stock);
				$.each(jsonUnits[row], function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				// $('.line_unit_'+row).select2('val',val.unit_purch);
			});
		}
	}
	
	function onChangeUnit(field, row, po_unit, po_qty){
		var item_id    = $(".line_item_"+row).val();
		var deliv_unit = $(field).val();
		var new_qty    = 0;
		if(item_id!=null && item_id!='' && po_unit!=null && po_unit!='' && jsonItems && jsonUnits[row]){
			var stock_unit = "";
			var stock_qty = 0;
			$.each(jsonItems.filter(c=>c.item_id==item_id), function(key, value){
				stock_unit = value.unit_stock;
			});
			$.each(jsonUnits[row].filter(c=>c.from_code==po_unit).filter(d=>d.to_code==stock_unit), function(key, value){
				stock_qty = parseFloat(value.factor) * parseFloat(po_qty);
			});
			$.each(jsonUnits[row].filter(c=>c.from_code==deliv_unit).filter(d=>d.to_code==stock_unit), function(key, value){
				new_qty = parseFloat(stock_qty) / parseFloat(value.factor);
			});
		}
		$(".line_qty_"+row).val(new_qty);
		$(".line_qty_orig_"+row).val(new_qty);
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
	}
	
	function initTable(data, warehouse_id){
		var str = "";
			str += '<tr>';
			str += '<td class="text-center all" style="vertical-align: middle !important;">';
			str += '<input type="hidden" class="line_index line_index_'+data.id+'" name="line_index[]" value="'+data.line_no+'" />';
			str += '<strong>'+data.line_no+'</strong>';
			str += '</td>';
			str += '<td class="text-left all" style="vertical-align: middle !important;">';
			str += '<input type="hidden" class="line_item line_item_'+data.id+'" value="'+data.item_id+'" name="line_item[]"/>';
			str += '<strong class="label_item label_item_'+data.id+'"></strong>';
			str += '</td>';
			str += '<td>';
			str += '<select class="form-control line_unit line_unit_'+data.id+'" name="line_unit[]">';
			str += '<option value=""></option>';
			str += '</select>';
			str += '</td>';
			str += '<td>';
				str += '<input type="text" name="order_qty[]" readonly value="'+data.qty+'" class="form-control line_qty_orig line_qty_orig_'+data.id+'"/>';
			str += '</td>';
			str += '<td>';
			str += '<input type="number" value="'+data.qty+'" length="50" step="any" class="form-control noscroll line_qty line_qty_'+data.id+'" onkeyup="enterQty(this, '+data.id+')"  name="line_qty[]"/>';
			str += '<input type="hidden" value="'+data.qty+'" class="line_qty_orig line_qty_orig_'+data.id+'"/>';
			str += '<input type="hidden" value="'+data.price+'" name="line_cost[]" class="line_price line_cost_'+data.id+'"/>';
			str += '</td>';
			// if (parseInt("{{getSetting()->is_costing}}")==1) {
			// 	str += '<td>';
			// 	str += '<input type="number" length="50" step="any" class="form-control noscroll line_cost line_cost_'+data.id+'" name="line_cost[]" value="'+data.price+'"/>';
			// 	str += '</td>';
			// }
			str += '<td>';
			str += '<select class="form-control line_warehouse line_warehouse_'+data.id+'" name="line_warehouse[]">';
			str += '<option value=""></option>';
			str += '{{getWarehouse()}}';
			str += '</select>';
			str += '</td>';
			str += '<td>';
			str += '<input type="text" length="100" class="form-control line_reference line_reference_'+data.id+'" name="line_reference[]"/>';
			str += '</td>';
			str += '<td class="text-center all">';
			str += '<button type="button" class="btn btn-danger" onclick="onRemove(this)" title="{{trans("lang.delete")}}"><i class="fa fa-remove"></i></button>';
			str += '</td>';
			str += '</tr>';

		$("#table-income tbody").append(str);
		$.fn.select2.defaults.set("theme", "classic");
		$(".line_unit_"+data.id+",.line_warehouse_"+data.id).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		$(".line_warehouse_"+data.id).select2('val', warehouse_id);
		$(".line_qty_"+data.id).ForceNumericOnly();
		onSetItem(data.item_id,data.id);
		$(".label_item_"+data.id).html(data.code+' ('+data.name+')');
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
	
	$(document).ready(function(){
		$.fn.select2.defaults.set("theme", "classic");
		$(".supplier").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		
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
		
		$('.po_no').select2({
		  width:'100%',
		  allowClear:'true',
		  placeholder:'{{trans("lang.please_choose")}}',
		  ajax: {
		    url: '{{url("/purch/order/GetRef/0")}}',
		    dataType:"json",
		    data: function (params) {
		      var query = {
		        q: params.term
		      }
		      return query;
		    },
		    async:true,
		    processResults: function (data) {
				
		      return {
		        results: data.data
		      };
			  
		    }
		  }
		});
		
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
							$(".supplier").select2('val', data.head.sup_id);
							$("#table-income tbody").empty();
							jsonItems = data.body;
							$.each(data.body,function(key, row){
								initTable(row, data.head.delivery_address);
							});
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
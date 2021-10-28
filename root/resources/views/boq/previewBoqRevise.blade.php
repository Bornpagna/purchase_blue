@extends('layouts.app')

@section('stylesheet')
	<style>
		td.details-control {
            background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
            cursor: pointer !important;
        }
        tr.shown td.details-control {
            background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
        }
		.boq-pointer{
		cursor: pointer;
	}
	</style>
@endsection
@section('content')
<form class="priveiw-boq-form form-horizontal" method="POST"  enctype="multipart/form-data" action="{{url('boqs/uploadBoqPreviewRevise')}}">
	<?php $items = \App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage']); ?>
	{{csrf_field()}}
	<input type="hidden" name="boq_id" value="{{$boq_id}}" />
	<input type="hidden" name="house_id" value="{{$house_id}}" />
	<div class="row" role="dialog">
		<div class="col-md-12">
			<div class="portlet light bordered">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-plus font-dark"></i>
						<span class="caption-subject bold font-dark uppercase"> {{ trans('lang.boq') }}</span>
						<span class="caption-helper">{{ trans('lang.preview') }}</span>
					</div>
					<div class="actions">
						<a rounte="" title="Back" class="btn btn-circle btn-icon-only btn-default" id="btnBack">
							<i class="fa fa-reply"></i>
						</a>
					</div>
				</div>
				<div class="portlet-body form">
						<div class="form-group">
							<div class="col-md-12 text-center">
								<span class="show-message-error-boq center font-red bold"></span>
								<input type="hidden" value="" name="" />
							</div>
						</div>
						<table class="table table-hover no-footer" id="table_boq">
							<thead>
								<tr>
									<th class="text-center all" width="2%">{{ trans('lang.no') }}<input type="hidden" name="working_type" id="working_type" /></th>
									<th width="15%" class="text-left all">{{ trans('lang.item_type') }}</th>
									<th width="20%" class="text-left all">{{ trans('lang.code') }}</th>
									<th width="20%" class="text-left all">{{ trans('lang.items') }}</th>
									<th width="15%" class="text-left all" style="min-width:100px;">{{ trans('lang.uom') }}</th>
									<th width="15%" class="text-left all" style="min-width:100px;">{{ trans('lang.qty_std') }}</th>
									<th width="10%" class="text-left all" style="min-width:100px;">{{ trans('lang.qty_add') }}</th>
									<th width="10%" class="text-left all" style="min-width:100px;">{{ trans('lang.cost') }}</th>
									<th width="2%" class="text-center all"><i class='fa fa-plus boq-pointer'> </i></th>
								</tr>
							</thead>
							<tbody>
                                <?php  $i=1; $start=1; ?>
                                @foreach($data  as $key => $value)
                                @if(!empty($value->no) && empty($value->item_type) && empty($value->items_name))                               
                                <tr class="boq_preview_{{$key}} lop_boq_{{$start}}" >
                                    <td><b><ol type="I" start="{{$start}}">  <li></li> </ol> </b></td>
                                    <td><b><input type="hidden" class="form-control" name="working_type[]" value="{{$value->no}}" /> {{$value->no}} <b> </td>
                                    <td><input type="hidden" class="form-control " name="working_type_no[]" value="{{$start}}" /></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><th width="2%" class="text-center all"><a href="javascript:void(0)" onclick="addPreviewNew({{$start}},{{$i}})"> <i class='fa fa-plus boq-pointer'></i> </a> </th>  </td>
                                </tr>
                                <?php $start++; $i=1; ?>
                                @else
								<input type="hidden" value="{{$key}}" class="boq_count_{{$start}}" />
                                <tr class="boq_{{$key}}">
                                    <td> {{$i++}} </td>
                                    <td><input type="text" length="11" class="form-control item_type_boq" name="item_type_{{$start-1}}[]" value="{{$value->item_type}}"></td>
                                    <td><input type="text" length="11" class="form-control code_boq" name="code_{{$start-1}}[]" value="{{$value->code}}"></td>
                                    <td><input type="text" length="11" class="form-control item_name_boq" name="item_name_{{$start-1}}[]" value="{{$value->items_name}}"></td>
                                    <td><input type="text" length="11" class="form-control unit_boq" name="unit_{{$start-1}}[]" value="{{$value->unit}}"></td>
                                    <td><input type="number" length="11" class="form-control qty_std_boq" name="qty_std_{{$start-1}}[]" placeholder="Enter number..." value="{{$value->qty_std}}"></td>
                                    <td><input type="number" length="11" class="form-control qty_add_boq" name="qty_add_{{$start-1}}[]" placeholder="Enter number..." value="{{$value->qty_add}}"></td>
                                    <td><input type="number" length="11" class="form-control cost_boq" name="cost_{{$start-1}}[]" placeholder="Enter number..." value="{{formatQty($value->cost)}}"></td>
                                    <td> <a class="btn btn-sm" onclick="DeletePreviewBOQ({{$key}})"><i class="fa fa-trash"></i></a> </td>
                                </tr>
                                @endif								
                                @endforeach
								<tr class="lop_boq_{{$start}}"> </tr>
                            </tbody>
						</table>
				</div>
	
		<div class="form-actions text-right">
			<button type="buttom" id="save_close" name="save_close" value="1" class="btn green bold">{{ trans('lang.save') }}</button>
			<a class="btn red bold" href																																																																																																																																																																																																																																																																																																																																																																																																								="{{url('boqs')}}" id="btnCancel">{{ trans('lang.cancel') }}</a>
		</div>
	</div>
</form>
@endsection()
@section('javascript')
<script type="text/javascript">
	var index_boq = 0;
	var index_work_type = 1;
	var objName = [];
	var jsonItem = JSON.parse(convertQuot("{{\App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage'])}}"));
	var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc'])}}"));
	var jsonHouse = JSON.parse(convertQuot("{{\App\Model\House::where(['status'=>1])->get(['id','house_no','street_id','house_type','zone_id','block_id','building_id'])}}"));
    $('#save_close').click(function(){
		if(onSubmit()){
		}else{
			return false;
		}
	});
	function onSubmit(){
		var isValid = true;
		$(".item_type_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		$(".code_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		$(".item_name_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		$(".unit_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		$(".qty_std_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		$(".cost_boq").each(function () {
			if ($(this).val()=='' || $(this).val()==null) {
				isValid = false;
				$(this).css("border", "1px solid red");
			} else {
				$(this).css("border", "1px solid #ccc");
			}
		});
		return isValid;
	}
	var value_row = 0;
	var add_value = 1;	
	// var numItems = 1;	
	function addPreviewNew(val,num){			
		var table_boq = $('.lop_boq_'+(val+1));
		add_value= add_value+1;
		var add_values= val+1;
		add_value = add_value+100000;
		var numItem = $('.boq_count_'+(val+1)).length;
		numItem = numItem+1;
		var hmtl = '';
				hmtl+='<tr class="boq_'+(add_value)+'">';				
				hmtl+='<td> '+(numItem)+' </td>';
				hmtl+='<td><input type="text" length="11" class="form-control item_type_boq" name="item_type_'+val+'[]"></td>';
				hmtl+='<td><input type="text" length="11" class="form-control code_boq" name="code_'+val+'[]"></td>';
				hmtl+='<td><input type="text" length="11" class="form-control item_name_boq" name="item_name_'+val+'[]"></td>';
				hmtl+='<td><input type="text" length="11" class="form-control unit_boq" name="unit_'+val+'[]"></td>';
				hmtl+='<td><input type="number" length="11" class="form-control qty_std_boq" name="qty_std_'+val+'[]" placeholder="Enter number"></td>';
				hmtl+='<td><input type="number" length="11" class="form-control qty_add_boq" name="qty_add_'+val+'[]" placeholder="Enter number"></td>';
				hmtl+='<td><input type="number" length="11" class="form-control cost_boq" name="cost_'+val+'[]" placeholder="Enter number"></td>';
				hmtl+='<td> <a class="btn btn-sm" onclick="DeletePreviewBOQ('+(add_value)+')"><i class="fa fa-trash"></i></a> </td>';
			hmtl+='</tr>';
			hmtl+='<input type="hidden" class="boq_count_'+add_values+'" />';
		table_boq.before(hmtl);  
	}
	$(function(){
		$('#sq1,#sp2').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' 
        });
		$('.option-house').on('ifChanged',function(){
			var street = $('#boq-street').val();
			$("#boq-house").empty();
			$("#boq-house").select2('val', null);
			$("#boq-house").append($('<option></option>').val('').text(''));
			if($(this).val()==1){
				$("#boq-street-preview option[value='0']").remove();
				$("#label-house").html('{{trans("lang.house_no")}}');
				if(street!=null && street!="" && jsonHouse){
					$.each(jsonHouse.filter(c=>c.street_id==street),function(key, val){
						$("#boq-house").append($('<option></option>').val(val.id).text(val.house_no));
					});
				}
			}else{
				$("#label-house").html('{{trans("lang.house_type")}}');
				$("#boq-street-preview").append('<option value="0">{{trans("lang.all")}}</option>');
				$("#boq-house").append('{{getSystemData("HT")}}');
			}
		});
		
		$('#boq-zone-preview').on('change', function(){
			getHouses();
		});
		$('#boq-block-preview').on('change', function(){
			getHouses();
		});
		$('#boq-building-preview').on('change', function(){
			getHouses();
		});
		$('#boq-street-preview').on('change', function(){
			getHouses();
		});

		$('#boq-house-type-preview').on('change', function(){
			var type = $(this).val();
			getHouses();
		});

		function getHouses(){
			var params = {
				zone_id: null,
				block_id: null,
				building_id : null,
				street_id: null,
				house_type: null,
			};
			const zoneID    = $('#boq-zone-preview').val();
			const blockID   = $('#boq-block-preview').val();
			const buildingID    = $('#boq-building-preview').val();
			const streetID  = $('#boq-street-preview').val();
			const houseType = $('#boq-house-type-preview').val();

			if(zoneID){
				params.zone_id = zoneID;
			}

			if(blockID){
				params.block_id = blockID;
			}
			if(buildingID){
				params.building_id = buildingID;
			}

			if(streetID){
				params.street_id = streetID;
			}

			if(houseType){
				params.house_type = houseType;
			}
			$.ajax({
				url:"{{url('repository/getHousesByAllTrigger')}}",
				type:'GET',
				data: params,
				success: function(result){
					$("#boq-house").empty();
					$.each(result,function(key, val){
						$("#boq-house-preview").append($('<option></option>').val(val.id).text(val.house_no));
					});
				}
			});			
		}
		
		/* button click save */
		$("#btnAdd").on("click",function(){
			var rounte = $(this).attr('rounte');
			$('.enter-boq-form').attr('action',rounte);
			$('.enter-boq-modal').children().find('div').children().find('h4').html('{{trans("lang.enter_boq")}}');
			$('.boq-button-submit').attr('id','btnEnterBoq').attr('name','btnEnterBoq');
			$('.boq-button-submit').html('{{trans("lang.save")}}');
			
			$('.boq-house').select2('val', null);
			$('.boq-street').select2('val', null);
			
			// var table_boq = $('#table_boq').DataTable();
			// table_boq.row().clear();
			$('.boq-pointer').trigger('click');
			$('.enter-boq-modal').modal('show');
			
			$("#btnEnterBoq").on('click',function(){
				if(chkValid([".boq-zone",".boq-block",".boq-building",".line_item_type",".line_item",".line_unit",".line_qty_std",".line_qty_add"])){
					$('.enter-boq-form').submit();
				}else{
					$('.boq-button-submit').prop('disabled', false);
					return false;
				}
			});
		});
		/* end button click save */
		$("#boq-house").select2({
			closeOnSelect : false,
			placeholder : "Placeholder",
			allowHtml: true,
			allowClear: false,
			tags: true // создает новые опции на лету
		});
	});	
    function DeletePreviewBOQ(val)
    {
        $('.boq_'+val).empty();
    }
</script>
@endsection()

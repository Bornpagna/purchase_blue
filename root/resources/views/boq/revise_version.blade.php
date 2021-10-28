@extends('layouts.app')

@section('stylesheet')
	<style>
		.modal-lg{
			width: 90% !important;
		}
		td.details-control {
            background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
            cursor: pointer !important;
        }
        tr.shown td.details-control {
            background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
        }
		.item_table>tbody>tr>td:first {
			border: 0 !important;
		}

		.select2-container {
		width:100% !important;
		}

		#select2-boq-house-results > .select2-results__option {
		padding-right: 20px;
		vertical-align: middle;
		}
		#select2-boq-house-results > .select2-results__option:before {
		content: "";
		display: inline-block;
		position: relative;
		height: 20px;
		width: 20px;
		border: 2px solid #e9e9e9;
		border-radius: 4px;
		background-color: #fff;
		margin-right: 20px;
		vertical-align: middle;
		}
		#select2-boq-house-results > .select2-results__option[aria-selected=true]:before {
		font-family:fontAwesome;
		content: "\f00c";
		color: #fff;
		background-color: #3d76cc;
		border: 0;
		display: inline-block;
		padding-left: 3px;
		}
		#select2-boq-house-results > .select2-container--default .select2-results__option[aria-selected=true] {
			background-color: #fff;
		}
		#select2-boq-house-results > .select2-container--default .select2-results__option--highlighted[aria-selected] {
			background-color: #eaeaeb;
			color: #272727;
		}
		#select2-boq-house-results > .select2-container--default .select2-selection--multiple {
			margin-bottom: 10px;
		}
		#select2-boq-house-results > .select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
			border-radius: 4px;
		}
		#select2-boq-house-results > .select2-container--default.select2-container--focus .select2-selection--multiple {
			border-color: #3d76cc;
			border-width: 2px;
		}
		#select2-boq-house-results > .select2-container--default .select2-selection--multiple {
			border-width: 2px;
		}
		#select2-boq-house-results > .select2-container--open .select2-dropdown--below {
			
			border-radius: 6px;
			box-shadow: 0 0 10px rgba(0,0,0,0.5);

		}
		#select2-boq-house-results > .select2-selection .select2-selection--multiple:after {
			content: 'hhghgh';
		}
		/* select with icons badges single*/
		#select2-boq-house-results > .select-icon .select2-selection__placeholder .badge {
			display: none;
		}
		#select2-boq-house-results > .select-icon .placeholder {
			display: none;
		}
		#select2-boq-house-results > .select-icon .select2-results__option:before,
		#select2-boq-house-results > .select-icon .select2-results__option[aria-selected=true]:before {
			display: none !important;
			/* content: "" !important; */
		}
		#select2-boq-house-results > .select-icon  .select2-search--dropdown {
			display: none;
		}

		#select2-boq_house_assign_house-results > .select2-results__option {
		padding-right: 20px;
		vertical-align: middle;
		}
		#select2-boq_house_assign_house-results > .select2-results__option:before {
		content: "";
		display: inline-block;
		position: relative;
		height: 20px;
		width: 20px;
		border: 2px solid #e9e9e9;
		border-radius: 4px;
		background-color: #fff;
		margin-right: 20px;
		vertical-align: middle;
		}
		#select2-boq_house_assign_house-results > .select2-results__option[aria-selected=true]:before {
		font-family:fontAwesome;
		content: "\f00c";
		color: #fff;
		background-color: #3d76cc;
		border: 0;
		display: inline-block;
		padding-left: 3px;
		}
		#select2-boq_house_assign_house-results > .select2-container--default .select2-results__option[aria-selected=true] {
			background-color: #fff;
		}
		#select2-boq_house_assign_house-results > .select2-container--default .select2-results__option--highlighted[aria-selected] {
			background-color: #eaeaeb;
			color: #272727;
		}
		#select2-boq_house_assign_house-results > .select2-container--default .select2-selection--multiple {
			margin-bottom: 10px;
		}
		#select2-boq_house_assign_house-results > .select2-container--default.select2-container--open.select2-container--below .select2-selection--multiple {
			border-radius: 4px;
		}
		#select2-boq_house_assign_house-results > .select2-container--default.select2-container--focus .select2-selection--multiple {
			border-color: #3d76cc;
			border-width: 2px;
		}
		#select2-boq_house_assign_house-results > .select2-container--default .select2-selection--multiple {
			border-width: 2px;
		}
		#select2-boq_house_assign_house-results > .select2-container--open .select2-dropdown--below {
			
			border-radius: 6px;
			box-shadow: 0 0 10px rgba(0,0,0,0.5);

		}
		#select2-boq_house_assign_house-results > .select2-selection .select2-selection--multiple:after {
			content: 'hhghgh';
		}
		/* select with icons badges single*/
		#select2-boq_house_assign_house-results > .select-icon .select2-selection__placeholder .badge {
			display: none;
		}
		#select2-boq_house_assign_house-results > .select-icon .placeholder {
			display: none;
		}
		#select2-boq_house_assign_house-results > .select-icon .select2-results__option:before,
		#select2-boq_house_assign_house-results > .select-icon .select2-results__option[aria-selected=true]:before {
			display: none !important;
			/* content: "" !important; */
		}
		#select2-boq_house_assign_house-results > .select-icon  .select2-search--dropdown {
			display: none;
		}
		.padding-20{
			padding:0 20px;
		}
		.upload_boq_tab{
			border-top: 0px !important;
    		border: #ddd solid 1px;
		}
		.upload-nav-tab {
			margin-bottom: 0px !important;
		}
		.padding-content-20{
			padding:20px;
		}
		.color-white{
			background: #fff;
			
		}
		.modal-title {
			color:#000000;
		}
		table.item_table {
			counter-reset: rowNumber;
		}
		
		table.item_table tr td:first-child::before {
			display: table-cell;
			counter-increment: rowNumber;
			content: counter(rowNumber) ".";
			padding-right: 0.3em;
			text-align: right;
		}
		table.item_table tr td:first-child {
			vertical-align: middle;
			font-weight: 700;
		}
	</style>
@endsection

@section('content')
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
					
					@if(isset($rounteExample))
					<div class="btn btn-xs">
						<a title="{{trans('lang.download_example')}}" class="btn btn-circle btn-icon-only btn-default" href="{{$rounteExample}}">
							<i class="fa fa-download"></i><br />{{ trans('lang.download_example') }}
						</a>
						<br /><span>{{ trans('lang.download_example') }}</span>
					</div>
					@endif
					@if(isset($rounteUpload))
					<div class="btn btn-xs">
						<a title="{{trans('lang.upload')}}" class="btn btn-circle btn-icon-only btn-default" rounte="{{$rounteUpload}}" id="btnUpload">
							<i class="fa fa-upload"></i><br />{{trans('lang.upload')}}
						</a>
						<br /><span>{{ trans('lang.upload') }}</span>
					</div>
					@endif
					@if(isset($rounteSave))
					<div class="btn btn-xs">
						<a rounte="{{$rounteSave}}" title="{{trans('lang.add_new')}}" class="btn btn-circle btn-icon-only btn-default" id="btnAdd">
							<i class="fa fa-plus"></i>
						</a>
						<br /><span>{{ trans('lang.add_new') }}</span>
					</div>
					@endif
				</div>
			</div>
			<div class="portlet-body">
				<?php if(Session::has('success')):?>
					<div class="alert alert-success display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.success')}}!</strong> {{Session::get('success')}} 
					</div>
				<?php elseif(Session::has('error')):?>
					<div class="alert alert-danger display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.error')}}!</strong> {{Session::get('error')}} 
					</div>
				<?php endif; ?>
				<?php if(Session::has('bug') && count(Session::get('bug')>0)): ?>
					<?php
						echo '<div class="alert alert-danger display-show"><button class="close" data-close="alert"></button>';
						foreach(Session::get('bug') as $key=>$val){
								echo '<strong>'.trans('lang.error').'!</strong>'.trans('lang.dublicate_at_record').' '.$val['index'].'<br/>';
						}
						echo '</div>';
					?>
				<?php endif; ?>
				<div class="table-responsive" style="overflow: auto;">
				<table class="table table-striped table-bordered table-hover " id="my-table">
					<thead>
						<tr>
							<th style="width: 3%;" class="all"></th>
							<th width="10%" class="text-center all">{{ trans('lang.boq_code') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.zone') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.block') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.building') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.street') }}</th>
							{{-- <th width="10%" class="text-center all">{{ trans('lang.line_no') }}</th> --}}
							<th width="10%" class="text-center">{{ trans('lang.trans_date') }}</th>
							<th width="10%" class="text-center">{{ trans('lang.trans_by') }}</th>
							<th width="10%" class="text-center">{{ trans('lang.trans_ref') }}</th>
							<th width="4%" class="text-center">{{ trans('lang.version') }}</th>
							<th width="8%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Varian -->

@include('modal.boq')
@include('modal.edit_boq')
@include('modal.revise_boq_house')
@include('modal.upload')
@include('modal.upload_revise')
@include('modal.assign_house')
@include('modal.confirm')
@endsection()

@section('javascript')
<script type="text/javascript">
	var index_boq = 0;
	var index_work_type = 1;
	var objName = [];
	var jsonItem = JSON.parse(convertQuot("{{\App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage'])}}"));
	var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc'])}}"));
	var jsonHouse = JSON.parse(convertQuot("{{\App\Model\House::where(['status'=>1])->get(['id','house_no','street_id','house_type','zone_id','block_id','building_id'])}}"));
		
	// $('#table_boq').DataTable({
	// 	"filter":   false,
	// 	"paging":   false,
	// 	"ordering": false,
	// 	"info":     false
	// });

	$('.table-scrollable').on('show.bs.dropdown', function () {
		$('.table-scrollable').css( "overflow", "inherit" );
	});

	$('.table-scrollable').on('hide.bs.dropdown', function () {
		$('.table-scrollable').css( "overflow", "auto" );
	})

	$(function(){
		$('#btnUpload').on('click',function(){
			var rounte = $(this).attr('rounte');
			$('.upload-excel-form').attr('action',rounte);
			$('.upload-excel-form').modal('show');
			$('.displayNone').css('display','block');
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
	});	
	
	function DeleteRowBOQ(id){
		// var table_boq = $('#table_boq').DataTable();
		// table_boq.row($('.row_'+id).parents('tr')).remove().draw( false );
		
		// table_boq.rows().eq(0).each(function(index_boq){
		// 	var cell = table_boq.cell(index_boq,0);
		// 	$(cell.node()).find(".line").text(lineNo(parseFloat(index_boq)+1,3));
		// 	$(cell.node()).find(".line_no").val(lineNo(parseFloat(index_boq)+1,3));
		// });
	}
	// function ChangeItemTypes(){
	// 	val = $('#boq-item-type').val();
	// 	if(val!=null && val!='' && jsonItem){
	// 		$('#boq-item_name').empty();
	// 		$('#boq-item_name').append($('<option></option>').val('').text(''));
	// 		$('#boq-item_name').select2('val', null);
	// 		$.each(jsonItem.filter(c=>c.cat_id==val),function(key, val){
	// 			$('#boq-item_name').append($('<option></option>').val(val.id).text(val.code+' ('+val.name+')'));
	// 		});
	// 		$('#boq-item_name').on('change', function(){
	// 			ChangeItems();
	// 		});
	// 	}
	// }
	// function ChangeItems(){
	// 	val = $('#boq-item_name').val();
	// 	item_type = $('#item-item-type').val();
	// 	if(val != '' ){
	// 		console.log(val);
	// 		var table_boq = $('#table_boq').DataTable();
	// 		var line_no = table_boq.rows().count();
	// 		if(line_no < 100){
	// 			$(".show-message-error-boq").empty();
	// 			table_boq.row.add([
	// 				'<strong class="line">'+lineNo((line_no+1),3)+'</strong>'
	// 				+'<input type="hidden" value="'+lineNo((line_no+1),3)+'" name="line_no[]" class="line_no line_no_'+index_boq+'" />',
	// 				'<select onchange="ChangeItemType('+item_type+', '+index_boq+')" class="form-control select2_'+index_boq+' line_item_type line_item_type_'+index_boq+'" name="line_item_type[]">'
	// 					+'<option value=""></option>'
	// 					+'{{getSystemData("IT")}}'
	// 				+'</select>',
	// 				'<select onchange="ChangeItem('+val+', '+index_boq+')" class="form-control select2_'+index_boq+' line_item line_item_'+index_boq+'" name="line_item[]">'
	// 					+'<option value=""></option>'
	// 				+'</select>',
	// 				'<select class="form-control select2_'+index_boq+' line_unit line_unit_'+index_boq+'" name="line_unit[]">'
	// 					+'<option value=""></option>'
	// 				+'</select>',
	// 				'<input type="number" length="11" class="form-control line_qty_std line_qty_std_'+index_boq+'" name="line_qty_std[]" placeholder="{{trans("lang.enter_number")}}" />',
	// 				'<input type="number" length="11" class="form-control line_qty_add line_qty_add_'+index_boq+'" name="line_qty_add[]" value="0" placeholder="{{trans("lang.enter_number")}}" />',
	// 				'<input type="number" length="11" class="form-control line_cost line_cost_'+index_boq+'" name="line_cost[]" value="0" placeholder="{{trans("lang.enter_number")}}" />',
	// 				'<a class="row_'+index_boq+' btn btn-sm red" onclick="DeleteRowBOQ('+index_boq+')"><i class="fa fa-times"></i></a>',
	// 			]).draw();
	// 			$.fn.select2.defaults.set('theme','classic');
	// 			$(".select2_"+index_boq).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
	// 			index_boq++;
	// 		}else{
	// 			$(".show-message-error-boq").html('{{trans("lang.not_more_than_100")}}!');
	// 		}
	// 	}
		
	// }
	
	
	function ChangeItemType(val, row){
		if(val!=null && val!='' && jsonItem){
			$('.line_item_'+row).empty();
			$('.line_item_'+row).append($('<option></option>').val('').text(''));
			$('.line_item_'+row).select2('val', null);
			$.each(jsonItem.filter(c=>c.cat_id==val),function(key, val){
				$('.line_item_'+row).append($('<option></option>').val(val.id).text(val.code+' ('+val.name+')'));
			});
		}
	}
	
	function ChangeItem(val, row){
		if(val!=null && val!='' && jsonUnit && jsonItem){
			$('.line_unit_'+row).empty();
			$('.line_unit_'+row).append($('<option></option>').val('').text(''));
			$('.line_unit_'+row).select2('val', null);
			$.each(jsonItem.filter(c=>c.id==val),function(key, val){
				$.each(jsonUnit.filter(d=>d.to_code==val.unit_stock),function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				$('.line_unit_'+row).select2('val', val.unit_usage);
			});
		}
	}
	function romanize(num) {
		var lookup = {M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1},
			roman = '',
			i;
		for ( i in lookup ) {
			while ( num >= lookup[i] ) {
			roman += i;
			num -= lookup[i];
			}
		}
		return roman;
	}
	$('.boq-button-add-working-type').on('click',function(){
		var working_type = $("input[name='working_type_no[]']").map(function(){return $(this).val();}).get();
		var data = $('.boq-working-type').select2('data');
		var table_boq = $('#table_boq');
		console.log(data[0].id );
		if(data[0].id != ""){
			if(jQuery.inArray(data[0].id, working_type) !== -1){
			}else{
				html = '<tr class="">';
					html += '<td><strong class="line bold">'+romanize(index_work_type)+'</strong></td>';
					html += '<td colspan="6"><span class="bold">'+data[0].text+'</span><input type="hidden" value="'+data[0].id+'" name="working_type_no[]" class="working_type_no_'+index_work_type+'" /></td>';
					html += '<td class="text-right"><a class="add_item_working_type_'+index_work_type+' btn btn-sm" onclick="addWorkingTypeItem('+index_work_type+','+data[0].id+')"><i class="fa fa-plus pionter"></i></a></td>';
				html += '</tr>';
				html += '<tr><td colspan="8"><table class="table table-hover no-footer item_table" id="working_type_'+index_work_type+'_'+data[0].id+'"></table></td></tr>';
				table_boq.append(html);
				index_work_type++;
			}
		}else{
			$(".confirm_body").html("{{trans('lang.please_select_working_type')}}");
			$("#mi-modal").modal('show');
		}
	});
	 function addWorkingTypeItem(row,type_id){
		
		var table_boq = $('#working_type_'+row+'_'+type_id);
		var rowCount = $('#working_type_'+row+'_'+type_id+' tr').length;
		var itemIndex = "'"+index_boq+"_"+type_id+"'";
		html = '<tr class="tr_"'+itemIndex+'>';
			// <strong>'+lineNo(rowCount+1,1)+'</strong>
				html+= '<td width="3%"><input type="hidden" value="'+lineNo((index_boq+1),3)+'" name="line_no_'+type_id+'[]" class="line_no line_no_'+index_boq+'_'+type_id+'" /></td>';
				html+= '<td width="15%"><select onchange="ChangeItemType(this.value, '+itemIndex+')" class="form-control select2_'+index_boq+'_'+type_id+' line_item_type line_item_type_'+index_boq+'_'+type_id+'" name="line_item_type_'+type_id+'[]">'
					+'<option value=""></option>'
					+'{{getSystemData("IT")}}'
					+'</select></td>';
				html+= '<td width="15%"><select onchange="ChangeItem(this.value, '+itemIndex+')" class="form-control select2_'+index_boq+'_'+type_id+' line_item line_item_'+index_boq+'_'+type_id+'" name="line_item_'+type_id+'[]">'
					+'<option value=""></option>'
				+'</select></td>';
				html+= '<td width="10%"><select class="form-control select2_'+index_boq+'_'+type_id+' line_unit line_unit_'+index_boq+'_'+type_id+'" name="line_unit_'+type_id+'[]">'
					+'<option value=""></option>'
				+'</select></td>';
				html+= '<td width="10%"><input type="number" length="11" class="form-control line_qty_std line_qty_std_'+index_boq+'_'+type_id+'" name="line_qty_std_'+type_id+'[]" placeholder="{{trans("lang.enter_number")}}" /></td>';
				html += '<td width="10%"><input type="number" length="11" class="form-control line_qty_add line_qty_add_'+index_boq+'_'+type_id+'" name="line_qty_add_'+type_id+'[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
				html += '<td width="10%"><input type="number" length="11" class="form-control line_cost line_cost_'+index_boq+'_'+type_id+'" name="line_cost_'+type_id+'[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
				html += '<td width="3%"><a class="row_'+index_boq+'_'+type_id+' btn btn-sm" onclick="deleteItem(this)"><i class="fa fa-trash"></i></a></td>';
			html+='</tr>';
		table_boq.append(html);
		$.fn.select2.defaults.set('theme','classic');
			$(".select2_"+index_boq+'_'+type_id).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
			index_boq++;
	 }
	 function deleteItem(itemIndex){
		var row = itemIndex.parentNode.parentNode;
		row.attr('disabled');
  		// row.parentNode.removeChild(row);
		// var itemIndex = "'"+row+"_"+type_id+"'";
		// $(".tr_"+itemIndex).remove();

	 }
	// $('.boq-pointer').on('click', function(){
	// 	var table_boq = $('#table_boq').DataTable();
	// 	var line_no = table_boq.rows().count();
	// 	if(line_no < 100){
	// 		$(".show-message-error-boq").empty();
	// 		table_boq.row.add([
	// 			'<strong class="line">'+lineNo((line_no+1),3)+'</strong>'
	// 			+'<input type="hidden" value="'+lineNo((line_no+1),3)+'" name="line_no[]" class="line_no line_no_'+index_boq+'" />',
	// 			'<select onchange="ChangeItemType(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item_type line_item_type_'+index_boq+'" name="line_item_type[]">'
	// 				+'<option value=""></option>'
	// 				+'{{getSystemData("IT")}}'
	// 			+'</select>',
	// 			'<select onchange="ChangeItem(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item line_item_'+index_boq+'" name="line_item[]">'
	// 				+'<option value=""></option>'
	// 			+'</select>',
	// 			'<select class="form-control select2_'+index_boq+' line_unit line_unit_'+index_boq+'" name="line_unit[]">'
	// 				+'<option value=""></option>'
	// 			+'</select>',
	// 			'<input type="number" length="11" class="form-control line_qty_std line_qty_std_'+index_boq+'" name="line_qty_std[]" placeholder="{{trans("lang.enter_number")}}" />',
	// 			'<input type="number" length="11" class="form-control line_qty_add line_qty_add_'+index_boq+'" name="line_qty_add[]" value="0" placeholder="{{trans("lang.enter_number")}}" />',
	// 			'<input type="number" length="11" class="form-control line_cost line_cost_'+index_boq+'" name="line_cost[]" value="0" placeholder="{{trans("lang.enter_number")}}" />',
	// 			'<a class="row_'+index_boq+' btn btn-sm red" onclick="DeleteRowBOQ('+index_boq+')"><i class="fa fa-times"></i></a>',
	// 		]).draw();
			// $.fn.select2.defaults.set('theme','classic');
			// $(".select2_"+index_boq).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
			// index_boq++;
	// 	}else{
	// 		$(".show-message-error-boq").html('{{trans("lang.not_more_than_100")}}!');
	// 	}
	// });

	// $('.boq-pointer').on('click', function(){
	// 	var table_boq = $('#table_boq');
	// 	var line_no = table_boq;
	// 	// if(line_no < 100){
	// 		$(".show-message-error-boq").empty();
	// 		html = '<tr>';
	// 			html+= '<td><strong>'+lineNo(1+1,1)+'</strong><input type="hidden" value="'+lineNo((line_no+1),3)+'" name="line_no[]" class="line_no line_no_'+index_boq+'" /></td>';
	// 			html+= '<td><select onchange="ChangeItemType(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item_type line_item_type_'+index_boq+'" name="line_item_type[]">'
	// 				+'<option value=""></option>'
	// 				+'{{getSystemData("IT")}}'
	// 				+'</select></td>';
	// 			html+= '<td><select onchange="ChangeItem(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item line_item_'+index_boq+'" name="line_item[]">'
	// 				+'<option value=""></option>'
	// 			+'</select></td>';
	// 			html+= '<td><select class="form-control select2_'+index_boq+' line_unit line_unit_'+index_boq+'" name="line_unit[]">'
	// 				+'<option value=""></option>'
	// 			+'</select></td>';
	// 			html+= '<td><input type="number" length="11" class="form-control line_qty_std line_qty_std_'+index_boq+'" name="line_qty_std[]" placeholder="{{trans("lang.enter_number")}}" /></td>';
	// 			html += '<td><input type="number" length="11" class="form-control line_qty_add line_qty_add_'+index_boq+'" name="line_qty_add[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
	// 			html += '<td><input type="number" length="11" class="form-control line_cost line_cost_'+index_boq+'" name="line_cost[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
	// 			html += '<td><a class="row_'+index_boq+' btn btn-sm red" onclick="DeleteRowBOQ('+index_boq+')"><i class="fa fa-times"></i></a></td>';
	// 		html+='</tr>';
	// 		table_boq.append(html);
			
	// 		$.fn.select2.defaults.set('theme','classic');
	// 		$(".select2_"+index_boq).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
	// 		index_boq++;
	// 	// }else{
	// 	// 	$(".show-message-error-boq").html('{{trans("lang.not_more_than_100")}}!');
	// 	// }
	// });
	
	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 15%;">{{trans("lang.item_code")}}</th>';
                    str += '<th style="width: 30%;">{{trans("lang.item_name")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.units")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.qty_std")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.qty_add")}}</th>';
					str += '<th style="width: 10%;">{{trans("lang.cost")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.action")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }
	
	function formatBoqWorkingType (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 15%;">{{trans("lang.boq_code")}}</th>';
                    str += '<th style="width: 10%;"><div class="actions">';
					str += '<div class="btn btn-xs">';
					str += '<a title="{{trans("lang.download_example")}}" class="btn" href="{{$rounteExample}}">';
					str += '<i class="fa fa-download"></i><br />{{ trans("lang.download_example") }}';
					str += '</a></div>';
					str += '<div class="btn btn-xs">';
					str += '<a title="{{trans("lang.view")}}" class="btn" href="{{$rounteExample}}">';
					str += '<i class="fa fa-eye"></i><br />{{ trans("lang.view") }}';
					str += '</a></div>';
					str += '<div class="btn btn-xs">';
					str += '<a title="{{trans("lang.revise")}}" class="btn" href="{{$rounteExample}}">';
					str += '<i class="fa fa-pencil"></i><br />{{ trans("lang.revise") }}';
					str += '</a></div>';
					str +='</div>';
					
					str += '</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }
	function formatBoqHouse (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 15%;">{{trans("lang.boq_code")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.house_no")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.house_desc")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.created_by")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.created_at")}}</th>';
					str += '<th style="width: 5%;">{{trans("lang.revise_count")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.action")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }
	
	var my_table = $('#my-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
		ajax: '{{$rounte}}',
		columns: [
			{
				className: 'details-control',
				orderable: false,
				searchable: false,
				data: null,
				defaultContent: ''
			},
			{data: 'boq_code', name:'boq_code'},
			{data: 'zone_name', name:'zone_name'},
			{data: 'block_name', name:'block_name'},
			{data: 'building_name', name:'building_name'},
			{data: 'street_name', name:'street_name'},
			// {data: 'line_no', class:'text-center' ,name:'line_no'},
			{data: 'trans_date', name:'trans_date'},
			{data: 'trans_by', name:'trans_by'},
			{data: 'trans_type', class:'text-center', name:'trans_type'},
			{data: 'version', class:'text-center', name:'version'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[1, 'desc']]
	});
	$('#my-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		console.log(tr);
		var row = my_table.row(tr);
		// console.log(my_table);
		var tableId = 'sub-' + row.data().id;
		if(row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
			objName = [];
		}else{
			row.child(formatBoqHouse(row.data())).show();
			initailHouseBoq(tableId,row.data());
			$('#' + tableId+'_wrapper').attr('style','width: 99%;');
			tr.addClass('shown');
		}
	});

	function initailChildWorkingType(tableId){
		var my_tablew = $('#'+tableId).DataTable();
		var my_tablew = $('#'+tableId).DataTable({
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			ajax: '{{$rounte}}',
			columns: [
				{
					className: 'details-control',
					orderable: false,
					searchable: false,
					data: null,
					defaultContent: ''
				},
				{data: 'boq_code', name:'boq_code'},
				{data: 'zone_name', name:'zone_name'},
				{data: 'block_name', name:'block_name'},
				{data: 'building_name', name:'building_name'},
				{data: 'street_name', name:'street_name'},
				// {data: 'line_no', class:'text-center' ,name:'line_no'},
				{data: 'trans_date', name:'trans_date'},
				{data: 'trans_by', name:'trans_by'},
				{data: 'trans_type', class:'text-center', name:'trans_type'},
				{data: 'action', class :'text-center', orderable: false, searchable: false},
			],order: [[1, 'desc']]
		});
		var tr = $(this).closest('tr');
		// var row = my_tablew.row(tr);
		console.log(tr);
		// var tableIds = 'sub-' + row.data().id;
		// row.child(formatBoqWorkingType(row.data())).show();
		// 	initailBoqWorkingType(tableIds,row.data());
		// 	$('#' + tableId+'_wrapper').attr('style','width: 99%;');
		// 	tr.addClass('shown');
	}

	// $('#my-table tbody').on('click', 'td.details-control', function () {
	// 	var tr = $(this).closest('tr');
	// 	var row = my_table.row(tr);
	// 	var tableId = 'sub-' + row.data().id;
	// 	if(row.child.isShown()) {
	// 		row.child.hide();
	// 		tr.removeClass('shown');
	// 		objName = [];
	// 	}else{
	// 		row.child(format(row.data())).show();
	// 		initTable(tableId,row.data());
	// 		$('#' + tableId+'_wrapper').attr('style','width: 99%;');
	// 		tr.addClass('shown');
	// 	}
	// });

	function initailHouseBoq(tableId,data){
		$('#' + tableId).DataTable({
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			paging:true,
			filter:true,
			info:true,
			ajax: data.details_url,
			columns: [
				{ data: 'boq_house_code', name: 'boq_house_code' },
				{ data: 'house', name: 'house' },
				{ data: 'house_desc', name: 'house_desc' },
				{ data: 'created_by', name: 'created_by' },
				{ data: 'created_at', name: 'created_at' },
				{ data: 'revise_count', name: 'revise_count' },
				{ data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
			],'fnCreatedRow':function(nRow,aData,iDataIndex){
				
				if (objName) {
					var obj = {
						'id':aData['id'],
						'boq_house_code':aData['boq_house_code'],
						'house':aData['house'],
						'house_desc':aData['house_desc'],
						'created_by':aData['created_by'],
						'created_at':aData['created_at'],
						'revise_count':aData['revise_count'],
					};
					objName.push(obj);
				}
			}
		});
	}

	function initailBoqWorkingType(tableId,data){
		
		$('#' + tableId).DataTable({
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			paging:true,
			filter:true,
			info:true,
			ajax: data.details_url,
			columns: [
				{ data: 'name', name: 'name' },
				{ data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
			],'fnCreatedRow':function(nRow,aData,iDataIndex){
				
				if (objName) {
					var obj = {
						'id':aData['id'],
						'name':aData['name'],
						
					};
					objName.push(obj);
				}
			}
		});
		initailChildWorkingType(tableId);
	}
	
	function initTable (tableId, data) {
		$('#' + tableId).DataTable({
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			paging:true,
			filter:true,
			info:true,
			ajax: data.details_url,
			columns: [
				{ data: 'code', name: 'code' },
				{ data: 'name', name: 'name' },
				{ data: 'unit', name: 'unit' },
				{ data: 'qty_std', name: 'qty_std' },
				{ data: 'qty_add', name: 'qty_add' },
				{ data: 'cost', name: 'cost' },
				{ data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
			],'fnCreatedRow':function(nRow,aData,iDataIndex){
				if(aData[2] == ''){
					// Add COLSPAN attribute
					$('td:eq(1)', nRow).attr('colspan', 6);
					// Hide required number of columns
					// next to the cell with COLSPAN attribute
					$('td:eq(2)', nRow).css('display', 'none');
					$('td:eq(3)', nRow).css('display', 'none');
					$('td:eq(4)', nRow).css('display', 'none');
					$('td:eq(5)', nRow).css('display', 'none');
					$('td:eq(6)', nRow).css('display', 'none');
				}
				if (objName) {
					var obj = {
						'id':aData['id'],
						'house_id':aData['house_id'],
						'item_id':aData['item_id'],
						'unit':aData['unit'],
						'qty_std':aData['qty_std'],
						'qty_add':aData['qty_add'],
						'cost':aData['cost'],
					};
					objName.push(obj);
				}
			}
		});
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
				$("#boq-street option[value='0']").remove();
				$("#label-house").html('{{trans("lang.house_no")}}');
				if(street!=null && street!="" && jsonHouse){
					$.each(jsonHouse.filter(c=>c.street_id==street),function(key, val){
						$("#boq-house").append($('<option></option>').val(val.id).text(val.house_no));
					});
				}
			}else{
				$("#label-house").html('{{trans("lang.house_type")}}');
				$("#boq-street").append('<option value="0">{{trans("lang.all")}}</option>');
				$("#boq-house").append('{{getSystemData("HT")}}');
			}
		});
		$('#boq-zone').on('change', function(){
			getHouses();
		});
		$('#boq-block').on('change', function(){
			getHouses();
		});
		$('#boq-building').on('change', function(){
			getHouses();
		});
		$('#boq-street').on('change', function(){
			getHouses();
		});

		$('#boq-house-type').on('change', function(){
			var type = $(this).val();
			getHouses();
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
			const zoneID    = $('#boq-zone').val();
			const blockID   = $('#boq-block').val();
			const buildingID    = $('#boq-building').val();
			const streetID  = $('#boq-street').val();
			const houseType = $('#boq-house-type').val();

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
						$("#boq-house").append($('<option></option>').val(val.id).text(val.house_no));
						$("#boq-house-preview").append($('<option></option>').val(val.id).text(val.house_no));
					});
				}
			});
		}

		$('#zone_id_assign_house').on('change', function(){
			getHouseNoBoq();
		});
		$('#block_id_assign_house').on('change', function(){
			getHouseNoBoq();
		});
		$('#building_id_assign_house').on('change', function(){
			getHouseNoBoq();
		});
		$('#street_id_assign_house').on('change', function(){
			console.log($('#street_id_assign_house').val());
			getHouseNoBoq();
		});

		$('#house_type_id_assign_house').on('change', function(){
			var type = $(this).val();
			getHouseNoBoq();
		});

		function getHouseNoBoq(){
			var params = {
				zone_id: null,
				block_id: null,
				building_id : null,
				street_id: null,
				house_type: null,
			};
			const zoneID    = $('#zone_id_assign_house').val();
			const blockID   = $('#block_id_assign_house').val();
			const buildingID    = $('#building_id_assign_house').val();
			const streetID  = $('#street_id_assign_house').val();
			const houseType = $('#house_type_id_assign_house').val();

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
				url:"{{url('repository/houseNoBoq')}}",
				type:'GET',
				data: params,
				success: function(result){
					$("#boq_house_assign_house").empty();
					$.each(result,function(key, val){
						$("#boq_house_assign_house").append($('<option></option>').val(val.id).text(val.house_no));
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
			$("#checkbox-house").click(function(){
				if($("#checkbox-house").is(':checked') ){
					console.log(11); 
					$(".boq-house > option").prop("selected","selected");
					$(".boq-house").trigger("change");
				}else{
					$(".boq-house > option").removeAttr("selected");
					$(".boq-house").trigger("change");
				}
			});
			
			// var table_boq = $('#table_boq').DataTable();
			// table_boq.row().clear();
			$('.boq-pointer').trigger('click');
			$('.enter-boq-modal').modal('show');
			
			$("#btnEnterBoq").on('click',function(){
				// if(chkValid([".boq-zone",".boq-block",".boq-building",".line_item_type",".line_item",".line_unit",".line_qty_std",".line_qty_add"])){
					$('.enter-boq-form').submit();
				// }else{
				// 	$('.boq-button-submit').prop('disabled', false);
				// 	return false;
				// }
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
	
	$('#boq-street-edit').on('change', function(){
		var street = $(this).val();
		if(street!='' && street!=null && jsonHouse){
			$("#boq-house-edit").empty();
			$("#boq-house-edit").select2('val', null);
			$("#boq-house-edit").append($('<option></option>').val('').text(''));
			$.each(jsonHouse.filter(c=>c.street_id==street),function(key, val){
				$("#boq-house-edit").append($('<option></option>').val(val.id).text(val.house_no));
			});
		}
	});
	function  onExcelRevise(field){
		console.log(field);
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.upload-excel-revise-form').attr('action',rounte);
		$('.modal-upload-excel-revise-form').children().find('div').children().find('h4').html('{{trans("lang.import_revised_boq")}}');
		if(jsonHouse){
			console.log(jsonHouse);
			$.each(objName.filter(c=>c.id==id),function(key,val){
				$.each(jsonHouse.filter(c=>c.id==val.house_id),function(k, v){
					$("#boq-street-edit").select2('val', v.street_id);
				});
				$("#boq-house-edit").select2('val', val.house_id);
				
				if(jsonItem){
					$('#item').empty();
					$('#item').append($('<option></option>').val('').text(''));
					$.each(jsonItem, function(k ,v){
						$('#item').append($('<option></option>').val(v.id).text(v.code+' ('+v.name+')'));
					});
					$('#item').select2('val', val.item_id);
					
					$.each(jsonItem.filter(c=>c.id==val.item_id), function(k, v){
						$('#unit').empty();
						$('#unit').append($('<option></option>').val('').text(''));
						$('#unit').select2('val', null);
						$.each(jsonUnit.filter(d=>d.to_code==v.unit_stock), function(kk, vv){
							$('#unit').append($('<option></option>').val(vv.from_code).text(vv.from_code+' ('+vv.from_desc+')'));
						});
					});
					$('#unit').select2('val', val.unit);
				}
				$("#qty_std").val(val.qty_std);
				$("#qty_add").val(val.qty_add);
			});
		}
		$('.button-submit-revise_boq_house').attr('id','btnExcelReviseBoq').attr('name','btnExcelReviseBoq');
		$('.button-submit-revise_boq_house').html('{{trans("lang.revise_boq")}}');
		$('.modal-upload-excel-revise-form').modal('show');
		
		$("#btnExcelReviseBoq").on('click',function(){
			
			$('#btnUpdate').prop('disabled', true);
			var working_type = $("#working_type").val();
			
			if(chkValid([".working_type"])){
				rounte = rounte + "?working_type="+working_type;
				console.log(rounte);
				$('.upload-excel-revise-form').attr('action',rounte);
				$('.upload-excel-revise-form').submit();
			}else{
				$('#btnExcelReviseBoq').prop('disabled', false);
				return false;
			}
		});
	}

	// function  onReviseBoqHouseExcel(field){
	// 	console.log(field);
	// 	var id = $(field).attr('row_id');
	// 	var rounte = $(field).attr('row_rounte');
	// 	var _token = $("input[name=_token]").val();
	// 	$('.upload-form').attr('action',rounte);
	// 	$('.modal-upload-form').children().find('div').children().find('h4').html('{{trans("lang.import_revised_boq")}}');
	// 	if(jsonHouse){
	// 		console.log(jsonHouse);
	// 		$.each(objName.filter(c=>c.id==id),function(key,val){
	// 			$.each(jsonHouse.filter(c=>c.id==val.house_id),function(k, v){
	// 				$("#boq-street-edit").select2('val', v.street_id);
	// 			});
	// 			$("#boq-house-edit").select2('val', val.house_id);
				
	// 			if(jsonItem){
	// 				$('#item').empty();
	// 				$('#item').append($('<option></option>').val('').text(''));
	// 				$.each(jsonItem, function(k ,v){
	// 					$('#item').append($('<option></option>').val(v.id).text(v.code+' ('+v.name+')'));
	// 				});
	// 				$('#item').select2('val', val.item_id);
					
	// 				$.each(jsonItem.filter(c=>c.id==val.item_id), function(k, v){
	// 					$('#unit').empty();
	// 					$('#unit').append($('<option></option>').val('').text(''));
	// 					$('#unit').select2('val', null);
	// 					$.each(jsonUnit.filter(d=>d.to_code==v.unit_stock), function(kk, vv){
	// 						$('#unit').append($('<option></option>').val(vv.from_code).text(vv.from_code+' ('+vv.from_desc+')'));
	// 					});
	// 				});
	// 				$('#unit').select2('val', val.unit);
	// 			}
	// 			$("#qty_std").val(val.qty_std);
	// 			$("#qty_add").val(val.qty_add);
	// 		});
	// 	}
	// 	$('.button-submit-revise_boq_house').attr('id','btnExcelReviseBoq').attr('name','btnExcelReviseBoq');
	// 	$('.button-submit-revise_boq_house').html('{{trans("lang.revise_boq")}}');
	// 	$('.modal-upload-form').modal('show');
		
	// 	$("#btnExcelReviseBoq").on('click',function(){
			
	// 		$('#btnUpdate').prop('disabled', true);
	// 		var working_type = $("#working_type").val();
			
	// 		if(chkValid([".working_type"])){
	// 			rounte = rounte + "?working_type="+working_type;
	// 			console.log(rounte);
	// 			$('.upload-form').attr('action',rounte);
	// 			$('.upload-form').submit();
	// 		}else{
	// 			$('#btnExcelReviseBoq').prop('disabled', false);
	// 			return false;
	// 		}
	// 	});
	// }
	function  onReviseBoqHouse(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.form-revise-boq-house').attr('action',rounte);
		$('.modal-revise-boq-house').children().find('div').children().find('h4').html('{{trans("lang.revise_boq_house")}}');
		// if(jsonHouse){
		// 	// console.log(jsonHouse);
		// 	$.each(objName.filter(c=>c.id==id),function(key,val){
		// 		$.each(jsonHouse.filter(c=>c.id==val.house_id),function(k, v){
		// 			$("#boq-street-edit").select2('val', v.street_id);
		// 		});
		// 		$("#boq-house-edit").select2('val', val.house_id);
				
		// 		if(jsonItem){
		// 			$('#item').empty();
		// 			$('#item').append($('<option></option>').val('').text(''));
		// 			$.each(jsonItem, function(k ,v){
		// 				$('#item').append($('<option></option>').val(v.id).text(v.code+' ('+v.name+')'));
		// 			});
		// 			$('#item').select2('val', val.item_id);
					
		// 			$.each(jsonItem.filter(c=>c.id==val.item_id), function(k, v){
		// 				$('#unit').empty();
		// 				$('#unit').append($('<option></option>').val('').text(''));
		// 				$('#unit').select2('val', null);
		// 				$.each(jsonUnit.filter(d=>d.to_code==v.unit_stock), function(kk, vv){
		// 					$('#unit').append($('<option></option>').val(vv.from_code).text(vv.from_code+' ('+vv.from_desc+')'));
		// 				});
		// 			});
		// 			$('#unit').select2('val', val.unit);
		// 		}
		// 		$("#qty_std").val(val.qty_std);
		// 		$("#qty_add").val(val.qty_add);
		// 	});
		// }
		$('.button-submit-revise_boq_house').attr('id','btnReviseBoqHouse').attr('name','btnReviseBoqHouse');
		$('.button-submit-revise_boq_house').html('{{trans("lang.revise_boq")}}');
		$('.modal-revise-boq-house').modal('show');

		var el = $("#working_type").select2();
		$("#checkbox-excel").click(function(){
			if($("#checkbox-excel").is(':checked') ){
				console.log(11); 
				$("#working_type_excel > option").prop("selected","selected");
				$("#working_type_excel").trigger("change");
			}else{
				$("#working_type_excel > option").removeAttr("selected");
				$("#working_type_excel").trigger("change");
			}
		});
		$("#checkbox").click(function(){
			
			if($("#checkbox").is(':checked') ){
				console.log(11); 
				$("#working_type > option").prop("selected","selected");
				$("#working_type").trigger("change");
			}else{
				$("#working_type > option").removeAttr("selected");
				$("#working_type").trigger("change");
			}
		});
		
		$("#btnReviseBoqHouse").on('click',function(){
			
			$('#btnUpdate').prop('disabled', true);
			var working_type = $("#working_type").val();
			
			if(chkValid([".working_type"])){

				rounte = rounte + "?working_type="+working_type;
				console.log(rounte);
				$('.form-revise-boq-house').attr('action',rounte);
				$('.form-revise-boq-house').submit();
			}else{
				$('#btnReviseBoqHouse').prop('disabled', false);
				return false;
			}
		});
	}
	
	// function onEdit(field){
	// 	var id = $(field).attr('row_id');
	// 	var rounte = $(field).attr('row_rounte');
	// 	var _token = $("input[name=_token]").val();
	// 	$('.form-edit-boq').attr('action',rounte);
	// 	$('.modal-edit-boq').children().find('div').children().find('h4').html('{{trans("lang.edit")}}');
	// 	if(objName && jsonHouse){
	// 		$.each(objName.filter(c=>c.id==id),function(key,val){
	// 			$.each(jsonHouse.filter(c=>c.id==val.house_id),function(k, v){
	// 				$("#boq-street-edit").select2('val', v.street_id);
	// 			});
	// 			$("#boq-house-edit").select2('val', val.house_id);
				
	// 			if(jsonItem){
	// 				$('#item').empty();
	// 				$('#item').append($('<option></option>').val('').text(''));
	// 				$.each(jsonItem, function(k ,v){
	// 					$('#item').append($('<option></option>').val(v.id).text(v.code+' ('+v.name+')'));
	// 				});
	// 				$('#item').select2('val', val.item_id);
					
	// 				$.each(jsonItem.filter(c=>c.id==val.item_id), function(k, v){
	// 					$('#unit').empty();
	// 					$('#unit').append($('<option></option>').val('').text(''));
	// 					$('#unit').select2('val', null);
	// 					$.each(jsonUnit.filter(d=>d.to_code==v.unit_stock), function(kk, vv){
	// 						$('#unit').append($('<option></option>').val(vv.from_code).text(vv.from_code+' ('+vv.from_desc+')'));
	// 					});
	// 				});
	// 				$('#unit').select2('val', val.unit);
	// 			}
	// 			$("#qty_std").val(val.qty_std);
	// 			$("#qty_add").val(val.qty_add);
	// 		});
	// 	}
	// 	$('.button-submit-edit').attr('id','btnUpdate').attr('name','btnUpdate');
	// 	$('.button-submit-edit').html('{{trans("lang.save_change")}}');
	// 	$('.modal-edit-boq').modal('show');
		
	// 	$("#btnUpdate").on('click',function(){
	// 		$('#btnUpdate').prop('disabled', true);
	// 		if(chkValid([".boq-street-edit",".boq-house-edit",".item",".unit",".qty_std",".qty_add"])){
	// 			$('.form-edit-boq').submit();
	// 		}else{
	// 			$('#btnUpdate').prop('disabled', false);
	// 			return false;
	// 		}
	// 	});
	// }
	
	$("#item").on('change', function(){
		var val = $(this).val();
		if(val!=null && val!='' && jsonUnit && jsonItem){
			$('.unit').empty();
			$('.unit').append($('<option></option>').val('').text(''));
			$('.unit').select2('val', null);
			$.each(jsonItem.filter(c=>c.id==val),function(key, val){
				$.each(jsonUnit.filter(d=>d.to_code==val.unit_stock),function(k, v){
					$('.unit').append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				$('.unit').select2('val', val.unit_usage);
			});
		}
	});
	
	function onView(field){
		var rounte = $(field).attr('row_rounte');
		window.location.href = rounte;
	}
	function onViewBoqHouse(field){
		var rounte = $(field).attr('row_rounte');
		window.location.href = rounte;
	}

	function  onAssignHouse(field){
		console.log(field);
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.assign-house-form').attr('action',rounte);
		$('.assign-house-modal').children().find('div').children().find('h4').html('{{trans("lang.assign_house")}}');
		if(objName && jsonHouse){
			console.log(jsonHouse);
			$.each(objName.filter(c=>c.id==id),function(key,val){
				$.each(jsonHouse.filter(c=>c.id==val.house_id),function(k, v){
					$("#boq-street-edit").select2('val', v.street_id);
				});
				$("#boq-house-edit").select2('val', val.house_id);
				
				if(jsonItem){
					$('#item').empty();
					$('#item').append($('<option></option>').val('').text(''));
					$.each(jsonItem, function(k ,v){
						$('#item').append($('<option></option>').val(v.id).text(v.code+' ('+v.name+')'));
					});
					$('#item').select2('val', val.item_id);
					
					$.each(jsonItem.filter(c=>c.id==val.item_id), function(k, v){
						$('#unit').empty();
						$('#unit').append($('<option></option>').val('').text(''));
						$('#unit').select2('val', null);
						$.each(jsonUnit.filter(d=>d.to_code==v.unit_stock), function(kk, vv){
							$('#unit').append($('<option></option>').val(vv.from_code).text(vv.from_code+' ('+vv.from_desc+')'));
						});
					});
					$('#unit').select2('val', val.unit);
				}
				$("#qty_std").val(val.qty_std);
				$("#qty_add").val(val.qty_add);
			});
		}
		$('.button-submit-assign-house').attr('id','btnAssignHouse').attr('name','btnAssignHouse');
		$('.button-submit-assign-house').html('{{trans("lang.assign_house")}}');
		$('.assign-house-modal').modal('show');
		
		$("#btnAssignHouse").on('click',function(){
			
			$('#btnUpdate').prop('disabled', true);
			var working_type = $("#assign-house").val();
			
			if(chkValid([".assign-house"])){

				rounte = rounte + "?working_type="+working_type;
				console.log(rounte);
				$('.assign-house-form').attr('action',rounte);
				$('.assign-house-form').submit();
			}else{
				$('#btnAssignHouse').prop('disabled', false);
				return false;
			}
		});
	}
	
	function onDelete(field){
		var rounte = $(field).attr('row_rounte');
		$.confirm({
            title:'{{trans("lang.confirmation")}}',
            content:'{{trans("lang.content_delete")}}',
            autoClose: 'no|10000',
            buttons:{
                yes:{
                    text:'{{trans("lang.yes")}}',
                    btnClass: 'btn-success',
                    action:function(){
                        window.location.href=rounte;
                    }
                },
                no:{
                    text:'{{trans("lang.no")}}',
                    btnClass: 'btn-danger',
                    action:function(){}
                }
            }
        });
	}
	
	var modalConfirm = function(callback){
  
	$("#btn-confirm").on("click", function(){
		$("#mi-modal").modal('show');
	});

	$("#modal-btn-si").on("click", function(){
		callback(true);
		$("#mi-modal").modal('hide');
	});
	
	$("#modal-btn-no").on("click", function(){
		callback(false);
		$("#mi-modal").modal('hide');
	});
	};

	modalConfirm(function(confirm){
	if(confirm){
		//Acciones si el usuario confirma
		$("#result").html("CONFIRMADO");
	}else{
		//Acciones si el usuario no confirma
		$("#result").html("NO CONFIRMADO");
	}
	});
</script>
@endsection()
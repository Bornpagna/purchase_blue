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
					@if(isset($rounteSave))
						<a rounte="{{$rounteSave}}" title="{{trans('lang.add_new')}}" class="btn btn-circle btn-icon-only btn-default" id="btnAdd">
							<i class="fa fa-plus"></i>
						</a>
					@endif
					@if(isset($rounteDownload))
						<a title="{{trans('lang.download')}}" class="btn btn-circle btn-icon-only btn-default" href="{{$rounteDownload}}">
							<i class="fa fa-file-excel-o"></i>
						</a>
					@endif
					@if(isset($rounteUploade))
						<a rounte="{{$rounteUploade}}" title="{{trans('lang.upload')}}" class="btn btn-circle btn-icon-only btn-default" id="btnUpload">
							<i class="icon-cloud-upload"></i>
						</a>
					@endif

					@if(isset($rounteUploadeUpdate))
						<a rounte="{{$rounteUploadeUpdate}}" title="{{trans('lang.upload_update')}}" class="btn btn-circle btn-icon-only btn-default" id="btnUploadUpdate">
							<i class="icon-cloud-upload"></i>
						</a>
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
				<table class="table table-striped table-bordered table-hover " id="my-table">
					<thead>
						<tr>
							<th style="width: 3%;" class="all"></th>
							<th width="10%" class="text-center all">{{ trans('lang.house_no') }}</th>
							<th width="10%" class="text-center">{{ trans('lang.house_type') }}</th>
							@if(getSetting()->allow_zone==1)<th width="10%" class="text-center all">{{ trans('lang.zone') }}</th>@endif
							@if(getSetting()->allow_block==1)<th width="10%" class="text-center all">{{ trans('lang.block') }}</th>@endif
							<th width="10%" class="text-center all">{{ trans('lang.building') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.street') }}</th>
							<th width="17%" class="text-center all">{{ trans('lang.desc') }}</th>
							<th width="8%" class="text-center all">{{ trans('lang.status') }}</th>
							<th width="15%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@include('modal.house')
@include('modal.edit_house')
@include('modal.modal')
@include('modal.upload')
@include('modal.enter_boq')

@endsection()

@section('javascript')
<script type="text/javascript">
	var objName = [];
	var index = 0;
	var index_boq = 0;
	var jsonItem = JSON.parse(convertQuot("{{\App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage'])}}"));
	var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc'])}}"));
	
	$('#table_purchase,#table_boq').DataTable({
		"filter":   false,
		"paging":   false,
		"ordering": false,
		"info":     false
	});
	
	function DeleteDataRow(id){
		var table_purchase = $('#table_purchase').DataTable();
		table_purchase.row($('.row_'+id).parents('tr')).remove().draw( false );
		
		table_purchase.rows().eq(0).each(function(index){
			var cell = table_purchase.cell(index,0);
			$(cell.node()).find(".line").text(lineNo(parseFloat(index)+1,3));
			$(cell.node()).find(".line_no").val(lineNo(parseFloat(index)+1,3));
		});
	}
	
	function DeleteRowBOQ(id){
		var table_boq = $('#table_boq').DataTable();
		table_boq.row($('.row_'+id).parents('tr')).remove().draw( false );
		
		table_boq.rows().eq(0).each(function(index_boq){
			var cell = table_boq.cell(index_boq,0);
			$(cell.node()).find(".line").text(lineNo(parseFloat(index_boq)+1,3));
			$(cell.node()).find(".line_no").val(lineNo(parseFloat(index_boq)+1,3));
		});
	}
	
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
	
	$('.pointer').on('click', function(){
		var table_purchase = $('#table_purchase').DataTable();
		var line_no = table_purchase.rows().count();
		if(line_no < 100){
			$(".show-message-error").empty();
			table_purchase.row.add([
				'<strong class="line">'+lineNo((line_no+1),3)+'</strong>'
				+'<input type="hidden" value="'+lineNo((line_no+1),3)+'" name="line_no[]" class="line_no line_no_'+index+'" />',
				'<input type="text" length="50" class="form-control line_house_no line_house_no_'+index+'" name="line_house_no[]" placeholder="{{trans("lang.enter_text")}}" />',
				'<input type="text" length="100" class="form-control line_house_desc line_house_desc_'+index+'" name="line_house_desc[]" placeholder="{{trans("lang.enter_text")}}" />',
				'<select class="form-control select2_'+index+' line_status line_status_'+index+'" name="line_status[]">'
					+'<option value="1">{{trans('lang.start')}}</option>'
					+'<option value="2">{{trans('lang.finish')}}</option>'
					+'<option value="3">{{trans('lang.stop')}}</option>'
				+'</select>',
				'<a class="row_'+index+' btn btn-sm red" onclick="DeleteDataRow('+index+')"><i class="fa fa-times"></i></a>',
			]).draw();
			$.fn.select2.defaults.set('theme','classic');
			$(".select2_"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
			index++;
		}else{
			$(".show-message-error").html('{{trans("lang.not_more_than_100")}}!');
		}
	});
	
	$('.boq-pointer').on('click', function(){
		var table_boq = $('#table_boq').DataTable();
		var line_no = table_boq.rows().count();
		if(line_no < 100){
			$(".show-message-error-boq").empty();
			table_boq.row.add([
				'<span class="line" style="font-size: larger;font-weight: bold;">'+lineNo((line_no+1),3)+'</span>'
				+'<input type="hidden" value="'+lineNo((line_no+1),3)+'" name="line_no[]" class="line_no line_no_'+index_boq+'" />',
				'<select onchange="ChangeItemType(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item_type line_item_type_'+index_boq+'" name="line_item_type[]">'
					+'<option value=""></option>'
					+'{{getSystemData("IT")}}'
				+'</select>',
				'<select onchange="ChangeItem(this.value, '+index_boq+')" class="form-control select2_'+index_boq+' line_item line_item_'+index_boq+'" name="line_item[]">'
					+'<option value=""></option>'
				+'</select>',
				'<select class="form-control select2_'+index_boq+' line_unit line_unit_'+index_boq+'" name="line_unit[]">'
					+'<option value=""></option>'
				+'</select>',
				'<input type="number" length="11" class="form-control line_qty_std line_qty_std_'+index_boq+'" name="line_qty_std[]" placeholder="{{trans("lang.enter_number")}}" />',
				'<input type="number" length="11" class="form-control line_qty_add line_qty_add_'+index_boq+'" name="line_qty_add[]" value="0" placeholder="{{trans("lang.enter_number")}}" />',
				'<a class="row_'+index_boq+' btn btn-sm red" onclick="DeleteRowBOQ('+index_boq+')"><i class="fa fa-times"></i></a>',
			]).draw();
			$.fn.select2.defaults.set('theme','classic');
			$(".select2_"+index_boq).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:true});
			index_boq++;
		}else{
			$(".show-message-error-boq").html('{{trans("lang.not_more_than_100")}}!');
		}
	});
	
	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 10%;">{{trans("lang.line_no")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.trans_date")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.trans_by")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.trans_ref")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.action")}}</th>';
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
			{data: 'house_no', name:'house_no'},
			{data: 'house_type_desc', name:'house_type_desc'},
			@if(getSetting()->allow_zone==1){data: 'zone', name:'zone'},@endif
			@if(getSetting()->allow_block==1){data: 'block', name:'block'},@endif
			{data: 'building', name:'building'},
			{data: 'street', name:'street'},
			{data: 'house_desc', name:'house_desc'},
			{data: 'status', name:'status'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[1, 'desc']],'fnCreatedRow':function(nRow,aData,iDataIndex){
			if(aData['status']==1){
				var str='<span class="label label-info" style="font-size: smaller;">{{trans("lang.starting")}}</span>';
			}else if(aData['status']==2){
				var str='<span class="label label-success" style="font-size: smaller;">{{trans("lang.finished")}}</span>';
			}else{
				var str='<span class="label label-danger" style="font-size: smaller;">{{trans("lang.stopped")}}</span>';
			}
			$('td:eq(8)',nRow).html(str).addClass("text-center");
			
			if (objName) {
				var obj = {
					'id':aData['id'],
					'house_no':aData['house_no'],
					'house_type':aData['house_type'],
					'house_desc':aData['house_desc'],
					'zone_id':aData['zone_id'],
					'block_id':aData['block_id'],
					'building_id':aData['building_id'],
					'street_id':aData['street_id'],
					'status':aData['status'],
				};
				objName.push(obj);
			}
		}
	});
	
	$('#my-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = my_table.row(tr);
		var tableId = 'sub-' + row.data().id;
		if(row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
		}else{
			row.child(format(row.data())).show();
			initTable(tableId,row.data());
			$('#' + tableId).parent('.table-scrollable').prev('.row').remove();
			$('#' + tableId).parent('.table-scrollable').next('.row').remove();
			tr.addClass('shown');
		}
	});
	
	function initTable(tableId, data) {
		$('#' + tableId).DataTable({
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			paging:true,
			filter:true,
			info:true,
			ajax: data.details_url,
			columns: [
				{ data: 'line_no', name: 'line_no', class :'text-center' },
				{ data: 'trans_date', name: 'trans_date' },
				{ data: 'trans_by', name: 'trans_by' },
				{ data: 'trans_type', name: 'trans_type' },
				{ data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
			]
		});
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
	
	$(function(){
		/* button click save */
		$("#btnAdd").on("click",function(){
			var rounte = $(this).attr('rounte');
			$('.house-form').attr('action',rounte);
			$('.house-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			index = 0;
			var table_purchase = $('#table_purchase').DataTable();
			table_purchase.row().clear();
			$('.pointer').trigger('click');
			
			$('.button-submit-house').attr('id','btnSave').attr('name','btnSave');
			$('.button-submit-house').html('{{trans("lang.save")}}');
			$('.house-modal').modal('show');
			
			$("#btnSave").on('click',function(){
				
				var table_purchase = $('#table_purchase').DataTable();
				if (!table_purchase.data().count()) {
					
					$('.house-modal').modal('hide');
					return false;
				}else{
					if(chkValid([".street",".zone",".block",".house_type",".line_house_no",".line_house_desc",".line_status"])){
						var dupArray = [];
						var isValid = true;
						var street = $('#building_id').val();
						$(".line_house_no").each(function(i){
							dupArray[i] = $(this).val();
							if(objName && street!='' && street!=null){
								$.each(objName.filter(c=>c.street_id==street).filter(d=>d.house_no==dupArray[i]),function(key, val){
									$(".show-message-error").html('Dublicate data!, value '+ dupArray[i] +' already exist');
									isValid = false;
								});
							}
						});
						if (isDuplicateArray(dupArray)==true) {
							$(".show-message-error").html('Dublicate table row!, check and try agan');
							isValid = false;
						}
						if(isValid == true){
							$(".show-message-error").empty();
							$('.button-submit-house').prop('disabled', true);
							$('.house-form').submit();
							return true;
						}else{
							$('.button-submit-house').prop('disabled', false);
							return false
						}
					}else{
						$('.button-submit-house').prop('disabled', false);
						return false;
					}
				}
			});
		});
		/* end button click save */
		
		/* upload file excel */
		$('#btnUpload').on('click',function(){
			var rounte = $(this).attr('rounte');
			$('.upload-excel-form').attr('action',rounte);
			$('.upload-excel-form').modal('show');
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
		/* end upload file excel */

		/* upload file excel */
		$('#btnUploadUpdate').on('click',function(){
			var rounte = $(this).attr('rounte');
			$('.upload-excel-form').attr('action',rounte);
			$('.upload-excel-form').modal('show');
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
		/* end upload file excel */
	});	
	
	function onBOQ(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		$('.upload-excel-form').attr('action',rounte);
		$('.upload-excel-form').modal('show');
		$('#btn_upload_excel').on('click',function(){
			if(onUploadExcel()){}else{return false}
		}); 
	}
	
	function enterBOQ(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		
		$('.enter-boq-form').attr('action',rounte);
		$('.enter-boq-modal').children().find('div').children().find('h4').html('{{trans("lang.enter_boq")}}');
		$('#boq-house-id').val(id);
		$('.boq-button-submit').attr('id','btnEnterBoq').attr('name','btnEnterBoq');
		$('.boq-button-submit').html('{{trans("lang.save")}}');
		
		var table_boq = $('#table_boq').DataTable();
		table_boq.row().clear();
		$('.boq-pointer').trigger('click');
		$('.enter-boq-modal').modal('show');
		
		$("#btnEnterBoq").on('click',function(){
			if(chkValid([".line_item_type",".line_item",".line_unit",".line_qty_std",".line_qty_add"])){
				$('.enter-boq-form').submit();
			}else{
				$('.boq-button-submit').prop('disabled', false);
				return false;
			}
		});
	}
	
	function onEdit(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		$('.house-form-edit').attr('action',rounte);
		$('.house-modal-edit').children().find('div').children().find('h4').html('{{trans("lang.edit")}}');
		if(objName){
			$.each(objName.filter(c=>c.id==id),function(key,val){
				@if(getSetting()->allow_zone==1)
					$('#zone-edit').select2('val',val.zone_id);
				@endif
				@if(getSetting()->allow_block==1)
					$('#block-edit').select2('val',val.block_id);
				@endif
				$('#street-edit').select2('val',val.street_id);
				$('#old_street_id').val(val.street_id);
				$('#house_type-edit').select2('val',val.house_type);
				$('#status-edit').select2('val',val.status);
				$('#desc-edit').text(val.house_desc);
				$('#house_no-edit').val(val.house_no);
				$('#old_house_no').val(val.house_no);
				$('#building_id-edit').select2('val',val.building_id);
			});
		}
		$('.button-submit-edit').attr('id','btnUpdate').attr('name','btnUpdate');
		$('.button-submit-edit').html('{{trans("lang.save_change")}}');
		$('.house-modal-edit').modal('show');
		
		$("#btnUpdate").on('click',function(){
			if(chkValid([".street-edit",<?php if(getSetting()->allow_zone==1){echo '".zone-edit",';} ?><?php if(getSetting()->allow_block==1){echo '".block-edit",';} ?>".status-edit",".desc-edit",".house_no-edit",".house_type-edit"])){
				var street = $('#street-edit').val();
				var old_street = $('#old_street_id').val();
				var house = $('#house_no-edit').val();
				var old_house = $('#old_house_no').val();
				var isValid = true;
				if(street!=old_street || house!=old_house){
					if(objName){
						$.each(objName.filter(c=>c.street_id==street).filter(d=>d.house_no==house),function(key, val){
							$(".show-message-error-edit").html('Dublicate data!, value '+ house +' already exist');
							isValid = false;
						});
					}
				}
				if(isValid == true){
					$(".show-message-error").empty();
					$('.button-submit-edit').prop('disabled', true);
					$('.house-form-edit').submit();
					return true;
				}else{
					$('.button-submit-edit').prop('disabled', false);
					return false
				}
			}else{
				$('.button-submit-edit').prop('disabled', false);
				return false;
			}
		});
	}
	
	function onView(field){
		var rounte = $(field).attr('row_rounte');
		window.location.href = rounte;
	}

	@if(hasRole('zone_add') && getSetting()->allow_zone==1)
		var objZone = JSON.parse(convertQuot('{{\App\Model\SystemData::where(["type"=>"ZN","parent_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddZone").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveZone').attr('name','btnSaveZone').attr('onclick','onSubmitZone(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitZone(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objZone, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("zone/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#zone").append($('<option></option>').val(data.id).text(data.name));
								$("#zone").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddZoneEdit").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveZoneEdit').attr('name','btnSaveZoneEdit').attr('onclick','onSubmitZoneEdit(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitZoneEdit(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objZone, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("zone/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#zone-edit").append($('<option></option>').val(data.id).text(data.name));
								$("#zone-edit").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}
	@endif

	@if(hasRole('block_add') && getSetting()->allow_zone==1)
		var objBlock = JSON.parse(convertQuot('{{\App\Model\SystemData::where(["type"=>"BK","parent_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddBlock").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveBlock').attr('name','btnSaveBlock').attr('onclick','onSubmitBlock(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitBlock(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objBlock, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("block/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#block").append($('<option></option>').val(data.id).text(data.name));
								$("#block").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddBlockEdit").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveBlockEdit').attr('name','btnSaveBlockEdit').attr('onclick','onSubmitBlockEdit(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitBlockEdit(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objBlock, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("zone/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#block-edit").append($('<option></option>').val(data.id).text(data.name));
								$("#block-edit").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}
	@endif

	@if(hasRole('street_add'))
		var objStreet = JSON.parse(convertQuot('{{\App\Model\SystemData::where(["type"=>"ST","parent_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddStreet").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveStreet').attr('name','btnSaveStreet').attr('onclick','onSubmitStreet(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitStreet(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objStreet, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("street/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#street").append($('<option></option>').val(data.id).text(data.name));
								$("#street").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddStreetEdit").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveStreetEdit').attr('name','btnSaveStreetEdit').attr('onclick','onSubmitStreetEdit(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitStreetEdit(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objStreet, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("street/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#street-edit").append($('<option></option>').val(data.id).text(data.name));
								$("#street-edit").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}
	@endif

	@if(hasRole('house_type_add'))
		var objHouseType = JSON.parse(convertQuot('{{\App\Model\SystemData::where(["type"=>"HT","parent_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddHouseType").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveHouseType').attr('name','btnSaveHouseType').attr('onclick','onSubmitHouseType(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitHouseType(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objHouseType, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("housetype/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#house_type").append($('<option></option>').val(data.id).text(data.name));
								$("#house_type").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddHouseTypeEdit").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveHouseTypeEdit').attr('name','btnSaveHouseTypeEdit').attr('onclick','onSubmitHouseTypeEdit(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			$('.parent_id').hide();
		});

		function onSubmitHouseTypeEdit(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objHouseType, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("housetype/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#house_type-edit").append($('<option></option>').val(data.id).text(data.name));
								$("#house_type-edit").select2('val', data.id);
							}
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						},error:function(){
							$('.system-modal').modal('hide');
							$('.button-submit').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit').prop('disabled', false);
				return false;
			}
		}
	@endif
	
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
	
</script>
@endsection()
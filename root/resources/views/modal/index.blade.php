@extends('layouts.app')

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
				<table class="table table-striped table-bordered table-hover dt-responsive" id="my-table">
					<thead>
						<tr>
							<th width="15%" class="text-center all">{{ trans('lang.name') }}</th>
							@if($types)
							<th width="15%" class="text-center all">{{ trans('lang.parent') }}</th>
							@else
							<th width="60%" class="text-center">{{ trans('lang.desc') }}</th>
							@endif
							<th width="10%" class="text-center all">{{ trans('lang.status') }}</th>
							<th width="15%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal Varian -->

@include('modal.modal')
@include('modal.upload')

@if(isset($type))
	@include('modal.enter_boq')
@endif

@if(isset($assign))
	@include('modal.assign_user')
@endif

@endsection()

@section('javascript')
<script type="text/javascript">
	@if(isset($type))
		var index_boq = 0;
		var jsonItem = JSON.parse(convertQuot("{{\App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage'])}}"));
		var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc'])}}"));
		
		$('#table_boq').DataTable({
			"filter":   false,
			"paging":   false,
			"ordering": false,
			"info":     false
		});
		
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
	@endif
	
	@if(isset($assign))
		var objUsers = JSON.parse(convertQuot("{{\App\User::where(['delete'=>0])->where('id','!=',config('app.owner'))->where('id','!=',config('app.admin'))->get(['id','name'])}}"));
		var objAssign = JSON.parse(convertQuot("{{\App\Model\UserGroup::get(['user_id','group_id'])}}"));
	
		function onAssign(field){
			var id = $(field).attr('row_id');
			var rounte = $(field).attr('row_rounte');
			var data = new Array();
			$('.assign-form').attr('action', rounte);
			$('.assign-modal').children().find('div').children().find('h4').html('{{trans("lang.assign_user")}}');
			$('#assign-user').empty();
			$('#assign-user').append($('<option></option>').val('').text(''));
			if(objUsers){
				$.each(objUsers, function(key, val){
					$('#assign-user').append($('<option></option>').val(val.id).text(val.name));
				});
			}
			
			if(objAssign){
				$.each(objAssign.filter(c=>c.group_id==id), function(key, val){
					data.push(val.user_id);
				});
			}
			
			$('#assign-user').select2('val', data);
			$('.button-submit-assign').attr('id','btnAssign').attr('name','btnAssign');
			$('.button-submit-assign').html('{{trans("lang.assign")}}');
			$('.assign-modal').modal('show');
			
			$("#btnAssign").on('click',function(){
				if(chkValid([".assign-user"])){
					$('.assign-form').submit();
				}else{
					return false;
				}
			});
		}
	@endif
	
	var objName = [];
	$('#my-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
		ajax: '{{$rounte}}',
		columns: [
			{data: 'name'},
			{data: 'desc'},
			{data: 'status'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],'fnCreatedRow':function(nRow,aData,iDataIndex){
			@if(isset($type))
				var str='<span class="label label-info" style="font-size: smaller;"> <i class="fa fa-home"></i> : '+aData['status']+' </span>';
			@else
				var str='<span class="label label-info" style="font-size: smaller;">{{trans("lang.active")}}</span>';
			@endif
			
			$('td:eq(2)',nRow).html(str).addClass("text-center");

			@if($types == 'item_type')
				var desc='<span>'+aData['parent_name']+' </span>';
			@else
				var desc ='<span>'+aData['desc']+' </span>';
			@endif	
			
			$('td:eq(1)',nRow).html(desc)
			if (objName) {
				var obj = {
					'id':aData['id'],
					'name':aData['name'],
					'desc':aData['desc'] ,
					'type':aData['type']};
				objName.push(obj);
			}
		}
	});
	
	$(function(){
		$('.select2').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		/* button click save */
		$("#btnAdd").on("click",function(){
			var type = "{{$types}}";
			console.log(type);
			var rounte = $(this).attr('rounte');
			$('.system-form').attr('action',rounte);
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSave').attr('name','btnSave');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
			if(type==''){
				$('.parent_id').hide();
			}
			$("#btnSave").on('click',function(){
				if(chkValid([".sys-name",".sys-desc"])){
					if(chkDublicateName(objName, '#sys-name')){
						$('.system-form').submit();
					}else{
						return false;
					}
				}else{
					return false;
				}
			});
		});
		/* end button click save */
		
		/* upload file excel */
		$('#btnUpload').on('click',function(){
			$('.upload-excel-form').modal('show');
			var rounte = $(this).attr('rounte');
			$('.upload-excel-form').attr('action',rounte);
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
		/* end upload file excel */
	});	
	
	function onEdit(field){
		var type = "{{$types}}";
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.system-form').attr('action',rounte);
		$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.edit")}}');
		
		if(objName){
			$.each(objName.filter(c=>c.id==id),function(key,val){
				$('#old_name').val(val.name);
				$('#sys-name').val(val.name);
				$('#sys-desc').val(val.desc);
			});
		}
		$('.button-submit').attr('id','btnUpdate').attr('name','btnUpdate');
		$('.button-submit').html('{{trans("lang.save_change")}}');
		$('.system-modal').modal('show');
		if(type==''){
			$('.parent_id').hide();
		}
		$("#btnUpdate").on('click',function(){
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objName, '#sys-name','#old_name')){
					$('.system-form').submit();
				}else{
					return false;
				}
			}else{
				return false;
			}
		});
	}
	
	function onPermission(field){
		var rounte = $(field).attr('row_rounte');
		window.location.href=rounte;
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
	
</script>
@endsection()
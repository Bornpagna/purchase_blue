@extends('layouts.app')

@section('stylesheet')
	<style>
		.small-pic{
			display: inline-block;
			vertical-align: middle;
			height: 30px;
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
					{{-- @if(isset($routeDownloadSampleUpdatePrice))
						<a title="{{trans('lang.sample_update_price')}}" class="btn btn-circle btn-success" href="{{$routeDownloadSampleUpdatePrice}}">
							<i class="fa fa-file-excel-o"></i> {{trans('lang.sample_update_price')}}
						</a>
					@endif
					@if(isset($routeUploadSampleUpdatePrice))
						<a rounte="{{$routeUploadSampleUpdatePrice}}" title="{{trans('lang.update_price')}}" class="btn btn-circle btn-default" id="btnUpload">
							<i class="fa fa-dollar"></i> {{trans('lang.update_price')}}
						</a>
					@endif --}}
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
				<?php if(Session::has('bug') && Session::get('bug')>0): ?>
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
							<th width="3%" class="text-center all">{{ trans('lang.photo') }}</th>
							<th width="12%" class="text-center all">{{ trans('lang.item_code') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.item_name') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.item_type') }}</th>
							<th width="20%" class="text-center mobile">{{ trans('lang.desc') }}</th>
							<th width="5%" class="text-center all">{{ trans('lang.unit_stock') }}</th>
							<th width="5%" class="text-center mobile">{{ trans('lang.unit_usage') }}</th>
							<th width="5%" class="text-center mobile">{{ trans('lang.unit_purch') }}</th>
							<th width="5%" class="text-center desktop">{{ trans('lang.cost') }}</th>
							<th width="5%" class="text-center mobile">{{ trans('lang.alert_qty') }}</th>
							<th width="10%" class="text-center mobile">{{ trans('lang.status') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@include('modal.item')
@include('modal.modal')
@include('modal.upload')

@endsection()

@section('javascript')
<script type="text/javascript">
	var objName = [];
	var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc','factor'])}}"));
	$('#my-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
		ajax: '{{$rounte}}',
		columns: [
			{data: 'photo', name:'photo'},
			{data: 'code', name:'code'},
			{data: 'name', name:'name'},
			{data: 'category', name:'category'},
			{data: 'desc', name:'desc'},
			{data: 'unit_stock', name:'unit_stock'},
			{data: 'unit_usage', name:'unit_usage'},
			{data: 'unit_purch', name:'unit_purch'},
			{data: 'cost_purch', name:'cost_purch'},
			{data: 'alert_qty', name:'alert_qty'},
			{data: 'status', name:'status'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],'fnCreatedRow':function(nRow,aData,iDataIndex){
			var str='<span class="label label-danger" style="font-size: smaller;">{{trans("lang.disable")}}</span>';
			if(aData['status']==1){
				str='<span class="label label-info" style="font-size: smaller;">{{trans("lang.active")}}</span>';
			}
			var pic='<img id="'+aData['id']+'" class="small-pic" src="{{ asset("assets/upload/picture/items/no-image.jpg")}}" onclick="onZoom(this.id);" />';
			if(aData['photo']!=null && aData['photo']!='' && aData['photo']!=0){
				pic = '<img id="'+aData['id']+'" class="small-pic" src="{{ asset("assets/upload/picture/items")}}/'+aData['photo']+'" onclick="onZoom(this.id);" />';
			}
			$('td:eq(0)',nRow).html(pic).addClass("text-center");
			$('td:eq(10)',nRow).html(str).addClass("text-center");
			$('td:eq(8)',nRow).html(formatDollar(aData['cost_purch'])).addClass("text-center");
			
			if (objName) {
				var obj = {
					'id':aData['id'],
					'code':aData['code'],
					'name':aData['name'],
					'cat_id':aData['cat_id'],
					'desc':aData['desc'],
					'unit_stock':aData['unit_stock'],
					'unit_usage':aData['unit_usage'],
					'unit_purch':aData['unit_purch'],
					'cost_purch':aData['cost_purch'],
					'alert_qty':aData['alert_qty'],
					'photo':aData['photo'],
					'status':aData['status'],
				};
				objName.push(obj);
			}
		}
	});
	
	function onZoom(id){
		var image=$('#'+id).attr('src');
		$.fancybox.open(image);
	}
	
	$(function(){
		$('.my-select2').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		$("#unit-stock").on('change',function(){
			var val = $(this).val();
			if(val!='' && val!=null && jsonUnit){
				$("#unit-purch").empty();
				$("#unit-purch").append($('<option></option>').val('').text(''));
				$("#unit-purch").select2('val', null);
				$("#unit-usage").empty();
				$("#unit-usage").append($('<option></option>').val('').text(''));
				$("#unit-usage").select2('val', null);
				$.each(jsonUnit.filter(c=>c.to_code==val), function(key, val){
					$("#unit-purch").append($('<option></option>').val(val.from_code).text(val.from_code+' ('+val.from_desc+')'));
					$("#unit-usage").append($('<option></option>').val(val.from_code).text(val.from_code+' ('+val.from_desc+')'));
				});
			}
		});
		/* end set value */
		
		/* button click save */
		$("#btnAdd").on("click",function(){
			var rounte = $(this).attr('rounte');
			$('.item-form').attr('action',rounte);
			$('.item-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#old_code').val('');
			$('#item-code').val('');
			$('#item-name').val('');
			$('#item-type').select2('val', null);
			$('#status').select2('val', 1);
			$('#unit-stock').select2('val', null);
			$('#unit-usage').empty();
			$('#unit-usage').select2('val', null);
			$('#unit-purch').empty();
			$('#unit-purch').select2('val', null);
			$('#item-cost').val('');
			$('#alert-qty').val('');
			$('#item-desc').val(' ');
			
			$('#item-image-show').attr("src", "{{asset('assets/upload/picture/items/no-image.jpg')}}");
			$('.fileupload').removeClass('fileupload-exists');
			$('.fileupload').addClass('fileupload-new');
			$('.fileupload').children('.fileupload-preview').empty();
			$('#item-photo').val('');
			
			$('.button-submit-item').attr('id','btnSave').attr('name','btnSave');
			$('.button-submit-item').html('{{trans("lang.save")}}');
			$('.item-modal').modal('show');
			
			$("#btnSave").on('click',function(){
				if(chkValid([".item-code",".item-name",".item-type",".unit-stock",".unit-usage",".status",".unit-purch",".item-cost",".alert-qty",".item-desc"])){
					if(chkDublicateCode(objName, '#item-code')){
						$('.item-form').submit();
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
			console.log(11);
			$('.upload-excel-form').modal('show');
			var rounte = $(this).attr('rounte');
			$('.upload-excel-form').attr('action',rounte);
			$("#home-tab").text("Item Upload")
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
		/* end upload file excel */
	});	
	
	function onEdit(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.item-form').attr('action',rounte);
		$('.item-modal').children().find('div').children().find('h4').html('{{trans("lang.edit")}}');
		if(objName){
			$.each(objName.filter(c=>c.id==id),function(key,val){
				$('#old_code').val(val.code);
				$('#item-code').val(val.code);
				$('#item-name').val(val.name);
				$('#item-type').select2('val', val.cat_id);
				$('#status').select2('val', 1);
				$('#unit-stock').select2('val', val.unit_stock);
				$('#unit-usage').empty();
				$('#unit-usage').select2('val', null);
				$('#unit-purch').empty();
				$('#unit-purch').select2('val', null);
				$('#item-cost').val(val.cost_purch);
				$('#alert-qty').val(val.alert_qty);
				$('#item-desc').val(val.desc);
				
				if(jsonUnit){
					$("#unit-purch").append($('<option></option>').val('').text(''));
					$("#unit-usage").append($('<option></option>').val('').text(''));
					$.each(jsonUnit.filter(c=>c.to_code==val.unit_stock), function(key, val){
						$("#unit-purch").append($('<option></option>').val(val.from_code).text(val.from_code+' ('+val.from_desc+')'));
						$("#unit-usage").append($('<option></option>').val(val.from_code).text(val.from_code+' ('+val.from_desc+')'));
					});
					$('#unit-usage').select2('val', val.unit_usage);
					$('#unit-purch').select2('val', val.unit_purch);
				}
				
				if(val.photo=='' || val.photo==null || val.photo==0){
					$('#item-image-show').attr("src", "{{asset('assets/upload/picture/items/no-image.jpg')}}");
					$('.fileupload').removeClass('fileupload-exists');
					$('.fileupload').addClass('fileupload-new');
					$('.fileupload').children('.fileupload-preview').empty();
					$('#item-photo').val('');
				}else{
					$('#item-image-show').attr("src", "{{asset('assets/upload/picture/items')}}/"+val.photo);
					$('.fileupload').addClass('fileupload-new');
					$('.fileupload').children('.fileupload-preview').empty();
					$('#item-photo').val('');
				}
				
			});
		}
		$('.button-submit-item').attr('id','btnUpdate').attr('name','btnUpdate');
		$('.button-submit-item').html('{{trans("lang.save_change")}}');
		$('.item-modal').modal('show');
		
		$("#btnUpdate").on('click',function(){
			if(chkValid([".item-code",".item-name",".item-type",".unit-stock",".unit-usage",".status",".unit-purch",".item-cost",".item-desc"])){
				if(chkDublicateCode(objName, '#item-code','#old_code')){
					$('.item-form').submit();
				}else{
					return false;
				}
			}else{
				return false;
			}
		});
	}
	
	@if(hasRole('item_type_add'))
	
		var objItemType = JSON.parse(convertQuot('{{\App\Model\SystemData::where(["type"=>"IT","parent_id"=>Session::get('project')])->get(["name"])}}'));
		console.log(objItemType)
		$("#btnAddItemType").on('click', function(event){
			event.preventDefault();
			$('.system-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#sys-name').val('');
			$('#old_name').val('');
			$('#sys-desc').val('');
			$('.button-submit').attr('id','btnSaveItemType').attr('name','btnSaveItemType').attr('onclick','onSubmitItemType(this)');
			$('.button-submit').html('{{trans("lang.save")}}');
			$('.system-modal').modal('show');
		});

		function onSubmitItemType(field){
			$('.button-submit').prop('disabled', true);
			if(chkValid([".sys-name",".sys-desc"])){
				if(chkDublicateName(objItemType, '#sys-name')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("item_type/save")}}',
						type:'POST',
						data:{
							'_token': _token,
							'name': $('#sys-name').val(),
							'desc': $('#sys-desc').val(),
						},
						success:function(data){
							if(data){
								$("#item-type").append($('<option></option>').val(data.id).text(data.name));
								$("#item-type").select2('val', data.id);
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
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
		.item_table>tbody>tr>td:first {
			border: 0 !important;
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
					<a href="{{$rounteBack}}" title="{{trans('lang.back')}}" class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-reply"></i>
					</a>
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
				<table class="table table-striped details-table table-responsive"  id="my-table">
					<thead>
						<tr>
							<th class="all" style="width: 5%;">{{trans("lang.boq_code")}}</th>
							<th style="width: 15%;">{{trans("lang.boq_code")}}</th>
							<th style="width: 20%;">{{trans("lang.house_no")}}</th>
							<th style="width: 15%;">{{trans("lang.house_desc")}}</th>
							<th style="width: 10%;">{{trans("lang.created_by")}}</th>
							<th style="width: 10%;">{{trans("lang.created_at")}}</th>
							{{-- <th style="width: 15%;">{{trans("lang.action")}}</th> --}}
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

@include('modal.edit_boq')

@endsection()

@section('javascript')
<script type="text/javascript">
	var index_boq = 0;
	var objName = [];
	var jsonItem = JSON.parse(convertQuot("{{\App\Model\Item::get(['id','cat_id','code','name','unit_stock','unit_purch','unit_usage'])}}"));
	var jsonUnit = JSON.parse(convertQuot("{{\App\Model\Unit::where(['status'=>1])->get(['id','from_code','from_desc','to_code','to_desc'])}}"));
	var jsonHouse = JSON.parse(convertQuot("{{\App\Model\House::where(['status'=>1])->get(['id','house_no','street_id'])}}"));
		
	// var my_table = $('#my-table').DataTable({
	// 	"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
	// 	processing: true,
	// 	serverSide: true,
	// 	ajax: '{{$rounte}}',
	// 	columns: [
	// 		{ data: 'house_no', name: 'house' },
	// 		{ data: 'code', name: 'code' },
	// 		{ data: 'name', name: 'name' },
	// 		{ data: 'unit', name: 'unit' },
	// 		{ data: 'qty_std', name: 'qty_std' },
	// 		{ data: 'qty_add', name: 'qty_add' },
	// 		{ data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
	// 	],'fnCreatedRow':function(nRow,aData,iDataIndex){
	// 		if (objName) {
	// 			var obj = {
	// 				'id':aData['id'],
	// 				'house_id':aData['house_id'],
	// 				'item_id':aData['item_id'],
	// 				'unit':aData['unit'],
	// 				'qty_std':aData['qty_std'],
	// 				'qty_add':aData['qty_add'],
	// 			};
	// 			objName.push(obj);
	// 		}
	// 	}
	// });

	var my_table = $('#my-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		ajax: '{{$rounte}}',
		columns: [
			{
				className: 'details-control',
				orderable: false,
				searchable: false,
				data: null,
				defaultContent: ''
			},
			{ data: 'boq_house_code', name: 'boq_house_code' },
			{ data: 'house', name: 'house' },
			{ data: 'house_desc', name: 'house_desc' },
			{ data: 'created_by', name: 'created_by' },
			{ data: 'created_at', name: 'created_at' },
			// { data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
		],order: [[1, 'desc']]
		
	});
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
                    // str += '<th style="width: 10%;">{{trans("lang.action")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }
	$('#my-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = my_table.row(tr);
		console.log(row.data());
		var tableId = 'sub-' + row.data().id;
		if(row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
			objName = [];
		}else{
			row.child(format(row.data())).show();
			initTable(tableId,row.data());
			$('#' + tableId+'_wrapper').attr('style','width: 99%;');
			tr.addClass('shown');
		}
	});
	
	function initTable(tableId, data) {
		// console.log(data);
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
				{ data: 'boq_unit', name: 'unit_usage' },
				{ data: 'qty_std', name: 'qty_std' },
				{ data: 'qty_add', name: 'qty_add' },
				{ data: 'cost', name: 'cost' },
				// { data: 'action', name: 'action', class :'text-center', orderable: false, searchable: false}
			],'fnCreatedRow':function(nRow,aData,iDataIndex){
				if (objName) {
					var obj = {
						'id':aData['id'],
						'house_id':aData['house_id'],
						'item_id':aData['item_id'],
						'unit':aData['boq_unit'],
						'qty_std':aData['qty_std'],
						'qty_add':aData['qty_add'],
						'cost':aData['cost'],
					};
					objName.push(obj);
				}
			}
		});
	}
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
	
	function onEdit(field){
		var id = $(field).attr('row_id');
		var rounte = $(field).attr('row_rounte');
		var _token = $("input[name=_token]").val();
		$('.form-edit-boq').attr('action',rounte);
		$('.modal-edit-boq').children().find('div').children().find('h4').html('{{trans("lang.edit")}}');
		if(objName && jsonHouse){
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
		$('.button-submit-edit').attr('id','btnUpdate').attr('name','btnUpdate');
		$('.button-submit-edit').html('{{trans("lang.save_change")}}');
		$('.modal-edit-boq').modal('show');
		
		$("#btnUpdate").on('click',function(){
			$('#btnUpdate').prop('disabled', true);
			if(chkValid([".boq-street-edit",".boq-house-edit",".item",".unit",".qty_std",".qty_add"])){
				$('.form-edit-boq').submit();
			}else{
				$('#btnUpdate').prop('disabled', false);
				return false;
			}
		});
	}
	
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
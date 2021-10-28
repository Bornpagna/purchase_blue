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
					<!-- Download -->
					<a href="{{url('stock/use/download_excel')}}" title="{{trans('lang.download_example')}}" class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-file-excel-o"></i>
					</a>
					<!-- Import -->
					<a title="{{trans('lang.upload')}}" class="btn btn-circle btn-icon-only btn-default" id="btnUpload">
						<i class="icon-cloud-upload"></i>
					</a>
					@if(isset($rounteAdd))
						<a rounte="{{$rounteAdd}}" title="{{trans('lang.add_new')}}" class="btn btn-circle btn-icon-only btn-default" id="btnAdd">
							<i class="fa fa-plus"></i>
						</a>
					@endif

					{{-- <a title="{{trans('lang.update')}}" href="{{$rounteUpdateQty}}" class="btn btn-circle btn-icon-only btn-default" id="updateQty">
						<i class="icon-cloud-upload"></i>
					</a> --}}
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
				<table class="table table-striped table-bordered table-hover" id="my-table">
					<thead>
						<tr>
							<th style="width: 3%;" class="all"></th>
							<th width="10%" class="text-center all">{{ trans('lang.reference_no') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.trans_date') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.reference') }}</th>
							<th width="15%" class="text-center all">{{trans('lang.engineer')}}</th>
							@if(getSetting()->usage_constructor==1)
							<th width="15%" class="text-center all">{{trans('lang.sub_const')}}</th>
							@endif
							<th width="20%" class="text-center">{{ trans('lang.desc') }}</th>
							<th width="15%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@include('modal.upload')
@endsection()

@section('javascript')
<script type="text/javascript">	
	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 5%;">{{trans("lang.line_no")}}</th>';
                    str += '<th style="width: 25%;">{{trans("lang.items")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.units")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.qty")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.from_warehouse")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.on_house")}}</th>';
					str += '<th style="width: 10%;">{{trans("lang.street")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.note")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }

	onPrint = function(parent){
		var mywindow = window.open($(parent).attr('route'), "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=800,height=800");
		setInterval(function(){ 
			mywindow.print();
			mywindow.close();
		}, 3000);
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
			{data: 'ref_no', class:'text-center', name:'ref_no'},
			{data: 'trans_date', class:'text-center', name:'trans_date'},
			{data: 'reference', class:'text-center', name:'reference'},
			{data: 'engineer', name:'engineer'},
			@if(getSetting()->usage_constructor==1)
			{data: 'sub_constructor', name:'sub_constructor'},
			@endif
			{data: 'desc', name:'desc'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[1, 'desc']],fnCreatedRow:function(nRow, aData, iDataIndex){
			$('td:eq(2)',nRow).html(formatDate(aData['trans_date'])).addClass("text-center");
		}
	});
	
	$('#my-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = my_table.row(tr);
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
		$('#' + tableId).DataTable({
			processing: true,
			serverSide: true,
			language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
			paging:true,
			filter:true,
			info:true,
			ajax: data.details_url,
			columns: [
				{ data: 'line_no', name: 'line_no' },
				{ data: 'item', name: 'item' },
				{ data: 'unit', name: 'unit' },
				{ data: 'qty', name: 'qty' },
				{ data: 'from_warehouse', name: 'from_warehouse' },
				{ data: 'on_house', name: 'on_house' },
				{ data: 'street', name: 'street' },
				{ data: 'note', name: 'note' }
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(3)',nRow).html(formatNumber(aData['qty']));
			}
		});
	}
	
	$(function(){
		$("#btnAdd").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
	});	

	$(function(){
		$('#btnUpload').on('click',function(){
			$('.upload-excel-form').attr('action',"{{url('stock/use/import_excel')}}");
			$('.upload-excel-form').modal('show');
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
	});
	
	function onEdit(field){
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
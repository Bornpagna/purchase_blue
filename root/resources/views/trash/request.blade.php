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
							<th width="15%" class="text-center all">{{ trans('lang.reference_no') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.trans_date') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.delivery_date') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.request_by') }}</th>
							<th width="10%" class="text-center all">{{ trans('lang.department') }}</th>
							<th width="17%" class="text-center">{{ trans('lang.desc') }}</th>
							<th width="10%" class="text-center">{{ trans('lang.deleted_by') }}</th>
							<th width="15%" class="text-center">{{ trans('lang.deleted_at') }}</th>
							<th width="5%" class="text-center all">{{ trans('lang.action') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- Modal Varian -->
@endsection()

@section('javascript')
<script type="text/javascript">	
	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 5%;">{{trans("lang.line_no")}}</th>';
                    str += '<th style="width: 30%;">{{trans("lang.items")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.size")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.units")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.qty")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.desc")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.remark")}}</th>';
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
			{data: 'ref_no', class:'text-center', name:'ref_no'},
			{data: 'trans_date', class:'text-center', name:'trans_date'},
			{data: 'delivery_date', name:'delivery_date'},
			{data: 'request_by', name:'department'},
			{data: 'department', name:'request_by'},
			{data: 'note', name:'desc'},
			{data: 'updated_by', name:'updated_by'},
			{data: 'updated_at', name:'updated_at'},
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
				{ data: 'size', name: 'size' },
				{ data: 'unit', name: 'unit' },
				{ data: 'qty', name: 'qty' },
				{ data: 'desc', name: 'desc' },
				{ data: 'remark', name: 'remark' },
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(4)',nRow).html(formatNumber(aData['qty']));
			}
		});
	}
	
	function onRestore(field){
		var rounte = $(field).attr('row_rounte');
		$.confirm({
            title:'{{trans("lang.confirmation")}}',
            content:'{{trans("lang.content_restore")}}',
            autoClose: 'no|10000',
            buttons:{
                yes:{
                    text:'{{trans("lang.yes")}}',
                    btnClass: 'btn-success',
                    action:function(){
						alert('Under Contruction');
                        // window.location.href=rounte;
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
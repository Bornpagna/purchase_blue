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
		.small-pic{
			display: inline-block;
			vertical-align: middle;
			height: 30px;
		}
		.padding-content-20{
			padding:20px;
		}
		.padding-20{
			padding:10px;
		}
		.upload_boq_tab{
			border-top: 0px !important;
    		border: #ddd solid 1px;
		}
		.upload-nav-tab {
			margin-bottom: 0px !important;
		}
		a.disabled {
			pointer-events: none;
			cursor: default;
			color: #cccccc;
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
					<a href="{{url('stock/deliv/download_excel')}}" title="{{trans('lang.download_example')}}" class="btn btn-circle btn-icon-only btn-default">
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
				<ul class="nav nav-tabs upload-nav-tab" id="myTab" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ trans('lang.po_list') }}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ trans('lang.delivery_list') }}</a>
					</li>
				</ul>
				<div class="tab-content upload_boq_tab padding-20" id="myTabContent">
					<div class="tab-pane fade active in padding-20" id="home" role="tabpanel" aria-labelledby="home-tab">
						<table class="table table-striped table-bordered table-hover" id="order-table">
							<thead>
								<tr>
									<th style="width: 3%;" class="all"></th>
									<th width="15%" class="text-center all">{{ trans('lang.reference_no') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.pr_no') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.trans_date') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.delivery_date') }}</th>
									{{-- <th width="10%" class="text-center all">{{ trans('lang.sub_total') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.discount') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.grand_total') }}</th> --}}
									<th width="15%" class="text-center all">{{ trans('lang.status') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.action') }}</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="tab-pane fade padding-20" id="profile" role="tabpanel" aria-labelledby="profile-tab">
						<table class="table table-striped table-bordered table-hover" id="my-table">
							<thead>
								<tr>
									<th width="3%" class="all"></th>
									<th width="5%" class="all">{{ trans('lang.photo') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.reference_no') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.po_no') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.trans_date') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.supplier') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.desc') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.action') }}</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal Varian -->
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
					str += '<th style="width: 10%;">{{trans("lang.price")}}</th>';
                    str += '<th style="width: 15%;">{{trans("lang.to_warehouse")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.note")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }

	function formatOrderTable (d) {
        var str = '';
		str += '<table class="table table-striped" width="100%" >';
            str += '<thead>';
            str += '<tr>';
                str += '<th width="20%">{{ trans('lang.supplier') }} : </th>';
                str += '<td width="80%">'+d.supplier+'</td>';
            str += '</tr>';
            str += '<tr>';
                str += '<th width="20%">{{trans("lang.delivery_address")}} : </th>';
                str += '<td width="80%">'+d.warehouse+'</td>';
            str += '</tr>';
            str += '<tr>';
                str += '<th width="20%">{{trans("lang.ordered_by")}} : </th>';
                str += '<td width="80%">'+d.ordered_by+'</td>';
            str += '</tr>';
            // str += '<tr>';
            //     str += '<th width="20%">{{trans("lang.term_payment")}} : </th>';
            //     str += '<td width="80%">'+d.term_pay+'</td>';
            // str += '</tr>';
            str += '<tr>';
                str += '<th width="20%">{{trans("lang.desc")}} : </th>';
                str += '<td width="80%">'+d.note+'</td>';
            str += '</tr>';
            str += '</thead>';
        str += '</table>';
        str += '<table class="table table-striped details-table table-responsive"  id="subs-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 5%;">{{trans("lang.line_no")}}</th>';
                    str += '<th style="width: 25%;">{{trans("lang.items")}}</th>';
                    str += '<th style="width: 8%;">{{trans("lang.units")}}</th>';
                    str += '<th style="width: 8%;">{{trans("lang.qty")}}</th>';
                    // str += '<th style="width: 8%;">{{trans("lang.price")}}</th>';
                    // str += '<th style="width: 10%;">{{trans("lang.amount")}}</th>';
                    // str += '<th style="width: 10%;">{{trans("lang.discount")}}</th>';
                    // str += '<th style="width: 10%;">{{trans("lang.total")}}</th>';
					str += '<th style="width: 8%;">{{trans("lang.delivery_qty")}}</th>';
					str += '<th style="width: 8%;">{{trans("lang.closed_qty")}}</th>';
                    str += '<th style="width: 16%;">{{trans("lang.desc")}}</th>';
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
	
	function onZoom(id){
		var image=$('#'+id).attr('src');
		$.fancybox.open(image);
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
			{data: 'photo', class:'text-center', name:'photo'},
			{data: 'ref_no', class:'text-center', name:'ref_no'},
			{data: 'po_ref', class:'text-center', name:'po_ref'},
			{data: 'trans_date', class:'text-center', name:'trans_date'},
			{data: 'supplier', name:'supplier'},
			{data: 'note', name:'note'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[2, 'desc']],fnCreatedRow:function(nRow, aData, iDataIndex){
			$('td:eq(4)',nRow).html(formatDate(aData['trans_date'])).addClass("text-center");
			
			var pic='<img id="'+aData['id']+'" class="small-pic" src="{{ asset("assets/upload/picture/items/no-image.jpg")}}" onclick="onZoom(this.id);" />';
			if(aData['photo']!=null && aData['photo']!='' && aData['photo']!=0){
				pic = '<img id="'+aData['id']+'" class="small-pic" src="{{ asset("assets/upload/picture/delivery")}}/'+aData['photo']+'" onclick="onZoom(this.id);" />';
			}
			$('td:eq(1)',nRow).html(pic).addClass("text-center");
		}
	});
	var order_table = $('#order-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
		ajax: '{{$rounteOrder}}',
		columns: [
			{
				className: 'details-control',
				orderable: false,
				searchable: false,
				data: null,
				defaultContent: ''
			},
			{data: 'ref_no', class:'text-center', name:'ref_no'},
			{data: 'pr_no', class:'text-center', name:'pr_no'},
			{data: 'trans_date', class:'text-center', name:'trans_date'},
			{data: 'delivery_date', name:'delivery_date'},
			// {data: 'sub_total', name:'sub_total'},
			// {data: 'disc_usd', name:'disc_usd'},
			// {data: 'grand_total', name:'grand_total'},
			{data: 'trans_status', name:'trans_status'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[3, 'desc']],fnCreatedRow:function(nRow, aData, iDataIndex){
			$('td:eq(3)',nRow).html(formatDate(aData['trans_date']));
			$('td:eq(4)',nRow).html(formatDate(aData['delivery_date']));
			// $('td:eq(5)',nRow).html(formatDollar(aData['sub_total'])).addClass("text-right");
			// $('td:eq(6)',nRow).html(formatDollar(aData['disc_usd'])).addClass("text-right");
			// $('td:eq(7)',nRow).html(formatDollar(aData['grand_total'])).addClass("text-right");
			$status = '';
			// Pending
			if(aData['trans_status']==1){
				$status = '<span class="label label-warning" style="font-size: smaller;">{{trans("lang.pending")}}</span>';
			}
			// Approving
			else if(aData['trans_status']==2){
				$status = '<span class="label label-info" style="font-size: smaller;">{{trans("lang.approving")}}</span>';
			}
			// Complete
			else if(aData['trans_status']==3){
				$status = '<span class="label label-success" style="font-size: smaller;">{{trans("lang.completed")}}</span>';
			}
			// Draft
			else if(aData['trans_status']==5){
				$status = '<span class="label label-draft" style="font-size: smaller;">{{trans("lang.draft")}}</span>';
			}
			// Reject
			else{
				$status = '<span class="label label-danger" style="font-size: smaller;">{{trans("lang.rejected")}}</span>';
			}
			$('td:eq(5)',nRow).html($status).addClass("text-center");
			$action = '';
			// $action = '<a row_rounte="{{url("purch/order/print_deliver_note")}}/'+aData.id+'/1" onclick="onPrintOrder(this)">{{trans("lang.view")}}</a>';
			
			if(aData.is_closed == 0 && aData['trans_status'] == 3){
				// console.log(aData.is_closed);
				$action += '<a href="{{url("stock/deliv/make_delivery")}}/'+aData.id+'">{{trans("lang.make_delivery")}}</a>';
			}else{
				$action += '<a href="#" class="close-record disabled" disabled>{{trans("lang.make_delivery")}}</a>';
			}
			
			
			$('td:eq(6)',nRow).html($action);
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
				{ data: 'price', name: 'price' },
				{ data: 'warehouse', name: 'warehouse' },
				{ data: 'desc', name: 'desc' }
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(3)',nRow).html(formatNumber(aData['qty']));
			}
		});
	}

	$('#order-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).parent('tr');
		var row = order_table.row(tr);
		var tableId = 'subs-' + row.data().id;
		if(row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
			objName = [];
		}else{
			row.child(formatOrderTable(row.data())).show();
			initTableOrder(tableId,row.data());
			$('#' + tableId+'_wrapper').attr('style','width: 99%;');
			tr.addClass('shown');
		}
	});
	
	function initTableOrder(tableId, data) {
		console.log(data);
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
				// { data: 'price', name: 'price' },
				// { data: 'amount', name: 'amount' },
				// { data: 'disc_usd', name: 'disc_usd' },
				// { data: 'total', name: 'total' },
				{ data: 'deliv_qty', name: 'deliv_qty'},
				{ data: 'closed_qty', name: 'closed_qty'},
				{ data: 'desc', name: 'desc' }
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(3)',nRow).html(formatNumber(aData['qty']));
				// $('td:eq(4)',nRow).html(formatDollar(aData['price']));
				// $('td:eq(5)',nRow).html(formatDollar(aData['amount']));
				// $('td:eq(6)',nRow).html(formatDollar(aData['disc_usd']));
				// $('td:eq(7)',nRow).html(formatDollar(aData['total']));
				
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
			$('.upload-excel-form').attr('action',"{{url('stock/deliv/import_excel')}}");
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

	function onPrintOrder(field){
		var rounte = $(field).attr('row_rounte');
		console.log(rounte);
		window.open(rounte, '_blank', 'width=735, height=891');
	}
</script>
@endsection()
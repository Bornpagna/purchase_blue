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

		td.request-details-control {
            background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
            cursor: pointer !important;
        }
        tr.shown td.request-details-control {
            background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
        }
		.label-draft {
			background-color: #7236d3;
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
		.padding-20{
			padding:10px;
		}
		a.disabled {
		/* Make the disabled links grayish*/
		color: gray;
		/* And disable the pointer events */
		pointer-events: none;
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
					<a href="{{url('purch/order/download_excel')}}" title="{{trans('lang.download_example')}}" class="btn btn-circle btn-icon-only btn-default">
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
				<?php if(Session::has('status')):?>
					<div class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal" style="display: block;">
						<div class="modal-dialog modal-sm raduis-10">
							<div class="modal-content raduis-10">
								<div class="modal-header color-white">
									<button type="button" id="close_modal" onclick="dismissModal()" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">{{ trans('lang.reminder') }}</h4>
								</div>
								<div class="modal-body">
									<span class="confirm_body">{{ trans('lang.order_closed_message') }}</span>
									<span class="confirm_body">{{ trans('lang.do_you_want_to_make_new_order') }}</span>
								</div>
								<div class="modal-footer color-white">
									<button type="button" class="btn btn-primary" id="modal-btn-no" onclick="dismissModal()">{{ trans('lang.no') }}</button>
									<button rounte="{{$rounteOrder}}" type="button" class="btn btn-primary" id="modal-btn-yes" onclick="makeOrder(this)">{{ trans('lang.yes') }}</button>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<ul class="nav nav-tabs upload-nav-tab" id="myTab" role="tablist">
					<li class="nav-item active">
						<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ trans('lang.pr_list') }}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ trans('lang.po_list') }}</a>
					</li>
				</ul>
				<div class="tab-content upload_boq_tab padding-20" id="myTabContent">
					<div class="tab-pane fade active in padding-20" id="home" role="tabpanel" aria-labelledby="home-tab">
						
						<?php if(Session::has('success')):?>
							<div class="alert alert-success display-show">
								<button class="close" data-close="alert"></button><strong>{{trans('lang.success')}}!</strong> {{Session::get('success')}} 
							</div>
						<?php elseif(Session::has('error')):?>
							<div class="alert alert-danger display-show">
								<button class="close" data-close="alert"></button><strong>{{trans('lang.error')}}!</strong> {{Session::get('error')}} 
							</div>
						<?php elseif(Session::has('status')):?>
							<div class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
								<div class="modal-dialog modal-sm raduis-10">
									<div class="modal-content raduis-10">
										<div class="modal-header color-white">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="myModalLabel">{{ trans('lang.warning') }}</h4>
										</div>
										<div class="modal-body">
											<span class="confirm_body"></span>
										</div>
										<div class="modal-footer color-white">
										<button type="button" class="btn btn-primary" id="modal-btn-no">{{ trans('lang.yes') }}</button>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<table class="table table-striped table-bordered table-hover" id="request-table">
							<thead>
								<tr>
									<th style="width: 3%;" class="all"></th>
									<th width="10%" class="text-center all">{{ trans('lang.reference_no') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.trans_date') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.delivery_date') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.request_by') }}</th>
									<th width="15%" class="text-center all">{{ trans('lang.department') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.status') }}</th>
									<th width="25%" class="text-center">{{ trans('lang.desc') }}</th>
									<th width="7%" class="text-center all">{{ trans('lang.action') }}</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<div class="tab-pane fade padding-20" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
									<th width="15%" class="text-center all">{{ trans('lang.pr_no') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.trans_date') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.delivery_date') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.sub_total') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.discount') }}</th>
									<th width="10%" class="text-center all">{{ trans('lang.grand_total') }}</th>
									<th width="5%" class="text-center all">{{ trans('lang.status') }}</th>
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
</div>
<!-- Modal Varian -->
@include('modal.upload')
@include('modal.approve_step')

@endsection()

@section('javascript')
<script type="text/javascript">	
	var objApproval = JSON.parse(convertQuot("{{\App\Model\ApproveOrder::select('po_id','role_id','approved_date','reject','approved_by',DB::raw('(SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id`=approved_by)AS approve_name'))->get()}}"));
	function formatRequest (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
					str += '<th style="width: 5%;">{{trans("lang.line_no")}}</th>';
                    str += '<th style="width: 30%;">{{trans("lang.items")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.size")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.units")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.qty")}}</th>';
					str += '<th style="width: 10%;">{{trans("lang.ordered_qty")}}</th>';
					str += '<th style="width: 10%;">{{trans("lang.closed_qty")}}</th>';
                    str += '<th style="width: 20%;">{{trans("lang.desc")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.remark")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }
	
	var request_table = $('#request-table').DataTable({
		"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
		processing: true,
		serverSide: true,
		language: {"url": "{{url('assets/lang').'/'.config('app.locale').'.json'}}"},
		ajax: '{{$rounte_request}}',
		columns: [
			{
				className: 'request-details-control',
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
			{data: 'trans_status', name:'trans_status'},
			{data: 'note', name:'desc'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],
		order: [[2, 'desc']],
		fnCreatedRow:function(nRow, aData, iDataIndex){
			console.log(aData);
			$('td:eq(2)',nRow).html(formatDate(aData['trans_date']));
			$('td:eq(3)',nRow).html(formatDate(aData['delivery_date']));
			$status = '';
			// Pending
			if(aData['trans_status']==1 && aData["is_ordered"] == 0){
				$status = '<span class="label label-warning" style="font-size: smaller;">{{trans("lang.pendding")}}</span>';
			}
			// Approving
			else if(aData['trans_status']==2 && aData["is_ordered"] == 0){
				$status = '<span class="label label-info" style="font-size: smaller;">{{trans("lang.approving")}}</span>';
			}
			// Complete
			else if(aData['trans_status']==3 && aData["is_ordered"] == 0){
				$status = '<span class="label label-success" style="font-size: smaller;">{{trans("lang.completed")}}</span>';
			}
			// Draft
			else if(aData['trans_status']==5 && aData["is_ordered"] == 0){
				$status = '<span class="label label-draft" style="font-size: smaller;">{{trans("lang.draft")}}</span>';
			}
			// Ordered
			else if(aData["trans_status"] == 3 && aData["is_ordered"] == 1){
				$status = '<span class="label label-draft" style="font-size: smaller;">{{trans("lang.ordered")}}</span>';
			}
			// Reject
			else{
				$status = '<span class="label label-danger" style="font-size: smaller;">{{trans("lang.rejected")}}</span>';
			}
			
			$('td:eq(6)',nRow).html($status).addClass("text-center");
			if((aData['ordered_qty'] != aData['total_qty'])){
				$action = '<a onclick="onEdit(this)" title="{{trans("lang.make_order")}}" row_id="'+aData.id+'" row_rounte="{{url("purch/order/makeOrder")}}/'+aData.id+'">{{trans("lang.make_order")}}</a>';
			}else{
				$action = '<a class="disabled" title="{{trans("lang.make_order")}}" row_id="'+aData.id+'" >{{trans("lang.make_order")}}</a>';
			}
			$('td:eq(8)',nRow).html($action).addClass("text-center");
		}
	});
	
	$('#request-table tbody').on('click', 'td.request-details-control', function () {
		var tr = $(this).closest('tr');
		var row = request_table.row(tr);
		var tableId = "";
		tableId = 'sub-' + row.data().id;
		console.log(tableId);
		if(row.child.isShown()) {
			row.child.hide();
			tr.removeClass('shown');
			objName = [];
		}else{
			row.child(formatRequest(row.data())).show();
			initTableRequest(tableId,row.data());
			$('#' + tableId+'_wrapper').attr('style','width: 99%;');
			tr.addClass('shown');
		}
	});
	
	function initTableRequest(tableId, data) {
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
				{ data: 'size', name: 'size' },
				{ data: 'unit', name: 'unit' },
				{ data: 'qty', name: 'qty' },
				{ data: 'ordered_qty', name: 'ordered_qty' },
				{ data: 'closed_qty', name: 'closed_qty' },
				{ data: 'desc', name: 'desc' },
				{ data: 'remark', name: 'remark' },
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(4)',nRow).html(formatNumber(aData['qty']));
			}
		});
	}
	////// Order  ///////////
	function format (d) {
        var str = '';
		str += '<table class="table table-striped" width="100%">';
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
            str += '<tr>';
                str += '<th width="20%">{{trans("lang.term_payment")}} : </th>';
                str += '<td width="80%">'+d.term_pay+'</td>';
            str += '</tr>';
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
                    str += '<th style="width: 8%;">{{trans("lang.price")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.amount")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.discount")}}</th>';
                    str += '<th style="width: 10%;">{{trans("lang.total")}}</th>';
					str += '<th style="width: 8%;">{{trans("lang.delivery_qty")}}</th>';
					str += '<th style="width: 8%;">{{trans("lang.closed_qty")}}</th>';
                    str += '<th style="width: 16%;">{{trans("lang.desc")}}</th>';
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
			{data: 'pr_no', class:'text-center', name:'pr_no'},
			{data: 'trans_date', class:'text-center', name:'trans_date'},
			{data: 'delivery_date', name:'delivery_date'},
			{data: 'sub_total', name:'sub_total'},
			{data: 'disc_usd', name:'disc_usd'},
			{data: 'grand_total', name:'grand_total'},
			{data: 'trans_status', name:'trans_status'},
			{data: 'action', class :'text-center', orderable: false, searchable: false},
		],order: [[3, 'desc']],fnCreatedRow:function(nRow, aData, iDataIndex){
			$('td:eq(3)',nRow).html(formatDate(aData['trans_date']));
			$('td:eq(4)',nRow).html(formatDate(aData['delivery_date']));
			$('td:eq(5)',nRow).html(formatDollar(aData['sub_total'])).addClass("text-right");
			$('td:eq(6)',nRow).html(formatDollar(aData['disc_usd'])).addClass("text-right");
			$('td:eq(7)',nRow).html(formatDollar(aData['grand_total'])).addClass("text-right");
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
			$('td:eq(8)',nRow).html($status).addClass("text-center");
		}
	});
	
	$('#my-table tbody').on('click', 'td.details-control', function () {
		var tr = $(this).parents('tr');
		var row = my_table.row(tr);
		var rRow = request_table.row(tr);
		
		$('#request-table tr').removeClass('shown');
		var tableId = "";
		tableId = 'subs-' + row.data().id;
		
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
				{ data: 'amount', name: 'amount' },
				{ data: 'disc_usd', name: 'disc_usd' },
				{ data: 'total', name: 'total' },
				{ data: 'deliv_qty', name: 'deliv_qty'},
				{ data: 'closed_qty', name: 'closed_qty'},
				{ data: 'desc', name: 'desc' }
			],fnCreatedRow:function(nRow, aData, iDataIndex){
				$('td:eq(3)',nRow).html(formatNumber(aData['qty']));
				$('td:eq(4)',nRow).html(formatDollar(aData['price']));
				$('td:eq(5)',nRow).html(formatDollar(aData['amount']));
				$('td:eq(6)',nRow).html(formatDollar(aData['disc_usd']));
				$('td:eq(7)',nRow).html(formatDollar(aData['total']));
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
			$('.upload-excel-form').attr('action',"{{url('purch/order/import_excel')}}");
			$('.upload-excel-form').modal('show');
			$('#btn_upload_excel').on('click',function(){
				if(onUploadExcel()){}else{return false}
			}); 
		});
	});	
	
	function onPrint(field){
		var rounte = $(field).attr('row_rounte');
		window.open(rounte, '_blank', 'width=735, height=891');
	}
	
	function onEdit(field){
		var rounte = $(field).attr('row_rounte');
		window.location.href=rounte;
	}
	
	function onCopy(field){
		var rounte = $(field).attr('row_rounte');
		alert('Under Construction.');
	}
	
	function onClose(field){
		var rounte = $(field).attr('row_rounte');
		$.confirm({
            title:'{{trans("lang.confirmation")}}',
            content:'{{trans("lang.content_close")}}',
            autoClose: 'no|10000',
            buttons:{
				yes:{
                    text:'{{trans("lang.yes")}}',
                    btnClass: 'btn-success',
                    action:function(){
						console.log(rounte+"/1");
                        window.location.href=rounte+"/1";
                    }
                },
                // yes:{
                //     text:'{{trans("lang.yes")}}',
                //     btnClass: 'btn-success',
                //     action:function(){
				// 		console.log(rounte);
                //         window.location.href=rounte;
                //     }
                // },
                no:{
                    text:'{{trans("lang.no")}}',
                    btnClass: 'btn-danger',
                    action:function(){}
                }
            }
        });
	}
	
	function responseStepApprove(url) {
        return $.ajax({url:url,type:'get',async:false}).responseJSON;
    }
	
	function generateStep(data){
		var classes='';
		var length=data.length;
		var col= 12 / length;
		var str='';
		$.each(data,function(k,v){
			if(k==0){
				classes=' first';
			}
			if((k+1)==length){
				classes=' last';
			}
			str+='<div class="col-md-'+col+' mt-step-col '+classes+'" id="step-approve-'+v.role_id+'">';
			str+='<div class="mt-step-number bg-white font-grey">'+(k+1)+'</div>';
			str+='<div class="mt-step-title uppercase font-grey-cascade">'+v.name+'</div>';
			str+='<div class="mt-step-content font-grey-cascade" id="user-approve-'+v.role_id+'">Not Available</div>';
			str+='<div class="mt-step-content font-grey-cascade">'+formatDollar(v.role_min_amount)+' -> '+formatDollar(v.role_max_amount)+'</div>';
			str+='<div class="mt-step-content font-grey-cascade" id="date-approve-'+v.role_id+'"></div>';
			str+='</div>';
			classes='';
		});
		return str;
	}
	
	function onView(field){
		var pr_id = $(field).attr('row_id');
		var url = $(field).attr('row_rounte');
		var step = responseStepApprove(url);
		var str = generateStep(step);
		$("#show-step").empty();
		$("#step_line").text(step.length+" {{trans('lang.step_to_approve')}}");
		$("#show-step").append(str);
		if(objApproval){
			$.each(objApproval.filter(c=>c.po_id==pr_id), function(key, val){
				var done = 'done';
				var approve_by = "{{trans('lang.approved_by')}}";
				if(val.reject==1){
					done = 'active';
					approve_by = "{{trans('lang.approved_by')}}";
				}else if(val.reject==2){
					done = 'error';
					approve_by = "{{trans('lang.rejected_by')}}";
				}
				$("#step-approve-"+val.role_id).addClass(done);
				$("#user-approve-"+val.role_id).text(approve_by+' : '+val.approve_name);
				$("#date-approve-"+val.role_id).text(val.approved_date);
			});
		}
		$('.approve-modal').children().find('div').children().find('h4').html('{{trans("lang.view")}}');
		$('.approve-modal').modal('show');
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
	function dismissModal(){
		$('#mi-modal').hide();
	}
	function makeOrder(field){
		var rounte = $(field).attr('rounte');
		console.log(rounte);
		window.location.href=rounte;
	}
</script>
@endsection()
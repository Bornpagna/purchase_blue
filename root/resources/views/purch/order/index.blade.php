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
		.label-draft {
			background-color: #7236d3;
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
<!-- Modal Varian -->
@include('modal.upload')
@include('modal.approve_step')

@endsection()

@section('javascript')
<script type="text/javascript">	
	var objApproval = JSON.parse(convertQuot("{{\App\Model\ApproveOrder::select('po_id','role_id','approved_date','reject','approved_by',DB::raw('(SELECT pr_users.`name` FROM pr_users WHERE pr_users.`id`=approved_by)AS approve_name'))->get()}}"));

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
        str += '<table class="table table-striped details-table table-responsive"  id="sub-'+d.id+'">';
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
				{ data: 'amount', name: 'amount' },
				{ data: 'disc_usd', name: 'disc_usd' },
				{ data: 'total', name: 'total' },
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
</script>
@endsection()
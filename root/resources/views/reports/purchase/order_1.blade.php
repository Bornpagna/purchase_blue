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
<?php 
	$start_date   = '';
	$end_date     = '';
	$depArray     = [];
	$statusArray  = [];
	$supArray     = [];
	$dep_id       = '';
	$sup_id       = '';
	$trans_status = '';
	$param        = '?v=1';
	$start        = 0;
    if (Request::input('start_date')) {
        $start_date = Request::input('start_date');
        $param.='&start_date='.$start_date;
    }else{
        $start_date = date('Y-m-d');
        $param.='&start_date='.$start_date;
    }

    if (Request::input('end_date')) {
        $end_date = Request::input('end_date');
        $param.='&end_date='.$end_date;
    }else{
        $end_date = date('Y-m-d');
        $param.='&end_date='.$end_date;
    }

    if (Request::input('dep_id')) {
        $dep_id = '';
        foreach (Request::input('dep_id') as $jkey => $jv) {
            $dep_id.=','.$_POST['dep_id'][$jkey];
            $depArray[$jkey] = $_POST['dep_id'][$jkey];
        }
        $param.='&dep_id='.$dep_id;
    }else{
        $param.='&dep_id='.$dep_id;
    }

    if (Request::input('sup_id')) {
        $sup_id = '';
        foreach (Request::input('sup_id') as $jkey => $jv) {
            $sup_id.=','.$_POST['sup_id'][$jkey];
            $supArray[$jkey] = $_POST['sup_id'][$jkey];
        }
        $param.='&sup_id='.$sup_id;
    }else{
        $param.='&sup_id='.$sup_id;
    }
?>
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
					<a title="{{trans('lang.print')}}" onclick="onPrint(this);" version="print" class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-print"></i>
					</a>
					<a title="{{trans('lang.download')}}" onclick="onPrint(this);" version="excel"  class="btn btn-circle btn-icon-only btn-default">
						<i class="fa fa-file-excel-o"></i>
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
				<?php if(Session::has('bug') && count(Session::get('bug')>0)): ?>
					<?php
						echo '<div class="alert alert-danger display-show"><button class="close" data-close="alert"></button>';
						foreach(Session::get('bug') as $key=>$val){
								echo '<strong>'.trans('lang.error').'!</strong>'.trans('lang.dublicate_at_record').' '.$val['index'].'<br/>';
						}
						echo '</div>';
					?>
				<?php endif; ?>
				<div class="portlet-body" style="padding-bottom: 10px;">
	                <form method="post">
	                    {{csrf_field()}}
	                    <input type="hidden" value="{{$start_date}}" name="start_date" id="start_date">
	                    <input type="hidden" value="{{$end_date}}" name="end_date" id="end_date">
	                    <div class="portlet-body form-horizontal" style="border: 1px solid #72aee2;padding: 5px 0px;background: #f8f9fb;">
	                        <div class="col-md-12">
	                            <div class="form-group">
	                                <div class="col-md-4">
	                                    <label class="control-label bold">{{trans('lang.created_at')}}</label>
	                                    <div id="report_date" class="btn btn-info" style="width: 100%;">
	                                        <i class="fa fa-calendar"></i> &nbsp;
	                                        <span> </span>
	                                        <b class="fa fa-angle-down"></b>
	                                    </div>
	                                </div>
	                                <div class="col-md-4">
	                                    <label for="sup_id" class="control-label bold">{{trans('lang.supplier')}}</label>
	                                    <select class="form-control" id="sup_id" name="sup_id[]" multiple>
	                                        {{getSuppliers()}}
	                                    </select>
	                                </div>
	                                <div class="col-md-4">
	                                    <label for="dep_id" class="control-label bold">{{trans('lang.department')}}</label>
	                                    <select class="form-control" id="dep_id" name="dep_id[]" multiple>
	                                        {{getSystemData('DP')}}
	                                    </select>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="col-md-12">
	                            <div class="form-group">
	                                <div class="col-md-6">
	                                    
	                                </div>
	                                <div class="col-md-6 text-right">
	                                    <button type="submit" class="btn btn-primary" id="btnSearch" name="btnSearch"><i class="fa fa-refresh"></i>&nbsp;{{trans('rep.search')}}</button>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="clearfix"></div>
	                    </div>
	                </form>
	            </div>
				<table class="table table-striped table-bordered table-hover" id="my-table">
					<thead style="font-size:10px !important;">
						<tr>
							<th width="5%" class="all">{{ trans('lang.trans_date') }}</th>
							<th width="5%" class="all">{{ trans('lang.reference_no') }}</th>
							<th width="5%" class="all">{{ trans('lang.request_no') }}</th>
							<th width="5%" class="none">{{ trans('lang.department') }}</th>
							<th width="5%" class="none">{{ trans('lang.supplier') }}</th>
							<th width="5%" class="none">{{ trans('lang.delivery_date') }}</th>
							<th width="5%" class="none">{{ trans('lang.delivery_address') }}</th>
							<th width="5%" class="all">{{ trans('lang.item_code') }}</th>
							<th width="15%" class="all">{{ trans('lang.item_name') }}</th>
							<th width="6%" class="all">{{ trans('lang.qty') }}</th>
							<th width="6%" class="all">{{ trans('rep.delivery_qty') }}</th>
							<th width="6%" class="all">{{ trans('rep.closed_qty') }}</th>
							<th width="6%" class="all">{{ trans('lang.units') }}</th>
							<th width="6%" class="all">{{ trans('lang.price') }}</th>
							<th width="6%" class="all">{{ trans('lang.amount') }}</th>
						</tr>
					</thead>
					<tbody style="font-size:10px !important;"></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="invoice" style="display: none;">
    @include('reports.header')
    <div style="width: -webkit-fill-available;">
        <span style="position: absolute;
        margin: 153px 0px 0px 0px;
        width: -webkit-fill-available;
        font-size: 12px;
        font-family: myKhBattambang;
        font-weight: bold;">*{{trans("rep.start_date")}} :.....................{{trans("rep.end_date")}} :.....................</span>
        <span style="position: absolute;
        margin: 149px 0px 0px 45px;
        width: -webkit-fill-available;
        font-size: 12px;
        font-family: myKhBattambang;
        font-weight: bold;
        color: {{getSetting()->report_header_color}};">{{date('d/m/Y',strtotime($start_date))}}</span>
        <span style="position: absolute;
        margin: 149px 0px 0px 144px;
        width: -webkit-fill-available;
        font-size: 12px;
        font-family: myKhBattambang;
        font-weight: bold;
        color: {{getSetting()->report_header_color}};">{{date('d/m/Y',strtotime($end_date))}}</span>
    </div>
    <style type="text/css">
        .invoice-table th {
            font-family: myKhBattambang !important;
            background-color: {{getSetting()->report_header_color}} !important;
            color: white !important;
            border-top: 1px solid {{getSetting()->report_header_color}} !important;
            border-bottom: 1px solid {{getSetting()->report_header_color}} !important;
            border-right: 1px solid {{getSetting()->report_header_color}} !important;
            border-left: 1px solid {{getSetting()->report_header_color}} !important;
            padding: 1px !important;
            font-size: 8px !important;
            text-align: center !important;
        }

        .invoice-table td {
            font-size: 7px !important;
            font-family: myKhBattambang !important;
            padding: 1px 1px 1px 1px !important;
            border-top: 1px dotted #9E9E9E !important;
            border-bottom: 1px dotted #9E9E9E !important;
            border-right: 1px solid #fff0 !important;
            border-left: 1px solid #fff0 !important;
        }
        
        .invoice-table-sub td{
            font-size: 7px !important;
            font-family: myKhBattambang !important;
            padding: 1px 1px 1px 1px !important;
            border-top: 1px solid #fff !important;
            border-bottom: 1px solid #fff !important;
            border-right: 1px solid #fff !important;
            border-left: 1px solid #fff !important; 
        }

        .invoice-table-sub th{
            font-family: myKhBattambang !important;
            background-color: #0f92b1 !important;
            color: white !important;
            border-top: 1px solid #0f92b1 !important;
            border-bottom: 1px solid #0f92b1 !important;
            border-right: 1px solid #0f92b1 !important;
            border-left: 1px solid #0f92b1 !important;
            padding: 1px !important;
            font-size: 7px !important;
            text-align: center !important;
        }

    </style>
    <div class="invoice-items">
        <div class="div-table">
            <table class="invoice-table">
                <thead>
                    <tr>
						<th width="6%" class="all">{{ trans('lang.trans_date') }}</th>
						<th width="6%" class="all">{{ trans('lang.reference_no') }}</th>
						<th width="6%" class="all">{{ trans('lang.request_no') }}</th>
						<th width="6%" class="none">{{ trans('lang.department') }}</th>
						<th width="6%" class="none">{{ trans('lang.supplier') }}</th>
						<th width="6%" class="none">{{ trans('lang.delivery_date') }}</th>
						<th width="6%" class="none">{{ trans('lang.delivery_address') }}</th>
						<th width="6%" class="all">{{ trans('lang.item_code') }}</th>
						<th width="7%" class="all">{{ trans('lang.item_name') }}</th>
						<th width="6%" class="all">{{ trans('lang.qty') }}</th>
						<th width="6%" class="all">{{ trans('rep.delivery_qty') }}</th>
						<th width="6%" class="all">{{ trans('rep.closed_qty') }}</th>
						<th width="6%" class="all">{{ trans('lang.units') }}</th>
						<th width="6%" class="all">{{ trans('lang.price') }}</th>
						<th width="6%" class="all">{{ trans('lang.amount') }}</th>
                    </tr>
                </thead>
                <tbody class="invoice-table-tbody"></tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Varian -->
@endsection()

@section('javascript')
<script type="text/javascript">

	function generatePrint(response) {
        if (response) {
            var div = $('.invoice-table-tbody');
            div.empty();
			var divString   = '';
			var project     = [];
			var request_obj = [];

            $.each(response,function(key,val){
	            divString += '<tr style="background: #fff !important;">';
	            divString += '<td style="text-align:center !important;" class="black-all">'+formatDate(val.trans_date)+'</td>';
	            divString += '<td style="text-align:center !important;" class="black-all">'+val.ref_no+'</td>';
	            divString += '<td style="width:10%;text-align:center !important;" class="black-all">'+val.pr_no+'</td>';
	            divString += '<td style="text-align:center !important;" class="black-all">'+(val.department!=null ? val.department : '')+'</td>';
	            divString += '<td style="width:10%;text-align:center !important;" class="black-all">'+val.supplier+'</td>';
	            divString += '<td style="text-align:center !important;" class="black-all">'+(val.delivery_date!=null ? formatDate(val.delivery_date) : '')+'</td>';
	            divString += '<td style="width:8%;text-align:center !important;" class="black-all">'+(val.delivery_addr_name!=null ? val.delivery_addr_name : '')+'</td>';
	            divString += '<td style="text-align:center !important;" class="black-all">'+val.item_code+'</td>';
	            divString += '<td style="width:20%;text-align:left !important;" class="black-all">'+val.item_name+'</td>';
	            divString += '<td style="text-align:right !important;" class="black-all">'+formatNumber(val.qty)+'</td>';
	            divString += '<td style="text-align:right !important;" class="black-all">'+formatNumber(val.deliv_qty)+'</td>';
	            divString += '<td style="text-align:right !important;" class="black-all">'+formatNumber(val.closed_qty)+'</td>';
	            divString += '<td style="text-align:center !important;" class="black-all">'+val.unit+'</td>';
	            divString += '<td style="width:8%;text-align:right !important;" class="black-all">'+formatDollar(val.price)+'</td>';
	            divString += '<td style="width:8%;text-align:right !important;" class="black-all">'+formatDollar(val.amount)+'</td>';
	            divString += '</tr>';
	        });
            div.append(divString);
        }
        diplayPrint();
    }

    function diplayPrint() {
        var strInvioce=$('.invoice').html();
        var styleInvoice = $('.style-invoice').html();
        var popupWin = window.open('', '_blank', 'width=714,height=800');
        var printInvoice = '<html>';
            printInvoice += '<head>';
            printInvoice += '<title></title>';
            printInvoice += styleInvoice;
            printInvoice += '</head>';
            printInvoice += '<body>';
            printInvoice += strInvioce;
            printInvoice += '</body>';
            printInvoice += '</html>';
        popupWin.document.open();
        popupWin.document.write(printInvoice);
        popupWin.print();
    }

	function onPrint(argument) {
		var version = $(argument).attr('version');
		if (version=='print') {
			$.ajax({
				url:'<?php echo url("/report/purchase/order/generate_order_1").$param;?>&version='+version,
				type:'GET',
				success:function(response){
					generatePrint(response);
				}
			});
		}else if(version=='excel'){
			window.location.href="<?php echo url("/report/purchase/order/generate_order_1").$param;?>&version="+version;
		}
	}

	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive">';
        	str += '<tbody>';
        		str += '<tr>';
        			str += '<td>{{trans("rep.project")}}</td>';
        			str += '<td>'+d.project+'</td>';
        			str += '<td>{{trans("lang.ordered_by")}}</td>';
        			str += '<td>'+d.ordered_people_name+' - '+d.ordered_people_code+'</td>';
        			str += '<td>{{trans("lang.note")}}</td>';
        			str += '<td>'+d.note+'</td>';
        		str += '</tr>';
        		str += '<tr>';
        			str += '<td>{{trans("lang.delivery_date")}}</td>';
        			str += '<td>'+d.delivery_date+'</td>';
        			str += '<td>{{trans("lang.delivery_address")}}</td>';
        			str += '<td>'+d.delivery_address+'</td>';
        			str += '<td></td>';
        			str += '<td></td>';
        		str += '</tr>';
        	str += '</tbody>';
        str += '</table>';
        str += '<table class="table table-striped details-table table-responsive"  id="posts-'+d.po_id+'">';
            str += '<thead>';
                str += '<tr>';
                    str += '<th class="text-center" style="width: 3%;">{{trans("lang.line_no")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.item_code")}}</th>';
                    str += '<th class="text-center" style="width: 17%;">{{trans("lang.item_name")}}</th>';
                    str += '<th class="text-center" style="width: 5%;">{{trans("lang.qty")}}</th>';
                    str += '<th class="text-center" style="width: 5%;">{{trans("lang.units")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.price")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.amount")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.discount")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.discount")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.total")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.desc")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }

	$(document).ready(function(){

		// $.fn.select2.defaults.set('theme','classic');
        $('#dep_id,#sup_id').select2({width:'100%',placeholder:'{{trans("lang.please_choose")}}',allowClear:true});

        var start_date = '{{$start_date}}';
        var end_date = '{{$end_date}}';

        if(start_date=='' || start_date==null){
            var date  = Date.parse(jsonStartDate[0].start_date);
            start_date = date.toString('MMMM d, yyyy');
        }else{
            var date =  Date.parse(start_date);
            start_date = date.toString('MMMM d, yyyy');
        }

        if(end_date=='' || end_date==null){
            var date  = Date.parse(jsonEndDate[0].end_date);
            end_date = date.toString('MMMM d, yyyy');
        }else{
            var date  = Date.parse(end_date);
            end_date = date.toString('MMMM d, yyyy');
        }
        $('#report_date span').html(start_date + ' - ' + end_date);
        $('#report_date').show();

        $('#dep_id').select2('val',<?php echo json_encode($depArray);?>);
        $('#sup_id').select2('val',<?php echo json_encode($supArray);?>);

		var table = $('#my-table').DataTable({
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{trans("lang.all")}}"]],
			processing: true,
			serverSide: true,
			ajax: '<?php echo url("/report/purchase/order/generate_order_1").$param;?>&version=datatables&group_by=1',
			columns: [
				{data: 'trans_date', name:'trans_date'},
				{data: 'ref_no', name:'ref_no'},
				{data: 'pr_no', name:'pr_no'},
				{data: 'department', name:'department'},
				{data: 'supplier', name:'supplier'},
				{data: 'delivery_date', name:'delivery_date'},
				{data: 'delivery_addr_name', name:'delivery_addr_name'},
				{data: 'item_code', name:'item_code'},
				{data: 'item_name', name:'item_name'},
				{data: 'qty', name:'qty'},
				{data: 'deliv_qty', name:'deliv_qty'},
				{data: 'closed_qty', name:'closed_qty'},
				{data: 'unit', name:'unit'},
				{data: 'price', name:'price'},
				{data: 'amount', name:'amount'}
			],order:[2,'desc'],fnCreatedRow:function(nRow,aData,iDataIndex){
				var trans_status = '';
				if(aData['trans_status']==1){
					trans_status='<span class="label label-warning">Pendding</span>';
				}else if(aData['trans_status']==2){
					trans_status='<span class="label label-info">Approving</span>';
				}else if(aData['trans_status']==3){
					trans_status='<span class="label label-success">Completed</span>';
				}else if(aData['trans_status']==4){
					trans_status='<span class="label label-danger">Rejected</span>';
				}
				$('td:eq(13)',nRow).html(formatDollar(aData['price'])).addClass("text-center");
				$('td:eq(14)',nRow).html(formatDollar(aData['amount'])).addClass("text-center");
				$('td:eq(9)',nRow).html(formatNumber(aData['qty'])).addClass("text-center");
				$('td:eq(10)',nRow).html(formatNumber(aData['deliv_qty'])).addClass("text-center");
				$('td:eq(11)',nRow).html(formatNumber(aData['closed_qty'])).addClass("text-center");
				$('td:eq(5)',nRow).html(formatDate(aData['delivery_date'])).addClass("text-center");
				$('td:eq(0)',nRow).html(formatDate(aData['trans_date'])).addClass("text-center");
			}
		});

		$('#my-table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var tableId = 'posts-' + row.data().po_id;
            if(row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            }else{
                row.child(format(row.data())).show();
                initTable(tableId,row.data());
                tr.addClass('shown');
            }
        });

        function initTable(tableId, data) {
            $('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                info:false,
                filter:false,
                paging:false,
                ajax: '<?php echo url("/report/purchase/order/generate_orders").$param;?>&version=datatables&po_id='+data.po_id,
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'item_code', name: 'item_code' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'qty', name: 'qty' },
                    { data: 'unit', name: 'unit' },
                    { data: 'price', name: 'price' },
                    { data: 'amount', name: 'amount' },
                    { data: 'disc_perc', name: 'disc_perc' },
                    { data: 'disc_usd', name: 'disc_usd' },
                    { data: 'total', name: 'total' },
                    { data: 'desc', name: 'desc'}
                ],order:[0,'asc'],fnCreatedRow:function(nRow,aData,iDataIndex){
                    $('td:eq(3)',nRow).html(formatNumber(aData['qty'])).addClass('text-center');
                    $('td:eq(5)',nRow).html(formatDollar(aData['price'])).addClass('text-center');
                    $('td:eq(6)',nRow).html(formatDollar(aData['amount'])).addClass('text-center');
                    $('td:eq(7)',nRow).html(formatNumber(aData['disc_perc'])+'%').addClass('text-center');
                    $('td:eq(8)',nRow).html(formatDollar(aData['disc_usd'])).addClass('text-center');
                    $('td:eq(9)',nRow).html(formatDollar(aData['total'])).addClass('text-center');
                    $('td:eq(0)',nRow).html(iDataIndex+1).addClass('text-center');
                }
            });
        }
	});
</script>
@endsection()
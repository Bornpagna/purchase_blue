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
    $start_date     = '';
    $end_date       = '';
    $warehouseArray = [];
    $streetArray    = [];
    $houseArray     = [];
    $eng_usage      = '';
    $sub_usage      = '';
    $street_id      = '';
    $house_id       = '';
    $warehouse_id   = '';
    $param          = '?v=1';
    $start          = 0;
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

    if (Request::input('eng_usage')) {
        $eng_usage = Request::input('eng_usage');
        $param.='&eng_usage='.$eng_usage;
    }

    if (Request::input('sub_usage')) {
        $sub_usage = Request::input('sub_usage');
        $param.='&sub_usage='.$sub_usage;
    }

    if (Request::input('street_id')) {
        $street_id = Request::input('street_id');
        $param.='&street_id='.$street_id;
    }

    if (Request::input('house_id')) {
        $house_id = '';
        foreach (Request::input('house_id') as $jkey => $jv) {
            $house_id.=','.$_POST['house_id'][$jkey];
            $houseArray[$jkey] = $_POST['house_id'][$jkey];
        }
        $param.='&house_id='.$house_id;
    }else{
        $param.='&house_id='.$house_id;
    }

    if (Request::input('warehouse_id')) {
        $warehouse_id = "";
        foreach (Request::input('warehouse_id') as $jkey => $jv) {
            $warehouse_id.=",".$_POST['warehouse_id'][$jkey];
            $warehouseArray[$jkey] = $_POST['warehouse_id'][$jkey];
        }
        $param.='&warehouse_id='.$warehouse_id;
    }else{
        $param.='&warehouse_id='.$warehouse_id;
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
	                                    <label for="eng_usage" class="control-label bold">{{trans('lang.engineer')}}</label>
	                                    <select class="form-control" id="eng_usage" name="eng_usage">
                                         @if(getSetting()->usage_constructor==1)
                                            {{getConstructor([1])}}
                                         @endif
                                        </select>
	                                </div>
	                                <div class="col-md-4">
	                                    <label for="sub_usage" class="control-label bold">{{trans('rep.subconstructor')}}</label>
	                                    <select class="form-control" id="sub_usage" name="sub_usage">
	                                        {{getConstructor([2])}}
	                                    </select>
	                                </div>
	                            </div>
	                        </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="street_id" class="control-label bold">{{trans('lang.street')}}</label>
                                        <select class="form-control" id="street_id" name="street_id">
                                            <option></option>
                                            {{getSystemData('ST')}}
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="house_id" class="control-label bold">{{trans('lang.house')}}</label>
                                        <select class="form-control" id="house_id" name="house_id[]" multiple></select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="warehouse_id" class="control-label bold">{{trans('lang.warehouse')}}</label>
                                        <select class="form-control" id="warehouse_id" name="warehouse_id[]" multiple>
                                            {{getWarehouse()}}
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
					<thead>
						<tr>
							<th width="8%" class="all">{{ trans('lang.trans_date') }}</th>
							<th width="15%" class="all">{{ trans('lang.reference_no') }}</th>
							<th width="7%" class="all">{{ trans('lang.reference') }}</th>
							<th width="10%" class="all">{{ trans('lang.engineer') }}</th>
							<th width="10%" class="all">{{ trans('lang.constructor') }}</th>
							<th width="10%" class="all">{{ trans('lang.warehouse') }}</th>
							<th width="5%" class="all">{{ trans('lang.street') }}</th>
							<th width="5%" class="all">{{ trans('lang.house') }}</th>
							<th width="20%" class="all">{{ trans('lang.items') }}</th>
							<th width="5%" class="all">{{ trans('lang.qty') }}</th>
							<th width="5%" class="all">{{ trans('lang.units') }}</th>
						</tr>
					</thead>
					<tbody></tbody>
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
						<th width="8%" class="all">{{ trans('lang.trans_date') }}</th>
                        <th width="15%" class="all">{{ trans('lang.reference_no') }}</th>
                        <th width="7%" class="all">{{ trans('lang.reference') }}</th>
                        <th width="10%" class="all">{{ trans('lang.engineer') }}</th>
                        <th width="10%" class="all">{{ trans('lang.constructor') }}</th>
                        <th width="10%" class="all">{{ trans('lang.warehouse') }}</th>
                        <th width="5%" class="all">{{ trans('lang.street') }}</th>
                        <th width="5%" class="all">{{ trans('lang.house') }}</th>
                        <th width="5%" class="all">{{ trans('lang.item_code') }}</th>
                        <th width="15%" class="all">{{ trans('lang.item_name') }}</th>
                        <th width="5%" class="all">{{ trans('lang.qty') }}</th>
                        <th width="5%" class="all">{{ trans('lang.units') }}</th>
                    </tr>
                </thead>
                <tbody class="invoice-table-tbody"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection()

@section('javascript')
<script type="text/javascript">

    function initHouse(argument,street_id) {
        $(argument).empty();
        $.ajax({
            url:'{{url("/report/getHouse")}}',
            type:'get',
            data:{'street_id':street_id},
            success:function(response){
                if (response.length > 0) {
                    $.each(response,function(key,val){
                        $(argument).append($('<option></option>').val(val.id).text(val.house_no+' - '+val.house_desc));
                    });
                }
            },complete:function(){
                $('#house_id').select2('val',<?php echo json_encode($houseArray);?>);
            }
        });
    }

    $('#street_id').on('change',function(){
        initHouse('#house_id',this.value);
    });

	function generatePrint(response) {
        if (response) {
            var div = $('.invoice-table-tbody');
            div.empty();
			var divString   = '';
            $.each(response,function(key,val){
                divString += '<tr>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+formatDate(val.trans_date)+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.ref_no+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.reference+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.engineer_name+' - '+val.engineer_code+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.subconstructor_name+' - '+val.subconstructor_code+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.warehouse+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.street+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.house_no+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.item_code+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.item_name+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+parseFloat(val.qty)+'</td>';
                    divString += '<td style="text-align:center !important;" class="black-all">'+val.unit+'</td>';
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
				url:'<?php echo url("/report/generate_usage").$param;?>&version='+version,
				type:'GET',
				success:function(response){
					generatePrint(response);
				}
			});
		}else if(version=='excel'){
			window.location.href="<?php echo url("/report/generate_usage").$param;?>&version="+version;
		}
	}

	$(document).ready(function(){

		// $.fn.select2.defaults.set('theme','classic');
        $('#eng_usage,#sub_usage,#street_id,#warehouse_id,#house_id').select2({width:'100%',placeholder:'{{trans("lang.please_choose")}}',allowClear:true});

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

        $('#sub_usage').select2('val','{{$sub_usage}}');
        $('#warehouse_id').select2('val',<?php echo json_encode($warehouseArray);?>);
        $('#eng_usage').select2('val','{{$eng_usage}}');
        $('#street_id').select2('val','{{$street_id}}');
        $('#house_id').select2('val',<?php echo json_encode($houseArray);?>);

		var table = $('#my-table').DataTable({
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{trans("lang.all")}}"]],
			processing: true,
			serverSide: true,
			ajax: '<?php echo url("/report/generate_usage").$param;?>&version=datatables&group_by=1',
			columns: [
				{data: 'trans_date', name:'trans_date'},
				{data: 'ref_no', name:'ref_no'},
				{data: 'reference', name:'reference'},
                {data: 'engineer_name', name:'engineer_name'},
                {data: 'subconstructor_name', name:'subconstructor_name'},
				{data: 'warehouse', name:'warehouse'},
				{data: 'street', name:'street'},
				{data: 'house_no', name:'house_no'},
				{data: 'item_code', name:'item_code'},
				{data: 'qty', name:'qty'},
				{data: 'unit', name:'unit'}
			],'search':{'regex':true},order:[0,'desc'],fnCreatedRow:function(nRow,aData,iDataIndex){
          $('td:eq(0)',nRow).html(formatDate(aData['trans_date'])).addClass("text-center");
          $('td:eq(3)',nRow).html(aData['engineer_code']+" - "+aData['engineer_name']);
          $('td:eq(4)',nRow).html(aData['subconstructor_code']+" - "+aData['subconstructor_name']);
          $('td:eq(8)',nRow).html(aData['item_name']+" - "+aData['item_code']);
			}
		});
	});
</script>
@endsection()

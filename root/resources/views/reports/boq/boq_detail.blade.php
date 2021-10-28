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
    $houseArray     = [];
    $house_type     = '';
    $house_id       = '';
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

    if (Request::input('house_type')) {
        $house_type = Request::input('house_type');
        $param.='&house_type='.$house_type;
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
	                                <div class="col-md-6">
                                        <label for="house_type" class="control-label bold">{{trans('lang.house_type')}}</label>
                                        <select class="form-control" id="house_type" name="house_type">
                                            <option></option>
                                            {{getSystemData('HT')}}
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="house_id" class="control-label bold">{{trans('lang.house')}}</label>
                                        <select class="form-control" id="house_id" name="house_id[]" multiple></select>
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
				<table class="table table-striped table-bordered table-hover dt-responsive" id="my-table">
					<thead>
						<tr>
                            <th width="10%" class="all">{{ trans('lang.house_type') }}</th>
                            <th width="10%" class="all">{{ trans('lang.house_no') }}</th>
							<th width="10%" class="all">{{ trans('lang.item_type') }}</th>
                            <th width="10%" class="all">{{ trans('lang.item_code') }}</th>
                            <th width="10%" class="all">{{ trans('lang.item_name') }}</th>
                            <th width="10%" class="all">{{ trans('rep.boq_qty') }}</th>
                            <th width="10%" class="all">{{ trans('rep.add_qty') }}</th>
                            <th width="10%" class="all">{{ trans('lang.units') }}</th>
                            <th width="10%" class="all">{{ trans('lang.price') }}</th>
                            <th width="10%" class="all">{{ trans('lang.total') }}</th>
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
    <div style="width: 100%;">
        <div style="position: absolute;
            font-size: 14px;
            top: 129px;">
            <span>Project &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <label style="font-weight: bold;" class="lblProject"></label></span><br/>
            <span>House Type : <label style="font-weight: bold;" class="lblHouseType"></label></span><br/>
            <span>House No &nbsp;&nbsp;&nbsp;: <label style="font-weight: bold;" class="lblHouseNo"></label></span>
        </div>
    </div>
    <div class="invoice-items">
        <div class="div-table">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="5%" class="all">No.</th>
                        <th width="15%" class="all">Item Code</th>
						<th width="20%" class="all">Description</th>
						<th width="10%" class="all">Unit</th>
						<th width="15%" class="all">Exactly BOQ</th>
                        <th width="15%" class="all">Additional BOQ</th>
                        <th width="10%" class="all">Unit Price</th>
						<th width="10%" class="all">Total</th>
                    </tr>
                </thead>
                <tbody class="invoice-table-tbody"></tbody>
            </table>
        </div>
    </div>
    @include('reports.footer')
</div>
<!-- Modal Varian -->
@endsection()

@section('javascript')
<script type="text/javascript">

    function initHouse(argument,street_id) {
        $(argument).empty();
        $.ajax({
            url:'{{url("/report/getHouse")}}',
            type:'get',
            data:{'house_type':street_id},
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

    $('#house_type').on('change',function(){
        initHouse('#house_id',this.value);
    });

    function isSubmit() {
        var isValid    = true;
        var house_type = $("#house_type").val(); 
        if (house_type==null || house_type=='' || house_type==undefined) {
            isValid = false;
            $('#house_type').children().find('.select2-selection .select2-selection--single').css('border','1px solid #f00');
        }else{
            $('#house_type').children().find('.select2-selection .select2-selection--single').css('border','1px solid #ccc');
        }

        return isValid;
    }

	function generatePrint(response) {
        if (response) {
            var div = $('.invoice-table-tbody');
            div.empty();
            var divString   = '';
            var project     = [];
            var request_obj = [];
            var big_total   = [];
            var house_no    = [];

            $.each(response,function(key,val){
                var total = (parseFloat(val.qty_std) + parseFloat(val.qty_add)) * parseFloat(val.item_price);
                if (big_total[val.item_type_id] > 0) {
                    big_total[val.item_type_id] = parseFloat(big_total[val.item_type_id]) + parseFloat(total);
                }else{
                    big_total[val.item_type_id] = parseFloat(total);
                }
                house_no[val.house_id] = val.house_no;
            	project[val.item_type_id] = {'item_type_id':val.item_type_id,'item_type':val.item_type,'item_type_desc':val.item_type_desc,'big_total':parseFloat(big_total[val.item_type_id])};
            });

            $.each(response,function(key,val){
            	request_obj[val.id] = val;
            });


            var main_id = 1;
            var total_item_type = 0;
            $.each(project,function(k,v){
            	if (v) {
            		divString += '<tr>';
                    divString += '<td style="text-align:center !important;font-weight:bold;" class="black-all">A'+(main_id++)+'</td>';
	                divString += '<td style="text-align:left !important;font-weight:bold;" class="black-all">'+v.item_type+'</td>';
	                divString += '<td colspan="5" style="text-align:left !important;font-weight:bold;" class="black-all">'+v.item_type_desc+'</td>';
	                divString += '<td style="text-align:center !important;font-weight:bold;" class="black-all total-'+v.item_type_id+'">'+formatDollar(v.big_total)+'</td>';
		            divString += '</tr>';
            		$.each(response.filter(a=>a.item_type_id==v.item_type_id),function(key,val){
                        var total = (parseFloat(val.qty_std) + parseFloat(val.qty_add)) * parseFloat(val.item_price);
                        total_item_type += parseFloat(total);
		                divString += '<tr style="background: #fff !important;">';
		                divString += '<td style="text-align:center !important;" class="black-all">'+(key+1)+'</td>';
		                divString += '<td style="text-align:left !important;" class="black-all">'+val.item_code+'</td>';
		                divString += '<td style="text-align:left !important;" class="black-all">'+val.item_name+'</td>';
		                divString += '<td style="text-align:center !important;" class="black-all">'+val.unit+'</td>';
		                divString += '<td style="text-align:center !important;" class="black-all">'+val.qty_std+'</td>';
                        divString += '<td style="text-align:center !important;" class="black-all">'+val.qty_add+'</td>';
                        divString += '<td style="text-align:center !important;" class="black-all">'+formatDollar(val.item_price)+'</td>';
                        divString += '<td style="text-align:center !important;" class="black-all">'+formatDollar(total)+'</td>';
		                divString += '</tr>';
                        $('.lblProject').html(val.project);
                        $('.lblHouseType').html(val.house_type);
		            });
            	}
            });
            divString += '<tr>';
            divString += '<td colspan="7" style="text-align:right !important;font-weight:bold;" class="black-all">Sub-Total :</td>';
            divString += '<td style="text-align:center !important;font-weight:bold;" class="black-all">'+formatDollar(total_item_type)+'</td>';
            divString += '</tr>';

            var house_str = '';
            $.each(house_no,function(key,val){
                if (val) {
                    if(house_str==''){
                        house_str = house_no[key];
                    }else{
                        house_str +=','+house_no[key];
                    }
                }
            });

            $('.lblHouseNo').html(house_str);

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
				url:'<?php echo url("/report/generate_sub_boq").$param;?>&version='+version,
				type:'GET',
				success:function(response){
					generatePrint(response);
				}
			});
		}else if(version=='excel'){
			window.location.href="<?php echo url("/report/generate_sub_boq").$param;?>&version="+version;
		}
	}

	function format (d) {
        var str = '';
        str += '<table class="table table-striped details-table table-responsive"  id="posts-'+d.id+'">';
            str += '<thead>';
                str += '<tr>';
                    str += '<th class="text-center" style="width: 3%;">{{trans("lang.line_no")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.warehouse")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.street")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.house")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.item_code")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.item_name")}}</th>';
                    str += '<th class="text-center" style="width: 10%;">{{trans("lang.stock_qty")}}</th>';
                    str += '<th class="text-center" style="width: 7%;">{{trans("lang.qty")}}</th>';
                    str += '<th class="text-center" style="width: 5%;">{{trans("lang.units")}}</th>';
                    str += '<th class="text-center" style="width: 5%;">{{trans("lang.note")}}</th>';
                str += '</tr>';
            str += '</thead>';
        str +='</table>';
        return str;
    }

    $("#house_type,#house_id").on('change',function(){
        if (isSubmit()) {}else{return false;}
    });

    $("#btnSearch").on('click',function(){
        if (isSubmit()) {}else{return false;}
    });

	$(document).ready(function(){

		// $.fn.select2.defaults.set('theme','classic');
        $('#house_type,#house_id').select2({width:'100%',placeholder:'{{trans("lang.please_choose")}}',allowClear:true});

        $('#house_type').select2('val','{{$house_type}}');
        $('#house_id').select2('val',<?php echo json_encode($houseArray);?>);

		var table = $('#my-table').DataTable({
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{trans("lang.all")}}"]],
			processing: true,
			serverSide: true,
			ajax: '<?php echo url("/report/generate_sub_boq").$param;?>&version=datatables',
			columns: [
                {data: 'house_type', name:'house_type'},
                {data: 'house_no', name:'house_no'},
                {data: 'item_type', name:'item_type'},
				{data: 'item_code', name:'item_code'},
				{data: 'item_name', name:'item_name'},
				{data: 'qty_std', name:'qty_std'},
                {data: 'qty_add', name:'qty_add'},
                {data: 'unit', name:'unit'},
                {data: 'cost', name:'cost'},
				{data: 'item_id', name:'item_id'}
			],order:[2,'desc'],fnCreatedRow:function(nRow,aData,iDataIndex){
                var total = (parseFloat(aData["qty_std"]) + parseFloat(aData["qty_add"])) * parseFloat(aData["cost"]);
                $('td:eq(8)',nRow).html(formatDollar(aData["cost"])).addClass("text-center");
                $('td:eq(9)',nRow).html(formatDollar(total)).addClass("text-center");
			}
		});

		$('#my-table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var tableId = 'posts-' + row.data().id;
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
                ajax: '<?php echo url("/report/generate_sub_boq").$param;?>&version=datatables',
                columns: [
                    { data: 'id', name: 'id'},
                    { data: 'warehouse', name: 'warehouse' },
                    { data: 'street', name: 'street' },
                    { data: 'house_no', name: 'house_no' },
                    { data: 'item_code', name: 'item_code' },
                    { data: 'item_name', name: 'item_name' },
                    { data: 'usage_qty', name: 'usage_qty' },
                    { data: 'qty', name: 'qty' },
                    { data: 'unit', name: 'unit' },
                    { data: 'note', name: 'note' }
                ],order:[0,'asc'],fnCreatedRow:function(nRow,aData,iDataIndex){
                    $('td:eq(6)',nRow).html(parseFloat(aData['usage_qty'])).addClass('text-center');
                    $('td:eq(7)',nRow).html(parseFloat(aData['qty'])).addClass('text-center');
                    $('td:eq(0)',nRow).html(iDataIndex+1).addClass('text-center');
                }
            });
        }
	});
</script>
@endsection()
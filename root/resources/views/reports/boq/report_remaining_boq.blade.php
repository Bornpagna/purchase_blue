@extends('layouts.app')

@section('content')
<style>
	.btnAdd{
		cursor: pointer;
	}
	.form-horizontal .form-group {
		margin-left: 0px !important;
		margin-right: 0px !important;
	}
</style>
<!-- Param request combine string -->
<?php
    $start_date = '';
    $end_date = date('Y-m-d');
    $zone = '';
    $block = '';
    $street = '';
    $house_type = '';
    $house = '';
    $product = '';
    $product_type = '';
    $param = '?v=1';
    $start = 0;
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

    if (Request::input('zone')) {
        $zone = Request::input('zone');
        $param.='&zone='.$zone;
    }

    if (Request::input('block')) {
        $block = Request::input('block');
        $param.='&block='.$block;
    }

    if (Request::input('house')) {
        $house = Request::input('house');
        $param.='&house='.$house;
    }

    if (Request::input('house_type')) {
        $house_type = Request::input('house_type');
        $param.='&house_type='.$house_type;
    }

    if (Request::input('product')) {
        $product = Request::input('product');
        $param.='&product='.$product;
    }

    if (Request::input('product_type')) {
        $product_type = Request::input('product_type');
        $param.='&product_type='.$product_type;
    }

    if (Request::input('street')) {
        $street = Request::input('street');
        $param.='&street='.$street;
    }
?>
<!-- Param request combine string -->
<!--------------------------------------------------->
<!-- Content -->
<div class="row">
	<div class="col-ld-12">
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
			<div class="portlet-body form">
				<?php if(Session::has('success')):?>
					<div class="alert alert-success display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.success')}}!</strong> {{Session::get('success')}} 
					</div>
				<?php elseif(Session::has('error')):?>
					<div class="alert alert-danger display-show">
						<button class="close" data-close="alert"></button><strong>{{trans('lang.error')}}!</strong> {{Session::get('error')}} 
					</div>
				<?php endif; ?>
				<div class="form-group">
					<div class="col-md-12 ">
						<span class="show-message-error center font-red bold"></span>
					</div>
				</div>
				<form method="POST" class="form-horizontal">
					{{csrf_field()}}
					<!-- To Date, Zone, Block, Street, House Type, House -->
                    <div class="row">
                        <!-- From Date -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label bold">{{trans('lang.date')}}</label>
                                <input type="text" class="form-control date-picker" value="{{$end_date}}" name="end_date" id="end_date">
                            </div>
                        </div>
                        @if(getSetting()->allow_zone == 1)
                        <!-- Zone -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zone" class="control-label" style="text-align: left;"><strong>{{trans('lang.zone')}}</strong></label>
                                <select class="form-control select2 zone" name="zone" id="zone">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        @endif
                        @if(getSetting()->allow_block == 1)
                        <!-- Block -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="block" class="control-label" style="text-align: left;"><strong>{{trans('lang.block')}}</strong></label>
                                <select class="form-control select2 block" name="block" id="block">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <!-- Street -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="street" class="control-label" style="text-align: left;"><strong>{{trans('lang.street')}}</strong></label>
                                <select class="form-control select2 street" name="street" id="street">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <!-- House Type -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="house_type" class="control-label" style="text-align: left;"><strong>{{trans('lang.house_type')}}</strong></label>
                                <select class="form-control select2 house_type" name="house_type" id="house_type">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <!-- House -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="house" class="control-label" style="text-align: left;"><strong>{{trans('lang.house')}}</strong></label>
                                <select class="form-control select2 house" name="house" id="house">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <!-- Product Type -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_type" class="control-label" style="text-align: left;"><strong>{{trans('lang.category')}}</strong></label>
                                <select class="form-control select2 product_type" name="product_type" id="product_type">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <!-- Product -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product" class="control-label" style="text-align: left;"><strong>{{trans('lang.product')}}</strong></label>
                                <select class="form-control select2 product" name="product" id="product">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
					</div>
                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group text-right">
                                <button type="submit" id="submit" name="submit" value="submit"  class="btn blue bold">{{trans('lang.submit')}}</button>
                            </div>
                        </div>
                    </div>
                    <!-- Result Table -->
                    <div class="row">
                        <!-- List of Product -->
                        <div class="col-md-12">
                            <div>
                                <table class="table table-striped table-bordered table-hover dt-responsive" id="data-table">
                                    <thead>
                                        <?php $colspan = 12; ?>
                                        <tr style="font-size:12px;">
                                            <th width="10%" class="text-center all">{{ trans('lang.trans_date') }}</th>
                                            @if(getSetting()->allow_zone == 1)
                                            <?php $colspan++;?>
                                            <th width="10%" class="text-center mobile">{{ trans('lang.zone') }}</th>
                                            @endif
                                            @if(getSetting()->allow_block == 1)
                                            <th width="10%" class="text-center mobile">{{ trans('lang.block') }}</th>
                                            <?php $colspan++;?>
                                            @endif
                                            <th width="10%" class="text-center mobile">{{ trans('lang.building') }}</th>
                                            <th width="10%" class="text-center mobile">{{ trans('lang.street') }}</th>
                                            <th width="10%" class="text-center mobile">{{ trans('lang.house_type') }}</th>
                                            <th width="10%" class="text-center all">{{ trans('lang.house_no') }}</th>
                                            <th width="10%" class="text-center mobile">{{ trans('lang.item_type') }}</th>
                                            <th width="30%" class="text-center all">{{ trans('lang.item_code') }}</th>
                                            <th width="30%" class="text-center all">{{ trans('lang.item_name') }}</th>
                                            <th width="10%" class="text-center all">{{ trans('lang.boq_std') }}</th>
                                            <th width="10%" class="text-center mobile">{{ trans('lang.boq_add') }}</th>
                                            <th width="5%" class="text-center all">{{ trans('lang.boq_unit') }}</th>
                                            <th width="10%" class="text-center all">{{ trans('lang.usage_qty') }}</th>
                                            <th width="5%" class="text-center all">{{ trans('lang.usage_unit') }}</th>
                                            <th width="5%" class="text-center mobile">{{ trans('lang.cost') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="text-center" colspan="{{$colspan}}">{{ trans('lang.no_data_available_in_table') }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- End Content -->
<!--------------------------------------------------->
<!-- Modal Block -->

<!-- End Modal Block -->
<!--------------------------------------------------->
@endsection()
<!--------------------------------------------------->
<!-- Script  -->
@section('javascript')
<script type="text/javascript">

    getZones = function(onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getZones')}}",
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getZones_onSuccess = function(response){
        $(".zone").empty();
        $.each(response,function(i,val){
            $(".zone").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getZones_onError = function(error){
        $(".zone").empty();
		$(".zone").select2('val', null);
        $(".zone").append($('<option></option>').val('').text(''));
    }
    getZones_onComplete = function(response){
        $('.zone').select2('val','{{$zone}}');
    }

    getBlocks = function(onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getBlocks')}}",
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getBlocks_onSuccess = function(response){
        $(".block").empty();
        $.each(response,function(i,val){
            $(".block").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getBlocks_onError = function(error){
        $(".block").empty();
		$(".block").select2('val', null);
        $(".block").append($('<option></option>').val('').text(''));
    }
    getBlocks_onComplete = function(response){
        $('.block').select2('val','{{$block}}');
    }

    getStreets = function(onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getStreets')}}",
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getStreets_onSuccess = function(response){
        $(".street").empty();
        $.each(response,function(i,val){
            $(".street").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getStreets_onError = function(error){
        $(".street").empty();
		$(".street").select2('val', null);
        $(".street").append($('<option></option>').val('').text(''));
    }
    getStreets_onComplete = function(response){
        $('.street').select2('val','{{$street}}');
    }

    getHouseTypes = function(onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getHouseTypes')}}",
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getHouseTypes_onSuccess = function(response){
        $(".house_type").empty();
        $.each(response,function(i,val){
            $(".house_type").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getHouseTypes_onError = function(error){
        $(".house_type").empty();
		$(".house_type").select2('val', null);
        $(".house_type").append($('<option></option>').val('').text(''));
    }
    getHouseTypes_onComplete = function(response){
        $('.house_type').select2('val','{{$house_type}}');
    }

    getHouseTypesByZoneID = function(zoneID,onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByZoneID')}}/" + zoneID,
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getHouseTypesByBlockID = function(blockID,onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByBlockID')}}/" + blockID,
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getHouseTypesByStreetID = function(streetID,onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByStreetID')}}/" + streetID,
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }
    getHousesByHouseType = function(houseTypeID,onSuccess,onError,onComplete){
        $.ajax({
            url:"{{url('repository/getHousesByHouseType')}}/" + houseTypeID,
            type:'GET',
            success: onSuccess,
            error: onError,
            complete: onComplete
        });
    }

    houseTypes_onSuccess = function(response){
        $(".house_type").empty();
        $.each(response,function(i,val){
            $(".house_type").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    houseTypes_onError = function(error){
        $(".house_type").empty();
		$(".house_type").select2('val', null);
        $(".house_type").append($('<option></option>').val('').text(''));
    }
    houseTypes_onComplete = function(response){
        $('.house_type').select2('val','{{$house_type}}');
    }

    house_onSuccess = function(response){
        $(".house").empty();
        $.each(response,function(i,val){
            $(".house").append($('<option></option>').val(val.id).text(val.house_no));
        });
    }
    house_onError = function(error){
        $(".house").empty();
		$(".house").select2('val', null);
        $(".house").append($('<option></option>').val('').text(''));
    }
    house_onComplete = function(response){
        $('.house').select2('val','{{$house}}');
    }

    // Category Response
    getProductTypes = function(success,error,complete){
        $.ajax({
            url:"{{url('repository/getProductTypes')}}",
            type:'GET',
            success: success,
            error: error,
            complete: complete
        });
    }
    categorySuccess = function(response){
        $.each(response,function(i,val){
            $(".product_type").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    categoryError = function(error){}
    categoryComplete = function(response){
        $('.product_type').select2('val','{{$product_type}}');
    }
    // Product Response
    getProductsByProductType = function(productType,success,error,complete){
        $.ajax({
            url:"{{url('repository/getProductsByProductType')}}" + '/' + productType,
            type:'GET',
            success: success,
            error: error,
            complete: complete
        });
    }
    productSuccess = function(response){
        $(".product").empty();
		$(".product").select2('val', null);
        $(".product").append($('<option></option>').val('').text(''));
        $.each(response,function(i,val){
            $(".product").append($('<option></option>').val(val.id).text(val.name + '(' + val.code + ')'));
        });
    }
    productError = function(error){
        $(".product").empty();
		$(".product").select2('val', null);
        $(".product").append($('<option></option>').val('').text(''));
    }
    productComplete = function(response){
        $('.product').select2('val','{{$product}}');
    }

    initDataTable = function(){
        // Initialized datatable
        var columns = [
            {data: 'trans_date', name:'trans_date'},
        ];
        if("{{getSetting()->allow_zone == 1}}"){
            columns.push({data: 'zone', name:'zone'});
        }
        if("{{getSetting()->allow_block == 1}}"){
            columns.push({data: 'block', name:'block'});
        }
        columns.push({data: 'building', name:'buildng'});
        columns.push({data: 'street', name:'street'});
        columns.push({data: 'house_type', name:'house_type'});
        columns.push({data: 'house_no', name:'house_no'});
        columns.push({data: 'item_type', name:'item_type'});
        columns.push({data: 'item_code', name:'item_code'});
        columns.push({data: 'item_name', name:'item_name'});
        columns.push({data: 'qty_std', name:'qty_std'});
        columns.push({data: 'qty_add', name:'qty_add'});
        columns.push({data: 'unit', name:'unit'});
        columns.push({data: 'usage_qty', name:'usage_qty'});
        columns.push({data: 'usage_unit', name:'usage_unit'});
        columns.push({data: 'usage_cost', name:'usage_cost'});
        var table = $('#data-table').DataTable({
			"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{trans("lang.all")}}"]],
			processing: true,
			serverSide: true,
			ajax: '<?php echo url("report/boq/generateRemainingBOQ").$param;?>&version=datatables',
			columns: columns,
            'search':{'regex':true},order:[0,'desc'],
            fnCreatedRow:function(nRow,aData,iDataIndex){
                $('td:eq(0)',nRow).html(formatDate(aData['trans_date'])).addClass("text-center");
			}
		});
    }

    onPrint = function(args){
        var version = $(args).attr('version');
		if (version=='print') {
            var mywindow = window.open("<?php echo url("report/boq/printRemainingBOQ").$param;?>", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=800,height=800");
            setInterval(function(){ 
                mywindow.print();
                mywindow.close();
            }, 10000);
        }else if(version=='excel'){
			window.location.href="<?php echo url("report/boq/generateRemainingBOQ").$param;?>&version="+version;
		}
    }
   
    $(document).ready(function(){
        $(".select2").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
        
        $('#end_date').val(formatDate('{{date("Y-m-d")}}'));
		$("#end_date").datepicker({
			format: "{{getSetting()->format_date}}",
            autoclose: true,
            pickerPosition: "bottom-right"
		});
        
        initDataTable();
        if("{{getSetting()->allow_zone == 1}}"){
            getZones(getZones_onSuccess,getZones_onError,getZones_onComplete);
            $('.zone').on('change',function(){
                var zoneID = $(this).val();
                if(zoneID){
                    getHouseTypesByZoneID(zoneID,houseTypes_onSuccess,houseTypes_onError,houseTypes_onComplete);
                }
            });
        }
        if("{{getSetting()->allow_block == 1}}"){
            getBlocks(getBlocks_onSuccess,getBlocks_onError,getBlocks_onComplete);
            $('.block').on('change',function(){
                var blockID = $(this).val();
                if(blockID){
                    getHouseTypesByBlockID(blockID,houseTypes_onSuccess,houseTypes_onError,houseTypes_onComplete);
                }
            });
        }
        getStreets(getStreets_onSuccess,getStreets_onError,getStreets_onComplete);
        getHouseTypes(getHouseTypes_onSuccess,getHouseTypes_onError,getHouseTypes_onComplete);
        getProductTypes(categorySuccess,categoryError,categoryComplete);

        $('.street').on('change',function(){
            var streetID = $(this).val();
            if(streetID){
                getHouseTypesByStreetID(streetID,houseTypes_onSuccess,houseTypes_onError,houseTypes_onComplete);
            }
        });

        $('.house_type').on('change',function(){
            var houseType = $(this).val();
            if(houseType){
                getHousesByHouseType(houseType,house_onSuccess,house_onError,house_onComplete);
            }
        });

        $('.product_type').on('change',function(){
            var productType = $(this).val();
            if(productType){
                getProductsByProductType(productType,productSuccess,productError,productComplete);
            }
        });

    });
</script>
@endsection()
<!-- End Script  -->
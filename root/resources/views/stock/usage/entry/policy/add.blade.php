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
    .form-message{
        background-color: antiquewhite !important;
        color: red !important;
    }
    .out-stock{background-color: #03a9f4 !important;}
    .out-boq{background-color: #ffeb3b !important;}
    .not-set-boq{background-color: #9c74e4 !important;}
    .invalid{background-color: #ff7065 !important;}
    .loader {
        margin: 0 auto;
        border: 8px solid #f3f3f3;
        border-radius:50% !important;
        border-top: 8px solid #3498db;
        width: 60px;
        height: 60px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

        /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    select[readonly].select2+.select2-container {
        pointer-events: none;
        touch-action: none;
    }
    #table-income tbody tr td{
        vertical-align: middle;
    }
</style>
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
					<a rounte="{{$rounteBack}}" title="{{trans('lang.back')}}" class="btn btn-circle btn-icon-only btn-default" id="btnBack">
						<i class="fa fa-reply"></i>
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
				
				<form action="{{$rounteSave}}" method="POST" id="form-stock-entry" class="form-horizontal" enctype="multipart/form-data">
					{{csrf_field()}}
					<input type="hidden" name="btnSubmit" id="btnSubmit"/>
					<div class="portlet-body" >
                        <div class="row">
                            <!-- REFERENCE NO -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reference_no" class="control-label"><strong>{{ trans('lang.reference_no') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <input class="form-control reference_no" length="20" type="text" id="reference_no" name="reference_no" placeholder="{{ trans('lang.enter_text') }}">
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <!-- TRANS DATE -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trans_date" class="control-label"><strong>{{ trans('lang.trans_date') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <input class="form-control trans_date" length="10" type="text" id="trans_date" name="trans_date" placeholder="{{ trans('lang.enter_text') }}">
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <!-- REFERENCE -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="reference" class="control-label"><strong>{{ trans('lang.reference') }}</strong></label>
                                    <input class="form-control reference"​​ length="100" type="text" id="reference" name="reference" placeholder="{{ trans('lang.enter_text') }}">
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <!-- Engineer -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="engineer" class="control-label" style="text-align: left;"><strong>{{ trans('lang.engineer') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    @if(hasRole('constructor_add'))
                                    <div class="input-group">
                                        <select class="form-control select2 engineer" name="engineer" id="engineer">
                                            
                                        </select>
                                        <span class="input-group-addon btn blue" id="btnAddEngineer">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                    @else
                                    <select class="form-control select2 engineer" name="engineer" id="engineer">
                                    </select>
                                    @endif
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <!-- Subcontractor -->
                            @if(getSetting()->usage_constructor==1)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sub_const" class="control-label" style="text-align: left;"><strong>{{ trans('lang.sub_const') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    @if(hasRole('constructor_add'))
                                    <div class="input-group">
                                        <select class="form-control select2 sub_const" name="sub_const" id="sub_const">
                                        </select>
                                        <span class="input-group-addon btn blue" id="btnAddSubConstructor">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                    @else
                                    <select class="form-control select2 sub_const" name="sub_const" id="sub_const">
                                    </select>
                                    @endif
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            @endif
                            <!-- Warehouse -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="warehouse_id" class="control-label" style="text-align: left;"><strong>{{ trans('lang.warehouse') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    @if(hasRole('warehouse_add'))
                                    <div class="input-group">
                                        <select class="form-control select2 warehouse_id" id="warehouse_id" name="warehouse_id">
                                           
                                        </select>
                                        <span class="input-group-addon btn blue" id="btnAddWarehouse">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                    @else
                                    <select class="form-control select2 warehouse_id" id="warehouse_id" name="warehouse_id">
                                    </select>
                                    @endif
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                             <!-- Note -->
                             <div class="col-md-12">
                                <div class="form-group">
                                    <label for="trans_desc" class="control-label"><strong>{{ trans('lang.note') }}</strong></label>
                                    <textarea class="form-control trans_desc" id="trans_desc" name="desc" rows="7" length="100" placeholder="{{ trans('lang.enter_text') }}"> </textarea>
                                </div>
                            </div>
                            <!-- Zone -->
                            @if(getSetting()->allow_zone == 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="zone_id" class="control-label" style="text-align: left;"><strong>{{ trans('lang.zone') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <select class="form-control select2 zone_id" id="zone_id" name="zone_id">
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            @endif
                            <!-- Block -->
                            @if(getSetting()->allow_block == 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="block_id" class="control-label" style="text-align: left;"><strong>{{ trans('lang.block') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <select class="form-control select2 block_id" id="block_id" name="block_id">
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            @endif

                            <!-- Building -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="building_id" class="control-label"><strong>{{ trans('lang.building') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <select class="form-control select2 building_id" id="building_id" name="building_id">
                                        
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="working_type_id" class="control-label"><strong>{{ trans('lang.working_type') }}</strong>
                                        <span class="required"> * </span>
                                    </label>
                                    <select class="form-control select2 working_type_id" id="working_type_id" name="working_type_id[]" multiple>
                                        {{getSystemData("WK")}}
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div>
                            <!-- Street -->
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="street_id" class="control-label"><strong>{{ trans('lang.street') }}</strong></label>
                                    <select class="form-control select2 street_id" name="street_id">
                                        
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div> --}}
                            <!-- House -->
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="house_id" class="control-label"><strong>{{ trans('lang.house') }}</strong></label>
                                    <select class="form-control select2 house_id" name="house_id">
                                        <option value=""></option>
                                    </select>
                                    <span class="help-block font-red bold"></span>
                                </div>
                            </div> --}}

                            <!-- Load Button -->
                            <div class="col-md-12">
                                {{-- <div class="form-group">
                                    <label for="load_button" class="control-label"><strong> </strong></label>
                                    <button type="button" class="load_button btn btn-success">{{ trans('lang.load_item') }}</button>
                                </div> --}}
                                <div class="form-actions right">
									<a class="btn blue bold disabled" id="load-boq-item">{{trans('lang.load_item')}}</a>
								</div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 ">
                                    <span class="show-message-error center font-red bold"></span>
                                </div>
                            </div>
                            <!-- Note Row color -->
                            <div class="col-md-12">
                                <label for="trans_desc" class="control-label"><strong>{{ trans('lang.status') }}</strong></label>
                                <table style="height: 40px !important;" class="table table-striped table-bordered" width="100%">
                                    <tbody>
                                        <tr>
                                            <td width="4%" class="out-stock"></td>
                                            <td width="21%">{{trans("lang.out_of_stock")}}</td>
                                            <td width="4%" class="out-boq"></td>
                                            <td width="21%">{{trans("lang.out_of_boq")}}</td>
                                            <td width="4%" class="not-set-boq"></td>
                                            <td width="21%">{{trans("lang.not_set_boq")}}</td>
                                            <td width="4%" style="background-color: white !important;"></td>
                                            <td width="21%">{{trans("lang.normal")}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Error Message -->
                            <div class="col-md-12">
                                <ul class="form-message"></ul>
                            </div>
                        </div>
					</div>
                    <!-- Table Submit Item -->
					<div class="portlet-body">
                        <table class="table table-striped table-responsive table-bordered table-hover" width="100%" id="table-income">
                            <thead>
                                <tr style="font-size:12px;">
                                    <th width="5%" class="text-center all">{{ trans('lang.line_no') }}</th>
                                    {{-- <th width="10%" class="text-center all">{{ trans('lang.item_type') }}</th> --}}
                                    <th width="15%" class="text-center all">{{ trans('lang.items') }}</th>
                                    <th width="5%" class="text-center all">{{ trans('lang.size') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.units') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.qty_in_stock') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.boq_qty') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.remain_qty') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.usage_qty') }}</th>
                                    <th width="10%" class="text-center all">{{ trans('lang.remark') }}</th>
                                    <th width="5%" class="text-center all"><i class='fa fa-plus btnAdd' id="btnAdd"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
					</div>
                    <!-- End Table Submit Item -->
                    
                    <!-- Submit Action Button -->
                    <div class="clearfix"></div><br/>
					<div class="form-actions text-right">
						<button type="submit" id="save_close" name="save_close" value="1" class="btn green bold">{{trans('lang.save')}}</button>
						<button type="submit" id="save_new" name="save_new" value="2"  class="btn blue bold">{{trans('lang.save_new')}}</button>
						<a class="btn red bold" rounte="{{$rounteBack}}" id="btnCancel">{{trans('lang.cancel')}}</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@include('modal.warehouse')
@include('modal.constructor')
@endsection()

@section('javascript')
<script type="text/javascript">	
    var objRef = [];
    var index = 0;
    function GetUnit(unit_stock) {
		return $.ajax({url:'{{url("/stock/deliv/GetUnit")}}',type:'GET',dataType:'json',data:{unit_stock:unit_stock},async:false}).responseJSON;
	}
	isDuplicateArray = function(err) {
        var duplicate = false;
        err.sort();
        var current = null;
        var cnt = 1;
        for (var i = 0; i < err.length; i++) {
            if (err[i] != current) {
                current = err[i];
            } else {
                cnt++;
            }
        }
        if (cnt > 1) {
            duplicate = true;
        }else{
            duplicate = false;
        }
        return duplicate;
    }
	
	jQuery.fn.ForceNumericOnly = function(){
        return this.each(function(){
            $(this).keydown(function(e){
                var key = e.charCode || e.keyCode || 0;
                return (
                    key == 8 || 
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };

    function GetRef(){
		return $.ajax({url:'{{url("/stock/use/GetRef")}}',type:'GET',dataType:'json',async:false}).responseJSON;
	}

	$('#save_close,#save_new').on('click',function(){
        $('.form-message').empty();
		$(this).prop('disabled', true);
        var errorClass =$("input[class*='error']");
        console.log(errorClass.length);
        if(errorClass.length == 0){
            // if(chkValid([".reference_no",".trans_date",".warehouse_id",".reference",<?php if(getSetting()->allow_zone==1){echo '".zone_id",';} ?>,<?php if(getSetting()->allow_block==1){echo '".block_id",';} ?>,<?php if(getSetting()->usage_constructor==1){echo '".sub_const",';} ?>".engineer"])){ 
            if(chkValid([".reference_no",".trans_date",".warehouse_id",".reference",".zone_id",".block_id",".building_id",".sub_const",".engineer"])){ 
                if(onSubmitFormCallback((self,msg) => {
                    $(self).addClass('invalid');
                    $('.form-message').append($('<li></li>').html(msg));
                })){
                    objRef = GetRef();
                    if (chkReference(objRef, '#reference')) {
                        $("#btnSubmit").val($(this).val());
                        $('#form-stock-entry').submit();
                        
                    }
                    else{
                        $(this).prop('disabled', false);
                        return false;
                    }
                }else{
                    $(this).prop('disabled', false);
                    return false;
                }
            }else{
                $(this).prop('disabled', false);
                return false;
            }
        }else{
            $(this).prop('disabled', false);
                return false;
        }
	});
	
	document.addEventListener("mousewheel", function(event){
		if(document.activeElement.type === "number" &&
		   document.activeElement.classList.contains("noscroll"))
		{
			document.activeElement.blur();
		}
	});

	@if(hasRole('warehouse_add'))
		var objWarehouse = JSON.parse(convertQuot('{{\App\Model\Warehouse::where(["pro_id"=>Session::get('project')])->get(["name"])}}'));
		$("#btnAddWarehouse").on('click', function(event){
			event.preventDefault();
			$('.warehouse-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#old_name').val('');
			$('#name').val('');
			$('.tel').val('N/A');
			$('#user_control').select2('val',null);
			$('.status').select2('val',1);
			$('.address').val('N/A');
			$('.button-submit-warehouse').attr('id','btnSaveWarehouse').attr('name','btnSaveWarehouse').attr('onclick','onSubmitWarehouse(this)');
			$('.button-submit-warehouse').html('{{trans("lang.save")}}');
			$('.warehouse-modal').modal('show');
		});

		function onSubmitWarehouse(field){
			$('.button-submit-warehouse').prop('disabled', true);
			if(chkValid([".name",".user_control"])){
				if(chkDublicateName(objWarehouse, '#name')){
					$.ajax({
						url :'{{url("warehouse/save")}}',
						type:'POST',
						data:$('.warehouse-form').serialize(),
						success:function(data){
							if(data){
								$("#warehouse_id").append($('<option></option>').val(data.id).text(data.name));
								$("#warehouse_id").select2('val', data.id);
							}
							$('.warehouse-modal').modal('hide');
							$('.button-submit-warehouse').prop('disabled', false);
						},error:function(){
							$('.warehouse-modal').modal('hide');
							$('.button-submit-warehouse').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-warehouse').prop('disabled', false);
					return false;					
				}
			}else{
				$('.button-submit-warehouse').prop('disabled', false);
				return false;
			}
		}
	@endif

	@if(hasRole('constructor_add'))
		var objConstructor = JSON.parse(convertQuot('{{\App\Model\Constructor::get(["name","id_card"])}}'));
		$("#btnAddEngineer").on('click', function(event){
			$('.constructor-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#id_card').val('');
			$('#old_id_card').val('');
			$('#con-name').val('');
			$('#con-tel').val('');
			$('#con-type option[value=1]').prop('disabled', false);
			$('#con-type option[value!=1]').prop('disabled', true);
			$.fn.select2.defaults.set("theme", "classic");
			$('#con-type').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
			$('#con-type').select2('val', null);
			$('#con-status').select2('val',1);
			$('#con-address').val('');
			$('.button-submit-constructor').attr('id','btnSaveEngineer').attr('name','btnSaveEngineer').attr('onclick','onSubmitEngineer(this)');
			$('.button-submit-constructor').html('{{trans("lang.save")}}');
			$('.constructor-modal').modal('show');
		});

		function onSubmitEngineer(field){
			$('.button-submit-constructor').prop('disabled', true);
			if(chkValid([".id_card",".con-name",".con-tel",".con-type",".con-status",".con-address"])){
				if(chkDublicateName(objConstructor, '#id_card')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("constr/save")}}',
						type:'POST',
						data:$('.constructor-form').serialize(),
						success:function(data){
							if(data){
								$("#engineer").append($('<option></option>').val(data.id).text(data.id_card+' ('+data.name+')'+' - '+data.tel));
								$("#engineer").select2('val', data.id);
							}
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						},error:function(){
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-constructor').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit-constructor').prop('disabled', false);
				return false;
			}
		}

		$("#btnAddSubConstructor").on('click', function(event){
			$('.constructor-modal').children().find('div').children().find('h4').html('{{trans("lang.add_new")}}');
			$('#id_card').val('');
			$('#old_id_card').val('');
			$('#con-name').val('');
			$('#con-tel').val('');
			$('#con-type option[value!=1]').prop('disabled', false);
			$('#con-type option[value=1]').prop('disabled', true);
			$.fn.select2.defaults.set("theme", "classic");
			$('#con-type').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
			$('#con-type').select2('val', null);
			$('#con-status').select2('val',1);
			$('#con-address').val('');
			$('.button-submit-constructor').attr('id','btnSaveSubConstructor').attr('name','btnSaveSubConstructor').attr('onclick','onSubmitSubConstructor(this)');
			$('.button-submit-constructor').html('{{trans("lang.save")}}');
			$('.constructor-modal').modal('show');
		});

		function onSubmitSubConstructor(field){
			$('.button-submit-constructor').prop('disabled', true);
			if(chkValid([".id_card",".con-name",".con-tel",".con-type",".con-status",".con-address"])){
				if(chkDublicateName(objConstructor, '#id_card')){
					var _token = $("input[name=_token]").val();
					$.ajax({
						url :'{{url("constr/save")}}',
						type:'POST',
						data:$('.constructor-form').serialize(),
						success:function(data){
							if(data){
								$("#sub_const").append($('<option></option>').val(data.id).text(data.id_card+' ('+data.name+')'+' - '+data.tel));
								$("#sub_const").select2('val', data.id);
							}
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						},error:function(){
							$('.constructor-modal').modal('hide');
							$('.button-submit-constructor').prop('disabled', false);
						}
					});
				}else{
					$('.button-submit-constructor').prop('disabled', false);
					return false;
				}
			}else{
				$('.button-submit-constructor').prop('disabled', false);
				return false;
			}
		}
	@endif

    getEngineers = function(success,complete){
        $.ajax({
            url:"{{url('repository/getEngineers')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getSubcontractors = function(success,complete){
        $.ajax({
            url:"{{url('repository/getSubcontractors')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getWarehouses = function(success,complete){
        $.ajax({
            url:"{{url('repository/getWarehouses')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getZones = function(success,complete){
        $.ajax({
            url:"{{url('repository/getZones')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getBuildings = function(success,complete){
        $.ajax({
            url:"{{url('repository/getBuildings')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getStreets = function(success,complete){
        $.ajax({
            url:"{{url('repository/getStreets')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getHouses = function(success,complete){
        $.ajax({
            url:"{{url('repository/getHouses')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getBlocksByZoneID = function(zoneID,success,complete){
        $.ajax({
            url:"{{url('repository/getBlocksByZoneID')}}/" + zoneID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getStreetsByZoneID = function(zoneID,success,complete){
        $.ajax({
            url:"{{url('repository/getStreetsByZoneID')}}/" + zoneID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getStreetsByBlockID = function(blockID,success,complete){
        $.ajax({
            url:"{{url('repository/getStreetsByBlockID')}}/" + blockID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    /// Get house //////

    /// get house All Trigger ///
    function getHouseAllTrigger(){
        var params = {
            zone_id : null,
            block_id : null,
            building_id : null,
            street_id : null,
            house_type_id : null
        }
        zoneID = $("#zone_id").val();
        blockID = $("#block_id").val();
        buildingID = $("#building_id").val();
        streetID = $("#street_id").val();
        houseTypeID = $("#house_type_id").val();
        if(zoneID){
            params.zone_id = zoneID;
        }
        if(blockID){
            params.block_id = blockID;
        }
        if(buildingID){
            params.building_id = buildingID;
        }
        if(streetID){
            params.street_id = streetID;
        }
        if(houseTypeID){
            params.house_type_id = houseTypeID;
        }

        $.ajax({
            url:"{{url('repository/getHousesByAllTrigger')}}",
            type:'GET',
            data:params,
            success: function(result){
                $(".house_id").empty();
                $(".house_id").select2('val', null);
                $(".house_id").append($('<option></option>').val('').text(''));
                $.each(result,function(i,val){
                    $(".house_id").append($('<option></option>').val(val.id).text(val.house_no));
                });

                // $("#boq-house").empty();
                // $.each(result,function(key, val){
                //     // if(boqHouse.length > 0){
                //     // 	for(var i =0 ; i < boqHouse.length; i++){
                //     // 		if(val.id == boqHouse[i].house_id){
                //     // 			$("#boq-house").append($('<option selected></option>').val(val.id).text(val.house_no));
                //     // 		}else{
                //     // 			$("#boq-house").append($('<option></option>').val(val.id).text(val.house_no));
                //     // 		}
                //     // 		console.log(val.id +"="+ boqHouse[i].house_id);
                //     // 	}
                //     // }else{
                //         $("#boq-house").append($('<option selected></option>').val(val.id).text(val.house_no));
                //     // }
                    
                // });
                // $('#boq-house').val(boqHouse).change();
            }
        });
    }

    getHousesByZoneID = function(zoneID,success,complete){
        $.ajax({
            url:"{{url('repository/getHousesByZoneID')}}/" + zoneID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getHousesByBlockID = function(blockID,success,complete){
        $.ajax({
            url:"{{url('repository/getHousesByBlockID')}}/" + blockID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getHousesByBuildingID = function(buildingID,success,complete){
        $.ajax({
            url:"{{url('repository/getHousesByBuildingID')}}/" + buildingID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getHousesByStreetID = function(streetID,success,complete){
        $.ajax({
            url:"{{url('repository/getHousesByStreetID')}}/" + streetID,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    getSubcontractors_success = function(res){
        $(".sub_const").empty();
		$(".sub_const").select2('val', null);
        $(".sub_const").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".sub_const").append($('<option></option>').val(val.id).text(val.id_card + ' (' + val.name + ')'));
        });
    }

    getWarehouses_success = function(res){
        $(".warehouse_id").empty();
		$(".warehouse_id").select2('val', null);
        $(".warehouse_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".warehouse_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }

    getZones_success = function(res){
        $(".zone_id").empty();
		$(".zone_id").select2('val', null);
        $(".zone_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".zone_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getBuildings_sussess = function(res){
        $(".building_id").empty();
		$(".building_id").select2('val', null);
        $(".building_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".building_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }

    getBlocks_success = function(res){
        $(".block_id").empty();
		$(".block_id").select2('val', null);
        $(".block_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".block_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    
    getStreets_success = function(res){
        $(".street_id").empty();
		$(".street_id").select2('val', null);
        $(".street_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".street_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getHouses_success = function(res){
        $(".house_id").empty();
		$(".house_id").select2('val', null);
        $(".house_id").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".house_id").append($('<option></option>').val(val.id).text(val.house_no));
        });
    }
    getEngineers_success = function(res){
        $(".engineer").empty();
		$(".engineer").select2('val', null);
        $(".engineer").append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            $(".engineer").append($('<option></option>').val(val.id).text(val.id_card + ' (' + val.name + ')'));
        });
    }

    getEngineers_complete = function(res){}
    getSubcontractors_complete = function(res){}
    getWarehouses_complete = function(res){}
    getZones_complete = function(res){}
    getStreets_complete = function(res){}
    getBlocks_complete = function(res){}
    getHouses_complete = function(res){}
    getBuilding_complete = function(res){}

    fetchUsageBOQ = function(params,success,error){}
    fetchUsageBOQ_success = function(res){}
    fetchUsageBOQ_error = function(res){}

    btnAddButtonClicked = function(e){
        var zone = $("#zone_id").val();
        var block = $("#block_id").val();
        var building = $("#building_id").val();
        var warehouse = $("#warehouse_id").val();
        console.log(block);
        if((zone == null || zone =="") || (block == null || block == "") || (building == null || building == "") || (warehouse == null || warehouse =="") ){
            $(".show-message-error").text("{{trans('lang.please_fill_require_info_load_item')}}");
        }else{
            $(".show-message-error").text("");
        // console.log(e);
        var table = $('#table-income tbody');
        var lineNum = $("#table-income tbody tr").length;
        // if(lineNum == 20){
        //     alert("Limit row of table less than 20");
        //     return;
        // }
        index++;

        // const index  = parseInt(e.timeStamp * 1000);
        const lineIndex = lineNo((lineNum),3);
            html = '<tr index='+ index +' class="tr-form tr-'+ index +'">';
				html+= '<td class="text-center all"><strong>'+lineNo(index,3)+'</strong>';
                    html+= '<input type="hidden" class="check_row check_row_'+index+'" value="" />';
                    html+= '<input type="hidden" class="line_index line_index_'+index+'" name="line_index[]" value="'+lineNo((index+1),3)+'" />';
                    html+= '<input type="hidden" value="'+lineNo((index),3)+'" name="line_no[]" class="line_no line_no_'+index+'" />';
                html+= '</td>';
				// html+= '<td>';
                //     html+= '<select class="form-control line_item_type line_item_type'+index+'" onchange="ChangeItemType(this, '+index+')" name="item_type[]">';
                //         html+= '<option value=""></option>';
                //         html+= '{{getSystemData("IT")}}'; 
                //     html+= '</select>';
                // html+= '</td>';
				html+= '<td><select onchange="onItemSelectChanged(this);" index="'+ index +'" id="item_id_'+ index +'" name="item_id[]" class="form-control select2 line_item line_item_'+index+'"><option></option></select></td>';
				html+= '<td><input type="text" length="11" class="form-control size line_size line_size_'+index+'" name="size[]" placeholder="{{trans("lang.size")}}" /></td>';
				html+= '<td>';
                    html+= '<select class="form-control select2 select2_'+index+' line_unit unit_id line_unit_'+index+'" id="unit_id_'+ index +'" name="unit_id[]" onchange="onChangeUnitNew(this, '+index+')">'; 
					// html+= '<option value=""></option>';
                    html+= '</select>';
                    html += '<input type="hidden" id="unit_factor_'+ index +'" name="unit_factor[]" class="form-control unit_factor" />';
                    html += '<input type="hidden" id="boq_unit_'+ index +'" name="boq_unit[]" class="form-control boq_unit" />';
                html+= '</td>';
                html+= '<td>';
                    html+= '<input type="hidden" length="11" class="form-control size line_size line_qty_stock_'+index+'" id="stock_qty_'+ index +'" name="stock_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-stock-qty-'+index+'"></span>';
                html+= '</td>';
                html+= '<td><input type="hidden" length="11" class="form-control size line_size line_boq_qty_'+index+'" name="boq_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-boq-qty-'+index+'"></span></td>';
				html+= '<td>';
                    html+= '<input type="hidden" length="11" class="form-control line_qty line_remain_qty_'+index+'" name="remain_qty[]" placeholder="{{trans("lang.enter_number")}}" />';
                    html+= '<input type="hidden" class="form-control line_boq_set line_boq_set_'+index+'" name="boq_set[]"/>';
                    html+= '<input type="hidden" class="form-control line_price line_price_'+index+'" name="line_price[]"/><span class="label-remain-qty-'+index+'"></span>';
                html+= '</td>';
				html += '<td><input type="number" length="11" class="form-control line_reference line_usage_qty_'+index+'" onkeyup="enterQtyUsage(this,'+index+')" id="qty_'+ index +'" name="qty[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
				html += '<td><input type="text" length="11" class="form-control  line_remark line_remark_'+index+'" name="remark[]" value="" placeholder="{{trans("lang.enter_remark")}}" /></td>';
				html += '<td><a class="row_'+index+' btn btn-sm" onclick="onRemove(this)"><i class="fa fa-trash"></i></a></td>';
			html+='</tr>';
            
			
        // table.append(buildRow(e));
        table.append(html);
        // setupSelect2();
        // setupSelect2Autocompleted();
        $(".qty").ForceNumericOnly();
        // $(".line_item_type").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
        // $(".line_item_type"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
            $(".item_id_"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
            // $(".line_unit_"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
            var itemSelect = $('#item_id_'+index);
            var type_id = $('.line_item_type'+index).val();
            itemSelect.select2({
			  width:'100%',
			  allowClear:'true',
			  placeholder:'{{trans("lang.please_choose")}}',
			  ajax: {
			    url: '{{url("/stock/use/GetItem")}}',
			    dataType:"json",
			    data: function (params) {
			      var query = {
			        q: params.term,
					cat_id : type_id
			      }
			      return query;
			    },
			    async:true,
			    success:function(data){
			    	jsonItems = data.data;
			    },
			    processResults: function (data) {
			      return {
			        results: data.data,
			        more: (data.to < data.total),
			        page: (data.current_page + 1),
			        limit: data.per_page
			      };
			    }
			  }
            });
            index++;
        }
    }

    onRemove = function(self){
        $(self).parents('tr').remove();
    }

    onItemSelectChanged = function(self){
        const index  = $(self).attr('index');
        const itemId = $(self).val();
        const selectUnitID = '#unit_id_' + index;
        const mainTR = ".tr-" + index;
        // console.log(index);

        $(mainTR).removeClass('invalid');
        $(mainTR).removeClass('out-stock');
        $(mainTR).removeClass('out-boq');
        $(mainTR).removeClass('not-set-boq');

        buildUnit(itemId,(res) => buildUnitWhenSuccess(selectUnitID,res),function(res) {
            // Noting selected
        });
        fetchCurrentBOQ(itemId,function(res){
            console.log(res);
            // Success
            $("#boq_qty_" + index).val(0);
            $("#boq_unit_" + index).val(null);
            $("#unit_factor_" + index).val(1);
            $("#stock_qty_" + index).val(0);

            $(".label-boq-qty-" + index).html(0);
            $(".label-stock-qty-" + index).html(0);
            if(res.length > 0){
                $.each(res,function(key,val){
                    console.log(val);
                    const boq           = (val.qty_std_x + val.qty_add_x);
                    const remain_boq           = (val.qty_std_x + val.qty_add_x) - val.usage_qty_x;
                    const boqWithUnit   = boq.toFixed(4) + ' | ' + val.unit;
                    const remainBoqWithUnit   = remain_boq.toFixed(4) + ' | ' + val.unit;
                    const stockWithUnit = val.stock_qty_x.toFixed(4) + ' | ' + val.unit;
                    const totalBoq = (val.qty_std_x + val.qty_add_x);

                    buildUnitWhenComplete(selectUnitID,val.unit_usage);

                    // stock out
                    if(val.stock_qty_x == 0 || boq == 0){
                        $(mainTR).addClass('out-stock');
                    }
                    // out of boq
                    else if(val.qty_std_x > 0 && boq == 0){
                        $(mainTR).addClass('out-boq');
                    }
                    // boq not set
                    else if(val.qty_std_x == 0){
                        $("#boq_set_" + index).val(-1);
                        $(mainTR).addClass('not-set-boq');
                    }
                    // normal & remove all highlight class
                    else{
                        $(mainTR).removeClass('out-stock');
                        $(mainTR).removeClass('out-boq');
                        $(mainTR).removeClass('not-set-boq');
                        $(mainTR).removeClass('invalid');
                    }
                    
                    $("#boq_qty_" + index).val(boq);
                    $("#boq_unit_" + index).val(val.unit);
                    $("#unit_factor_" + index).val(val.factor);
                    $("#stock_qty_" + index).val(val.stock_qty_x);

                    $(".label-boq-qty-" + index).html(boqWithUnit);
                    $(".label-stock-qty-" + index).html(stockWithUnit);
                    $(".label-remain-qty-"+index).html(remainBoqWithUnit);
                });
            }else{
                
                var params = {
                    'item_id' : itemId
                }
                checkStockQuantity(params,function(res){
                    if(res.length > 0){
                        $.each(res,function(key,val){
                            // console.log(val);
                            const boq           = (val.qty_std_x + val.qty_add_x) - val.usage_qty_x;
                            const boqWithUnit   = boq.toFixed(4) + ' | ' + val.unit;
                            const stockWithUnit = val.stock_qty_x.toFixed(4) + ' | ' + val.unit;

                            buildUnitWhenComplete(selectUnitID,val.unit_usage);

                            // stock out
                            if(val.stock_qty_x == 0 || boq == 0){
                                $(mainTR).addClass('out-stock');
                            }
                            // out of boq
                            else if(val.qty_std_x > 0 && boq == 0){
                                $(mainTR).addClass('out-boq');
                            }
                            // boq not set
                            else if(val.qty_std_x == 0){
                                $("#boq_set_" + index).val(-1);
                                $(mainTR).addClass('not-set-boq');
                            }
                            // normal & remove all highlight class
                            else{
                                $(mainTR).removeClass('out-stock');
                                $(mainTR).removeClass('out-boq');
                                $(mainTR).removeClass('not-set-boq');
                                $(mainTR).removeClass('invalid');
                            }
                            
                            $("#boq_qty_" + index).val(0);
                            $("#boq_unit_" + index).val(val.unit);
                            $("#unit_factor_" + index).val(val.factor);
                            $("#stock_qty_" + index).val(val.stock_qty_x);

                            $(".label-boq-qty-" + index).html(boqWithUnit);
                            $(".label-stock-qty-" + index).html(stockWithUnit);
                            $(".label-remain-qty-"+index).html(boqWithUnit);
                        });
                        
                    }else{
                        const boqWithUnit   = "{{trans('lang.none_boq')}}";
                        $(".label-boq-qty-" + index).html(boqWithUnit);
                        $(".label-stock-qty-" + index).html(0);
                    }
                   
                });
            }
        },function(err){
            // Error
            alert(err);
        });
    }

    onSubmitFormCallback = function(result){

        var okSubmit = true;

        $('tr.out-stock').each(function(){
            result(this,"Product out of stock");
            okSubmit = false;
            return;
        });

        $('tr.out-boq').each(function(){
            result(this,"Product out of BOQ");
            okSubmit = false;
            return;
        });

        var dupArray = [];

        $('.qty').each(function(i){
            const qty = this.value;
            const parent = $(this).parent().parent();

            // if(qty!=null && qty!='' && typeof qty!=undefined){
			// 	dupArray[i] = qty;
			// }

            if(qty == 0){
                result(parent,"Quantity equal zero");
                okSubmit = false;
                return;
            }

            if(qty == undefined){
                result(parent,"Quantity is undefined");
                okSubmit = false;
                return;
            }

            if(qty == ''){
                result(parent,"Quantity is empty");
                okSubmit = false;
                return;
            }            
        });
        $('.item_id').each(function(i){
            const id = this.value;
            if(id!=null && id!='' && typeof id!=undefined){
				dupArray[i] = id;
			}
        })
        // console.log(dupArray);

        if (isDuplicateArray(dupArray)==true) {
            result(this,'{{trans("lang.some_record_is_dublicate")}}');
            okSubmit = false;
            return;
		}

        $('.unit_id').each(function(){
            const unitId = this.value;
            const parent = $(this).parent().parent();
            if(unitId == undefined){
                result(parent,"Unit is undefined");
                okSubmit = false;
                return;
            }

            if(unitId == ''){
                result(parent,"Unit is empty");
                okSubmit = false;
                return;
            } 
        });

        $('.item_id').each(function(){
            const itemId = this.value;
            const parent = $(this).parent().parent();
            if(itemId == undefined){
                result(parent,"Item is undefined");
                okSubmit = false;
                return;
            }

            if(itemId == ''){
                result(parent,"Item is empty");
                okSubmit = false;
                return;
            } 
        });

        $('tr.tr-form').each(function(){
            const itemId = $(this).children().find('.line_item').val();
            const unitId = $(this).children().find('.unit_id').val();
            const unitSelectId = $(this).children().find('.unit_id').attr('id');
            const factor = $('.' + unitSelectId + '-' + unitId).attr('factor');
            const qty = $(this).children().find('.qty').val();
            const boqQty = $(this).children().find('.boq_qty').val();
            const stockQty = $(this).children().find('.stock_qty').val();
            console.log(itemId);
            console.log(unitId);

            if(itemId && unitId){

                console.log("========= CHECKING =======");
                console.log("factor = ",factor);
                console.log("stockQty = ",stockQty);
                console.log("qty = ",qty);
                console.log("==========================");

                if(factor < 1){
                    
                    var checkStockQty = stockQty / factor;
                    if(checkStockQty < qty){
                        result(this,"Quantity bigger than stock value");
                        okSubmit = false;
                        return;
                    }

                    var checkBoqQty = boqQty / factor;
                    if(checkBoqQty < qty){
                        result(this,"Quantity bigger than BOQ value");
                        okSubmit = false;
                        return;
                    }

                }else{

                    var checkStockQty = stockQty * factor;
                    if(checkStockQty < qty){
                        result(this,"Quantity bigger than stock value");
                        okSubmit = false;
                        return;
                    }
                    
                    var checkBoqQty = boqQty * factor;
                    if(checkBoqQty < qty){
                        result(this,"Quantity bigger than BOQ value");
                        okSubmit = false;
                        return;
                    }
                }
            }else{
                result(this,"Invalid value");
                okSubmit = false;
                return;
            }
        });
 
        return okSubmit;
    }

    onUnitSelectChanged = function(self){
        const index = $(self).attr('index');
    }

    // buildRow = function(e){
    //     var lineNum = $("#table-income tbody tr").length;
    //     if(lineNum == 20){
    //         alert("Limit row of table less than 20");
    //         return;
    //     }

    //     // const index  = parseInt(e.timeStamp * 1000);
    //     const lineIndex = lineNo((lineNum+1),3);
    //     // var row  = '<tr index='+ index +' class="tr-form tr-'+ index +'">';
    //     //     // No
    //     //     row += '<td width="5%">';
    //     //     row += '<strong>'+ lineIndex +'</strong>';
    //     //     row += '<input type="hidden" value='+lineIndex+' id="line_index'+ index +'" name="line_index[]" class="form-control line_index" />';
    //     //     row += '</td>';
    //     //     // Item
    //     //     row += '<td width="30%">';
    //     //     row += '<select onchange="onItemSelectChanged(this);" index="'+ index +'" id="item_id_'+ index +'" name="item_id[]" class="form-control select2 item_id"><option></option></select>';
    //     //     row += '</td>';
    //     //     // BOQ Qty
    //     //     row += '<td width="15%" class="text-center">';
    //     //     row += '<label class="label-boq-qty-'+ index +'">0</label>';
    //     //     row += '<input type="hidden" id="boq_qty_'+ index +'" name="boq_qty[]" class="form-control boq_qty" />';
    //     //     row += '<input type="hidden" id="boq_unit_'+ index +'" name="boq_unit[]" class="form-control boq_unit" />';
    //     //     row += '<input type="hidden" id="boq_set_'+ index +'" name="boq_set[]" class="form-control boq_set" />';
    //     //     row += '</td>';
    //     //     // BOQ Unit
    //     //     row += '<td width="15%" class="text-center">';
    //     //     row += '<label class="label-stock-qty-'+ index +'">0</label>';
    //     //     row += '<input type="hidden" id="stock_qty_'+ index +'" name="stock_qty[]" class="form-control stock_qty" />';
    //     //     row += '</td>';
    //     //     // Qty
    //     //     row += '<td width="15%">';
    //     //     row += '<input index="'+ index +'" type="text" id="qty_'+ index +'" name="qty[]" class="form-control qty" />';
    //     //     row += '</td>';
    //     //     // Unit
    //     //     row += '<td width="15%">';
    //     //     row += '<input type="hidden" id="unit_factor_'+ index +'" name="unit_factor[]" class="form-control unit_factor" />';
    //     //     row += '<select index="'+ index +'" id="unit_id_'+ index +'" name="unit_id[]" class="form-control select2 unit_id"><option></option></select>';
    //     //     row += '</td>';
    //     //     // Remove
    //     //     row += '<td width="5%" class="text-center">';
    //     //     row += '<button type="button" index="'+ index +'" onclick="onRemove(this);" class="btn btn-sm red"><i class="fa fa-trash"></i></button>';
    //     //     row += '</td>';
    //     //     row += '</tr>';

    //         html = '<tr index='+ index +' class="tr-form tr-'+ index +'">';
	// 			html+= '<td class="text-center all"><strong>'+lineNo(index+1,3)+'</strong>';
    //                 html+= '<input type="hidden" class="check_row check_row_'+index+'" value="" />';
    //                 html+= '<input type="hidden" class="line_index line_index_'+index+'" name="line_index[]" value="'+lineNo((index+1),3)+'" />';
    //                 html+= '<input type="hidden" value="'+lineNo((index+1),3)+'" name="line_no[]" class="line_no line_no_'+index+'" />';
    //             html+= '</td>';
	// 			// html+= '<td>';
    //             //     html+= '<select class="form-control line_item_type line_item_type'+index+'" onchange="ChangeItemType(this, '+index+')" name="item_type[]">';
    //             //         html+= '<option value=""></option>';
    //             //         html+= '{{getSystemData("IT")}}'; 
    //             //     html+= '</select>';
    //             // html+= '</td>';
	// 			html+= '<td><select onchange="onItemSelectChanged(this);" index="'+ index +'" id="item_id_'+ index +'" name="item_id[]" class="form-control select2 item_id_"'+index+'><option></option></select></td>';
	// 			html+= '<td><input type="text" length="11" class="form-control size line_size line_size_'+index+'" name="size[]" placeholder="{{trans("lang.size")}}" /></td>';
	// 			html+= '<td>';
    //                 html+= '<select class="form-control select2 select2_'+index+' line_unit line_unit_'+index+'" id="unit_id_'+ index +'" name="unit_id[]"> onchange="onChangeUnit(this, '+index+')"'; 
	// 				html+= '<option value=""></option>';
    //                 html+= '</select>';
    //                 html += '<input type="hidden" id="unit_factor_'+ index +'" name="unit_factor[]" class="form-control unit_factor" />';
    //                 html += '<input type="hidden" id="boq_unit_'+ index +'" name="boq_unit[]" class="form-control boq_unit" />';
    //             html+= '</td>';
    //             html+= '<td>';
    //                 html+= '<input type="hidden" length="11" class="form-control size line_size line_qty_stock_'+index+'" id="stock_qty_'+ index +'" name="stock_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-stock-qty-'+index+'"></span>';
    //             html+= '</td>';
    //             html+= '<td><input type="hidden" length="11" class="form-control size line_size line_boq_qty_'+index+'" name="boq_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-boq-qty-'+index+'"></span></td>';
	// 			html+= '<td>';
    //                 html+= '<input type="hidden" length="11" class="form-control line_qty line_remain_qty_'+index+'" name="line_remain_qty_[]" placeholder="{{trans("lang.enter_number")}}" />';
    //                 html+= '<input type="hidden" class="form-control line_boq_set line_boq_set_'+index+'" name="boq_set[]"/>';
    //                 html+= '<input type="hidden" class="form-control line_price line_price_'+index+'" name="line_price[]"/><span class="label-remain-qty-'+index+'"></span>';
    //             html+= '</td>';
	// 			html += '<td><input type="number" length="11" class="form-control line_reference line_usage_qty_'+index+'" id="qty_'+ index +'" name="qty[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
	// 			html += '<td><input type="text" length="11" class="form-control  line_remark line_remark_'+index+'" name="line_remark[]" value="" placeholder="{{trans("lang.enter_remark")}}" /></td>';
	// 			html += '<td><a class="row_'+index+' btn btn-sm" onclick="DeleteRowBOQ('+index+')"><i class="fa fa-trash"></i></a></td>';
	// 		html+='</tr>';
    //         $(".line_item_type"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
    //         $(".item_id_"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
    //         $(".line_unit_"+index).select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
    //         var itemSelect = $('#item_id_'+index);
    //         var type_id = $('.line_item_type'+index).val();
    //         // itemSelect.select2({
	// 		//   width:'100%',
	// 		//   allowClear:'true',
	// 		//   placeholder:'{{trans("lang.please_choose")}}',
	// 		//   ajax: {
	// 		//     url: '{{url("/stock/use/GetItem")}}',
	// 		//     dataType:"json",
	// 		//     data: function (params) {
	// 		//       var query = {
	// 		//         q: params.term,
	// 		// 		cat_id : type_id
	// 		//       }
	// 		//       return query;
	// 		//     },
	// 		//     async:true,
	// 		//     success:function(data){
	// 		//     	jsonItems = data.data;
	// 		//     },
	// 		//     processResults: function (data) {
	// 		//       return {
	// 		//         results: data.data,
	// 		//         more: (data.to < data.total),
	// 		//         page: (data.current_page + 1),
	// 		//         limit: data.per_page
	// 		//       };
	// 		//     }
	// 		//   }
	// 		// });
    //     return html;
    // }
	
    setupDate = function(){
        $('#trans_date').val(formatDate("{{date('Y-m-d')}}"));
		$("#trans_date").datepicker({
			format: "{{getSetting()->format_date}}",
            autoclose: true,
            pickerPosition: "bottom-right"
		});
    }
    var $S2 = $("select[name=select2]");
    $S2.attr("readonly", "readonly");

    setupSelect2 = function(){
        $(".select2").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
        $(".line_unit").select2({readonly:'readonly'});
        // $('.line_unit').on('click', function() {
        // //readonly_select($(".select2"), true);

        // $S1.attr("readonly", "readonly");
    
            // $S2.attr("readonly", "readonly");

        // });
    }

    setupSelect2Autocompleted = function(){
        var type_id = $('.line_item_type'+index).val();
        $(".item_id").select2({
            width:'100%',
            allowClear:'true',
            placeholder:'{{trans("lang.please_choose")}}',
            ajax: {
                url: '{{url("/stock/use/GetItem")}}',
                dataType:"json",
                data: function (params) {
                    var query = {
                        q: params.term,
                        cat_id : type_id
                    }
                    return query;
                },
                async:true,
                success:function(data){
                    jsonItems = data.data;
                },
                processResults: function (data) {
                    return {
                        results: data.data,
                        more: (data.to < data.total),
                        page: (data.current_page + 1),
                        limit: data.per_page
                    };
                }
            }
        });
    }

    checkStockQuantity = function(params,success,complete){
        $.ajax({
            url:'{{url("repository/checkStockQuantity")}}',
            type:'GET',
            data: params,
            success: success,
            complete: complete
        });
    }

    buildUnit = function(itemId,success,complete){
        $.ajax({
            url:'{{url("stock/use/policy/buildUnit")}}/' + itemId,
            type:'GET',
            success: success,
            complete: complete
        });
    }

    buildUnitWhenSuccess  = function(self,res){
        const index = $(self).attr('id');
        $(self).empty();
		// $(self).select2('val', null);
        $(self).val(null);
        // $(self).append($('<option></option>').val('').text(''));
        $.each(res,function(i,val){
            console.log(val);
            const className = index + '-' + val.from_code;
            $(self).append($('<option class='+ className +' factor='+ val.factor +' to_code='+ val.to_code +'></option>').val(val.from_code).text(val.from_desc));
        });
    }

    buildUnitWhenComplete = function(self,selectedValue){
        // $(self).select2('val', selectedValue);
        $(self).val(selectedValue);
    }

    /// Current BOQ ///
    /// by zone,street,house
    /// Callback success
    /// Callback complete
    fetchCurrentBOQ = function(itemId,success,error){

        var params = {
            zone_id: null,
            block_id: null,
            building_id:null,
            street_id: null,
            house_id: null,
            trans_date: '{{date("Y-m-d")}}',
            item_id: itemId
        };

        @if(getSetting()->allow_zone == 1)
            if (zoneId = $("#zone_id").val()) {
                params.zone_id = zoneId;
            }
        @endif

        @if(getSetting()->allow_block == 1)
            if (blockId = $("#block_id").val()) {
                params.block_id = blockId;
            }
        @endif
        if(buildingID = $("#building_id").val()){
            params.building_id = buildingID;
        }
        if (streetId = $("street_id").val()) {
            params.street_id = streetId;
        }

        if (houseId = $("house_id").val()) {
            params.house_id = houseId;
        }

        if (transDate = $("trans_date").val()) {
            params.trans_date = transDate;
        }

        $.ajax({
            url:'{{url("repository/fetchCurrentBOQ")}}',
            type:'GET',
            data: params,
            success:success,
            error:error
        });
    }

	$(document).ready(function(){
		setupSelect2();
		setupDate();
        getEngineers(getEngineers_success,getEngineers_complete);
        getSubcontractors(getSubcontractors_success,getSubcontractors_complete);
        getWarehouses(getWarehouses_success,getWarehouses_complete);
        // getStreets(getStreets_success,getStreets_complete);
        // getHouses(getHouses_success,getHouses_complete);
        @if(getSetting()->allow_zone == 1)
        getZones(getZones_success,getZones_complete);
        
        $('.zone_id').on('change',function(){
            const zoneID = $(this).val();
            const blockID = $('#block_id').val();
            if(zoneID){

                @if(getSetting()->allow_block == 1)
                    getBlocksByZoneID(zoneID,getBlocks_success,getBlocks_complete);
                    getBuilding(zoneID,blockID);
                @endif

                // getStreetsByZoneID(zoneID,getStreets_success,getStreets_complete);
                // getHousesByZoneID(zoneID,getHouses_success,getHouses_complete);
                // getHouseAllTrigger();
            }
        });
        @endif

        @if(getSetting()->allow_block == 1)
        $('.block_id').on('change',function(){
            const blockID = $(this).val();
            const zoneID = $('#zone_id').val();
            if(blockID){
                getBuilding(zoneID,blockID);
                // getStreetsByBlockID(blockID,getStreets_success,getStreets_complete);
                // getHousesByBlockID(blockID,getHouses_success,getHouses_complete);
                // getHouseAllTrigger();
            }
        });
        @endif
        getBuildings(getBuildings_sussess,getBuilding_complete);
        $('.building_id').on('change',function(){
            const buildingID = $(this).val();
            if(buildingID){
                $('#load-boq-item').removeClass("disabled");
                // getHouseAllTrigger();
            }
        });

        $('.street_id').on('change',function(){
            const buildingID = $(this).val();
            if(buildingID){

                // getHouseAllTrigger();
            }
        });

		$("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
		
		$('#btnAdd').on('click',function(e){
            btnAddButtonClicked(e);
        });
		
		setInterval(function() {
			$.ajax({
				url :'{{url("prefix/gen/USE/Usage/ref_no")}}',
				type:'get',
				success:function(data){
					if(data!=null && data!=''){
						$('#reference_no').attr("readonly", true);
						$('#reference_no').val(data);
					}else{
						$('#reference_no').attr("readonly", false);
					}
				},error:function(){}
			});
		}, 3000);
	});

    $("#load-boq-item").on("click",function(){
		var params = {
			zone_id: null,
			block_id: null,
			building_id : null,
			street_id: null,
			house_type: null,
            warehouse : null,
            working_type : null,
		};
		const zoneID    = $('#zone_id').val();
		const blockID   = $('#block_id').val();
		const buildingID    = $('#building_id').val();
		const streetID  = $('#street_id').val();
		const houseType = $('#house_type_id').val();
        const wareHouse = $('#warehouse_id').val();
        const workingType = $("#working_type_id").val();

		if(zoneID){
			params.zone_id = zoneID;
		}

		if(blockID){
			params.block_id = blockID;
		}
		if(buildingID){
			params.building_id = buildingID;
		}

		if(streetID){
			params.street_id = streetID;
		}

		if(houseType){
			params.house_type = houseType;
		}
        if(workingType){
            params.working_type = workingType;
        }
        if(wareHouse){
            params.warehouse = wareHouse;
        }
        if(zoneID && blockID && buildingID && wareHouse){
            $("#table-income tbody").empty();
            var html = "<tr ><td colspan='10'><div class='loader'></div></td></tr>";
            $("#table-income tbody").append(html);
            $.ajax({
                url :'{{url("repository/getBoqItems")}}',
                type:'GET',
                data:params,
                success:function(data){
                    $("#table-income tbody").empty();
                    $.each(data,function(key, val){
                        index++;
                        loadWorkingTypeItem(val,index);
                        
                    });
                },error:function(){
                    
                }
            });
        }else{
            console.log(111);
        }
	});
    function onChangeUnitNew(field, row){
        const mainTR = ".tr-" + row;
		var warehouse_id = $(".warehouse_id").val();
		var trans_date = $(".trans_date").val();
		var item_id = $(".line_item_"+row).val();
        var zone_id = $("#zone_id").val();
        var block_id = $("#block_id").val();
        var building_id = $("#building_id").val();
		var unit = $(field).val();
		if(warehouse_id!=null && warehouse_id!='' && item_id!=null && item_id!='' && unit!=null && unit!='' && trans_date!=null && trans_date!='' && building_id!=null && building_id && building_id != ''){
			var _token = $("input[name=_token]").val();
			$.ajax({
				url :'{{url("stock/use/remoteItemWithBuilding")}}',
				type:'POST',
				data:{
					'_token': _token,
					'warehouse_id':warehouse_id,
					'house_id':null,
					'item_id':item_id,
					'unit':unit,
                    'building_id' : building_id,
					'trans_date':trans_date,
				},
				success:function(data){
                    console.log(data);
					var stock_qty = parseFloat(data.stock_qty);
					var boq_qty = parseFloat(data.boq_set);
                    var remain_qty = parseFloat(data.remain_boq);
					var stock_sty_with_unit = stock_qty.toFixed(4) + " | " + unit;
                    var boq_qty_with_unit = boq_qty.toFixed(4) + " | " + unit;
                    var remain_boq_qty_with_unit = remain_qty.toFixed(4) + " | " + unit;
                    if(data.boq_set == -1){
                        var boq_qty_with_unit =  "Not set boq";
                    }
                    // $("#boq_unit_" + index).val(val.unit);
                    // $("#unit_factor_" + index).val(val.factor);
                    $("#boq_qty_" + row).val(boq_qty);
                    $("#stock_qty_" + row).val(stock_qty);
                    $(".line_boq_set_" + row).val(boq_qty);
                    $(".line_remain_qty_"+row).val(remain_qty);
					// $('.line_stock_qty_'+row).val(stock_qty);
					// $('.line_boq_set_'+row).val(boq_qty);
					$('.label-stock-qty-'+row).html(stock_sty_with_unit);
                    $('.label-boq-qty-'+row).html(boq_qty_with_unit);
					$('.label-remain-qty-'+row).html(remain_boq_qty_with_unit);
                    if(data.stock_qty == 0 || data.boq_set == 0){
                        $(mainTR).addClass('out-stock');
                    }
                    // out of boq
                    else if(data.stock_qty > 0 && data.boq_set == 0){
                        $(mainTR).addClass('out-boq');
                    }
                    // boq not set
                    else if(data.stock_qty == 0){
                        $(mainTR).addClass('not-set-boq');
                    }
                    // normal & remove all highlight class
                    else{
                        $(mainTR).removeClass('out-stock');
                        $(mainTR).removeClass('out-boq');
                        $(mainTR).removeClass('not-set-boq');
                        $(mainTR).removeClass('invalid');
                    }
				},error:function(){
					// $('.line_stock_qty_'+row).val(0);
					// $('.line_boq_set_'+row).val(0);
                    $("#boq_qty_" + row).val(0);
                    $("#stock_qty_" + index).val(0);
                    $(".line_boq_set_" + row).val(0);
                    $(".line_remain_qty_"+row).val(0);
					$('.label_qty_stock_'+row).html(0);
					$('.label_qty_remain_'+row).html(0);
					console.log('error get qty stock.');
				}
			});
		}
		$(".line_use_qty_"+row).val('');
		$(".check_row_"+row).val(warehouse_id+"_"+item_id);
	}
    function onChangeItem(field, row){
		var val = $(field).val();
		if(val!=null && val!='' && jsonItems){
			$.each(jsonItems.filter(c=>c.id==val), function(key, val){
				$('.line_unit_'+row).empty();
				$('.line_unit_'+row).append($('<option></option>').val('').text(''));
				jsonUnits = GetUnit(val.unit_stock);
				$.each(jsonUnits, function(k, v){
					$('.line_unit_'+row).append($('<option></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
				});
				$('.line_unit_'+row).select2('val', val.unit_usage);
			});
		}
	}

    function enterQtyUsage(field, row){
		var stock_qty = $(".qty_stock_"+row).val();
		var boq_set = $(".line_remain_qty_"+row).val();
		var use_qty = $(field).val();
		if(boq_set!=null && boq_set!='' && boq_set!=null && stock_qty!=null && stock_qty!='' && use_qty!=null && use_qty!=''){
			if(parseFloat(boq_set) == -1){
				if(parseFloat(use_qty) > parseFloat(stock_qty)){
					$(field).css('border','1px solid #e43a45');
                    $(field).addClass("error");
					$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
				}else{
					$(field).css('border','1px solid #c2cad8');
                    $(field).removeClass("error");
					$(".show-message-error").empty();
				}
			}else{
				if(parseFloat(use_qty) > parseFloat(boq_set)){
					$(field).css('border','1px solid #e43a45');
                    $(field).addClass("error");
					$(".show-message-error").html('{{trans("lang.greater_than_boq_qty")}}!');
				}else if(parseFloat(use_qty) > parseFloat(stock_qty)){
					$(field).css('border','1px solid #e43a45');
                    $(field).addClass("error");
					$(".show-message-error").html('{{trans("lang.greater_than_stock_qty")}}!');
				}else{
					$(field).css('border','1px solid #c2cad8');
                    $(field).removeClass("error");
					$(".show-message-error").empty();
				}
			}
		}else{
			$(field).css('border','1px solid #c2cad8');
            $(field).removeClass("error");
			$(".show-message-error").empty();
		}
	}

    function loadWorkingTypeItem(data,index){
		var table_boq = $('#table-income');
		html = '<tr index='+ index +' class="tr-form tr-'+ index +'">';
				html+= '<td class="text-center all"><strong>'+lineNo(index,3)+'</strong>';
                    html+= '<input type="hidden" class="check_row check_row_'+index+'" value="" />';
                    html+= '<input type="hidden" class="line_index line_index_'+index+'" name="line_index[]" value="'+lineNo((index+1),3)+'" />';
                    html+= '<input type="hidden" value="'+lineNo((index),3)+'" name="line_no[]" class="line_no line_no_'+index+'" />';
                html+='</td>';
				// html+= '<td>'+data.item_type+'<input type="hidden" name="line_item_type[]" class="line_item_type'+index+'" value="'+data.cat_id+'" /></td>';
				html+= '<td>'+data.item_name+'<input type="hidden" class="line_item line_item_'+index+'" id="item_id_'+index+'" name="item_id[]" value="'+data.item_id+'" /></td>';
				html+= '<td><input type="text" length="11" class="form-control size line_size line_size_'+index+'" name="size[]" placeholder="{{trans("lang.size")}}" /></td>';
				html+= '<td>';
                    html+= '<select class="form-control select2 select2_'+index+' line_unit unit_id line_unit_'+index+'" id="unit_id_'+index+'" name="unit_id[]" onchange="onChangeUnitNew(this, '+index+')">' 
                        // html+= '<option value=""></option>';
                    html+= '</select>';
                    html += '<input type="hidden" id="unit_factor_'+ index +'" name="unit_factor[]" class="form-control unit_factor" />';
                    html += '<input type="hidden" id="boq_unit_'+ index +'" name="boq_unit[]" class="form-control boq_unit" />';
                html+= '</td>';
                html+= '<td><input type="hidden" length="11" class="form-control size line_size stock_qty qty_stock_'+index+'" id="stock_qty_'+index+'" name="stock_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-stock-qty-'+index+'"></span></td>';
                html+= '<td><input type="hidden" length="11" class="form-control size line_size boq_qty boq_qty_'+index+'" id="boq_qty_'+index+'" name="boq_qty[]" placeholder="{{trans("lang.size")}}" /><span class="label-boq-qty-'+index+'"></span></td>';
				html+= '<td><input type="hidden" length="11" class="form-control line_qty line_remain_qty_'+index+'" name="remain_qty[]" placeholder="{{trans("lang.enter_number")}}" /><input type="hidden" class="form-control line_boq_set line_boq_set_'+index+'" name="boq_set[]"/>'+
					    '<input type="hidden" class="form-control line_price line_price_'+index+'" name="line_price[]"/><span class="label-remain-qty-'+index+'"></span></td>';
				html += '<td><input type="number" length="11" class="form-control line_reference qty line_usage_qty_'+index+'" onkeyup="enterQtyUsage(this,'+index+')" id="qty_'+index+'" name="qty[]" value="0" placeholder="{{trans("lang.enter_number")}}" /></td>';
				html += '<td><input type="text" length="11" class="form-control  line_remark line_remark_'+index+'" name="remark[]" value="" placeholder="{{trans("lang.enter_remark")}}" /></td>';
				html += '<td><a class="row_'+index+' btn btn-sm" onclick="onRemove(this)"><i class="fa fa-trash"></i></a></td>';
			html+='</tr>';
		table_boq.append(html);
		
		$.fn.select2.defaults.set('theme','classic');
		// $('.select2').select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
		$('.line_unit_'+index).empty();
		// $('.line_unit_'+index).append($('<option></option>').val('').text(''));
		jsonUnits = GetUnit(data.unit_stock);
		$.each(jsonUnits, function(k, v){
            const className = index + '-' + v.from_code;
			$('.line_unit_'+index).append($('<option class="unit_id_'+className+'" factor='+ v.factor +' to_code='+ v.to_code +'></option>').val(v.from_code).text(v.from_code+' ('+v.from_desc+')'));
		});
		@if(!isset($head))
			// $('.line_unit_'+index).select2('val', data.unit_purch);
            $('.line_unit_'+index).val( data.unit_purch);
			var field = '.line_unit_'+index;
			onChangeUnitNew(field,index);
			
		@endif
        const selectUnitID = '.line_unit_' + index;
        const mainTR = ".tr-" + index;

        $(mainTR).removeClass('invalid');
        $(mainTR).removeClass('out-stock');
        $(mainTR).removeClass('out-boq');
        $(mainTR).removeClass('not-set-boq');

        // fetchCurrentBOQ(data.item_id,function(res){
        //     // Success
        //     $("#boq_qty_" + index).val(0);
        //     $("#boq_unit_" + index).val(null);
        //     $("#unit_factor_" + index).val(1);
        //     $("#stock_qty_" + index).val(0);

        //     $(".label-boq-qty-" + index).html(0);
        //     $(".label-stock-qty-" + index).html(0);
            

        //     if(res){
        //         $.each(res,function(key,val){
        //             console.log(val);
        //             const boq           = (val.qty_std_x + val.qty_add_x);
        //             const boq_remain    = (val.qty_std_x + val.qty_add_x) + val.usage_qty_x;
        //             const boqWithUnit   = boq + ' | ' + val.unit_usage;
        //             const stockWithUnit = val.stock_qty_x + ' | ' + val.unit;
        //             const boq_remain_unit = boq_remain + ' | ' + val.unit_usage;

        //             buildUnitWhenComplete(selectUnitID,val.unit_usage);

        //             // stock out
        //             if(val.stock_qty_x == 0 || boq == 0){
        //                 $(mainTR).addClass('out-stock');
        //             }
        //             // out of boq
        //             else if(val.qty_std_x > 0 && boq == 0){
        //                 $(mainTR).addClass('out-boq');
        //             }
        //             // boq not set
        //             else if(val.qty_std_x == 0){
        //                 $("#boq_set_" + index).val(-1);
        //                 $(mainTR).addClass('not-set-boq');
        //             }
        //             // normal & remove all highlight class
        //             else{
        //                 $(mainTR).removeClass('out-stock');
        //                 $(mainTR).removeClass('out-boq');
        //                 $(mainTR).removeClass('not-set-boq');
        //                 $(mainTR).removeClass('invalid');
        //             }
                    
        //             $("#boq_qty_" + index).val(boq);
        //             $("#boq_unit_" + index).val(val.unit);
        //             $("#unit_factor_" + index).val(val.factor);
        //             $("#stock_qty_" + index).val(val.stock_qty_x);

        //             $(".label-boq-qty-" + index).html(boqWithUnit);
        //             $(".label-stock-qty-" + index).html(stockWithUnit);
        //             $(".label-remain-qty-"+index).html(boq_remain_unit);
        //         });
        //     }
        // },function(err){
        //     // Error
        //     alert(err);
        // });
	}

    function onChangeUnit(field, row){
		var item_id = $(".line_item_"+row).val();
		var unit = $(field).val();
		var _token = $("input[name=_token]").val();
		var params = {
			zone_id: null,
			block_id: null,
			building_id : null,
			street_id: null,
			house_type: null,
			_token :_token,
			item_id : item_id,
			unit : unit
		};
		const zoneID    = $('#zone_id').val();
		const blockID   = $('#block_id').val();
		const buildingID    = $('#building_id').val();
		const streetID  = $('#boq-street').val();
		const houseType = $('#boq-house-type').val();

		if(zoneID){
			params.zone_id = zoneID;
		}

		if(blockID){
			params.block_id = blockID;
		}
		if(buildingID){
			params.building_id = buildingID;
		}

		if(streetID){
			params.street_id = streetID;
		}

		if(houseType){
			params.house_type = houseType;
		}
		
		if(item_id!=null && item_id!='' && unit!=null && unit!=''){
			
			$.ajax({
				url :'{{url("purch/request/remoteItem")}}',
				type:'POST',
				async:false,
				data:params,
				success:function(data){
					// console.log(data);
					$('.line_boq_set_'+row).val(data.boq_set);
					$('.line_price_'+row).val(data.price);
				},error:function(){
					$('.line_boq_set_'+row).val(0);
					$('.line_price_'+row).val(0);
					console.log('error get qty stock.');
				}
			});
		}
		$(".line_return_qty_"+row).val('');
		$(".check_row_"+row).val(item_id+"_"+unit);
	}

    function ChangeItemType(val, row){
        
		if(val!=null && val!=''){
			var itemSelect = $('.item_id_'+row);
			var type_id = $('.line_item_type'+row).val();
			// console.log(itemSelect.select2({allowClear:'false'}));
            // var query = {
			// 		cat_id : type_id,
			// 		q: "",
			//       };
            // $.ajax({
			// 	url :'{{url("/stock/use/GetItem")}}',
			// 	type:'GET',
			// 	async:false,
			// 	data:query,
			// 	success:function(data){
            //         jsonItems = data.data;
			// 		console.log(jsonItems);
					
			// 	},
            //     processResults: function (data) {
			//       return {
			//         results: data.data,
			//         more: (data.to < data.total),
			//         page: (data.current_page + 1),
			//         limit: data.per_page
			//       };
			//     },error:function(){
			// 		console.log('error get qty stock.');
			// 	}
			// });
			itemSelect.select2({
			  width:'100%',
			  allowClear:'false',
			  placeholder:'{{trans("lang.please_choose")}}',
			  ajax: {
			    url: '{{url("/stock/use/GetItem")}}',
			    dataType:"json",
			    data: function (params) {
			      var query = {
					cat_id : type_id,
					q: params.term,
			      }
			      return query;
			    },
			    async:true,
			    success:function(data){
                    // console.log(data);
			    	jsonItems = data.data;
			    },
			    processResults: function (data) {
			      return {
			        results: data.data,
			        more: (data.to < data.total),
			        page: (data.current_page + 1),
			        limit: data.per_page
			      };
			    }
			  }
			});

		}else{
			$(".show-message-error").html('{{trans("lang.not_more_than_100")}}!');
		}
        // }

        
	}
    function getBuilding(zone_id,block_id){
        var params = {
            zone_id : null,
            block_id : null
        }

        const zoneId = $("#zone_id").val();
        const blockID = $("#block_id").val();
        if(zoneId){
            params.zone_id = zoneId;
        }
        if(blockID){
            params.block_id = blockID;
        }

        $.ajax({
            url:"{{url('repository/getBuilding')}}",
            type:'GET',
            data: params,
            success: function(result){
                $("#building_id").empty();
                $("#building_id").append($('<option></option>').val("").text(""));
                $('#building_id').val(null).trigger('change');
                $.each(result,function(key, val){
                    $("#building_id").append($('<option></option>').val(val.id).text(val.name));						
                });
            }
        });
    }
</script>
@endsection()
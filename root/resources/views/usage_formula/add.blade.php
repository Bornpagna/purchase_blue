@extends('layouts.app')

@section('content')
<style>
    td.details-control {
        background: url("{{url("assets/upload/temps/details_open.png")}}") no-repeat center center !important;
        cursor: pointer !important;
    }
    tr.shown td.details-control {
        background: url("{{url("assets/upload/temps/details_close.png")}}") no-repeat center center !important;
    }
	.btnAdd{
		cursor: pointer;
	}
	.form-horizontal .form-group {
		margin-left: 0px !important;
		margin-right: 0px !important;
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
                <!-- Form -->
                <form id="frmUsagePolicy" class="form-horizontal" method="POST" action="{{$route}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                        @if(getSetting()->allow_zone == 1)
                        <!-- Zone -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zone_id" class="control-label" style="text-align: left;"><strong>{{trans('lang.zone')}}</strong></label>
                                <select class="form-control select2 zone_id" name="zone_id" id="zone_id">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        @endif
                        @if(getSetting()->allow_block == 1)
                        <!-- Block -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="block_id" class="control-label" style="text-align: left;"><strong>{{trans('lang.block')}}</strong></label>
                                <select class="form-control select2 block_id" name="block_id" id="block_id">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        @endif
                        {{-- @if(getSetting()->allow_building == 1) --}}
                        <!-- Block -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="building_id" class="control-label" style="text-align: left;"><strong>{{trans('lang.building')}}</strong></label>
                                <select class="form-control select2 building_id" name="building_id" id="building_id">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        {{-- @endif --}}
                        <!-- Street -->
                        {{-- <div class="col-md-4">
                            <div class="form-group">
                                <label for="street_id" class="control-label" style="text-align: left;"><strong>{{trans('lang.street')}}</strong></label>
                                <select class="form-control select2 street_id" name="street_id" id="street_id">
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
                        </div> --}}
                    </div>
                    <!-- Generate Button -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group text-right">
                                    <button onclick="onGenenerateButtonClicked();" type="button" id="generate" name="generate" value="generate"  class="btn blue bold disabled">{{trans('lang.generate')}}</button>
                            </div>
                        </div>
                    </div>
                    <!-- Table Generate -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-hover" id="my-table">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center all">{{ trans('lang.no') }}</th>
                                        @if(getSetting()->allow_zone == 1)
                                        <th width="15%" class="text-center all">{{ trans('lang.zone') }}</th>
                                        @endif
                                        @if(getSetting()->allow_block == 1)
                                        <th width="15%" class="text-center all">{{ trans('lang.block') }}</th>
                                        @endif
                                        <th width="15%" class="text-center all">{{ trans('lang.street') }}</th>
                                        <th width="15%" class="text-center all">{{ trans('lang.house_type') }}</th>
                                        <th width="15%" class="text-center all">{{ trans('lang.house_no') }}</th>
                                        <th width="15%" class="text-center all">{{ trans('lang.percent') }}</th>
                                        <th width="5%" class="text-center all">{{ trans('lang.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Action Submit -->
                    <div class="form-actions text-right">
						<button onclick="onSubmit();" type="button" id="save_close" name="save_close" value="1" class="btn green bold">{{trans('lang.save')}}</button>
						<button onclick="onSubmit();" type="button" id="save_new" name="save_new" value="2"  class="btn blue bold">{{trans('lang.save_new')}}</button>
						<a class="btn red bold" rounte="{{$route}}" id="btnCancel">{{trans('lang.cancel')}}</a>
					</div>
                </form>
			</div>
		</div>
	</div>
</div>

<!-- Modal Varian -->
@include('modal.upload')

@endsection()

@section('javascript')
<script type="text/javascript">

    onSubmit = function(){
        var percent = 0;
        $('.input-percent').each(function(e){
            console.log($(this).val());
            const val = $(this).val();
            percent += parseFloat(val ? val : 0);
        });
        console.log(percent);
        if(percent == 100){
            $('#frmUsagePolicy').submit();
        }
    }

    getZones = function(success,complete){
        $.ajax({
            url:"{{url('repository/getZones')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getZonesSuccess = function(response){
        $(".zone_id").empty();
		$(".zone_id").select2('val', null);
        $(".zone_id").append($('<option></option>').val('').text(''));
        $.each(response,function(i,val){
            $(".zone_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getZonesComplete = function(response){
        $(".zone_id").select2('val', null);
    }

    getBlocks = function(success,complete){
        $.ajax({
            url:"{{url('repository/getBlocks')}}",
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
    getBlocksSuccess = function(response){
        $(".block_id").empty();
		$(".block_id").select2('val', null);
        $(".block_id").append($('<option></option>').val('').text(''));
        $.each(response,function(i,val){
            $(".block_id").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getBlocksComplete = function(response){
        $(".block_id").select2('val', null);
    }

    getStreets = function(success,complete){
        $.ajax({
            url:"{{url('repository/getStreets')}}",
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
    // getStreetsSuccess = function(response){
    //     $(".street_id").empty();
	// 	$(".street_id").select2('val', null);
    //     $(".street_id").append($('<option></option>').val('').text(''));
    //     $.each(response,function(i,val){
    //         $(".street_id").append($('<option></option>').val(val.id).text(val.name));
    //     });
    // }
    getStreetsComplete = function(response){
        $(".street_id").select2('val', null);
    }

    getHouseTypes = function(success,complete){
        $.ajax({
            url:"{{url('repository/getHouseTypes')}}",
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getHouseTypesByZoneID = function(zoneID,success,complete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByZoneID')}}/" + zoneID,
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getHouseTypesByBlockID = function(blockID,success,complete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByBlockID')}}/" + blockID,
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getHouseTypesByStreetID = function(streetID,success,complete){
        $.ajax({
            url:"{{url('repository/getHouseTypesByStreetID')}}/" + streetID,
            type:'GET',
            success: success,
            complete: complete
        });
    }
    getHouseTypesSuccess = function(response){
        $(".house_type").empty();
		$(".house_type").select2('val', null);
        $(".house_type").append($('<option></option>').val('').text(''));
        $.each(response,function(i,val){
            $(".house_type").append($('<option></option>').val(val.id).text(val.name));
        });
    }
    getHouseTypesComplete = function(response){
        $(".house_type").select2('val', null);
    }

    onGenenerateButtonClicked = function(){
        var params = {
            zone_id: null,
            block_id: null,
            building_id : null,
            street_id: null,
            house_type: null,
            house_policy : 1
        };
        const zoneID    = $('.zone_id').val();
        const blockID   = $('.block_id').val();
        const buildingID   = $('.building_id').val();
        const streetID  = $('.street_id').val();
        const houseType = $('.house_type').val();

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
        showLoading();
        getHousesByAllTrigger(params,getHousesSuccess,getHousesComplete);
    }

    getHousesByAllTrigger = function(params,success,complete){
        $.ajax({
            url:"{{url('repository/getHousesByAllTrigger')}}",
            type:'GET',
            data: params,
            success: success,
            complete: complete
        });
    }

    getHousesSuccess = function(response){
        $('#my-table > tbody').empty();
        buildBody(response);
    }
    getHousesComplete = function(response){stopLoading();}

    onRemoveRowClicked = function(index){
        $(".tr-" + index).remove();
    }

    buildBody = function(response){
        var body = $('#my-table > tbody');
        $.each(response,function(i,val){
            body.append(buildRow(i,val));
        });
        stopLoading();
    }

    buildRow = function(index,value){
        var row  = '<tr class="tr-'+ value.id +'">';
            // no
            row += '<td class="text-center house house-'+ value.id +'">';
            row += (index + 1);
            row += '</td>';
            if('{{getSetting()->allow_zone}}' == 1){
                // zone
                row += '<td class="text-center zone zone-'+ value.id +'">';
                row += value.zone ? value.zone : '';
                row += '<input type="hidden" name="line_zone_id[]" value="'+ value.zone_id +'"/>';
                row += '</td>';
            }
            if('{{getSetting()->allow_block}}' == 1){
                // block
                row += '<td class="text-center block block-'+ value.id +'">';
                row += value.block ? value.block : '';
                row += '<input type="hidden" name="line_block_id[]" value="'+ value.block_id +'"/>';
                row += '</td>';
            }
            
            // street
            row += '<td class="text-center street street-'+ value.id +'">';
            row += value.street;
            row += '<input type="hidden" name="line_street_id[]" value="'+ value.street_id +'"/>';
            row += '</td>';
            // house type
            row += '<td class="text-center house_type house_type-'+ value.id +'">';
            row += value.houseType;
            row += '<input type="hidden" name="line_house_type[]" value="'+ value.house_type +'"/>';
            row += '</td>';
            // house
            row += '<td class="text-center house house-'+ value.id +'">';
            row += value.house_no;
            row += '<input type="hidden" name="line_house_id[]" value="'+ value.id +'"/>';
            row += '</td>';
            // percent
            row += '<td class="percent percent-'+ value.id +'">';
            row += '<input type="number" min="1" max="100" required class="form-control input-percent input-percent-'+ value.id +'" id="line_percent.'+index+'" name="line_percent[]" />';
            row += '</td>';
            // delete
            row += '<td class="delete delete-'+ value.id +'">';
            row += '<button title="{{trans("lang.remove")}}" onclick="onRemoveRowClicked('+value.id+');" class="btn btn-sm red"><i class="fa fa-trash"></i> {{trans("lang.remove")}}</button>';
            row += '</td>';
            row += '</tr>';

        return row;
    }

    function getBuildings(){
        var zone_id = $('.zone_id').val();
        var block_id = $('.block_id').val();
        var params = {
            zone_id: null,
            block_id: null,
            street_id: null,
            house_type: null,
        };
        params.zone_id = zone_id;
        params.block_id = block_id;
        $.ajax({
				url :'{{url("repository/getBuilding")}}',
				type:'GET',
				async:false,
				data:params,
				success:function(data){
                    $(".building_id").empty();
                    $(".building_id").select2('val', null);
                    $(".building_id").append($('<option></option>').val('').text(''));
                    $.each(data,function(i,val){
                        $(".building_id").append($('<option></option>').val(val.id).text(val.name));
                    });
				},error:function(){
					console.log('error get qty stock.');
				}
			});
    }

    function getHouseType(){
        var zone_id = $('.zone_id').val();
        var block_id = $('.block_id').val();
        var building_id = $('.building_id').val();
        var params = {
            zone_id: null,
            block_id: null,
            building_id : null,
            street_id: null,
            house_type: null,
        };
        params.zone_id = zone_id;
        params.block_id = block_id;
        params.building_id = building_id;
        $.ajax({
				url :'{{url("repository/getHouseType")}}',
				type:'GET',
				async:false,
				data:params,
				success:function(data){
                    $(".house_type").empty();
                    $(".house_type").select2('val', null);
                    $(".house_type").append($('<option></option>').val('').text(''));
                    $.each(data,function(i,val){
                        $(".house_type").append($('<option></option>').val(val.id).text(val.name));
                    });
				},error:function(){
					console.log('error get qty stock.');
				}
			});
    }

    $(document).ready(function(){
        $(".select2").select2({placeholder:'{{trans("lang.please_choose")}}',width:'100%',allowClear:'true'});
        // getStreets(getStreetsSuccess,getStreetsComplete);
        // getHouseTypes(getHouseTypesSuccess,getHouseTypesComplete);
        if('{{getSetting()->allow_zone}}' == 1){
            getZones(getZonesSuccess,getZonesComplete);
            $('.zone_id').on('change',function(){
                var building_id = $('.building_id').val();
                if(building_id){
                    $('#generate').removeClass("disabled");
                }else{
                    $('#generate').addClass("disabled");
                }
                const zoneID = $(this).val();
                if(zoneID){
                    getBlocksByZoneID(zoneID,getBlocksSuccess,getBlocksComplete);
                    // getStreetsByZoneID(zoneID,getStreetsSuccess,getStreetsComplete);
                    // getHouseTypesByZoneID(zoneID,getHouseTypesSuccess,getHouseTypesComplete);
                }
            });
        }
        if('{{getSetting()->allow_block}}' == 1){
            getBlocks(getBlocksSuccess,getBlocksComplete);
            $('.block_id').on('change',function(){
                var building_id = $('.building_id').val();
                if(building_id){
                    $('#generate').removeClass("disabled");
                }else{
                    $('#generate').addClass("disabled");
                }
                const blockID = $(this).val();
                if(blockID){
                    // getStreetsByBlockID(blockID,getStreetsSuccess,getStreetsComplete);
                    // getHouseTypesByBlockID(blockID,getHouseTypesSuccess,getHouseTypesComplete);
                }
            });
        }
        $('.block_id').on('change',function(){
            var building_id = $('.building_id').val();
            if(building_id){
                $('#generate').removeClass("disabled");
            }
            getBuildings();
        });
        $('.building_id').on('change',function(){
            var building_id = $('.building_id').val();
            if(building_id){
                $('#generate').removeClass("disabled");
            }
            // getHouseType();
        });
        $('.street_id').on('change',function(){
            const streetID = $(this).val();
            if(streetID){
                // getHouseTypesByStreetID(streetID,getHouseTypesSuccess,getHouseTypesComplete);
            }
        });     

        $("#btnBack, #btnCancel").on("click",function(){
			var rounte = $(this).attr('rounte');
			window.location.href=rounte;
		});
    });
</script>
@endsection()
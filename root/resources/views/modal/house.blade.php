<style>
	.pointer{
		cursor: pointer;
	}
</style>
<form class="house-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="house-modal draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="form-group">
							<div class="col-md-12 text-center	">
								<span class="show-message-error center font-red bold"></span>
							</div>
						</div>
						<div class="row">
							@if(getSetting()->allow_zone==1)
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label bold">{{trans('lang.zone')}} 	
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										@if(hasRole('zone_add'))
											<div class="input-group">
												<select name="zone_id" id="zone" class="form-control zone my-select2">
													<option value=""></option>
													{{getSystemData('ZN')}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddZone">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select name="zone_id" id="zone" class="form-control zone my-select2">
												<option value=""></option>
												{{getSystemData('ZN')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							@endif
							@if(getSetting()->allow_block==1)
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label bold">{{trans('lang.block')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										@if(hasRole('block_add'))
											<div class="input-group">
												<select name="block_id" id="block" class="form-control block my-select2">
													<option value=""></option>
													{{getSystemData('BK')}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddBlock">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select name="block_id" id="block" class="form-control block my-select2">
												<option value=""></option>
												{{getSystemData('BK')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							@endif
						</div>
						<div class="row">
							
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label bold">{{trans('lang.building')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										@if(hasRole('building_add'))
											<div class="input-group">
												<select name="building_id" id="building_id" class="form-control building my-select2">
													<option value=""></option>
													{{getSystemData('BD')}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddBuilding">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select name="building_id" id="building_id" class="form-control building my-select2">
												<option value=""></option>
												{{getSystemData('BD')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label bold">{{trans('lang.street')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										@if(hasRole('street_add'))
											<div class="input-group">
												<select name="street_id" id="street" class="form-control street my-select2">
													<option value=""></option>
													{{getSystemData('ST')}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddStreet">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select name="street_id" id="street" class="form-control street my-select2">
												<option value=""></option>
												{{getSystemData('ST')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-4 control-label bold">{{trans('lang.house_type')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										@if(hasRole('house_type_add'))
											<div class="input-group">
												<select name="house_type" id="house_type" class="form-control house_type my-select2">
													<option value=""></option>
													{{getSystemData('HT')}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddHouseType">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select name="house_type" id="house_type" class="form-control house_type my-select2">
												<option value=""></option>
												{{getSystemData('HT')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<table class="table table-striped table-bordered table-hover" id="table_purchase">
							<thead>
								<tr>
									<th class="text-center all">{{ trans('lang.no') }}</th>
									<th class="text-center all">{{ trans('lang.house_no') }}</th>
									<th class="text-center all">{{ trans('lang.desc') }}</th>
									<th class="text-center all">{{ trans('lang.status') }}</th>
									<th class="text-center all"><i class='fa fa-plus pointer'></i></th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success button-submit-house" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
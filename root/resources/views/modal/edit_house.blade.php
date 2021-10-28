<form class="house-form-edit form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<input type="hidden" name="old_street_id" id="old_street_id" class="old_street_id"/>
	<input type="hidden" name="old_house_no" id="old_house_no" class="old_house_no"/>
	<div class="house-modal-edit draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<div class="col-md-12 text-center	">
							<span class="show-message-error-edit center font-red bold"></span>
						</div>
					</div>
					<div class="form-body">
						@if(getSetting()->allow_zone==1)
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-4 control-label bold">{{trans('lang.zone')}} 	
									<span class="required">*</span>
								</label>
								<div class="col-md-8">
									@if(hasRole('zone_add'))
										<div class="input-group">
											<select name="zone_id" id="zone-edit" class="form-control zone-edit my-select2">
												<option value=""></option>
												{{getSystemData('ZN')}}
											</select>
											<span class="input-group-addon btn blue" id="btnAddZoneEdit">
												<i class="fa fa-plus"></i>
											</span>
										</div>
									@else
										<select name="zone_id" id="zone-edit" class="form-control zone-edit my-select2">
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
											<select name="block_id" id="block-edit" class="form-control block-edit my-select2">
												<option value=""></option>
												{{getSystemData('BK')}}
											</select>
											<span class="input-group-addon btn blue" id="btnAddBlockEdit">
												<i class="fa fa-plus"></i>
											</span>
										</div>
									@else
										<select name="block_id" id="block-edit" class="form-control block-edit my-select2">
											<option value=""></option>
											{{getSystemData('BK')}}
										</select>
									@endif
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
						@endif
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-4 control-label bold">{{trans('lang.building')}} 
									<span class="required">*</span>
								</label>
								<div class="col-md-8">
									@if(hasRole('building_add'))
										<div class="input-group">
											<select name="building_id" id="building_id-edit" class="form-control building my-select2">
												<option value=""></option>
												{{getSystemData('BD')}}
											</select>
											<span class="input-group-addon btn blue" id="btnAddBuildingEdit">
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
											<select name="street_id" id="street-edit" class="form-control street-edit my-select2">
												<option value=""></option>
												{{getSystemData('ST')}}
											</select>
											<span class="input-group-addon btn blue" id="btnAddStreetEdit">
												<i class="fa fa-plus"></i>
											</span>
										</div>
									@else
										<select name="street_id" id="street-edit" class="form-control street-edit my-select2">
											<option value=""></option>
											{{getSystemData('ST')}}
										</select>
									@endif
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-4 control-label bold">{{trans('lang.house_type')}} 
									<span class="required">*</span>
								</label>
								<div class="col-md-8">
									@if(hasRole('house_type_add'))
										<div class="input-group">
											<select name="house_type" id="house_type-edit" class="form-control house_type-edit my-select2">
												<option value=""></option>
												{{getSystemData('HT')}}
											</select>
											<span class="input-group-addon btn blue" id="btnAddHouseTypeEdit">
												<i class="fa fa-plus"></i>
											</span>
										</div>
									@else
										<select name="house_type" id="house_type-edit" class="form-control house_type-edit my-select2">
											<option value=""></option>
											{{getSystemData('HT')}}
										</select>
									@endif
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-4 control-label bold">{{trans('lang.status')}} 
									<span class="required">*</span>
								</label>
								<div class="col-md-8">
									<select id="status-edit" class="form-control my-select2 status-edit" name="status">
										<option value="1">{{trans('lang.start')}}</option>
										<option value="2">{{trans('lang.finish')}}</option>
										<option value="3">{{trans('lang.stop')}}</option>
									</select>
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="col-md-4 control-label bold">{{trans('lang.house_no')}} 
									<span class="required">*</span>
								</label>
								<div class="col-md-8">
									<input type="text" id="house_no-edit" length="50" class="form-control house_no-edit" name="house_no" placeholder="{{trans("lang.enter_text")}}" />
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="col-md-2 control-label bold">{{trans('lang.desc')}} 
									<span class="required">*</span>
								</label>
								<div class="col-md-10">
									<textarea id="desc-edit" length="100" name="desc" rows="10" data-height="100" class="desc-edit form-control" placeholder="{{trans('lang.enter_text')}}"></textarea>
									<span class="help-block font-red bold"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success button-submit-edit" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
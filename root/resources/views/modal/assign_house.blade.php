<form class="assign-house-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="assign-house-modal draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-zone" class="col-md-12 bold">{{trans('lang.zone')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-12">
										<select name="zone_id" id="zone_id_assign_house" class="form-control boq-zone-assign-house my-select2">
											<option value=""></option>
											{{getSystemData('ZN')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-block" class="col-md-12 bold">{{trans('lang.block')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-12">
										<select name="block_id" id="block_id_assign_house" class="form-control boq-block-assign-house my-select2">
											<option value=""></option>
											{{getSystemData('BK')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-building" class="col-md-12 bold">{{trans('lang.building')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-12">
										<select name="building_id" id="building_id_assign_house" class="form-control boq-building-assign-house my-select2">
											<option value=""></option>
											{{getSystemData('BD')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-street" class="col-md-12 bold">{{trans('lang.street')}} 
										{{-- <span class="required">*</span> --}}
									</label>
									<div class="col-md-12">
										<select name="street_id" id="street_id_assign_house" class="form-control boq-street-assign-house my-select2">
											<option value=""></option>
											{{getSystemData('ST')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-house-type" class="col-md-12 bold">{{trans('lang.house_type')}} 
										{{-- <span class="required">*</span> --}}
									</label>
									<div class="col-md-12">
										<select name="house_type_ids" id="house_type_id_assign_house" class="form-control boq-house-type-assign-house my-select2">
											<option value=""></option>
											{{getSystemData('HT')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-house" class="col-md-12 bold " id="label-house">{{trans('lang.house_no')}} 
										{{-- <span class="required">*</span> --}}
									</label>
									<div class="col-md-12 boq-house-wrapper">
										<select name="house[]" id="boq_house_assign_house" class="form-control boq-house-assign-house my-select2" multiple>
										
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success button-submit-assign-house" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
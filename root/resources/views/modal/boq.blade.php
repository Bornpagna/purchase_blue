<style>
	.boq-pointer{
		cursor: pointer;
	}
</style>
<form class="enter-boq-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="enter-boq-modal draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="form-group">
							<div class="col-md-12 text-center">
								<span class="show-message-error-boq center font-red bold"></span>
							</div>
						</div>
						{{-- <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label "></label>
									<div class="col-md-8">
										<input type="radio" name="option_house" id="sq1" class="option-house" value="1" checked />{{trans('lang.select_house')}}
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
                                    <label class="col-md-3 control-label "></label>
                                    <div class="col-md-8">
                                        <input type="radio" name="option_house" id="sp2" class="option-house" value="2" />{{trans('lang.select_house_type')}}
                                    </div>
									<label class="col-md-6 control-label "></label>
                                </div>
							</div>
						</div> --}}
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="boq-zone" class="col-md-12 bold">{{trans('lang.zone')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-12">
										<select name="zone_id" id="boq-zone" class="form-control boq-zone-add my-select2">
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
										<select name="block_id" id="boq-block" class="form-control boq-block-add my-select2">
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
										<select name="building_id[]" id="boq-building" class="form-control boq-building-add my-select2" multiple>
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
										<select name="street_id" id="boq-street" class="form-control boq-street-add my-select2">
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
										<select name="house_type_id" id="boq-house-type" class="form-control boq-house-type-add my-select2">
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
										<div class="row">
											<div class="col-md-9">
											<select name="house[]" id="boq-house" class="form-control boq-house-add my-select2" multiple>
											
											</select>
											<span class="help-block font-red bold"></span>
											</div>
											<div class="col-md-3">
												<label class="checkbox-inline"><input type="checkbox" id="checkbox-house" value="">Select All</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{-- <div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="item-type" class="col-md-12 bold" id="label-item-type">
										{{trans('lang.item_type')}}
									</label>
								<div class="col-md-12">
									<select onchange="ChangeItemTypes()" name="item_type" id="boq-item-type" class="form-control boq-item-type my-select2">
										<option value=""> {{trans('please_choose')}}</option>
										{{getSystemData("IT")}}
									</select>
									<span class="help-block font-red bold"></span>
								</div>
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group">
									<label for="item-type" class="col-md-12 bold" id="label-item-name">
										{{trans('lang.items')}}
									</label>
								<div class="col-md-12">
									<select name="item_name" id="boq-item_name" class="form-control boq-item-name my-select2">
										<option value=""> {{trans('please_choose')}}</option>
									</select>
									<span class="help-block font-red bold"></span>
								</div>
								</div>
							</div>
						</div> --}}
						<div class="row">
							<div class="col-md-5">
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="boq-working-type" class="col-md-4 bold control-label" id="label-working-type">{{trans('lang.working_type')}}
										<span class="required">*</span>
									</label>
									<div class="col-md-8">
										<select name="working_type" id="boq-working-type" class="form-control boq-working-type-add my-select2">
											<option value=""></option>
											{{getSystemData('WK')}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<div class="col-md-1">
								<button type="button" class="btn btn-success boq-button-add-working-type" >{{trans('lang.add')}}</button>
							</div>
						</div>
						<table class="table table-hover no-footer" id="table_boq">
							<thead>
								<tr>
									<th class="text-center all" width="2%">{{ trans('lang.no') }}</th>
									<th class="text-center all" width="15%">{{ trans('lang.item_type') }}</th>
									<th class="text-center all" width="15%">{{ trans('lang.items') }}</th>
									<th class="text-center all" width="10%">{{ trans('lang.uom') }}</th>
									<th class="text-center all" width="10%">{{ trans('lang.qty_std') }}</th>
									<th class="text-center all" width="10%">{{ trans('lang.qty_add') }}</th>
									<th class="text-center all" width="10%">{{ trans('lang.cost') }}</th>
									<th class="text-center all" width="3%"><i class='fa fa-plus boq-pointer'></i></th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success boq-button-submit" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>

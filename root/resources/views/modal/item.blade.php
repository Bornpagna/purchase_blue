<form class="item-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<input type="hidden" id="old_code" class="old_code" name="old_code">
	<div class="item-modal draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-body">
						<div class="row">
							<!-- Item Code -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.item_code')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<input type="text" length="150" id="item-code" name="code" class="item-code form-control" placeholder="{{trans('lang.enter_text')}}" />
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<!-- Item Name -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.item_name')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<input type="text" length="150" id="item-name" name="name" class="item-name form-control" placeholder="{{trans('lang.enter_text')}}" />
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Item Type -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.item_type')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										@if(hasRole('item_type_add'))
											<div class="input-group">
												<select id="item-type" name="item_type" class="item-type my-select2 form-control">
													<option value=""></option>
													{{getSystemData('IT',NULL,NULL)}}
												</select>
												<span class="input-group-addon btn blue" id="btnAddItemType">
													<i class="fa fa-plus"></i>
												</span>
											</div>
										@else
											<select id="item-type" name="item_type" class="item-type my-select2 form-control">
												<option value=""></option>
												{{getSystemData('IT')}}
											</select>
										@endif
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<!-- Status -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.status')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<select id="status" name="status" class="status my-select2 form-control">
											<option value="1">{{trans('lang.active')}}</option>
											<option value="0">{{trans('lang.disable')}}</option>
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Unit Stock -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.unit_stock')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<select id="unit-stock" name="unit_stock" class="unit-stock my-select2 form-control">
											<option value=""></option>
											{{getUnitStock()}}
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<!-- Unit Usage -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.unit_usage')}}</label>
									<div class="col-md-9">
										<select id="unit-usage" name="unit_usage" class="unit-usage my-select2 form-control">
											<option value=""></option>
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Unit Purch -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.unit_purch')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<select id="unit-purch" name="unit_purch" class="unit-purch my-select2 form-control">
											<option value=""></option>
										</select>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<!-- Cost -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.cost')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<input type="number" length="11" step="any" id="item-cost" name="purch_cost" class="item-cost form-control" placeholder="{{trans('lang.enter_number')}}" />
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Alert QTY -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.alert_qty')}} 
										<span class="required">*</span>
									</label>
									<div class="col-md-9">
										<input type="number" length="11" step="any" id="alert-qty" name="alert_qty" class="alert-qty form-control" placeholder="{{trans('lang.enter_number')}}" />
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<!-- Description -->
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label bold">{{trans('lang.desc')}}</label>
									<div class="col-md-9">
										<textarea length="200" id="item-desc" name="desc" rows="10" data-height="100" class="item-desc form-control" placeholder="{{trans('lang.enter_text')}}"> </textarea>
										<span class="help-block font-red bold"></span>
									</div>
								</div>
							</div>
							<!-- Photo -->
							<div class="form-group">
								<div class="col-md-6">
									<label class="col-md-3 control-label bold">{{trans('lang.photo')}}</label>
									<div class="col-md-9">
										<div class="fileupload " data-provides="fileupload">
											<div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
											<img id="item-image-show" alt="no image" /></div>
											<div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
											<div>
												<span class="btn btn-file btn-primary">
													<span class="fileupload-new">{{trans('lang.select_image')}}</span>
													<span class="fileupload-exists">{{trans('lang.change')}}</span>
													<input type="file" accept="image/*" name="photo" id="item-photo" />
												</span>
												<a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">{{trans('lang.remove')}}</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success button-submit-item" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
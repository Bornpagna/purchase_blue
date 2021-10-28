<form class="upload-excel-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="upload-excel-form draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{trans('lang.upload_document')}}</h4>
				</div>
				<div class="modal-body">
							<div class="col-12">
								<ul class="nav nav-tabs upload-nav-tab" id="myTab" role="tablist">
									<li class="nav-item active">
										<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">{{ trans('lang.new_boq') }}</a>
									</li>
									<!-- <li class="nav-item">
										<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">{{ trans('lang.exist_boq') }}</a>
									</li> -->
								</ul>
								<div class="tab-content upload_boq_tab" id="myTabContent">
									<div class="tab-pane fade active in" id="home" role="tabpanel" aria-labelledby="home-tab">
										<div class="form-body">
											<div class="form-group padding-20">
												<div class="note">
													<p>The first line in downloaded csv file should remain as it is. Please do not change the order of columns.<br />
														The correct column order is () &amp; you must follow this. Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).</p>
												</div>
												<!-- Form Select Zone Street Block Building House -->
												<div class="displayNone" style="display: none;">
													<div class="row">
														<div class="col-md-4">
															<div class="form-group">
																<label for="boq-zone" class="col-md-12 bold">{{trans('lang.zone')}} 
																	<span class="required">*</span>
																</label>
																<div class="col-md-12">
																	<select name="zone_id" id="boq-zone-preview" class="form-control boq-zone my-select2">
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
																	<select name="block_id" id="boq-block-preview" class="form-control boq-block my-select2">
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
																	<select name="building_id[]" id="boq-building-preview" class="form-control boq-building my-select2" multiple>
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
																	<select name="street_id" id="boq-street-preview" class="form-control boq-street my-select2">
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
																	<select name="house_type_id" id="boq-house-type-preview" class="form-control boq-house-type my-select2">
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
																	<select name="house[]" id="boq-house-preview" class="form-control boq-house my-select2" multiple>
																	
																	</select>
																	<span class="help-block font-red bold"></span>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- End Form Select Zone Street Block Building House -->
												<div class="text-right">
													<a title="{{trans('lang.download_example')}}" class="btn btn-primary float-right" href="{{@$rounteExample}}">
														<i class="fa fa-download"></i> {{ trans('lang.download_example') }}
													</a>
												</div>
												
												<label class="col-md-12 bold">{{trans('lang.document')}}<span class="required">*</span></label>
												<div class="col-md-12">
													<div class="fileinput fileinput-new input-group excel" data-provides="fileinput">
														<div class="form-control" data-trigger="fileinput">
															<i class="glyphicon glyphicon-file fileinput-exists"></i> 
															<span class="fileinput-filename"></span>
														</div>
														<span class="input-group-addon btn btn-success btn-file">
															<span class="fileinput-new bold">{{trans('lang.select_doc')}}</span>
															<span class="fileinput-exists bold">{{trans('lang.change')}}</span>
															<input type="file" id="excel" name="excel" />
														</span>
															<a href="#" class="input-group-addon btn btn-danger fileinput-exists bold" data-dismiss="fileinput">{{trans('lang.delete')}}</a>
													</div>
													<span class="help-block error-excel font-red bold"></span>
												</div>
											</div>
										</div>
									</div>
									<!-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
										<div class="form-body">
											<div class="form-group padding-20">
												<div class="note">
													<p>The first line in downloaded csv file should remain as it is. Please do not change the order of columns.<br />
														The correct column order is () &amp; you must follow this. Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).</p>
												</div>
												<div class="text-right">
													<a title="{{trans('lang.download_example')}}" class="btn btn-primary float-right" href="#">
														<i class="fa fa-download"></i> {{ trans('lang.download_example') }}
													</a>
												</div>
												<label for="boq-house-type" class="col-md-12 bold">{{trans('lang.boq_code')}} 
													{{-- <span class="required">*</span> --}}
												</label>
												<div class="col-md-12">
													<select name="house_type_ids[]" id="boq-house-type" class="form-control boq-house-type my-select2">
														<option value=""></option>
														{{getBOQNumber()}}
													</select>
													<span class="help-block font-red bold"></span>
												</div>
												<label class="col-md-12 bold">{{trans('lang.document')}}<span class="required">*</span></label>
												<div class="col-md-12">
													<div class="fileinput fileinput-new input-group excel" data-provides="fileinput">
														<div class="form-control" data-trigger="fileinput">
															<i class="glyphicon glyphicon-file fileinput-exists"></i> 
															<span class="fileinput-filename"></span>
														</div>
														<span class="input-group-addon btn btn-success btn-file">
															<span class="fileinput-new bold">{{trans('lang.select_doc')}}</span>
															<span class="fileinput-exists bold">{{trans('lang.change')}}</span>
															<input type="file" id="excel" name="excels" />
														</span>
															<a href="#" class="input-group-addon btn btn-danger fileinput-exists bold" data-dismiss="fileinput">{{trans('lang.delete')}}</a>
													</div>
													<span class="help-block error-excel font-red bold"></span>
												</div>
											</div>
										</div>
									</div> -->
								</div>
							</div>
							{{-- 
							<!-- <label class="col-md-3 control-label bold">{{trans('lang.document')}}<span class="required">*</span></label>
							<div class="col-md-8">
								<div class="fileinput fileinput-new input-group excel" data-provides="fileinput">
									<div class="form-control" data-trigger="fileinput">
										<i class="glyphicon glyphicon-file fileinput-exists"></i> 
										<span class="fileinput-filename"></span>
									</div>
									<span class="input-group-addon btn btn-success btn-file">
										<span class="fileinput-new bold">{{trans('lang.select_doc')}}</span>
										<span class="fileinput-exists bold">{{trans('lang.change')}}</span>
										<input type="file" id="excel" name="excel" />
									</span>
										<a href="#" class="input-group-addon btn btn-danger fileinput-exists bold" data-dismiss="fileinput">{{trans('lang.delete')}}</a>
								</div>
								<span class="help-block error-excel font-red bold"></span>
							</div>  -->
							--}}
						
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success button-save-excel" id="btn_upload_excel" name="btn_upload_excel" value="1">{{trans('lang.upload')}}</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
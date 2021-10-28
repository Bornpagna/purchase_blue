<form class="form-revise-boq-house form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="modal-revise-boq-house draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{ trans('lang.revise_boq') }}</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<div class="col-md-12 text-center	">
							<span class="show-message-error-edit center font-red bold"></span>
						</div>
					</div>
					<div class="form-body">
						
						<div class="row">
							<div class="col-md-12">
								<ul class="nav nav-tabs upload-nav-tab" id="myTabReivise" role="tablist">
									<li class="nav-item active">
										<a class="nav-link active" id="manual-tab" data-toggle="tab" href="#manual" role="tab" aria-controls="manual" aria-selected="true">{{ trans('lang.manual_input') }}</a>
									</li>
									<!-- <li class="nav-item">
										<a class="nav-link" id="excel-tab" data-toggle="tab" href="#excel" role="tab" aria-controls="excel" aria-selected="false">{{ trans('lang.excel_import') }}</a>
									</li> -->
								</ul>
								<div class="tab-content upload_boq_tab" id="myTabContentRevise">
									<div class="padding-20 tab-pane fade active in" id="manual" role="tabpanel" aria-labelledby="manual-tab">
										<div class="row">
											<div class="col-md-12">
												<span><b>{{ trans('lang.note') }} : </b>{{ trans('lang.note_revise_boq_house') }}</span>
											</div>
										</div>
										<br />
										<div class="form-group ">
											<label for="boq-street-edit" class="col-md-6 bold">{{trans('lang.select_working_type')}} 
												<span class="required">*</span>
											</label>
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-10">
														<select name="working_type[]" id="working_type" class="form-control working_type my-select2" multiple>
															{{getSystemData('WK')}}
														</select>
														<span class="help-block font-red bold"></span>
													</div>
													<div class="col-md-2">
														<label class="checkbox-inline"><input type="checkbox" id="checkbox" value="">Select All</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								
								<!-- <div class="tab-pane fade" id="excel" role="tabpanel" aria-labelledby="excel-tab">
									<div class="form-body">
										<div class="form-group padding-20">
											<div class="row padding-20">
												<div class="col-md-12">
													<br />
													<span><b>{{ trans('lang.note') }} : </b>{{ trans('lang.note_revise_boq_house') }}</span>
													<p>The first line in downloaded csv file should remain as it is. Please do not change the order of columns.<br />
														The correct column order is () &amp; you must follow this. Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).</p>
												</div>
											</div>
											
											<div class="text-right">
												<a title="{{trans('lang.download_example')}}" class="btn btn-primary float-right" href="#">
													<i class="fa fa-download"></i> {{ trans('lang.download_example') }}
												</a>
											</div>
											<label for="boq-house-type" class="col-md-12 bold">{{trans('lang.select_working_type')}} 
												{{-- <span class="required">*</span> --}}
											</label>
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-10">
														<select name="working_type[]" id="working_type_excel" class="form-control working_type-excel my-select2" multiple>
															{{getSystemData('WK')}}
														</select>
														<span class="help-block font-red bold"></span>
													</div>
													<div class="col-md-2">
														<label class="checkbox-inline"><input type="checkbox" id="checkbox-excel" value="">Select All</label>
													</div>
												</div>
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
								</div> -->
							</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success button-submit-revise_boq_house" value="1">{{ trans('lang.revise') }}</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
<form class="upload-excel-revise-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<div class="modal-upload-excel-revise-form draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{trans('lang.upload_document')}}</h4>
				</div>
				<div class="modal-body">
								<div class="form-body">
									<div class="form-group padding-20">
										<div class="note">
											{{ trans('lang.note_upload_revise_boq') }}<br />
											<p>The first line in downloaded csv file should remain as it is. Please do not change the order of columns.<br />
												The correct column order is () &amp; you must follow this. Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).</p>
										</div>
										<div class="text-right">
											<a title="{{trans('lang.download_example')}}" class="btn btn-primary float-right download_example_revise" >
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
													<input type="file" id="excel_revise" name="excel" />
												</span>
													<a href="#" class="input-group-addon btn btn-danger fileinput-exists bold" data-dismiss="fileinput">{{trans('lang.delete')}}</a>
											</div>
											<span class="help-block error-excel font-red bold"></span>
										</div>
									</div>
								</div>
							
				
		</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success button-save-excel" id="btn_upload_excel_revise" name="btn_upload_excel" value="1">{{trans('lang.upload')}}</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>
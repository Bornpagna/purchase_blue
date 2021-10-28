<form class="system-form form-horizontal" method="POST"  enctype="multipart/form-data">
	{{csrf_field()}}
	<input type="hidden" id="old_name" name="old_name">
	<div class="system-modal draggable-modal modal fade in" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-body item_typr">
						<div class="form-group parent_id">
							<label class="col-md-3 control-label bold">{{trans('lang.parent')}} 
								<span class="required">*</span>
							</label>
							<div class="col-md-8">
								<select class="form-control select2" name="parent_id">
									{{getSystemData('IT',NULL,1)}}
								</select>
								<span class="help-block font-red bold"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label bold">{{trans('lang.name')}} 
								<span class="required">*</span>
							</label>
							<div class="col-md-8">
								<input type="text" length="50" id="sys-name" name="name" class="sys-name form-control" placeholder="{{trans('lang.enter_text')}}">
								<span class="help-block font-red bold"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label bold">{{trans('lang.desc')}} 
								<span class="required">*</span>
							</label>
							<div class="col-md-8">
								<textarea id="sys-desc" length="200" name="desc" rows="10" data-height="100" class="sys-desc form-control" placeholder="{{trans('lang.enter_text')}}"></textarea>
								<span class="help-block font-red bold"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success button-submit" value="1"></button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">{{trans('lang.cancel')}}</button>
				</div>
			</div>
		</div>
	</div>
</form>